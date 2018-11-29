<?php
/**
 * Created by PhpStorm.
 * User: jvogel
 * Date: 16/11/17
 * Time: 15:50
 */
namespace Bluesnap\Payment\Helper;

use Magento\Framework\Math\Random;
use Magento\Framework\Session\SessionManagerInterface;

class Fraudsession {


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

		public function getFraudSessionId() {
				if(!$this->session->getData('fraud-session-id')){
						$fraudId = $this->mathRandom->getUniqueHash();
						$this->session->setData('fraud-session-id', $fraudId);
						$this->logger->debug('Creating fraud session id: ' . $fraudId);
				}

				return $this->session->getData('fraud-session-id');
		}

}