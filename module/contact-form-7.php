<?php
/*
 * contact-form-7.php
 * -*- Encoding: utf8n -*-
 */

/* Add cfshoppingcart.php module into contact-form-7.
 * It module call this function.
 * Put in [cfshoppingcart cartdata] to contact-form-7 configuration, 
 * displaying Shopping-cart data in contact form textarea.
 */
function cfshoppingcart_ContactForm7($cf_opt = array()) {
    //echo 'cfshoppingcart_ContactForm7';
    //if (!session_id()){ @session_start(); }
    //if (array_key_exists('show_product_url', $cf_opt)) {
    if (isset($cf_opt['show_product_url'])) {
        $show_product_url = true;
    } else {
        $show_product_url = false;
    }
    //if (array_key_exists('hidden_fields', $cf_opt)) {
    if (isset($cf_opt['hidden_fields'])) {
        $cf_opt['hidden_fields'] = array_flip($cf_opt['hidden_fields']);
    }
    //print_r($cf_opt);
    
    // get data object
    $WpCFShoppingcart = /* php4_110323 & new */ new WpCFShoppingcart();
    $model = $WpCFShoppingcart->model;
    require_once('common.php');
    $cfshoppingcart_common = /* php4_110323 & new */ new cfshoppingcart_common();
    
    //print_r($model);
    $price_field_name = $model->getPriceFieldName();
    $custom_fields = $model->getCustomFields();
    $currency_format = $model->getCurrencyFormat();
    $quantity = $model->getQuantity();
    $cart_url = $model->getCartUrl();
    $send_order_url = $model->getSendOrderUrl();
    //$is_use_shipping = $model->getIsUseShipping();
    $number_of_stock_field_name = $model->getNumberOfStockFieldName();
    
    $commodities = $_SESSION['cfshoppingcart']['commodities'];
    $sum = $_SESSION['cfshoppingcart']['sum'];

    // shop now closed
    $current_user = wp_get_current_user();
    if ($model->getShopNowClosed() && $current_user->user_level < $model->getShopNowClosedUserLevel()) {
        return array(false, $model->getClosedMessageForSidebarWidget());
    }
    // zero
    if ($sum['quantity_of_commodity'] == 0 || !$commodities) {
        //return '<span class="cart_empty">' . __('Shopping Cart is empty.', 'cfshoppingcart') . '</span>';
        return array(false, __('Shopping Cart is empty.','cfshoppingcart'));
    }

    //print_r($commodities);
    $ret = '';
    foreach ($commodities as $postid => $commodity) {
        if (!$postid) continue;
        if ($show_product_url) {
            // get real post id and stock key.
            list($post_id, $stock_key) = $cfshoppingcart_common->get_real_postid_and_stock_key($postid);
            $url = "URL: " . get_permalink($post_id) . "\n";
        } else {
            $url = '';
        }
        // get number of stock
        $number_of_stock = $cfshoppingcart_common->get_cf_stock($postid);
        
        // stock is zero
        if ($number_of_stock_field_name && $number_of_stock == 0) {
            return array(false, __('Includes no stock products. Please confirm.','cfshoppingcart'));
        }
        
        $ret .= '---------------------------------' . "\n";
        foreach ($custom_fields as $key => $value) {
            //if (array_key_exists($value, $cf_opt['hidden_fields'])) {
            if (isset($cf_opt['hidden_fields'][$value])) {
                continue;
            }
            
            $value = trim($value);
            
            if (strstr($commodity[$value], '#hidden')) { continue; }
            if (!$commodity[$value] && $model->getBeDontShowEmptyField()) { continue; }
            // stock
            if ($value === $number_of_stock_field_name) {
                // not use stock
                //echo 'number_of_stock : ' . $number_of_stock;
                //print_r($number_of_stock);
                if ($number_of_stock['num'] == -1) {
                    $commodity[$value] = __('Many','cfshoppingcart');
                    //continue;
                } else 
                // out of stock
                if ($commodity[$value] > $number_of_stock['num']) {
                    return array(false, __('Includes out of stock products. Please confirm.','cfshoppingcart'));
                }
            }
            if ($value === $price_field_name) {
                $commodity[$value] = sprintf($currency_format, $commodity[$value]);
                /*
                $unit_b = $currency_before;
                $unit_a = $currency_after;
            } else {
                $unit_b = '';
                $unit_a = '';
                */
            }
            $ret .= '' . $value . ": " . $commodity[$value] . "\n";
        }
        $ret .= '' . __('Quantity','cfshoppingcart') . ': ' . $commodity['quantity'] . ' ' . $quantity . "\n";
        if ($url) {
            $ret .= $url;
        }
    }
    
    $ret .= '---------------------------------' . "\n";

    $ret .= '' . __('Quantity','cfshoppingcart') . ': ' . $sum['quantity_of_commodity'] . $quantity . "\n";
    $ret .= '' . __('Subtotal','cfshoppingcart') . ': ' . sprintf($currency_format, $sum['price']) . "\n";
    $ret .= '---------------------------------' . "\n";

    // shipping new and old
    //if ($model->getShippingEnabled() || $is_use_shipping) {
    if ($model->getShippingEnabled()) {
        if ($sum['shipping'] < 0 && $sum['shipping_msg']) {
            $ret .= '' . $sum['shipping_msg'] . "\n";
        } else {
            $ret .= '' . __('Shipping','cfshoppingcart') . ': ' . sprintf($currency_format, $sum['shipping']) . "\n";
            if ($sum['shipping_msg']) {
                $ret .= '' . $sum['shipping_msg'] . "\n";
            }
        }
        $ret .= '' . __('Total price','cfshoppingcart') . ': ' . sprintf($currency_format, $sum['total']) . "\n";
        $ret .= '---------------------------------' . "\n";
    }
    
    return array(true, $ret);
}

?>
