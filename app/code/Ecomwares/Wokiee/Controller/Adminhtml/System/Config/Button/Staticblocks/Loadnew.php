<?php /**/
namespace Ecomwares\Wokiee\Controller\Adminhtml\System\Config\Button\Staticblocks;

use Magento\Framework\Controller\ResultFactory;

class Loadnew extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $this->_objectManager->get('Ecomwares\Wokiee\Model\Config\Button\Staticblocks\Loadnew')->importStaticBlocks();
        $this->messageManager->addSuccess('Absent static blocks were installed successfully.');
        $this->_redirect('adminhtml/system_config/edit/section/wokiee_install');
    }
}