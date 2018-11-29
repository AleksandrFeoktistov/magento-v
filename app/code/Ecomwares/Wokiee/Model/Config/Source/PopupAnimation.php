<?php /**/
namespace Ecomwares\Wokiee\Model\Config\Source;
class PopupAnimation implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'style1', 'label' => __('FadeIn')],
            ['value' => 'style2', 'label' => __('Slide From Top')],
            ['value' => 'style3', 'label' => __('Slide From Right')],
            ['value' => 'style4', 'label' => __('Fall')],
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