<?php

namespace Bluesnap\Payment\Model\Config\Source;

class PaymentAction implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [['value' => 'authorize', 'label' => __('Authorize Only')], ['value' => 'authorize_capture', 'label' => __('Authorize and Capture')]];
    }

    public function toArray()
    {
        return ['authorize' => __('Authorize Only'), 'authorize_capture' => __('Authorize and Capture')];
    }
}
