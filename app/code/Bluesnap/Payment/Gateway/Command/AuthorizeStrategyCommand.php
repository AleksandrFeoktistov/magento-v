<?php
namespace Bluesnap\Payment\Gateway\Command;

use Magento\Payment\Gateway\Command;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;

class AuthorizeStrategyCommand extends TransactionStrategyCommand
{
    /**
     * @inheritdoc
     */
    public function execute(array $commandSubject)
    {
        $this->createAndUpdateVaultedShopper($commandSubject);
        $this->commandPool->get('auth_only')->execute($commandSubject);
    }
}
