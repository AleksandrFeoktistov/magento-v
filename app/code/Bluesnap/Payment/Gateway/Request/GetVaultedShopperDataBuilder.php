<?php
namespace Bluesnap\Payment\Gateway\Request;

class GetVaultedShopperDataBuilder extends AbstractDataBuilder
{
    public function getUrl(array $buildSubject)
    {
        /** @var \Magento\Customer\Model\Data\Customer $customer */
        $vaultedShopperId = $buildSubject['shopper_id'];
        return $this->config->getUrl() . 'services/2/vaulted-shoppers/' . $vaultedShopperId;
    }

    public function getMethod(array $buildSubject)
    {
        return 'GET';
    }

    /**
     * TODO docs
     */
    public function getBody(array $buildSubject)
    {
        return false;
    }
}
