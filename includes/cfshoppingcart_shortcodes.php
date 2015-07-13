<?php

namespace cfshoppingcart;

function cfshoppingcart_message_shortcode($atts, $content = '') {
    if (!is_array($atts)) {
        $atts = array();
    }
    if (opt::get_option('disable_ajax') && array_key_exists('if_ajax_disabled', $atts)) {
        // ajax enabled
        return;
    }
    if (opt::get_option('disable_ajax')) {
        // ajax enabled
        unset($atts['not_output_if_no_message']);
    }
    $msg = message::get_text();
    $error_count = message::get_error_count();
    $tag = '';

    if (array_key_exists('not_output_if_no_message', $atts) && !$msg) {
        return;
    }

    if ($msg) {
        $classes = 'message';
    } else {
        $classes = '';
    }
    if ($error_count) {
        $tag .= '<div class="' . DOMAIN_CF_SHOPPING_CART . '_message_wrap error ' . $classes . '"><div class="' . DOMAIN_CF_SHOPPING_CART . '_message">' . $msg . '</div></div>';
    } else {
        $tag .= '<div class="' . DOMAIN_CF_SHOPPING_CART . '_message_wrap ' . $classes . '"><div class="' . DOMAIN_CF_SHOPPING_CART . '_message">' . $msg . '</div></div>';
    }
    return $tag;
}

add_shortcode(DOMAIN_CF_SHOPPING_CART . '_message', 'cfshoppingcart\cfshoppingcart_message_shortcode');

function cfshoppingcart_result_shortcode($atts, $content = '') {
    $f = explode('[else]', $content);
    if (message::get_error_count()) {
        return $f[1];
    } else {
        $msg = message::get_text();
        //session_destroy();
        //$_SESSION[DOMAIN_CF_SHOPPING_CART] = array();
        return $f[0];
    }
}

add_shortcode(DOMAIN_CF_SHOPPING_CART . '_result', 'cfshoppingcart\cfshoppingcart_result_shortcode');

function cfshoppingcart_cart_shortcode($atts, $content = '') {
    $atts['enable_form'] = 1;

    if (!array_key_exists('class', $atts)) {
        $atts['class'] = 'cfshoppingcart_cart';
    }
    $atts['shortcode_type'] = 'cart';
    return cfshoppingcart_shortcode($atts, $content);
}

add_shortcode(DOMAIN_CF_SHOPPING_CART . '_cart', 'cfshoppingcart\cfshoppingcart_cart_shortcode');

/**
 * 
 * [cfshoppingcart_cart_info class="class_name" type="table|dl" enable_form=1 product_title="<caption>Caption</caption>" total_title="<caption>Caption</caption>" product_fileds="Product ID,Name,Color,Price,Stock,Quantity" total_fileds="Gross Number,Total Price"]
 * Empty message
 * [/cfshoppingcart_cart_info]
 * 
 * @param type $atts
 * @param type $content
 * @return string
 */
function cfshoppingcart_widget_shortcode($atts, $content = '') {
    $atts['enable_form'] = false;

    if (!array_key_exists('class', $atts)) {
        $atts['class'] = 'cfshoppingcart_widget';
    }
    $atts['shortcode_type'] = 'widget';
    return cfshoppingcart_shortcode($atts, $content);
}

add_shortcode(DOMAIN_CF_SHOPPING_CART . '_widget', 'cfshoppingcart\cfshoppingcart_widget_shortcode');

function cfshoppingcart_check_out_shortcode($atts, $content = '') {
    $atts['enable_form'] = false;

    if (!array_key_exists('class', $atts)) {
        $atts['class'] = 'cfshoppingcart_check_out';
    }
    $atts['shortcode_type'] = 'check_out';
    return cfshoppingcart_shortcode($atts, $content);
}

add_shortcode(DOMAIN_CF_SHOPPING_CART . '_check_out', 'cfshoppingcart\cfshoppingcart_check_out_shortcode');


/* */

function cfshoppingcart_reset_cart_link($atts, $content = '') {
    extract(shortcode_atts(array(
        'text' => __('Reset Cart', DOMAIN_CF_SHOPPING_CART),
        'url_only' => ''
                    ), $atts));

    $url = get_bloginfo('url') . '/?cfshoppingcart_destory=1';
    if ($url_only) {
        return $url;
    } else {
        return sprintf("<a href=\"%s\">%s</a>", esc_url($url), esc_html($text));
    }
}

add_shortcode(DOMAIN_CF_SHOPPING_CART . '_reset_cart_link', 'cfshoppingcart\cfshoppingcart_reset_cart_link');

function cfshoppingcart_cart_link($atts, $content = '') {
    extract(shortcode_atts(array(
        'text' => __('Cart', DOMAIN_CF_SHOPPING_CART),
        'url_only' => ''
                    ), $atts));

    $url = get_permalink(opt::get_option('cart_screen_post_id'));
    if ($url_only) {
        return $url;
    } else {
        return sprintf("<a href=\"%s\">%s</a>", esc_url($url), esc_html($text));
    }
}

add_shortcode(DOMAIN_CF_SHOPPING_CART . '_cart_link', 'cfshoppingcart\cfshoppingcart_cart_link');

function cfshoppingcart_check_out_link($atts, $content = '') {
    extract(shortcode_atts(array(
        'text' => __('Check Out', DOMAIN_CF_SHOPPING_CART),
        'url_only' => ''
                    ), $atts));

    $url = get_permalink(opt::get_option('check_out_screen_post_id'));
    if ($url_only) {
        return $url;
    } else {
        return sprintf("<a href=\"%s\">%s</a>", esc_url($url), esc_html($text));
    }
}

add_shortcode(DOMAIN_CF_SHOPPING_CART . '_check_out_link', 'cfshoppingcart\cfshoppingcart_check_out_link');

