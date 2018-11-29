<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ecomwares\Wkmegamenu\Model\Category;

/**
 * Class DataProvider
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DataProvider extends \Magento\Catalog\Model\Category\DataProvider
{
    /**
     * @return array
     */
    protected function getFieldsMap()
    {
        return [
            'general' =>
                [
                    'parent',
                    'path',
                    'is_active',
                    'include_in_menu',
                    'name',
                ],
            'content' =>
                [
                    'image',
                    'description',
                    'landing_page',
                ],
            'display_settings' =>
                [
                    'display_mode',
                    'is_anchor',
                    'available_sort_by',
                    'use_config.available_sort_by',
                    'default_sort_by',
                    'use_config.default_sort_by',
                    'filter_price_range',
                    'use_config.filter_price_range',
                ],
            'search_engine_optimization' =>
                [
                    'url_key',
                    'url_key_create_redirect',
                    'use_default.url_key',
                    'url_key_group',
                    'meta_title',
                    'meta_keywords',
                    'meta_description',
                ],
            'assign_products' =>
                [
                ],
            'design' =>
                [
                    'custom_use_parent_settings',
                    'custom_apply_to_products',
                    'custom_design',
                    'page_layout',
                    'custom_layout_update',
                ],
            'schedule_design_update' =>
                [
                    'custom_design_from',
                    'custom_design_to',
                ],
            'ys-menu' =>
                [
                    'ys_nav_simple',
                    'ys_nav_simple_width',
                    'ys_nav_btm',
                    'ys_nav_top',
                    'ys_nav_left',
                    'ys_nav_left_width',
                    'ys_nav_right',
                    'ys_nav_right_width',
                    'ys_category_lable',
                    'ys_label_color',
                    'ys_cat_image',
                    'ys_data_tm_width',
                    'ys_data_tm_align_vertical',
                    'ys_data_tm_align_horizontal',
                    'ys_products_list_type',
                    'ys_gal_proportion',
                    'ys_gal_order',
                    'ys_products_title',
                    'ys_products_ids',
                    'ys_columns',
                ],
            'category_view_optimization' =>
                [
                ],
            'category_permissions' =>
                [
                ],
        ];
    }
}
