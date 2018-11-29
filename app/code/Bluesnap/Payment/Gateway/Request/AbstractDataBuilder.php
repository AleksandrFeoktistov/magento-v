<?php
namespace Bluesnap\Payment\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Framework\Math\Random;
use Magento\Framework\Session\SessionManagerInterface;

abstract class AbstractDataBuilder implements BuilderInterface
{
    protected $subjectReader;
    protected $config;
    protected $remoteAddress;
    protected $state;
    protected $customerRepositoryInterface;
    protected $localeFormat;
    protected $registry;
    protected $mathRandom;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $session;

		/**
		 * @var \Magento\Payment\Model\Method\Logger
		 */
    protected $logger;

		/**
		 * @var \Bluesnap\Payment\Helper\Fraudsession
		 */
    protected $fraudSession;

    public function __construct(
        \Magento\Payment\Gateway\Helper\SubjectReader $subjectReader,
        \Bluesnap\Payment\Gateway\Config\Config $config,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Framework\App\State $state,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Framework\Locale\Format $localeFormat,
        \Magento\Framework\Registry $registry,
        SessionManagerInterface $session,
        \Psr\Log\LoggerInterface $logger,
        \Bluesnap\Payment\Helper\Fraudsession $fraudSession,
        Random $mathRandom
    ) {
        $this->subjectReader = $subjectReader;
        $this->config = $config;
        $this->remoteAddress = $remoteAddress;
        $this->state = $state;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->localeFormat = $localeFormat;
        $this->registry = $registry;
        $this->session = $session;
        $this->mathRandom = $mathRandom;
        $this->logger = $logger;
        $this->fraudSession = $fraudSession;
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        $request = array(
            'url' => $this->getUrl($buildSubject),
            'method' => $this->getMethod($buildSubject),
            'headers' => $this->getHeaders($buildSubject),
            'body' => $this->getBody($buildSubject)
        );

        $this->registry->unregister('request_detail');
        $this->registry->register('request_detail', $request);

        return $request;
    }

    protected function getHeaders(array $buildSubject)
    {
        $authorization = base64_encode(sprintf('%s:%s', $this->config->getApiUsername(), $this->config->getApiPassword()));
        return array(
            'Authorization' => sprintf('Basic %s', $authorization),
            'Content-type' => 'application/xml',
            'Expect' => '' // Overwrite the "Expect: 100 continue" header, to keep things simple
        );
    }

    /**
     * AUTH_ONLY and AUTH_CAPTURE are very similar, so abstract these out.
     *
     * @param array $buildSubject
     * @param $type
     */
    protected function getAuthBody(array $buildSubject, $type)
    {
        $paymentData = $this->subjectReader->readPayment($buildSubject);
        $amount = $this->subjectReader->readAmount($buildSubject);
        $order = $paymentData->getOrder();
        $payment = $paymentData->getPayment();
        $orderModel = $payment->getOrder();

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><card-transaction></card-transaction>');
        $xml->addAttribute('xmlns', 'http://ws.plimus.com');
        $xml->addChild('card-transaction-type', $type);
        $xml->addChild('merchant-transaction-id', $order->getOrderIncrementId());
        $xml->addChild('recurring-transaction', 'ECOMMERCE');
        $xml->addChild('soft-descriptor', $this->config->getSoftDescriptor());


        if ($this->config->isDccActive() && $order->getCurrencyCode() !== $orderModel->getOrderCurrencyCode()) {
            // DCC was used, we need to alter the $amount and the $currencyCode
            $xml->addChild('amount', $orderModel->getGrandTotal());
            $xml->addChild('currency', $orderModel->getOrderCurrencyCode());
        } else {
            $xml->addChild('amount', $amount);
            $xml->addChild('currency', $order->getCurrencyCode());
        }

        $billingAddress = $order->getBillingAddress();

        $cardHolderInfo=$xml->addChild('card-holder-info');
        $cardHolderInfo->addChild('first-name', $billingAddress->getFirstname());
        $cardHolderInfo->addChild('last-name', $billingAddress->getLastname());
        $cardHolderInfo->addChild('email', $billingAddress->getEmail());

        if ($payment->getMethod() == \Bluesnap\Payment\Model\Ui\ConfigProvider::HOSTEDFIELDS_CODE) {
            if ($customerId = $payment->getOrder()->getCustomerId()) {
                $customer = $this->customerRepositoryInterface->getById($customerId);
                $xml->addChild('vaulted-shopper-id', $customer->getExtensionAttributes()->getVaultedShopperId());
                $creditCard = $xml->addChild('credit-card');
                $creditCard->addChild('card-last-four-digits', $payment->getAdditionalInformation('cc_last_4_digits'));
                $creditCard->addChild('card-type', $payment->getAdditionalInformation('cc_type'));
                $cardHolderInfo->addChild('zip', $billingAddress->getPostcode());
            } else {
                $this->addAddress($cardHolderInfo, $billingAddress, false);
		            if($payment->getAdditionalInformation('token') == '') {
				            $xml->addChild('pf-token', $this->session->getData('pftoken'));
		            } else {
				            $xml->addChild('pf-token', $payment->getAdditionalInformation('token'));
		            }
            }
        } elseif ($payment->getMethod() == \Bluesnap\Payment\Model\Ui\ConfigProvider::VAULT_CODE) {
            $extensionAttributes = $payment->getExtensionAttributes();
            $paymentToken = $extensionAttributes->getVaultPaymentToken();
            $gatewayToken = $paymentToken->getGatewayToken();
            $vaultedShopperId = explode('-', $gatewayToken);
            $vaultedShopperId = $vaultedShopperId[0];
            $xml->addChild('vaulted-shopper-id', $vaultedShopperId);
            $cardDetails = unserialize($paymentToken->getTokenDetails());
            $creditCard = $xml->addChild('credit-card');
            $creditCard->addChild('card-last-four-digits', $cardDetails['cc_last_4_digits']);
            $creditCard->addChild('card-type', $cardDetails['cc_type']);
            $cardHolderInfo->addChild('zip', $billingAddress->getPostcode());
        }

        $transactionFraudInfo = $xml->addChild('transaction-fraud-info');

        // If MOTO then set the fraud session to be empty and force the IP to 10.0.0.1
		    $transactionFraudInfo->addChild('fraud-session-id', $this->fraudSession->getFraudSessionId());
        if ($this->state->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML) {
            $transactionFraudInfo->addChild('shopper-ip-address', '10.0.0.1');
        } else {
            $transactionFraudInfo->addChild('shopper-ip-address', $this->remoteAddress->getRemoteAddress());
        }

        if (!$payment->getOrder()->getIsVirtual()) {
            $this->addAddress($transactionFraudInfo->addChild('shipping-contact-info'), $order->getShippingAddress(), true, 'address1');
        }

        return $xml;
    }

    protected function addAddress($contactInfo, $address, $addName=true, $addressKey='address')
    {
        if ($addName) {
            $contactInfo->addChild('first-name', $address->getFirstname());
            $contactInfo->addChild('last-name', $address->getLastname());
        }
        $addressLine = $address->getStreetLine1();
        if ($address->getStreetLine2()) {
            $addressLine .= ', ' . $address->getStreetLine2();
        }
        $contactInfo->addChild($addressKey, $addressLine);
        if (!in_array($address->getCountryId(), array('US', 'CA')) && $address->getRegionCode()) {
            $contactInfo->addChild('address2', $address->getRegionCode());
        }
        $contactInfo->addChild('city', $address->getCity());
        if (in_array($address->getCountryId(), array('US', 'CA')) && $address->getRegionCode()) {
            $contactInfo->addChild('state', $address->getRegionCode());
        }
        $contactInfo->addChild('zip', $address->getPostcode());

        $countryId = strtolower($address->getCountryId());
        if ($countryId == 'gb') {
            $countryId = 'uk';
        }
        $contactInfo->addChild('country', $countryId);
    }

    /**
     * We get something like "123456689-authorize" so we just need to split that on the "-" and return the first part
     *
     * @param $transactionId
     * @return mixed
     */
    protected function stripTransactionTypeSuffix($transactionId)
    {
        $transactionIdParts = explode('-', $transactionId);
        return $transactionIdParts[0];
    }

    abstract protected function getUrl(array $buildSubject);
    abstract protected function getMethod(array $buildSubject);
    abstract protected function getBody(array $buildSubject);
}
