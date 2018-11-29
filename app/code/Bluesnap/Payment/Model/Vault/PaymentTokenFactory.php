<?php

namespace Bluesnap\Payment\Model\Vault;

use Magento\Framework\ObjectManagerInterface;
use Magento\Vault\Api\Data\PaymentTokenInterface;

class PaymentTokenFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * AccountPaymentTokenFactory constructor.
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create payment token entity
     * @return PaymentTokenInterface
     */
    public function create()
    {
        /** @var PaymentTokenInterface $paymentToken */
        $paymentToken = $this->objectManager->create(PaymentTokenInterface::class);
        $paymentToken->setType($this->getType());
        return $paymentToken;
    }

    public function getType()
    {
        return 'card';
    }
}
