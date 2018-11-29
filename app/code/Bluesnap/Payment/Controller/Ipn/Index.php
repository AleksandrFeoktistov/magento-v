<?php

namespace Bluesnap\Payment\Controller\Ipn;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $ipnHandler;
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $rawResultFactory;

    /**
     * @var \Magento\Payment\Model\Method\Logger
     */
    protected $logger;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $rawResultFactory,
        \Magento\Payment\Model\Method\Logger $logger,
        \Bluesnap\Payment\Model\IpnHandler $ipnHandler
    ) {
        $this->rawResultFactory = $rawResultFactory;
        $this->logger = $logger;
        $this->ipnHandler = $ipnHandler;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->rawResultFactory->create();
        try {
            $params = $this->getRequest()->getParams();
            $this->ipnHandler->handle($params);
        } catch (\Exception $e) {
            $this->logger->debug(array('exception' => $e->getMessage()));
        }
        return $result;
    }
}
