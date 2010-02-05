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

    require_once('common.php');
    $plugin_fullpath = get_plugin_fullpath();
    //$plugin_path = get_plugin_path();
    //$plugin_folder = get_plugin_folder();
    //$plugin_uri = get_plugin_uri();
    //$plugin_module_uri = get_plugin_module_uri();
    
    // get data object
    $WpCFShoppingcart = & new WpCFShoppingcart();
    $model = $WpCFShoppingcart->model;
    $price_field_name = $model->getPriceFieldName();
    $is_use_shipping = $model->getIsUseShipping();
    
    $sname = 'cfshoppingcart';
    $commodities  = $_SESSION[$sname]['commodities'];
    
    $sum = 0;
    $num = 0;
    if ($commodities) {
        foreach ($commodities as $postid => $commodity) {
            $sum += $commodity[$price_field_name] * $commodity['quantity'];
            $num += $commodity['quantity'];
        }
    }

    if ($is_use_shipping) {
        require_once($plugin_fullpath . '/extention/shipping.php');
        list($shipping, $shipping_msg) = shipping($num, $sum);
    }
    
    $_SESSION[$sname]['sum']['quantity_of_commodity'] = $num;
    $_SESSION[$sname]['sum']['price'] = $sum;
    $_SESSION[$sname]['sum']['shipping'] = $shipping;
    $_SESSION[$sname]['sum']['shipping_msg'] = $shipping_msg;
    $_SESSION[$sname]['sum']['total'] = $shipping + $sum;
    
    if ($sum == 0 || $num == 0) {
        $html = __('Shopping Cart is empty.', 'cfshoppingcart');
    } else {
        $html  = cfshoppingcart_widget_html($_SESSION[$sname]['sum']);
    }
    $_SESSION[$sname]['sum']['html'] = $html;
    
    return $html;
}

function cfshoppingcart_widget_html($sum) {
    // get data object
    $WpCFShoppingcart = & new WpCFShoppingcart();
    $model = $WpCFShoppingcart->model;
    //print_r($model);
    $price_field_name = $model->getPriceFieldName();
    $custom_fields = $model->getCustomFields();
    $currency_format = $model->getCurrencyFormat();
    $quantity = $model->getQuantity();
    $cart_url = $model->getCartUrl();
    $is_use_shipping = $model->getIsUseShipping();

    //$sum = $_SESSION['cfshoppingcart']['sum'];

    
    $html  = '<table>';
    $html .= '<tr><td>' . __('Quantity','cfshoppingcart') . '</td><td>' . $sum['quantity_of_commodity'] . $quantity . '</td></tr>';
    $html .= '<tr><td>' . __('Subtotal','cfshoppingcart') . '</td><td>' . sprintf($currency_format, $sum['price']) . '</td></tr>';
    if ($is_use_shipping) {
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
    $html .= '<span class="go_cart">&raquo;&nbsp;<a href="' . $cart_url . '">' . __('Go to Cart','cfshoppingcart') . '</a></span>';

    return $html;
}

?>