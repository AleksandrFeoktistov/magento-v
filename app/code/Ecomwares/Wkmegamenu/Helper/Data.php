<?php
/**
* Copyright © 2016 SW-THEMES. All rights reserved.
*/
namespace Ecomwares\Wkmegamenu\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_objectManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\View\LayoutInterface $layoutInterface,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_storeInterface = $storeManager;
        $this->layoutInterface = $layoutInterface;
        $this->_objectManager= $objectManager;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }


    public function getSystemValue($path, $storeId = null)
    {
        return $this->scopeConfig->getValue($path,\Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
    }

}