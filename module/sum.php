<?php
/* sum.php
 *
 * -*- Encoding: utf8n -*-
 */

function cfshoppingcart_sum() {
    if (!session_id()) {
        $m = '<p>cfshoppingcart_sum: please session_start</p>';
        echo $m;
        return $m;
    }

    //require_once('common.php');
    //$cfshoppingcart_common = new cfshoppingcart_common();
    global $cfshoppingcart_common;
    $cfname = $cfshoppingcart_common->get_session_key();
    
    $plugin_fullpath = $cfshoppingcart_common->get_plugin_fullpath();
    //$plugin_path = $cfshoppingcart_common->get_plugin_path();
    //$plugin_folder = $cfshoppingcart_common->get_plugin_folder();
    //$plugin_uri = $cfshoppingcart_common->get_plugin_uri();
    //$plugin_module_uri = $cfshoppingcart_common->get_plugin_module_uri();
    //$shipping_php_path = get_shipping_php_path();
    
    // get data object
    global $wpCFShoppingcart;
    $model = $wpCFShoppingcart->model;
    $price_field_name = $model->getPriceFieldName();
    
    $commodities  = $_SESSION[$cfname]['commodities'];
    
    $sum = 0;
    $num = 0;
    if ($commodities) {
        foreach ($commodities as $postid => $commodity) {
            $sum += $commodity[$price_field_name] * $commodity['quantity'];
            $num += $commodity['quantity'];
        }
    }

    // shipping
    if ($wpCFShoppingcart->shipping->model->getShippingEnabled()) {
        list($shipping, $shipping_msg) = $wpCFShoppingcart->shipping->get_shipping($num, $sum);
    }
    
    $_SESSION[$cfname]['sum']['quantity_of_commodity'] = $num;
    $_SESSION[$cfname]['sum']['price'] = $sum;
    $_SESSION[$cfname]['sum']['shipping'] = $shipping;
    $_SESSION[$cfname]['sum']['shipping_msg'] = $shipping_msg;
    $_SESSION[$cfname]['sum']['total'] = $shipping + $sum;
    
    $html  = cfshoppingcart_widget_html($_SESSION[$cfname]['sum']);
    //}
    $_SESSION[$cfname]['sum']['html'] = $html;
    
    return $html;
}

function cfshoppingcart_widget_html($sum) {
    // get data object
    global $wpCFShoppingcart;// = new WpCFShoppingcart();
    $model = $wpCFShoppingcart->model;
    //print_r($model);
    $price_field_name = $model->getPriceFieldName();
    $custom_fields = $model->getCustomFields();
    $currency_format = $model->getCurrencyFormat();
    $quantity = $model->getQuantity();
    $cart_url = $model->getCartUrl();

    // shop now closed
    $current_user = wp_get_current_user();
    if ($model->getShopNowClosed() && $current_user->user_level < $model->getShopNowClosedUserLevel()) {
        $html = '<span class="shop_now_closed">' . $model->getClosedMessageForSidebarWidget() . '</span>';
        return $html;
    }
    
    if ($sum['price'] == 0 || $sum['quantity_of_commodity'] == 0) {
        $html = $model->getWidgetEmpyCartHtml();
        return $html;
    }
    
    $html = cfshoppingcart_widget_html_products();
    $html .= '<table>';
    $html .= '<tr><td>' . __('Quantity','cfshoppingcart') . '</td><td>' . $sum['quantity_of_commodity'] . $quantity . '</td></tr>';
    $html .= '<tr><td>' . __('Subtotal','cfshoppingcart') . '</td><td>' . sprintf($currency_format, $sum['price']) . '</td></tr>';
    if ($wpCFShoppingcart->shipping->model->getShippingEnabled()) {
        if ($sum['shipping'] < 0 && $sum['shipping_msg']) {
            $html .= '<tr><td colspan="2">' . $sum['shipping_msg'] . '</td></tr>';
        } else {
            $html .= '<tr><td>' . __('Shipping','cfshoppingcart') . '</td><td>' . sprintf($currency_format, $sum['shipping']) . '</td></tr>';
            if ($sum['shipping_msg']) {
                $html .= '<tr><td colspan="2">' . $sum['shipping_msg'] . '</td></tr>';
            }
        }
        $html .= '<tr><td>' . __('Total price','cfshoppingcart') . '</td><td>' . sprintf($currency_format, $sum['total']) . '</td></tr>';
    }
    $html .= '</table>';
    if ($model->getGoToCartText()) {
        $html .= '<span class="go_cart">&raquo;&nbsp;<a href="' . $cart_url . '">' . $model->getGoToCartText() . '</a></span>';
    }
    return $html;
}

function cfshoppingcart_widget_html_products() {
    global $cfshoppingcart_common;
    $cfname = $cfshoppingcart_common->get_session_key();
    
    //$plugin_fullpath = $cfshoppingcart_common->get_plugin_fullpath();
    
    // get data object
    global $wpCFShoppingcart;
    $widget = $wpCFShoppingcart->widget;
    if (!$widget->model->getWidgetEnabledCartInfo()) {
        return '';
    }
    $cart_info = $widget->model->getCartInfo();
    $cart_info_head = $widget->model->getCartInfoHead();
    $cart_info_tail = $widget->model->getCartInfoTail();
    $model = $wpCFShoppingcart->model;
    $price_field_name = $model->getPriceFieldName();

    if (!$cart_info) {
        return $cart_info_head . $cart_info_tail;
    }

    //$test = "<tr><td>Maker</td>[Maker]</td></tr><tr><td>Product ID</td><td>[Product ID]</td></tr><tr><td>Name</td><td>[Name]</td></tr><tr><td>Size</td><td>[Size]</td></tr><tr><td>extra charges</td><td>[extra_charges]</td></tr><tr><td>Color</td><td>[Color]</td></tr><tr><td>Price</td><td>[Price]</td></tr><tr><td>Stock</td><td>[Stock]</td></tr><tr><td>Checkbox</td><td>[Checkbox]</td></tr><tr><td>quantity</td><td>[quantity]</td></tr><tr><td>_sum_</td><td>[_sum_]</td></tr>";
    $html = "";
    $commodities  = $_SESSION[$cfname]['commodities'];
    foreach ($commodities as $key1 => $commodity) {
        $line = $cart_info;
        $commodity['_sum_'] = $commodity[$price_field_name] * $commodity['quantity'];
        foreach ($commodity as $key2 => $str) {
            $line = str_replace('['.$key2.']', $str, $line);
        }
        $html .= $line;
    }

    return $cart_info_head . $html . $cart_info_tail;
}

?>