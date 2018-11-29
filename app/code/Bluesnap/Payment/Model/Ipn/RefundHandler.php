<?php

namespace Bluesnap\Payment\Model\Ipn;

class RefundHandler extends AbstractHandler
{
    protected $creditmemoFactory;
    protected $creditmemoService;
    protected $orderRepository;

    public function __construct(
        \Magento\Sales\Model\Order $order,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Bluesnap\Payment\Gateway\Config\Config $config,
        \Magento\Sales\Model\Order\CreditmemoFactory $creditmemoFactory,
        \Magento\Sales\Model\Service\CreditmemoService $creditmemoService,
        \Magento\Sales\Model\OrderRepository $orderRepository

    ) {
        parent::__construct($order, $logger, $remoteAddress, $config);
        $this->creditmemoFactory = $creditmemoFactory;
        $this->creditmemoService = $creditmemoService;
        $this->orderRepository = $orderRepository;
    }

    public function handle($request)
    {
        if ($this->isValidIp()) {
            $this->logger->debug(array('recieved_ipn' => $request));
            $this->loadOrder($request);
            $this->addOrderComment($request);

            if (0 === strpos($request['reversalReason'], 'Magento generated refund for order')) {
                // If the reversal reason starts with our auto generated string then don't create the offline credit memo
                $this->orderRepository->save($this->order);
                return;
            }

            $data = array();
            if ($request['fullRefund'] == 'N') {
                $adjustment = abs($request['reversalAmount']);

                // Is this a refund for a DCC order?
                if ( $this->config->isDccActive() && $this->order->getBaseCurrencyCode() !== $this->order->getOrderCurrencyCode()) {
                    // In this case, the adjustment refund comes back in the order currency, so to align it with Magento,
                    // we need to scale it up.
                    $adjustment = $adjustment / $this->order->getBaseToOrderRate();
                }

                /**
                 * If partial refund, we can't know which items are being wholly or partially refunded in the order. The way
                 * we will deal with this is to set them all to 0 and then set a positive adjustment amount to whatever the
                 * refund needs to be.
                 *
                 * The exception to this is if the total amount refunded is equal to the grand total (the order is completely refunded).
                 * In this event, we will mark all the items as refunded.
                 */
                if ($adjustment + $this->order->getBaseTotalRefunded() < $this->order->getBaseGrandTotal()) {
                    $data['qtys'] = [];
                    foreach ($this->order->getAllItems() as $orderItem) {
                        $data['qtys'][$orderItem->getId()] = 0;
                    }
                } else {
                    $data['qtys'] = [];
                    foreach ($this->order->getAllItems() as $orderItem) {
                        $data['qtys'][$orderItem->getId()] = 1;
                    }
                    /**
                     * This is important. We use this line to negate the refund amount which would be calculated from above.
                     * We do this so that the item(s) will be marked as refunded, and therefore the order will be marked as
                     * closed.
                     */
                    $data['adjustment_negative'] = $this->order->getBaseSubtotal() + $this->order->getBaseTaxAmount();;
                }

                $data['shipping_amount'] = 0;
                $leftToRefund = ($this->order->getBaseSubtotal() + $this->order->getBaseTaxAmount()) - $this->order->getBaseTotalRefunded();
                if ($adjustment > $leftToRefund) {
                    // There's more to refund than exists in the items. This means we need to spread the discount into the
                    // shipping also.
                    if ($leftToRefund > 0) {
                        $data['adjustment_positive'] = $leftToRefund;
                    } else {
                        $leftToRefund = 0;
                    }
                    $data['shipping_amount'] = $adjustment - $leftToRefund;
                } else {
                    $data['adjustment_positive'] = $adjustment;
                }
            } else {
                /**
                 * If we get a full refund after receiving a partial refund, we need to adjust out the amount which has been
                 * refunded already. Otherwise, Magento will attempt to refund the whole order amount which will throw an error.
                 */
                $data['adjustment_negative'] = $this->order->getTotalRefunded();
            }

            $creditmemo = $this->creditmemoFactory->createByOrder($this->order, $data);
            $this->creditmemoService->refund($creditmemo, true);
        }
    }

    public function addOrderComment($request)
    {
        $comment = sprintf('Received %s IPN.', $request['transactionType']);
        if (isset($request['reversalReason'])) {
            $comment .= sprintf(' Reason: %s.', $request['reversalReason']);
            $comment .= sprintf(' Amount: %s.', $request['reversalAmount']);
            $comment .= sprintf(' Currency: %s.', $request['currency']);
        }
        $this->order->addStatusHistoryComment($comment);
    }
}
