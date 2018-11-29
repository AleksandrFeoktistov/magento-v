<?php

namespace Bluesnap\Payment\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Math\Random;
use Magento\Framework\Session\SessionManagerInterface;

class FraudSessionIdSetter implements ObserverInterface
{
    /**
     * @var Random
     */
    private $mathRandom;
    /**
     * @var SessionManagerInterface
     */
    private $session;

		/**
		 * @var \Magento\Payment\Model\Method\Logger
		 */
    protected $logger;

		/**
		 * FraudSessionIdSetter constructor.
		 *
		 * @param \Magento\Framework\Math\Random $mathRandom
		 * @param \Magento\Framework\Session\SessionManagerInterface $session
		 * @param \Magento\Payment\Model\Method\Logger $logger
		 */
    public function __construct(
        Random $mathRandom,
        SessionManagerInterface $session,
        \Psr\Log\LoggerInterface $logger
    )
    {

        $this->mathRandom = $mathRandom;
        $this->session = $session;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
    		if(!$this->session->getData('fraud-session-id')){
				    $fraudId = $this->mathRandom->getUniqueHash();
				    $this->session->setData('fraud-session-id', $fraudId);
				    $this->logger->debug('Creating fraud session id: ' . $fraudId);
        }
    }
}