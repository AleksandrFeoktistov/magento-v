<?php /**/
namespace Ecomwares\Wokiee\Model\Config\Source;
class TabsPosition implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'side',   'label' => __('Side Position')],
            ['value' => 'bottom', 'label' => __('Bottom Position')]
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