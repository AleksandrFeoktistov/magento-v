<?php
namespace Bluesnap\Payment\Gateway\Command;

use Magento\Payment\Gateway\Command;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;

class TransactionStrategyCommand implements CommandInterface
{
    protected $commandPool;
    protected $subjectReader;
    protected $dataObjectFactory;
    protected $customerRepositoryInterface;
    protected $registry;

    public function __construct(
        \Magento\Payment\Gateway\Command\CommandPoolInterface $commandPool,
        \Magento\Payment\Gateway\Helper\SubjectReader $subjectReader,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->commandPool = $commandPool;
        $this->subjectReader = $subjectReader;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->registry = $registry;
    }

    public function execute(array $commandSubject)
    {
        // implement in children
    }

    /**
     * @inheritdoc
     */
    public function createAndUpdateVaultedShopper(array $commandSubject)
    {
        $paymentData = $this->subjectReader->readPayment($commandSubject);
        /** @var \Magento\Sales\Model\Order\Payment\Interceptor $payment */
        $payment = $paymentData->getPayment();

        /**
         * We don't want to update the customer if they've already been updated this request. This can only happen
         * if the customer is checkout out via multiple delivery checkout.
         */
        if ($this->registry->registry('has_updated_vaulted')) {
            return;
        }


        if ($payment->getMethod() == \Bluesnap\Payment\Model\Ui\ConfigProvider::HOSTEDFIELDS_CODE) {
            // If this customer is logged in, and doesn't have a vaulted shopper ID, then perform a request to create one.
            if ($customerId = $payment->getOrder()->getCustomerId()) {
                $customer = $this->customerRepositoryInterface->getById($customerId);
                if (!$customer->getExtensionAttributes()->getVaultedShopperId()) {
                    $subject = array('customer' => $customer);
                    $this->commandPool->get('create_vaulted_shopper')->execute($subject);
                    $this->customerRepositoryInterface->save($customer);
                }

                // Update the vaulted shopper with the payment card, this may be a new card or an update to a current one.
                // from Magento's perspective, we don't care
		            if($paymentData->getPayment()->getAdditionalInformation('token') != '') {
				            $subject = [
					            'customer' => $customer,
					            'payment' => $paymentData
				            ];
				            $this->commandPool->get('update_vaulted_shopper')
					            ->execute($subject);
		            }
            }
        }

        /**
         * Set the flag so that we can recognise that this customer has already been updated. Updating again will cause an
         * error with the bluesnap API because the pf-token will have expired.
         */
        $this->registry->register('has_updated_vaulted', true);
    }
}

