<?php

namespace cfshoppingcart;

class shipping {

    public function calc($total_price) {
        $shipping = opt::get_option('shipping');
        if (!is_array($shipping)) {
            // shipping setting is nothing.
            // return 0.
            return array(0, '');
        }

        //print_r($shipping);
        // [price] => 1 [value1] => 2 [operator1] => < [operator2] => < [value2] => 3
        $l = 0;
        foreach ($shipping as $line) {
            //print_r($line);
            $l++;
            $price = $line['price'];
            $min = $line['value1'];
            $gt1 = $line['operator1'];
            $gt2 = $line['operator2'];
            $max = $line['value2'];

            // check setting
            if (!strlen($price) || !strlen($min) || !strlen($max)) {
                return array(0, sprintf("%s, line: %d", __('Shipping setting error', DOMAIN_CF_SHOPPING_CART), $l));
            }
            if ($min > $max) {
                return array(0, sprintf("%s, line: %d", __('Shipping setting error', DOMAIN_CF_SHOPPING_CART), $l));
            }
            if ($gt1 !== '<' && $gt1 !== '<=') {
                return array(0, sprintf("%s, line: %d", __('Shipping setting error', DOMAIN_CF_SHOPPING_CART), $l));
            }
            if ($gt2 !== '<' && $gt2 !== '<=') {
                return array(0, sprintf("%s, line: %d", __('Shipping setting error', DOMAIN_CF_SHOPPING_CART), $l));
            }

            // calc
            if ($gt1 === '<') {
                if (!($min < $total_price)) {
                    continue;
                }
            } else if ($gt1 === '<=') {
                if (!($min <= $total_price)) {
                    continue;
                }
            }
            if ($gt2 === '<') {
                if (!($total_price < $max)) {
                    continue;
                }
            } else if ($gt2 === '<=') {
                if (!($total_price <= $max)) {
                    continue;
                }
            }
            return array($price, '');
        }
        return array(0, __('Calculate shipping failed', DOMAIN_CF_SHOPPING_CART));
    }

    public function check_setting() {
        $shipping = opt::get_option('shipping');
        if (!is_array($shipping)) {
            $shipping = array();
        }
        //print_r($shipping);
        // [price] => 1 [value1] => 2 [operator1] => < [operator2] => < [value2] => 3
        $l = 0;
        foreach ($shipping as $line) {
            //print_r($line);
            $l++;
            $price = $line['price'];
            $min = $line['value1'];
            $gt1 = $line['operator1'];
            $gt2 = $line['operator2'];
            $max = $line['value2'];

            // check setting
            if (!strlen($price) || !strlen($min) || !strlen($max)) {
                return sprintf("%s, line: %d", __('Shipping setting error', DOMAIN_CF_SHOPPING_CART), $l);
            }
            if ($min > $max) {
                return sprintf("%s, line: %d", __('Shipping setting error', DOMAIN_CF_SHOPPING_CART), $l);
            }
            if ($gt1 !== '<' && $gt1 !== '<=') {
                return sprintf("%s, line: %d", __('Shipping setting error', DOMAIN_CF_SHOPPING_CART), $l);
            }
            if ($gt2 !== '<' && $gt2 !== '<=') {
                return sprintf("%s, line: %d", __('Shipping setting error', DOMAIN_CF_SHOPPING_CART), $l);
            }
        }
        return '';
    }

}
