<?php

/* commu.php
 * communication module
 * Return Custom-field data.
 *
 * -*- Encoding: utf8n -*-
 *
 */

//require_once('./common.php');
//$cfshoppingcart_common = /* php4_110323 & new */ new cfshoppingcart_common();

//$wp_fullpath = $cfshoppingcart_common->get_wp_fullpath();
//require_once($wp_fullpath . '/wp-load.php');
//$WpCFShoppingcart = /* php4_110323 & new */ new WpCFShoppingcart();
//require_once('./sum.php');
//$cfshoppingcart_commu = /* php4_110323 & new */ new cfshoppingcart_commu(dirname(__FILE__));

class cfshoppingcart_commu {
    var $_self_path;
    var $model;
    var $common;
    var $customfieldnames;
    var $postid;
    var $stock;

    function cfshoppingcart_commu() {//$self_path) {
        $this->_self_path = dirname(__FILE__);

        // get data object
        global $WpCFShoppingcart, $cfshoppingcart_common;
        $this->model = $WpCFShoppingcart->model;
        $this->customfieldnames = $this->model->getCustomFields();
        $this->common = $cfshoppingcart_common;
        
        //$this->cfshoppingcart_session_start();
        //$this->cfshoppingcart_main();
        //$this->error_exit();
    }
    
    function commu($cmd, $id, $quantity, $customfield, $id2) {
        if (0) {
            $_SESSION = array();
            session_destroy();
            $_SESSION = array();
            
            unset($_SESSION[$sname]);
        }

        $id = $id2;

        $price_field_name = $this->model->getPriceFieldName();
        $cart_url = $this->model->getCartUrl();
        $max_quantity_of_one_commodity = $this->model->getMaxQuantityOfOneCommodity();
        $max_quantity_of_total_order = $this->model->getMaxQuantityOfTotalOrder();
        $number_of_stock_field_name = $this->model->getNumberOfStockFieldName();

        $sname = 'cfshoppingcart';
        $commodities = &$_SESSION[$sname]['commodities'];
        $total_quantity = $_SESSION[$sname]['sum']['quantity_of_commodity'];
        
        if ($cmd === 'add_to_cart') {
            // Check number of stock
            if ($this->stock['num'] == 0) {
                // stock is no.
                $msg = array('msg_red' => __('Sorry, stock is no.', 'cfshoppingcart'),
                             'widget' => $_SESSION[$sname]['sum']['html'],
                             //'cart_html' => cfshoppingcart_cart(array('commu'))
                             );
                return ($msg);
            } else if ($this->stock['num'] > 0) {
                // order is out of stock
                if (($commodities[$id]['quantity'] + $quantity) > $this->stock['num'] || ($_SESSION[$sname]['incart_stock'][$this->stock['key']] + $quantity) > $this->stock['num']) {
                    $msg = array('msg_red' => __('Out of stock', 'cfshoppingcart'),
                                 'widget' => $_SESSION[$sname]['sum']['html'],
                                 //'cart_html' => cfshoppingcart_cart(array('commu'))
                                 );
                    return ($msg);
                }
            }
            // Check total quantity
            if ($max_quantity_of_total_order != 0 && ($total_quantity + $quantity) > $max_quantity_of_total_order) {
                $msg = array('msg_red' => __('Max quantity of total order is', 'cfshoppingcart') . ' ' . $max_quantity_of_total_order,
                             'widget' => $_SESSION[$sname]['sum']['html'],
                             //'cart_html' => cfshoppingcart_cart(array('commu'))
                             );
                return ($msg);
            }
            if (!$commodities[$id]) {
                //echo $id; exit;
                //echo $this->stock['num']; exit;
                $commodity = $this->customfield_to_commodity($customfield, $number_of_stock_field_name, $this->stock['num']);
                //echo $commodity;exit;
                if (is_array($commodity)) {
                    $commodities[$id] = $commodity;
                } else {
                    // error
                    $msg = array('msg_red' => $commodity,
                                 'widget' => $commodity,
                                 'cart_html' => $commodity
                                 );
                    return ($msg);
                }
            }
            // Check quantity of one commodity
            if ($max_quantity_of_one_commodity != 0 && ($commodities[$id]['quantity'] + $quantity) > $max_quantity_of_one_commodity) {
                $msg = array('msg_red' => __('Max quantity of one commodity is', 'cfshoppingcart') . ' ' . $max_quantity_of_one_commodity,
                             'widget' => $_SESSION[$sname]['sum']['html'],
                             //'cart_html' => cfshoppingcart_cart(array('commu'))
                );
                return ($msg);
            }
            //echo $id;exit;
            $commodities[$id]['quantity'] += $quantity;
            // Change in cart stock
            $_SESSION[$sname]['incart_stock'][$this->stock['key']] += $quantity;
            $_SESSION[$sname]['commodities'] = $commodities;
            cfshoppingcart_sum();
            $msg = array('msg' => __('Item to cart.', 'cfshoppingcart'),
                'widget' => $_SESSION[$sname]['sum']['html'],
                    //'cart_html' => cfshoppingcart_cart(array('commu'))
            );
            return ($msg);
        } else if ($cmd === 'change_quantity') {
            if (!$commodities[$id]) {
                $msg = array('msg_red' => __('Change quantity is faild.', 'cfshoppingcart'),
                             //'widget' => $_SESSION[$sname]['sum']['html'],
                             //'cart_html' => cfshoppingcart_cart(array('commu'))
                             );
                return ($msg);
            }
            if ($quantity == 0) {
                // Change in cart stock
                $_SESSION[$sname]['incart_stock'][$this->stock['key']] -= $commodities[$id]['quantity'];
                unset($commodities[$id]);
                $_SESSION[$sname]['commodities'] = $commodities;
                cfshoppingcart_sum();
                if ($commodities) {
                    $msg = array('msg' => __('Off the item.', 'cfshoppingcart'),
                                 'widget' => $_SESSION[$sname]['sum']['html'],
                                 'cart_html' => cfshoppingcart_cart(array('commu'))
                                 );
                } else {
                    $msg = array('msg' => __('Shopping Cart is empty.', 'cfshoppingcart'),
                                 'widget' => $_SESSION[$sname]['sum']['html'],
                                 'cart_html' => cfshoppingcart_cart(array('commu'))
                                 );
                }
                return ($msg);
            }
            // Check number of stock
            if ($this->stock['num'] == 0) {
                // stock is no.
                $msg = array('msg_red' => __('Sorry, stock is no.', 'cfshoppingcart'),
                             'widget' => $_SESSION[$sname]['sum']['html'],
                             'cart_html' => cfshoppingcart_cart(array('commu'))
                             );
                return ($msg);
            } else if ($this->stock['num'] > 0) {
                // order is out of stock
                if ($quantity > $this->stock['num'] || ($_SESSION[$sname]['incart_stock'][$this->stock['key']] - $commodities[$id]['quantity'] + $quantity) > $this->stock['num']) {
                //if ($quantity > $this->stock['num']) {
                    $msg = array('msg_red' => __('Out of stock', 'cfshoppingcart'),
                                 'widget' => $_SESSION[$sname]['sum']['html'],
                                 'cart_html' => cfshoppingcart_cart(array('commu'))
                                 );
                    return ($msg);
                }
            }
            // Check total quantity
            if ($max_quantity_of_total_order != 0 && ($total_quantity + $quantity - $commodities[$id]['quantity']) > $max_quantity_of_total_order) {
                $msg = array('msg_red' => __('Max quantity of total order is', 'cfshoppingcart') . ' ' . $max_quantity_of_total_order,
                             'widget' => $_SESSION[$sname]['sum']['html'],
                             'cart_html' => cfshoppingcart_cart(array('commu'))
                             );
                return ($msg);
            }
            // Check quantity of one commodity
            if ($max_quantity_of_one_commodity != 0 && $quantity > $max_quantity_of_one_commodity) {
                $msg = array('msg_red' => __('Max quantity of one commodity is', 'cfshoppingcart') . ' ' . $max_quantity_of_one_commodity,
                             'widget' => $_SESSION[$sname]['sum']['html'],
                             'cart_html' => cfshoppingcart_cart(array('commu'))
                             );
                return ($msg);
            }
            // Change in cart stock
            $_SESSION[$sname]['incart_stock'][$this->stock['key']] = $_SESSION[$sname]['incart_stock'][$this->stock['key']] - $commodities[$id]['quantity'] + $quantity;
            $commodities[$id]['quantity'] = $quantity;
            $_SESSION[$sname]['commodities'] = $commodities;
            cfshoppingcart_sum();
            $msg = array('msg' => __('Quantity has changed', 'cfshoppingcart'),
                         'widget' => $_SESSION[$sname]['sum']['html'],
                         'cart_html' => cfshoppingcart_cart(array('commu'))
                         );
            //echo 'a';exit;
            return ($msg);     // success;
        } else if ($cmd === 'cancel') {
            if ($commodities[$id]) {
                // Change in cart stock
                $_SESSION[$sname]['incart_stock'][$this->stock['key']] -= $commodities[$id]['quantity'];
                //
                unset($commodities[$id]);
                $_SESSION[$sname]['commodities'] = $commodities;
                cfshoppingcart_sum();
                require_once($this->_self_path . '/cart.php');
                if ($commodities) {
                    $msg = array('msg' => __('Off the item', 'cfshoppingcart'),
                                 'widget' => $_SESSION[$sname]['sum']['html'],
                                 'cart_html' => cfshoppingcart_cart(array('commu'))
                                 );
                } else {
                    $msg = array('msg' => __('Shopping Cart is empty.', 'cfshoppingcart'),
                                 'widget' => $_SESSION[$sname]['sum']['html'],
                                 'cart_html' => cfshoppingcart_cart(array('commu'))
                                 );
                }
                return ($msg);  // success;
            }
        /*} else if ($cmd === 'empty_cart') {
            $this->update_stock();
            unset($_SESSION[$sname]['commodities']);
            cfshoppingcart_sum();
            $msg = array('widget' => $_SESSION[$sname]['sum']['html']);
            return ($msg);*/
        }
        //$this->error_exit();
        return;
    }

    function cfshoppingcart_empty_cart() {
        $sname = 'cfshoppingcart';
        $this->update_stock();
        unset($_SESSION[$sname]);
        //unset($_SESSION[$sname]['commodities']);
        //unset($_SESSION[$sname]['incart_stock']);
        cfshoppingcart_sum();
    }
    

    /*
     * return:
     *  success: array
     *  error: no array
     */
    function customfield_to_commodity($customfield, $number_of_stock_field_name, $number_of_stock) {
        //echo $number_of_stock_field_name; exit;
        //echo $number_of_stock; exit;
        foreach ($this->customfieldnames as $customfieldnames_index => $key) {
            $value = $customfield[$key][0];
            if (strstr($value, '#hidden')) {
                $commodity[$key] = $value;
                continue;
            }
            $value = str_replace('#postid', sprintf($this->model->getPostidFormat(), $this->postid), $value);
            if ($number_of_stock_field_name && $key === $number_of_stock_field_name) {
                $commodity[$number_of_stock_field_name] = $number_of_stock;
            } else if (preg_match('/^#select/', $value)) {
                // check select value
                //$value = str_replace("\r\n", "\n", $value);
                //$value = str_replace("\r", "\n", $value);
                $value = $this->common->clean_cf_textarea($value);
                $a = array_flip(explode("\n", $value));
                if (array_key_exists($_POST[$key], $a)) {
                    // check extra charges
                    if (preg_match('/^(.*)=(-{0,1}[0-9]*|-{0,1}[0-9]*\.[0-9]*)$/', $_POST[$key], $match)) {
                        $commodity[$key] = preg_replace('/_/', ' ', trim($match[1], 1));
                        $commodity['extra_charges'] += $match[2];
                    } else {
                        $commodity[$key] = $_POST[$key];
                    }
                    // for stock
                    if ($_POST[$key]) $stock_key .= '_';
                    $stock_key .= $_POST[$key];
                } else {
                    // Irregular post value
                    //return false;
                    return 'System error: key = ' . $key . ', _REQUEST = ' . $_POST[$key];
                }
            } else {
                $commodity[$key] = $value;
            }
        }
        // check extra charges
        $price_field_name = $this->model->getPriceFieldName();
        $commodity[$price_field_name] += $commodity['extra_charges'];
        //
        $commodity['quantity'] = 0;
        return $commodity;
    }
    
    /* update number of stock */
    function update_stock() {
        $sname = 'cfshoppingcart';
        
        $commodities = $_SESSION[$sname]['commodities'];
        foreach ($commodities as $post_id => $commodity) {
            // get real post id and stock key.
            list($postid, $stock_key) = $this->common->get_real_postid_and_stock_key($post_id);
            
            $quantity = $commodity['quantity'];
            // get number of stock array
            $stock = $this->common->get_cf_stock($post_id);
            if ($stock['num'] == -1) {
                // the product is not use stock manage.
                continue;
            }
            $now_stock = $stock['num'] - $quantity;
            if ($now_stock < 0) {
                $now_stock = 0;
            }

            // set now number of stock to Custom Field
            $this->common->set_cf_stock($postid, $stock['key'], $now_stock);
            
            // the post to private when sold out.
            if ($now_stock == 0 && $this->model->getTypeOfShowSoldOutMessage() === 'dont_show_the_product' && $this->common->is_stock_zero($post_id)) {
                //return;
                //echo $now_stock;exit;
                //echo $postid;exit;
                $this->change_post_status_to_private($postid);
            }
        }
    }

    function change_post_status_to_private($post_id) {
        global $wpdb;

        //$v = 'publish';
        $v = 'private';

        $wpdb->update($wpdb->posts, array('post_status' => $v), array('ID' => $post_id), array('%s'), array('%d'));
        if ($wpdb->last_error) {
            return '<p>Error: to_be_private_the_post: ' . $wpdb->last_error . '</p>';
        }
    }

    function cfshoppingcart_main() {
        //print_r($_GET);
        
        // command
        if (array_key_exists('add_to_cart', $_POST)) {
            $cmd = 'add_to_cart';
        } else if (array_key_exists('change_quantity', $_POST)) {
            $cmd = 'change_quantity';
        } else if (array_key_exists('cancel', $_POST)) {
            $cmd = 'cancel';
        /*} else if (array_key_exists('empty_cart', $_POST)) {
            $cmd = 'empty_cart';
          */
        } else if (!$cmd = $_POST['cmd']) {
            return;
            //$this->error_exit();
        }
        // 商品ID (post id)
        if (!$id = $_POST['include']) {
            //$this->error_exit();
            return;
        }
        $this->postid = $id;
        
        // 注文数
        if (!array_key_exists('quantity', $_POST)) {
            //$this->error_exit();
            return;
        }
        $quantity = intval(preg_replace('/[^0-9]/', '', mb_convert_kana($_POST['quantity'], 'n')));
        //$quantity = intval($_POST['quantity']);

        // custom field
        //$customfield = get_post_custom($id);
        $customfield = $this->common->get_custom_fields($id);
        
        // #select
        $select = array($id);
        $i = 1;
        foreach ($this->customfieldnames as $index2 => $fieldname) {
            $value = $customfield[$fieldname][0];
            if (!preg_match('/^#select/', $value)) { continue; }
            $select[$i] = ''; // default value
            //$value = str_replace("\r\n", "\n", $value);
            //$value = str_replace("\r", "\n", $value);
            $value = $this->common->clean_cf_textarea($value);
            $items = explode("\n", $value);
            foreach ($items as $index => $item) {
                $item = trim($item);
                if (!$item) { continue; }
                //echo $_POST[$key]; exit;
                //if ($_POST[$key] === $item) {
                if ($_POST[$fieldname] === $item) {
                    //echo $item . '=' . $fieldname; exit;
                    $select[$i] = $item;
                    break;
                }
            }
            $i++;
        }
        $id2 = preg_replace('/\|*$/', '', join('|', $select));
        $this->stock = $this->common->get_cf_stock($id2);//, $stock_key);
        //echo $stock['num'];exit;
        //echo $stock['key'];exit;
        //echo $cmd;exit;
        //echo $id;exit;
        //echo $id2;exit;
        //echo $quantity;exit;
        //echo print_r($customfield);exit;
        if (is_null($this->stock)) {
            $j = array('msg_red' => __('Sorry, stock is no.','cfshoppingcart'));
        } else if (!is_array($this->stock)) {
            $j = array('msg_red' => __('Error: Check Stock Custom Field value.','cfshoppingcart'));
        } else if (!$this->stock) {
            /*
            if (is_null($this->stock)) {
                $j = array('msg_red' => 'System error: stock is null.');
            } else if (!is_array($this->stock)) {
                $j = array('msg_red' => 'System error: stock is not array.');
            }
              */
        } else {
            $j = $this->commu($cmd, $id, $quantity, $customfield, $id2);
        }

        if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            if (0) {
                // Javascript へ送る為に JSON 形式に変換
                require_once($this->_self_path . '/../Jsphon/Jsphon.php');
                $json = Jsphon::encode($j);
                return $json;
            } else {
                require_once($this->_self_path . '/../JSON/JSON.php');
                $json = /* php4_110323 & new */ new Services_JSON;
                $encode = $json->encode($j, true);
                return $encode;
            }
        } else {
            return $j;
        }
    }

}

?>