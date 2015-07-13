<?php

namespace cfshoppingcart;

function the_content($content) {
    //echo "<p>the_content</p>";
    //return;
    global $post;

    if (is_feed()) {
        return $content;
    }

    if (opt::get_option('closed_shop')) {
        return $content;
    }

    if (!is_product_category_member($post->ID)) {
        return $content;
    }

    if ((is_category() || is_archive()) && !opt::get_option('show_product_category')) {
        return $content;
    } else if ((is_single() || is_singular()) && !opt::get_option('show_product_single')) {
        return $content;
    } else if (is_page() && !opt::get_option('show_product_page')) {
        return $content;
    } else if (is_home() && !opt::get_option('show_product_home')) {
        return $content;
    }


    /* */

    $custom_field_names_array = opt::get_option('custom_field_names_array');
    if (!is_array($custom_field_names_array)) {
        // custom field is nempty.
        return $content;
    }
    if (to_int(get_cf($post->ID, opt::get_option('stock_quantity_field_name'))) == 0 && opt::get_option('in_the_case_of_sold_out') == 'not_to_be_product') {
        // stock is empty
        return $content;
    }

    $tag = '<form class="' . DOMAIN_CF_SHOPPING_CART . '" name="' . DOMAIN_CF_SHOPPING_CART . '" method="post">';
    $tag .= get_wp_nonce_field();
    $tag .= '<input type="hidden" name="action" value="cfshoppingcart_ajax" />';
    $tag .= '<table><tbody>';
    //$stock = get_stock($post->ID);
    $stock_tag = get_stock_tag($post->ID);
    foreach ($custom_field_names_array as $name) {
        $value = get_cf($post->ID, $name);

        switch ($name) {
            case opt::get_option('quantity_text'):
            case opt::get_option('subtotal_text'):
                continue 2;
            case opt::get_option('stock_quantity_field_name'):
                if ($stock_tag) {
                    $value = $stock_tag;
                } else {
                    $value = 0;
                }
                break;
            case opt::get_option('field_name_of_link_to_product_page'):
                if (!$value) {
                    // product name is empty.
                    return $content;
                }
                $value = sprintf("<a href=\"%s\">%s</a>", get_permalink($post->ID), $value);
                break;
            case opt::get_option('price_field_name'):
                //if (!$stock_tag && $stock == 0 && opt::get_option('in_the_case_of_sold_out') == 'sold_out_message_on_price') {
                if (!$stock_tag && opt::get_option('in_the_case_of_sold_out') == 'sold_out_message_on_price') {
                    $value = opt::get_option('sold_out_message');
                } else {
                    $value = sprintf(opt::get_option('currency_format'), $value);
                }
                break;
            default :
                $value = get_html_select($name, $value);
                if (!opt::get_option('show_empty_field') && !$value) {
                    continue 2;
                }
                break;
        }
        $tag .= sprintf("<tr><th>%s</th><td>%s</td></tr>", $name, $value);
    }
    $tag .= '</tbody></table>';
    //if ($stock < 0 || $stock > 0 || $stock_tag) {
    if ($stock_tag) {
        $tag .= '<input type="hidden" name="product_id" value="' . $post->ID . '" />';

        $quantity = to_int(getPostValue('quantity'));
        /*
        if (!$quantity || $quantity < 1) {
            $quantity = 1;
        }
         */
        $quantity_input_type = get_quantity_input_type($post->ID, $quantity);
        if (!$quantity_input_type['type']) {
            $tag .= '<input type="hidden" class="quantity ' . DOMAIN_CF_SHOPPING_CART . '" name="quantity" value="1" />';
            $tag .= ' <button type="submit" class="add_to_cart ' . DOMAIN_CF_SHOPPING_CART . '" name="cmd" value="add_to_cart">' . opt::get_option('add_to_cart_button_text') . '</button>';
        } else if ($quantity_input_type['type'] == 'text' || $quantity_input_type['type'] == 'number') {
            if (!$quantity || $quantity < 1) {
                $quantity = 1;
            }
            $tag .= '<input type="'.$quantity_input_type['type'].'" class="quantity ' . DOMAIN_CF_SHOPPING_CART . '" name="quantity" value="' . $quantity . '" />';
            $tag .= ' <button type="submit" class="add_to_cart ' . DOMAIN_CF_SHOPPING_CART . '" name="cmd" value="change_quantity">' . opt::get_option('change_quantity_button_text') . '</button>';
        } else if ($quantity_input_type['type'] == 'select') {
            $tag .= $quantity_input_type['value'];
            $tag .= ' <button type="submit" class="add_to_cart ' . DOMAIN_CF_SHOPPING_CART . '" name="cmd" value="change_quantity">' . opt::get_option('change_quantity_button_text') . '</button>';
        }
    }
    $tag .= '</form>';

    $tag = apply_filters(DOMAIN_CF_SHOPPING_CART . '_filter_the_content', $tag, $post->ID);
    return '<div class="' . DOMAIN_CF_SHOPPING_CART . ' product post_id-' . $post->ID . '">' . $content . $tag . '</div>';
}

add_filter('the_content', 'cfshoppingcart\the_content');



function get_html_select($name, $value) {
    if (!preg_match('/^#select/', $value)) {
        return $value;
    }
    return format_custom_field_value_to_select($name, $value, getPostValue(name_encode($name, CF_SHOPPING_CART_OPTION_KEY_PREFIX)));
}

function format_custom_field_value_to_select($name, $value, $default_value = null) {
    $array = explode("\n", $value);
    $tag = '';
    foreach ($array as $index => $line) {
        if ($index == 0) {
            continue;
        }
        $line = trim($line);
        if (!$line) {
            continue;
        }

        $f = explode("|", $line, 2);
        if ($default_value && $f[0] == $default_value) {
            $selected = 'selected';
        } else {
            $selected = '';
        }
        $tag .= '<option value="' . $f[0] . '" ' . $selected . '>' . $f[0] . '</option>';
    }
    if (!$tag) {
        return '';
    }
    $tag = '<select name="' . name_encode($name, CF_SHOPPING_CART_OPTION_KEY_PREFIX) . '">' . $tag . '</select>';
    return $tag;
}

function is_product_category_member($post_id) {
    $category_of_product = opt::get_option('category_of_product');

    if (!$category_of_product) {
        return false;
    }

    $cates = get_the_category($post_id);
    foreach ($cates as $cate) {

        if ($cate->cat_ID == $category_of_product) {
            return true;
        }
    }
    return false;
}
