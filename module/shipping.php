<?php
/*
 * shipping.php
 * -*- Encoding: utf8n -*-
 */

function shipping(&$model, $quantity, $total_price) {
    
    for ($l = 0; $l < 5; $l++) {
        //$fields = $model->getShipping();
        //echo 'k0 = ' . $fields[$l][0];
        $shipping = $model->getShipping($l, 0);
        $min = $model->getShipping($l, 1);
        $gt1 = $model->getShipping($l, 2);
        $gt2 = $model->getShipping($l, 3);
        $max = $model->getShipping($l, 4);
        $msg = '';

        /* check ******************/
        if (!strlen($shipping) || !strlen($min) || !strlen($max)) {
            return array(0, __('Shipping Error','cfshoppingcart').': 1');
        }
        if ($min > $max) {
            return array(0, __('Shipping Error','cfshoppingcart').': 2');
        }
        if ($gt1 !== '<' && $gt1 !== '<=') {
            return array(0, __('Shipping Error','cfshoppingcart').': 3');
        }
        if ($gt2 !== '<' && $gt2 !== '<=') {
            return array(0, __('Shipping Error','cfshoppingcart').': 4');
        }

        /* calc ********************/
        if ($gt1 === '<') {
            if (!($min < $total_price)) continue;
        } else if ($gt1 === '<=') {
            if (!($min <= $total_price)) continue;
        }
        if ($gt2 === '<') {
            if (!($total_price < $max)) continue;
        } else if ($gt2 === '<=') {
            if (!($total_price <= $max)) continue;
        }
        return array($shipping, $msg);
    }
    return array(0, __('Shipping Error','cfshoppingcart').': 5: l = ' . $l . '('. $msg . ')');
}

?>
