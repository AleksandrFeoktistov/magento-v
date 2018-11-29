<?php
namespace Ecomwares\Wkmegamenu\Model\Config;

class Animation implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'toggle', 'label' => __('toggle')],
            ['value' => 'fade', 'label' => __('fade')],
            ['value' => 'emersion', 'label' => __('emersion')],
            ['value' => 'emersion-vertical', 'label' => __('emersion-vertical')]
        ];
    }

    public function toArray()
    {
        return [
            'toggle' => __('toggle'),
            'fade' => __('fade'),
            'emersion' => __('emersion'),
            'emersion-vertical' => __('emersion-vertical')
        ];
    }
}
