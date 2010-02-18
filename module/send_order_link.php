<?php
/*
 * send_order_link.php
 * -*- Encoding: utf8n -*-
 */

/*
 * shortcode: 
 * [cfshoppingcart_send_order_link link-string]
 */

//require_once('sum.php');

function cfshoppingcart_send_order_link($args = '') {
    //print_r($args);
    $link_string = $args[0];
    
    // get data object
    $WpCFShoppingcart = & new WpCFShoppingcart();
    $model = $WpCFShoppingcart->model;
    //print_r($model);
    if ($is_debug = $model->is_debug()) {
        require_once('debug.php');
        echo debug_cfshoppingcart('called: function cfshoppingcart_cart_link()');
    }
    //echo 'is_debug = ' . $is_debug;
    /*
    $price_field_name = $model->getPriceFieldName();
    $custom_fields = $model->getCustomFields();
    $currency_format = $model->getCurrencyFormat();
    $quantity = $model->getQuantity();
    */
    //$cart_url = $model->getCartUrl();
    $send_order_url = $model->getSendOrderUrl();
    /*
    $qfgetthumb_option_1 = $model->getQfgetthumbOption1();
    $qfgetthumb_default_image = $model->getQfgetthumbDefaultImage();
    $is_use_shipping = $model->getIsUseShipping();
    
    $commodities = $_SESSION['cfshoppingcart']['commodities'];
    $sum = $_SESSION['cfshoppingcart']['sum'];
      */

    $a = '<a href="' . $send_order_url . '" class="cfshoppingcart_send_order_url">' . $link_string . '</a>';
    
    return $a;
}


?>
