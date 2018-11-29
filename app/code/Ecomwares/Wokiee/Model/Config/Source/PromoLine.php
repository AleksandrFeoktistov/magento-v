<?php /**/
namespace Ecomwares\Wokiee\Model\Config\Source;
class PromoLine implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'once', 'label' => __('Once')],
            ['value' => 'limited', 'label' => __('Limited')]
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