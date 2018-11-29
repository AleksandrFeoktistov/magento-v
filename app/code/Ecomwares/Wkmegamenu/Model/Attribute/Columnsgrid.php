<?php /**/
namespace Ecomwares\Wkmegamenu\Model\Attribute;

class Columnsgrid extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['label' => __('1/12'), 'value' => 1],
                ['label' => __('2/12'), 'value' => 2],
                ['label' => __('3/12'), 'value' => 3],
                ['label' => __('4/12'), 'value' => 4],
                ['label' => __('5/12'), 'value' => 5],
                ['label' => __('6/12'), 'value' => 6]
            ];
        }
        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = [];
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|int $value
     * @return string|false
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }

}