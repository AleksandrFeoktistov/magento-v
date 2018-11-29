<?php
namespace Bluesnap\Payment\Gateway\Request;

class AuthReversalDataBuilder extends AbstractDataBuilder
{
    public function getUrl(array $buildSubject)
    {
        return $this->config->getUrl() . 'services/2/transactions/';
    }

    public function getMethod(array $buildSubject)
    {
        return 'PUT';
    }

    /**
     * Authorize reversal XML.
     *
     * <card-transaction xmlns="http://ws.plimus.com">
     *     <card-transaction-type>AUTH_REVERSAL</card-transaction-type>
     *     <transaction-id>1011671987</transaction-id>
     * </card-transaction>'
     *
     * See https://developers.bluesnap.com/v2.0/docs/auth-reversal
     *
     * @param array $buildSubject
     * @return bool
     */
    public function getBody(array $buildSubject)
    {
        $paymentData = $this->subjectReader->readPayment($buildSubject);
        $payment = $paymentData->getPayment();
        $authorizeTransaction = $payment->getAuthorizationTransaction();

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><card-transaction></card-transaction>');
        $xml->addAttribute('xmlns', 'http://ws.plimus.com');
        $xml->addChild('card-transaction-type', 'AUTH_REVERSAL');
        $xml->addChild('transaction-id', $this->stripTransactionTypeSuffix($authorizeTransaction->getTxnId()));

        return $xml->asXML();
    }
}
