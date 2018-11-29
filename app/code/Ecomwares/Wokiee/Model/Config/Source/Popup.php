<?php /**/
namespace Ecomwares\Wokiee\Model\Config\Source;
class Popup implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 500, 'label' => __('500 ms')],
            ['value' => 1000, 'label' => __('1 sec')],
            ['value' => 2000, 'label' => __('2 sec')],
            ['value' => 3000, 'label' => __('3 sec')],
            ['value' => 4000, 'label' => __('4 sec')],
            ['value' => 5000, 'label' => __('5 sec')],
            ['value' => 6000, 'label' => __('6 sec')],
            ['value' => 7000, 'label' => __('7 sec')],
            ['value' => 8000, 'label' => __('8 sec')],
            ['value' => 9000, 'label' => __('9 sec')],
            ['value' => 10000, 'label' => __('10 sec')],
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