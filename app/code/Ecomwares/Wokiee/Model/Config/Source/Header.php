<?php /**/
namespace Ecomwares\Wokiee\Model\Config\Source;
class Header implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Header 1')],
            ['value' => 2, 'label' => __('Header 2')],
            ['value' => 3, 'label' => __('Header 3')],
            ['value' => 4, 'label' => __('Header 4')]
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