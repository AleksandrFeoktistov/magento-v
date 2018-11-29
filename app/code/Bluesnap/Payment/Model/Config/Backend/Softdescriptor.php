<?php
namespace Bluesnap\Payment\Model\Config\Backend;

class Softdescriptor extends \Magento\Framework\App\Config\Value
{
    /**
     * Validate a base URL field value
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        if (strlen($value) > 20) {
            throw new \Magento\Framework\Exception\LocalizedException(__("Invalid Soft Descriptor: Must be 20 or fewer characters"));
        }
    }
}
