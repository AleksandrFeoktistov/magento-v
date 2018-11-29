<?php /**/
namespace Ecomwares\Wokiee\Block;
class Template extends \Magento\Framework\View\Element\Template
{
    public $_coreRegistry;
    public $_objectManager;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $coreRegistry, array $data = [])
    {
        $this->_coreRegistry = $coreRegistry;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct($context, $data);
    }

    public function isLoggedIn()
    {
        $customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
        return $customerSession->isLoggedIn();
    }

    public function getBaseUrl()
    {
        $storeManager = $this->_objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        return $storeManager->getStore()->getBaseUrl();
    }

    public function getSkinUrl($path = '')
    {
        $storeManager = $this->_objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        return $storeManager->getStore()->getBaseUrl() . 'app/design/frontend/Ecomwares/wokiee/web/' . $path;
    }

    public function getSystemValue($path, $storeId = null)
    {
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getConfigOption($path)
    {
        return $this->_scopeConfig->getValue('wokiee_settings/' . $path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
    }

    function getStoreId(){
        return $this->_storeManager->getStore()->getId();
    }

    public function getMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'wokiee/';
    }

    public function getLogoSrc()
    {
        return $this->getMediaUrl() . '' . $this->getSystemValue('design/header/logo_src');
    }

    public function isHomePage()
    {
        $currentUrl = $this->getUrl('', ['_current' => true]);
        $urlRewrite = $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
        return $currentUrl == $urlRewrite;
    }

    public function getColorOption($path, $storeId = 0)
    {
        return $this->_scopeConfig->getValue('wokiee_colors/' . $path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
} ?>