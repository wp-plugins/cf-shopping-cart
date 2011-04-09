<?php
/*
 * function_cfshoppingcart.php
 * -*- Encoding: utf8n -*-
 */

require_once('cart.php');
require_once('contact-form-7.php');

// get data object
$WpCFShoppingcart = /* php4_110323 & new */ new WpCFShoppingcart();
require_once('common.php');
$cfshoppingcart_common = /* php4_110323 & new */ new cfshoppingcart_common();

function cfshoppingcart($args = '') {
    //print_r($_SESSION);
    
    global $cfshoppingcart_stat;
    if ($cfshoppingcart_stat === 'cart_page') return;
    
    // get data object
    global $WpCFShoppingcart;
    $model = $WpCFShoppingcart->model;
    global $cfshoppingcart_common;
    
    global $post;
    //$get_post_custom = get_post_custom();
    $get_post_custom = $cfshoppingcart_common->get_custom_fields();
    //print_r($get_post_custom);
    
    //print_r($model);
    if ($is_debug = $model->is_debug()) {
        require_once('debug.php');
        echo debug_cfshoppingcart('called: function cfshoppingcart()');
        //
        //echo '<a href="' . $cfshoppingcart_common->get_plugin_module_uri() . '/commu.php' . '" target="_blank">SEE THE commu.php OUTPUT MESSAGE.</a>';
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
    if ((!$c[$price_field_name] && !$model->getShowCustomFieldWhenPriceFieldIsEmpty()) ||
        (strstr($c[$price_field_name][0], '#hidden') && !$model->getShowCustomFieldWhenPriceFieldIsEmpty())) {
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
    foreach ($custom_fields as $key => $value) {
        //print "[$value]";
        $value = trim($value);
        $is_sold_out = false;
        if (strstr($c[$value][0], '#hidden')) { continue; }
        if (!$c[$value][0] && $model->getBeDontShowEmptyField()) { continue; }
        $c[$value][0] = str_replace('#postid', sprintf($model->getPostidFormat(), $id), $c[$value][0]);
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
            $c[$value][0] = sprintf($currency_format, $c[$value][0]);
        }
        // select
        if (strstr($c[$value][0], '#hidden')) {
            continue;
        } else if (preg_match('/^#select/', $c[$value][0])) {
            // make select html tag
            $c[$value][0] = cfshoppingcart_get_post_select($value, $c[$value][0]);
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

    $stock_value = $c[$number_of_stock_field_name][0];
    //echo 'stock_value = ' . $stock_value;
    // stock table html
    //echo 'select_array = '; print_r($select_array);
    //$stock_value = cfshoppingcart_get_stock_html($select_array, $stock_value);
    $stock_value = cfshoppingcart_get_stock_html($stock_value);
    //echo 'stock_value = ' . $stock_value;
    if (!$number_of_stock_field_name) {
        // don't stock manage
        $is_sold_out = false;
    } else if (preg_match('/[^0-9\-]/', $stock_value)) {
    } else if ($stock_value == -1) {
        // -1: don't stock manage
        $is_sold_out = false;
        $stock_value = __('Many','cfshoppingcart');
    } else if ($stock_value == 0 && $model->getTypeOfShowSoldOutMessage() === 'show_sold_out_message') {
        $stock_value = $model->getSoldOutMessage();
        $is_sold_out = true;
    }
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
        // nothing
    } else if (strstr($c[$price_field_name][0], '#hidden')) {
        // nothing
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


//function cfshoppingcart_get_stock_html($select_array, $stock_value) {
function cfshoppingcart_get_stock_html($stock_value) {
    global $WpCFShoppingcart;// = /* php4_110323 & new */ new WpCFShoppingcart();
    $model = $WpCFShoppingcart->model;

    global $cfshoppingcart_common;
    
    $stock_value = $cfshoppingcart_common->clean_cf_textarea($stock_value);
    if (!strstr($stock_value, "\n")) {
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

function cfshoppingcart_get_post_select($n, $cf) {
    //echo 'cfshoppingcart_get_post_select';
    global $cfshoppingcart_common;// = /* php4_110323 & new */ new cfshoppingcart_common();

    $cf = $cfshoppingcart_common->clean_cf_textarea($cf);
    $cfa = explode("\n", $cf);
    //print_r($cfa);
    $h = '';
    foreach ($cfa as $index => $value) {
        if ($index == 0) { continue; }
        $value = trim($value);
        if (!$value) { continue; }
        $str = $value;
        // check extra charges
        if (preg_match('/^(.*)=(-{0,1}[0-9]*|-{0,1}[0-9]*\.[0-9]*)$/', $str, $match)) {
            $str = preg_replace('/_/', ' ', trim($match[1], 1));
        }
        $h .= '<option value="' . $value . '">' . $str . '</option>';
        //$h .= '<option value="' . $str . '">' . $str . '</option>';
    }
    if (!$h) return '';
    $h = '<select name="' . $n . '">' . $h . '</select>';
    return $h;
}
?>
