<?php

namespace Bluesnap\Payment\Model\Ipn;

abstract class AbstractHandler
{
    protected $order;
    protected $logger;

    protected $remoteAddress;

    protected $config;

    public function __construct(
        \Magento\Sales\Model\Order $order,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Bluesnap\Payment\Gateway\Config\Config $config
    ) {
        $this->order = $order;
        $this->logger = $logger;
        $this->remoteAddress = $remoteAddress;
        $this->config = $config;
    }

    protected function loadOrder($request)
    {
        $incrementId = $request['merchantTransactionId'];
        $this->order->loadByIncrementId($incrementId);
    }

    protected function addOrderComment($request)
    {
        $this->order->addStatusHistoryComment(sprintf('Received %s IPN.', $request['transactionType']));
    }

    protected function isValidIp()
    {
        $ip = $this->remoteAddress->getRemoteAddress();
        $validIps = $this->config->getIpnIpWhitelist();
        $validIps = explode(',', $validIps);
        return in_array($ip, $validIps);
    }

    abstract public function handle($request);
}

