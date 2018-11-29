/*TONYMENU PLUGIN JS*/

(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as anonymous module.
        define(['jquery', 'jquery/ui', 'plugins/velocity', 'plugins/scrollbar', 'plugins/slick'], factory);
    } else {
        // Browser globals.
        factory(jQuery);
    }
}(function ($) {
    'use strict';

    var TonyM = {
        options: {
            dir: 'row',

            pos: 'left',

            mob: false,

            bp: 1024,

            anm: 'fade',

            btn: false,

            btnActCl: 'active',

            dd_dur: 200,

            list_dur: 400
        },
        _crnt: {},
        _toggleList: null,
        _create: function() {
            var _self = this,
                $tm = this.bindings,
                $tm_panel = $tm.find('.TonyM__panel'),
                $tm_mm = $tm.find('.TonyM__mm'),
                $tm_dd = $tm_mm.find('.TonyM__dd'),
                $tm_list = $tm_mm.find('.TonyM__list'),
                opt = this.options,
                $tm_lvl,
                $tm_trns;

            $tm.addClass('TonyM--proces-init-js');

            this._setOption('dir', $tm.data('tm-dir'), true);
            this._setOption('pos', $tm.data('tm-pos'), true);
            this._setOption('mob', $tm.data('tm-mob'), true);
            this._setOption('anm', $tm.data('tm-anm'), true);
            this._setOption('cont', $tm.data('tm-cont'), true);

            //set parent
            this.parent = $tm.parent();

            //set menu position
            if(opt.dir === 'column') $tm.data('tm-pos', opt.pos);

            //set animations classes
            $tm_mm.addClass(this._animations[opt.anm]);

            //set align classes
            $.each(this._alignClasses, function(key, value) {
                var data_a;

                if(key === 'alignHorizontal') data_a = 'a-h';
                else if(key === 'alignVertical') data_a = 'a-v';

                $.each(value, function(k, v) {
                    $.each(v, function(a_name) {
                        var btn_cl = this.btn,
                            mm_cl = this.mm,
                            $this_mm;

                        $this_mm = $tm.filter('[data-tm-dir="' + k + '"]').find('.TonyM__mm[data-tm-' + data_a + '="' + a_name + '"]');

                        $this_mm.addClass(mm_cl);
                        $this_mm.parents('li').addClass(btn_cl);
                    });
                });
            });

            //set next level class
            $tm_lvl = $tm_mm.add($tm_panel.find('li > ul')).parent().addClass('TonyM--lvl');
            //set level arrow
            $tm_lvl.each(function() {
                var $this = $(this),
                    $a = $this.find('> a'),
                    $span = $a.find('> span'),
                    $parent;

                /*if(_self.options.dir === 'row' && $this.parent().hasClass('TonyM__panel') && $span.length) {
                    $parent = $span;
                } else {
                    $parent = $a;
                }*/

                $parent = $a;

                if(!$parent.find('.TonyM__arw').length) {
                    var $arrow = $('<i>').addClass('TonyM__arw');

                    $parent.append($arrow);
                }
            });

            //set transition block
            $tm_trns = $('<div>').addClass('TonyM__trns').appendTo($tm_mm.parent().find('> a'));
            $tm_trns.on('click', function(e) {
                e.preventDefault();
                return false;
            });

            //set list spacer
            $tm_list.each(function() {
                var part_w = 0;
                $(this).find('> li.TonyM__ttl').each(function() {
                    var $ttl = $(this);

                    part_w += parseInt($ttl.css('width'), 10);

                    if(part_w >= 99 && $ttl.next().length) {
                        $ttl.after($('<li>').addClass('TonyM__clearfix'));
                        part_w = 0;
                    }
                });
            });

            //get scroll width
            var $scrlBl = $('<div>').css({
                overflowY: 'scroll',
                width: '50px',
                height: '50px',
                visibility: 'hidden'
            });

            $('body').append($scrlBl);
            this._scrollW = $scrlBl[0].offsetWidth - $scrlBl[0].clientWidth;
            $scrlBl.remove();

            //resize
            $(window).on('resize', function() {
                _self._crnt.desktop = window.innerWidth > opt.bp;
                _self._hideMM($tm_panel.find('> li.TonyM--btn-act'), true);
            });

            this._widthHandlers({
                elem: $tm_panel,
                delegate: '> li',
                namespace: 'togglemegamenu',
                desktop: true,
                events: {
                    'mouseenter': function(e) {
                        _self._showMM(this);
                    },
                    'mouseleave': function(e) {
                        _self._hideMM(this);
                    }
                }
            });

            //dropdown handlers
            this._widthHandlers({
                elem: $tm_dd.add($tm_dd.find('ul').parent()).find('> a'),
                namespace: 'toggledropdown',
                desktop: true,
                events: {
                    'mouseenter': function(e) {
                        _self._showDD($(this).parent(), opt.dd_dur);
                    },
                }
            });
            this._widthHandlers({
                elem: $tm_dd.add($tm_dd.find('ul').parent()),
                namespace: 'toggledropdown',
                desktop: true,
                events: {
                    'mouseleave': function(e) {
                        _self._hideDD(this, opt.dd_dur);
                    },
                }
            });

            if(opt.mob) {
                //set mob background
                this.bg = $('<div>').addClass('TonyM__bg').insertAfter($tm);
                var $tm_bg = this.bg;

                this.showTM = function(btn, clName) {
                    var $tm = this.bindings;

                    $tm.unbind('transitionend.togglemenu');

                    if(this.options.beforeOpenTM) this.options.beforeOpenTM($tm);

                    function resize() {
                        if(_self._crnt.desktop && $tm.hasClass('TonyM--open')) {
                            _self.hideTM(opt.btn, opt.btnActCl, true);
                            $tm.removeAttr('style');
                        } else {
                            var wind_h = window.innerHeight,
                                tm_t = $tm[0].getBoundingClientRect().top;

                            $tm.innerHeight(wind_h - tm_t);
                            $tm.perfectScrollbar('update');
                        }
                    };

                    $tm.perfectScrollbar();

                    $(window).on('resize.togglemenu', function() {
                        resize();
                    });

                    resize();

                    //_self._fixexBody('set');
                    $tm.perfectScrollbar('update');

                    $tm.one('transitionend.togglemenu', function() {
                        if(_self.options.afterOpenTM) _self.options.afterOpenTM($tm);
                    });

                    $tm.addClass('TonyM--ready');

                    $tm_bg.addClass('TonyM__bg--ready');
                    setTimeout(function() {
                        $tm.addClass('TonyM--open');

                        $tm_bg.addClass('TonyM__bg--show');
                    }, 0);

                    btn = btn ? btn : this.options.btn;
                    if(btn) $(btn).addClass(clName ? clName : 'active');

                    /*$('html, body').velocity('stop').velocity( 'scroll' , {
                        offset: 0,
                        duration: $('body').scrollTop() > 0 ? 500 : 0,
                        complete: function() {
                            _self._fixexBody('set');
                            $tm.perfectScrollbar('update');

                            $tm.one('transitionend.togglemenu', function() {
                                if(_self.options.afterOpenTM) _self.options.afterOpenTM($tm);
                            });

                            $tm.addClass('TonyM--ready');

                            $tm_bg.addClass('TonyM__bg--ready');
                            setTimeout(function() {
                                $tm.addClass('TonyM--open');

                                $tm_bg.addClass('TonyM__bg--show');
                            }, 0);

                            btn = btn ? btn : this.options.btn;
                            if(btn) $(btn).addClass(clName ? clName : 'active');
                        }
                    });*/
                };

                this.hideTM = function(btn, clName, is_fast) {
                    var $tm = this.bindings;

                    $tm.unbind('transitionend.togglemenu');
                    $(window).unbind('resize.togglemenu');

                    if(this.options.beforeCloseTM) this.options.beforeCloseTM($tm);

                    $tm.one('transitionend.togglemenu', function() {
                        _self._fixexBody('unset');
                        $tm.removeAttr('style');
                        $tm.perfectScrollbar('destroy').removeClass('ps');
                        $tm.removeClass('TonyM--ready');
                        $tm_bg.removeClass('TonyM__bg--ready');
                        if(_self.options.afterCloseTM) _self.options.afterCloseTM($tm);
                    });

                    $tm.removeClass('TonyM--open');

                    $tm_bg.removeClass('TonyM__bg--show');

                    if(is_fast) {
                        $tm.trigger('transitionend.togglemenu');
                    }

                    btn = btn ? btn : this.options.btn;
                    if(btn) $(btn).removeClass(clName ? clName : 'active');
                };

                this.toggleTM = function(btn, clName) {
                    btn = btn ? btn : this.options.btn;

                    if(!this.bindings.hasClass('TonyM--open')) {
                        this.showTM(btn, clName);

                        return 'show';
                    } else {
                        this.hideTM(btn, clName);

                        return 'hide';
                    }
                };

                this._toggleList = function(btn) {
                    var _self = this,
                        $btn = $(btn),
                        $li = $btn.parent(),
                        $lvl = $li.find('> *:not(a)');

                    $lvl.velocity('stop');

                    if($li.hasClass('TonyM--btn-act')) {
                        var $li_other = $li.find('.TonyM--btn-act');

                        $li.removeClass('TonyM--btn-act');

                        $lvl.show().velocity( 'slideUp', {
                            duration: _self.options.list_dur,
                            complete: function() {
                                $li_other.removeClass('TonyM--btn-act');
                                $lvl.add($li_other).removeAttr('style');
                                $tm.perfectScrollbar('update');
                            }
                        });
                    } else {
                        $lvl.velocity( 'slideDown', {
                            duration: _self.options.list_dur,
                            complete: function() {
                                $lvl.removeAttr('style');
                                $tm.perfectScrollbar('update');
                            }
                        });

                        $li.addClass('TonyM--btn-act');
                    }
                };

                this._widthHandlers({
                    elem: $tm_bg.add(opt.btn),
                    namespace: 'togglemenu',
                    desktop: false,
                    events: {
                        'click': function(e) {
                            _self.toggleTM(opt.btn, opt.btnActCl);

                            e.preventDefault();
                            return false;
                        },
                    }
                });

                this._widthHandlers({
                    elem: $tm_lvl.find('> a'),
                    namespace: 'togglelist',
                    desktop: false,
                    events: {
                        'click': function(e) {
                            var t = e.target;

                            if(t.tagName === 'A' && $(this).parent().hasClass('TonyM--btn-act')) return;

                            _self._toggleList(this);

                            e.preventDefault();
                            return false;
                        },
                    }
                });
            }

            $(window).trigger('resize');

            $tm.removeClass('TonyM--proces-init-js');

            $tm.addClass('TonyM--initialized');
        },
        _widthHandlers: function(obj) {
            var _self = this;

            $(window).on('resize.handlerswidth', function() {
                if(obj.desktopBp === _self._crnt.desktop) return;
                else obj.desktopBp = _self._crnt.desktop;

                var $elem = $(obj.elem),
                    ns = obj.namespace ? '.' + obj.namespace : '';

                if(obj.desktop === _self._crnt.desktop) {
                    $.each(obj.events, function(key, val) {
                        $elem.on(key + ns, obj.delegate, val);
                    });
                } else {
                    $.each(obj.events, function(key) {
                        $elem.unbind(key + ns);
                    });
                }
            });
        },
        _showMM: function(btn) {
            var _self = this,
                $btn = $(btn),
                $mm = $btn.find('.TonyM__mm'),
                $tm_mm = this.bindings.find('.TonyM__mm');

            if(!$mm.length) return;

            $mm.unbind('transitionend.mmclose');
            $tm_mm.not($mm).removeClass('TonyM__mm--ready TonyM__mm--animate TonyM__mm--open');

            if(this.options.beforeOpenMM) this.options.beforeOpenMM($mm, $btn);

            this._crnt.btn = $btn;
            this._crnt.mm = $mm;

            $btn.addClass('TonyM--btn-act');
            $mm.addClass('TonyM__mm--ready');

            this._beforeOpenMM();

            $mm.one('transitionend.mmopen', function() {
                $mm.addClass('TonyM__mm--open');
                _self._afterOpenMM();
                if(_self.options.afterOpenMM) _self.options.afterOpenMM($mm, $btn);
            });
            $mm.addClass('TonyM__mm--animate');
        },
        _hideMM: function(btn, is_fast) {
            var _self = this,
                $btn = $(btn),
                $mm = $btn.find('.TonyM__mm');

            if(!$mm.length) return;

            $mm.unbind('transitionend.mmopen');

            if(this.options.beforeCloseMM) this.options.beforeCloseMM($mm, $btn);

            this._crnt.btn = $btn;
            this._crnt.mm = $mm;

            this._beforeCloseMM();

            $mm.one('transitionend.mmclose', function() {
                $mm.removeClass('TonyM__mm--ready');
                $btn.removeClass('TonyM--btn-act');
                _self._afterCloseMM();
                if(_self.options.afterCloseMM) _self.options.afterCloseMM($mm, $btn);
            });
            $mm.removeClass('TonyM__mm--open TonyM__mm--animate');

            if(is_fast) $mm.trigger('transitionend.mmclose');
        },
        _beforeOpenMM: function() {
            var _self = this;

            function setWidth() {
                var mm_w = _self._crnt.mm.data('tm-w'),
                    w_funcs = _self._widthFunctions[_self.options.dir],
                    set_w;

                if(w_funcs.hasOwnProperty(mm_w)) set_w = w_funcs[mm_w].apply(_self, arguments);
                else if($.isNumeric(mm_w)) set_w = mm_w;

                if(set_w) _self._crnt.mm.innerWidth(set_w);
            };

            function setAlign(attr, method) {
                var mm_a = _self._crnt.mm.data(attr),
                    a_funcs = _self._alignFunctions[method][_self.options.dir],
                    set_a;

                if(!mm_a) mm_a = a_funcs['define'];

                if(a_funcs.hasOwnProperty(mm_a)) set_a = a_funcs[mm_a].apply(_self, arguments);

                if(set_a) _self._crnt.mm.css(set_a);
            };

            function checkWidth() {
                var $mm = _self._crnt.mm,
                    mm_l = $mm[0].getBoundingClientRect().left,
                    mm_r = $mm[0].getBoundingClientRect().right,
                    mm_w = $mm.innerWidth(),
                    wind_w = window.innerWidth - _self._scrollW;

                if(mm_w > wind_w) {
                    if(mm_l > 0) {
                        $mm.innerWidth(wind_w - mm_l);
                    } else if(mm_r < wind_w) {
                        $mm.innerWidth(wind_w - (wind_w - mm_r));
                    } else {
                        $mm.innerWidth(wind_w);
                    }
                } else if(mm_l < 0) {
                    $mm.innerWidth(mm_w + mm_l);
                } else if(mm_r > wind_w) {
                    $mm.innerWidth(mm_w - (mm_r - wind_w));
                }
            };

            setWidth();

            setAlign('tm-a-h', 'alignHorizontal');

            setAlign('tm-a-v', 'alignVertical');

            checkWidth();

            this._initSlider({
                selector: '.TonyM__featured',
                params: {
                    infinite: true,
                    autoplay: true,
                    autoplaySpeed: 2000,
                    speed: 500
                },
                callback: function ($slider) {
                    var $img = $slider.find('.slick-current'),
                        $arrows = $slider.find('.slick-arrow'),
                        img_h = $img.eq(0).innerHeight(),
                        arrows_h = $arrows.eq(0).innerHeight(),
                        slider_t = parseInt($slider.css('margin-top'), 10),
                        set_top = img_h / 2 - arrows_h / 2 - slider_t;

                    $arrows.css({top: set_top});
                }
            });
            this._initSlider({
                selector: '.TonyM__special',
                params: {
                    dots: false,
                    arrows: true,
                    infinite: true,
                    speed: 300,
                    slidesToShow: 2,
                    slidesToScroll: 2,
                    adaptiveHeight: true
                }
            });
            this._initSlider({
                selector: '.TonyM__brands',
                params: {
                    slidesToShow: 10,
                    slidesToScroll: 5,
                    infinite: true,
                    autoplay: true,
                    autoplaySpeed: 3000,
                    speed: 1000
                }
            });

            this._toggleTransition('show');
        },
        _afterOpenMM: function() {
            this._checkWindowLimit();
            this._toggleTransition('show');
        },
        _beforeCloseMM: function() {
            this._toggleTransition('hide');
        },
        _afterCloseMM: function() {
            var $mm = this._crnt.mm;

            $mm.removeAttr('style');

            if($mm.hasClass('TonyM--scroll')) {
                $mm.removeClass('TonyM--scroll');
                $mm.perfectScrollbar('destroy').removeClass('ps');
            }
        },
        _initSlider: function(obj) {
            var $mm = this._crnt.mm,
                $slider_all = $mm.find(obj.selector);

            if($slider_all.length) {
                $slider_all.each(function() {
                    var $slider = $(this);

                    if($slider.hasClass('slick-initialized')) {
                        $slider.slick('setPosition');
                    } else {
                        $slider.slick(obj.params);
                    }

                    if(obj.callback && typeof obj.callback === 'function') {
                        obj.callback($slider);
                    }
                });
            }
        },
        _animations: {
            'show': 'TonyM__mm--anim_show',
            'fade': 'TonyM__mm--anim_fade',
            'emersion': 'TonyM__mm--anim_emersion',
            'emersion-vertical': 'TonyM__mm--anim_emersion-vert',
        },
        _widthFunctions: {
            'row': {
                'menu': function() {
                    return this.bindings.innerWidth();
                },
                'fullwidth': function() {
                    return window.innerWidth - this._scrollW;
                },
                'container': function() {
                    return $(this.options.cont).innerWidth();
                },
            },
            'column': {
                'fullwidth': function() {
                    switch(this.options.pos) {
                        case 'right':
                            return window.innerWidth - this._scrollW - (window.innerWidth - this._scrollW - this.bindings[0].getBoundingClientRect().left);
                        default:
                            return window.innerWidth - this.bindings[0].getBoundingClientRect().right - this._scrollW;
                    }
                },
            },
        },
        _alignClasses: {
            alignHorizontal: {
                'row': {
                    'item-left': {
                        'btn': 'TonyM--relative',
                    },
                    'item-right': {
                        'btn': 'TonyM--relative',
                        'mm': 'TonyM__mm--right',
                    },
                    'item-center': {
                        'btn': 'TonyM--relative',
                        'mm': 'TonyM__mm--center',
                    },
                    'menu-right': {
                        'mm': 'TonyM__mm--right',
                    },
                    'menu-center': {
                        'mm': 'TonyM__mm--center',
                    },
                    'container-center': {
                        'mm': 'TonyM__mm--center',
                    },
                    'window-center': {
                        'mm': 'TonyM__mm--center',
                    },
                },
                'column': {

                },
            },
            alignVertical: {
                'row': {

                },
                'column': {
                    'item-top': {
                        'btn': 'TonyM--relative',
                    },
                    'item-bottom': {
                        'btn': 'TonyM--relative',
                        'mm': 'TonyM__mm--bottom',
                    },
                    'item-center': {
                        'btn': 'TonyM--relative',
                        'mm': 'TonyM__mm--center',
                    },
                    'menu-bottom': {
                        'mm': 'TonyM__mm--bottom',
                    },
                },
            },
        },
        _alignFunctions: {
            alignHorizontal: {
                'row': {
                    'container-left': function() {
                        var $cont = $(this.options.cont),
                            cont_l = $cont[0].getBoundingClientRect().left,
                            tm_l = this.bindings[0].getBoundingClientRect().left;

                        return {
                            left: cont_l - tm_l
                        };
                    },
                    'container-right': function() {
                        var $cont = $(this.options.cont),
                            cont_r = $cont[0].getBoundingClientRect().right,
                            tm_r = this.bindings[0].getBoundingClientRect().right;

                        return {
                            left: 'auto',
                            right: tm_r - cont_r
                        };
                    },
                    'container-center': function() {
                        var $cont = $(this.options.cont),
                            cont_l = $cont[0].getBoundingClientRect().left,
                            cont_w =  $cont.innerWidth(),
                            tm_l = this.bindings[0].getBoundingClientRect().left;

                        return {
                            left: cont_l - tm_l + cont_w/2
                        };
                    },
                    'window-left': function() {
                        var tm_l = this.bindings[0].getBoundingClientRect().left;

                        return {
                            left: tm_l * -1
                        };
                    },
                    'window-right': function() {
                        var wind_w = window.innerWidth - this._scrollW,
                            tm_r = this.bindings[0].getBoundingClientRect().right;

                        return {
                            left: 'auto',
                            right: (wind_w - tm_r) * -1
                        };
                    },
                    'window-center': function() {
                        var wind_w = window.innerWidth - this._scrollW,
                            tm_l = this.bindings[0].getBoundingClientRect().left;

                        return {
                            left: tm_l * -1 + wind_w/2
                        };
                    },
                    'define': 'menu-left',
                },
                'column': {
                    'container-left': function() {
                        var $cont = $(this.options.cont),
                            cont_l = $cont[0].getBoundingClientRect().left,
                            tm_l = this.bindings[0].getBoundingClientRect().left,
                            tm_w = this.bindings.innerWidth();

                        return {
                            left: 'auto',
                            right: tm_l - cont_l + tm_w
                        };
                    },
                    'container-right': function() {
                        var $cont = $(this.options.cont),
                            cont_r = $cont[0].getBoundingClientRect().right,
                            tm_r = this.bindings[0].getBoundingClientRect().right,
                            tm_w = this.bindings.innerWidth();

                        return {
                            left: cont_r - tm_r + tm_w
                        };
                    },
                    'define': 'item',
                }
            },
            alignVertical: {
                'row': {
                    'container-bottom': function() {
                        var $cont = $(this.options.cont),
                            cont_b = $cont[0].getBoundingClientRect().bottom,
                            tm_b = this.bindings[0].getBoundingClientRect().bottom,
                            tm_h = this.bindings.innerHeight();

                        return {
                            top: cont_b - tm_b + tm_h
                        };
                    },
                    'menu-bottom': function() {
                        var $btn = this._crnt.btn;

                        if(!$btn.hasClass('TonyM--relative')) return {};

                        var btn_b = $btn[0].getBoundingClientRect().bottom,
                            btn_h = $btn.innerHeight(),
                            tm_b = this.bindings[0].getBoundingClientRect().bottom;

                        return {
                            top: tm_b - btn_b + btn_h
                        };
                    },
                    'define': 'menu-bottom',
                },
                'column': {
                    'define': 'menu-top',
                }
            },
        },
        _toggleTransition: function(act) {
            var $btn = this._crnt.btn,
                $mm = this._crnt.mm,
                $trns = $btn.find('.TonyM__trns');

            switch(act) {
                case 'show':
                    var set_obj = {},
                        btn_pos = $btn[0].getBoundingClientRect(),
                        mm_pos = $mm[0].getBoundingClientRect();

                    if(this.options.dir === 'row') {
                        set_obj.width = $mm.innerWidth();
                        set_obj.height = mm_pos.top - btn_pos.bottom + 10;

                        if(mm_pos.left > btn_pos.left) {
                            set_obj.width += mm_pos.left - btn_pos.left;
                        } else if (mm_pos.right < btn_pos.right) {
                            set_obj.width += btn_pos.right - mm_pos.right;
                            set_obj.left = (btn_pos.left - mm_pos.left) * -1;
                        } else {
                            set_obj.left = (btn_pos.left - mm_pos.left) * -1;
                        }

                    } else if(this.options.dir === 'column') {
                        set_obj.height = $mm.innerHeight();

                        if(this.options.pos === 'left') {
                            set_obj.width = mm_pos.left - btn_pos.right;

                        } else if(this.options.pos === 'right') {
                            set_obj.width = btn_pos.left - mm_pos.right;
                        }

                        if(mm_pos.top > btn_pos.top) {
                            set_obj.height += mm_pos.top - btn_pos.top;
                        } else if (mm_pos.bottom < btn_pos.bottom) {
                            set_obj.height += btn_pos.bottom - mm_pos.bottom;
                            set_obj.top = (btn_pos.top - mm_pos.top) * -1;
                        } else {
                            set_obj.top = (btn_pos.top - mm_pos.top) * -1;
                        }
                    }

                    $trns.css(set_obj);
                    break;

                case 'hide':
                    $trns.removeAttr('style');
                    break;
            }
        },
        _checkWindowLimit: function() {
            if(this.options.dir !== 'row') return;

            var $mm = this._crnt.mm,
                mm_b = $mm[0].getBoundingClientRect().bottom,
                wind_h = window.innerHeight;

            if(mm_b > wind_h) {
                var mm_h = $mm.innerHeight();

                $mm.addClass('TonyM--scroll');
                $mm.css({ 'max-height': mm_h - (mm_b - wind_h) });
                $mm.perfectScrollbar({
                    suppressScrollX: true
                });
            }

        },
        _fixexBody: function (act) {
            var $body = $('body');
            switch(act) {
                case 'set':
                    $body.addClass('TonyM--ovf-hide').css({ 'padding-right': this._scrollW });
                    break;
                case 'unset':
                    $body.removeClass('TonyM--ovf-hide').removeAttr('style');
            }
        },
        _showDD: function(btn, dur) {
            var $btn = $(btn),
                $dd = $btn.find('> ul'),
                $mm = this._crnt.mm;

            $dd.addClass('TonyM__dd--ready');

            if($mm.hasClass('ps') && $mm.hasClass('TonyM__mm--simple')) {
                $dd.addClass('TonyM__dd--inner');
                $mm.perfectScrollbar('update');
            } else {
                var dd_pos = $dd[0].getBoundingClientRect(),
                    wind_w = window.innerWidth - this._scrollW,
                    wind_h = window.innerHeight,
                    dd_lim_l = (dd_pos.left - $dd.innerWidth() - $dd.parents('ul').first().innerWidth()) < 0,
                    dd_lim_r = dd_pos.right > wind_w,
                    dd_lim_b = dd_pos.bottom > wind_h,
                    is_prnt_reverse = $dd.parents('ul').first().hasClass('TonyM__dd--reverse');

                if(dd_lim_r || (is_prnt_reverse && !dd_lim_l)) $dd.addClass('TonyM__dd--reverse');

                if(dd_lim_b) $dd.addClass('TonyM__dd--btm');
            }

            $dd.velocity('stop').velocity({ opacity: 1 }, dur);
        },
        _hideDD: function(btn, dur) {
            var $btn = $(btn),
                $dd = $btn.find('> ul'),
                $mm = this._crnt.mm;

            $dd.velocity('stop').velocity({ opacity: 0 }, {
                duration: dur,
                complete: function() {
                    $dd.removeClass('TonyM__dd--ready TonyM__dd--reverse TonyM__dd--btm TonyM__dd--inner').removeAttr('style');
                    if($mm.hasClass('ps')) $mm.perfectScrollbar('update');
                }
            });
        },
        displace: function (cont, pos) {
            var $tm_n_bg = $(this.element).add(this.bg);

            $(cont === 'return' ? this.parent : cont)[pos ? pos : 'append']($tm_n_bg);
        },
        _init: function () {

        },
        _setOption: function(key, value, is_attr) {
            if(is_attr && !value) return;

            $.Widget.prototype._setOption.apply(this, arguments);
        },
        destroy: function() {
            $.Widget.prototype.destroy.call(this);
        }
    };

    $.widget( 'ui.TonyM', TonyM );
}));


/*DOCUMENTATION*/

/*
 * START HTML
 *
 * CLASS .TonyM
 *
 * ******** DIRRECTION ******** --- data-tm-dir
 * 'row' - *default
 * 'column'
 *
 * ******** MOBILE MENU ******** --- data-tm-mob
 * bool - *default: true
 *
 * ******** ANIMATION TYPE ******** --- data-tm-anm
 * 'toggle'
 * 'fade'
 * 'emersion' - *default
 * 'emersion-vertical'
 *
 * ******** POSITION ******** --- data-tm-pos
 * 'left' - *default
 * 'right'
 *
 * ******** CONTAINER ******** --- data-tm-cont
 * selector - *default: empty
 *
 *
 * CLASS .TonyM__mm
 *
 * ******** ALIGNS ********
 *
 * //HORIZONTAL --- data-tm-a-h
 * ////ROW
 * 'item-left'
 * 'item-right'
 * 'item-center'
 * 'menu-left' - *default
 * 'menu-right'
 * 'menu-center'
 * 'container-left'
 * 'container-right'
 * 'container-center'
 * 'window-left'
 * 'window-right'
 * 'window-center'
 * ////COLUMN
 * 'item' - *default
 * 'container-left'
 * 'container-right'
 *
 * //VERTICAL --- data-tm-a-v
 * ////ROW
 * 'menu-bottom' - *default
 * 'container-bottom'
 * ////COLUMN
 * 'item-top'
 * 'item-bottom'
 * 'item-center'
 * 'menu-top' - *default
 * 'menu-bottom'
 *
 * ******** WIDTH ******** --- data-tm-w
 *
 * ////ROW
 * int
 * 'menu'
 * 'fullwidth'
 * 'container'
 *
 * ////COLUMN
 * int
 * 'fullwidth'
 *
 * END HTML
 * */

/*
 * START JS
 * ******** METHOD ********
 *
 * toggleTM( btn, className )
 * showTM( btn, clName )
 * hideTM( btn, clName, is_fast )
 * displace( container || 'return', position ) position = jQuery methods (after, before, append, prepend...)
 *
 * ******** CALLBACK ********
 *
 * beforeOpenMM( $mm, $btn )
 * afterOpenMM( $mm, $btn )
 * beforeCloseMM( $mm, $btn )
 * afterCloseMM( $mm, $btn )
 * beforeOpenTM( $tm )
 * afterOpenTM( $tm )
 * beforeCloseTM( $tm )
 * afterCloseTM( $tm )
 *
 * ******** EVENTS ********
 *
 * beforeopenmm
 * afteropenmm
 * beforeclosemm
 * afterclosemm
 * beforeopentm
 * afteropentm
 * beforeclosetm
 * afterclosetm
 *
 * END JS
 * */
