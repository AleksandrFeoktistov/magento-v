<?php

namespace Bluesnap\Payment\Model\Method\Checks;

use Magento\Payment\Model\MethodInterface;
use Magento\Payment\Model\Checks\SpecificationInterface;
use Magento\Paypal\Model\Config;
use Magento\Quote\Model\Quote;

class SpecificationPlugin
{
    protected $state;

    public function __construct(\Magento\Framework\App\State $state)
    {
        $this->state = $state;
    }

    public function aroundIsApplicable(
        SpecificationInterface $specification,
        \Closure $proceed,
        MethodInterface $paymentMethod,
        Quote $quote
    ) {

        if ($this->state->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML
            && $paymentMethod->getCode() == \Bluesnap\Payment\Model\Ui\ConfigProvider::VAULT_CODE
        ) {
            return false;
        }

        return $proceed($paymentMethod, $quote);
    }
}
