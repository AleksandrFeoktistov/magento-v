<?php
namespace Ecomwares\Wokiee\Block\Products;

class Categories extends \Magento\Framework\View\Element\Template
{
    protected $_productCollectionFactory;
    protected $_categoryFactory;
    protected $_registry;
    protected $_resourceFactory;
    protected $_collection;
    protected $_productCollection;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Block\Product\ListProduct $listProductBlock,
        \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $collectionFactory,
        \Magento\Reports\Model\ResourceModel\Report\Collection\Factory $resourceFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection,
        array $data = []
    ) {
        $this->_categoryFactory = $categoryFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_registry = $registry;
        $this->listProductBlock = $listProductBlock;
        $this->_collectionFactory = $collectionFactory;
        $this->_resourceFactory = $resourceFactory;
        $this->_collection = $collection;
        parent::__construct($context, $data);
    }

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getCurrentCategory()
    {
        return $this->_registry->registry('current_category');
    }

    public function getAddToCartPostParams($product)
    {
        return $this->listProductBlock->getAddToCartPostParams($product);
    }

    public function getCategoryProduct($categoryId,$productsLimit,$shuffle,$productType)
    {
        $todayDate = date('Y-m-d');
        $category = $this->_categoryFactory->create()->load($categoryId);
        $collection = $this->_productCollectionFactory->create();
        $collection->addUrlRewrite();
        $collection->addAttributeToSelect('*');
        $collection->addCategoryFilter($category);
        $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
        $collection->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $collection->setPageSize($productsLimit);
        //shuffle products
        if ($shuffle == 1) {
            $collection->getSelect()->orderRand();
        }
        if ($productType == 'new') {
            $collection->addAttributeToFilter(
                'news_from_date',
                [
                    'or' => [
                        0 => ['date' => true, 'to' => $todayDate],
                        1 => ['is' => new \Zend_Db_Expr('null')],
                    ]
                ])->addAttributeToFilter(
                [
                    ['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
                ]
            );
        }
        if ($productType == 'special') {
            $collection->addAttributeToFilter('special_price', ['neq' => '']);
        }
        if ($productType == 'featured') {
            $collection->addAttributeToFilter('thm_featured' , '1');
        }
        $collection->setCurPage(1);
        return $collection;
    }


    public function getBestsellers($productsLimit,$shuffle) {
        $sqlQuery = "e.entity_id = aggregation.product_id";
        $this->_collection->clear()->getSelect()->reset('where');
        $collection = $this->_collection->addAttributeToSelect('*');
        $collection->getSelect()->joinRight(
            array('aggregation' => 'sales_bestsellers_aggregated_monthly'),
            $sqlQuery,
            array('SUM(aggregation.qty_ordered) AS sold_quantity')
        )->group('e.entity_id')->order($shuffle)->limit($productsLimit);
        $collection->getSelect();

        $this->_productCollection = $collection;
        return $this->_productCollection;
    }

    public function getProductPrice($product)
    {
        $priceRender = $this->getLayout()->getBlock('product.price.render.default')
            ->setData('is_product_list', true);

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                [
                    'include_container' => true,
                    'display_minimal_price' => true,
                    'zone' => \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
                    'list_category_page' => true
                ]
            );
        }

        return $price;
    }
}