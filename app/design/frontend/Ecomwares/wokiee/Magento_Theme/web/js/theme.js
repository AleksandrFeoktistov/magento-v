/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* Theme JS */
require([
        'jquery',
        'bootstrapminify',
        'slick',
        'jquery-bridget',
        'imagesloaded',
        'isotop',
        'domReady!'
    ],
    function($) {
        'use strict';

        var $document = $(document),
            $window = $(window),
            $body = $('body'),
            $html = $('html'),
            $ttPageContent = $('#tt-pageContent'),
            $ttFooter = $('footer'),
            $ttHeader = $('header'),
            $ttLeftColumnAside = $ttPageContent.find('.leftColumn.aside'),
            $ttFilterOptionsTop = $ttPageContent.find('.top-toolbar'),
            $ttFilterOptions = $ttFilterOptionsTop.find('.tt-filters-options'),

        /* menu setting*/
            header_menu_timeout = 200,
            header_menu_delay = 200,

        //header
            $ttTopPanel = $('.tt-top-panel'),
        //header stuck
            $stucknav = $ttHeader.find('.tt-stuck-nav'),
        //header menu
            $ttDesctopMenu = $ttHeader.find('.tt-desctop-menu'),
            $ttDesctopParentMenu = $ttHeader.find('.tt-desctop-parent-menu'),
            $ttMobileParentMenu = $ttHeader.find('.tt-mobile-parent-menu'),
            $ttMobileParentMenuCst = $ttHeader.find('.tt-mobile-parent-menu-cst'),
            $ttMobileParentMenuChildren = $ttMobileParentMenu.children(),
            $ttStuckParentMenu = $ttHeader.find('.tt-stuck-parent-menu'),
        //header search
            $ttSearchObj = $ttHeader.find('.tt-search'),
            $ttDesctopParentSearch = $ttHeader.find('.tt-desctop-parent-search'),
            $ttMobileParentSearch = $ttHeader.find('.tt-mobile-parent-search'),
            $ttStuckParentSearch = $ttHeader.find('.tt-stuck-parent-search'),
            $ttSearchObjPopupInput = $ttSearchObj.find('.tt-search-input'),
            $ttSearchObjPopupResults = $ttSearchObj.find('.search-results'),
        //header cart
            $ttcartObj = $ttHeader.find('.tt-cart'),
            $ttDesctopParentCart = $ttHeader.find('.tt-desctop-parent-cart'),
            $ttMobileParentCart = $ttHeader.find('.tt-mobile-parent-cart'),
            $ttStuckParentCart = $ttHeader.find('.tt-stuck-parent-cart'),
        //header account
            $ttAccountObj = $ttHeader.find('.tt-account'),
            $ttDesctopParentAccount = $ttHeader.find('.tt-desctop-parent-account'),
            $ttMobileParentAccount = $ttHeader.find('.tt-mobile-parent-account'),
            $ttStuckParentAccount = $ttHeader.find('.tt-stuck-parent-account'),
        //header langue and currency(*all in one module)
            $ttMultiObj = $ttHeader.find('.tt-multi-obj'),
            $ttDesctopParentMulti = $ttHeader.find('.tt-desctop-parent-multi'),
            $ttMobileParentMulti = $ttHeader.find('.tt-mobile-parent-multi'),
            $ttStuckParentMulti = $ttHeader.find('.tt-stuck-parent-multi'),

            $ttFilterOnePage = $ttPageContent.find('.filters_side_block'),
            $ttLoginPopup = $ttPageContent.find('.authentication-dropdown'),

        // Template Blocks
            blocks = {
                ttCalendarDatepicker: $ttPageContent.find('.calendarDatepicker'),
                ttSliderBlog: $ttPageContent.find('.tt-slider-blog'),
                ttSlickMain: $ttPageContent.find('.tt-slick-main'),
                ttSliderBlogSingle: $ttPageContent.find('.tt-slider-blog-single'),
                ttVideoBlock: $('.tt-video-block'),
                ttBlogMasonry: $ttPageContent.find('.tt-blog-masonry'),
                ttPortfolioMasonry: $ttPageContent.find('.tt-portfolio-masonry'),
                ttProductMasonry: $ttPageContent.find('.tt-product-listing-masonry'),
                ttLookBookMasonry: $ttPageContent.find('.tt-lookbook-masonry'),
                ttInputCounter: $('.tt-input-counter'),
                ttCollapseBlock: $('.tt-collapse-block'),
                modalVideoProduct: $('#modalVideoProduct'),
                modalAddToCart: $('#modalAddToCartProduct'),
                ttMobileProductSlider: $('.tt-mobile-product-slider'),
                ttCollapse: $ttPageContent.find('.tt-collapse'),
                ttCollapseSide: $ttLeftColumnAside.find('.block'),
                ttProductListing: $ttPageContent.find('.tt-product-listing'),
                ttCountdown: $ttPageContent.find('.tt-countdown'),
                ttBtnColumnClose: $ttLeftColumnAside.find('.tt-btn-col-close'),
                ttFilterOnePageClose: $ttFilterOnePage.find('.tt-btn-col-close'),
                ttBtnToggle: $ttFilterOptions.find('.tt-btn-toggle a'),
                ttBtnAddProduct: $ttPageContent.find('.tt_product_showmore'),
                ttOptionsSwatch: $ttPageContent.find('.tt-options-swatch'),
                ttProductItem: $ttPageContent.find('.tt-product, .tt-product-design02'),
                ttProductDesign02: $ttPageContent.find('.tt-product-design02'),
                ttProductDesign01: $ttPageContent.find('.tt-product'),
                ttFilterDetachOption: $ttPageContent.find('.tt-filter-detach-option'),
                ttFilterSort: $ttFilterOptions.find('.tt-sort'),
                ttShopCart: $ttPageContent.find('.tt-shopcart-table, .tt-shopcart-table-02'),
                ttSliderLookbook: $ttPageContent.find('.tt-slider-lookbook'),
                ttCaruselLookbook: $ttPageContent.find('.tt-carousel-lookbook'),
                ttPortfolioContent: $ttPageContent.find('.tt-portfolio-content'),
                ttLookbook: $ttPageContent.find('.tt-lookbook'),
                ttAirSticky: $ttPageContent.find('.airSticky'),
                ttfooterMobileCollapse: $ttFooter.find('.tt-collapse-title'),
                ttBackToTop: $('.tt-back-to-top'),
                ttHeaderDropdown: $ttHeader.find('.tt-dropdown-obj'),
                mobileMenuToggle: $('.tt-menu-toggle'),
                ttCarouselProducts: $('.tt-carousel-products'),
                ttSliderFullwidth: $('.tt-slider-fullwidth'),
                ttCarouselBrands: $('.tt-carousel-brands'),
                sliderRevolution: $('.slider-revolution'),
                ttItemsCategories: $ttPageContent.find('.tt-items-categories'),
                ttDotsAbsolute: $ttPageContent.find('.tt-dots-absolute'),
                ttAlignmentImg: $ttPageContent.find('.tt-alignment-img'),
                ttModalQuickView: $('#ModalquickView'),
                ttProductSingleBtnZomm: $ttPageContent.find('.tt-product-single-img .tt-btn-zomm'),
                ttPromoFixed: $('.tt-promo-fixed'),
                ttInstafeedFluid: $ttPageContent.find('.instafeed-fluid'),
                ttInstafeedCol: $ttPageContent.find('.instafeed-col'),
                // main slider "Slick" - full height and full width (* index-slick-slider.html)
                mainSliderSlick: $('.mainSliderSlick'),
                // main slider "Slick" - full width and container (* index-slick-slider.html)
                ttSlickSlider: $ttPageContent.find('.tt-slick-slider'),
            };

        var ttwindowWidth = window.innerWidth || $window.width();



        collapseTooltip();

        initStuck();
        $(window).trigger('updateSkuck');

        // back to top init
        if (blocks.ttBackToTop.length) {
            ttBackToTop();
        };

        //tt-collapse-block(pages product single)
        if (blocks.ttCollapseBlock.length) {
            ttCollapseBlock();
        };
        if (blocks.ttfooterMobileCollapse.length) {
            ttFooterCollapse();
        };
        // header - tt-dropdown-obj
        if (blocks.ttHeaderDropdown.length) {
            ttHeaderDropdown();
        };
        if ($ttDesctopParentSearch.length) {
            //mobileParentMenu();
        };
        if ($ttDesctopParentSearch.length) {
            mobileParentSearch();
        };
        if ($ttcartObj.length) {
            mobileParentCart();
        };
        if ($ttDesctopParentAccount.length) {
            mobileParentAccount();
        };
        if ($ttDesctopParentMulti.length) {
            mobileParentMulti();
        };
        // Slide Column
        if ($ttLeftColumnAside && blocks.ttBtnColumnClose && blocks.ttBtnToggle) {
            ttToggleCol();
        };
        if ($ttFilterOnePage.length) {
            ttToggleOneCol();
        };

        if (blocks.ttInputCounter.length) {
            ttInputCounter();
        };

        if (blocks.ttProductDesign01.length) {
            ttProductHover();
        };

        if (blocks.ttAlignmentImg.length) {
            alignmentArrowValue();
        };

        // lookbook.html
        if (blocks.ttLookbook.length) {
            ttLookbook(ttwindowWidth);
        };
        if (blocks.ttLookBookMasonry.length) {
            gridLookbookMasonr();
        };
        if (blocks.ttProductMasonry.length) {
            gridProductMasonr();
        };

        // portfolio mobile click
        if (blocks.ttPortfolioContent.length && is_touch_device()) {
            ttPortfolioContentMobile();
        };

        if (blocks.ttPortfolioMasonry.length) {
            gridPortfolioMasonr();
            initPortfolioPopup();
        };

        alignPopup();

        // collapseBlock
        ttCollapse();
        ttCollapseFilter();

        function debouncer(func, timeout) {
            var timeoutID, timeout = timeout || 500;
            return function() {
                var scope = this,
                    args = arguments;
                clearTimeout(timeoutID);
                timeoutID = setTimeout(function() {
                    func.apply(scope, Array.prototype.slice.call(args));
                }, timeout);
            }
        };

        // identify touch device
        function is_touch_device() {
            return !!('ontouchstart' in window) || !!('onmsgesturechange' in window);
        };
        if (is_touch_device()) {
            $body.addClass('touch-device');
            $html.addClass('touch-device');
        };
        if (/Edge/.test(navigator.userAgent)) {
            $html.addClass('edge');
        };

        if (blocks.ttCarouselProducts.length) {
            blocks.ttCarouselProducts.each( function() {
                var slick = $(this),
                    item =  $(this).data('item');
                slick.slick({
                    dots: false,
                    arrows: true,
                    infinite: true,
                    speed: 300,
                    slidesToShow: item || 4,
                    slidesToScroll: item || 1,
                    adaptiveHeight: true,
                    responsive: [{
                        breakpoint: 1025,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 1
                        }
                    },
                        {
                            breakpoint: 791,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 1
                            }
                        }]
                });
            });
        };
        // slider fullwidth content(*index-07.html)
        if (blocks.ttSliderFullwidth.length) {
            blocks.ttSliderFullwidth.slick({
                dots: false,
                arrows: true,
                infinite: true,
                speed: 300,
                slidesToShow: 1,
                adaptiveHeight: true
            });
        };
        // carousel brandsh content(*index-07.html)
        if (blocks.ttCarouselBrands.length) {
            blocks.ttCarouselBrands.slick({
                dots: false,
                arrows: true,
                infinite: true,
                speed: 300,
                slidesToShow: 8,
                slidesToScroll: 1,
                adaptiveHeight: true,
                responsive: [
                    {
                        breakpoint: 1230,
                        settings: {
                            slidesToShow: 6,
                        }
                    },
                    {
                        breakpoint: 1025,
                        settings: {
                            slidesToShow: 4,
                        }
                    },
                    {
                        breakpoint: 790,
                        settings: {
                            slidesToShow: 3
                        }
                    },
                    {
                        breakpoint: 576,
                        settings: {
                            slidesToShow: 2
                        }
                    },
                    {
                        breakpoint: 380,
                        settings: {
                            slidesToShow: 1
                        }
                    }
                ]
            });
        };
        // carusel
        if (blocks.ttCaruselLookbook.length) {
            blocks.ttCaruselLookbook.slick({
                dots: true,
                arrows: true,
                infinite: true,
                speed: 300,
                slidesToShow: 3,
                slidesToScroll: 3,
                adaptiveHeight: true,
                responsive: [{
                    breakpoint: 1025,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2
                    }
                },
                    {
                        breakpoint: 790,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    }]
            });
        };

        //tabs init carusel
        $('a[data-toggle="tab"]').length && $('body').on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
            $('.slick-slider').each(function() {
                $(this).slick("getSlick").refresh();
            });
            if (blocks.ttAlignmentImg.length) {
                alignmentArrowValue();
            };
        });
        $('.modal').on('shown.bs.modal', function (e) {
            var objSlickSlider = $(this).find('.slick-slider');
            if(objSlickSlider.length){
                objSlickSlider.each(function() {
                    $(this).slick("getSlick").refresh();
                });
            };
        });

        // centering arrow
        function alignmentArrowValue(){
            var ttwindowWidth = window.innerWidth || $window.width();

            if(ttwindowWidth > 1024){
                setTimeout(function() {
                    blocks.ttAlignmentImg.each(function() {
                        $(this).find('.slick-arrow').removeAttr("style");
                    });
                }, 225);
            } else {
                setTimeout(function() {
                    blocks.ttAlignmentImg.each(function() {
                        var ttObj = $(this),
                            $objParentArrow = ttObj.find('.slick-arrow');
                        if(ttObj.find('.tt-image-box').length == 0 || $objParentArrow.length == 0) return;
                        var $obj = ttObj.find('.tt-image-box').first();
                        $objParentArrow.css({
                            'top' : $obj.findHeight() - $objParentArrow.findHeight() - parseInt(ttObj.css('marginTop'), 10) + 'px'
                        });

                        ttObj.find('.tt-product').length && ttProductSmall();
                    });
                }, 225);
            };

        };
        $.fn.findHeight = function (){
            var $blocks = $(this),
                maxH    = $blocks.eq(0).innerHeight();

            $blocks.each(function(){
                maxH = ( $(this).innerHeight() > maxH ) ? $(this).innerHeight() : maxH;
            });

            return maxH/2;
        };

        // tt-hotspot
        function ttLookbook(ttwindowWidth){
            //add lookbook popup
            var objPopup = $('.tt-lookbook-popup');
            if(!objPopup.length){
                $body.append('<div class="tt-lookbook-popup"><div class="tt-lookbook-container"></div></div>');
            };

            blocks.ttLookbook.on('click', '.tt-hotspot' , function(e) {
                var $this = $(this),
                    target = e.target,
                    ttHotspot = $('.tt-hotspot'),
                    ttwindowWidth = window.innerWidth || $window.width(),
                    ttCenterBtn = $('.tt-btn').innerHeight() / 2,
                    ttWidthPopup = $('.tt-hotspot-content').innerWidth();


                ttwindowWidth <= 789 ?  ttLookbookMobile($this) : ttLookbookDesktop($this);

                //ttLookbookDesktop
                function ttLookbookDesktop($this){

                    if ($this.hasClass('active')) return;

                    var objTop = $this.offset().top + ttCenterBtn,
                        objLeft = $this.offset().left,
                        objContent = $this.find('.tt-hotspot-content').detach();

                    //check if an open popup
                    var checkChildren = $('.tt-lookbook-container').children().size();
                    if(checkChildren > 0){
                        if(ttwindowWidth <= 789){
                            closePopupMobile();
                        } else {
                            closePopupDesctop();
                        };
                    }

                    //open popup
                    popupOpenDesktop(objContent, objTop, objLeft);

                };
                function popupOpenDesktop(objContent, objTop, objLeft){
                    //check out viewport(left or right)
                    var halfWidth =  ttwindowWidth / 2,
                        objLeftFinal = 0;

                    if(halfWidth < objLeft){
                        objLeftFinal = objLeft - ttWidthPopup - 7;
                        popupShowLeft(objLeftFinal);
                    } else{
                        objLeftFinal = objLeft + 45;
                        popupShowRight(objLeftFinal);
                    };

                    $('.tt-lookbook-popup').find('.tt-lookbook-container').append(objContent);
                    $this.addClass('active').siblings().removeClass('active');

                    function popupShowLeft(objLeftFinal){
                        $('.tt-lookbook-popup').css({
                            'top' : objTop,
                            'left' : objLeftFinal,
                            'display' : 'block'
                        }, 300).animate({
                            marginLeft: 26 + 'px',
                            opacity: 1
                        }, 300);
                    };
                    function popupShowRight(objLeftFinal){
                        $('.tt-lookbook-popup').css({
                            'top' : objTop,
                            'left' : objLeftFinal,
                            'display' : 'block'
                        }).animate({
                            marginLeft: -26 + 'px',
                            opacity: 1
                        });
                    };
                };
                //ttLookbookMobile
                function ttLookbookMobile($this){
                    var valueTop = $this.attr('data-top') + '%',
                        valueLeft = $this.attr('data-left') + '%';

                    $this.find('.tt-btn').css({
                        'top' : valueTop,
                        'left' : valueLeft
                    });
                    $this.css({
                        'top' : '0px',
                        'left' : '0px',
                        'width' : '100%',
                        'height' : '100%'
                    });
                    $this.addClass('active').siblings().removeClass('active');
                    $this.find('.tt-content-parent').fadeIn(200);
                };
                //Close mobile
                if(ttwindowWidth <= 789){
                    if ($('.tt-btn-close').is(e.target)){
                        closePopupMobile();
                        return false;
                    };
                    if ($('.tt-hotspot').is(e.target)){
                        closePopupMobile();
                    };
                    $(document).mouseup(function(e){
                        if (!$('.tt-lookbook-popup').is(e.target) && $('.tt-lookbook-popup').has(e.target).length === 0 && !$('.tt-hotspot').is(e.target) && $('.tt-hotspot').has(e.target).length === 0){
                            closePopupDesctop();
                        };
                    });
                };
                //Close desctope
                if(ttwindowWidth > 789){
                    //ttLookbookClose
                    $(document).mouseup(function(e){
                        var ttwindowWidth = window.innerWidth || $window.width();
                        if ($('.tt-btn-close').is(e.target)){
                            closePopupDesctop();
                            return false;
                        };
                        if (!$('.tt-lookbook-popup').is(e.target) && $('.tt-lookbook-popup').has(e.target).length === 0 && !$('.tt-hotspot').is(e.target) && $('.tt-hotspot').has(e.target).length === 0){
                            closePopupDesctop();
                        };
                    });
                };

                function closePopupDesctop(){
                    //detach content popup
                    var detachContentPopup = $('.tt-lookbook-popup').removeAttr("style").find('.tt-hotspot-content').detach();
                    $('.tt-hotspot.active').removeClass('active').find('.tt-content-parent').append(detachContentPopup);
                };
                function closePopupMobile(){
                    if($('.tt-lookbook-container').is(':has(div)')){
                        var checkPopupContent = $('.tt-lookbook-container').find('.tt-hotspot-content').detach();
                        $('.tt-hotspot.active').find('.tt-content-parent').append(checkPopupContent);
                    };
                    $('.tt-lookbook').find('.tt-hotspot.active').each(function(index) {
                        var $this = $(this),
                            valueTop = $this.attr('data-top') + '%',
                            valueLeft = $this.attr('data-left') + '%';

                        $this.removeClass('active').removeAttr("style").css({
                            'top' : valueTop,
                            'left' : valueLeft,
                        }).find('.tt-btn').removeAttr("style").next().removeAttr("style");
                    });
                };
                function checkclosePopupMobile(){
                    $('.tt-hotspot').find('.tt-content-parent').each(function() {
                        var $this = $(this);
                        if($this.css('display') == 'block'){
                            var $thisParent = $this.closest('.tt-hotspot'),
                                valueTop = $thisParent.attr('data-top') + '%',
                                valueLeft = $thisParent.attr('data-left') + '%';

                            $this.removeAttr("style").prev().removeAttr("style");
                            $thisParent.removeAttr("style").css({
                                'top' : valueTop,
                                'left' : valueLeft,
                            });
                        };
                    });
                };
                $(window).resize(debouncer(function(e) {
                    var ttwindowWidth = window.innerWidth || $window.width();
                    if(ttwindowWidth <= 789){
                        closePopupMobile();
                    } else {
                        closePopupDesctop();
                        checkclosePopupMobile();
                    };
                }));
            });
        };

        // Product Masonr (listing-metro.html)
        function gridProductMasonr() {
            // init Isotope
            var $grid = blocks.ttProductMasonry.find('.tt-product-init').isotope({
                itemSelector: '.element-item',
                layoutMode: 'masonry',
            });
            // layout Isotope after each image loads
            $grid.imagesLoaded().progress( function() {
                $grid.isotope('layout');
            });
            // filter functions
            var ttFilterNav =  blocks.ttProductMasonry.find('.tt-filter-nav');
            if (ttFilterNav.length) {
                var filterFns = {
                    ium: function() {
                        var name = $(this).find('.name').text();
                        return name.match(/ium$/);
                    }
                };
                // bind filter button click
                ttFilterNav.on('click', '.button', function() {
                    var filterValue = $(this).attr('data-filter');
                    filterValue = filterFns[filterValue] || filterValue;
                    $grid.isotope({
                        filter: filterValue
                    });
                    $(this).addClass('active').siblings().removeClass('active');
                });
            };
            //add item
            var isotopShowmoreJs = $('.isotop_showmore_js .btn'),
                ttAddItem = $('.tt-add-item');
            if (isotopShowmoreJs.length && ttAddItem.length) {
                isotopShowmoreJs.on('click', function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: 'ajax_product_metro.php',
                        success: function(data) {
                            var $item = $(data);
                            ttAddItem.append($item);
                            $grid.isotope('appended', $item);
                            ttProductSmall();
                            adjustOffset();
                        }
                    });
                    function adjustOffset(){
                        var offsetLastItem = ttAddItem.children().last().children().offset().top - 80;
                        $($body, $html).animate({
                            scrollTop: offsetLastItem
                        }, 500);
                    };
                    return false;
                });
            };
        };

        // Lookbook Masonr
        function gridLookbookMasonr() {
            // init Isotope
            var $grid = blocks.ttLookBookMasonry.find('.tt-lookbook-init').isotope({
                itemSelector: '.element-item',
                layoutMode: 'masonry',
                gutter: 0
            });
            // layout Isotope after each image loads
            $grid.imagesLoaded().progress( function() {
                $grid.addClass('tt-show').isotope('layout');
            });
            //add item
            var isotopShowmoreJs = $('.isotop_showmore_js .btn'),
                ttAddItem = $('.tt-add-item');
            if (isotopShowmoreJs.length && ttAddItem.length) {
                isotopShowmoreJs.on('click', function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: 'ajax_post.php',
                        success: function(data) {
                            var $item = $(data);
                            ttAddItem.append($item);
                            $grid.isotope('appended', $item);
                            adjustOffset();
                        }
                    });
                    function adjustOffset(){
                        var offsetLastItem = ttAddItem.children().last().children().offset().top - 180;
                        $($body, $html).animate({
                            scrollTop: offsetLastItem
                        }, 500);
                    };
                    return false;
                });
            };
        };

        /*Basic Features*/
        function getInternetExplorerVersion() {
            var rv = -1;
            if (navigator.appName === 'Microsoft Internet Explorer') {
                var ua = navigator.userAgent;
                var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
                if (re.exec(ua) != null)
                    rv = parseFloat(RegExp.$1);
            } else if (navigator.appName === 'Netscape') {
                var ua = navigator.userAgent;
                var re = new RegExp("Trident/.*rv:([0-9]{1,}[\.0-9]{0,})");
                if (re.exec(ua) != null)
                    rv = parseFloat(RegExp.$1);
            }
            return rv;
        };


        // button back to top
        function ttBackToTop() {
            blocks.ttBackToTop.on('click',  function(e) {
                $('html, body').animate({
                    scrollTop: 0
                }, 500);
                return false;
            });
            $window.scroll(function() {
                $window.scrollTop() > 500 ? blocks.ttBackToTop.stop(true.false).addClass('tt-show') : blocks.ttBackToTop.stop(true.false).removeClass('tt-show');
            });
        };

        function ttCollapseBlock() {
            blocks.ttCollapseBlock.each( function () {
                var obj = $(this),
                    objOpen = obj.find('.tt-item.active'),
                    objItemTitle = obj.find('.tt-item .tt-collapse-title');

                objOpen.find('.tt-collapse-content').slideToggle(200);

                objItemTitle.on('click', function () {
                    $(this).next().slideToggle(200).parent().toggleClass('active');
                });
            });
        };
        // Mobile footer collapse
        function ttFooterCollapse() {
            blocks.ttfooterMobileCollapse.on('click',  function(e) {
                e.preventDefault;
                $(this).toggleClass('tt-open');
            });
        };
        // Header dropdowns
        function ttHeaderDropdown(){
            var dropdownPopup = $('.header-popup-bg');
            if(!dropdownPopup.length){
                $body.append('<div class="header-popup-bg"></div>');
            };
            $('header').on('click', '.tt-dropdown-obj', function(e) {
                var ttwindowWidth = window.innerWidth || $window.width(),
                    $this = $(this),
                    target = e.target,
                    objSearch = $('.tt-search'),
                    objSearchInput = objSearch.find('.tt-search-input');

                // search
                if ($this.hasClass('tt-search') && $('.tt-dropdown-toggle').is(target)){
                    searchPopup();
                };
                function searchPopup(){
                    $this.addClass('active');
                    objSearchInput.focus();
                    return false;
                };
                if (objSearch.find('.tt-btn-close').is(target)){
                    objSearchClose();
                    return false;
                };
                function objSearchClose(){
                    $this.removeClass('active');
                    objSearchInput.blur();
                    return false;
                };

                // cart, account, multi-ob
                if (!$(this).hasClass('tt-search') && $('.tt-dropdown-toggle').is(target)){
                    ttwindowWidth <= 1024 ?  popupObjMobile($this) : popupObjDesctop($this);
                };
                function popupObjMobile(obj){
                    $('header').find('.tt-dropdown-obj.active').removeClass('active');
                    obj.toggleClass('active').find('.tt-dropdown-menu').removeAttr("style");
                    $body.toggleClass('tt-popup-dropdown');
                };
                function popupObjDesctop(obj){
                    var $this = obj,
                        target = e.target;

                    if ($this.hasClass('active')){
                        $this.toggleClass('active').find('.tt-dropdown-menu').slideToggle(200);
                        return;
                    };
                    $('.tt-desktop-header .tt-dropdown-obj').each( function () {
                        var $this = $(this);
                        if($this.hasClass('active')){
                            $this.removeClass('active').find('.tt-dropdown-menu').css("display", "none");
                        }
                    });
                    if ($('.tt-dropdown-toggle').is(target)){
                        toggleDropdown($this);
                    };
                };
                function toggleDropdown(obj){
                    obj.toggleClass('active').find('.tt-dropdown-menu').slideToggle(200);
                };

                $(document).mouseup(function(e){
                    var ttwindowWidth = window.innerWidth || $window.width();

                    if (!$this.is(e.target) && $this.has(e.target).length === 0){
                        $this.each(function(){
                            if($this.hasClass('active') && $this.hasClass('tt-search')){
                                objSearch.find('.tt-btn-close').trigger('click');
                            };
                            if($this.hasClass('active') && !$this.hasClass('tt-search')){
                                if(ttwindowWidth <= 1024){
                                    closeObjPopupMobile();
                                } else {
                                    $('.tt-dropdown-obj').each( function () {
                                        if($(this).hasClass('active')){
                                            $(this).removeClass('active').find('.tt-dropdown-menu').css("display", "none");
                                        }
                                    });
                                };
                            };
                        });
                    };
                    if ($this.find('.tt-mobile-add .tt-close').is(e.target)){
                        closeObjPopupMobile();
                    };
                });
                function closeObjPopupMobile(){
                    $('.tt-dropdown-obj.active').removeClass('active');
                    $body.removeClass('tt-popup-dropdown');
                    return false;
                };
            });
        };

        //header menu
        function mobileParentMenu() {
            if (window.innerWidth < 1025) {
                if ($ttMobileParentMenuCst.children().lenght) return false;
                if ($('.stuck').length) return false;
                //$ttMobileParentMenuCst.append($ttDesctopMenu.detach());
            } else {
                if ($ttDesctopParentMenu.children().lenght) return false;
                if ($('.stuck').length) return false;
                //$ttDesctopParentMenu.append($ttDesctopMenu.detach());
            };
        };
        //header search
        function mobileParentSearch() {
            if (window.innerWidth < 1025) {
                if ($ttMobileParentSearch.children().lenght) return false;
                if ($('.stuck').length) return false;
                $ttMobileParentSearch.append($ttSearchObj.detach());
            } else {
                if ($ttDesctopParentSearch.children().lenght) return false;
                if ($('.stuck').length) return false;
                $ttDesctopParentSearch.append($ttSearchObj.detach());
            };
        };
        //header cart
        function mobileParentCart() {
            if (window.innerWidth < 1025) {
                if ($ttMobileParentCart.children().lenght) return false;
                if ($('.stuck').length) return false;
                $ttMobileParentCart.append($ttcartObj.detach());
            } else {
                if ($ttDesctopParentCart.children().lenght) return false;
                if ($('.stuck').length) return false;
                $ttDesctopParentCart.append($ttcartObj.detach());
            };
        };
        //header account
        function mobileParentAccount() {
            if (window.innerWidth < 1025) {
                if ($ttMobileParentAccount.children().lenght) return false;
                if ($('.stuck').length) return false;
                $ttMobileParentAccount.append($ttAccountObj.detach());
            } else {
                if ($ttDesctopParentAccount.children().lenght) return false;
                if ($('.stuck').length) return false;
                $ttDesctopParentAccount.append($ttAccountObj.detach());
            };
        };
        //header langue and currency(*all in one module)
        function mobileParentMulti() {
            if (window.innerWidth < 1025) {
                if ($ttMobileParentMulti.children().lenght) return false;
                if ($('.stuck').length) return false;
                $ttMobileParentMulti.append($ttMultiObj.detach());
            } else {
                if ($ttDesctopParentMulti.children().lenght) return false;
                if ($('.stuck').length) return false;
                $ttDesctopParentMulti.append($ttMultiObj.detach());
            };
        };

        function initStuck(value) {
            if($stucknav.hasClass('disabled')) return;

            var value = value || false,
                ie = (getInternetExplorerVersion() !== -1) ? true : false;

            if (value === 'off') return false;
            var n = 0;
            $window.on('scroll updateSkuck', function() {
                var HeaderTop = $('header').innerHeight();
                if ($window.scrollTop() > HeaderTop) {
                    if ($stucknav.hasClass('stuck')) return false;
                    $stucknav.hide();
                    $stucknav.addClass('stuck');
                    window.innerWidth < 1025 ? $ttStuckParentMenu.append($ttMobileParentMenuChildren.detach()) : $ttStuckParentMenu.append($ttDesctopMenu.detach());
                    $ttStuckParentCart.append($ttcartObj.detach());
                    $ttStuckParentMulti.append($ttMultiObj.detach());
                    $ttStuckParentAccount.append($ttAccountObj.detach());
                    $ttStuckParentSearch.append($ttSearchObj.detach());


                    if ($stucknav.find('.tt-stuck-cart-parent > .tt-cart > .dropdown').hasClass('open') || ie)
                        $stucknav.stop().show();
                    else
                        $stucknav.stop().fadeIn(300);

                } else {
                    if (!$stucknav.hasClass('stuck')) return false;
                    $stucknav.hide();
                    $stucknav.removeClass('stuck');
                    if (window.innerWidth < 1025) {
                        $ttMobileParentMenu.append($ttMobileParentMenuChildren.detach());
                        $ttMobileParentCart.append($ttcartObj.detach());
                        $ttMobileParentMulti.append($ttMultiObj.detach());
                        $ttMobileParentAccount.append($ttAccountObj.detach());
                        $ttMobileParentSearch.append($ttSearchObj.detach());
                        //$ttMobileParentMenuCst.append($ttDesctopMenu.detach());
                        return false;
                    }
                    $ttDesctopParentMenu.append($ttDesctopMenu.detach());
                    $ttDesctopParentCart.append($ttcartObj.detach());
                    $ttDesctopParentMulti.append($ttMultiObj.detach());
                    $ttDesctopParentAccount.append($ttAccountObj.detach());
                    $ttDesctopParentSearch.append($ttSearchObj.detach());
                }
            });
            $window.on('resize updateSkuck', function() {
                if (!$stucknav.hasClass('stuck')) return false;
                if (window.innerWidth < 1025) {
                    $ttDesctopParentMenu.append($ttDesctopMenu.detach());
                    $ttStuckParentMenu.append($ttMobileParentMenuChildren.detach());
                } else {
                    $ttMobileParentMenu.append($ttMobileParentMenuChildren.detach());
                    $ttStuckParentMenu.append($ttDesctopMenu.detach());
                }
            });
        };

        function collapseTooltip() {
            $('.link.tooltip').on('click', function(e) {
                var speed = 300;
                var thisItem = $(this).parent(),
                    nextLevel = $(this).next('.tooltip.content');
                if (thisItem.hasClass('open')){
                    thisItem.removeClass('open');
                    nextLevel.slideUp(speed);
                }
                else {
                    thisItem.addClass('open');
                    nextLevel.slideDown(speed);
                }
                e.preventDefault();
                return false;
            })
        };

        function ttCollapse() {
            var item = $ttLeftColumnAside.find('.block'),
                itemContent = item.find('.block-content');

            if(item.length) {
                item.each(function() {
                    if ($(this).hasClass('collapsible')) {
                        if ($(this).hasClass('open')) {
                            $(this).find(itemContent).slideDown();
                        } else {
                            $(this).find(itemContent).slideUp();
                        }
                    }
                });
                $(document).on('click', '.block-title', function(e){
                    e.preventDefault();
                    var speed = 300;
                    var thisParent = $(this).parent(),
                        nextLevel = $(this).next('.block-content');
                    if (thisParent.hasClass('collapsible')) {
                        if (thisParent.hasClass('open')) {
                            thisParent.removeClass('open');
                            nextLevel.slideUp(speed);
                        } else {
                            thisParent.addClass('open');
                            nextLevel.slideDown(speed);
                        }
                    }
                })
            }
        };

        function ttCollapseFilter() {
            var item = $ttPageContent.find('.filter-options'),
                itemContent = item.find('.filter-options-content');

            if(item.length) {
                item.each(function() {
                    if ($(this).hasClass('collapsible')) {
                        if ($(this).hasClass('open')) {
                            $(this).find(itemContent).slideDown();
                        } else {
                            $(this).find(itemContent).slideUp();
                        }
                    }
                });
                $(document).on('click', '.filter-options-title', function(e){
                    e.preventDefault();
                    var speed = 300;
                    var thisParent = $(this).parent(),
                        nextLevel = $(this).next('.filter-options-content');
                    if (thisParent.hasClass('collapsible')) {
                        if (thisParent.hasClass('open')) {
                            thisParent.removeClass('open');
                            nextLevel.slideUp(speed);
                        } else {
                            thisParent.addClass('open');
                            nextLevel.slideDown(speed);
                        }
                    }
                })
            }
        };

        // portfolio mobile click
        function ttPortfolioContentMobile(){
            blocks.ttPortfolioContent.on('click', 'figure img', function() {
                $(this).closest(".tt-portfolio-content").find('figure').removeClass('gallery-click');
                $(this).closest("figure").addClass('gallery-click');
            });
        };

        //toggle col (listing-left-column.html)
        function ttToggleCol() {
            var $btnClose = $ttLeftColumnAside.find('.tt-btn-col-close a');
            $(document).on('click', '.tt-btn-toggle', function(e){
                e.preventDefault();
                var ttScrollValue = $body.scrollTop() || $html.scrollTop();
                $ttLeftColumnAside.toggleClass('column-open').perfectScrollbar();
                $body.css("top", - ttScrollValue).addClass("no-scroll").append('<div class="modal-filter"></div>');
                var modalFilter = $('.modal-filter').fadeTo('fast',1);
                if (modalFilter.length) {
                    modalFilter.on('click', function(){
                        $btnClose.trigger('click');
                    })
                }
                return false;
            });
            blocks.ttBtnColumnClose.on('click', function(e) {
                e.preventDefault();
                $ttLeftColumnAside.removeClass('column-open').perfectScrollbar('destroy');
                var top = parseInt($body.css("top").replace("px", ""), 10) * -1;
                $body.removeAttr("style").removeClass("no-scroll").scrollTop(top);
                $html.removeAttr("style").scrollTop(top);
                $(".modal-filter").off().remove();
            });
        };

        function ttToggleOneCol() {
            var $btnClose = $ttFilterOnePage.find('.tt-btn-col-close a');

            $(document).on('click', '.tt-btn-toggle', function(e){
                e.preventDefault();
                var ttScrollValue = $body.scrollTop() || $html.scrollTop();
                $ttFilterOnePage.toggleClass('column-open').perfectScrollbar();
                $body.css("top", - ttScrollValue).addClass("no-scroll").append('<div class="modal-filter"></div>');
                var modalFilter = $('.modal-filter').fadeTo('fast',1);
                if (modalFilter.length) {
                    modalFilter.on('click', function(){
                        $btnClose.trigger('click');
                    })
                }
                return false;
            });
            blocks.ttFilterOnePageClose.on('click', function(e) {
                e.preventDefault();
                $ttFilterOnePage.removeClass('column-open').perfectScrollbar('destroy');
                var top = parseInt($body.css("top").replace("px", ""), 10) * -1;
                $body.removeAttr("style").removeClass("no-scroll").scrollTop(top);
                $html.removeAttr("style").scrollTop(top);
                $(".modal-filter").off().remove();
            });
        };

        // product Small
        function ttProductSmall(){
            var currentW = parseInt(blocks.ttProductItem.width(), 10),
                objProduct = $(".tt-product, .tt-product-design02");
            currentW <= 210 ? objProduct.addClass("tt-small") : objProduct.removeClass("tt-small");
        };

        // ttFiltersOptions
        (function($) {
            $.fn.removeClassFirstPart = function(mask) {
                return this.removeClass(function(index, cls) {
                    var re = mask.replace(/\*/g, '\\S+');
                    return (cls.match(new RegExp('\\b' + re + '', 'g')) || []).join(' ');
                });
            };
        })(jQuery);

        function ttFilterLayout(ttwindowWidth){

            // detach filter aside left
            if($ttFilterOptions.hasClass('desctop-no-sidebar') && !$ttFilterOptions.hasClass('filters-detach-mobile')){
                ttwindowWidth <= 1024 ?  insertMobileCol() : insertFilter();
            };
            if($ttFilterOptions.hasClass('filters-detach-mobile')){
                ttwindowWidth <= 1024 ?  insertMobileCol() : insertFilter();
            };
            if(!$ttFilterOptions.hasClass('desctop-no-sidebar')){
                ttwindowWidth <= 1024 ?  insertMobileCol() : insertFilter();
            };

            function insertMobileCol(){
                var objFilterOptions = blocks.ttFilterSort.find('.tool-item').detach();
                blocks.ttFilterDetachOption.find('.filters-row-select').append(objFilterOptions);
            };
            function insertFilter(){
                var objColFilterOptions = blocks.ttFilterDetachOption.find('.filters-row-select .tool-item').detach();
                blocks.ttFilterSort.append(objColFilterOptions);
            };

            //active filter detection
            blocks.ttProductListing.removeClassFirstPart("tt-col-*");

            var ttQuantity = $ttFilterOptions.find('.tt-quantity'),
                ttProductItem = blocks.ttProductListing.find('.tt-col-item:first'),
                ttProductItemValue =  (function(){
                    if(ttQuantity.length && !ttQuantity.hasClass('tt-disabled')){
                        var ttValue = parseInt(ttProductItem.css("flex").replace("0 0", "").replace("%", ""), 10) || parseInt(ttProductItem.css("max-width"), 10);
                        return ttValue;
                    };
                }()),
                ttGridSwitch = $ttFilterOptions.find('.tt-grid-switch');


            if(ttProductItemValue == 16){
                ttСhangeclass(ttQuantity, '.tt-col-six');
            } else if(ttProductItemValue == 25){
                ttСhangeclass(ttQuantity, '.tt-col-four');
            } else if(ttProductItemValue == 33){
                ttСhangeclass(ttQuantity, '.tt-col-three');
            } else if(ttProductItemValue == 50){
                ttСhangeclass(ttQuantity, '.tt-col-two');
            } else if(ttProductItemValue == 100){
                ttСhangeclass(ttQuantity, '.tt-col-one');
            };

            function ttСhangeclass(ttObj, ttObjvalue){
                ttObj.find(ttObjvalue).addClass('active').siblings().removeClass('active');
                ttwindowWidth <= 1024 ?  ttShowIconMobile(ttObj, ttObjvalue) : ttShowIconDesctop(ttObj, ttObjvalue);
            };

            function ttShowIconDesctop(ttObj, ttObjvalue){

                ttObj.find('.tt-show').removeClass('tt-show');
                ttObj.find('.tt-show-siblings').removeClass('tt-show-siblings');

                var $this = ttObj.find(ttObjvalue);
                $this.addClass('tt-show');

                $this.next().addClass('tt-show-siblings');
                $this.prev().addClass('tt-show-siblings');
                var quantitySiblings = $('.tt-quantity .tt-show-siblings').length;
                if(quantitySiblings === 1){
                    ttObj.find('.tt-show-siblings').prev().addClass('tt-show-siblings');
                };
            };
            function ttShowIconMobile(ttObj, ttObjvalue){
                ttObj.find('.tt-show').removeClass('tt-show');
                ttObj.find('.tt-show-siblings').removeClass('tt-show-siblings');

                var $this = ttObj.find(ttObjvalue);
                $this.addClass('tt-show');
                $this.prev().addClass('tt-show-siblings');
            };

            //click buttons filter
            ttQuantity.on('click', 'a', function(e) {
                e.preventDefault();
                if(ttQuantity.hasClass('tt-disabled')){
                    blocks.ttProductListing.removeClass('tt-row-view').find('.tt-col-item > div').removeClass('tt-view');
                    ttQuantity.removeClass('tt-disabled');
                    ttGridSwitch.removeClass('active');
                    ttOverflowProduct();
                };

                ttQuantity.find('a').removeClass('active');
                var ttActiveValue = $(this).addClass('active').attr('data-value');
                blocks.ttProductListing.removeClassFirstPart("tt-col-*").addClass(ttActiveValue);
                ttProductSmall();
            });
        };

        function ttOverflowProduct(){
            blocks.ttProductDesign02.on('hover',function(){
                if (window.innerWidth < 1024) return;
                var objImgHeight = $(this).find('.tt-image-box').height(),
                    objScroll = $(this).find('.tt-description'),
                    objScrollHeight = objScroll.height() + 25;

                if (objImgHeight > objScrollHeight) return;
                $(this).addClass('tt-small');
                objScroll.height(objImgHeight).perfectScrollbar();

            }, function() {
                if (window.innerWidth < 1024) return;
                $(this).removeClass('tt-small').find('.tt-description').removeAttr('style').perfectScrollbar('destroy');
            });
        };

        $ttFilterOptions.find('.tt-grid-switch').on('click', function(e){
            e.preventDefault();
            $(this).toggleClass('active');
            blocks.ttProductListing.toggleClass('tt-row-view').find('.tt-col-item > div').toggleClass('tt-view');
            $ttFilterOptions.find('.tt-quantity').toggleClass('tt-disabled');
        });

        // product item Design01 hover (*desctope)
        function ttProductHover() {
            $document.on('mouseenter mouseleave click', '#tt-pageContent .tt-product:not(.tt-view)', function(e) {
                var $this = $(this),
                    windW = window.innerWidth,
                    objLiftUp01 = $this.find('.tt-description'),
                    objLiftUp02 = $this.find('.tt-product-inside-hover'),
                    objHeight02 = objLiftUp02.height(),
                    objCountdown = $this.find('.tt-countdown_box'),
                    target = e.target;

                if (e.type === 'mouseenter' && windW > 1024) {
                    ttOnHover();
                } else if (e.type === 'mouseleave' && e.relatedTarget && windW > 1024) {
                    ttOffHover();
                };

                function ttOnHover(e){
                    $this.stop().css({
                        height: $this.innerHeight()
                    }).addClass('hovered');
                    objLiftUp01.stop().animate({'top': '-' + objHeight02}, 200);
                    objLiftUp02.stop().animate({ 'opacity': 1 }, 400);
                    objCountdown.stop().animate({'bottom': objHeight02}, 200);
                    return false;
                };
                function ttOffHover(e){
                    $this.stop().removeClass('hovered').removeAttr('style');
                    objLiftUp01.stop().animate({'top': '0'}, 200, function(){$(this).removeAttr('style')});
                    objLiftUp02.stop().animate({ 'opacity': 0 }, 100, function(){$(this).removeAttr('style')});
                    objCountdown.stop().animate({'bottom': 0}, 200, function(){$(this).removeAttr('style')});
                    return false
                };
            });
        };

        // Portfolio
        function gridPortfolioMasonr() {
            // init Isotope
            var $grid = blocks.ttPortfolioMasonry.find('.tt-portfolio-content').isotope({
                itemSelector: '.element-item',
                layoutMode: 'masonry',
            });
            // layout Isotope after each image loads
            $grid.imagesLoaded().progress( function() {
                $grid.isotope('layout').addClass('tt-show');
            });
            // filter functions
            var ttFilterNav =  blocks.ttPortfolioMasonry.find('.tt-filter-nav');
            if (ttFilterNav.length) {
                var filterFns = {
                    ium: function() {
                        var name = $(this).find('.name').text();
                        return name.match(/ium$/);
                    }
                };
                // bind filter button click
                ttFilterNav.on('click', '.button', function() {
                    var filterValue = $(this).attr('data-filter');
                    filterValue = filterFns[filterValue] || filterValue;
                    $grid.isotope({
                        filter: filterValue
                    });
                    $(this).addClass('active').siblings().removeClass('active');
                });
            };
            //add item
            var isotopShowmoreJs = $('.isotop_showmore_js .btn'),
                ttAddItem = $('.tt-add-item');
            if (isotopShowmoreJs.length && ttAddItem.length) {
                isotopShowmoreJs.on('click', function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: 'ajax_portfolio.php',
                        success: function(data) {
                            var $item = $(data);
                            ttAddItem.append($item);
                            $grid.isotope('appended', $item);
                            initPortfolioPopup();
                            adjustOffset();
                        }
                    });
                    function adjustOffset(){
                        var offsetLastItem = ttAddItem.children().last().children().offset().top - 180;
                        $($body, $html).animate({
                            scrollTop: offsetLastItem
                        }, 500);
                    };
                    return false;
                });
            };
        };
        function initPortfolioPopup() {
            var objZoom = $ttPageContent.find('.tt-portfolio-masonry .tt-btn-zomm');
            objZoom.magnificPopup({
                type: 'image',
                gallery: {
                    enabled: true
                }
            });
        };

        //input-counter
        function ttInputCounter() {
            blocks.ttInputCounter.find('.minus-btn, .plus-btn').on('click',function(e) {
                var $input = $(this).parent().find('input');
                var count = parseInt($input.val(), 10) + parseInt(e.currentTarget.className === 'plus-btn' ? 1 : -1, 10);
                $input.val(count).change();
            });
            blocks.ttInputCounter.find("input").change(function() {
                var _ = $(this);
                var min = 1;
                var val = parseInt(_.val(), 10);
                var max = parseInt(_.attr('size'), 10);
                val = Math.min(val, max);
                val = Math.max(val, min);
                _.val(val);
            })
                .on("keypress", function( e ) {
                    if (e.keyCode === 13) {
                        e.preventDefault();
                    }
                });
        };

        //Align Popup
        function alignPopup() {

            $(document).on('click', '.action-auth-toggle', function(e){
                var pop_height = jQuery('.authentication-dropdown').outerHeight(),
                    window_height = jQuery(window).height();
                jQuery('.authentication-dropdown').css("top", Math.max(0, ((window_height - pop_height) / 2)) + "px");
            });

        };

        var ttwindowWidth = window.innerWidth || $window.width();

        // filters options product(definition layout)
        if ($ttFilterOptions.length) {
            ttFilterLayout(ttwindowWidth);
        };
        if (blocks.ttProductItem.length) {
            ttProductSmall(ttwindowWidth);
        };
        if (blocks.ttProductDesign02.length) {
            ttOverflowProduct();
        };

        var ttCachedWidth = $window.width();
        $window.on('resize', function () {
            var newWidth = $window.width();
            if(newWidth !== ttCachedWidth){
                ttCachedWidth = newWidth;

                var ttwindowWidth = window.innerWidth || $window.width();

                // filters options product(definition layout)
                if ($ttFilterOptions.length) {
                    ttFilterLayout(ttwindowWidth);
                };
                if (blocks.ttProductItem.length) {
                    ttProductSmall();
                };
                if (blocks.ttProductDesign02.length) {
                    ttOverflowProduct();
                };
                // portfolio mobile click
                if (blocks.ttPortfolioContent.length && is_touch_device()) {
                    ttPortfolioContentMobile();
                };
                //header init stuck and detach
                if ($ttDesctopParentSearch.length) {
                    mobileParentMenu();
                };
                if ($ttDesctopParentSearch.length) {
                    mobileParentSearch();
                };
                if ($ttcartObj.length) {
                    mobileParentCart();
                };
                if ($ttDesctopParentAccount.length) {
                    mobileParentAccount();
                };
                if ($ttDesctopParentMulti.length) {
                    mobileParentMulti();
                };
                if (blocks.ttAlignmentImg.length) {
                    alignmentArrowValue();
                };

                alignPopup();

            }
        });

    }
);

require([
        'jquery',
        'countdown_pl',
        'countdown_cd'
    ],
    function($) {
        if ($(".tt-countdown").length) {

            var showZero = showZero || true;

            $(".tt-countdown").each(function() {
                var $this = $(this),
                    date = $this.data('date'),
                    set_year = $this.data('year') || 'Yrs',
                    set_month = $this.data('month') || 'Mths',
                    set_week = $this.data('week') || 'Wk',
                    set_day = $this.data('day') || 'Day',
                    set_hour = $this.data('hour') || 'Hrs',
                    set_minute = $this.data('minute') || 'Min',
                    set_second = $this.data('second') || 'Sec';

                if (date = date.split('-')) {
                    date = date.join('/');
                } else return;

                $this.countdown(date , function(e) {
                    var format = '<span class="countdown-row">';

                    function addFormat(func, timeNum, showZero) {
                        if(timeNum === 0 && !showZero) return;

                        func(format);
                    };

                    addFormat(function() {
                        format += '<span class="countdown-section">'
                            + '<span class="countdown-amount">' + e.offset.totalDays + '</span>'
                            + '<span class="countdown-period">' + set_day + '</span>'
                            + '</span>';
                    }, e.offset.totalDays, showZero);

                    addFormat(function() {
                        format += '<span class="countdown-section">'
                            + '<span class="countdown-amount">' + e.offset.hours + '</span>'
                            + '<span class="countdown-period">' + set_hour + '</span>'
                            + '</span>';
                    }, e.offset.hours, showZero);

                    addFormat(function() {
                        format += '<span class="countdown-section">'
                            + '<span class="countdown-amount">' + e.offset.minutes + '</span>'
                            + '<span class="countdown-period">' + set_minute + '</span>'
                            + '</span>';
                    }, e.offset.minutes, showZero);

                    addFormat(function() {
                        format += '<span class="countdown-section">'
                            + '<span class="countdown-amount">' + e.offset.seconds + '</span>'
                            + '<span class="countdown-period">' + set_second + '</span>'
                            + '</span>';
                    }, e.offset.seconds, showZero);

                    format += '</span>';

                    $(this).html(format);
                });
            });
        }
    });

require([
    'jquery',
    'domReady!'
], function ($) {
    'use strict';

    var $body = $('body');

    if ($body.length) {
        $body.addClass('loaded');
    };

});


require([
    'jquery',
    'slick',
    'imagesloaded'
], function ($) {
    'use strict';

    var $ttPageContent = $('#tt-pageContent'),

    // Template Blocks
        blocks = {
            // main slider "Slick" - full height and full width (* index-slick-slider.html)
            mainSliderSlick: $('.mainSliderSlick'),
            // main slider "Slick" - full width and container (* index-slick-slider.html)
            ttSlickSlider: $ttPageContent.find('.tt-slick-slider'),
        };


    if (blocks.mainSliderSlick.length) {
        mainSliderSlick();
        dataBg('[data-bg]');
    };

    function mainSliderSlick() {
        var $el = blocks.mainSliderSlick;
        /*$el.find('.slide').first().imagesLoaded({
            background: true
        }, function(){
            setTimeout(function () {
                $el.parent().find('.loading-content').addClass('disable');
            }, 1200);
        });*/
        setTimeout(function () {
            $el.parent().find('.loading-content').addClass('disable');
        }, 4000);
        $el.on('init', function (e, slick) {
            var $firstAnimatingElements = $('div.slide:first-child').find('[data-animation]');
            doAnimations($firstAnimatingElements);
        });
        $el.on('beforeChange', function (e, slick, currentSlide, nextSlide) {
            var $currentSlide = $('div.slide[data-slick-index="' + nextSlide + '"]');
            var $animatingElements = $currentSlide.find('[data-animation]');
            doAnimations($animatingElements);
        });
        $el.slick({
            arrows: false,
            dots: true,
            autoplay: true,
            autoplaySpeed: 10000,
            fade: true,
            speed: 1000,
            pauseOnHover: false,
            pauseOnDotsHover: true,
            responsive: [{
                breakpoint: 1025,
                settings: {
                    dots: false,
                    arrows: true
                }
            }]
        });
    };
    function doAnimations(elements) {
        var animationEndEvents = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
        elements.each(function () {
            var $this = $(this);
            var $animationDelay = $this.data('animation-delay');
            var $animationType = 'animated ' + $this.data('animation');
            $this.css({
                'animation-delay': $animationDelay,
                '-webkit-animation-delay': $animationDelay
            });
            $this.addClass($animationType).one(animationEndEvents, function () {
                $this.removeClass($animationType);
            });
            if ($this.hasClass('animate')) {
                $this.removeClass('animation');
            }
        });
    };
    function dataBg(el) {
        $(el).each(function () {
            var $this = $(this),
                bg = $this.attr('data-bg');
            $this.css({
                'background-image': 'url(' + bg + ')'
            });
        });
    };
    // main slider "Slick" - full width and container (* index-slick-slider.html)
    if (blocks.ttSlickSlider.length) {
        blocks.ttSlickSlider.slick({
            dots: true,
            arrows: true,
            infinite: true,
            speed: 300,
            slidesToShow: 1,
            adaptiveHeight: true,
            responsive: [{
                breakpoint: 1025,
                settings: {
                    dots: false,
                }
            }]
        });
    };

});