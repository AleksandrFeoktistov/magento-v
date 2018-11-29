<?php /**/
namespace Ecomwares\Wkmegamenu\Block\Megamenu;

class Html extends \Magento\Theme\Block\Html\Topmenu
{

    public function getBlockTemplateProcessor($content = '',$objectManager) {
        return  $objectManager->get('\Magento\Cms\Model\Template\FilterProvider')->getBlockFilter()->filter(trim($content));
    }

    protected function _getHtml(
        \Magento\Framework\Data\Tree\Node $menuTree,
        $childrenWrapClass,
        $limit,
        $colBrakes = [],
        $type_simple=0
    ) {
        $html = [];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = $parentLevel === null ? 0 : $parentLevel + 1;

        $counter = 1;
        $itemPosition = 1;
        $childrenCount = $children->count();


        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

        foreach ($children as $child) {
            $child->setLevel($childLevel);
            $child->setIsFirst($counter == 1);
            $child->setIsLast($counter == $childrenCount);
            $hasChildren=$child->hasChildren();
            $level=$childLevel;
            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();
            if($childLevel == 0) $outermostClass.=' ';
            if ($childLevel == 0 && $outermostClass) {

                $outermostClassCode = ' class="' . $outermostClass . '" ';
                $child->setClass($outermostClass);
            }

            $data=[];
            $category = $objectManager->get('\Magento\Catalog\Model\CategoryFactory')->create()->load(str_replace('category-node-','',$child->getId()));
            if($level==0 && $category->getData('ys_nav_simple'))$type_simple=1;
            if($level==0 && !$category->getData('ys_nav_simple'))$type_simple=0;

            if(!$type_simple)
            {

                if($level==0)
                {
                    $data['btm']=$this->getBlockTemplateProcessor($category->getData('ys_nav_btm'),$objectManager);
                    $data['right']=$this->getBlockTemplateProcessor($category->getData('ys_nav_right'),$objectManager);
                    $data['left']=$this->getBlockTemplateProcessor($category->getData('ys_nav_left'),$objectManager);
                    $data['top']=$this->getBlockTemplateProcessor($category->getData('ys_nav_top'),$objectManager);

                    $left_col = $category->getData('ys_nav_left_width');
                    if(!$category->getData('ys_nav_left'))$left_col=0;
                    $right_col = $category->getData('ys_nav_right_width');
                    if(!($category->getData('ys_nav_right')||$category->getData('ys_products_ids')))$right_col=0;
                    $center_col = 12-$left_col-$right_col;
                }

                if($level == 0)
                {
                    $html[]= '<li>';
                } elseif($level == 1)
                {
                    $html[]= '<li class="TonyM__ttl">';
                } else
                {
                    if ($hasChildren) {
                        $html[]= '<li class="TonyM__dd">';
                    } else {
                        $html[]= '<li>';
                    }
                }

                if($level == 0)
                {
                    $html[]= '<a href="' . $child->getUrl() . '">';
                } elseif($level==1)
                {
                    $html[]= '<a href="' . $child->getUrl() . '">';
                } else
                {
                    $html[]= '<a href="' . $child->getUrl() . '" ' . $outermostClassCode . '>';
                }

                $html[]= $this->escapeHtml($child->getName());

                if($level == 1) {
                    if(!empty($category->getData('ys_cat_image')))
                        $html[]= $this->getBlockTemplateProcessor($category->getData('ys_cat_image'),$objectManager);
                }

                if($level == 0){
                    if(!empty($category->getData('ys_category_lable')))
                        $html[]='<span class="TonyM__label">'.$category->getData('ys_category_lable').'</span>';
                }


                $html[]= '</a>';

                if($level==0 && $hasChildren)$html[]='<div class="TonyM__mm TonyM__mm--mega" data-tm-w="'.$category->getData('ys_data_tm_width').'" data-tm-a-v="'.$category->getData('ys_data_tm_align_vertical').'" data-tm-a-h="'.$category->getData('ys_data_tm_align_horizontal').'">';

                if($level==0) {
                    if(!empty($data['top']))
                    {
                        $html[]='<div class="TonyM__bx-t">'.$data['top'].'</div>';
                    }
                };

                if($level==0 && $hasChildren) {
                    $html[]='<div class="TonyM__bx-out-c">';
                }

                if($level==0) {
                    if(!empty($data['left']))
                    {
                        $html[]='<div class="TonyM__bx-l TonyM--gr12-c'.$left_col.'">'.$data['left'].'</div>';
                    }
                };


                if($level==0 && $hasChildren) {
                    $html[]='<div class="TonyM__bx-c TonyM--gr12-c'.$center_col.'">
                                <div class="TonyM__bx-in-c">
                                    <ul class="TonyM__list TonyM--gr'.$category->getData('ys_columns').'-in">';
                };
                $html[]= $this->_addSubMenuMegamenu($child,$childLevel,$childrenWrapClass,$limit,$data);
                if($level==0 && $hasChildren)$html[]='</ul></div></div>';


                if($level==0){
                    if(!empty($category->getData('ys_products_ids')))
                    {
                        if ($category->getData('ys_products_list_type') == 'horizontal') {
                            $wrp_type = 'TonyM__featured';
                            $crsl_type = $this->getProductsFeatured($category->getData('ys_products_ids'));
                        } else {
                            $wrp_type = 'TonyM__special';
                            $crsl_type = $this->getProductsSpecials($category->getData('ys_products_ids'));
                        }

                        $html[]='<div class="TonyM__bx-r TonyM--gr12-c'.$right_col.'">';
                        if(!empty($data['right'])){
                            $html[]='<div class="cols-with-carousel '.$category->getData('ys_gal_proportion').'">';
                            if($category->getData('ys_gal_order') == 'ban-gal') {
                                $html[]='<div class="col-with-carousel-1">'.$data['right'].'</div>';
                                $html[]='<div class="col-with-carousel-2">
                                        <div class="tt-title-submenu">'.$category->getData('ys_products_title').'</div>
                                            <div class="'.$wrp_type.' tt-menu-slider header-menu-product arrow-location-03 row">
                                                '.$crsl_type.'
                                            </div>
                                        </div>';
                            } else {
                                $html[]='<div class="col-with-carousel-1">
                                        <div class="tt-title-submenu">'.$category->getData('ys_products_title').'</div>
                                            <div class="'.$wrp_type.' tt-menu-slider header-menu-product arrow-location-03 row">
                                                '.$crsl_type.'
                                            </div>
                                        </div>';
                                $html[]='<div class="col-with-carousel-2">'.$data['right'].'</div>';
                            }
                            $html[]='</div>';
                        } else {
                            $html[]='<div class="tt-title-submenu">'.$category->getData('ys_products_title').'</div>
                                    <div class="'.$wrp_type.' tt-menu-slider header-menu-product arrow-location-03 row">
                                        '.$crsl_type.'
                                    </div>';
                        }
                        $html[]='</div>';

                    }elseif(!empty($data['right'])){
                        $html[]='<div class="TonyM__bx-r TonyM--gr12-c'.$right_col.'">'.$data['right'].'</div>';
                    }
                };

                if($level==0 && $hasChildren) {
                    $html[]='</div>';
                }

                if($level==0) {
                    if(!empty($data['btm']))
                    {
                        $html[]='<div class="TonyM__bx-b">'.$data['btm'].'</div>';
                    }
                };

                if($level==0 && $hasChildren)$html[]='</div>';
                $html[]= '</li>';

            } else  {

                //$html[]= '<li>';
                if($level == 0)
                {
                    $html[]= '<li>';

                } else
                {
                    if ($hasChildren) {
                        $html[]= '<li class="TonyM__dd">';
                    } else {
                        $html[]= '<li>';
                    }
                }


                if($level == 0)
                {
                    $html[]= '<a href="' . $child->getUrl() . '">';
                } else
                {
                    $html[]= '<a href="' . $child->getUrl() . '" ' . $outermostClassCode . '>';
                }


                $html[]= $this->escapeHtml($child->getName());
                if($level == 0){
                    if(!empty($category->getData('ys_category_lable'))) {
                        if($category->getData('ys_label_color')) {
                            $l_color = ' style="background:'.$category->getData('ys_label_color').'"';
                        }
                        $html[]='<span class="TonyM__label"'.$l_color.'>'.$category->getData('ys_category_lable').'</span>';
                    }
                }


                $html[]= '</a>';

                if($level==0 && $hasChildren)$html[]='<div class="TonyM__mm TonyM__mm--simple"
														 data-tm-w="'.$category->getData('ys_nav_simple_width').'"
														 data-tm-a-v="'.$category->getData('ys_data_tm_align_vertical').'"
														 data-tm-a-h="'.$category->getData('ys_data_tm_align_horizontal').'">
																	<ul class="TonyM__list TonyM--gr'.$category->getData('ys_nav_simple_cols').'-in">';
                $html[]= $this->_addSubMenuSimple($child,$childLevel,$childrenWrapClass,$limit,1);
                if($level==0 && $hasChildren)$html[]='</ul></div>';

                $html[]= '</li>';
            }

            $itemPosition++;
            $counter++;
        }


        $html = implode("\n", $html);
        return $html;
    }

    protected function _addSubMenuMegamenu($child, $childLevel, $childrenWrapClass, $limit,$data=[])
    {
        $html = '';
        if (!$child->hasChildren()) {
            return $html;
        }
        $level=$childLevel;
        $hasChildren=$child->hasChildren();

        $colStops = null;
        if ($childLevel == 0 && $limit) {
            $colStops = $this->_columnBrake($child->getChildren(), $limit);
        }

        if($level==1) {
            $html .='<ul class="tonyMenu__inner-list-items">';
        }elseif($level>1){
            $html .= '<ul class="level' . $childLevel . ' submenu">';
        }
        $html .= $this->_getHtml($child, $childrenWrapClass, $limit, $colStops, 0);
        if($level>=1)$html .= '</ul>';



        return $html;
    }

    protected function _addSubMenuSimple($child, $childLevel, $childrenWrapClass, $limit, $type_simple=0)
    {
        $html = '';
        if (!$child->hasChildren()) {
            return $html;
        }
        $level=$childLevel;
        $hasChildren=$child->hasChildren();

        if($level>0){
            $html .= '<ul class="level' . $childLevel . '">';
        }
        $html .= $this->_getHtml($child, $childrenWrapClass, $limit, [], $type_simple);
        if($level>0)$html .= '</ul>';

        return $html;
    }

    public function getProductsFeatured($ids){
        $html = '';
        $ids = explode(',',$ids);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $abstractProductBlock = $objectManager->get('\Magento\Catalog\Block\Product\AbstractProduct');

        foreach($ids as $key)
        {
            $product = $objectManager->get('Magento\Catalog\Model\Product')->load($key);
            $link=$objectManager->create('\Magento\Catalog\Model\ProductRepository')->getById($key);
            $image=$objectManager->create('\Magento\Catalog\Block\Product\AbstractProduct')->getImage($product,'category_page_list');
            $html.='
            <div class="TonyM__featured_slide">
                <div class="TonyM__featured_pdt">
                    <div class="TonyM__featured_img">
                        <a href="'.$link->getUrlModel()->getUrl($link).'">
                            <img src="'.$image->getImageUrl().'" alt="">
                        </a>
                    </div>
                    <div class="TonyM__featured_ttl">
                        <h2><a href="'.$link->getUrlModel()->getUrl($link).'">'.$product->getName().'</a></h2>
                    </div>
                    <div class="TonyM__featured_price">
                        <span>'.$objectManager->create('\Magento\Framework\Pricing\PriceCurrencyInterface')->format($product->getPrice(),true,0).'</span>
                    </div>
                </div>
            </div>';
        }
        return $html;
    }

    public function getProductsSpecials($ids){
        $html = '';
        $ids = explode(',',$ids);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $abstractProductBlock = $objectManager->get('\Magento\Catalog\Block\Product\AbstractProduct');
        $priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data');

        foreach($ids as $key)
        {
            $product = $objectManager->get('Magento\Catalog\Model\Product')->load($key);
            $link=$objectManager->create('\Magento\Catalog\Model\ProductRepository')->getById($key);
            $image=$objectManager->create('\Magento\Catalog\Block\Product\AbstractProduct')->getImage($product,'category_page_list');
            $prices=$product->getPriceInfo();
            $html.='
            <div class="col-2">
                <div class="tt-c-product">
                    <div class="tt-c-image-box">
                        <a href="'.$link->getUrlModel()->getUrl($link).'"><span class="tt-img"><img src="'.$image->getImageUrl().'" alt=""></span></a>
                    </div>
                    <div class="tt-c-description">
                        <h2 class="tt-c-title"><a href="'.$link->getUrlModel()->getUrl($link).'">'.$product->getName().'</a></h2>
                        <div class="tt-c-price">
                            <div class="price">'.$formattedPrice = $priceHelper->currency($product->getPrice(), true, false).'</div>
                        </div>
                    </div>
                </div>
            </div>';
        }
        return $html;
    }
}