<?php /**/
namespace Ecomwares\Wokiee\Model\Config\Source;
class FalseTrue implements \Magento\Framework\Option\ArrayInterface
{
    /**     * Options getter     *     * @return array */
    public function toOptionArray()
    {
        return [
            ['value' => 'true', 'label' => __('True')],
            ['value' => 'false', 'label' => __('False')]
        ];
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