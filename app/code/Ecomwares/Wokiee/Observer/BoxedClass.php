<?php
namespace Ecomwares\Wokiee\Observer;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\Event\ObserverInterface;

class BoxedClass implements ObserverInterface
{
    protected $pageConfig;
    protected $scopeConfig;

    public function __construct(
        PageConfig $pageConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->pageConfig = $pageConfig;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $boxed_mode = $this->scopeConfig->getValue('wokiee_settings/general/boxed_mode', $storeScope);
        if($boxed_mode) {
            $this->pageConfig->addBodyClass('tt-boxed');
        }
    }
}