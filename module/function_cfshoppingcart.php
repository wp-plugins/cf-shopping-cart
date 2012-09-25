<?php
/*
 * function_cfshoppingcart.php
 * -*- Encoding: utf8n -*-
 */


// get data object
$wpCFShoppingcart = new WpCFShoppingcart();
require_once('common.php');
$cfshoppingcart_common = new cfshoppingcart_common();

function cfshoppingcart($args = '') {
    //print_r($_SESSION);
    
    // get data object
    global $wpCFShoppingcart;
    $model = $wpCFShoppingcart->model;
    global $cfshoppingcart_common;
    global $post;

    // manual
    if (!$cfshoppingcart_common->is_show_product()) {
        return false;
    }
    
    $get_post_custom = $cfshoppingcart_common->get_custom_fields();
    //print_r($get_post_custom);
    
    //print_r($model);
    if ($is_debug = $model->is_debug()) {
        require_once('debug.php');
        echo debug_cfshoppingcart('called: function cfshoppingcart()');
    }
    
    
    $price_field_name = $model->getPriceFieldName();
    $number_of_stock_field_name = $model->getNumberOfStockFieldName();
    $custom_fields = $model->getCustomFields();
    $currency_format = $model->getCurrencyFormat();
    $quantity_str = $model->getQuantity();
    $table_tag = $model->getTableTag();
    $link_to_product = false;
    if ($link_to_product) {
        $link_to_product_field_name = $model->getLinkToProductFieldName();
        $open_product_link_to_another_window = $model->getOpenProductLinkToAnotherWindow();
    }

    //print_r($custom_fields);
    //trim($custom_fields);
    //print "[$price_field_name]";
    
    //global $post;
    $id = $post->ID;
    
    //$custom_fields = array('メーカー', '品番', '単価', 'シリーズ名', '年代');
    $c = $get_post_custom;
    //$c = $cfshoppingcart_common->get_custom_fields();
    if ($is_debug) {
        debug_cfshoppingcart('function get_post_custom() return is');
        print_r($c);
    }

    if ($is_debug) {
        debug_cfshoppingcart('custom_fields is');
        print_r($custom_fields);
    }
    // form start ****************************************************
    $content = '<div class="cfshoppingcart_post_wrap">';
    $fnid = 'cfshoppingcart_product_id_x';
    $content .= '<form class="' . $fnid . '" name="' . $fnid . '" method="post" action="">';
    $content .= '<input type="hidden" class="wp_cfshoppingcart" name="wp_cfshoppingcart" value="cfshoppingcart" />';
    $content .= '<input type="hidden" name="include" value="' . $id . '" />';
    $content .= '<div class="cfshoppingcart_commodity">';
    // put no ajax message
    $content .= $cfshoppingcart_common->get_no_ajax_message();
    
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
    
    $select_array = array();
    $select_array_index = 0;
    $stock_array = array();
    $stock_array_index = 0;

    $dont_display_these_information_of_below_if_sold_out_product = $model->getDontDisplayTheseInformationOfBelowIfSoldOutProduct();
    //print_r($dont_display_these_information_of_below_if_sold_out_product);
    $is_sold_out = $cfshoppingcart_common->is_stock_zero($id);
    
    $stock_value = $c[$number_of_stock_field_name][0];
    $stock_value = cfshoppingcart_get_stock_html($stock_value, $is_sold_out);
    
    foreach ($custom_fields as $key => $value) {
        //print "[$value]";
        $value = trim($value);
        //$is_sold_out = false;
        $asfso = array_search($value, $dont_display_these_information_of_below_if_sold_out_product);
        if ($is_sold_out === true && $asfso !== NULL && $asfso !== false) { continue; }
        if (strstr($c[$value][0], '#hidden')) { continue; }
        if ((is_null($c[$value][0]) || $c[$value][0] === '') && $model->getBeDontShowEmptyField()) { continue; }
        $c[$value][0] = str_replace('#postid', sprintf($model->getPostidFormat(), $id), $c[$value][0]);
        $c[$value][0] = str_replace('#post_title', $post->post_title, $c[$value][0]);
        if ($link_to_product) {
            if ($value === $link_to_product_field_name) {
                if ($open_product_link_to_another_window) {
                    $target = 'target="_blank"';
                } else {
                    $targe = '';
                }
                $c[$value][0] = '<a class="to_product_page" href="' . get_permalink($post_id) . '" ' . $target . ' >' . $c[$value][0] . '</a>';
            }
        }
        if ($value === $price_field_name) {
            if ($is_sold_out && $model->getDisplaySoldOutMessageInPriceField()) {
                $c[$price_field_name][0] = $model->getSoldOutMessageInPriceField();
            } else {
                $c[$value][0] = sprintf($currency_format, $c[$value][0]);
            }
        }
        // select
        if (strstr($c[$value][0], '#hidden')) {
            continue;
        } else if (preg_match('/^#select/', $c[$value][0])) {
            // make select html tag
            $c[$value][0] = cfshoppingcart_get_post_select($value, $c[$value][0], 'select');
        } else if (preg_match('/^#radio/', $c[$value][0])) {
            // make select html tag
            $c[$value][0] = cfshoppingcart_get_post_select($value, $c[$value][0], 'radio');
        }
        // stock
        if ($value === $number_of_stock_field_name) {
            $content .= '__stock__';
            continue;
        }
        $content .= $trth . $value . $thtd . $c[$value][0] . $tdtr;
    }
    if ($table_tag == 'table') { $content .= '</table>'; } else { $content .= '</dl>'; }
    $content .= '</div>';

    $stock_content = $trth . $number_of_stock_field_name . $thtd . $stock_value . $tdtr;

    $content = str_replace('__stock__', $stock_content, $content);

    //
    $current_user = wp_get_current_user();
    if ($model->getShopNowClosed() && $current_user->user_level < $model->getShopNowClosedUserLevel()) {
        return $content = '<div class="cfshoppingcart_commodity_op_shop_now_closed"><span></span></div>';
    }
    //echo 'is_sold_out = ' . $is_sold_out;
    if ($is_sold_out) {
        $content .= '<div class="cfshoppingcart_commodity_op_sold_out"></div>';
    } else if (!$c[$price_field_name]) {
    } else if (strstr($c[$price_field_name][0], '#hidden')) {
    } else {
        $content .= '<div class="cfshoppingcart_commodity_op"><span>';
        if ($model->getDontDisplayOrderQuantityTextboxValue()) {
            $content .= '<input name="quantity" class="cfshoppingcart_quantity_' . $id . '" type="hidden" value="1" />';
        } else {
            $content .= __('Quantity','cfshoppingcart') . ' <input name="quantity" class="cfshoppingcart_quantity_' . $id . '" type="text" value="1" /> ' . $quantity_str . ' ';
        }
        $content .= '</span>';
        $content .= '<input class="add_to_cart_button" type="submit" name="add_to_cart" value="' . $model->getAddToCartButtonText() . '" />';
        $content .= cfshoppingcart_get_under_link();
        if ($model->getDisplayWaitingAnimation()) {
            $content .= ' <img class="cfshoppingcart_waiting_anm" style="border:none;margin:0;padding:0;display:none" src="' . $cfshoppingcart_common->get_plugin_uri()  . '/js/ajax_activity_indicators_download_animated_indicator.gif" />';
        }
        $content .= '</div><!-- /cfshoppingcart_commodity_op -->';
    }
    // form end ********************************************************
    $content .= '</form>';
    $content .= '</div><!-- /.cfshoppingcart_post_wrap -->';
    
    
    // Out put contents.
    if ($model->getShowCommodityOnManually()) {
        echo $content;
    } else {
        return $content;
    }
}

function cfshoppingcart_get_under_link() {
    global $wpCFShoppingcart;
    $model = $wpCFShoppingcart->model;
    $html = array();
    
    if ($model->getPlacedCartLinkToUnderTheProductValue()) {
        $html[] = '<a class="cfshoppingcart_under_the_link_cart" href="' . $model->getCartUrl() . '">' . $model->getCartLinkText() . '</a>';
    }
    if ($model->getPlacedCheckOutLinkToUnderTheProductValue()) {
        $html[] = '<a class="cfshoppingcart_under_the_link_check_out" href="' . $model->getSendOrderUrl() . '">' . $model->getCheckOutLinkText() . '</a>';
    }
    return '<div class="cfshoppingcart_under_the_link">' . join('<span class="cfshoppingcart_under_the_link_separator"> | </span>', $html) . '</div>';
}


function cfshoppingcart_get_stock_html($stock_value, $is_sold_out) {
    global $wpCFShoppingcart;
    $model = $wpCFShoppingcart->model;

    global $cfshoppingcart_common;

    if ($is_sold_out && $model->getTypeOfShowSoldOutMessage() == 'show_sold_out_message') {
        return $model->getSoldOutMessage();
    }
    
    $stock_value = $cfshoppingcart_common->clean_cf_textarea($stock_value);
    if (!strstr($stock_value, "\n")) {
        if ($stock_value == -1) {
            return __('Many','cfshoppingcart');
        }
        return $stock_value;
    }
    //echo "[$stock_value]";
    $items = explode("\n", $stock_value);
    //print_r($items);
    foreach ($items as $index => $item) {
        if (!preg_match('/^(.*)=(-{0,1}[0-9]*?)$/', $item, $match)) {
            continue;
        }
        $stock_key = $match[1];
        $stock_num = $match[2];
        if ($stock_num == 0) {
            if ($model->getTypeOfShowSoldOutMessage() === 'dont_show_the_product') {
                continue;
            } else {
                $stock_num = $model->getSoldOutMessage();
            }
        }
        if ($stock_num == -1) { $stock_num = __('Many','cfshoppingcart'); }
        $stock_key = str_replace('_', ', ', $stock_key);
        
        $stock_content .= '<div class="stock_item"><span class="stock_item_title">' . trim($stock_key) . ': </span><span class="number_of_stock_itme">' . trim($stock_num) . '</span></div>';
    }
    if ($stock_content) { return $stock_content; }
    //echo 'zero';
    return 0;
}

function cfshoppingcart_get_post_select($n, $cf, $type) {
    //echo 'cfshoppingcart_get_post_select';
    global $cfshoppingcart_common;// = new cfshoppingcart_common();

    $cf = $cfshoppingcart_common->clean_cf_textarea($cf);
    $cfa = explode("\n", $cf);
    //print_r($cfa);
    $h = '';
    foreach ($cfa as $index => $value) {
        if ($index == 0) { continue; }
        if ($index == 1 && $type === 'radio') {
            $checked = 'checked';
        } else {
            $checked = '';
        }
        $value = trim($value);
        if (!$value) { continue; }
        $str = $value;
        // check extra charges
        if (preg_match('/^(.*)=(-{0,1}[0-9]*|-{0,1}[0-9]*\.[0-9]*)$/', $str, $match)) {
            $str = preg_replace('/_/', ' ', trim($match[1]), 1);
        }
        if ($type === 'select') {
            $h .= '<option value="' . $value . '">' . $str . '</option>';
        } else { // radio
            $h .= '<input type="radio" name="' . $n . '" value="' . $value . '" ' . $checked . ' />' . $str;
        }
    }
    if (!$h) return '';
    if ($type == 'select') {
        $h = '<select name="' . $n . '">' . $h . '</select>';
    }
    return $h;
}
?>
