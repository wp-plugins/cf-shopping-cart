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
    $send_order_url = $model->getSendOrderUrl();
    
    $a = '<a href="' . $send_order_url . '" class="cfshoppingcart_send_order_url">' . $link_string . '</a>';
    
    return $a;
}


?>
