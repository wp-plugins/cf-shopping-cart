<?php

namespace cfshoppingcart;

class opt {

    private $name;
    private $defalutValue;

    function __construct($name, $value = null) {
        $this->name = $name; // = DOMAIN_CF_SHOPPING_CART . '_' . $name;
        $this->defalutValue = $value;
        if (preg_match('/[^a-zA-Z0-9_]/', $name)) {
            echo '<p>allowed charactor is alphabet and numeric and underbar.</p>';
        }
        if (!$this->optionExists()) {
            return update_option(DOMAIN_CF_SHOPPING_CART . '_' . $this->name, $value);
        }
    }

    public function optionExists() {
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1", DOMAIN_CF_SHOPPING_CART . '_' . $this->name));
        if (is_object($row)) {
            return true;
        }
        return false;
    }

    public function getOption() {
        return get_option(DOMAIN_CF_SHOPPING_CART . '_' . $this->name);
    }

    // I want overload method.
    public static function get_option($name) {
        return get_option(DOMAIN_CF_SHOPPING_CART . '_' . $name);
    }

    public function updateOption() {
        if (array_key_exists($this->name, $_POST)) {
            return update_option(DOMAIN_CF_SHOPPING_CART . '_' . $this->name, $_POST[$this->name]);
        } else {
            return update_option(DOMAIN_CF_SHOPPING_CART . '_' . $this->name, null);
        }
    }

    public static function update_option($name, $value) {
        return update_option(DOMAIN_CF_SHOPPING_CART . '_' . $name, $value);
    }

    public function deleteOption() {
        delete_option(DOMAIN_CF_SHOPPING_CART . '_' . $this->name);
    }

    public function getChecked() {
        if (get_option(DOMAIN_CF_SHOPPING_CART . '_' . $this->name)) {
            return "checked";
        } else {
            return "";
        }
    }

}
