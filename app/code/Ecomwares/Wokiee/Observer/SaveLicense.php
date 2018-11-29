<?php
namespace Ecomwares\Wokiee\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveLicense implements ObserverInterface
{
    protected $_messageManager;
    protected $_lic;

    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Ecomwares\Wokiee\Helper\Lic $lic
    ) {
        $this->_messageManager = $messageManager;
        $this->_lic = $lic;
    }


    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if($this->_lic->validateLicense()) {
            $this->_messageManager->getMessages(true);
            $this->_messageManager->addSuccess( __('Wokiee Theme was activated.') );
        } else {
            //if not return error message and clear lic_code value
            $this->_lic->clearLic();
            $this->_messageManager->addError( __('Wrong Purchase Code.') );
        }
    }
}