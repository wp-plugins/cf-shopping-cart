<?php
/*
 * function_cfshoppingcart.php
 * -*- Encoding: utf8n -*-
 */

require_once('cart.php');
require_once('contact-form-7.php');

function cfshoppingcart($args = '') {//($get_post_custom){
    //print_r($_SESSION);
    
    global $cfshoppingcart_stat;
    if ($cfshoppingcart_stat === 'cart_page') return;
    
    global $post;
    $get_post_custom = get_post_custom();
    
    /*
    $plugin_fullpath = get_plugin_fullpath();
    $plugin_path = get_plugin_path();
    $plugin_folder = get_plugin_folder();
    $plugin_uri = get_plugin_uri();
      */
    //echo "<p>current_path = $plugin_folder, $plugin_path</p>";
    
    // get data object
    $WpCFShoppingcart =  /* php4_110323 & new */ new WpCFShoppingcart();
    $model = $WpCFShoppingcart->model;
    //print_r($model);
    if ($is_debug = $model->is_debug()) {
        require_once('debug.php');
        echo debug_cfshoppingcart('called: function cfshoppingcart()');
        require_once('common.php');
        echo '<a href="' . get_plugin_module_uri() . '/commu.php' . '" target="_blank">SEE THE commu.php OUTPUT MESSAGE.</a>';
    }
    
    $rf = 1;
    if ($args === 'setting') {
        if ($model->getShowCommodityOnHome() && is_home()) $rf = 0;
        if ($model->getShowCommodityOnPage() && is_page()) $rf = 0;
        if ($model->getShowCommodityOnArchive() && is_archive()) $rf = 0;
        if ($model->getShowCommodityOnSingle() && is_single()) $rf = 0;
        if ($rf) return;
    } else {
        if ($model->getShowCommodityOnManually()) $rf = 0;
        if ($rf) return;
    }
    
    $price_field_name = $model->getPriceFieldName();
    $number_of_stock_field_name = $model->getNumberOfStockFieldName();
    $custom_fields = $model->getCustomFields();
    $currency_format = $model->getCurrencyFormat();
    $quantity_str = $model->getQuantity();
    $table_tag = $model->getTableTag();
    //print_r($custom_fields);
    //trim($custom_fields);
    //print "[$price_field_name]";
    
    //global $post;
    $id = $post->ID;
    
    //$custom_fields = array('メーカー', '品番', '単価', 'シリーズ名', '年代');
    $c = $get_post_custom;
    if ($is_debug) {
        debug_cfshoppingcart('function get_post_custom() return is');
        print_r($c);
    }
    if (!$c[$price_field_name]) {
        if ($is_debug) {
            debug_cfshoppingcart('price_field_name not found in Custom Field on this post. return function.');
        }
        return; // 単価が無い
    }

    if ($is_debug) {
        debug_cfshoppingcart('custom_fields is');
        print_r($custom_fields);
    }
    // form start ****************************************************
    $content = '<div class="cfshoppingcart_post_wrap">';
    //$fnid = 'cfshoppingcart_product_id_' . $id;
    //$content .= '<form id="' . $fnid . '" name="' . $fnid . '" method="post" action="">';
    $fnid = 'cfshoppingcart_product_id_x';
    $content .= '<form class="' . $fnid . '" name="' . $fnid . '" method="post" action="">';
    $content .= '<input type="hidden" name="include" value="' . $id . '" />';
    //$content .= '<input type="hidden" name="cmd" value="add_to_cart" />';
    //
    $content .= '<div class="cfshoppingcart_commodity">';
    if ($table_tag == 'table') {
        $content .= '<table>';
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
        //print "[$value]";
        $value = trim($value);
        $is_sold_out = false;
        if ($value === $price_field_name) {
            $c[$value][0] = sprintf($currency_format, $c[$value][0]);
        }
        // stock
        if ($value === $number_of_stock_field_name) {
            if ($c[$value][0] == 0 && $model->getTypeOfShowSoldOutMessage() === 'show_sold_out_message') {
                $c[$value][0] = $model->getSoldOutMessage();
                $is_sold_out = true;
            } else if ($c[$value][0] == -1) {
                continue;
            }
        }
        // select
        if (preg_match('/^#select\|/', $c[$value][0])) {
            //$content .= '['.$c[$value][0].']';
            $c[$value][0] = cfshoppingcart_get_post_select($value, $c[$value][0]);
        }
        
        //if ($table_tag == 'table') {
        //$content .= '<table>';
        $content .= $trth . $value . $thtd . $c[$value][0] . $tdtr;
        //} else {
        //$content .= '<dl>';
        //    $content .= '<dt>' . $value . '</dt><dd>' . $c[$value][0] . '</dd>';
        //}
    }
    if ($table_tag == 'table') { $content .= '</table>'; } else { $content .= '</dl>'; }
    $content .= '</div>';

    $current_user = wp_get_current_user();
    if ($model->getShopNowClosed() && $current_user->user_level < $model->getShopNowClosedUserLevel()) {
        return $content .= '<div class="cfshoppingcart_commodity_op_shop_now_closed"><span></span></div>';
    }
    if ($is_sold_out) {
        $content .= '<div class="cfshoppingcart_commodity_op_sold_out"></div>';
    } else {
        $content .= '<div class="cfshoppingcart_commodity_op"><span>';
        $content .= __('Quantity','cfshoppingcart') . ' <input name="quantity" class="cfshoppingcart_quantity_' . $id . '" type="text" value="1" /> ' . $quantity_str . ' ';
        $content .= '</span>';
        $content .= '<input class="add_to_cart_button" type="submit" name="add_to_cart" value="' . __('Add to Cart','cfshoppingcart') . '" />';
        $content .= '</div><!-- /cfshoppingcart_commodity_op -->';
    }
    // form end ********************************************************
    $content .= '</form>';
    $content .= '</div><!-- /.cfshoppingcart_post_wrap -->';
    
    if ($model->getShowCommodityOnManually()) {
        echo $content;
    } else {
        return $content;
    }
}

function cfshoppingcart_get_post_select($value, $cf) {
    //echo 'cfshoppingcart_get_post_select';
    $cfa = explode('|', $cf);
    //print_r($cfa);
    $h = '';
    foreach ($cfa as $index => $value) {
        if ($index == 0) { continue; }
        $value = trim($value);
        if (!$value) { continue; }
        $h .= '<option value="' . $value . '">' . $value . '</option>';
    }
    if (!$h) return '';
    $h = '<select name="' . $value . '">' . $h . '</select>';
    return $h;
}
?>
