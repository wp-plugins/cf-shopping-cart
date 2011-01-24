<?php
/* add_wp_head.php
 * -*- Encoding: utf8n -*-
 */

function cfshoppingcart_add_wp_head() {
    require_once('common.php');
    //$plugin_folder = get_plugin_folder();
    //$plugin_fullpath = get_plugin_fullpath();
    //$plugin_path = get_plugin_path();
    $plugin_uri = get_plugin_uri();

    echo '<link type="text/css" rel="stylesheet" href="';
    echo $plugin_uri . '/js/jquery.pnotify.default.css" />' . "\n";
    echo '<link type="text/css" rel="stylesheet" href="';
    echo $plugin_uri . '/js/jquery-ui.css" />' . "\n";

    echo '<link type="text/css" rel="stylesheet" href="';
    echo $plugin_uri . '/js/jquery.alerts.css" />' . "\n";
    
    echo '<script type="text/javascript">' . "\n";
    echo "//<![CDATA[\n";
    require_once('cfshoppingcart.js.php');
    if (function_exists('cfshoppingcart_login')) {
        require_once('cfshoppingcart_login.js.php');
    }
    echo "//]]>\n";
    echo '</script>' . "\n";
}
?>
