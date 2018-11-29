<?php

namespace Bluesnap\Payment\Gateway\Response;

class GetVaultedShopperHandler extends \Bluesnap\Payment\Gateway\Response\AbstractHandler
{
    protected $transactionType = 'auth-reversal';

    public function handle(array $handlingSubject, array $response)
    {
        $handlingSubject['data']->setResponse($response['body']);
    }
}
