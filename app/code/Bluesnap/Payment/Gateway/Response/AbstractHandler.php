<?php

namespace Bluesnap\Payment\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Vault\Api\Data\PaymentTokenInterfaceFactory;
use Magento\Vault\Api\PaymentTokenManagementInterface;
use Magento\Sales\Api\Data\OrderPaymentExtensionInterface;
use Magento\Sales\Api\Data\OrderPaymentExtensionInterfaceFactory;
use Magento\Vault\Api\PaymentTokenRepositoryInterface;

/**
 * Payment Details Handler
 */
abstract class AbstractHandler implements HandlerInterface
{
    protected $subjectReader;
    protected $transactionType;
    protected $paymentTokenFactory;
    protected $paymentExtensionFactory;
    protected $tokenManagement;
    protected $tokenRepository;
		protected $session;
    protected $config;

    public function __construct(
        \Magento\Payment\Gateway\Helper\SubjectReader $subjectReader,
        \Bluesnap\Payment\Model\Vault\PaymentTokenFactory $paymentTokenFactory,
        OrderPaymentExtensionInterfaceFactory $paymentExtensionFactory,
        PaymentTokenManagementInterface $tokenManagement,
        PaymentTokenRepositoryInterface $tokenRepository,
        \Bluesnap\Payment\Gateway\Config\Config $config,
        \Magento\Framework\Session\SessionManagerInterface $session
    ) {
        $this->subjectReader = $subjectReader;
        $this->paymentTokenFactory = $paymentTokenFactory;
        $this->paymentExtensionFactory = $paymentExtensionFactory;
        $this->tokenManagement = $tokenManagement;
        $this->tokenRepository = $tokenRepository;
        $this->config = $config;
        $this->session = $session;
    }

    /**
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param \SimpleXMLElement $body
     */
    public function handleProcessingInformation($payment, $body)
    {
        if ($processingInfo = $body->{'processing-info'}) {
            $payment->setAdditionalInformation('processing_status', (string)$processingInfo->{'processing-status'});
            // Only hosted fields have CVV response codes, vaulted doesn't. So handle that case:
            if ($cvvResponseCode = (string)$processingInfo->{'cvv-response-code'}) {
                $payment->setAdditionalInformation('cvv_response_code', $cvvResponseCode);
            }
            $payment->setAdditionalInformation('avs_response_code_zip', (string)$processingInfo->{'avs-response-code-zip'});
            $payment->setAdditionalInformation('avs_response_code_address', (string)$processingInfo->{'avs-response-code-address'});
            $payment->setAdditionalInformation('avs_response_code_name', (string)$processingInfo->{'avs-response-code-name'});
        }
        $payment->setAdditionalInformation('last_transaction_id', (string)$body->{'transaction-id'});
    }

    public function addTransactionTypeSuffix($transactionId)
    {
        return $transactionId . '-' . $this->transactionType;
    }

    public function handleVaultedShopper($payment, $body)
    {
        // add vault payment token entity to extension attributes
        $paymentToken = $this->getVaultPaymentToken($payment, $body);
        if (null !== $paymentToken) {
            $extensionAttributes = $this->getExtensionAttributes($payment);
            $extensionAttributes->setVaultPaymentToken($paymentToken);
        }
    }

    public function handleDcc($payment, $body)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $payment->getOrder();
        if ($this->config->isDccActive() && $order->getBaseCurrencyCode() != $body->{'currency'}) {
            $payment->setAdditionalInformation('dcc_used', 1);
            // DCC was used for this order, let's add an order comment to reflect that fact.
            $order->addStatusHistoryComment(sprintf(
                'BlueSnap DCC was used on this order. Magento will claim the amount charged is in the base currency (%s), however the customer was charged %s %s',
                $order->getBaseCurrencyCode(),
                $body->{'amount'},
                $body->{'currency'}
            ));
        } else {
            $payment->setAdditionalInformation('dcc_used', 0);
        }
    }

    protected function getVaultPaymentToken($payment, $body)
    {
        if (!$payment->getAdditionalInformation('save_cc')) {
            return null;
        }

        // Check token existing in gateway response
        $token = (string)$body->{'vaulted-shopper-id'};
        if (empty($token)) {
            return null;
        }

        $tokenKey = sprintf('%s-%s-%s', $token, $payment->getAdditionalInformation('cc_type'), $payment->getAdditionalInformation('cc_expiry'));

        /** @var PaymentTokenInterface $paymentToken */
        $paymentToken = $this->tokenManagement->getByGatewayToken($tokenKey, $payment->getMethod(), $payment->getOrder()->getCustomerId());

        // If the token doesn't already exist, then create a new one.
        if (!$paymentToken) {
            $paymentToken = $this->paymentTokenFactory->create();
            $paymentToken->setGatewayToken($tokenKey);
            $dateTime = \DateTime::createFromFormat('d/m/Y', sprintf('01/%s', $payment->getAdditionalInformation('cc_expiry')));
            $paymentToken->setExpiresAt($dateTime->format('Y-m-d'));
            $payment->setAdditionalInformation('is_active_payment_token_enabler', 1);
        }

        // The billing address may have changed, so update our token.
        $billingAddress = $payment->getOrder()->getBillingAddress();
        $paymentToken->setTokenDetails(serialize(array(
            'cc_last_4_digits' => $payment->getAdditionalInformation('cc_last_4_digits'),
            'cc_type' => $payment->getAdditionalInformation('cc_type'),
            'cc_expiry' => $payment->getAdditionalInformation('cc_expiry'),
            'billing_address' => array(
                'company' => $billingAddress->getCompany(),
                'telephone' => $billingAddress->getTelephone(),
                'firstname' => $billingAddress->getFirstname(),
                'lastname' => $billingAddress->getLastname(),
                'street' => $billingAddress->getStreet(),
                'city' => $billingAddress->getCity(),
                'country_id' => $billingAddress->getCountryId(),
                'postcode' => $billingAddress->getPostcode(),
                'region' => $billingAddress->getRegion(),
                'region_id' => $billingAddress->getRegionId()
            )
        )));

        // If this is a token which already existed, we need to save it. The billing address may have updated and if we don't do
        // this then it doesn't ever get stored in the DB;
        if ($paymentToken->getId()) {
            $paymentToken->setData('is_active', true);
            $this->tokenRepository->save($paymentToken);
        }

        return $paymentToken;
    }

    /**
     * Get payment extension attributes
     * @param $payment
     * @return OrderPaymentExtensionInterface
     */
    private function getExtensionAttributes($payment)
    {
        $extensionAttributes = $payment->getExtensionAttributes();
        if (null === $extensionAttributes) {
            $extensionAttributes = $this->paymentExtensionFactory->create();
            $payment->setExtensionAttributes($extensionAttributes);
        }
        return $extensionAttributes;
    }
}
