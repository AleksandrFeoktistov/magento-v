<?php

namespace Bluesnap\Payment\Block;

class HostedFieldsScript extends \Magento\Framework\View\Element\Template
{
    protected $config;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $session;

    protected $fraudHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Bluesnap\Payment\Gateway\Config\Config $config,
        \Bluesnap\Payment\Helper\Fraudsession $fraudHelper,
        array $data = array()
    ) {
        $this->config = $config;
        $this->session = $context->getSession();
        $this->fraudHelper = $fraudHelper;
        \Magento\Framework\View\Element\Template::__construct($context, $data);
    }

    public function getScriptUrl()
    {
        return $this->config->getUrl() . 'services/hosted-payment-fields/v1.0/bluesnap.hpf.mini.js';
    }

    public function getFraudIframeUrl()
    {
        if ($this->config->getMode() == 'production') {
            return "//www.bluesnap.com/servlet/logo.htm?s=" . $this->fraudHelper->getFraudSessionId();
        } else {
            return "//sandbox.bluesnap.com/servlet/logo.htm?s=" . $this->fraudHelper->getFraudSessionId();
        }
    }

    public function getFraudGifUrl()
    {
        if ($this->config->getMode() == 'production') {
            return "//www.bluesnap.com/servlet/logo.gif?s=" . $this->fraudHelper->getFraudSessionId();
        } else {
            return "//sandbox.bluesnap.com/servlet/logo.gif?s=" . $this->fraudHelper->getFraudSessionId();
        }
    }
}

