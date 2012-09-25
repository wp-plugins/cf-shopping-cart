<?php

/* cf.php
 * custom field module
 * Return Custom-field data.
 *
 * -*- Encoding: utf8n -*-
 *
 */

class cfshoppingcart_cf {
    // #special
    var $hidden;
    var $post_id;
    var $post_title;
    var $select;
    var $radio;
    //
    var $name;
    var $raw_value;
    var $clean_value;
    var $explode_value;
    
    function cfshoppingcart_cf($name, $raw_cf) {
        //echo "<p>$raw_cf</p>";
        $this->name = $name;
        $this->raw_value = $raw_cf;
        if (strstr($raw_cf, '#hidden')) { $this->hidden = true; }
        if (strstr($raw_cf, '#postid')) { $this->post_id = true; }
        if (strstr($raw_cf, '#post_title')) { $this->post_title = true; }
        if (strstr($raw_cf, '#select')) { $this->select = true; }
        if (strstr($raw_cf, '#radio')) { $this->radio = true; }
        $this->clean_value = $this->clean_cf_value($raw_cf);
        $this->explode_value = explode("\n", $this->clean_value);
    }
    
    function clean_cf_value($text) {
        // CR and LF
        $text = str_replace("\r\n", "\n", $text);
        $text = str_replace("\r", "\n", $text);
        // remove comment
        $text = preg_replace('/(^|\n);.*($|\n)/', "\n", $text);
        // remove blank line
        $text = preg_replace('/(^|\n)[\s\t]{2,}($|\n)/', '', $text);
        // remove top space
        $text = preg_replace('/(^|\n)[\s\t]*/', "\n", $text);
        // remove end space
        $text = preg_replace('/[\s\t]*($|\n)/', "\n", $text);
        // remove unnecessary new line
        $text = preg_replace('/[\n]{2,}/', "\n", $text);
        $text = preg_replace('/^\n*|\n*$/', '', $text);
        // #special
        $text = preg_replace('/^#[a-zA-Z_]*(\n|$)/', '', $text);
        
        return $text;
    }
    
} // class cfshoppingcart_cf

class cfshoppingcart_products {
    //
    var $obj;
    var $model;
    //
    var $post;
    var $product_name;
    var $stock_cf;
    var $price_cf;
    var $cf;
    var $raw_cf;
    var $cf_names; // is array (Maker,Product ID,Name,Size,Color,Price,Stock)
    var $cf_default;
    // field name
    var $fn_price;
    var $fn_stock;
    var $fn_link;

    function cfshoppingcart_products($obj) {
        $this->obj = $obj;
        $this->model = $obj->model;
        
        $this->cf_names = $this->model->getCustomFields();
        $this->cf_default = $this->model->getCustomFieldDefaultValue();
        
        $this->fn_price = $this->model->getPriceFieldName();
        $this->fn_stock = $this->model->getNumberOfStockFieldName();
        $this->fn_link = $this->model->getLinkToProductFieldName();
    }
    function setPost($post = NULL) {
        if (is_null($post)) {
            global $post;
        }
        $this->post = $post;
        //print_r($post);
        $this->raw_cf = get_post_custom($post->ID);

        /*
        $currency_format = $model->getCurrencyFormat();
        $quantity_str = $model->getQuantity();
        $table_tag = $model->getTableTag();
        $link_to_product = false;
        if ($link_to_product) {
            $open_product_link_to_another_window = $model->getOpenProductLinkToAnotherWindow();
        }
        */
        
        $this->loop();
        var_dump($this->cf);
    }
    function loop() {
        foreach ($this->cf_names as $index => $name) {
            //echo $name;
            //print_r($raw_cf);
            if (!isset($this->raw_cf[$name][0]) && isset($this->cf_default[$name])) {
                $this->raw_cf[$name][0] = $this->cf_default[$name];
            }
            $this->cf[$name] = new cfshoppingcart_cf($name, $this->raw_cf[$name][0]);
        }
    }
    
} // class

?>