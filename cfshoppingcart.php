<?php
/*
Plugin Name: Cf Shopping Cart
Plugin URI: http://takeai.silverpigeon.jp/
Description: Placement simply shopping cart to content.
Author: AI.Takeuchi
Version: 0.3.2
Author URI: http://takeai.silverpigeon.jp/
*/

// -*- Encoding: utf8n -*-
// If you notice a my mistake(Program, English...), Please tell me.

/*  Copyright 2009-2011 AI Takeuchi (email: takeai@silverpigeon.jp)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

require_once('module/common.php');
$plugin_folder = get_plugin_folder();
$plugin_fullpath = get_plugin_fullpath();
$plugin_path = get_plugin_path();
$plugin_uri = get_plugin_uri();

load_plugin_textdomain('cfshoppingcart',
                       $plugin_path . '/lang', $plugin_folder . '/lang');

/* session start */
add_action('init', 'cfshoppingcart_init_session_start');
function cfshoppingcart_init_session_start(){
    global $Ktai_Style;
    if (is_object($Ktai_Style)) {
        if ($Ktai_Style->is_ktai()) {
            ini_set('session.use_cookies','off');
            ini_set('session.use_trans_sid', '1');
        }
    }
    if (!session_id()) {
        session_start();
    }
    // create sidebar widget html
    require_once('module/sum.php');
    $sname = 'cfshoppingcart';
    $html  = cfshoppingcart_widget_html($_SESSION[$sname]['sum']);
    $_SESSION[$sname]['sum']['html'] = $html;
}


if (is_admin()) {
    $wpCFShoppingcart = & new WpCFShoppingcart();
    // Registration of management screen header output function.
    add_action('admin_head', array(&$wpCFShoppingcart, 'addAdminHead'));
    // Registration of management screen function.
    add_action('admin_menu', array(&$wpCFShoppingcart, 'addAdminMenu'));
    add_action('admin_notices', 'cfshoppingcart_action_admin_notices', 5);
} else {
    /* $handle スクリプトの識別名
     * $src(optional) スクリプトファイルへのパス
     * http://で始まるURLまたはサイトルートから絶対パス
     * $deps(optional) 依存するスクリプトのリスト（配列）
     * $ver(optional) スクリプトのバージョン
     */
    //wp_enqueue_script('jquery', $plugin_uri . '/js/jquery.js', null, '1.4.4');
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery.form', $plugin_uri . '/js/jquery.form.js', array('jquery'), 2.52);
    wp_enqueue_script('jquery.pnotify', $plugin_uri . '/js/jquery.pnotify.min.js', array('jquery'), '1.0.1');
    wp_enqueue_script('jquery.alerts', $plugin_uri . '/js/jquery.alerts.js', array('jquery'), 1.1);
    //wp_enqueue_script('jQuery.cookie', $plugin_uri . '/js/jquery.cookie.js', array('jQuery'), null);
    //wp_enqueue_script('jQuery.droppy', $plugin_uri . '/module/jquery.droppy.js', array('jQuery'), null);
    
    require_once('module/add_wp_head.php');
    add_action('wp_head', 'cfshoppingcart_add_wp_head');
    require_once('module/function_cfshoppingcart.php');
    require_once('module/show_product.php');
    add_action('the_content', 'show_product');
    // short-code
    require_once('module/cart.php');
    add_shortcode('cfshoppingcart_cart', 'cfshoppingcart_cart');
    //
    require_once('module/cart_link.php');
    add_shortcode('cfshoppingcart_cart_link', 'cfshoppingcart_cart_link');
    //
    require_once('module/send_order_link.php');
    add_shortcode('cfshoppingcart_send_order_link', 'cfshoppingcart_send_order_link');
    // Can use the short-code in sidebar widget
    add_filter('widget_text', 'do_shortcode');
}

function cfshoppingcart_action_admin_notices() {
    //check_wpcf7_cfshoppingcart_shortcode_handler();
    //function check_wpcf7_cfshoppingcart_shortcode_handler() {
    if (!function_exists('symlink')) {
        function symlink($a,$b){ return false; }
        $symlink_msg = '<li>' . __("This server don't have symlink function, therefore can't use button 'Set extend module for Contact Form 7' in Cf Shopping Cart setting screen.",'cfshoppingcart') . '</li>';
    }
    if (!function_exists('wpcf7_cfshoppingcart_shortcode_handler')) {
        echo '<p>' . __("Could you install extended module for Contact Form 7 plugin.",'cfshoppingcart') . '<br />' . __("Two ways:",'cfshoppingcart') . '<ol><li>' . __("Visit Cf Shopping Cart Setting Screen and click button: 'Set extend module for Contact Form 7'.",'cfshoppingcart') . '</li><li>' . __("Copy 'cfshoppingcart.php' file from '/wp-content/plugins/cf-shopping-cart/contact-form-7-module/' to '/wp-content/plugins/contact-form-7/module/'.",'cfshoppingcart') . '</li>' . $symlink_msg . '</ol></p>';
    }
    //}
}

/* Data model */
class WpCFShoppingcartItemModel {
    //
}
class WpCFShoppingcartModel {
    // member variable
    var $version;// = '0.2.11';
    var $debug;// = '';
    //var $dont_create_symbolic_link_cf7_module;
    var $custom_fields;// = mb_split(',', 'Product_ID,Name,Price');
    var $price_field_name;// = 'Price';
    // stock
    var $number_of_stock_field_name;
    var $type_of_show_sold_out_message;
    var $sold_out_message;
    
    var $currency_format;// = '$%.02fYen';
    var $quantity;// = 'Quantity';
    var $cart_url;// = 'http://';
    var $send_order_url;// = 'http://';
    var $qfgetthumb_option_1;// = 'tag=0&num=0&crop_w=150&width=160&crop_h=150&height=160';
    var $qfgetthumb_default_image;// = '';
    var $is_use_shipping;// = '';
    var $cfshoppingcart_justamomentplease;// = 'text-align:center;background:#fff09e;border:2px solid orange;';
    var $max_quantity_of_one_commodity;// = 12;
    var $max_quantity_of_total_order;// = 36;
    //
    var $show_commodity_on_home;// = 'checked';
    var $show_commodity_on_page;// = 'checked';
    var $show_commodity_on_archive;// = 'checked';
    var $show_commodity_on_single;// = 'checked';
    var $show_commodity_on_manually;// = '';
    var $go_to_cart_text;
    var $orderer_input_screen_text;
    var $thanks_url;
    var $shipping;
    var $shipping_enabled;
    var $shop_now_closed;
    var $closed_message_for_sidebar_widget;
    var $table_tag;
    
    // constructor
    function WpCFShoppingcartModel() {
        // default value
        $this->version = '0.3.2';
        $this->debug = '';
        //$this->dont_create_symbolic_link_cf7_module = '';
        $this->custom_fields = mb_split(',', 'Product_ID,Name,Price');
        $this->price_field_name = 'Price';
        $this->currency_format = '$%.02fYen';
        $this->quantity = 'Quantity';
        $this->cart_url = 'http://';
        $this->send_order_url = 'http://';
        $this->qfgetthumb_option_1 = 'tag=0&num=0&crop_w=150&width=160&crop_h=150&height=160';
        $this->qfgetthumb_default_image = '';
        $this->is_use_shipping = '';
        $this->cfshoppingcart_justamomentplease = 'text-align:center;background:#fff09e;border:2px solid orange;';
        $this->max_quantity_of_one_commodity = 12;
        $this->max_quantity_of_total_order = 36;
        //
        $this->show_commodity_on_home = 'checked';
        $this->show_commodity_on_page = 'checked';
        $this->show_commodity_on_archive = 'checked';
        $this->show_commodity_on_single = 'checked';
        $this->show_commodity_on_manually = '';
        //
        $this->go_to_cart_text = '&raquo;&nbsp;Go To Cart';
        $this->orderer_input_screen_text = '&raquo;&nbsp;Orderer Input screen';
        $this->thanks_url = '';
        $this->shipping = array();
        $this->shipping_enabled = '';
        // stock
        $this->number_of_stock_field_name = '';
        $this->type_of_show_sold_out_message = '';
        $this->sold_out_message = 'Sold out';
        $this->shop_now_closed = '';
        $this->closed_message_for_sidebar_widget = 'Shop now closed';
        //
        $this->table_tag = 'table'; // table, dl
    }

    function toDouble($v) {
        if (preg_match('/-?[0-9]+\.[0-9]+$/', $v)) {
            return $v;
        } else if (preg_match('/-?[0-9]+$/', $v)) {
            return $v;
        } else {
            return '';
        }
    }

    function toAlpha($v) {
        return preg_replace('/[^-?[0-9]+\.[0-9]+$/', $v);
    }
    
    //
    function get_version() {
        return $this->version;
    }
    
    function is_debug() {
        if ($this->debug) return true;
        else return false;
    }
    function setDebug($fields) {
        $this->debug = $fields;
    }
    function getDebug() {
        return $this->debug;
    }
    /*
    function setDontCreateSymbolicLinkCF7Module($fields) {
        $fields = preg_replace('/[^a-zA-Z]/', '', $fields);
        $this->dont_create_symbolic_link_cf7_module = $fields;
    }
    function getDontCreateSymbolicLinkCF7Module() {
        return $this->dont_create_symbolic_link_cf7_module;
    }
    function is_dontCreateSymbolicLinkCF7Module() {
        if ($this->dont_create_symbolic_link_cf7_module) return true;
        else return false;
    }
      */
    //
    function getShopNowClosedUserLevel() {
        return 6; // user level
    }
    function setShopNowClosed($fields) {
        $fields = preg_replace('/[^a-zA-Z]/', '', $fields);
        $this->shop_now_closed = $fields;
    }
    function getShopNowClosed() {
        return $this->shop_now_closed;
    }
    function setClosedMessageForSidebarWidget($fields) {
        $this->closed_message_for_sidebar_widget = strip_tags($fields);
    }
    function getClosedMessageForSidebarWidget() {
        return $this->closed_message_for_sidebar_widget;
    }
    //
    function setShippingEnabled($fields) {
        $fields = preg_replace('/[^a-zA-Z]/', '', $fields);
        $this->shipping_enabled = $fields;
    }
    function getShippingEnabled() {
        return $this->shipping_enabled;
    }
    function setShipping($fields) {
        //print_r($fields);
        for ($l = 0; $l < 5; $l++) {
            //echo 'k0 = ' . $fields[$l][0];
            $fields[$l][0] = $this->toDouble($fields[$l][0]);
            $fields[$l][1] = $this->toDouble($fields[$l][1]);
            if ($fields[$l][2] !== '<' && $fields[$l][2] !== '<=') {
                $fields[$l][2] = '';
            }
            if ($fields[$l][3] !== '<' && $fields[$l][3] !== '<=') {
                $fields[$l][3] = '';
            }
            $fields[$l][4] = $this->toDouble($fields[$l][4]);
        }
        $this->shipping = $fields;
    }
    function getShipping($l, $c) {
        return $this->shipping[$l][$c];
    }
    function getShippingListHtml($l, $c) {
        $gt = $this->shipping[$l][$c];
        
        $h = '<select name="shipping[' . $l . '][' . $c . ']" id="shipping[' . $l . '][' . $c . ']" >';
        $h .= '<option value="">' . __('Select','cfshoppingcart').'</option>';
        if ($gt === '<') { $selected = 'selected="selected"'; } else { $selected = ''; }
        $h .= '<option class="lt" value="<" ' . $selected . '>&lt;</option>';
        if ($gt === '<=') { $selected = 'selected="selected"'; } else { $selected = ''; }
        $h .= '<option class="le" value="<=" ' . $selected . '>&lt;=</option>';
        $h .= '</select>';
return $h;
    }
    
    //
    function setTableTag($fields) {
        $this->table_tag = strip_tags($fields);
    }
    function getTableTag() {
        return $this->table_tag;
    }
    function getTableTagListHtml() {
        $gt = $this->table_tag;
        
        $h = '<select name="table_tag" id="table_tag">';
        if ($gt === 'table') { $selected = 'selected="selected"'; } else { $selected = ''; }
        $h .= '<option class="table" value="table" ' . $selected . '>table</option>';
        if ($gt === 'dl') { $selected = 'selected="selected"'; } else { $selected = ''; }
        $h .= '<option class="dl" value="dl" ' . $selected . '>dl</option>';
        $h .= '</select>';
        return $h;
    }
    
    // stock
    function setNumberOfStockFieldName($fields) {
        $this->number_of_stock_field_name = strip_tags(trim($fields));
    }
    function getNumberOfStockFieldName() {
        return $this->number_of_stock_field_name;
    }
    //
    function setTypeOfShowSoldOutMessage($fields) {
        $this->type_of_show_sold_out_message = strip_tags($fields);
    }
    function getTypeOfShowSoldOutMessage() {
        return $this->type_of_show_sold_out_message;
    }
    function getTypeOfShowSoldOutMessageListHtml() {
        $gt = $this->type_of_show_sold_out_message;
        
        $h = '<select name="type_of_show_sold_out_message" id="type_of_show_sold_out_message" >';
        if ($gt === 'show_sold_out_message') { $selected = 'selected="selected"'; } else { $selected = ''; }
        $h .= '<option class="show_sold_out_message" value="show_sold_out_message" ' . $selected . '>Show sold out message</option>';
        if ($gt === 'dont_show_the_product') { $selected = 'selected="selected"'; } else { $selected = ''; }
        $h .= '<option class="dont_show_the_product" value="dont_show_the_product" ' . $selected . ">Don't show the product</option>";
        $h .= '</select>';
return $h;
    }
    //
    function setSoldOutMessage($fields) {
        $this->sold_out_message = strip_tags(trim($fields));
    }
    function getSoldOutMessage() {
        return $this->sold_out_message;
    }
    
    //
    function setShowCommodityOnHome($fields) {
        $fields = preg_replace('/[^a-zA-Z]/', '', $fields);
        $this->show_commodity_on_home = $fields;
    }
    function getShowCommodityOnHome() {
        return $this->show_commodity_on_home;
    }
    //
    function setShowCommodityOnPage($fields) {
        $fields = preg_replace('/[^a-zA-Z]/', '', $fields);
        $this->show_commodity_on_page = $fields;
    }
    function getShowCommodityOnPage() {
        return $this->show_commodity_on_page;
    }
    //
    function setShowCommodityOnArchive($fields) {
        $fields = preg_replace('/[^a-zA-Z]/', '', $fields);
        $this->show_commodity_on_archive = $fields;
    }
    function getShowCommodityOnArchive() {
        return $this->show_commodity_on_archive;
    }
    //
    function setShowCommodityOnSingle($fields) {
        $fields = preg_replace('/[^a-zA-Z]/', '', $fields);
        $this->show_commodity_on_single = $fields;
    }
    function getShowCommodityOnSingle() {
        return $this->show_commodity_on_single;
    }
    //
    function setShowCommodityOnManually($fields) {
        $fields = preg_replace('/[^a-zA-Z]/', '', $fields);
        $this->show_commodity_on_manually = $fields;
    }
    function getShowCommodityOnManually() {
        return $this->show_commodity_on_manually;
    }
    //
    function setCustomFields($fields) {
        $a = array();
        //$f = split(',', $fields);
        $f = mb_split(',', $fields);
        foreach ($f as $key => $value) {
            $s = strip_tags(trim($value));
            if ($s) array_push($a, $s);
        }
        $this->custom_fields = $a;
    }
    function getCustomFields() {
        $cf = $this->custom_fields;
        if (is_array($cf)) {
            return $cf;
        } else {
            $a = array();
            array_push($a, $cf);
            return $a;
        }
    }
    function getCustomFieldsString() {
        $a = $this->getCustomFields();
        return join(',', $a);
    }
    //
    function setPriceFieldName($field) {
        $this->price_field_name = strip_tags(trim($field));
    }
    function getPriceFieldName() {
        return $this->price_field_name;
    }
    //
    /*
    function setCurrency_before($field) {
        $this->currency_before = $field;
    }
    function getCurrency_before() {
        return $this->currency_before;
    }
    //
    function setCurrency_after($field) {
        $this->currency_after = $field;
    }
    function getCurrency_after() {
        return $this->currency_after;
    }
      */
    //
    function setCurrencyFormat($field) {
        $this->currency_format = strip_tags($field);
    }
    function getCurrencyFormat() {
        return $this->currency_format;
    }
    //
    function setQuantity($field) {
        $this->quantity = strip_tags($field);
    }
    function getQuantity() {
        return $this->quantity;
    }
    //
    function setGoToCartText($field) {
        $this->go_to_cart_text = strip_tags($field);
    }
    function getGoToCartText() {
        return $this->go_to_cart_text;
    }
    //
    function setOrdererInputScreenText($field) {
        $this->orderer_input_screen_text = strip_tags($field);
    }
    function getOrdererInputScreenText() {
        return $this->orderer_input_screen_text;
    }
    //
    function setThanksUrl($field) {
        $this->thanks_url = strip_tags($field);
    }
    function getThanksUrl() {
        return $this->thanks_url;
    }
    //
    function setCartUrl($field) {
        $this->cart_url = strip_tags($field);
    }
    function getCartUrl() {
        return $this->cart_url;
    }
    //
    function setSendOrderUrl($field) {
        $this->send_order_url = strip_tags($field);
    }
    function getSendOrderUrl() {
        return $this->send_order_url;
    }
    //
    function setQfgetthumbOption1($field) {
        $this->qfgetthumb_option_1 = strip_tags($field);
    }
    function getQfgetthumbOption1() {
        return $this->qfgetthumb_option_1;
    }
    //
    function setQfgetthumbDefaultImage($field) {
        $this->qfgetthumb_default_image = strip_tags($field);
    }
    function getQfgetthumbDefaultImage() {
        return $this->qfgetthumb_default_image;
    }
    //
    function setIsUseShipping($field) {
        $this->is_use_shipping = strip_tags($field);
    }
    function getIsUseShipping() {
        return $this->is_use_shipping;
    }
    //
    function setJustAMomentPlease($field) {
        $this->cfshoppingcart_justamomentplease = strip_tags($field);
    }
    function getJustAMomentPlease() {
        return $this->cfshoppingcart_justamomentplease;
    }
    //
    function setMaxQuantityOfOneCommodity($field) {
        $this->max_quantity_of_one_commodity = $this->toDouble($field);
    }
    function getMaxQuantityOfOneCommodity() {
        return $this->max_quantity_of_one_commodity;
    }
    //
    function setMaxQuantityOfTotalOrder($field) {
        $this->max_quantity_of_total_order = $this->toDouble($field);
    }
    function getMaxQuantityOfTotalOrder() {
        return $this->max_quantity_of_total_order;
    }
}

/* main class */
class WpCFShoppingcart {
    var $view;
    var $model;
    var $request;
    var $plugin_name;
    //var $plugin_uri;
    var $plugin_fullpath, $plugin_path, $plugin_folder, $plugin_uri;
    
    // constructor
    function WpCFShoppingcart() {
        /*
        $this->plugin_fullpath = get_plugin_fullpath();
        $this->plugin_path = get_plugin_path();
        $this->plugin_folder = get_plugin_folder();
        $this->plugin_uri = get_plugin_uri();
          */

        $this->plugin_name = 'cfshoppingcart';
        //$this->plugin_uri  = $plugin_uri . '/';
        $this->model = $this->getModelObject();
    }
    
    // create model object
    function getModelObject() {
        $data_clear = 0; // Debug: 1: Be empty to data
        
        // get option from Wordpress
        $option = $this->getWpOption();
        
        //printf("<p>Debug[%s, %s]</p>", strtolower(get_class($option)), strtolower('WpCFShoppingcartModel'));
        
        // Restore the model object if it is registered
        if (strtolower(get_class($option)) === strtolower('WpCFShoppingcartModel') && $data_clear == 0) {
            $model = $option;
        } else {
            // create model instance if it is not registered,
            // register it to Wordpress
            $model = & new WpCFShoppingcartModel();
            $this->addWpOption($model);
        }
        return $model;
    }
    
    function getWpOption() {
        $option = get_option($this->plugin_name);
        
        if(!$option == false) {
            $OptionValue = $option;
        } else {
            $OptionValue = false;
        }
        return $OptionValue;
    }

    /* be add plug-in data to Wordpresss */
    function addWpOption(&$model) {
        $option_description = $this->plugin_name . " Options";
        $OptionValue = $model;
        //print_r($OptionValue);
        add_option(
            $this->plugin_name,
            $OptionValue,
            $option_description);
    }

    /* update plug-in data */
    function updateWpOption(&$OptionValue) {
        $option_description = $this->plugin_name . " Options";
        $OptionValue = $OptionValue;
        //$OptionValue = $this->model;
        
        update_option(
            $this->plugin_name,
            $OptionValue,
            $option_description);
    }
    
    /*
     * management screen header output
     * reading javascript and css
     */
    function addAdminHead() {
        echo '<link type="text/css" rel="stylesheet" href="';
        echo $this->plugin_uri . 'cfshoppingcart.css" />' . "\n";
    }

    function addAdminMenu() {
        add_options_page(
            __('Cf Shopping Cart Options','cfshoppingcart'),
            __('Cf Shopping Cart','cfshoppingcart'),
            8,
            'cfshoppingcart.php',
            array(&$this, 'executeAdmin')
            );
    }

    function executeAdmin() {
        require_once('module/execute_admin.php');
        execute_admin($this);
    }
}
?>
