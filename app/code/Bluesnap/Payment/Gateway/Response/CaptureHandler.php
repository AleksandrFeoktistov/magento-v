<?php

namespace Bluesnap\Payment\Gateway\Response;

class CaptureHandler extends \Bluesnap\Payment\Gateway\Response\AbstractHandler
{
    protected $transactionType = 'capture';

    public function handle(array $handlingSubject, array $response)
    {
        $paymentData = $this->subjectReader->readPayment($handlingSubject);
        $payment = $paymentData->getPayment();

        $body = $response['body'];
        $card = $body->{'credit-card'};

        $payment->setCcType((string)$card->{'card-type'});
        $payment->setCcLast4((string)$card->{'card-last-four-digits'});
        $payment->setTransactionId($this->addTransactionTypeSuffix((string)$body->{'transaction-id'}));
        $payment->setLastTransId($this->addTransactionTypeSuffix((string)$body->{'transaction-id'}));

        $this->handleProcessingInformation($payment, $body);

        $payment->setIsTransactionClosed(true);
    }
}
