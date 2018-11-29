<?php

namespace Bluesnap\Payment\Model\Ipn;

class FallbackHandler extends AbstractHandler
{
    public function handle($request)
    {
        if ($this->isValidIp()) {
            $this->logger->debug(array('recieved_ipn' => $request));
            $this->loadOrder($request);
            $this->addOrderComment($request);
            $this->order->save();
        }
    }
}
