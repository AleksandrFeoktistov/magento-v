<?php /**/
namespace Ecomwares\Wkmegamenu\Setup;
use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Setup\CategorySetupFactory;

class InstallData implements InstallDataInterface
{

    private $categorySetupFactory;

    public function __construct(CategorySetupFactory $categorySetupFactory)
    {
        $this->categorySetupFactory = $categorySetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
        $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Category::ENTITY);
        $attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId);

        $megamenu = array(
            'ys_nav_simple' => array(
                'type' => 'int',
                'label' => 'Apply simple mode (shows only tree)',
                'input' => 'select',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required' => false,
                'sort_order' => 0,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Wokiee Megamenu'
            ),
            'ys_nav_simple_width' => array(
                'type' => 'text',
                'label' => 'Simple Menu Width',
                'input' => 'text',
                'required' => false,
                'sort_order' => 10,
                'wysiwyg_enabled' => false,
                'is_html_allowed_on_front' => true,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Wokiee Megamenu'
            ),

            'ys_nav_btm' => array(
                'type' => 'text',
                'label' => 'Html block under menu ',
                'input' => 'textarea',
                'required' => false,
                'sort_order' => 20,
                'wysiwyg_enabled' => false,
                'is_html_allowed_on_front' => true,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Wokiee Megamenu',
                'note'=>'This field is compatible only with 1st-level category'
            ),
            'ys_nav_top' => array(
                'type' => 'text',
                'label' => 'Top Html',
                'input' => 'textarea',
                'required' => false,
                'sort_order' => 30,
                'wysiwyg_enabled' => false,
                'is_html_allowed_on_front' => true,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Wokiee Megamenu',
            ),
            'ys_nav_left' => array(
                'type' => 'text',
                'label' => 'Left Html',
                'input' => 'textarea',
                'required' => false,
                'sort_order' => 40,
                'wysiwyg_enabled' => false,
                'is_html_allowed_on_front' => true,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Wokiee Megamenu',

            ),
            'ys_nav_left_width' => array(
                'type' => 'text',
                'label' => 'Left Html Width',
                'input' => 'select',
                'source' => 'Ecomwares\Wkmegamenu\Model\Attribute\Columnsgrid',
                'required' => false,
                'sort_order' => 50,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Wokiee Megamenu',
                'default' => '3'
            ),
            'ys_nav_right' => array(
                'type' => 'text',
                'label' => 'Megamenu Vertical Right Html',
                'input' => 'textarea',
                'required' => false,
                'sort_order' => 60,
                'wysiwyg_enabled' => false,
                'is_html_allowed_on_front' => true,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Wokiee Megamenu',
                'note'=>'This field is compatible only with 1st-level category'
            ),
            'ys_nav_right_width' => array(
                'type' => 'text',
                'label' => 'Right Html Width',
                'input' => 'select',
                'source' => 'Ecomwares\Wkmegamenu\Model\Attribute\Columnsgrid',
                'required' => false,
                'sort_order' => 70,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Wokiee Megamenu',
                'default' => '3'
            ),
            'ys_category_lable' => array(
                'type' => 'text',
                'label' => 'Category label, for ex. "Hot!"',
                'input' => 'text',
                'required' => false,
                'sort_order' => 80,
                'wysiwyg_enabled' => false,
                'is_html_allowed_on_front' => true,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Wokiee Megamenu',
                'note'=>'This field is compatible only with 1st-level category megamenu'
            ),
            'ys_label_color' => array(
                'type' => 'text',
                'label' => 'Category label background color',
                'input' => 'text',
                'required' => false,
                'sort_order' => 90,
                'wysiwyg_enabled' => false,
                'is_html_allowed_on_front' => true,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Wokiee Megamenu',
                'note'=>'This field is compatible only with 1st-level category megamenu'
            ),
            'ys_data_tm_width' => array(
                'type' => 'text',
                'label' => 'Top Menu Dropdown Width',
                'input' => 'select',
                'source' => 'Ecomwares\Wkmegamenu\Model\Attribute\Menuwidth',
                'required' => false,
                'sort_order' => 100,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Wokiee Megamenu',
                'default' => '1170'
            ),
            'ys_data_tm_align_vertical' => array(
                'type' => 'text',
                'label' => 'Top Menu Dropdown Align Vertical',
                'input' => 'select',
                'source' => 'Ecomwares\Wkmegamenu\Model\Attribute\Alignvertical',
                'required' => false,
                'sort_order' => 110,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Wokiee Megamenu',
                'default' => 'menu-bottom'
            ),
            'ys_data_tm_align_horizontal' => array(
                'type' => 'text',
                'label' => 'Top Menu Dropdown Align Horizontal',
                'input' => 'select',
                'source' => 'Ecomwares\Wkmegamenu\Model\Attribute\Alignhorizontal',
                'required' => false,
                'sort_order' => 120,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Wokiee Megamenu',
                'default' => 'menu-left'
            ),
            'ys_products_list_type' => array(
                'type' => 'text',
                'label' => 'Select Gallery Type',
                'input' => 'select',
                'source' => 'Ecomwares\Wkmegamenu\Model\Attribute\CarouselType',
                'required' => false,
                'sort_order' => 130,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Wokiee Megamenu',
                'default' => 'horizontal'
            ),
            'ys_gal_proportion' => array(
                'type' => 'text',
                'label' => 'Gallery-Banner cols width proportion',
                'input' => 'select',
                'source' => 'Ecomwares\Wkmegamenu\Model\Attribute\GalleryProp',
                'required' => false,
                'sort_order' => 140,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Wokiee Megamenu',
                'default' => 'cwc-1-2'
            ),
            'ys_gal_order' => array(
                'type' => 'text',
                'label' => 'Gallery-Banner sort order',
                'input' => 'select',
                'source' => 'Ecomwares\Wkmegamenu\Model\Attribute\GalleryOrder',
                'required' => false,
                'sort_order' => 150,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Wokiee Megamenu',
                'default' => 'ban-gal'
            ),
            'ys_products_title' => array(
                'type' => 'text',
                'label' => 'Product\'s block title',
                'input' => 'text',
                'required' => false,
                'sort_order' => 160,
                'wysiwyg_enabled' => false,
                'is_html_allowed_on_front' => true,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Wokiee Megamenu',
            ),
            'ys_products_ids' => array(
                'type' => 'text',
                'label' => 'Product\'s ids, separated by commas',
                'input' => 'text',
                'required' => false,
                'sort_order' => 170,
                'wysiwyg_enabled' => false,
                'is_html_allowed_on_front' => true,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Wokiee Megamenu',
                'note'=> 'Minimum two ids, only simple products',
            ),
            'ys_columns' => array(
                'type' => 'text',
                'label' => 'Columns categories per row',
                'input' => 'select',
                'source' => 'Ecomwares\Wkmegamenu\Model\Attribute\Columns',
                'required' => false,
                'sort_order' => 180,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Wokiee Megamenu',
                'default' => '3'
            ),
        );
        foreach($megamenu as $key => $value) {
            $categorySetup->addAttribute(\Magento\Catalog\Model\Category::ENTITY, $key, $value);
        }
        $idg =  $categorySetup->getAttributeGroupId($entityTypeId, $attributeSetId, 'Wokiee Megamenu');
        foreach($megamenu as $key => $value) {
            $categorySetup->addAttributeToGroup(
                $entityTypeId,
                $attributeSetId,
                $idg,
                $key,
                $value['sort_order']
            );
        }
        $installer->endSetup();
    }
}