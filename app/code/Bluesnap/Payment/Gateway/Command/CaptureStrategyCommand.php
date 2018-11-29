<?php
namespace Bluesnap\Payment\Gateway\Command;

use Magento\Payment\Gateway\Command;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;

class CaptureStrategyCommand extends TransactionStrategyCommand
{
    /**
     * @inheritdoc
     */
    public function execute(array $commandSubject)
    {
        $paymentData = $this->subjectReader->readPayment($commandSubject);
        /** @var \Magento\Sales\Model\Order\Payment\Interceptor $payment */
        $payment = $paymentData->getPayment();

        // If this request has already been auth'd then we just need to capture.
        if ($payment->getAuthorizationTransaction()) {
            $this->commandPool->get('capture_only')->execute($commandSubject);
            return;
        }

        $this->createAndUpdateVaultedShopper($commandSubject);
        $this->commandPool->get('auth_capture')->execute($commandSubject);
    }
}
