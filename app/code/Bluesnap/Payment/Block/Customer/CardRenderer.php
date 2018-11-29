<?php
namespace Bluesnap\Payment\Block\Customer;

use Bluesnap\Payment\Model\Ui\ConfigProvider;
use Magento\Framework\View\Element\Template;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Vault\Block\AbstractCardRenderer;
use Bluesnap\Payment\Model\CheckoutConfigProvider;

class CardRenderer extends AbstractCardRenderer
{
    protected $checkoutConfigProvider;

    public function __construct(
        Template\Context $context,
        \Magento\Payment\Model\CcConfigProvider $iconsProvider,
        CheckoutConfigProvider $checkoutConfigProvider,
        array $data = [])
    {
        parent::__construct($context, $iconsProvider, $data);
        $this->checkoutConfigProvider = $checkoutConfigProvider;
    }


    /**
     * Can render specified token
     *
     * @param PaymentTokenInterface $token
     * @return boolean
     */
    public function canRender(PaymentTokenInterface $token)
    {
        return $token->getPaymentMethodCode() === ConfigProvider::HOSTEDFIELDS_CODE;
    }

    /**
     * @return string
     */
    public function getNumberLast4Digits()
    {
        return $this->getTokenDetails()['cc_last_4_digits'];
    }

    /**
     * @return string
     */
    public function getExpDate()
    {
        return $this->getTokenDetails()['cc_expiry'];
    }

    /**
     * @return string
     */
    public function getIconUrl()
    {
        return $this->getIconForType($this->getTokenDetails()['cc_type'])['url'];
    }

    /**
     * @return int
     */
    public function getIconHeight()
    {
        return $this->getIconForType($this->getTokenDetails()['cc_type'])['height'];
    }

    /**
     * @return int
     */
    public function getIconWidth()
    {
        return $this->getIconForType($this->getTokenDetails()['cc_type'])['width'];
    }

    public function getTokenDetails()
    {
        return unserialize($this->getToken()->getTokenDetails());
    }

    protected function getIconForType($type)
    {
        $config = $this->checkoutConfigProvider->getConfig();
        if (isset($config['bluesnap_vault']['creditcard_images'][$this->getTokenDetails()['cc_type']])) {
            return $config['bluesnap_vault']['creditcard_images'][$this->getTokenDetails()['cc_type']];
        }
        return parent::getIconForType($type);
    }
}
