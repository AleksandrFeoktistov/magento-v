<?php
namespace Bluesnap\Payment\Gateway\Request;

class UpdateVaultedShopperDataBuilder extends AbstractDataBuilder
{
    public function getUrl(array $buildSubject)
    {
        /** @var \Magento\Customer\Model\Data\Customer $customer */
        $customer = $buildSubject['customer'];
        $vaultedShopperId = $customer->getExtensionAttributes()->getVaultedShopperId();
        return $this->config->getUrl() . 'services/2/vaulted-shoppers/' . $vaultedShopperId;
    }

    public function getMethod(array $buildSubject)
    {
        return 'PUT';
    }

    /**
     * TODO docs
     *
     * <vaulted-shopper xmlns="http://ws.plimus.com">
     *     <first-name>FirstName</first-name>
     *     <last-name>LastName</last-name>
     *     <payment-sources>
     *         <credit-card-info>
     *             <pf-token>****</pf-token>
     *         </credit-card-info>
     *     </payment-sources>
     * </vaulted-shopper>
     *
     * @param array $buildSubject
     * @return mixed
     *
     *
     *
     *
     */
    public function getBody(array $buildSubject)
    {
        $paymentData = $this->subjectReader->readPayment($buildSubject);
        $payment = $paymentData->getPayment();
        $order = $paymentData->getOrder();
        $customer = $buildSubject['customer'];

        if($payment->getAdditionalInformation('token') == '') {
        		$token = $this->session->getData('pftoken');
        } else {
        		$token = $payment->getAdditionalInformation('token');
        }

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><vaulted-shopper xmlns="http://ws.plimus.com"></vaulted-shopper>');
        $xml->addChild('first-name', $customer->getFirstName());
        $xml->addChild('last-name', $customer->getLastName());

        $paymentSources = $xml->addChild('payment-sources');
        $creditCardInfo = $paymentSources->addChild('credit-card-info');
        $this->addAddress($creditCardInfo->addChild('billing-contact-info'), $order->getBillingAddress(), true, 'address1');
		    $creditCardInfo->addChild('pf-token', $token);

        return $xml->asXML();
    }
}
