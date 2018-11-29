<?php

namespace Bluesnap\Payment\Controller\Vault;

class GetBilling extends \Magento\Framework\App\Action\Action
{
    protected $tokenManagement;
    protected $customerSession;
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonResultFactory;

    /**
     * @var \Magento\Payment\Gateway\Command\CommandPoolInterface
     */
    protected $commandPool;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var \Magento\Payment\Model\Method\Logger
     */
    protected $logger;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Payment\Gateway\Command\CommandPoolInterface $commandPool,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Vault\Api\PaymentTokenManagementInterface $tokenManagement,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->jsonResultFactory = $jsonResultFactory;
        $this->commandPool = $commandPool;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->logger = $logger;
        $this->tokenManagement = $tokenManagement;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->jsonResultFactory->create();

        try {
            $paymentToken = $this->tokenManagement->getByPublicHash(
                $this->getRequest()->getParam('public_hash'),
                $this->customerSession->getCustomerId()
            );

            $tokenDetails = unserialize($paymentToken->getTokenDetails());
            $billingAddress = $tokenDetails['billing_address'];

            $json = array(
                'result' => 'success',
                'address' => array(
                    'company' => $billingAddress['company'],
                    'telephone' => $billingAddress['telephone'],
                    'firstname' => $billingAddress['firstname'],
                    'lastname' => $billingAddress['lastname'],
                    'street' => $billingAddress['street'],
                    'city' => $billingAddress['city'],
                    'countryId' => $billingAddress['country_id'],
                    'postcode' => $billingAddress['postcode'],
                    'region' => $billingAddress['region'],
                    'region_id' => $billingAddress['region_id']
                ),
            );
        } catch (\Exception $e) {
            $this->logger->debug(array('exception' => $e->getMessage()));
            $json = array(
                'result' => 'error',
                'message' => __('Unable to initialise payment method. Please try again, if the problem persists please contact us.')
            );
        }

        $result->setData($json);
        return $result;
    }
}
