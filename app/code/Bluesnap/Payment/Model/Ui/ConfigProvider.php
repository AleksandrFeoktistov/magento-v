<?php
namespace Bluesnap\Payment\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;

final class ConfigProvider implements ConfigProviderInterface
{
    const HOSTEDFIELDS_CODE = 'bluesnap_hostedfields';
    const VAULT_CODE = 'bluesnap_vault';

    private $config;
    private $scopeConfig;
    protected $currency;
    protected $storeManager;


    public function __construct(
        \Bluesnap\Payment\Gateway\Config\Config $config,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Directory\Block\Currency $currency,
         \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->assetRepo = $assetRepo;
        $this->scopeConfig = $scopeConfig;
        $this->currency = $currency;
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                self::HOSTEDFIELDS_CODE => [
                    'isActive' => $this->config->isActive(),
                    'mode' => $this->config->getMode(),
                    'vault_enabled' => $this->getVaultActive()
                ],
                'ccform' => [
                    'cvvImageUrl' => [
                        self::HOSTEDFIELDS_CODE => $this->assetRepo->getUrl('Bluesnap_Payment::images/cvv.png')
                    ]
                ],
                'bluesnap_dcc' => [
                    'isActive' => $this->config->isDccActive(),
                    'availableCurrencies' => $this->formatCurrencies($this->currency->getCurrencies()),
                    'selectedCurrency' => $this->currency->getCurrentCurrencyCode(),
                    'baseCurrency' => $this->storeManager->getStore()->getBaseCurrency()->getCode()
                ]
            ]
        ];
    }

    public function formatCurrencies($currencies)
    {
        $result = array();
        foreach ($currencies as $value => $label) {
            $result []= array('value' => $value, 'label' => $label);
        }
        return $result;
    }

    public function getVaultActive()
    {
        return $this->scopeConfig->getValue(
            sprintf('payment/%s/%s', 'bluesnap_vault', 'active'),
            'store'
        ) == "1";
    }
}

