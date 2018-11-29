<?php
namespace Bluesnap\Payment\Gateway\Request;

class RefundDataBuilder extends AbstractDataBuilder
{
    public function getUrl(array $buildSubject)
    {
        $paymentData = $this->subjectReader->readPayment($buildSubject);
        $payment = $paymentData->getPayment();
        $amount = $this->subjectReader->readAmount($buildSubject);
        $order = $payment->getOrder();
        $transactionId = $this->stripTransactionTypeSuffix($payment->getParentTransactionId());

        $reason = 'Magento generated refund for order ' . $order->getIncrementId();
        $url = $this->config->getUrl() . 'services/2/transactions/' . $transactionId . '/refund';
        if ($amount == $order->getBaseGrandTotal()) {
            // We don't need to supply the amount if it's a full refund.
            $url .= '?reason='. urlencode($reason);
        } else {
            if ($payment->getAdditionalInformation('dcc_used')  && $order->getBaseCurrencyCode() !== $order->getOrderCurrencyCode()) {
                // DCC was used, we need to alter the $amount and the $currencyCode
                $priceFormat = $this->localeFormat->getPriceFormat(null, $order->getOrderCurrencyCode());
                // JPY is incorrectly coming back with a precision of 2, it should be 0. Handle it as follows.
                if ($order->getOrderCurrencyCode() == 'JPY') {
                    $priceFormat['precision'] = 0;
                }
                $amount = number_format($amount * $order->getBaseToOrderRate(), $priceFormat['precision'], '.', '');

            }

            // It's a partial refund, so we need to supply the amount.
            $url .= '?amount=' . $amount . '&reason=' . urlencode($reason);
        }
        return $url;
    }

    public function getMethod(array $buildSubject)
    {
        return 'PUT';
    }

    /**
     * The refund has no body.
     *
     * @param array $buildSubject
     * @return bool
     */
    public function getBody(array $buildSubject)
    {
        return false;
    }
}
