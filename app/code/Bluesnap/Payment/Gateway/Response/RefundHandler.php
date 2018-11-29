<?php

namespace Bluesnap\Payment\Gateway\Response;

class RefundHandler extends \Bluesnap\Payment\Gateway\Response\AbstractHandler
{
    protected $transactionType = 'refund';

    public function handle(array $handlingSubject, array $response)
    {
        return true;
    }
}
