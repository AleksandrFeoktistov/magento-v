<?php

namespace Ecomwares\Wokiee\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_objectManager;
    protected $wishlistHelper;
    protected $compareHelper;
    protected $storeManager;
    protected $_collection;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\View\LayoutInterface $layoutInterface,
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        \Magento\Catalog\Helper\Product\Compare $compareHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Directory\Model\Currency $currencyModel,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
    ) {
        $this->_storeInterface = $storeManager;
        $this->layoutInterface = $layoutInterface;
        $this->_objectManager= $objectManager;
        $this->wishlistHelper= $wishlistHelper;
        $this->compareHelper= $compareHelper;
        $this->customerSession = $customerSession;
        $this->currenciesModel = $currencyModel;
        $this->_request = $request;
        $this->_collection = $collection;
        parent::__construct($context);
    }

    public function checkPage() {
        return $this->_request->getFullActionName();
    }

    public function getCurrentCategory() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $category = $objectManager->get('Magento\Framework\Registry')->registry('current_category');//get current category
        if($this->_request->getFullActionName() == 'catalog_category_view') {
            return $category->getPageLayout();
        } else {
        return false;
        }
    }

    public function countCurrencies()
    {
        $currencies = count($this->currenciesModel->getConfigAllowCurrencies());
        return $currencies;
    }

    function getStoreInterface(){
        return $this->_storeInterface;
    }

    function getLayoutInterface(){
        return $this->layoutInterface;
    }

    public function isLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }

    public function getBaseUrl()
    {
        return $this->_storeInterface->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    public function getSystemValue($path, $storeId = null)
    {
        return $this->scopeConfig->getValue($path,\Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
    }

    public function getConfigOption($path)
    {
        return $this->scopeConfig->getValue('wokiee_settings/'.$path,\Magento\Store\Model\ScopeInterface::SCOPE_STORE,$this->_storeInterface->getStore()->getId());
    }

    function getStoreId(){
        return $this->_storeInterface->getStore()->getId();
    }

    public function getMediaUrl()
    {
        return  $this->_storeInterface->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'wokiee/';
    }

    function newLabel($_product){
        $enable = $this->getSystemValue('wokiee_settings/product_labels/label_new');
        $label_txt = $this->getSystemValue('wokiee_settings/product_labels/label_new_txt');
        if (!$enable) {
            return false;
        }
        $output='';
        $now = date("Y-m-d");
        $newsFrom = substr($_product->getNewsFromDate(), 0, 10);
        $newsTo = substr($_product->getNewsToDate(), 0, 10);
        $new = false;

        if (!empty($newsFrom) && !empty($newsTo)) {
            if ($now >= $newsFrom && $now <= $newsTo) $new = true;

        } elseif (!empty($newsFrom) && empty($newsTo)) {
            if ($now >= $newsFrom) $new = true;

        } elseif (empty($newsFrom) && !empty($newsTo)) {
            if ($now <= $newsTo) $new = true;
        }
        if ($new)$output='<span class="tt-label-new">'.$label_txt.'</span>';
        return $output;
    }

    function saleLabel($_product){
        $enable = $this->getSystemValue('wokiee_settings/product_labels/label_sale');
        $label_txt = $this->getSystemValue('wokiee_settings/product_labels/label_sale_txt');
        $label_sale_discount = $this->getSystemValue('wokiee_settings/product_labels/label_sale_discount');
        if (!$enable) {
            return false;
        }
        $output=[];
        $_productType = $_product->getTypeID();
        if($_productType == 'simple'){
            $now = date("Y-m-d");
            $specialFrom = substr($_product->getSpecialFromDate(), 0, 10);
            $specialTo = substr($_product->getSpecialToDate(), 0, 10);
            $special = false;
            if (!empty($specialFrom) && !empty($specialTo)) {
                if ($now >= $specialFrom && $now <= $specialTo) $special = true;

            } elseif (!empty($specialFrom) && empty($specialTo)) {
                if ($now >= $specialFrom) $special = true;

            } elseif (empty($specialFrom) && !empty($specialTo)) {
                if ($now <= $specialTo) $special = true;
            }
            if ($special) {
                $output[]='<span class="tt-label-sale">'.$label_txt;
                if($_product->getSpecialPrice() && $label_sale_discount) $output[]=(round(100-$_product->getSpecialPrice()/$_product->getPrice()*100)).'%';
                $output[]='</span>';
            }
        }
        return implode("\n", $output);
    }

    function featuredLabel($_product){
        $enable = $this->getSystemValue('wokiee_settings/product_labels/label_featured');
        $label_txt = $this->getSystemValue('wokiee_settings/product_labels/label_featured_txt');
        if (!$enable) {
            return false;
        }
        $output='';
        $featured = false;

        if($_product->getThmFeatured()) {
            $featured = true;
        }

        if ($featured)$output='<span class="tt-label-featured">'.$label_txt.'</span>';
        return $output;
    }

    function getBestsellers($_product) {
        $enable = $this->getSystemValue('wokiee_settings/product_labels/label_best');
        $label_txt = $this->getSystemValue('wokiee_settings/product_labels/label_best_txt');
        if (!$enable) {
            return false;
        }
        $output='';
        $best_ids = [];
        $_productID = $_product->getId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productCollection = $objectManager->create('Magento\Reports\Model\ResourceModel\Report\Collection\Factory');
        $collection = $productCollection->create('Magento\Sales\Model\ResourceModel\Report\Bestsellers\Collection');
        $collection->setPeriod('year');

        foreach ($collection as $item) {
            $best_ids[] = $item->getProductId();
        }

        if (in_array($_productID, $best_ids)) {
            $output='<span class="tt-label-best">'.$label_txt.'</span>';
        }
        return $output;
    }

    function getCountdown($_product){
        $output='';
        $_productType = $_product->getTypeID();
        if($_productType == 'simple') {
            $now = date("Y-m-d");
            $specialTo = substr($_product->getSpecialToDate(), 0, 10);
            if (!empty($specialTo)) {
                if ($now <= $specialTo)
                {
                    $output='<div class="tt-countdown_box"><div class="tt-countdown_inner"><div class="tt-countdown" data-date="'.date_format(date_create($_product->getSpecialToDate()),"Y-m-d").'"></div></div></div>';
                }

            }
        }
        return $output;
    }

    function getCountdownProduct($_product){
        $output='';
        $_productType = $_product->getTypeID();
        if($_productType == 'simple') {
            $now = date("Y-m-d");
            $specialTo = substr($_product->getSpecialToDate(), 0, 10);
            if (!empty($specialTo)) {
                if ($now <= $specialTo)
                {
                    $output='<div class="tt-wrapper"><div class="tt-countdown_box_02"><div class="tt-countdown_inner"><div class="tt-countdown" data-date="'.date_format(date_create($_product->getSpecialToDate()),"Y-m-d").'"></div></div></div></div>';
                }

            }
        }
        return $output;
    }

    public function getCurCategoryIds($cur_cat) {
        $cur_cat = $cur_cat->getProductCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('is_saleable', 1, 'left')
            ->addAttributeToSort('position','asc');
        $cur_cat_ids = $cur_cat->getAllIds();
        return $cur_cat_ids;
    }

    public function prev_next($product,$type) {
        $cur_cat = $product->getCategory();
        if(!$cur_cat) foreach($product->getCategoryCollection() as $parent_cat)  $cur_cat = $parent_cat;
        if(!$cur_cat) return false;
        $ids = $this->getCurCategoryIds($cur_cat);
        $product_index = array_search($product->getId(), $ids);
        if($type=='prev')$array_index=$product_index - 1;else $array_index=$product_index + 1;
        $prod=false;
        if (isset($ids[$array_index])) $prod = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($ids[$array_index]);
        return $prod;
    }

    function getMedia($_product){

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($_product->getId());

        return  $product->getMediaGalleryImages();

    }

    public function getCssFile()
    {
        return $this->getMediaUrl(). 'css/' . $this->_storeInterface->getStore()->getCode() . '.css';
    }

    function getWishlistCount()
    {
        return $this->wishlistHelper->getItemCount();
    }

    function getCompareListUrl()
    {
        return $this->compareHelper->getListUrl();
    }

    function getCompareListCount()
    {
        return $this->compareHelper->getItemCount();
    }

    public function getUrl($route = '', $params = [])
    {
        return $this->_urlBuilder->getUrl($route, $params);
    }

    public function isHomePage()
    {
        $currentUrl = $this->getUrl('', ['_current' => true]);
        $urlRewrite = $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
        return $currentUrl == $urlRewrite;
    }


}