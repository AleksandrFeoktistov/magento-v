<?php /**/
namespace Ecomwares\Wokiee\Controller\Adminhtml\System\Config\Button\Skins;

use Magento\Framework\Controller\ResultFactory;

class Load extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        $this->_objectManager->get('Ecomwares\Wokiee\Model\Config\Button\Skins\Load')->importSkin($this->getRequest()->getParam('skin'),$storeId,0);
        $this->messageManager->addSuccess('Skin was installed successfully.');
        $this->_redirect('adminhtml/system_config/edit/section/wokiee_install');
    }
}