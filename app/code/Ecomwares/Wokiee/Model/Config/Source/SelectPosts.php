<?php
/**
 * Copyright © 2016
 */

namespace Ecomwares\Wokiee\Model\Config\Source;

/**
 * Used in creating options for permalink config value selection
 */
class SelectPosts implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'latest', 'label' => __('Latest: sort by "Publish At" post field')],
            ['value' => 'manual', 'label' => __('Manual: put post IDs in next config field')],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach($this->toOptionArray() as $item) {
            $array[$item['value']] = $item['label'];
        }
        return $array;
    }
}
