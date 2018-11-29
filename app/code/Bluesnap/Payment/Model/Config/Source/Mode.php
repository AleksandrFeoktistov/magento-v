<?php

namespace Bluesnap\Payment\Model\Config\Source;

class Mode implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [['value' => 'sandbox', 'label' => __('Sandbox')], ['value' => 'production', 'label' => __('Production')]];
    }

    public function toArray()
    {
        return ['sandbox' => __('Sandbox'), 'production' => __('Production')];
    }
}
