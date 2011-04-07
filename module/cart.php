<?php
/*
 * cart.php
 * -*- Encoding: utf8n -*-
 */

/* Enable Exec-PHP plugin.
 * New page 'Shopping cart'.
 * Write content:
 *   <?php cfshoppingcart_cart(); ?>
 */

require_once('sum.php');

function cfshoppingcart_cart($args = '') {
    //echo '_SESSION = '; print_r($_SESSION);
    //echo '_SESSION_cfshoppingcart_commodities = '; print_r($_SESSION['cfshoppingcart']['commodities']);
    //unset($_SESSION['cfshoppingcart']);
    //if (!session_id()){ @session_start(); }

    global $cfshoppingcart_stat;
    $cfshoppingcart_stat = 'cart_page';
    // get data object
    global $WpCFShoppingcart;// = /* php4_110323 & new */ new WpCFShoppingcart();
    $model = $WpCFShoppingcart->model;

    global $cfshoppingcart_common;
    //require_once('common.php');
    //$cfshoppingcart_common = /* php4_110323 & new */ new cfshoppingcart_common();

    if (is_array($args)) {
        $is_shortcode = true;
        $is_debug = $model->is_debug();
    } else {
        $is_shortcode = false;
    }
    
    //print_r($model);
    if ($is_debug) {
        require_once('debug.php');
        //echo debug_cfshoppingcart('called: function cfshoppingcart_cart()');
    }
    $price_field_name = $model->getPriceFieldName();
    $custom_fields = $model->getCustomFields();
    $currency_format = $model->getCurrencyFormat();
    $quantity = $model->getQuantity();
    $cart_url = $model->getCartUrl();
    $send_order_url = $model->getSendOrderUrl();
    $qfgetthumb_option_1 = $model->getQfgetthumbOption1();
    $qfgetthumb_default_image = $model->getQfgetthumbDefaultImage();
    //$is_use_shipping = $model->getIsUseShipping();
    $number_of_stock_field_name = $model->getNumberOfStockFieldName();
    $table_tag = $model->getTableTag();
    $link_to_product_field_name = $model->getLinkToProductFieldName();
    $open_product_link_to_another_window = $model->getOpenProductLinkToAnotherWindow();
    
    $commodities = $_SESSION['cfshoppingcart']['commodities'];
    $sum = $_SESSION['cfshoppingcart']['sum'];

    // shop now closed
    $current_user = wp_get_current_user();
    if ($model->getShopNowClosed() && $current_user->user_level < $model->getShopNowClosedUserLevel()) {
        $content = '<span class="shop_now_closed"></span>';
        if ($is_shortcode) return $content;
        else echo $content;
        return;
    }
    // zero
    if ($sum['quantity_of_commodity'] == 0 || !$commodities) {
        $content = '<span class="cart_empty">' . __('Shopping Cart is empty.', 'cfshoppingcart') . '</span>';
        if ($is_shortcode) return $content;
        else echo $content;
        return;
    }

    $content = '';
    $order_is_more_than_stock = false;
    foreach ($commodities as $postid => $commodity) {
        if (!$postid) continue;
        list($post_id, $stock_key) = $cfshoppingcart_common->get_real_postid_and_stock_key($postid);
        //$cf = get_post_custom($post_id);
        $cf = $cfshoppingcart_common->get_custom_fields($post_id);
        
        // form start ****************************************************
        $content .= '<div class="cfshoppingcart_cart_product_wrap">';
        $content .= '<form class="cfshoppingcart_in_cart_product_id_x" name="cfshoppingcart_in_cart_product_id_x" method="post" action="">';
        $content .= '<input type="hidden" class="wp_cfshoppingcart" name="wp_cfshoppingcart" value="cfshoppingcart" />';
        $content .= '<input type="hidden" name="include" value="' . $postid . '" />';
        // put no ajax message
        $content .= $cfshoppingcart_common->get_no_ajax_message();
        if (function_exists('the_qf_get_thumb_one')) {
            //$img = '<td rowspan="' . (count($custom_fields)+2) . '">' . get_commodity_img($postid, $qfgetthumb_option_1, $qfgetthumb_default_image) . '</td>';
            $img = get_commodity_img($postid, $qfgetthumb_option_1, $qfgetthumb_default_image);
        } else {
            $img = '';
        }
        $content .= $img;
        //$content .= '<table border="1">';
        if ($table_tag == 'table') {
            $content .= '<table  class="cfshoppingcart_in_cart_product_table">';
            $trth = '<tr><th>';
            $thtd = '</th><td>';
            $tdtr = '</td></tr>';
        } else {
            $content .= '<dl>';
            $trth = '<dt>';
            $thtd = '</dt><dd>';
            $tdtr = '</dd>';
        }
        foreach ($custom_fields as $key => $value) {
            $value = trim($value);
            if (strstr($commodity[$value], '#hidden')) { continue; }
            if (!$commodity[$value] && $model->getBeDontShowEmptyField()) { continue; }
            if ($value === $price_field_name) {
                $commodity[$value] = sprintf($currency_format, $commodity[$value]);
            }
            // link to product page
            if ($value === $link_to_product_field_name) {
                if ($open_product_link_to_another_window) {
                    $target = 'target="_blank"';
                } else {
                    $target = '';
                }
                $commodity[$value] = '<a class="link_to_product_page" href="' . get_permalink($post_id) . '" ' . $target . ' >' . $commodity[$value] . '</a>';
            }
            // stock
            if ($value === $number_of_stock_field_name) {
                //print_r($cf[$number_of_stock_field_name][0]);
                if (strstr($cf[$number_of_stock_field_name][0], '#hidden')) {
                    continue;
                }
                // Check stock of custom field, Just to be sure.
                $stock = $cfshoppingcart_common->get_cf_stock($postid);
                if ($stock['num'] == -1) {
                    //continue;
                    $commodity[$value] = __('Many','cfshoppingcart');
                } else if ($stock['num'] >= 0 && $stock['num'] < $commodity['quantity']) {
                    $commodity[$value] .= ' (* ' . __('Order is more than stock','cfshoppingcart') . ')';
                    $order_is_more_than_stock = true;
                }
            }
            $content .= $trth . $value . $thtd . $commodity[$value] . $tdtr;
        }
        //
        $content .= $trth. __('Quantity','cfshoppingcart') . $thtd . '<input type="text" name="quantity" class="cfshoppingcart_quantity_' . $postid . '" value="' . $commodity['quantity'] . '" /> ' . $quantity . '</td></tr>';
        $content .= $trth . $thtd . '<input type="submit" class="cfshoppingcart_cancel_button" name="cancel" value="' . __('Cancel','cfshoppingcart') . '" /> <input type="submit" class="cfshoppingcart_change_quantity_button" name="change_quantity" value="' . __('Change quantity','cfshoppingcart') . '" />' . $tdtr;
        // form end ****************************************************
        if ($table_tag == 'table') {
            $content .= '</table>';
        } else {
            $content .= '</dl>';
        }
        $content .= '</form>';
        $content .= '</div><!-- /.cfshoppingcart_cart_product_wrap -->';
    }
    
    //print_r($sum);
    $content .= '<div class="cfshoppingcart_in_cart_sum">';
    if ($table_tag == 'table') {
        $content .= '<table>';
    } else {
        $content .= '<dl>';
    }
    $content .= $trth . __('Quantity','cfshoppingcart') . $thtd . $sum['quantity_of_commodity'] . $quantity . $tdtr;
    $content .= $trth . __('Subtotal','cfshoppingcart') . $thtd . sprintf($currency_format, $sum['price']) . $tdtr;

    if ($model->getShippingEnabled()) {
        if ($sum['shipping'] < 0 && $sum['shipping_msg']) {
            if ($table_tag == 'table') {
                $content .= '<tr><td colspan="2">' . $sum['shipping_msg'] . '</td></tr>';
            } else {
                $content .= '<dt></dt><dd>' . $sum['shipping_msg'] . '</dd>';
            }
        } else {
            $content .= $trth . __('Shipping','cfshoppingcart') . $thtd . sprintf($currency_format, $sum['shipping']) . $tdtr;
            if ($sum['shipping_msg']) {
                if ($table_tag == 'table') {
                    $content .= '<tr><td colspan="2">' . $sum['shipping_msg'] . '</td></tr>';
                } else {
                    $content .= '<dt></dt><dd>' . $sum['shipping_msg'] . '</dd>';
                }
            }
        }
        $content .= $trth . __('Total price','cfshoppingcart') . $thtd . sprintf($currency_format, $sum['total']) . $tdtr;
    }
    if ($table_tag == 'table') { $content .= '</table>'; } else { $content .= '</dl>'; }
    $content .= '</div>';

    if ($order_is_more_than_stock) {
        $content .= '<div class="order_is_more_than_stock"><span>' . __("Order is more than stock, Can't check out.",'cfshoppingcart') . '</span></div>';
    } else {
        $content .= '<div class="orderer">&raquo;&nbsp;<a class="orderder_input_screen" href="' . $send_order_url . '">' . $model->getOrdererInputScreenText() . '</a></div>';
    }

    if ($args[0] === 'commu') { return $content; }
    $content = '<div id="cfshoppingcart_form">' . $content .= '</div><!-- /#cfshoppingcart_form -->';

    if ($is_shortcode) return $content;
    else echo $content;
    return;
}

/* qf-getthumb */
function get_commodity_img($postid, $qfgetthumb_option_1, $qfgetthumb_default_image) {
    global $post;
    $posts = get_posts('include=' . $postid . '&quantityposts=1');
    $post = $posts[0];
    setup_postdata($post);
    if ($qfgetthumb_default_image) {
        $s = the_qf_get_thumb_one($qfgetthumb_option_1, $qfgetthumb_default_image);
    } else {
        $s = the_qf_get_thumb_one($qfgetthumb_option_1);
    }
    $img = '<img src="' . $s . '" />';
    return $img;

}

?>
