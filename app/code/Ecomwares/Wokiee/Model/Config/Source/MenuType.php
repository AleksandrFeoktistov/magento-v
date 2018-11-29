<?php /**/
namespace Ecomwares\Wokiee\Model\Config\Source;
class MenuType implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'tt-hover-01', 'label' => __('Type 1')],
            ['value' => 'tt-hover-02', 'label' => __('Type 2')]
        ];
    }
    public function toArray()
    {
        $array = [];
        foreach ($this->toOptionArray() as $item) {
            $array[$item['value']] = $item['label'];
        }
        return $array;
    }
}