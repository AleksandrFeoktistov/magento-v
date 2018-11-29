<?php
namespace Bluesnap\Payment\Gateway\Request;

class RetrieveDataBuilder extends AbstractDataBuilder
{
    public function getUrl(array $buildSubject)
    {
        $transactionId = $buildSubject['transaction_id'];
        return $this->config->getUrl() . 'services/2/transactions/' . $transactionId;
    }

    public function getMethod(array $buildSubject)
    {
        return 'GET';
    }

    /**
     * The retrieve has no body.
     *
     * @param array $buildSubject
     * @return bool
     */
    public function getBody(array $buildSubject)
    {
        return false;
    }
}
