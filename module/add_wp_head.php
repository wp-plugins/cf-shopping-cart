<?php
/* add_wp_head.php
 * -*- Encoding: utf8n -*-
 */

function cfshoppingcart_add_wp_head() {
    //require_once('common.php');
    //$cfshoppingcart_common = /* php4_110323 & new */ new cfshoppingcart_common();
    //global $cfshoppingcart_common;
    global $WpCFShoppingcart, $cfshoppingcart_common;
    $model = & $WpCFShoppingcart->model;
    
    //$plugin_folder = $cfshoppingcart_common->get_plugin_folder();
    //$plugin_fullpath = $cfshoppingcart_common->get_plugin_fullpath();
    //$plugin_path = $cfshoppingcart_common->get_plugin_path();
    $plugin_uri = $cfshoppingcart_common->get_plugin_uri();

    if (!$model->getDontLoadCss()) {
        echo '<link type="text/css" rel="stylesheet" href="';
        echo $plugin_uri . '/js/jquery.pnotify.default.css" />' . "\n";
        echo '<link type="text/css" rel="stylesheet" href="';
        echo $plugin_uri . '/js/jquery-ui.css" />' . "\n";
        //echo '<link type="text/css" rel="stylesheet" href="';
        //echo $plugin_uri . 'cfshoppingcart.css" />' . "\n";
        //echo '<link type="text/css" rel="stylesheet" href="';
        //echo $plugin_uri . '/js/jquery.alerts.css" />' . "\n";
    }
    
    /*
    echo '<script type="text/javascript">' . "\n";
    echo "//<![CDATA[\n";
    require_once('cfshoppingcart.js.php');
    if (function_exists('cfshoppingcart_login')) {
        require_once('cfshoppingcart_login.js.php');
    }
    echo "//]]>\n";
    echo '</script>' . "\n";
      */
}
?>
