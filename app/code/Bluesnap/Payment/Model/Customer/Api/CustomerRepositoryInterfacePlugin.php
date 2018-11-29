<?php

namespace Bluesnap\Payment\Model\Customer\Api;

use \Magento\Customer\Api\CustomerRepositoryInterface;

class CustomerRepositoryInterfacePlugin
{
    protected $vaultedShopperDataRepository;

    protected $customerExtensionFactory;

    public function __construct(
        \Bluesnap\Payment\Model\VaultedShopperDataFactory $vaultedShopperDataFactory,
        \Magento\Customer\Api\Data\CustomerExtensionFactory $customerExtensionFactory
    ) {
        $this->vaultedShopperDataFactory = $vaultedShopperDataFactory;
        $this->customerExtensionFactory = $customerExtensionFactory;
    }

    public function aroundGetById(CustomerRepositoryInterface $customerRepositoryInterface, \Closure $proceed, $customerId)
    {
        $result = $proceed($customerId);

        /** @var \Bluesnap\Payment\Model\VaultedShopperData $vaultedShopperData */
        $vaultedShopperData = $this->vaultedShopperDataFactory->create();
        $vaultedShopperData->getResource()->load($vaultedShopperData, $customerId, 'customer_id');

        $extensionAttributes = $this->getExtensionAttributes($result);
        $extensionAttributes->setVaultedShopperId($vaultedShopperData->getVaultedShopperId());

        return $result;
    }

    public function aroundSave(
        CustomerRepositoryInterface $customerRepositoryInterface,
        \Closure $proceed,
        \Magento\Customer\Api\Data\CustomerInterface $customer,
        $passwordHash = null
    ) {
        $result = $proceed($customer, $passwordHash);

        if ($customer->getExtensionAttributes() && $customer->getExtensionAttributes()->getVaultedShopperId()) {
            /** @var \Bluesnap\Payment\Model\VaultedShopperData $vaultedShopperData */
            $vaultedShopperData = $this->vaultedShopperDataFactory->create();
            $vaultedShopperData->getResource()->load($vaultedShopperData, $customer->getId(), 'customer_id');

            $vaultedShopperData->setData('customer_id', $customer->getId());
            $vaultedShopperData->setData('vaulted_shopper_id', $customer->getExtensionAttributes()->getVaultedShopperId());
            $vaultedShopperData->getResource()->save($vaultedShopperData);
        }

        return $result;
    }

    private function getExtensionAttributes($customer)
    {
        $extensionAttributes = $customer->getExtensionAttributes();
        if (null === $extensionAttributes) {
            $extensionAttributes = $this->customerExtensionFactory->create();
            $customer->setExtensionAttributes($extensionAttributes);
        }
        return $extensionAttributes;
    }
}
