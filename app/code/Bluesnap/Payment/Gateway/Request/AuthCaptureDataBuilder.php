<?php
namespace Bluesnap\Payment\Gateway\Request;

class AuthCaptureDataBuilder extends AbstractDataBuilder
{
    public function getUrl(array $buildSubject)
    {
        return $this->config->getUrl() . 'services/2/transactions';
    }

    public function getMethod(array $buildSubject)
    {
        return 'POST';
    }

    /**
     * Authorize and then Capture XML.
     *
     * <?xml version="1.0" encoding="UTF-8"?>
     * <card-transaction xmlns="http://ws.plimus.com">
     *     <card-transaction-type>AUTH_CAPTURE</card-transaction-type>
     *     <recurring-transaction>ECOMMERCE</recurring-transaction>
     *     <soft-descriptor>DescTest</soft-descriptor>
     *     <amount>11.00</amount>
     *     <currency>USD</currency>
     *     <card-holder-info>
     *         <first-name>test first name</first-name>
     *         <last-name>test last name</last-name>
     *     </card-holder-info>
     *     <pf-token>abcde12345**********</pf-token>
     *     <transaction-fraud-info>
     *         <fraud-session-id>fbcc094208f54c0e974d56875c73af7a</fraud-session-id>
     *     </transaction-fraud-info>
     * </card-transaction>'
     *
     * See https://developers.bluesnap.com/v2.0/docs/auth-capture
     *
     * @param array $handlingSubject
     * @param array $response
     */
    public function getBody(array $buildSubject)
    {
        $xml = $this->getAuthBody($buildSubject, 'AUTH_CAPTURE');
        return $xml->asXML();
    }
}
