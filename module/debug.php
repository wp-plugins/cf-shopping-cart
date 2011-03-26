<?php
/*
 * debug.php
 * -*- Encoding: utf8n -*-
 */

function debug_cfshoppingcart($msg){
    // get data object
    //$WpCFShoppingcart =  /* php4_110323 & new */ new WpCFShoppingcart();
    //$model = $WpCFShoppingcart->model;
    //print_r($model);
    //if (!$model->is_debug()) return;
    echo '<p>** Debug-mode[' . $msg . ']</p>';
}
?>
