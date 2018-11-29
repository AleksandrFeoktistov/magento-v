<?php /**/
namespace Ecomwares\Wokiee\Controller\Adminhtml\System\Config\Button\Install;
use Magento\Framework\Controller\ResultFactory;

class Theme extends \Magento\Backend\App\Action
{
    public function execute()
    {
        if ($this->_objectManager->get('Ecomwares\Wokiee\Model\Config\Button\Install\Theme')->installTheme()) {
            $this->messageManager->addSuccess(__('Wokiee Theme with default skin was installed successfully.'));
        } else {
            $this->messageManager->addError(__('Wokiee Theme  wasn\'t installed. Check that you have uploaded theme files.'));
        }
        if ($this->_objectManager->get('Ecomwares\Wokiee\Model\Config\Button\Staticblocks\Loadnew')->importStaticBlocks()) {
            $this->messageManager->addSuccess(__('Static blocks were imported.'));
        } else {
            $this->messageManager->addError(__(' Static blocks were not installed.'));
        }
        if ($this->_objectManager->get('Ecomwares\Wokiee\Model\Config\Button\Staticpages\Loadnew')->importStaticPages()) {
            $this->messageManager->addSuccess(__('Static pages were imported.'));
        } else {
            $this->messageManager->addError(__(' Static pages were not installed.'));
        }
        $this->_redirect('adminhtml/system_config/edit/section/wokiee_install');
    }
}