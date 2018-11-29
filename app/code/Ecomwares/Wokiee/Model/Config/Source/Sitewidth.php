<?php /**/
namespace Ecomwares\Wokiee\Model\Config\Source;
class Sitewidth implements \Magento\Framework\Option\ArrayInterface
{
    /**     * Options getter     *     * @return array */
    public function toOptionArray()
    {
        return [['value' => 0, 'label' => __('Default 1170px')], ['value' => 1, 'label' => __('Boxed (not for Landing Content)')], ['value' => 2, 'label' => __('Fullwidth')]];
    }

    /**     * Get options in "key-value" format     *     * @return array */
    public function toArray()
    {
        $array = [];
        foreach ($this->toOptionArray() as $item) {
            $array[$item['value']] = $item['label'];
        }
        return $array;
    }
}