<?php

namespace Bluesnap\Payment\Model;

class IpnHandler
{
    protected $handlers;

    public function __construct(array $handlers=[])
    {
        $this->handlers = $handlers;
    }

    public function handle($ipnDetails)
    {
        if (isset($ipnDetails['transactionType'])) {
            if (isset($this->handlers[$ipnDetails['transactionType']])) {
                $this->handlers[$ipnDetails['transactionType']]->handle($ipnDetails);
            } else {
                $this->handlers['fallback']->handle($ipnDetails);
            }
        }
    }
}
