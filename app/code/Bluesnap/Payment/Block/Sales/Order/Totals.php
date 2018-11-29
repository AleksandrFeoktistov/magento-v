<?php

namespace Bluesnap\Payment\Block\Sales\Order;

class Totals extends \Magento\Sales\Block\Order\Totals
{
    protected function _beforeToHtml()
    {
        $result = parent::_beforeToHtml();
        $payment = $this->getOrder()->getPayment();
        // If DCC active and the order was placed using Bluesnap then remove the total line which says something like "Grand Total to be Charged"
        $this->setTemplate('Magento_Sales::order/totals.phtml');
        if ($payment->getMethod() == \Bluesnap\Payment\Model\Ui\ConfigProvider::HOSTEDFIELDS_CODE && $payment->getAdditionalInformation('dcc_used')) {
            unset($this->_totals['base_grandtotal']);
        }
        return $result;
    }
}


