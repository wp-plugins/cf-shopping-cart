<?php
/*
 * common.php
 * -*- Encoding: utf8n -*-
 */

function get_shipping_php_path() {
    return get_wp_content_fullpath() . '/cfshoppingcart/shipping.php';
}


/************************************************************************/

/* ex: http://wordpress/wp-content/plugins/this_plugin */
function get_plugin_uri() {
    $p = get_plugin_path();
    $first = substr($p, 0, 1);
    if ($first !== '/') $p = '/' . $p;
    return get_settings('siteurl') . $p;
}

/* ex: http://wordpress/wp-content/plugins/this_plugin/module */
function get_plugin_module_uri() {
    return get_plugin_uri() . '/module';
}

/* ex: /home/user/public_html/wordpress/wp-content/plugins/this_plugin */
function get_plugin_fullpath() {
    $path = '';
    $cpath = get_current_path();
    $first = substr($cpath, 0, 1);
    if ($first === '/') $path = $first;
    $f = split('/', $cpath);
    $max = count($f);
    foreach ($f as $i => $p) {
        if ($p === 'module') {
            return $path;
        }
        if ($i == 0 && $p === '') {
            $path = '/';
            continue;
        }
        if ($path !== '' && $path !== '/') $path .= '/';
        $path .= $p;
    }
    return $path;
}

/* ex: /home/user/public_html/wordpress */
function get_wp_fullpath() {
    $path = '';
    $cpath = get_current_path();
    $first = substr($cpath, 0, 1);
    if ($first === '/') $path = $first;
    $f = split('/', $cpath);
    $max = count($f);
    foreach ($f as $i => $p) {
        if ($p === 'wp-content') {
            return $path;
        }
        if ($i == 0 && $p === '') {
            $path = '/';
            continue;
        }
        if ($path !== '' && $path !== '/') $path .= '/';
        $path .= $p;
    }
    return $path;
}

/* ex: /home/user/public_html/wordpress/wp-content */
function get_wp_content_fullpath() {
    return get_wp_fullpath() . '/wp-content';
}

/* ex: /wp-content/plugins/this_plugin */
function get_plugin_path() {
    $path = '';
    $flag = 1;
    $cpath = get_current_path();
    $first = substr($cpath, 0, 1);
    if ($first === '/') $path = $first;
    $f = split('/', $cpath);
    $max = count($f);
    foreach ($f as $i => $p) {
        if ($p !== 'wp-content' && $flag) continue;
        $flag = 0;
        if ($p === 'module') {
            return $path;
        }
        if ($i == 0 && $p === '') {
            $path = '/';
            continue;
        }
        if ($path !== '' && $path !== '/') $path .= '/';
        $path .= $p;
    }
    return $path;
}

/* ex: this_plugin */
function get_plugin_folder() {
    $cpath = get_current_path();
    $f = split('/', $cpath);
    $max = count($f);
    for ($i = $max - 1; $i >= 0; $i--) {
        if ($f[$i] === 'module' && $i > 0) {
            return $f[$i - 1];
        }
    }
    return false;
}

/* ex: /home/user/public_html/wordpress/wp-content/plugins/this_plugin... */
function get_current_path() {
    //echo "WP_PLUGIN_URL = " . WP_PLUGIN_URL;
    $current_path = (dirname(__FILE__));
    return $current_path;
    //echo "<p>current_path = $current_path</p>";
}

?>
