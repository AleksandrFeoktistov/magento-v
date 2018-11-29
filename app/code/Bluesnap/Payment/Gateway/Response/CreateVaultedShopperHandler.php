<?php

namespace Bluesnap\Payment\Gateway\Response;

class CreateVaultedShopperHandler extends \Bluesnap\Payment\Gateway\Response\AbstractHandler
{
    protected $transactionType = 'auth-reversal';

    public function handle(array $handlingSubject, array $response)
    {
        $vaultedShopperId = (string)$response['body']->{'vaulted-shopper-id'};
        /** @var \Magento\Customer\Model\Data\Customer $customer */
        $customer = $handlingSubject['customer'];
        $customer->getExtensionAttributes()->setVaultedShopperId($vaultedShopperId);
    }
}
