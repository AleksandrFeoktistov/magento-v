<?php

namespace Bluesnap\Payment\Gateway\Response;

class ConvertHandler extends \Bluesnap\Payment\Gateway\Response\AbstractHandler
{

    public function handle(array $handlingSubject, array $response)
    {
        $body = $response['body'];
        $handlingSubject['data']->setValue((string) $body->{'currency-rate'}->{'conversion-rate'});
    }
}
