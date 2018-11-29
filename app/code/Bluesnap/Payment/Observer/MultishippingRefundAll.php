<?php

namespace Bluesnap\Payment\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;


class MultishippingRefundAll implements ObserverInterface
{
    protected $messageManager;

    protected $transportBuilder;

    protected $storeManager;

    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig

    ) {
        $this->messageManager = $messageManager;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute(Observer $observer)
    {
        $this->messageManager->addErrorMessage('Unable to create all your orders, payment may have been taken. Please contact us to resolve.');
        $debugInformation = array();
        $orders = $observer->getOrders();

        $orderCounter = 1;
        foreach ($orders as $order) {
            $debugInformation['order_' . $orderCounter] = array(
                'customer_id' => $order->getCustomerId(),
                'customer_firstname' => $order->getCustomerFirstname(),
                'customer_lastname' => $order->getCustomerLastname(),
                'shipping_method' => $order->getShippingMethod(),
                'grand_total' => $order->getGrandTotal(),
                'total_paid' => $order->getTotalPaid() == null ? 0 : $order->getTotalPaid(),
                'currency' => $order->getOrderCurrencyCode(),
                'items' => array()
            );

            foreach ($order->getItemsCollection() as $item) {
                $debugInformation['order_' . $orderCounter]['items'][] = array(
                    'name' => $item->getName(),
                    'sku' => $item->getSku(),
                    'type' => $item->getProductType(),
                    'qty_ordered' => $item->getQtyOrdered(),
                    'qty_invoiced' => $item->getQtyInvoiced()
                );
            }
            $orderCounter += 1;
        }


        $transport = $this->transportBuilder
            ->setTemplateIdentifier('bluesnap_failed_multiaddress_template')
            ->setTemplateOptions(array(
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $this->storeManager->getStore()->getId()
            ))
            ->setTemplateVars(array(
                'debug_info' => print_r($debugInformation, true)
            ))
            ->setFrom(array(
                "name" => $this->scopeConfig->getValue('trans_email/ident_support/email',ScopeInterface::SCOPE_STORE),
                "email" => $this->scopeConfig->getValue('trans_email/ident_support/name',ScopeInterface::SCOPE_STORE)
            ))
            ->addTo(
                $this->scopeConfig->getValue('payment/bluesnap_hostedfields/error_recipient',ScopeInterface::SCOPE_STORE)
            )
            ->getTransport();

        $transport->sendMessage();
    }
}
