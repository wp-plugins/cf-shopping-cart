<?php
/*
 * function_cfshoppingcart.php
 * -*- Encoding: utf8n -*-
 */

//require_once('common.php');
require_once('cart.php');
require_once('contact-form-7.php');

function cfshoppingcart($get_post_custom){
    $is_change = 0; /* CONFIGURATION - 1:change or 0:add */

    if ($is_change) {
        if (!session_id()){ @session_start(); }
    }

    /*
    $plugin_fullpath = get_plugin_fullpath();
    $plugin_path = get_plugin_path();
    $plugin_folder = get_plugin_folder();
    $plugin_uri = get_plugin_uri();
      */
    //echo "<p>current_path = $plugin_folder, $plugin_path</p>";
    
    // get data object
    $WpCFShoppingcart = & new WpCFShoppingcart();
    $model = $WpCFShoppingcart->model;
    //print_r($model);
    $price_field_name = $model->getPriceFieldName();
    $custom_fields = $model->getCustomFields();
    $currency_format = $model->getCurrencyFormat();
    $quantity_str = $model->getQuantity();
    //print_r($custom_fields);
    //trim($custom_fields);
    //print "[$price_field_name]";
    
    global $post;
    $id = $post->ID;
    if ($is_change) {
        $commodities = $_SESSION['cfshoppingcart']['commodities'];
        $quantity = $commodities[$id]['quantity'];
        if (!$quantity) $quantity = 1;
    }
    
    //$custom_fields = array('メーカー', '品番', '単価', 'シリーズ名', '年代');
    $c = $get_post_custom;
    //print_r($c);
    if (!$c[$price_field_name]) return; // 単価が無い
    
    echo '<div class="cfshoppingcart_commodity"><table>';
    foreach ($custom_fields as $key => $value) {
        //print "[$value]";
        $value = trim($value);
        if ($value === $price_field_name) {
            $c[$value][0] = sprintf($currency_format, $c[$value][0]);
        }
        echo '<tr><td>' . $value . '</td><td>' . $c[$value][0] . '</td></tr>';
    }
    echo '</table></div>';
    
    echo '<div class="cfshoppingcart_commodity_op">';
    if ($is_change) {
        echo __('Quantity','cfshoppingcart') . ' <input class="cfshoppingcart_quantity_' . $id . '" type="text" value="' . $quantity . '" /> ' . $quantity_str . ' ';
        //echo __('Quantity','cfshoppingcart') . ' <input class="cfshoppingcart_quantity_' . $id . '" type="text" value="1" /> ' . $quantity_str . ' (' . __('In cart is ','cfshoppingcart') . ' ' . $quantity . ') ';
        echo '<input class="change_quantity_button" type="button" name="id=' . $id . '" value="' . __('Into Cart','cfshoppingcart') . '" />';
        //echo '<input class="change_quantity_button" type="button" name="id=' . $id . '" value="' . __('Change quantity','cfshoppingcart') . '" />';
    } else {
        echo __('Quantity','cfshoppingcart') . ' <input class="cfshoppingcart_quantity_' . $id . '" type="text" value="1" /> ' . $quantity_str . ' ';
        echo '<input class="add_to_cart_button" type="button" name="id=' . $id . '" value="' . __('Add to Cart','cfshoppingcart') . '" />';
    }
    echo '</div>';
}

?>
