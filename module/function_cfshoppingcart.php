<?php
/*
 * function_cfshoppingcart.php
 * -*- Encoding: utf8n -*-
 */

//require_once('common.php');
require_once('cart.php');
require_once('contact-form-7.php');

function cfshoppingcart($args = '') {//($get_post_custom){
    global $cfshoppingcart_stat;
    if ($cfshoppingcart_stat === 'cart_page') return;

    $is_change = 0; /* CONFIGURATION - 1:change or 0:add */

    global $post;
    $get_post_custom = get_post_custom();

    /*
    if ($is_change) {
        //if (!session_id()){ @session_start(); }
    }
      */

    /*
    $plugin_fullpath = get_plugin_fullpath();
    $plugin_path = get_plugin_path();
    $plugin_folder = get_plugin_folder();
    $plugin_uri = get_plugin_uri();
      */
    //echo "<p>current_path = $plugin_folder, $plugin_path</p>";
    
    // get data object
    $WpCFShoppingcart = & new WpCFShoppingcart();
    $model = $WpCFShoppingcart->model;
    //print_r($model);
    if ($is_debug = $model->is_debug()) {
        require_once('debug.php');
        echo debug_cfshoppingcart('called: function cfshoppingcart()');
    }
    
    $rf = 1;
    if ($args === 'setting') {
        if ($model->getShowCommodityOnHome() && is_home()) $rf = 0;
        if ($model->getShowCommodityOnPage() && is_page()) $rf = 0;
        if ($model->getShowCommodityOnArchive() && is_archive()) $rf = 0;
        if ($model->getShowCommodityOnSingle() && is_single()) $rf = 0;
        if ($rf) return;
    } else {
        if ($model->getShowCommodityOnManually()) $rf = 0;
        if ($rf) return;
    }
    
    $price_field_name = $model->getPriceFieldName();
    $custom_fields = $model->getCustomFields();
    $currency_format = $model->getCurrencyFormat();
    $quantity_str = $model->getQuantity();
    //print_r($custom_fields);
    //trim($custom_fields);
    //print "[$price_field_name]";
    
    //global $post;
    $id = $post->ID;
    if ($is_change) {
        $commodities = $_SESSION['cfshoppingcart']['commodities'];
        $quantity = $commodities[$id]['quantity'];
        if (!$quantity) $quantity = 1;
    }
    
    //$custom_fields = array('メーカー', '品番', '単価', 'シリーズ名', '年代');
    $c = $get_post_custom;
    if ($is_debug) {
        debug_cfshoppingcart('function get_post_custom() return is');
        print_r($c);
    }
    if (!$c[$price_field_name]) {
        if ($is_debug) {
            debug_cfshoppingcart('price_field_name not found in Custom Field on this post. return function.');
        }
        return; // 単価が無い
    }

    if ($is_debug) {
        debug_cfshoppingcart('custom_fields is');
        print_r($custom_fields);
    }
    $content .= '<div class="cfshoppingcart_commodity"><table>';
    foreach ($custom_fields as $key => $value) {
        //print "[$value]";
        $value = trim($value);
        if ($value === $price_field_name) {
            $c[$value][0] = sprintf($currency_format, $c[$value][0]);
        }
        $content .= '<tr><td>' . $value . '</td><td>' . $c[$value][0] . '</td></tr>';
    }
    $content .= '</table></div>';
    
    $content .= '<div class="cfshoppingcart_commodity_op"><span>';
    if ($is_change) {
        $content .= __('Quantity','cfshoppingcart') . ' <input class="cfshoppingcart_quantity_' . $id . '" type="text" value="' . $quantity . '" /> ' . $quantity_str . ' ';
        //$content .= __('Quantity','cfshoppingcart') . ' <input class="cfshoppingcart_quantity_' . $id . '" type="text" value="1" /> ' . $quantity_str . ' (' . __('In cart is ','cfshoppingcart') . ' ' . $quantity . ') ';
        $content .= '</span>';
        $content .= '<input class="change_quantity_button" type="button" name="id=' . $id . '" value="' . __('Into Cart','cfshoppingcart') . '" />';
        //$content .= '<input class="change_quantity_button" type="button" name="id=' . $id . '" value="' . __('Change quantity','cfshoppingcart') . '" />';
    } else {
        $content .= __('Quantity','cfshoppingcart') . ' <input class="cfshoppingcart_quantity_' . $id . '" type="text" value="1" /> ' . $quantity_str . ' ';
        $content .= '</span>';
        $content .= '<input class="add_to_cart_button" type="button" name="id=' . $id . '" value="' . __('Add to Cart','cfshoppingcart') . '" />';
    }
    $content .= '</div><!-- /cfshoppingcart_commodity_op -->';

    if ($model->getShowCommodityOnManually()) {
        echo $content;
    } else {
        return $content;
    }
}

?>
