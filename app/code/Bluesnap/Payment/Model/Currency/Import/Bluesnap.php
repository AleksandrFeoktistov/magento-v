<?php

namespace Bluesnap\Payment\Model\Currency\Import;

/**
 * Currency rate import model (From www.webservicex.net)
 */
class Bluesnap extends \Magento\Directory\Model\Currency\Import\AbstractImport
{
    /**
     * Http Client Factory
     *
     * @var \Magento\Framework\HTTP\ZendClientFactory
     */
    protected $httpClientFactory;

    /**
     * @var \Magento\Payment\Gateway\Command\CommandPoolInterface
     */
    protected $commandPool;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * Core scope config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Payment\Gateway\Command\CommandPoolInterface $commandPool,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($currencyFactory);
        $this->commandPool = $commandPool;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string $currencyFrom
     * @param string $currencyTo
     * @param int $retry
     * @return float|null
     */
    protected function _convert($currencyFrom, $currencyTo, $retry = 0)
    {
        try {
            $subject = array('from' => $currencyFrom, 'to' => $currencyTo, 'data' => $this->dataObjectFactory->create());
            $this->commandPool->get('convert')->execute($subject);
            return $subject['data']->getValue();
        } catch (\Exception $ex) {
            $this->_messages[] = __('We can\'t retrieve a rate for %1.', $currencyTo);
        }
    }

}
