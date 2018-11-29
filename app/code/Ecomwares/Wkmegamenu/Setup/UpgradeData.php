<?php

namespace Ecomwares\Wkmegamenu\Setup;

use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Setup\CategorySetupFactory;

class UpgradeData implements UpgradeDataInterface
{
    private $categorySetupFactory;

    public function __construct(CategorySetupFactory $categorySetupFactory)
    {
        $this->categorySetupFactory = $categorySetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Category::ENTITY);
            $attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId);

            $megamenu = array(
                'ys_cat_image' => array(
                    'type' => 'text',
                    'label' => 'Insert Image to 2-lvl category',
                    'input' => 'textarea',
                    'required' => false,
                    'sort_order' => 95,
                    'wysiwyg_enabled' => false,
                    'is_html_allowed_on_front' => true,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'Wokiee Megamenu',

                ),

            );

            foreach ($megamenu as $key => $value) {
                $categorySetup->addAttribute(\Magento\Catalog\Model\Category::ENTITY, $key, $value);
            }
            $idg = $categorySetup->getAttributeGroupId($entityTypeId, $attributeSetId, 'Wokiee Megamenu');
            foreach ($megamenu as $key => $value) {
                $categorySetup->addAttributeToGroup(
                    $entityTypeId,
                    $attributeSetId,
                    $idg,
                    $key,
                    $value['sort_order']
                );
            }
        }
        $installer->endSetup();
    }
}