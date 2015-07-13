<?php

namespace cfshoppingcart;

function check_out($content) {
    global $post;

    $serial = new serial();
    $session = $serial->load();
    $products = &$session->items;

    if ($post->ID == opt::get_option('check_out_screen_post_id') && !$products) {
        $content = opt::get_option('check_out_page_content_if_cart_is_empty');
        return $content;
    }

    return $content;
}

add_filter('the_content', 'cfshoppingcart\check_out', 65535);

