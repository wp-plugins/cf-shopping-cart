<?php
/*
 * debug.php
 * -*- Encoding: utf8n -*-
 */

function debug_cfshoppingcart($msg){
    // get data object
    //$wpCFShoppingcart = new WpCFShoppingcart();
    //$model = $wpCFShoppingcart->model;
    //print_r($model);
    //if (!$model->is_debug()) return;
    echo '<p>** Debug-mode[' . $msg . ']</p>';
}
?>
