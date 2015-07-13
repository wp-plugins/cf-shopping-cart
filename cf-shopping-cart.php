<?php

namespace cfshoppingcart;

/**
 * Plugin Name: Cf Shopping Cart
 * Plugin URI: http://cfshoppingcart.silverpigeon.jp/
 * Description: Simple shopping cart.
 * Version: 2.0.0
 * Author: AI.Takeuchi
 * Author URI: http://cfshoppingcart.silverpigeon.jp/
 * Created : September 25, 2012
 * Modified: May 6, 2015
 * Text Domain: cfshoppingcart
 * Domain Path: /languages/
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
define("DOMAIN_CF_SHOPPING_CART", 'cfshoppingcart');
define("CF_SHOPPING_CART_PLUGIN_DIR", rtrim(__DIR__, '/'));
define("CF_SHOPPING_CART_PLUGIN_URL", plugins_url() . '/' . basename(__DIR__));
define("CF_SHOPPING_CART_PRODUCT_KEY_PREFIX", 'cfshoppingcart_product_key_');
define("CF_SHOPPING_CART_OPTION_KEY_PREFIX", 'cfshoppingcart_option_key_');
// messages
define("CF_SHOPPING_CART_MSG_TRUE", true);
define("CF_SHOPPING_CART_MSG_FALSE", false);    // error
define("CF_SHOPPING_CART_MSG_UNKNOWN_ERROR", 'E9099'); //'Unknown error.' // error
//
define("CF_SHOPPING_CART_MSG_ADDED_TO_CART", 'A1010'); //'Added to cart.'
define("CF_SHOPPING_CART_MSG_QUANTITY_ADD_TO_CART_FAILED", 'E9010'); //'Add to cart failed.' // error
//
define("CF_SHOPPING_CART_MSG_HAVE_CHANGED_THE_QUANTITY", 'A1015'); //'Have changed the quantity.'
//define("CF_SHOPPING_CART_MSG_FAILED_IN_CHANGE_THE_QUANTITY", 'E9015'); //'Failed in change the quantity.' // error
//
define("CF_SHOPPING_CART_MSG_QUANTITY_WAS_DECREASED", 'A1020'); //'Quantity was decreased.'
define("CF_SHOPPING_CART_MSG_DECREASE_QUANTITY_HAS_FAILED", 'E9020'); //'Decrease quantity has failed.' // error
//
define("CF_SHOPPING_CART_MSG_QUANTITIES_WAS_MORE_THAN_ORDER_MAX_QUANTITY_OF_THE_PRODUCT", 'E9030'); //'Quantities was more than order max quantity of the product.'   // error
define("CF_SHOPPING_CART_MSG_QUANTITIES_WAS_MORE_THAN_ORDER_MAX_QUANTITY_OF_TOTAL", 'E9040'); //'Quantities was more than order max quantity of total.'   // error
define("CF_SHOPPING_CART_MSG_ORDER_QUANTITY_WAS_MORE_THAN_NUMBER_OF_STOCK", 'E9050'); //'Order quantities was more than stock quantity.' // error
define("CF_SHOPPING_CART_MSG_FAILED_IN_CHANGE_STOCK_QUANTITY", 'E9060'); //'Failed in change stock quantity.'   // error
define("CF_SHOPPING_CART_MSG_STOCK_QUANTITY_IS_EMPTY", 'E9070'); //'Stock quantity is empty.'   // error
define("CF_SHOPPING_CART_MSG_UNKNOWN_QUANTITY", 'E9080'); //'Unknown quantity.'   // error
//define("CF_SHOPPING_CART_MSG_", '');


load_textdomain(DOMAIN_CF_SHOPPING_CART, __DIR__ . '/language/' . DOMAIN_CF_SHOPPING_CART . '-' . get_locale() . '.mo');

require_once __DIR__ . '/includes/opt.php';
require_once __DIR__ . '/includes/opts.php';
require_once __DIR__ . '/includes/message.php';
include_once __DIR__ . '/includes/shipping.php';

if (is_admin()) {
    require_once __DIR__ . '/includes/admin.php';
} else {
    load_php_files();
}

function load_php_files() {
    require_once __DIR__ . '/includes/init.php';
    require_once __DIR__ . '/includes/the_content.php';

    include_once __DIR__ . '/includes/cfshoppingcart_shortcode.php';
    require_once __DIR__ . '/includes/cfshoppingcart_shortcodes.php';

    require_once __DIR__ . '/includes/check_out.php';

    require_once __DIR__ . '/includes/cart.php';

    add_filter('widget_text', 'do_shortcode');
}

function cfshoppingcart_ajax() {
    if (!check_wp_verify_nonce()) {
        return;
    }

    load_php_files();

    //debuglog("cfshoppingcart_ajax");
    //debuglog(var_export($_POST, true));

    exec_cmd();

    global $post;

    $post = get_post(getPostValue('product_id'));
    $content = apply_filters('the_content', $post->post_content);
    $result['product']['selector'] = '.' . DOMAIN_CF_SHOPPING_CART . '.product.post_id-' . $post->ID;
    $result['product']['content'] = $content;


    if (array_key_exists('shortcodes', $_SESSION[DOMAIN_CF_SHOPPING_CART]) && is_array($_SESSION[DOMAIN_CF_SHOPPING_CART]['shortcodes'])) {
        foreach ($_SESSION[DOMAIN_CF_SHOPPING_CART]['shortcodes'] as $class => $array) {
            $result['shortcodes'][$class]['selector'] = '.' . DOMAIN_CF_SHOPPING_CART . '.shortcode.' . $class;
            $result['shortcodes'][$class]['content'] = cfshoppingcart_shortcode($array['atts'], $array['content']);
        }
    }


    $result['message']['text'] = message::get_text();
    $result['message']['error'] = message::get_error_count();
    if (message::get_error_count()) {
        $result['message']['title'] = opt::get_option('message_title_if_failed');
    } else {
        $result['message']['title'] = opt::get_option('message_title');
    }

    //$result = array('a'=>'b');
    $result = json_encode($result);
    echo $result;
    message::clear();
    die();
}

if (is_admin() || !opt::get_option('disable_ajax')) {
    add_action('wp_ajax_cfshoppingcart_ajax', 'cfshoppingcart\cfshoppingcart_ajax');
    add_action('wp_ajax_nopriv_cfshoppingcart_ajax', 'cfshoppingcart\cfshoppingcart_ajax');
}



/* common function */

/* custom field */

/**
 * get custome field value
 * 
 * @param type $post_id
 * @param type $key
 * @return type
 */
function get_cf($post_id, $key) {
    if (!array_values_exists($key, opt::get_option('custom_field_names_array'))) {
        return '';
    }

    $value = get_post_meta($post_id, $key, true);

    if (!$value) {
        // get custom field default value
        $default = opt::get_option('custom_field_default_value_array');
        // use default value instead of value
        if (array_key_exists($key, $default)) {
            $value = $default[$key];
        }
    }

    // special keyword
    if (strpos($value, '{post_id}') !== false) {
        $value = str_replace('{post_id}', sprintf(opt::get_option('post_id_format'), $post_id), $value);
    }
    if (strpos($value, '{post_title}') !== false) {
        $value = str_replace('{post_title}', get_the_title($post_id), $value);
    }

    return $value;
}

function set_stock($product_key, $new_stock) {
    // $product_key: 90|色=Red|サイズ=S
    $f = explode('|', $product_key, 2);
    if (count($f) === 2) {
        $product_id = $f[0];
        $stock_id = $f[1];
    } else {
        $product_id = $f[0];
        $stock_id = '';
    }
    // custom field
    $stock_quantity_field_name = opt::get_option('stock_quantity_field_name');
    $cf = get_cf($product_id, $stock_quantity_field_name);
    $lines = explode("\n", $cf);
    /*
     * lines:
     *   50
     * 
     * or
     * 
     * lines:
     *   #select
     *   色=Red|サイズ=S|=10
     *   色=Green|サイズ=M|=20
     *   色=Blue|サイズ=M|=30
     * ...
     */
    $line = trim(array_shift($lines));
    if ($line != '#select') {
        // number only
        //return array('stock' => to_int($line), 'type' => 'number');
        update_post_meta($product_id, $stock_quantity_field_name, $new_stock); //, to_int($line));
        return true;
    }
    $update_flag = false;
    $new_cf = array('#select');
    foreach ($lines as $line) {
        $line = trim($line);
        if (!$line) {
            continue;
        }
        $g = explode('|=', $line, 2);
        if (count($g) !== 2) {
            continue;
        }
        if ($g[0] === $stock_id) {
            $new_cf[] = $g[0] . '|=' . $new_stock;
            $update_flag = true;
        } else {
            $new_cf[] = $g[0] . '|=' . $g[1];
        }
    }
    if ($update_flag) {
        update_post_meta($product_id, $stock_quantity_field_name, join("\n", $new_cf)); //, to_int($g[1]));
        return true;
    } else {
        return false;
    }
}

function get_stock($product_key) {
    // $product_key: 90|色=Red|サイズ=S
    $f = explode('|', $product_key, 2);
    if (count($f) === 2) {
        $product_id = $f[0];
        $stock_id = $f[1];
    } else {
        $product_id = $f[0];
        $stock_id = '';
    }
    // custom field
    $cf = get_cf($product_id, opt::get_option('stock_quantity_field_name'));
    $lines = explode("\n", $cf);
    /*
     * lines:
     *   50
     * 
     * or
     * 
     * lines:
     *   #select
     *   色=Red|サイズ=S|=10
     *   色=Green|サイズ=M|=20
     *   色=Blue|サイズ=M|=30
     * ...
     */
    $line = trim(array_shift($lines));
    if ($line != '#select') {
        // number only
        return array('stock' => to_int($line), 'type' => 'number');
    }
    foreach ($lines as $line) {
        $line = trim($line);
        if (!$line) {
            continue;
        }
        $g = explode('|=', $line, 2);
        if (count($g) === 2 && $g[0] === $stock_id) {
            //return to_int($g[1]);
            return array('stock' => to_int($g[1]), 'type' => 'select');
        }
    }
    //return null;
    return array('stock' => null, 'type' => 'select');
}

function get_stock_tag($product_id) {
    $cf = get_cf($product_id, opt::get_option('stock_quantity_field_name'));
    $lines = explode("\n", $cf);
    /*
     * lines:
     *   50
     * 
     * or
     * 
     * lines:
     *   #select
     *   色=Red|サイズ=S|=10
     *   色=Green|サイズ=M|=20
     *   色=Blue|サイズ=M|=30
     * ...
     */
    $line = trim(array_shift($lines));
    if ($line != '#select') {
        // number only
        $stock = to_int($line);
        if ($stock < 0) {
            return __('Many', DOMAIN_CF_SHOPPING_CART);
        }
        if (!$stock && opt::get_option('in_the_case_of_sold_out') == 'sold_out_message_on_stock') {
            return opt::get_option('sold_out_message');
        }
        return $stock;
    }

    // #select
    $stock_flag = false;
    $tag = '';
    foreach ($lines as $line) {
        $g = explode('|=', $line, 2);
        if (count($g) !== 2) {
            continue;
        }
        $stock_flag = true;
        $stock = to_int($g[1]);
        if (!$stock) {
            continue;
        }
        if ($stock < 0) {
            $stock = __('Many', DOMAIN_CF_SHOPPING_CART);
        }
        $gs = explode('|', $g[0]);
        $th = '';
        $t = '';
        foreach ($gs as $g) {
            $hs = explode('=', $g, 2);
            $t .= '<td>' . $hs[1] . '</td>';
            $th .= '<th>' . $hs[0] . '</th>';
        }
        $tag .= '<tr>' . $t . '<td>' . $stock . '</td></tr>';
    }
    if ($tag) {
        $th .= '<th>' . opt::get_option('stock_quantity_field_name') . '</th>';
        return '<table class="' . DOMAIN_CF_SHOPPING_CART . '_stock_tag"><tr>' . $th . '</tr>' . $tag . '</table>';
    } else if ($stock_flag && opt::get_option('in_the_case_of_sold_out') == 'sold_out_message_on_stock') {
        return opt::get_option('sold_out_message');
    }
    return '';
}

/*  */

function array_values_exists($value, $array) {
    if (($key = array_search($value, $array)) !== false) {
        return true;
    }
    return false;
}

function array_remove_value($value, &$array) {
    if (($key = array_search($value, $array)) !== false) {
        unset($array[$key]);
    }
}

/*  */

function name_encode($str, $prefix) {
    if (!$str) {
        return $str;
    }
    return $prefix . str_replace('%', '_', urlencode(str_replace('_', '%UB%', $str)));
}

function name_decode($str, $prefix) {
    if (!$str) {
        return false;
    }
    if (strpos($str, $prefix) !== 0) {
        return false;
    }
    return str_replace('%UB%', '_', urldecode(str_replace('_', '%', substr($str, strlen($prefix)))));
}

/* session */

function set_session_message($value) {
    $array = $_SESSION[DOMAIN_CF_SHOPPING_CART]['message'];
    if (!is_array($array)) {
        $array = array();
    }
    $array[] = $value;

    $_SESSION[DOMAIN_CF_SHOPPING_CART]['message'] = $array;
}

function get_session_message($key) {
    $array = $_SESSION[DOMAIN_CF_SHOPPING_CART]['message'];
    if (!is_array($array)) {
        $array = array();
    }
    return $array;
}

function unset_session_message() {
    $_SESSION[DOMAIN_CF_SHOPPING_CART]['message'] = array();
}

/* post */

function getPostValue($s, $support_before_redirect = false) {
    return stripslashes(get_POSTvalue($s, $support_before_redirect));
}

function get_POSTvalue($key, $support_before_redirect = false) {
    $request = $_POST;

    //debuglog(serialize($_POST));
    //debuglog(serialize($_FILES));

    if (!$request && $support_before_redirect && array_key_exists('_POST', $_SESSION[DOMAIN_CF_SHOPPING_CART])) {
        $request = $_SESSION[DOMAIN_CF_SHOPPING_CART]['_POST'];
    }

    if (is_array($request) && array_key_exists($key, $request)) {
        return $request[$key];
    }
    return null;
}

/* quantity */

function get_quantity_input_type($post_id, $default_quantity) {
    $quantity_cf = get_cf($post_id, opt::get_option('quantity_field_name'));
    if (!$quantity_cf) {
        return array('type' => '', 'value' => '', 'array' => array());
    }
    //$lines = explode("\n", $quantity_cf);
    //$first = trim(array_shift($lines));
    $lines = preg_split('/\n|\r\n?/', $quantity_cf);
    $first = array_shift($lines);
    if ($first == '#text') {
        return array('type' => 'text', 'value' => '', 'array' => $lines);
    }
    if ($first == '#number') {
        return array('type' => 'number', 'value' => '', 'array' => $lines);
    }
    if ($first == '#select') {
        return array('type' => 'select', 'value' => get_html_quantity_select($lines, $default_quantity), 'array' => $lines);
    }
}

function get_html_quantity_select($array, $default_quantity) {
    $tag = '';
    $default = '';
    foreach ($array as $line) {
        $line = trim($line);
        $f = explode('|', $line, 2);
        if (count($f) == 2 && $f[1] == 'default') {
            $line = $f[0];
            $default = $f[1];
        } else {
            $default = '';
        }
        //echo "if (($default_quantity && $line == $default_quantity) || (!$default_quantity && $default)) {";
        if (($default_quantity && $line == $default_quantity) || (!$default_quantity && $default)) {
            $selected = 'selected';
        } else {
            $selected = '';
        }
        $tag .= '<option value="' . $line . '" ' . $selected . '>' . $line . '</option>';
    }
    return '<select name="quantity">' . $tag . '</select>';
}

/* math */

function to_int($val, $dont_remove_sign = false) {
    $a = string_to_float($val, $dont_remove_sign);

    if ($a === null) {
        return null;
    }

    $iv = intval($a[0]);
    if ($dont_remove_sign && $iv >= 0) {
        return $a[1] . $iv;
    }
    return $iv;
}

function to_float($val, $dont_remove_sign = false) {
    $a = string_to_float($val, $dont_remove_sign);
    if ($a === null) {
        return null;
    }
    return $a[0];
}

/**
 * 
 * @param type $val
 * @param type $dont_remove_sign
 * @return type
 */
function string_to_float($val, $dont_remove_sign = false) {
    $e = false; // is expression 1.0E-19
    $val = trim($val);
    if (strlen($val) == 0) {
        return null;
    }
    $f = explode('.', $val);
    if (count($f) > 2) {
        return null;
    }
    $b = array_shift($f);
    if (!preg_match('/^([\-\+]?)[0-9]*$/', $b, $match)) {
        return null;
    }
    $sign = $match[1];

    $a = '';
    if ($f) {
        $a = array_shift($f);
        if (!preg_match('/^[0-9]*$/', $a)) {
            if (preg_match('/^[0-9]+E[\-\+][0-9]+$/', $a)) {
                $e = true;
            } else {
                return null;
            }
        }
    }
    $fv = floatval($b . '.' . $a);
    if ($dont_remove_sign && $fv >= 0) {
        return array($sign . $fv, $sign);
    }
    return array($fv, $sign);
}

/* html */

function get_table_tag_array($type) {
    if ($type == 'table') {
        return array(
            'tableA' => '<table>', 'tableB' => '</table>',
            'tbodyA' => '<tbody>', 'tbodyB' => '</tbody>',
            'trA' => '<tr>', 'trB' => '</tr>',
            'thA' => '<th>', 'thB' => '</th>',
            'tdA' => '<td>', 'tdB' => '</td>'
        );
    } else {
        return array(
            'tableA' => '', 'tableB' => '',
            'tbodyA' => '<dl>', 'tbodyB' => '</dl>',
            'trA' => '', 'trB' => '',
            'thA' => '<dt>', 'thB' => '</dt>',
            'tdA' => '<dd>', 'tdB' => '</dd>'
        );
    }
}

/* security */

function get_wp_nonce_field() {
    return wp_nonce_field(wp_create_nonce(DOMAIN_CF_SHOPPING_CART), DOMAIN_CF_SHOPPING_CART . '_nonce', true, false);
}

function check_wp_verify_nonce() {
    //return false; // test
    //return true; // test
    $nonce = isset($_POST[DOMAIN_CF_SHOPPING_CART . '_nonce']) ? $_POST[DOMAIN_CF_SHOPPING_CART . '_nonce'] : null;
    return wp_verify_nonce($nonce, wp_create_nonce(DOMAIN_CF_SHOPPING_CART));
}

function debuglog($text) {
    $dirs = wp_upload_dir();
    $dir = $dirs['basedir'] . '/' . DOMAIN_CF_SHOPPING_CART;
    if (!file_exists($dir)) {
        mkdir($dir);
    }
    $fn = $dir . '/debug.txt';
    //echo $fn;
    $fp = fopen($fn, "a");
    fwrite($fp, $text . "\n");
    fclose($fp);
}
