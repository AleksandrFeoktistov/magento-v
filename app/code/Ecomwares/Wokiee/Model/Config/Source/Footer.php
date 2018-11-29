<?php /**/
namespace Ecomwares\Wokiee\Model\Config\Source;
class Footer implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Footer 1')],
            ['value' => 2, 'label' => __('Footer 2')],
            ['value' => 3, 'label' => __('Footer 3')],
            ['value' => 4, 'label' => __('Footer 4')],
            ['value' => 5, 'label' => __('Footer 5')],
            ['value' => 6, 'label' => __('Footer 6')]
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