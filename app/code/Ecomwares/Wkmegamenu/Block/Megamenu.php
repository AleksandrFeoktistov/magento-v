<?php /**/
namespace Ecomwares\Wkmegamenu\Block;
class Megamenu extends \Magento\Framework\View\Element\Template
{
    protected $_topMenu;
    protected $_objectManager;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Ecomwares\Wkmegamenu\Block\Megamenu\Html $topMenu)
    {
        $this->_topMenu = $topMenu;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct($context);
    }

    public function getHtml($outermostClass = '', $childrenWrapClass = '', $limit = 0)
    {
        return $this->_topMenu->getHtml($outermostClass, $childrenWrapClass, $limit);
    }

    public function getConfigOption($path, $storeId = 0)
    {
        return $this->_scopeConfig->getValue('wokiee_settings/' . $path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getBaseUrl()
    {
        $storeManager = $this->_objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        return $storeManager->getStore()->getBaseUrl();
    }
}