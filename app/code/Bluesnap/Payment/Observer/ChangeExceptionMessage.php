<?php

namespace Bluesnap\Payment\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;


class ChangeExceptionMessage implements ObserverInterface
{
    public function execute(Observer $observer)
    {

        /** @var \Magento\Framework\Webapi\Rest\Response $response */
        $response = $observer->getResponse();
        $exceptions = $response->getException();
        if ($exceptions) {
            foreach ($exceptions as $exception) {
                if ($exception instanceof \Bluesnap\Payment\Model\Exception) {
                    $response->setException($exception);
                    break;
                }
            }
        }

    }
}
