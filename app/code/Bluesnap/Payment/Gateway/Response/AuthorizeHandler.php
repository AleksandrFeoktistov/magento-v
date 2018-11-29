<?php

namespace Bluesnap\Payment\Gateway\Response;

class AuthorizeHandler extends \Bluesnap\Payment\Gateway\Response\AbstractHandler
{
    protected $transactionType = 'authorize';

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
        $this->handleVaultedShopper($payment, $body);
        $this->handleDcc($payment, $body);

        // Authorize transactions aren't closed. If we close it, it can't then be captured.
        $payment->setIsTransactionClosed(false);
    }
}
