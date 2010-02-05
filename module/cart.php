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

function cfshoppingcart_cart() {
    if (!session_id()){ @session_start(); }
    
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
    $qfgetthumb_option_1 = $model->getQfgetthumbOption1();
    $qfgetthumb_default_image = $model->getQfgetthumbDefaultImage();
    $is_use_shipping = $model->getIsUseShipping();
    
    $commodities = $_SESSION['cfshoppingcart']['commodities'];
    $sum = $_SESSION['cfshoppingcart']['sum'];

    if ($sum['quantity_of_commodity'] == 0 || !$commodities) {
        echo __('Shopping Cart is empty.', 'cfshoppingcart');
        return;
    }
    
    //echo '<div class="cfshoppingcart_commodity_cart">';
    //echo '<form id="cfshoppingcart_form" method="post" action="' . $cart_url . '">';
    //echo '<form id="cfshoppingcart_form">';
    echo '<div id="cfshoppingcart_form">';
    echo '<table border="1">';
    foreach ($commodities as $postid => $commodity) {
        if (!$postid) continue;
        foreach ($custom_fields as $key => $value) {
            $value = trim($value);
            if ($value === $price_field_name) {
                $commodity[$value] = sprintf($currency_format, $commodity[$value]);
            }
            /* image */
            if ($key == 0 && function_exists('the_qf_get_thumb_one')) {
                $img = '<td rowspan="' . (count($custom_fields)+2) . '">' . get_commodity_img($postid, $qfgetthumb_option_1, $qfgetthumb_default_image) . '</td>';
            } else {
                $img = '';
            }
            echo '<tr><td>' . $value . '</td><td>' . $commodity[$value] . '</td>' . $img . '</tr>';
        }
        echo '<tr><td>'. __('Quantity','cfshoppingcart') . '</td><td><input type="text" class="cfshoppingcart_quantity_' . $postid . '" value="' . $commodity['quantity'] . '" /> ' . $quantity . '</td></tr>';
        echo '<tr><td></td><td><input type="button" class="cfshoppingcart_cancel_button" name="id=' . $postid . '" value="' . __('Cancel','cfshoppingcart') . '" /><input type="button" class="cfshoppingcart_change_quantity_button" name="id=' . $postid . '" value="' . __('Change quantity','cfshoppingcart') . '" /></td></tr>';
    }
    
    //echo '</table></form></div>';
    //echo '</table></div></div>';
    echo '</table></div>';

    //print_r($sum);
    echo '<div><table>';
    echo '<tr><td>' . __('Quantity','cfshoppingcart') . '</td><td>' . $sum['quantity_of_commodity'] . $quantity . '</td></tr>';
    echo '<tr><td>' . __('Subtotal','cfshoppingcart') . '</td><td>' . sprintf($currency_format, $sum['price']) . '</td></tr>';

    if ($is_use_shipping) {
        if ($sum['shipping'] < 0 && $sum['shipping_msg']) {
            echo '<tr><td colspan="2">' . $sum['shipping_msg'] . '</td></tr>';
        } else {
            echo '<tr><td>' . __('Shipping','cfshoppingcart') . '</td><td>' . sprintf($currency_format, $sum['shipping']) . '</td></tr>';
            if ($sum['shipping_msg']) {
                echo '<tr><td colspan="2">' . $sum['shipping_msg'] . '</td></tr>';
            }
        }
        echo '<tr><td>' . __('Total price','cfshoppingcart') . '</td><td>' . sprintf($currency_format, $sum['total']) . '</td></tr>';
    }
    echo '</table></div>';

    echo '<div class="orderer">&raquo;&nbsp;<a class="orderder_input_screen" href="' . $send_order_url . '">' . __('Orderer Input screen','cfshoppingcart') . '</a></div>';
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
