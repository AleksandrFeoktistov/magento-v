<?php /**/
namespace Ecomwares\Wokiee\Model\Config\Source;
class MenuColor implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'default', 'label' => __('Default')],
            ['value' => 'tt-color-scheme-01', 'label' => __('Dark')],
            ['value' => 'tt-color-scheme-02', 'label' => __('Light')]
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