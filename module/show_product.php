<?php
/*
 * show_product.php
 * -*- Encoding: utf8n -*-
 */

require_once('function_cfshoppingcart.php');

function show_product($content) {
    if (is_feed()) return;

    $keyword = '_CFSHOPPINGCART_PRODUCT_IS_HERE_';
    $cart = cfshoppingcart('setting');
    if (strstr($content, $keyword)) {
        return str_replace($keyword, $cart, $content);
    } else {
        return $content . $cart;
    }
}

?>
