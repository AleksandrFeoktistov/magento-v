<?php /**/
namespace Ecomwares\Wokiee\Controller\Adminhtml\System\Config\Button\Staticblocks;

use Magento\Framework\Controller\ResultFactory;

class Rewrite extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $this->_objectManager->get('Ecomwares\Wokiee\Model\Config\Button\Staticblocks\Rewrite')->rewriteStaticBlocks();
        $this->messageManager->addSuccess('Static blocks were rewrited to default');
        $this->_redirect('adminhtml/system_config/edit/section/wokiee_install');
    }
}