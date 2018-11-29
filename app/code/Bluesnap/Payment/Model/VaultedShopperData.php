<?php

namespace Bluesnap\Payment\Model;

class VaultedShopperData extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Bluesnap\Payment\Model\ResourceModel\VaultedShopperData');
    }
}
