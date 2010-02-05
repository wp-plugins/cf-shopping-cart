<?php
/* commu.php
 * communication module
 * Return Custom-field data.
 *
 * -*- Encoding: utf8n -*-
 *
 * test code:
 *  http://192.168.11.101/~aic/hotta/wp/wp-content/plugins/cfshoppingcart/module/commu.php?cmd=add_to_cart&include=49&quantity=3
 */


cfshoppingcart_session_start();

require_once('common.php');
$wp_fullpath = get_wp_fullpath();
//$plugin_fullpath = get_plugin_fullpath();
//$plugin_path = get_plugin_path();
//$plugin_folder = get_plugin_folder();
//$plugin_uri = get_plugin_uri();
//$plugin_module_uri = get_plugin_module_uri();
//require('../../../../wp-load.php');
require($wp_fullpath . '/wp-load.php');
require_once('sum.php');
cfshoppingcart_main();
cfshoppingcart_error_exit();

function cfshoppingcart_error_exit() {
    exit(false);
}

function cfshoppingcart_session_start() {
    $sid = session_id();
    if (!$sid) session_start();
}

/*
function cfshoppingcart_session_info() {
    $sid = session_id();
    echo '<p>session id = ' . $sid . '</p>';
    echo '<p>session_name = ' . session_name() . '</p>';
    var_dump($_SESSION);
    $s = $_SESSION;
    print_r($s);
}
*/

function cfshoppingcart_work($cmd, $id, $quantity, $customfield) {
    if (0) {
        $_SESSION = array();
        session_destroy() ;
        $_SESSION = array();
    }

    // get data object
    $WpCFShoppingcart = & new WpCFShoppingcart();
    $model = $WpCFShoppingcart->model;
    //print_r($model);

    $price_field_name = $model->getPriceFieldName();
    $cart_url = $model->getCartUrl();
    $max_quantity_of_one_commodity = $model->getMaxQuantityOfOneCommodity();
    $max_quantity_of_total_order = $model->getMaxQuantityOfTotalOrder();
    
    $sname = 'cfshoppingcart';
    $commodities  = $_SESSION[$sname]['commodities'];
    $total_quantity = $_SESSION[$sname]['sum']['quantity_of_commodity'];
    
    
    if ($cmd === 'add_to_cart') {
        // Check total quantity
        if ($max_quantity_of_total_order != 0 && ($total_quantity + $quantity) > $max_quantity_of_total_order) {
            $msg = __('Max quantity of total order is','cfshoppingcart') . ' ' . $max_quantity_of_total_order;
            return array($_SESSION[$sname]['sum']['html'], $msg);
        }
        if (!$commodities[$id]) {
            foreach ($customfield as $key => $value) {
                $commodities[$id][$key] = $value[0];
            }
            $commodities[$id]['quantity'] = 0;
        }
        // Check quantity of one commodity
        if ($max_quantity_of_one_commodity != 0 && ($commodities[$id]['quantity'] + $quantity) > $max_quantity_of_one_commodity) {
            $msg = __('Max quantity of one commodity is','cfshoppingcart') . ' ' . $max_quantity_of_one_commodity;
            return array($_SESSION[$sname]['sum']['html'], $msg);
        }
        $commodities[$id]['quantity'] += $quantity;
        $_SESSION[$sname]['commodities'] = $commodities;
        cfshoppingcart_sum();
        return array($_SESSION[$sname]['sum']['html'], '');
    } else if ($cmd === 'change_quantity_commodity') {
        if ($commodities[$id]) {
            if ($quantity == 0) {
                unset($commodities[$id]);
                $_SESSION[$sname]['commodities'] = $commodities;
                cfshoppingcart_sum();
                return array($_SESSION[$sname]['sum']['html'], '');
            }
            // Check total quantity
            if ($max_quantity_of_total_order != 0 && ($total_quantity + $quantity - $commodities[$id]['quantity']) > $max_quantity_of_total_order) {
                $msg = __('Max quantity of total order is','cfshoppingcart') . ' ' . $max_quantity_of_total_order;
                return array($_SESSION[$sname]['sum']['html'], $msg);
            }
            // Check quantity of one commodity
            if ($max_quantity_of_one_commodity != 0 && $quantity > $max_quantity_of_one_commodity) {
                $msg = __('Max quantity of one commodity is','cfshoppingcart') . ' ' . $max_quantity_of_one_commodity;
                return array($_SESSION[$sname]['sum']['html'], $msg);
            }
        } else {
            foreach ($customfield as $key => $value) {
                $commodities[$id][$key] = $value[0];
            }
            $commodities[$id]['quantity'] = 0;
        }
        $commodities[$id]['quantity'] = $quantity;
        $_SESSION[$sname]['commodities'] = $commodities;
        cfshoppingcart_sum();
        return array($_SESSION[$sname]['sum']['html'], '');
    } else if ($cmd === 'change_quantity') {
        if ($commodities[$id]) {
            if ($quantity == 0) {
                unset($commodities[$id]);
                $_SESSION[$sname]['commodities'] = $commodities;
                cfshoppingcart_sum();
                return array($cart_url, '');
            }
            // Check total quantity
            if ($max_quantity_of_total_order != 0 && ($total_quantity + $quantity - $commodities[$id]['quantity']) > $max_quantity_of_total_order) {
                $msg = __('Max quantity of total order is','cfshoppingcart') . ' ' . $max_quantity_of_total_order;
                return array($cart_url, $msg);
            }
            // Check quantity of one commodity
            if ($max_quantity_of_one_commodity != 0 && $quantity > $max_quantity_of_one_commodity) {
                $msg = __('Max quantity of one commodity is','cfshoppingcart') . ' ' . $max_quantity_of_one_commodity;
                return array($cart_url, $msg);
            }
            $commodities[$id]['quantity'] = $quantity;
            $_SESSION[$sname]['commodities'] = $commodities;
            cfshoppingcart_sum();
            return array($cart_url, ''); // success;
        }
    } else if ($cmd === 'cancel') {
        if ($commodities[$id]) {
            unset($commodities[$id]);
            $_SESSION[$sname]['commodities'] = $commodities;
            cfshoppingcart_sum();
            return array($cart_url, ''); // success;
        }
    }
    cfshoppingcart_error_exit();
}

function cfshoppingcart_main() {
    //print_r($_GET);

    // command
    if (!$cmd = $_REQUEST['cmd']) cfshoppingcart_error_exit();
    // 商品ID (post id)
    if (!$id = $_REQUEST['include']) cfshoppingcart_error_exit();
    // 注文数
    //if (!$quantity = $_REQUEST['quantity']) cfshoppingcart_error_exit();
    if (!array_key_exists('quantity', $_REQUEST)) cfshoppingcart_error_exit();
    $quantity = intval($_REQUEST['quantity']);

    // カスタムフィールドの情報を得る
    $c = get_post_custom($id);
    //print_r($c);
    if (!$c) cfshoppingcart_error_exit();

    // これまでの注文情報に加える
    list($html, $msg) = cfshoppingcart_work($cmd, $id, $quantity, $c);
    
    // $html : Sidebar widget message will be overwrite to $html by Javascript.
    // $msg : Javascript put alert msg.
    $j = array($html, $msg);

    // Javascript へ送る為に JSON 形式に変換
    require_once('../JSON/JSON.php');
    $json = new Services_JSON;
    $encode = $json->encode($j);
    //cfshoppingcart_error_exit();
    //echo '['. $encode . ']';
    echo $encode;
    exit;
}
?>