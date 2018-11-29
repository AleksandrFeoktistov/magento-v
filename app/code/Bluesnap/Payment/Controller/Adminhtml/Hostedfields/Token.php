<?php

namespace Bluesnap\Payment\Controller\Adminhtml\Hostedfields;

class Token extends \Magento\Backend\App\Action
{
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
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Payment\Gateway\Command\CommandPoolInterface $commandPool,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Payment\Model\Method\Logger $logger
    ) {
        $this->jsonResultFactory = $jsonResultFactory;
        $this->commandPool = $commandPool;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->logger = $logger;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->jsonResultFactory->create();

        try {
            $subject = array('data' => $this->dataObjectFactory->create());
            $this->commandPool->get('token')->execute($subject);
            $token = $subject['data']->getToken();

            $json = array(
                'result' => 'success',
                'token' => $token
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

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::create');
    }
}
