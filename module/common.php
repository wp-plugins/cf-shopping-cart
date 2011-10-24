<?php

/*
 * common.php
 * -*- Encoding: utf8n -*-
 */

class cfshoppingcart_common {
    function get_no_ajax_message() {
        // put no ajax message
        if ($_SESSION['cfshoppingcart']['no_ajax_msg']) {
            $msg_red = $_SESSION['cfshoppingcart']['no_ajax_msg']['msg_red'];
            $msg = $_SESSION['cfshoppingcart']['no_ajax_msg']['msg'];
            if ($msg_red) { $msg = $msg_red; }
            if ($msg) {
                $content .= '<div class="no_ajax_msg">' . $msg . '</div>';
            }
        }
        return $content;
    }
    
    function is_show_product() {
        //echo 'is_show_product';
        global $post;
        global $wpCFShoppingcart;
        global $cfshoppingcart_common;
        $model = $wpCFShoppingcart->model;
        
        $rf = 1;
        if ($model->getShowCommodityOnManually()) {
            $rf = 0;
            if (!$rf && $model->isShowProductsCategoryNumber($post->ID)) {
                $rf = 0;
            } else {
                $rf = 1;
            }
            if ($rf) {
                return false;
            }
        } else {
            if ($model->getShowCommodityOnHome() && is_home()) $rf = 0;
            if ($model->getShowCommodityOnPage() && is_page()) $rf = 0;
            if ($model->getShowCommodityOnArchive() && is_archive()) $rf = 0;
            if ($model->getShowCommodityOnSingle() && is_single()) $rf = 0;
            if (!$rf && $model->isShowProductsCategoryNumber($post->ID)) {
                $rf = 0;
            } else {
                $rf = 1;
            }
            if ($rf) {
                return false;
            }
        }
        
        $price_field_name = $model->getPriceFieldName();
        $c = $cfshoppingcart_common->get_custom_fields();
        
        if ((!isset($c[$price_field_name]) && !$model->getShowCustomFieldWhenPriceFieldIsEmpty()) ||
            (strstr($c[$price_field_name][0], '#hidden') && !$model->getShowCustomFieldWhenPriceFieldIsEmpty())) {
            if ($is_debug) {
                debug_cfshoppingcart('price_field_name not found in Custom Field on this post. return function.');
            }
            return false; // 単価が無い
        }
        return true;
    }
    
    /*
     * use to content instead of excerpt
     */
    function use_the_content_instead_of_the_excerpt() {
        //echo 'use_the_content_instead_of_the_excerpt';
        global $post;
        global $wpCFShoppingcart;
        global $cfshoppingcart_common;
        $model = $wpCFShoppingcart->model;

        if (!$this->is_show_product()) {
            return false;
        }
        
        $instead = false;
        if ($model->getContentInsteadOfExcerptOnHome() && is_home()) {
            $instead = true;
        } else if ($model->getContentInsteadOfExcerptOnPage() && is_page()) {
            $instead = true;
        } else if ($model->getContentInsteadOfExcerptOnArchive() && is_archive()) {
            //echo 'is_archive';exit;
            $instead = true;
        } else if ($model->getContentInsteadOfExcerptOnSingle() && is_single()) {
            $instead = true;
        } else {
            if (is_archive()) {
                $cat = $model->getContentInsteadOfExcerptOnCategoryNumbers();
                $cat_id = get_query_var('cat');
                if (in_array($cat_id, $cat)) {
                    $instead = true;
                }
            } else if (is_page()) {
                $page = $model->getContentInsteadOfExcerptOnPageNumbers();
                $page_id = get_query_var('page_id');
                if (in_array($page_id, $page)) {
                    $instead = true;
                }
            }
        }
        return $instead;
    }
    
    function get_custom_fields($postid = NULL) {
        global $post;

        global $wpCFShoppingcart;
        //$wpCFShoppingcart = new WpCFShoppingcart();
        $model = $wpCFShoppingcart->model;
        $default = $model->getCustomFieldDefaultValue();
        
        if (is_null($postid) || preg_match('/[^0-9]/', $postid)) {
            $cfs = get_post_custom();
        } else {
            $cfs = get_post_custom($postid);
        }
        if (!$default) { return $cfs; }
        
        foreach ($default as $key => $value) {
            if (!$cfs[$key]) {
                $cfs[$key][0] = $value;
            }
        }
        return $cfs;
    }
    
    function clean_cf_textarea($text) {
        // CR and LF
        $text = str_replace("\r\n", "\n", $text);
        $text = str_replace("\r", "\n", $text);
        // remove comment
        $text = preg_replace('/(^|\n);.*($|\n)/', "\n", $text);
        // remove blank line
        $text = preg_replace('/(^|\n)[\s\t]{2,}($|\n)/', '', $text);
        // remove top space
        $text = preg_replace('/(^|\n)[\s\t]*/', "\n", $text);
        // remove end space
        $text = preg_replace('/[\s\t]*($|\n)/', "\n", $text);
        // remove unnecessary new line
        $text = preg_replace('/[\n]{2,}/', "\n", $text);
        $text = preg_replace('/^\n*|\n*$/', '', $text);
        
        return $text;
    }
    
    /*
     * 'L=500|M|S=0';
     * return:
     *  array('L' => 500, 'M' => NULL, 'S' => 0);
     */
    /*
    function split_select($s) {
        $items = explode('|', $s);
        foreach ($items as $i => $item) {
            if (preg_match('/^(.*)=(-{0,1}[0-9]*)$/', $item, $match)) {
                $ret[$match[1]] = $match[2];
            } else {
                $ret[$item] = NULL;
            }
        }
        return $ret;
    }
      */
    
    /*
     * find stock key and number of stock
     *
     * $id: 55|S|Yellow
     * return:
     *  array('key' => 'S_Yellow', 'num' => 5);
     */
    //function get_stock($cf_stock, $id) {
    function get_cf_stock($id) {
        global $wpCFShoppingcart;
        //$wpCFShoppingcart = new WpCFShoppingcart();
        $model = $wpCFShoppingcart->model;
        
        // get real post id and stock key.
        $ids = explode('|', $id);
        $postid = array_shift($ids);

        // get custom field stock value: cf_stock.
        // $cf_stock: S_Yellow=5|M_Red=11|L=3 or 1 to or 0 or -1
        $number_of_stock_field_name = $model->getNumberOfStockFieldName();
        if ($number_of_stock_field_name) {
            //$cf = get_post_custom($postid);
            $cf = $this->get_custom_fields($postid);
            $cf_stock = $cf[$number_of_stock_field_name][0];
        } else {
            // the product is not stock manage.
            $cf_stock = -1;
        }

        // stock is plane number.
        if (preg_match('/^-{0,1}[0-9]*$/', $cf_stock)) {
            return array('key' => NULL, 'num' => $cf_stock);
        }
        
        // get stock key array
        $cf_stock = $this->clean_cf_textarea($cf_stock);
        $items = explode("\n", $cf_stock);
        foreach ($items as $key => $item) {
            if (!preg_match('/^(.*)=(-{0,1}[0-9]*)$/', $item, $match)) {
                continue;
            }
            $key = $match[1];
            $num = $match[2];
            $stock[$key] = $num;
            $stock_keys[] = explode('_', $key); 
            //$stock_keys[] = array_flip(explode('_', $key));
        }
        //print_r($stock_keys);exit;

        // find id from stock_keys order by.
        foreach ($stock_keys as $i => $stock_key) {
            $found_key = array();
            $found_count = 0;
            $start = 0;
            $end = count($ids);
            foreach ($stock_key as $k => $key) {
                for ($j = $start; $j < $end; $j++) {
                    // check extra charges
                    if (preg_match('/^(.*)=(-{0,1}[0-9]*|-{0,1}[0-9]*\.[0-9]*)$/', $ids[$j], $match)) {
                        $s = explode('_', $match[1]);
                        $ids[$j] = $s[0];
                    }
                    if ($ids[$j] === $key) {
                        $found_key[] = $key;
                        $start = $j + 1;
                        $found_count++;
                        break;
                    }
                }
            }
            //print_r($found_key);
            //echo 'fountd_count = ' . $found_count . ', count found_key = ' . count($found_key);
            //print_r($found_key);
            if (count($found_key) == $found_count) {
                $key = join('_', $found_key);
                if (array_key_exists($key, $stock)) {
                    return array('key' => $key, 'num' => $stock[$key]);
                }
            }
        }
        // number of stock not found.
        return NULL;
    }


    function is_stock_zero($postid) {
        global $wpCFShoppingcart;
        $model = $wpCFShoppingcart->model;

        // get real post id and stock key.
        list($postid, $stock_key) = $this->get_real_postid_and_stock_key($postid);
        
        $customfield = $this->get_custom_fields($postid);
        $number_of_stock_field_name = $model->getNumberOfStockFieldName();
        if (!$number_of_stock_field_name) return false;
        $stock_value = $this->split_cf_text($customfield[$number_of_stock_field_name][0]);
        //print_r($stock_value);
        foreach ($stock_value as $key => $value) {
            //if (is_null($value)) continue;
            if (preg_match('/^-{0,1}[0-9]*$/', $value)) {
                if ($value != 0) return false;
            }
            if (preg_match('/^-{0,1}[0-9]*$/', $key)) {
                if ($key) return false;
            }
        }
        return true;
    }
    
    // get real post id and stock key.
    // list($postid, $stock_key) = $this->get_real_postid_and_stock_key($postid);
    function get_real_postid_and_stock_key($postid) {
        // get real post id and stock key.
        if (preg_match('/^([0-9]*)\|(.*)$/', $postid, $match)) {
            $postid = $match[1];
            $stock_key = str_replace('|', '_', $match[2]);
        }
        return array($postid, $stock_key);
    }
    
    /*
     * $postid: integer
     * $stock_num: change stock to this value
     * return:
     *   product is not stock manage: -1
     *   success: $stock_num
     *   failed: -255
     */
    function set_cf_stock($postid, $stock_key, $stock_num) {
        //echo "function set_cf_stock($postid, $stock_key, $stock_num) {";
        if (!preg_match('/^-{0,1}[0-9]*$/', $postid)) {
            echo '<p>set_cf_stock: postid is not integer.</p>';
            return -255;
        }
        global $wpCFShoppingcart;
        //$wpCFShoppingcart = new WpCFShoppingcart();
        $model = $wpCFShoppingcart->model;
        
        $number_of_stock_field_name = $model->getNumberOfStockFieldName();
        // the product is not stock manage.
        //echo 'number_of_stock_field_name = '.$number_of_stock_field_name; exit;
        if (!$number_of_stock_field_name) {
            return -1;
        }
        //$customfield = get_post_custom($postid);
        $customfield = $this->get_custom_fields($postid);
        
        $cf_stock_value = $customfield[$number_of_stock_field_name][0];
        // CR and LF
        $cf_stock_value = str_replace("\r\n", "\n", $cf_stock_value);
        $cf_stock_value = str_replace("\r", "\n", $cf_stock_value);
        if ($stock_key) {
            $regx = '/(^|\n)' . $stock_key . '=[0-9]*(\n|$)/';
            $strx = '${1}' . $stock_key . '=' . $stock_num . '${2}';
            $cf_stock_value = preg_replace($regx, $strx, $cf_stock_value, 1);
        } else {
            //$regx = '/(^|\n)' . $stock_num . '(\n|$)/';
            $regx = '/(^|\n)[0-9]*(\n|$)/';
            $strx = '${1}' . $stock_num . '${2}';
            $cf_stock_value = preg_replace($regx, $strx, $cf_stock_value, 1);
        }
        //$stock_str = $this->join_cf_array($stock_value);
        //echo "if (update_post_meta($postid, $number_of_stock_field_name, $cf_stock_value) == false) {";
        if (update_post_meta($postid, $number_of_stock_field_name, $cf_stock_value) == false) {
            // error
            echo '<p>Error: can not update meta.</p>';
            return -255;
        }
        return $stock_num;
    }
    
    
    function get_cf_names() {
        global $wpCFShoppingcart;
        $model = $wpCFShoppingcart->model;
        return $model->getCustomFields();
    }

    /*
     * text: #select|#hidden|L_Red=1|M_Yellow=-1
     * return: array(
     *   [#select] => NULL
     *   [#hidden] => NULL
     *   [L_Red] => 1
     *   [M_Yellow] => -1
     * );
     */
    function split_cf_text($text) {
        //$text = str_replace("\r\n", "\n", $text);
        //$text = str_replace("\r", "\n", $text);
        $text = $this->clean_cf_textarea($text);
        $items = explode("\n", $text);
        
        $cf = array();
        if (!is_array($items)) {
            $cf[$items] = NULL;
            return $cf;
        }
        
        foreach ($items as $index => $item) {
            //if (preg_match('/^(.*)=(-{0,1}[0-9]*?)$/', $item, $match)) {
            if (preg_match('/^(.*?)=(.*)$/', $item, $match)) {
                $cf[$match[1]] = $match[2];
            } else {
                $cf[$item] = NULL;
            }
        }
        return $cf;
    }

    
    /* directory *************************/
    
    /* ex: http://wordpress/wp-content/plugins/this_plugin */

    function get_plugin_uri() {
        $p = $this->get_plugin_path();
        $first = substr($p, 0, 1);
        if ($first !== '/')
            $p = '/' . $p;
        return get_settings('siteurl') . $p;
    }

    /* ex: http://wordpress/wp-content/plugins/this_plugin/module */

    function get_plugin_module_uri() {
        return $this->get_plugin_uri() . '/module';
    }

    /* ex: /home/user/public_html/wordpress/wp-content/plugins/this_plugin */

    function get_plugin_fullpath() {
        $path = '';
        $cpath = $this->get_current_path();
        $first = substr($cpath, 0, 1);
        if ($first === '/')
            $path = $first;
        $f = explode('/', $cpath);
        $max = count($f);
        foreach ($f as $i => $p) {
            if ($p === 'module') {
                return $path;
            }
            if ($i == 0 && $p === '') {
                $path = '/';
                continue;
            }
            if ($path !== '' && $path !== '/')
                $path .= '/';
            $path .= $p;
        }
        return $path;
    }

    /* ex: /home/user/public_html/wordpress */

    function get_wp_fullpath() {
        $path = '';
        $cpath = $this->get_current_path();
        $first = substr($cpath, 0, 1);
        if ($first === '/')
            $path = $first;
        $f = explode('/', $cpath);
        $max = count($f);
        foreach ($f as $i => $p) {
            if ($p === 'wp-content') {
                return $path;
            }
            if ($i == 0 && $p === '') {
                $path = '/';
                continue;
            }
            if ($path !== '' && $path !== '/')
                $path .= '/';
            $path .= $p;
        }
        return $path;
    }

    /* ex: /home/user/public_html/wordpress/wp-content */

    function get_wp_content_fullpath() {
        return $this->get_wp_fullpath() . '/wp-content';
    }

    /* ex: /wp-content/plugins/this_plugin */

    function get_plugin_path() {
        $path = '';
        $flag = 1;
        $cpath = $this->get_current_path();
        $first = substr($cpath, 0, 1);
        if ($first === '/')
            $path = $first;
        $f = explode('/', $cpath);
        $max = count($f);
        foreach ($f as $i => $p) {
            if ($p !== 'wp-content' && $flag)
                continue;
            $flag = 0;
            if ($p === 'module') {
                return $path;
            }
            if ($i == 0 && $p === '') {
                $path = '/';
                continue;
            }
            if ($path !== '' && $path !== '/')
                $path .= '/';
            $path .= $p;
        }
        return $path;
    }

    /* ex: this_plugin */

    function get_plugin_folder() {
        $cpath = $this->get_current_path();
        $f = explode('/', $cpath);
        $max = count($f);
        for ($i = $max - 1; $i >= 0; $i--) {
            if ($f[$i] === 'module' && $i > 0) {
                return $f[$i - 1];
            }
        }
        return false;
    }
    
    /*
     * php.ini
     * -- before --
     * error_reporting = E_ALL | E_STRICT
     * -- after --
     * ;error_reporting = E_ALL | E_STRICT
     * error_reporting  =  E_ALL & ~E_NOTICE & ~E_DEPRECATED
     */
    /* ex: /home/user/public_html/wordpress/wp-content/plugins/this_plugin... */
    function get_current_path() {
        //echo "WP_PLUGIN_URL = " . WP_PLUGIN_URL;
        $current_path = (dirname(__FILE__));
        
        // sanitize for Win32 installs
        $current_path = str_replace('\\' ,'/', $current_path);
        $current_path = preg_replace('|/+|', '/', $current_path);
        return $current_path;
        //echo "<p>current_path = $current_path</p>";
    }
    
}//class


function cfshoppingcart_use_the_content_instead_of_the_excerpt_hook($content) {
    //echo 'cfshoppingcart_use_the_content_instead_of_the_excerpt_hook';
    global $post;
    global $cfshoppingcart_common;
    
    //$content = get_the_excerpt($post->ID);
    if (!$cfshoppingcart_common->use_the_content_instead_of_the_excerpt()) {
        return $content;
    }
    $content = get_the_content($post->ID);
    $content = apply_filters('the_content', $content);
    return $content;
}

?>
