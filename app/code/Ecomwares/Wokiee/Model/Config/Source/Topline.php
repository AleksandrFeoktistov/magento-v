<?php /**/
namespace Ecomwares\Wokiee\Model\Config\Source;
class Topline implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'container', 'label' => __('Boxed')],
            ['value' => 'container-fluid', 'label' => __('Wide')]
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