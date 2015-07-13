<?php

namespace cfshoppingcart;

/**
 * 
 * [cfshoppingcart_shortcode class="class_name" type="table|dl" enable_form=1 product_title="<caption>Caption</caption>" total_title="<caption>Caption</caption>" product_fileds="Product ID,Name,Color,Price,Stock,Quantity" total_fileds="Gross Number,Total Price"]
 * Empty message
 * [/cfshoppingcart_cart_info]
 * 
 * @param type $atts
 * @param type $content
 * @return string
 */
function cfshoppingcart_shortcode($atts = array(), $content = '') {
    $tag = '';

    //print_r($_SESSION[DOMAIN_CF_SHOPPING_CART]['_POST']);
    if (getPostValue('submitConfirm', true)) {
        //echo '<p>Confirm Screen</p>';
        $tag .= '<input type="hidden" name="cmd" value="check_out" />';
    }

    $serial = new serial();
    $cart = $serial->load();
    $products = &$cart->items;
    $cart->calc();

    $emails = cfshoppingcart_get_email_content($cart);
    $email_admin = $emails['admin'];
    $email_customer = $emails['customer'];
    //echo '<pre>' . $email_admin . '</pre>';
    //echo '<pre>' . $email_customer . '</pre>';

    extract(shortcode_atts(array(
        'shortcode_type' => 'widget',
        'class' => '',
        'product_title' => '',
        'product_fileds' => '',
        'total_title' => '',
        'total_fileds' => '',
        'type' => 'table',
        'enable_form' => ''
                    ), $atts));

    if (!$class) {
        $class = DOMAIN_CF_SHOPPING_CART . '_' . $shortcode_type;
    }

    $_SESSION[DOMAIN_CF_SHOPPING_CART]['shortcodes'][$class]['atts'] = $atts;
    $_SESSION[DOMAIN_CF_SHOPPING_CART]['shortcodes'][$class]['content'] = $content;

    if (!$products) {
        return '<div class="' . DOMAIN_CF_SHOPPING_CART . ' shortcode ' . esc_attr($class) . '">' . $content . '</div>';
    }

    $tt = get_table_tag_array($type);

    if ($product_fileds) {
        $product_fileds = explode(',', $product_fileds);
        foreach ($product_fileds as $i => $field) {
            $product_fileds[$i] = trim($field);
        }
    } else {
        $product_fileds = opt::get_option('custom_field_names_array');
    }

    if ($total_fileds) {
        $total_fileds = explode(',', $total_fileds);
        foreach ($total_fileds as $i => $field) {
            $total_fileds[$i] = trim($field);
        }
    } else {
        $total_fileds = array(opt::get_option('gross_number_text'), opt::get_option('product_total_text'), opt::get_option('shipping_text'), opt::get_option('total_price_text'));
    }

    switch ($shortcode_type) {
        case 'cart':
            array_remove_value(opt::get_option('stock_quantity_field_name'), $product_fileds);
            break;
        case 'check_out':
            //echo 'check out';
            //add_mw_wp_form_validation_rule();
            array_remove_value(opt::get_option('stock_quantity_field_name'), $product_fileds);
            break;
        case 'widget':
            array_remove_value(opt::get_option('stock_quantity_field_name'), $product_fileds);
            break;
        default :
            break;
    }

    $tag .= '<div class="products">';
    if (getPostValue('submitConfirm', true)) {
        // confirm screen, just before send.
        $shortcode_type = $shortcode_type . '-confirm';
    }
    foreach ($products as $id => $product) {
        $tag .= '<div class="product">';
        if ($enable_form) {
            $tag .= '<form name="' . DOMAIN_CF_SHOPPING_CART . '" method="post">';
            $tag .= get_wp_nonce_field();
            $tag .= '<input type="hidden" name="action" value="cfshoppingcart_ajax" />';
            $tag .= '<input type="hidden" name="product_id" value="' . $product->product_id . '" />';
            $tag .= '<input type="hidden" name="product_key" value="' . name_encode($id, CF_SHOPPING_CART_PRODUCT_KEY_PREFIX) . '" />';
        }
        $tag .= '<input type="hidden" name="' . DOMAIN_CF_SHOPPING_CART . '-screen" value="' . $shortcode_type . '" />';

        $tag_product = '';
        $tag_product .= $tt['tableA'];
        $tag_product .= $product_title;

        $tag_product .= $tt['tbodyA'];
        foreach ($product_fileds as $field) {
            $value = get_cf($product->product_id, $field);
            switch ($field) {
                case opt::get_option('field_name_of_link_to_product_page'):
                    $value = sprintf("<a href=\"%s\">%s</a>", get_permalink($product->product_id, false), esc_html($value));
                    break;
                case opt::get_option('price_field_name'):
                    $value = esc_html(sprintf(opt::get_option('currency_format'), $product->price));
                    break;
                case opt::get_option('subtotal_text'):
                    $value = esc_html(sprintf(opt::get_option('currency_format'), $product->subtotal));
                    break;
                case opt::get_option('quantity_text'):
                    if ($enable_form) {
                        $quantity_input_type = get_quantity_input_type($product->product_id, $product->quantity);
                        if (!$quantity_input_type['type']) {
                            $value = '<span class="quantity">' . esc_html($product->quantity) . '</span> <button class="' . DOMAIN_CF_SHOPPING_CART . '" type="submit" name="cmd" value="quantity_minus">-</button> <button class="' . DOMAIN_CF_SHOPPING_CART . '" type="submit" name="cmd" value="quantity_plus">+</button>';
                        } else if ($quantity_input_type['type'] == 'text' || $quantity_input_type['type'] == 'number') {
                            $value = '<input type="' . $quantity_input_type['type'] . '" class="quantity ' . DOMAIN_CF_SHOPPING_CART . '" name="quantity" value="' . $product->quantity . '" />';
                            $value .= ' <button type="submit" class="change_quantity ' . DOMAIN_CF_SHOPPING_CART . '" name="cmd" value="change_quantity">' . opt::get_option('change_quantity_button_text') . '</button>';
                        } else if ($quantity_input_type['type'] == 'select') {
                            $value = $quantity_input_type['value'];
                            $value .= ' <button type="submit" class="change_quantity ' . DOMAIN_CF_SHOPPING_CART . '" name="cmd" value="change_quantity">' . opt::get_option('change_quantity_button_text') . '</button>';
                        }
                    } else {
                        $value = esc_html($product->quantity);
                    }
                    break;
                default:
                    if (array_key_exists($field, $product->options)) {
                        $value = $product->options[$field];
                    } else {
                        $value = esc_html($value);
                    }
                    if (!opt::get_option('show_empty_field') && !$value) {
                        continue 2;
                    }
                    break;
            } // switch
            $tag_product .= $tt['trA'] . $tt['thA'] . esc_html($field) . $tt['thB'] . $tt['tdA'] . $value . $tt['tdB'] . $tt['trB'];
        } // foreach
        $tag_product .= $tt['tbodyB'];
        $tag_product .= $tt['tableB'];

        $tag .= apply_filters(DOMAIN_CF_SHOPPING_CART . '_filter_' . $shortcode_type, $tag_product, $product->product_id);
        if ($enable_form) {
            $tag .= '</form>';
        }
        $tag .= '</div><!-- .product -->';
    } // foreach
    $tag .= '</div><!-- .products -->';

    $tag .= '<div class="' . esc_attr($class) . ' total">';
    $tag .= $tt['tableA'];
    $tag .= $total_title;
    $tag .= $tt['tbodyA'];

    foreach ($total_fileds as $field) {
        switch ($field) {
            case opt::get_option('gross_number_text'):
                $value = $cart->sum->gross;
                break;
            case opt::get_option('product_total_text'):
                $value = sprintf(opt::get_option('currency_format'), $cart->sum->total);
                break;
            case opt::get_option('shipping_text'):
                $value = sprintf(opt::get_option('currency_format'), $cart->sum->shipping);
                break;
            case opt::get_option('total_price_text'):
                $value = sprintf(opt::get_option('currency_format'), $cart->sum->inclusive_sum);
                break;
            default:
                continue 2;
                break;
        }
        $tag .= $tt['trA'] . $tt['thA'] . esc_html($field) . $tt['thB'] . $tt['tdA'] . esc_html($value) . $tt['tdB'] . $tt['trB'];
    }
    $tag .= $tt['tbodyB'];
    $tag .= $tt['tableB'];
    if ($enable_form) {
        $tag .= '<a name="goto_check_out" href="' . esc_url(get_permalink(opt::get_option('check_out_screen_post_id'))) . '"><button type="button">' . esc_html(opt::get_option('check_out_link_text')) . '</button></a>';
    }
    $tag .= '</div><!-- .total -->';
    $tag .= '<input type="hidden" name="' . DOMAIN_CF_SHOPPING_CART . '_check_out" value="' . $email_admin . '" />';
    $tag .= '<input type="hidden" name="' . DOMAIN_CF_SHOPPING_CART . '_email_admin" value="' . $email_admin . '" />';
    $tag .= '<input type="hidden" name="' . DOMAIN_CF_SHOPPING_CART . '_email_customer" value="' . $email_customer . '" />';
    //$tag .= '<div class="mw-wp-form_file"><a href="http://cfshoppingcart.silverpigeon.jp/wp-content/uploads/2015/06/caro.jpg" target="_blank">アップロードしました。</a></div><input type="hidden" name="file" value="http://cfshoppingcart.silverpigeon.jp/wp-content/uploads/2015/06/caro.jpg" /><input type="hidden" name="mwf_upload_files[]" value="file" />';

    return '<div class="' . DOMAIN_CF_SHOPPING_CART . ' shortcode ' . esc_attr($class) . '">' . $tag . '</div>';
}

function cfshoppingcart_get_email_content($cart) {

    $products = &$cart->items;
    $cart->calc();

    if (!$products) {
        return;
    }

    $product_fileds = opt::get_option('custom_field_names_array');
    $stock_quantity_field_name = opt::get_option('stock_quantity_field_name');
    //$key_stock = array_search($stock_quantity_field_name, $product_fileds);
    //$key_stock = opt::get_option('stock_quantity_field_name');



    $total_fileds = array(opt::get_option('gross_number_text'), opt::get_option('product_total_text'), opt::get_option('shipping_text'), opt::get_option('total_price_text'));

    $email_field_separator = opt::get_option('email_field_separator');
    $separator = "\t";
    if ($email_field_separator == 'comma') {
        $separator = ",";
    }

    $email_admin = '#' . $separator . "Cf Shopping Cart\n";
    $email_admin .= '#' . $separator . join($separator, $product_fileds) . "\n";
    $email_customer = '';

    $count = 1;
    $count2 = 1;
    foreach ($products as $id => $product) {
        $email_admin .= $count . $separator;
        $email_customer .= "-- " . __('Order Product', DOMAIN_CF_SHOPPING_CART) . " [" . $count2++ . "] --\n";
        foreach ($product_fileds as $field) {
            $value = get_cf($product->product_id, $field);

            switch ($field) {
                case opt::get_option('price_field_name'):
                    $value = $product->price;
                    break;
                case opt::get_option('stock_quantity_field_name'):
                    $stock_array = get_stock($product->product_key);
                    $value = $stock_array['stock'];
                    break;
                case opt::get_option('subtotal_text'):
                    $value = $product->subtotal;
                    break;
                case opt::get_option('quantity_text'):
                    $value = $product->quantity;
                    break;
                default:
                    if (array_key_exists($field, $product->options)) {
                        $value = $product->options[$field];
                    }
                    break;
            } // switch
            if ($email_field_separator == 'comma') {
                $value = str_replace(',', '\\,', $value);
            }
            $email_admin .= $value . $separator;
            if ($field !== $stock_quantity_field_name) {
                $email_customer .= $field . ': ' . $value . "\n";
            }
        } // foreach
        $email_admin = trim($email_admin) . "\n";
        $count++;
    } // foreach
    // total 
    $email_admin .= '#' . $separator . join($separator, $total_fileds) . "\n";
    $email_customer .= "-- " . __('Total', DOMAIN_CF_SHOPPING_CART) . " --\n";
    $count = 1;
    foreach ($total_fileds as $field) {
        switch ($field) {
            case opt::get_option('gross_number_text'):
                $value = $cart->sum->gross;
                break;
            case opt::get_option('product_total_text'):
                $value = $cart->sum->total;
                break;
            case opt::get_option('shipping_text'):
                $value = $cart->sum->shipping;
                break;
            case opt::get_option('total_price_text'):
                $value = $cart->sum->inclusive_sum;
                break;
            default:
                continue 2;
                break;
        }
        $email_admin .= $count++ . $separator . $value . $separator;
        $email_customer .= $field . ': ' . $value . "\n";
    }
    $email_admin = trim($email_admin) . "\n";
    $email_admin .= '#' . $separator . "End of Cf Shopping Cart\n";

    return array('admin' => $email_admin, 'customer' => $email_customer);
}
