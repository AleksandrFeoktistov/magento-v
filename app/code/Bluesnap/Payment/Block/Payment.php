<?php

namespace Bluesnap\Payment\Block;

use Bluesnap\Payment\Model\Ui\ConfigProvider;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Payment
 */
class Payment extends Template
{
    protected $checkoutConfig;
    /**
     * @var ConfigProviderInterface
     */
    private $config;

    /**
     * Constructor
     *
     * @param Context $context
     * @param ConfigProviderInterface $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConfigProviderInterface $config,
        \Bluesnap\Payment\Model\CheckoutConfigProvider $checkoutConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
        $this->checkoutConfig = $checkoutConfig;
    }

    /**
     * @return string
     */
    public function getPaymentConfig()
    {
        $payment = $this->config->getConfig()['payment'];
        $config = $payment[$this->getCode()];
        $config['code'] = $this->getCode();
        $config['token_url'] = $this->getUrl('bluesnap/hostedfields/token');
        $config['cc_images'] = $this->checkoutConfig->getConfig()['bluesnap_hostedfields']['creditcard_images'];
        return json_encode($config, JSON_UNESCAPED_SLASHES);
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return ConfigProvider::HOSTEDFIELDS_CODE;
    }
}
