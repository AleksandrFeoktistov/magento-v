<?php

namespace Bluesnap\Payment\Controller\Dcc;

class Currencyswitch extends \Magento\Framework\App\Action\Action
{
    protected $checkoutSession;
    protected $quoteRepository;
    protected $jsonResultFactory;
    protected $localeFormat;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\Locale\Format $localeFormat
    ) {
        $this->jsonResultFactory = $jsonResultFactory;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->localeFormat = $localeFormat;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->jsonResultFactory->create();
        $responseJson = ['result' => 'success'];

        try {
            $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
            $currency = (string)$this->getRequest()->getParam('currency');
            if ($currency) {
                $storeManager->getStore()->setCurrentCurrencyCode($currency);
                $quote = $this->checkoutSession->getQuote();
                $quote->setQuoteCurrencyCode($currency);
                $priceFormat = $this->localeFormat->getPriceFormat(null, $currency);
                $responseJson['newPriceFormat'] = $priceFormat;
                $responseJson['price'] = $quote->getGrandTotal();
            }
        } catch (\Exception $e) {
            $responseJson = ['result' => 'error'];
        }

        $result->setData($responseJson);
        return $result;
    }
}
