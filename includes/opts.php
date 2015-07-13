<?php
namespace cfshoppingcart;

class opts {

    private $opts;
    private $names;

    function __construct() {
        $this->opts = array();
        $this->names = array();
    }

    function set($name, $value = null) {
        $this->opts[$name] = new opt($name, $value);
    }

    function getOption($name) {
        //echo "getOption name = ".$name;
        return $this->opts[$name]->getOption();
    }

    function getChecked($name) {
        if ($this->opts[$name]->getChecked()) {
            return "checked";
        } else {
            return "";
        }
    }

    function updateOptions() {
        foreach ($this->opts as $name => $value) {
            $this->opts[$name]->updateOption();
            //echo "<p>updo:".$name." = ".$_POST[$name]."</p>";
        }
    }

    function updateOption($name) {
        //foreach ($this->opts as $name => $value) {
        $this->opts[$name]->updateOption();
        //echo "<p>updo:".$name."</p>";
        //}
    }

    function deleteOptions() {
        foreach ($this->opts as $name => $value) {
            $this->opts[$name]->deleteOption();
            //echo "<p>updo:".$name." = ".$_POST[$name]."</p>";
        }
    }

}
