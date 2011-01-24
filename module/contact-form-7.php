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
function cfshoppingcart_ContactForm7() {
    //if (!session_id()){ @session_start(); }

    // get data object
    $WpCFShoppingcart = & new WpCFShoppingcart();
    $model = $WpCFShoppingcart->model;
    //print_r($model);
    $price_field_name = $model->getPriceFieldName();
    $custom_fields = $model->getCustomFields();
    $currency_format = $model->getCurrencyFormat();
    $quantity = $model->getQuantity();
    $cart_url = $model->getCartUrl();
    $send_order_url = $model->getSendOrderUrl();
    $is_use_shipping = $model->getIsUseShipping();
    $number_of_stock_field_name = $model->getNumberOfStockFieldName();
    
    $commodities = $_SESSION['cfshoppingcart']['commodities'];
    $sum = $_SESSION['cfshoppingcart']['sum'];

    // shop now closed
    $current_user = wp_get_current_user();
    if ($model->getShopNowClosed() && $current_user->user_level < $model->getShopNowClosedUserLevel()) {
        return '';
    }
    // zero
    if ($sum['quantity_of_commodity'] == 0 || !$commodities) {
        //return '<span class="cart_empty">' . __('Shopping Cart is empty.', 'cfshoppingcart') . '</span>';
        return '';
    }
    
    $ret = '';
    foreach ($commodities as $postid => $commodity) {
        if (!$postid) continue;

        // get number of stock
        $post_custom = get_post_custom($postid);
        $number_of_stock = $post_custom[$number_of_stock_field_name][0];
        // stock is zero
        if ($number_of_stock_field_name && $number_of_stock == 0) { return ''; }
        
        $ret .= '---------------------------------' . "\n";
        foreach ($custom_fields as $key => $value) {
            $value = trim($value);

            // stock
            if ($value === $number_of_stock_field_name) {
                // not use stock
                if ($number_of_stock == -1) { continue; }
                // out of stock
                if ($commodity[$value] > $number_of_stock) { return ''; }
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
    }
    
    $ret .= '---------------------------------' . "\n";

    $ret .= '' . __('Quantity','cfshoppingcart') . ': ' . $sum['quantity_of_commodity'] . $quantity . "\n";
    $ret .= '' . __('Subtotal','cfshoppingcart') . ': ' . sprintf($currency_format, $sum['price']) . "\n";
    $ret .= '---------------------------------' . "\n";

    // shipping new and old
    if ($model->getShippingEnabled() || $is_use_shipping) {
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
    
    return $ret;
}

?>
