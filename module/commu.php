<?php

/* commu.php
 * communication module
 * Return Custom-field data.
 *
 * -*- Encoding: utf8n -*-
 *
 */

class cfshoppingcart_commu {
    var $model;
    var $common;
    var $customfieldnames;
    var $postid;
    var $the_post;
    var $stock;
    var $pnotify;

    function cfshoppingcart_commu() {
        // get data object
        global $wpCFShoppingcart, $cfshoppingcart_common;
        $this->model = $wpCFShoppingcart->model;
        $this->customfieldnames = $this->model->getCustomFields();
        $this->common = $cfshoppingcart_common;
        //
        $pnotify_obj = new WpCFShoppingcartPnotify($wpCFShoppingcart);
        $this->pnotify = $pnotify_obj->model;
    }
    
    function commu($cmd, $id, $quantity, $customfield, $id2) {
        $id = $id2;
        $cfname = $this->common->get_session_key();

        $price_field_name = $this->model->getPriceFieldName();
        $cart_url = $this->model->getCartUrl();
        $max_quantity_of_one_commodity = $this->model->getMaxQuantityOfOneCommodity();
        $max_quantity_of_total_order = $this->model->getMaxQuantityOfTotalOrder();
        $number_of_stock_field_name = $this->model->getNumberOfStockFieldName();

        $sname = 'cfshoppingcart';
        $commodities = &$_SESSION[$cfname]['commodities'];
        $total_quantity = $_SESSION[$cfname]['sum']['quantity_of_commodity'];
        
        if ($cmd === 'add_to_cart') {
            // Check number of stock
            if ($this->stock['num'] == 0) {
                // stock is no.
                $msg = array('msg_red' => $this->pnotify->getSorryStockIsNo(),
                             'title' => $this->pnotify->getSorryStockIsNoTitle(),
                             'widget' => $_SESSION[$cfname]['sum']['html'],
                             //'cart_html' => cfshoppingcart_cart(array('commu'))
                             );
                return ($msg);
            } else if ($this->stock['num'] > 0) {
                // order is out of stock
                if (($commodities[$id]['quantity'] + $quantity) > $this->stock['num'] || ($_SESSION[$cfname]['incart_stock'][$id][$this->stock['key']] + $quantity) > $this->stock['num']) {
                    $msg = array('msg_red' => $this->pnotify->getOutOfStock(),
                                 'title' => $this->pnotify->getOutOfStockTitle(),
                                 'widget' => $_SESSION[$cfname]['sum']['html'],
                                 //'cart_html' => cfshoppingcart_cart(array('commu'))
                                 );
                    return ($msg);
                }
            }
            // Check total quantity
            if ($max_quantity_of_total_order != 0 && ($total_quantity + $quantity) > $max_quantity_of_total_order) {
                $msg = array('msg_red' => sprintf($this->pnotify->getMaxQuantity(), $max_quantity_of_total_order),
                             'title' => $this->pnotify->getMaxQuantityTitle(),
                             'widget' => $_SESSION[$cfname]['sum']['html'],
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
                $msg = array('msg_red' => sprintf($this->pnotify->getMaxQuantityProduct(), $max_quantity_of_one_commodity),
                             'title' => $this->pnotify->getMaxQuantityProductTitle(),
                             'widget' => $_SESSION[$cfname]['sum']['html'],
                             //'cart_html' => cfshoppingcart_cart(array('commu'))
                );
                return ($msg);
            }
            //echo $id;exit;
            $commodities[$id]['quantity'] += $quantity;
            // Change in cart stock
            $_SESSION[$cfname]['incart_stock'][$id][$this->stock['key']] += $quantity;
            $_SESSION[$cfname]['commodities'] = $commodities;
            cfshoppingcart_sum();
            $msg = array('msg' => $this->pnotify->getItemToCart(),
                         'title' => $this->pnotify->getItemToCartTitle(),
                         'widget' => $_SESSION[$cfname]['sum']['html'],
                         //'cart_html' => cfshoppingcart_cart(array('commu'))
                         );
            return ($msg);
        } else if ($cmd === 'change_quantity') {
            if (!$commodities[$id]) {
                $msg = array('msg_red' => $this->pnotify->getChangeQuantityIsFaild(),
                             'title' => $this->pnotify->getChangeQuantityIsFaildTitle(),
                             //'widget' => $_SESSION[$cfname]['sum']['html'],
                             //'cart_html' => cfshoppingcart_cart(array('commu'))
                             );
                return ($msg);
            }
            if ($quantity == 0) {
                // Change in cart stock
                $_SESSION[$cfname]['incart_stock'][$id][$this->stock['key']] -= $commodities[$id]['quantity'];
                unset($commodities[$id]);
                $_SESSION[$cfname]['commodities'] = $commodities;
                cfshoppingcart_sum();
                if ($commodities) {
                    $msg = array('msg' => $this->pnotify->getOffTheItem(),
                                 'title' => $this->pnotify->getOffTheItemTitle(),
                                 'widget' => $_SESSION[$cfname]['sum']['html'],
                                 'cart_html' => cfshoppingcart_cart(array('commu'))
                                 );
                } else {
                    $msg = array('msg' => $this->pnotify->getCartIsEmpty(),
                                 'title' => $this->pnotify->getCartIsEmptyTitle(),
                                 'widget' => $_SESSION[$cfname]['sum']['html'],
                                 'cart_html' => cfshoppingcart_cart(array('commu'))
                                 );
                    /*
                    $msg = array('msg' => __('Shopping Cart is empty.', 'cfshoppingcart'),
                                 'widget' => $_SESSION[$cfname]['sum']['html'],
                                 'cart_html' => cfshoppingcart_cart(array('commu'))
                                 );
                      */
                }
                return ($msg);
            }
            // Check number of stock
            if ($this->stock['num'] == 0) {
                // stock is no.
                /*
                $msg = array('msg_red' => $this->pnotify->getStockIsNo(),
                             'title' => $this->pnotify->getStockIsNoTitle(),
                             'widget' => $_SESSION[$cfname]['sum']['html'],
                             'cart_html' => cfshoppingcart_cart(array('commu'))
                             );
                  */
                $msg = array('msg_red' => $this->pnotify->getSorryStockIsNo(),
                             'title' => $this->pnotify->getSorryStockIsNoTitle(),
                             'widget' => $_SESSION[$cfname]['sum']['html'],
                             //'cart_html' => cfshoppingcart_cart(array('commu'))
                             );
                return ($msg);
            } else if ($this->stock['num'] > 0) {
                // order is out of stock
                if ($quantity > $this->stock['num'] || ($_SESSION[$cfname]['incart_stock'][$id][$this->stock['key']] - $commodities[$id]['quantity'] + $quantity) > $this->stock['num']) {
                //if ($quantity > $this->stock['num']) {
                    $msg = array('msg_red' => $this->pnotify->getOutOfStock(),
                                 'title' => $this->pnotify->getOutOfStockTitle(),
                                 'widget' => $_SESSION[$cfname]['sum']['html'],
                                 //'cart_html' => cfshoppingcart_cart(array('commu'))
                                 );
                    /*
                    $msg = array('msg_red' => __('Out of stock', 'cfshoppingcart'),
                                 'widget' => $_SESSION[$cfname]['sum']['html'],
                                 'cart_html' => cfshoppingcart_cart(array('commu'))
                                 );
                     */
                    return ($msg);
                }
            }
            // Check total quantity
            if ($max_quantity_of_total_order != 0 && ($total_quantity + $quantity - $commodities[$id]['quantity']) > $max_quantity_of_total_order) {
                $msg = array('msg_red' => sprintf($this->pnotify->getMaxQuantity(), $max_quantity_of_total_order),
                             'title' => $this->pnotify->getMaxQuantityTitle(),
                             'widget' => $_SESSION[$cfname]['sum']['html'],
                             //'cart_html' => cfshoppingcart_cart(array('commu'))
                             );
                /*
                $msg = array('msg_red' => __('Max quantity of total order is', 'cfshoppingcart') . ' ' . $max_quantity_of_total_order,
                             'widget' => $_SESSION[$cfname]['sum']['html'],
                             'cart_html' => cfshoppingcart_cart(array('commu'))
                             );
                  */
                return ($msg);
            }
            // Check quantity of one commodity
            if ($max_quantity_of_one_commodity != 0 && $quantity > $max_quantity_of_one_commodity) {
                /*
                $msg = array('msg_red' => __('Max quantity of one commodity is', 'cfshoppingcart') . ' ' . $max_quantity_of_one_commodity,
                             'widget' => $_SESSION[$cfname]['sum']['html'],
                             'cart_html' => cfshoppingcart_cart(array('commu'))
                             );
                 */
                $msg = array('msg_red' => sprintf($this->pnotify->getMaxQuantityProduct(), $max_quantity_of_one_commodity),
                             'title' => $this->pnotify->getMaxQuantityProductTitle(),
                             'widget' => $_SESSION[$cfname]['sum']['html'],
                             //'cart_html' => cfshoppingcart_cart(array('commu'))
                             );
                return ($msg);
            }
            // Change in cart stock
            $_SESSION[$cfname]['incart_stock'][$id][$this->stock['key']] = $_SESSION[$cfname]['incart_stock'][$id][$this->stock['key']] - $commodities[$id]['quantity'] + $quantity;
            $commodities[$id]['quantity'] = $quantity;
            $_SESSION[$cfname]['commodities'] = $commodities;
            cfshoppingcart_sum();
            $msg = array('msg' => $this->pnotify->getQuantityChanged(),
                         'title' => $this->pnotify->getQuantityChangedTitle(),
                         'widget' => $_SESSION[$cfname]['sum']['html'],
                         'cart_html' => cfshoppingcart_cart(array('commu'))
                         );
            //echo 'a';exit;
            return ($msg);     // success;
        } else if ($cmd === 'cancel') {
            if ($commodities[$id]) {
                // Change in cart stock
                $_SESSION[$cfname]['incart_stock'][$id][$this->stock['key']] -= $commodities[$id]['quantity'];
                //
                unset($commodities[$id]);
                $_SESSION[$cfname]['commodities'] = $commodities;
                cfshoppingcart_sum();
                require_once('cart.php');
                if ($commodities) {
                    $msg = array('msg' => $this->pnotify->getOffTheItem(),
                                 'title' => $this->pnotify->getOffTheItemTitle(),
                                 'widget' => $_SESSION[$cfname]['sum']['html'],
                                 'cart_html' => cfshoppingcart_cart(array('commu'))
                                 );
                    /*
                    $msg = array('msg' => __('Off the item', 'cfshoppingcart'),
                                 'widget' => $_SESSION[$cfname]['sum']['html'],
                                 'cart_html' => cfshoppingcart_cart(array('commu'))
                                 );
                      */
                } else {
                    $msg = array('msg' => $this->pnotify->getCartIsEmpty(),
                                 'title' => $this->pnotify->getCartIsEmptyTitle(),
                                 'widget' => $_SESSION[$cfname]['sum']['html'],
                                 'cart_html' => cfshoppingcart_cart(array('commu'))
                                 );
                }
                return ($msg);  // success;
            }
        } else if ($cmd === 'empty_cart') {
            $this->cfshoppingcart_empty_cart();
        }
        return;
    }

    function cfshoppingcart_empty_cart() {
        $cfname = $this->common->get_session_key();
        $this->update_stock();
        unset($_SESSION[$cfname]);
        //unset($_SESSION[$cfname]['commodities']);
        //unset($_SESSION[$cfname]['incart_stock']);
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
            $value = str_replace('#post_title', $this->the_post->post_title, $value);
            if ($number_of_stock_field_name && $key === $number_of_stock_field_name) {
                $commodity[$number_of_stock_field_name] = $number_of_stock;
            } else if (preg_match('/^#(select|radio)/', $value)) {
                // check select value
                $value = $this->common->clean_cf_textarea($value);
                $a = array_flip(explode("\n", $value));
                if (array_key_exists($_POST[$key], $a)) {
                    // check extra charges
                    if (preg_match('/^(.*)=(-{0,1}[0-9]*|-{0,1}[0-9]*\.[0-9]*)$/', $_POST[$key], $match)) {
                        $commodity[$key] = preg_replace('/_/', ' ', trim($match[1]), 1);
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
        $cfname = $this->common->get_session_key();
        
        $commodities = $_SESSION[$cfname]['commodities'];
        foreach ($commodities as $post_id => $commodity) {
            // get real post id and stock key.
            list($postid, $stock_key) = $this->common->get_real_postid_and_stock_key($post_id);
            
            $quantity = $commodity['quantity'];
            // get number of stock array
            $stock = $this->common->get_cf_stock($post_id);
            //print_r($stock);
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
        } else if (array_key_exists('empty_cart', $_POST)) {
            $cmd = 'empty_cart';
        } else if (!$cmd = $_POST['cmd']) {
        //} else if (!$cmd) {
            return;
            //$this->error_exit();
        }
        // 商品ID (post id)
        if (!$id = $_POST['include']) {
            //$this->error_exit();
            return;
        }
        $this->postid = $id;
        $this->the_post = get_post($id);
        
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
        
        // #select and #radio
        $select = array($id);
        $i = 1;
        foreach ($this->customfieldnames as $index2 => $fieldname) {
            $value = $customfield[$fieldname][0];
            if (!preg_match('/^#(select|radio)/', $value)) { continue; }
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
            if (function_exists(json_encode)) {
                // PHP 5 >= 5.2.0, PECL json >= 1.2.0
                $encode = json_encode($j);
                return $encode;
            } else {
                if (0) {
                    // Javascript へ送る為に JSON 形式に変換
                    require_once('../Jsphon/Jsphon.php');
                    $json = Jsphon::encode($j);
                    return $json;
                } else {
                    require_once('../JSON/JSON.php');
                    $json = new Services_JSON;
                    $encode = $json->encode($j, true);
                    return $encode;
                }
            }
        } else {
            return $j;
        }
    }

}

?>