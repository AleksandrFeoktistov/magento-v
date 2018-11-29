<?php /**/
namespace Ecomwares\Wokiee\Model\Config\Source;
class FooterTop implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'none', 'label' => __('Default')],
            ['value' => 'nomargin', 'label' => __('0 px')],
            ['value' => 'tt-offset-20', 'label' => __('20 px')],
            ['value' => 'tt-offset-40', 'label' => __('40 px')],

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