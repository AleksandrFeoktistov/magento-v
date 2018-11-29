<?php

namespace Bluesnap\Payment\Gateway\Config;

/**
 * Class Config
 */
class Config extends \Magento\Payment\Gateway\Config\Config
{
    const KEY_ACTIVE = 'active';
    const KEY_MODE = 'mode';
    const KEY_API_USERNAME = 'api_username';
    const KEY_API_PASSWORD = 'api_password';
    const KEY_IPN_WHITELIST = 'ipn_whitelist';
    const KEY_VAULT_ACTIVE = 'vault_active';
    const KEY_DCC_ACTIVE = 'dcc_active';
    const KEY_SOFT_DESCRIPTOR = 'soft_descriptor';
    const KEY_DEBUG = 'debug';
    const KEY_ERROR_RECIPIENT = 'error_recipient';

    public function isActive()
    {
        return (bool) $this->getValue(self::KEY_ACTIVE);
    }

    public function getMode()
    {
        return $this->getValue(self::KEY_MODE);
    }

    public function getApiUsername()
    {
        return $this->getValue(sprintf('%s_%s', $this->getMode(), self::KEY_API_USERNAME));
    }

    public function getApiPassword()
    {
        return $this->getValue(sprintf('%s_%s', $this->getMode(), self::KEY_API_PASSWORD));
    }

    public function getIpnIpWhitelist()
    {
        return $this->getValue(sprintf('%s_%s', $this->getMode(), self::KEY_IPN_WHITELIST));
    }

    public function getUrl()
    {
        if ($this->getMode() == 'production') {
            return 'https://ws.bluesnap.com/';
        }
        return 'https://sandbox.bluesnap.com/';
    }

    public function isDccActive()
    {
        return (bool) $this->getValue(self::KEY_DCC_ACTIVE);
    }

    public function getSoftDescriptor()
    {
        return $this->getValue(self::KEY_SOFT_DESCRIPTOR);
    }

    public function getDebug()
    {
        return $this->getValue(self::KEY_DEBUG);
    }

    public function getErrorRecipientEmail()
    {
        return $this->getValue(self::KEY_ERROR_RECIPIENT);
    }
}
