<?php
namespace Bluesnap\Payment\Gateway\Request;

class ConvertDataBuilder extends AbstractDataBuilder
{
    public function getUrl(array $buildSubject)
    {
        return $this->config->getUrl() . 'services/2/tools/currency-rates?base-currency='.$buildSubject['from'].'&quote-currency='.$buildSubject['to'];
    }

    public function getMethod(array $buildSubject)
    {
        return 'GET';
    }

    /**
     * The convert has no body.
     *
     * @param array $buildSubject
     * @return bool
     */
    public function getBody(array $buildSubject)
    {
        return false;
    }
}
