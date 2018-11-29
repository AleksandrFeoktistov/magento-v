<?php

namespace Bluesnap\Payment\Block;

class Form extends \Magento\Payment\Block\Form
{
    protected $_template = "Bluesnap_Payment::hostedfields/form.phtml";
    protected $config;
    protected $checkoutConfig;

    protected $customerSession;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Bluesnap\Payment\Model\Ui\ConfigProvider $config,
        \Bluesnap\Payment\Model\CheckoutConfigProvider $checkoutConfig,
        \Magento\Customer\Model\Session $customerSession,
        array $data = [])
    {
        parent::__construct($context, $data);
        $this->config = $config;
        $this->checkoutConfig = $checkoutConfig;
        $this->customerSession = $customerSession;
    }

    public function getPaymentConfig()
    {
        return $this->jsonEncode($this->config->getConfig()['payment']);
    }

    public function getCode()
    {
        return $this->getMethodCode();
    }

    public function getTokenUrl()
    {
        return $this->getUrl('bluesnap/hostedfields/token');
    }

    public function isCustomerLoggedIn()
    {
        return $this->jsonEncode($this->customerSession->isLoggedIn());
    }

    public function getCardImages()
    {
        return $this->jsonEncode($this->checkoutConfig->getConfig()['bluesnap_hostedfields']['creditcard_images']);
    }

    public function getCurrencyChangeUrl()
    {
        return $this->getUrl('bluesnap/dcc/currencyswitch');
    }

    protected function jsonEncode($data)
    {
        return json_encode($data, JSON_UNESCAPED_SLASHES);
    }
}

