<?php
/*
 * send_order_link.php
 * -*- Encoding: utf8n -*-
 */

/*
 * shortcode: 
 * [cfshoppingcart_checkout_link link-string]
 */

//require_once('sum.php');

function cfshoppingcart_checkout_link($args = '', $content = '') {
    //print_r($args);
    
    // get data object
    global $wpCFShoppingcart;// = new WpCFShoppingcart();
    $model = $wpCFShoppingcart->model;
    //print_r($model);
    $a = '';
    
    if ($args) {
        $link_string = $args[0];
        if ($is_debug = $model->is_debug()) {
            require_once('debug.php');
            echo debug_cfshoppingcart('called: function cfshoppingcart_cart_link()');
        }
        $send_order_url = $model->getSendOrderUrl();
        
        $a .= '<a href="' . $send_order_url . '" class="cfshoppingcart_send_order_url">' . $link_string . '</a>';
    }
    if ($content) {
        $a .= '<a href="' . $send_order_url . '" class="cfshoppingcart_send_order_url">' . $content . '</a>';
    }
    return $a;
}


?>
