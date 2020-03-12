<?php namespace App\Service;

class WebSkinMapping {

    public $full_mapping = [
        'primary_logo_url'=> ['item_number' => '1', 'name' => 'Brand Logo', 'field_name' => 'primary_logo_url', 'child_color_fields' => false, 'color_field' => false,
                'child_image_fields'=>['app_logo_url']],


        'hero_image_url'=> ['item_number' => '2', 'name' => 'Merchant Hero Image', 'field_name' => 'hero_image_url', 'child_color_fields' => false, 'color_field' => false, 'child_image_fields'=>[]],
//
//
//        'app_logo_url'=> ['item_number' => '3', 'name' => 'App Logo', 'field_name' => 'app_logo_url', 'child_color_fields' => false, 'color_field' => false],
//
//
//        'loyalty_logo_url'=> ['item_number' => '4', 'name' => 'Loyalty Logo', 'field_name' => 'loyalty_logo_url', 'child_color_fields' => false, 'color_field' => false],


        'primary_bg_color'=> ['item_number' => '3', 'name' => 'Main Background', 'field_name' => 'primary_bg_color', 'child_color_fields' => ['dialog_bg_color'],
            'child_off_color_fields'=>['card_bg_color', 'link_hover_color', 'menu_nav_item_hover_color', 'menu_item_hover_color', 'loyalty_nth_row_color'],

            'color_field' => true],


        //'card_bg_color'=> ['item_number' => '4', 'name' => 'Secondary Background', 'field_name' => 'card_bg_color', 'child_color_fields' => ['modal_row_color', 'menu_item_hover_color', 'loyalty_nth_row_color'], 'color_field' => true],


        'footer_bg_color'=> ['item_number' => '4', 'name' => 'Header and Footer Color', 'field_name' => 'footer_bg_color', 'child_color_fields' => [],
                             'child_light_dark_fields'=>['footer_text_color'],
                        'color_field' => true],


        'primary_button_color'=> ['item_number' => '5', 'name' => 'Primary Button Color', 'field_name' => 'primary_button_color',
                                    'child_color_fields' => ['small_nav_bg_color', 'menu_nav_bg_color', 'menu_price_text_color', 'plus_minus_button_bg_active', 'link_color', 'map_pin_color','bag_text_color'],
                                    'child_off_color_fields'=>['primary_button_hover_color', 'link_hover_color', 'menu_nav_item_hover_color', 'menu_nav_item_selected_text_color'],
                                    'child_light_dark_fields'=>['primary_button_text_color', 'menu_nav_item_text_color'],
                                    'color_field' => true],

        'secondary_button_color'=> ['item_number' => '6', 'name' => 'Secondary Button Color', 'field_name' => 'secondary_button_color', 'child_color_fields' => [],
                                    'child_off_color_fields'=>['secondary_button_hover_color'],
                                    'child_light_dark_fields'=>['secondary_button_text_color'],
                                    'color_field' => true],

        //'secondary_button_hover_color'=> ['item_number' => '11', 'name' => 'Secondary Button Hover', 'field_name' => 'secondary_button_hover_color', 'child_color_fields' => [], 'color_field' => true],

//        'card_border_color'=> ['item_number' => '12', 'name' => 'Borders', 'field_name' => 'card_border_color', 'child_color_fields' => ['drop_down_border_color', 'text_field_border_color', 'loyalty_border_color', 'checkbox_border_color', 'plus_minus_button_border_color'], 'color_field' => true],

//        'separator_color'=> ['item_number' => '13', 'name' => 'Separators', 'field_name' => 'separator_color', 'child_color_fields' => ['checkbox_unchecked_color', 'card_separator_color', 'footer_top_separator_color', 'plus_minus_button_bg_inactive', 'small_nav_separator_color', 'small_primary_nav_separator_color', 'small_secondary_nav_separator_color', 'modal_separator_color'], 'color_field' => true],

     //   'primary_button_text_color'=> ['item_number' => '7', 'name' => 'Button Text (usually white)', 'field_name' => 'primary_button_text_color', 'child_color_fields' => ['address_block_text_color', 'bag_text_color', 'menu_nav_item_text_color', 'menu_nav_primary_item_text_color', 'secondary_button_text_color', 'footer_text_color', 'check_color', 'small_nav_text_color'], 'color_field' => true],

        'light_text_color'=> ['item_number' => '7', 'name' => 'Light Text Color', 'field_name' => 'light_text_color', 'child_color_fields' => ['info_window_light_text_color'], 'color_field' => true],

        'medium_text_color'=> ['item_number' => '8', 'name' => 'Medium Text Color', 'field_name' => 'medium_text_color', 'child_color_fields' => ['modal_row_text_color', 'address_block_text_color'], 'color_field' => true],

        'dark_text_color'=> ['item_number' => '9', 'name' => 'Dark Text Color', 'field_name' => 'dark_text_color', 'child_color_fields' => ['info_window_dark_text_color', 'menu_nav_primary_item_text_color', 'check_color', 'small_nav_text_color'], 'color_field' => true],



        'menu_nav_primary_item_text_color'=> [
            'item_number' => '10',
            'name' => 'Navigation Icon Color',
            'field_name' => 'menu_nav_primary_item_text_color',
            'child_color_fields' => ['address_block_text_color', 'bag_bg_color'],
            'color_field' => true
        ],

        'address_block_text_color'=> ['item_number' => '11', 'name' => 'Address Block Color', 'field_name' => 'address_block_text_color', 'child_color_fields' => [], 'color_field' => true],
        /*,
        'menu_nav_bg_color'=> [
            'item_number' => '11',
            'name' => 'Navigation Background Color',
            'field_name' => 'menu_nav_bg_color',
            'child_color_fields' => [],
            'color_field' => true
        ]*/
    ];

    public $image_mapping = [
        'primary_logo_url'=>[
            'max_width_height'=>400,
            's3_path'=> '/web/brand-assets/logo/',
            'new_file_extension'=>'png',
            'tmp_location'=> '/img/web_skin/logo/'
        ],
        'hero_image_url'=>[
            'set_width'=>1446,
            'set_height'=>771,
            's3_path'=> 'merchant-location-images/large/',
            'new_file_extension'=>'jpg',
            'tmp_location'=> '/img/web_skin/hero/'
        ],
        'app_logo_url'=>[
            'set_width'=>118,
            'set_height'=>118,
            's3_path'=> '/web/brand-assets//app-icon.png',
            'new_file_extension'=>'png',
            'tmp_location'=> '/img/web_skin/app_logo/'
        ],
    ];
}