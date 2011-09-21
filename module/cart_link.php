<?php
/*
 * cart_link.php
 * -*- Encoding: utf8n -*-
 */

/*
 * shortcode: 
 * [cfshoppingcart_cart_link link-string]
 */

function cfshoppingcart_cart_link($args = '') {
    //print_r($args);
    $link_string = $args[0];
    
    // get data object
    global $wpCFShoppingcart;// = new WpCFShoppingcart();
    $model = $wpCFShoppingcart->model;
    //print_r($model);
    if ($is_debug = $model->is_debug()) {
        require_once('debug.php');
        echo debug_cfshoppingcart('called: function cfshoppingcart_cart_link()');
    }
    $cart_url = $model->getCartUrl();
    
    $a = '<a href="' . $cart_url . '" class="cfshoppingcart_cart_url">' . $link_string . '</a>';
    
    return $a;
}


?>
