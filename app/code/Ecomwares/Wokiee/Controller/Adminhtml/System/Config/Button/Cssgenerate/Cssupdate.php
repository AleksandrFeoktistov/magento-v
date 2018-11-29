<?php /**/
namespace Ecomwares\Wokiee\Controller\Adminhtml\System\Config\Button\Cssgenerate;
use Magento\Framework\Controller\ResultFactory;
class Cssupdate extends \Magento\Backend\App\Action{
    public function execute()
    {
        $this->_objectManager->get('Ecomwares\Wokiee\Model\Config\Button\Cssgenerate\Cssupdate')->refreshCssFiles();
        $this->messageManager->addSuccess(__('CSS files were successfully updated.'));
        $this->_redirect('adminhtml/system_config/edit/section/wokiee_colors');
    }
}