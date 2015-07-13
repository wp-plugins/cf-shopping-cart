<?php

namespace cfshoppingcart;

function init() {
    //print_r($_POST);
    exec_cmd();
}

add_action('init', 'cfshoppingcart\init', 8);

function load_scripts() {
    wp_enqueue_script('jquery');
}

add_action('wp_enqueue_scripts', 'cfshoppingcart\load_scripts');

function wp_head() {
    ?>
    <script>
        var cfshoppingcart_ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        var cfshoppingcart_ajax_nonce = '<?php echo wp_create_nonce('cfshoppingcart'); ?>';
    </script>
    <?php
}

add_action('wp_head', 'cfshoppingcart\wp_head', 1);

function wp_footer() {
    if (!opt::get_option('disable_ajax')) {
        ?>
        <style>
        <?php echo stripslashes(htmlspecialchars_decode(opt::get_option('pnotify_css_ok'))); ?>
        <?php echo stripslashes(htmlspecialchars_decode(opt::get_option('pnotify_css_error'))); ?>
        </style>
        <?php
        echo '<link type="text/css" rel="stylesheet" href="' . CF_SHOPPING_CART_PLUGIN_URL . '/lib/pnotify-master/pnotify.core.css" />';
        echo '<link type="text/css" rel="stylesheet" href="' . CF_SHOPPING_CART_PLUGIN_URL . '/lib/pnotify-master/oxygen/icons.css" />';
        echo '<script type="text/javascript" src="' . CF_SHOPPING_CART_PLUGIN_URL . '/lib/pnotify-master/pnotify.core.js"></script>';
        echo '<script type="text/javascript" src="' . CF_SHOPPING_CART_PLUGIN_URL . '/lib/pnotify-master/pnotify.callbacks.js"></script>';
        echo '<script type="text/javascript" src="' . CF_SHOPPING_CART_PLUGIN_URL . '/js/ajax.js"></script>';
    }
}

add_action('wp_footer', 'cfshoppingcart\wp_footer');

function clear_cart() {
    session_destroy();
    $_SESSION[DOMAIN_CF_SHOPPING_CART] = array();
}

function exec_cmd() {
    if (!session_id()) {
        $_SESSION[DOMAIN_CF_SHOPPING_CART] = array();
        session_start();
    }

    // debug reset
    if (array_key_exists(DOMAIN_CF_SHOPPING_CART . '_destory', $_GET)) {
        // http://192.168.11.101/~aic/lastella.me/wrla/?cat=4&cfshoppingcart_destory=1
        clear_cart();
        //wp_redirect(get_bloginfo('url'));
        header('Location: ' . get_bloginfo('url'));
        exit();
    }

    $serial = new serial();
    $cart = $serial->load();
    $cmd = getPostValue('cmd');
    if (strtolower($_SERVER['REQUEST_METHOD']) == 'post' && $cmd == 'check_out') {
        $msg = $cart->check_out();
        clear_cart();
        if ($msg === CF_SHOPPING_CART_MSG_TRUE) {
            message::add(opt::get_option($msg));
        } else {
            // error
            message::add(opt::get_option($msg), false);
        }
    }

    after_intercept_post_request_before_redirect();

    if ('post' != strtolower($_SERVER['REQUEST_METHOD'])) {
        return;
    }

    if ($cmd == 'add_to_cart') {
        $msg = $cart->add_to_cart();
        if ($msg === CF_SHOPPING_CART_MSG_ADDED_TO_CART) {
            message::add(opt::get_option($msg));
        } else {
            //clear_cart();
            message::add(opt::get_option($msg), true);
        }
    } else if ($cmd == 'change_quantity') {
        $msg = $cart->change_quantity();
        if ($msg === CF_SHOPPING_CART_MSG_HAVE_CHANGED_THE_QUANTITY) {
            message::add(opt::get_option($msg));
        } else {
            //clear_cart();
            message::add(opt::get_option($msg), true);
        }
    } else if ($cmd == 'quantity_plus') {
        $msg = $cart->quantity_plus();
        if ($msg === CF_SHOPPING_CART_MSG_ADDED_TO_CART) {
            message::add(opt::get_option($msg));
        } else {
            //clear_cart();
            message::add(opt::get_option($msg), true);
        }
    } else if ($cmd == 'quantity_minus') {
        $msg = $cart->quantity_minus();
        if ($msg === CF_SHOPPING_CART_MSG_QUANTITY_WAS_DECREASED) {
            message::add(opt::get_option($msg));
        } else {
            clear_cart();
            message::add(opt::get_option($msg), true);
        }
    }
    //var_dump($session);
    $serial->save();
}

function final_check_stock() {
    $serial = new serial();
    $session = $serial->load();
    $session->calc();
}

function intercept_post_request_before_redirect($location, $status) {
    $_SESSION[DOMAIN_CF_SHOPPING_CART]['wp_redirect_status'] = true;

    if ('post' == strtolower($_SERVER['REQUEST_METHOD'])) {
        $_SESSION[DOMAIN_CF_SHOPPING_CART]['_POST'] = $_POST;
    } else {
        unset($_SESSION[DOMAIN_CF_SHOPPING_CART]['_POST']);
    }
    return false;
}

add_filter('wp_redirect_status', 'cfshoppingcart\intercept_post_request_before_redirect', 1, 2);

function after_intercept_post_request_before_redirect() {
    if (!isset($_SESSION[DOMAIN_CF_SHOPPING_CART]['wp_redirect_status'])) {
        unset($_SESSION[DOMAIN_CF_SHOPPING_CART]['_POST']);
    }
    $_SESSION[DOMAIN_CF_SHOPPING_CART]['wp_redirect_status'] = false;
}
