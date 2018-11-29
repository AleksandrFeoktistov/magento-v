<?php /**/
namespace Ecomwares\Wokiee\Model\Config\Source;
class CategoriesType implements \Magento\Framework\Option\ArrayInterface
{
    /**     * Options getter     *     * @return array */
    public function toOptionArray()
    {
        return [
            ['value' => 'current', 'label' => __('Current Category')],
            ['value' => 'select', 'label' => __('Select Category')]
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