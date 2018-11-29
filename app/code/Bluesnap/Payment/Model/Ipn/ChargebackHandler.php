<?php

namespace Bluesnap\Payment\Model\Ipn;

class ChargebackHandler extends AbstractHandler
{
    protected $creditmemoFactory;
    protected $order;
    protected $creditmemoService;
    protected $orderRepository;

    protected $adminInbox;

    public function __construct(
        \Magento\Sales\Model\Order $order,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Bluesnap\Payment\Gateway\Config\Config $config,
        \Magento\Sales\Model\Order\CreditmemoFactory $creditmemoFactory,
        \Magento\Sales\Model\Service\CreditmemoService $creditmemoService,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\AdminNotification\Model\Inbox $adminInbox

    ) {
        parent::__construct($order, $logger, $remoteAddress, $config);
        $this->creditmemoFactory = $creditmemoFactory;
        $this->creditmemoService = $creditmemoService;
        $this->orderRepository = $orderRepository;
        $this->adminInbox = $adminInbox;
    }

    public function handle($request)
    {
        if ($this->isValidIp()) {
            $this->logger->debug(array('recieved_ipn' => $request));
            $this->loadOrder($request);
            $this->addOrderComment($request);

            $data = array();
            // invoiceChargeAmount contains the amount of the CHARGEBACK. It's also negative, so run it through abs() to get
            // it as a positive value.
            $amount = abs($request['invoiceChargeAmount']);

            // Determine if the amount is the full order amount or partial order amount
            if ($amount < $this->order->getGrandTotal()) {
                $adjustment = $amount;

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
                    $data['adjustment_negative'] = $this->order->getBaseSubtotal() + $this->order->getBaseTaxAmount();
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
            $this->adminInbox->add(
                \Magento\Framework\Notification\MessageInterface::SEVERITY_MAJOR,
                sprintf('Chargeback received for %s', $this->order->getIncrementId()),
                sprintf("A chargeback has been received for order %s for %s%s. This order has therefore been automatically refunded for this amount. A chargeback occurs when a shopper contacts their card issuing bank and disputes a transaction they see on their statements.",
                    $this->order->getIncrementId(),
                    $request['invoiceChargeAmount'],
                    $request['invoiceChargeCurrency']
                ),
                $request['invoiceURL']
            );
        }
    }

    public function addOrderComment($request)
    {
        $comment = sprintf('Received %s IPN.', $request['transactionType']);
        $comment .= sprintf(' Amount: %s.', $request['invoiceChargeAmount']);
        $comment .= sprintf(' Currency: %s.', $request['invoiceChargeCurrency']);
        $this->order->addStatusHistoryComment($comment);
    }
}
