<?php
/*
 * shipping.php
 * -*- Encoding: utf8n -*-
 */

/* NOTE: 
     This file copy to /wp-content/cfshoppingcart/shipping.php */

/* If $shipping less than 0 then display message only */
function shipping($quantity, $total_price) {
    /* edit this */
    if ($total_price >= 30000) {
        $shipping = 0;
        $msg = '';
    } else {
        $shipping = 1500;
        $msg = '';
    }

    /*************/
    return array($shipping, $msg);
}

?>
