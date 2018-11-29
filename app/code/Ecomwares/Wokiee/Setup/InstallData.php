<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ecomwares\Wokiee\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
	/**
	 * EAV setup factory
	 *
	 * @var EavSetupFactory
	 */
	private $eavSetupFactory;

	/**
	 * Init
	 *
	 * @param EavSetupFactory $eavSetupFactory
	 */
	public function __construct(EavSetupFactory $eavSetupFactory)
	{
		$this->eavSetupFactory = $eavSetupFactory;
	}


	/**
	 * create featured product attribute
	 * {@inheritdoc}
	 */

	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

		$setup_attributes = array(
			'thm_featured' => array(
				'group' => 'Product Details',
				'type' => 'int',
				'backend' => '',
				'frontend' => '',
				'label' => 'Featured Product',
				'input' => 'boolean',
				'class' => '',
				'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
				'sort_order' => 3,
				'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
				'visible' => true,
				'required' => false,
				'user_defined' => false,
				'default' => '0',
				'searchable' => false,
				'filterable' => false,
				'comparable' => false,
				'visible_on_front' => true,
				'used_in_product_listing' => true,
				'unique' => false,
				'apply_to' => 'simple,virtual,bundle,downloadable,grouped,configurable'
			),
			'img_hover' => array(
				'group' => 'Image Management',
				'type' => 'varchar',
				'label' => 'Hover Image',
				'input' => 'media_image',
				'frontend' => 'Magento\Catalog\Model\Product\Attribute\Frontend\Image',
				'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'user_defined' => false,
				'filterable' => false,
				'visible_on_front' => false,
				'used_in_product_listing' => true,
				'sort_order' => 10,
				'required' => false
			),
		);

		foreach($setup_attributes as $key => $value) {
			$eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, $key, $value);
		}

	}

}