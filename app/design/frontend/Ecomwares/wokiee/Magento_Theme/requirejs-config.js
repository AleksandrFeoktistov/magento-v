/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    paths: {
        'popper':  'Magento_Theme/js/popper.min',
        'bootstrapminify':  'Magento_Theme/js/bootstrap.min',
        'slick':            'Magento_Theme/js/slick.min',
        'jquery-bridget':   'Magento_Theme/js/jquery-bridget',
        'imagesloaded':     'Magento_Theme/js/imagesloaded',
        'isotop':           'Magento_Theme/js/isotope.pkgd.min',
        'countdown_pl':     'Magento_Theme/js/countdown/jquery.plugin.min',
        'countdown_cd':     'Magento_Theme/js/countdown/jquery.countdown.min'
    },
    shim: {
        'bootstrapminify': { deps: ['jquery'] },
        'slick':  { deps: ['jquery'] },
        'isotop':  { deps: ['jquery'] },
        'imagesloaded':  { deps: ['jquery'] },
        'countdown_pl' : { deps :['jquery'] },
        'countdown_cd' : { deps :['jquery', 'countdown_pl'] }
    }
};
