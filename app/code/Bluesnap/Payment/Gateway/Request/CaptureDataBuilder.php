<?php
namespace Bluesnap\Payment\Gateway\Request;

class CaptureDataBuilder extends AbstractDataBuilder
{
    public function getUrl(array $buildSubject)
    {
        return $this->config->getUrl() . 'services/2/transactions';
    }

    public function getMethod(array $buildSubject)
    {
        return 'PUT';
    }

    /**
     * Capture XML.
     *
     * <?xml version="1.0" encoding="UTF-8"?>
     * <card-transaction xmlns="http://ws.plimus.com">
     *     <card-transaction-type>CAPTURE</card-transaction-type>
     *     <transaction-id>1011671985</transaction-id>
     * </card-transaction>'
     *
     * See https://developers.bluesnap.com/v2.0/docs/capture
     *
     * @param array $buildSubject
     * @return mixed
     */
    public function getBody(array $buildSubject)
    {
        $paymentData = $this->subjectReader->readPayment($buildSubject);
        $payment = $paymentData->getPayment();
        $amount = $this->subjectReader->readAmount($buildSubject);
        $authorizeTransaction = $payment->getAuthorizationTransaction();

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><card-transaction></card-transaction>');
        $xml->addAttribute('xmlns', 'http://ws.plimus.com');
        $xml->addChild('card-transaction-type', 'CAPTURE');
        $xml->addChild('transaction-id', $this->stripTransactionTypeSuffix($authorizeTransaction->getTxnId()));
        
        return $xml->asXML();
    }
}
