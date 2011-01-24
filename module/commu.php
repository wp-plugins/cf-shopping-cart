<?php
/* commu.php
 * communication module
 * Return Custom-field data.
 *
 * -*- Encoding: utf8n -*-
 *
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
require_once($wp_fullpath . '/wp-load.php');
require_once('sum.php');
//
cfshoppingcart_main();
cfshoppingcart_error_exit();

function cfshoppingcart_error_exit() {
    $j = 'error';
    require_once('../JSON/JSON.php');
    $json = new Services_JSON;
    $encode = $json->encode($j);
    //cfshoppingcart_error_exit();
    //echo '['. $encode . ']';
    echo $encode;
    //return 'error';
    exit(false);
}

function cfshoppingcart_session_start() {
    $sid = session_id();
    if (!$sid) session_start();
}

function cfshoppingcart_work($cmd, $id, $quantity, $customfield) {
    if (0) {
        $_SESSION = array();
        session_destroy();
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
        // Stock
        // get number of stock
        $number_of_stock_field_name = $model->getNumberOfStockFieldName();
        if ($number_of_stock_field_name) {
            $post_custom = get_post_custom($id);
            $number_of_stock = $post_custom[$number_of_stock_field_name][0];
            // Check number of stock
            if ($number_of_stock == 0) {
                // stock is no.
                //if (($commodities[$id]['quantity'] + $quantity) > $number_of_stock) {
                $msg = array('msg_red' => __('Sorry, stock is no.','cfshoppingcart'),
                             'widget' => $_SESSION[$sname]['sum']['html'],
                             //'cart_html' => cfshoppingcart_cart(array('commu'))
                             );
                return ($msg);
                //}
            } else if ($number_of_stock > 0) {
                // order is out of stock
                if (($commodities[$id]['quantity'] + $quantity) > $number_of_stock) {
                    $msg = array('msg_red' => __('Out of stock','cfshoppingcart'),
                                 'widget' => $_SESSION[$sname]['sum']['html'],
                                 //'cart_html' => cfshoppingcart_cart(array('commu'))
                                 );
                    return ($msg);
                }
            }
        }
        // Check total quantity
        if ($max_quantity_of_total_order != 0 && ($total_quantity + $quantity) > $max_quantity_of_total_order) {
            $msg = array('msg_red' => __('Max quantity of total order is','cfshoppingcart') . ' ' . $max_quantity_of_total_order,
                         'widget' => $_SESSION[$sname]['sum']['html'],
                         //'cart_html' => cfshoppingcart_cart(array('commu'))
                         );
            return ($msg);
        }
        if (!$commodities[$id]) {
            foreach ($customfield as $key => $value) {
                $commodities[$id][$key] = $value[0];
            }
            $commodities[$id]['quantity'] = 0;
        }
        // Check quantity of one commodity
        if ($max_quantity_of_one_commodity != 0 && ($commodities[$id]['quantity'] + $quantity) > $max_quantity_of_one_commodity) {
            $msg = array('msg_red' => __('Max quantity of one commodity is','cfshoppingcart') . ' ' . $max_quantity_of_one_commodity,
                         'widget' => $_SESSION[$sname]['sum']['html'],
                         //'cart_html' => cfshoppingcart_cart(array('commu'))
                         );
            return ($msg);
        }
        $commodities[$id]['quantity'] += $quantity;
        $_SESSION[$sname]['commodities'] = $commodities;
        cfshoppingcart_sum();
        $msg = array('msg' => 'Item to cart.',
                     'widget' => $_SESSION[$sname]['sum']['html'],
                     //'cart_html' => cfshoppingcart_cart(array('commu'))
                     );
        return ($msg);
    } else if ($cmd === 'change_quantity') {
        if ($commodities[$id]) {
            if ($quantity == 0) {
                unset($commodities[$id]);
                $_SESSION[$sname]['commodities'] = $commodities;
                cfshoppingcart_sum();
                if ($commodities) {
                    $msg = array('msg' => __('Off the item.','cfshoppingcart'),
                                 'widget' => $_SESSION[$sname]['sum']['html'],
                                 'cart_html' => cfshoppingcart_cart(array('commu'))
                                 );
                } else {
                    $msg = array('msg' => __('Shopping Cart is empty.','cfshoppingcart'),
                                 'widget' => $_SESSION[$sname]['sum']['html'],
                                 'cart_html' => cfshoppingcart_cart(array('commu'))
                                 );
                }
                return ($msg);
                //return array($cart_url, '');
            }
            // Stock
            // get number of stock
            $number_of_stock_field_name = $model->getNumberOfStockFieldName();
            if ($number_of_stock_field_name) {
                $post_custom = get_post_custom($id);
                $number_of_stock = $post_custom[$number_of_stock_field_name][0];
                // Check number of stock
                if ($number_of_stock == 0) {
                    // stock is no.
                    $msg = array('msg_red' => __('Sorry, stock is no.','cfshoppingcart'),
                                 'widget' => $_SESSION[$sname]['sum']['html'],
                                 'cart_html' => cfshoppingcart_cart(array('commu'))
                                 );
                    //return array($_SESSION[$sname]['sum']['html'], $msg);
                    //return array($cart_url, $msg);
                    return ($msg);
                } else if ($number_of_stock > 0) {
                    // order is out of stock
                    //if (($commodities[$id]['quantity'] + $quantity) > $number_of_stock) {
                    if ($quantity > $number_of_stock) {
                        $msg = array('msg_red' => __('Out of stock','cfshoppingcart'),
                                     'widget' => $_SESSION[$sname]['sum']['html'],
                                     'cart_html' => cfshoppingcart_cart(array('commu'))
                                     );
                        //return array($_SESSION[$sname]['sum']['html'], $msg);
                        return ($msg);
                        //return array($cart_url, $msg);
                    }
                }
            }
            // Check total quantity
            if ($max_quantity_of_total_order != 0 && ($total_quantity + $quantity - $commodities[$id]['quantity']) > $max_quantity_of_total_order) {
                $msg = array('msg_red' => __('Max quantity of total order is','cfshoppingcart') . ' ' . $max_quantity_of_total_order,
                             'widget' => $_SESSION[$sname]['sum']['html'],
                             'cart_html' => cfshoppingcart_cart(array('commu'))
                             );
                return ($msg);
            }
            // Check quantity of one commodity
            if ($max_quantity_of_one_commodity != 0 && $quantity > $max_quantity_of_one_commodity) {
                $msg = array('msg_red' => __('Max quantity of one commodity is','cfshoppingcart') . ' ' . $max_quantity_of_one_commodity,
                             'widget' => $_SESSION[$sname]['sum']['html'],
                             'cart_html' => cfshoppingcart_cart(array('commu'))
                             );
                return ($msg);
            }
            $commodities[$id]['quantity'] = $quantity;
            $_SESSION[$sname]['commodities'] = $commodities;
            cfshoppingcart_sum();
            $msg = array('msg' => __('Quantity has changed','cfshoppingcart'),
                         'widget' => $_SESSION[$sname]['sum']['html'],
                         'cart_html' => cfshoppingcart_cart(array('commu'))
                         );
            return ($msg);
            //return array($cart_url, ''); // success;
        }
    } else if ($cmd === 'cancel') {
        if ($commodities[$id]) {
            unset($commodities[$id]);
            $_SESSION[$sname]['commodities'] = $commodities;
            cfshoppingcart_sum();
            require_once('cart.php');
            if ($commodities) {
                $msg = array('msg' => __('Off the item','cfshoppingcart'),
                             'widget' => $_SESSION[$sname]['sum']['html'],
                             'cart_html' => cfshoppingcart_cart(array('commu'))
                             );
            } else {
                $msg = array('msg' => __('Shopping Cart is empty.','cfshoppingcart'),
                             'widget' => $_SESSION[$sname]['sum']['html'],
                             'cart_html' => cfshoppingcart_cart(array('commu'))
                             );
            }
            return ($msg);
            //return array($cart_url, ''); // success;
        }
    } else if ($cmd === 'empty_cart') {
        cfshoppingcart_stock();
        //$_SESSION[$sname]['commodities'] = array();
        unset($_SESSION[$sname]['commodities']);
        //$_SESSION[$sname] = array();
        cfshoppingcart_sum();
        $msg = array('widget' => $_SESSION[$sname]['sum']['html']);
        return ($msg);
    }
    cfshoppingcart_error_exit();
}

function cfshoppingcart_stock() {
    $sname = 'cfshoppingcart';
    // get data object
    $WpCFShoppingcart = & new WpCFShoppingcart();
    $model = $WpCFShoppingcart->model;
    
    $number_of_stock_field_name = $model->getNumberOfStockFieldName();
    if (!$number_of_stock_field_name) return;
    
    $commodities = $_SESSION[$sname]['commodities'];
    foreach ($commodities as $post_id => $commodity) {
        $checkout_num = $commodity['quantity'];
        $post_custom = get_post_custom($post_id);
        $num = $post_custom[$number_of_stock_field_name][0];
        if ($num < 0) return; // the product is not use stock.
        $num -= $checkout_num;
        if ($num < 0) $num = 0;
        if (update_post_meta($post_id, $number_of_stock_field_name, $num, $post_custom[$number_of_stock_field_name][0]) == false) {
            // error
        }
        if ($model->getTypeOfShowSoldOutMessage() === 'dont_show_the_product' && $num == 0) {
            cfshoppingcart_to_be_private_the_post($post_id);
        }
    }
}

function cfshoppingcart_to_be_private_the_post($post_id){
    //print_r($menu);

    //$wpdb = $GLOBALS['wpdb'];
    global $wpdb;

    //$v = 'publish';
    $v = 'private';
    
    $wpdb->update($wpdb->posts, array('post_status' => $v), array('ID' => $post_id), array('%s'), array('%d'));
    if ($wpdb->$wpdb->last_error) {
        echo '<p>Error: cfshoppingcart_to_be_private_the_post: ' . $wpdb->last_error . '</p>';
    }
    
    //$sql = "UPDATE $wpdb->posts SET post_status = '$v' WHERE ID = $post_id;";
    //$wpdb->query($sql);
}

function cfshoppingcart_main() {
    //print_r($_GET);

    // command
    if (array_key_exists('add_to_cart', $_REQUEST)) {
        $cmd = 'add_to_cart';
    } else if (array_key_exists('change_quantity', $_REQUEST)) {
        $cmd = 'change_quantity';
    } else if (array_key_exists('cancel', $_REQUEST)) {
        $cmd = 'cancel';
    } else if (array_key_exists('empty_cart', $_REQUEST)) {
        $cmd = 'empty_cart';
    } else if (!$cmd = $_REQUEST['cmd']) {
        cfshoppingcart_error_exit();
    }
    // 商品ID (post id)
    if (!$id = $_REQUEST['include']) cfshoppingcart_error_exit();
    // 注文数
    if (!array_key_exists('quantity', $_REQUEST)) cfshoppingcart_error_exit();
    $quantity = intval($_REQUEST['quantity']);

    // カスタムフィールドの情報を得る
    $c = get_post_custom($id);
    //print_r($c);
    //if (!$c) cfshoppingcart_error_exit(); // empty_cart

    // これまでの注文情報に加える
    //list($html, $msg) = cfshoppingcart_work($cmd, $id, $quantity, $c);
    $j = cfshoppingcart_work($cmd, $id, $quantity, $c);
    //$html = 'a'; $msg='b';
    
    // $html : Sidebar widget message will be overwrite to $html by Javascript.
    // $msg : Javascript put alert msg.
    //$j = array($html, $msg);

    if (0) {
    // Javascript へ送る為に JSON 形式に変換
    require_once('../Jsphon/Jsphon.php');
    $json = Jsphon::encode($j);
    echo $json;
    } else {
    require_once('../JSON/JSON.php');
    $json = new Services_JSON;
    $encode = $json->encode($j, true);
    //cfshoppingcart_error_exit();
    //echo '['. $encode . ']';
    echo $encode;
    }
    exit;
}
?>