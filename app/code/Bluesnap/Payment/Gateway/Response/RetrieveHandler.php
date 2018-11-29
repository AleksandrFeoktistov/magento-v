<?php

namespace Bluesnap\Payment\Gateway\Response;

class RetrieveHandler extends \Bluesnap\Payment\Gateway\Response\AbstractHandler
{

    /**
     * This command isn't currently used, so the implementation here is largely irrelevant.
     *
     * Currently it will update the payment model with data out of the response XML.
     *
     * I imagine it to be used something like this:
     *
     * $this->commandPool->get('retrieve')->execute(array('transaction_id' => '1012760135');
     *
     * @param array $handlingSubject
     * @param array $response
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentData = $this->subjectReader->readPayment($handlingSubject);
        $payment = $paymentData->getPayment();

        $body = $response['body'];
        $card = $body->{'credit-card'};

        $payment->setCcType((string)$card->{'card-type'});
        $payment->setCcLast4((string)$card->{'card-last-four-digits'});

        $this->handleProcessingInformation($payment, $body);
    }
}
