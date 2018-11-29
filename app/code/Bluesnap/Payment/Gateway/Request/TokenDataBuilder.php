<?php
namespace Bluesnap\Payment\Gateway\Request;

class TokenDataBuilder extends AbstractDataBuilder
{
    public function getUrl(array $buildSubject)
    {
        return $this->config->getUrl() . 'services/2/payment-fields-tokens';
    }

    public function getMethod(array $buildSubject)
    {
        return 'POST';
    }

    /**
     * The token has no body.
     *
     * @param array $buildSubject
     * @return bool
     */
    public function getBody(array $buildSubject)
    {
        return false;
    }
}
