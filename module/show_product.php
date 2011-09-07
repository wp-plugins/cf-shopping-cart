<?php
/*
 * show_product.php
 * -*- Encoding: utf8n -*-
 */

//require_once('common.php');
require_once('function_cfshoppingcart.php');
//require_once('cart.php');
//require_once('contact-form-7.php');

function show_product($content) {//($get_post_custom){
    //global $WpCFShoppingcart;
    //$model = $WpCFShoppingcart->model;
    //global $cfshoppingcart_common;
    //$is_debug = $model->is_debug();
    //global $cfshoppingcart_common;
    //global $cfshoppingcart_stat;
    //if ($cfshoppingcart_stat === 'cart_page') return;

    //global $post;
    //$get_post_custom = get_post_custom();

    if (is_feed()) return;
    
    /*
    if ($cfshoppingcart_common->use_the_content_instead_of_the_excerpt()) {
        add_action('the_excerpt', 'cfshoppingcart_use_the_content_instead_of_the_excerpt_hook');
    }
      */
    //add_action('the_excerpt', 'cfshoppingcart_use_the_content_instead_of_the_excerpt_hook');
    //if ($cfshoppingcart_stat === 'cart_page') return $content;
    //if (strstr($content, '[cfshoppingcart_cart')) { return $content; }
    //if (strstr($content, '[contact-form')) { return $content; }
    $content .= cfshoppingcart('setting');
    //remove_action('the_excerpt');
    return $content;
}

?>
