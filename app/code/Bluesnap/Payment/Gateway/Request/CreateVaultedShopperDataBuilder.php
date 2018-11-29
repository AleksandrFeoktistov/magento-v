<?php
namespace Bluesnap\Payment\Gateway\Request;

class CreateVaultedShopperDataBuilder extends AbstractDataBuilder
{
    public function getUrl(array $buildSubject)
    {
        return $this->config->getUrl() . 'services/2/vaulted-shoppers';
    }

    public function getMethod(array $buildSubject)
    {
        return 'POST';
    }

    /**
     * TODO docs
     *
     * @param array $buildSubject
     * @return mixed
     */
    public function getBody(array $buildSubject)
    {
        $customer = $buildSubject['customer'];

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><vaulted-shopper xmlns="http://ws.plimus.com"></vaulted-shopper>');
        $xml->addChild('first-name', $customer->getFirstName());
        $xml->addChild('last-name', $customer->getLastName());

        return $xml->asXML();
    }
}
