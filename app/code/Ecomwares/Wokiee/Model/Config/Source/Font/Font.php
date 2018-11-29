<?php /**/
namespace Ecomwares\Wokiee\Model\Config\Source\Font;
class Font implements \Magento\Framework\Option\ArrayInterface
{
    /**     * Options getter     *     * @return array */
    public function toOptionArray()
    {
        return [['value' => 'custom', 'label' => __('Custom+')], ['value' => 'google', 'label' => __('Google Fonts+')], ['value' => 'Arial, "Helvetica Neue", Helvetica, sans-serif', 'label' => __('Arial, "Helvetica Neue", Helvetica, sans-serif')], ['value' => 'Georgia, serif', 'label' => __('Georgia, serif')], ['value' => '"Lucida Sans Unicode", "Lucida Grande", sans-serif', 'label' => __('"Lucida Sans Unicode", "Lucida Grande", sans-serif')], ['value' => 'Tahoma, Geneva, sans-serif', 'label' => __('Tahoma, Geneva, sans-serif')], ['value' => '"Trebuchet MS", Helvetica, sans-serif', 'label' => __('"Trebuchet MS", Helvetica, sans-serif')], ['value' => 'Verdana, Geneva, sans-serif', 'label' => __('Verdana, Geneva, sans-serif')],];
    }

    /**     * Get options in "key-value" format     *     * @return array */
    public function toArray()
    {
        $array = [];
        foreach ($this->toOptionArray() as $item) {
            $array[$item['value']] = $item['label'];
        }
        return $array;
    }
}