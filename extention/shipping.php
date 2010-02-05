<?php
/*
 * shipping.php
 * -*- Encoding: utf8n -*-
 */

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
