<?php
/*
 * Widget module
 * -*- Encoding: utf8n -*-
 */

class WpCFShoppingcartWidgetModel {
    // member variable
    var $widget_enabled_cart_info;
    var $cart_info;
    var $cart_info_head;
    var $cart_info_tail;
    
    // constructor
    function WpCFShoppingcartWidgetModel() {
        // default value
        $this->widget_enabled_cart_info = '';
        $this->cart_info = '';
        $this->cart_info_head = '';
        $this->cart_info_tail = '';
    }

    //method
    function toDouble($v) {
        if (preg_match('/-?[0-9]+\.[0-9]+$/', $v)) {
            return $v;
        } else if (preg_match('/-?[0-9]+$/', $v)) {
            return $v;
        } else {
            return '';
        }
    }
    function setWidgetEnabledCartInfo($fields) {
        $fields = preg_replace('/[^a-zA-Z]/', '', $fields);
        $this->widget_enabled_cart_info = $fields;
    }
    function getWidgetEnabledCartInfo() {
        return $this->widget_enabled_cart_info;
    }
    function setCartInfo($fields) {
        $this->cart_info = $fields;
    }
    function getCartInfo() {
        return $this->cart_info;
    }
    function setCartInfoHead($fields) {
        $this->cart_info_head = $fields;
    }
    function getCartInfoHead() {
        return $this->cart_info_head;
    }
    function setCartInfoTail($fields) {
        $this->cart_info_tail = $fields;
    }
    function getCartInfoTail() {
        return $this->cart_info_tail;
    }
    function getWidgetListHtml($l, $c) {
        $gt = $this->widget[$l][$c];
        
        $h = '<select name="widget[' . $l . '][' . $c . ']" id="widget[' . $l . '][' . $c . ']" >';
        $h .= '<option value="">' . __('Select','cfshoppingcart').'</option>';
        if ($gt === '<') { $selected = 'selected="selected"'; } else { $selected = ''; }
        $h .= '<option class="lt" value="<" ' . $selected . '>&lt;</option>';
        if ($gt === '<=') { $selected = 'selected="selected"'; } else { $selected = ''; }
        $h .= '<option class="le" value="<=" ' . $selected . '>&lt;=</option>';
        $h .= '</select>';
return $h;
    }
}

/* main class */
class WpCFShoppingcartWidget {
    var $wpCFShoppingcart;
    var $view;
    var $model;
    var $common;
    var $request;
    var $plugin_name;
    var $plugin_fullpath, $plugin_path, $plugin_folder, $plugin_uri;
    
    // constructor
    function WpCFShoppingcartWidget($obj) {
        $this->plugin_name = 'cfshoppingcart_widget';
        $this->model = $this->getModelObject();
        $this->wpCFShoppingcart = $obj;
        
        //require_once('module/common.php');
        //$this->common = new cfshoppingcart_common();
        //$this->plugin_uri = $this->common->get_plugin_uri();
    }
    
    // create model object
    function getModelObject() {
        $data_clear = 0; // Debug: 1: Be empty to data
        
        // get option from Wordpress
        $option = $this->getWpOption();
        
        //printf("<p>Debug[%s, %s]</p>", strtolower(get_class($option)), strtolower('WpCFShoppingcartWidgetModel'));
        
        // Restore the model object if it is registered
        if (strtolower(get_class($option)) === strtolower('WpCFShoppingcartWidgetModel') && $data_clear == 0) {
            $model = $option;
        } else {
            // create model instance if it is not registered,
            // register it to Wordpress
            $model = new WpCFShoppingcartWidgetModel();
            $this->addWpOption($model);
        }
        return $model;
    }
    
    function getWpOption() {
        $option = get_option($this->plugin_name);
        
        if(!$option == false) {
            $OptionValue = $option;
        } else {
            $OptionValue = false;
        }
        return $OptionValue;
    }

    /* be add plug-in data to Wordpresss */
    function addWpOption(&$model) {
        $option_description = $this->plugin_name . " Options";
        $OptionValue = $model;
        //print_r($OptionValue);
        add_option(
            $this->plugin_name,
            $OptionValue,
            $option_description);
    }

    /* update plug-in data */
    function updateWpOption(&$OptionValue) {
        $option_description = $this->plugin_name . " Options";
        $OptionValue = $OptionValue;
        //$OptionValue = $this->model;
        
        update_option(
            $this->plugin_name,
            $OptionValue,
            $option_description);
    }

    function save() {
        //print_r($_REQUEST);
        if (is_array($_REQUEST)) {
            // Array extract to variable
            extract($_REQUEST);
        }
        
        $model = $this->model;
        //
        $model->setCartInfo($cart_info);
        $model->setCartInfoHead($cart_info_head);
        $model->setCartInfoTail($cart_info_tail);
        $model->setWidgetEnabledCartInfo($widget_enabled_cart_info);
        //
        $this->updateWpOption($model); // Update database-model
        $msg .= __('Updated', 'cfshoppingcart');
        return $msg;
    }
    
    //function cfshoppingcart_widget(&$model, $quantity, $total_price) {
    function get_widget($quantity, $total_price) {
        $model = $this->model;
        
        for ($l = 0; $l < 5; $l++) {
            //$fields = $model->getWidget();
            //echo 'k0 = ' . $fields[$l][0];
            $widget = $model->getWidget($l, 0);
            $min = $model->getWidget($l, 1);
            $gt1 = $model->getWidget($l, 2);
            $gt2 = $model->getWidget($l, 3);
            $max = $model->getWidget($l, 4);
            $msg = '';
            
            /* check ******************/
            if (!strlen($widget) || !strlen($min) || !strlen($max)) {
                return array(0, __('Widget Error','cfshoppingcart').': 1');
            }
            if ($min > $max) {
                return array(0, __('Widget Error','cfshoppingcart').': 2');
            }
            if ($gt1 !== '<' && $gt1 !== '<=') {
                return array(0, __('Widget Error','cfshoppingcart').': 3');
            }
            if ($gt2 !== '<' && $gt2 !== '<=') {
                return array(0, __('Widget Error','cfshoppingcart').': 4');
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
            return array($widget, $msg);
        }
        return array(0, __('Widget Error','cfshoppingcart').': 5: l = ' . $l . '('. $msg . ')');
    }
    
    function edit($obj, $msg = '') {
        $model = & $this->model;
        if ($msg) {
            echo '<div id="message" class="updated"><p>' . $msg . '</p></div>';
        }
        
?>

<div class="postbox cfshoppingcart_postbox closed">
  <div class="handlediv" title="Click to toggle"><br /></div>
  <h3><?php _e('Widget Options','cfshoppingcart');?></h3>
  <div class="inside">

    <table class="form-table">

       <tr><th><?php _e("Display Custom Field value in Cart widget", 'cfshoppingcart');?></th><td><input type="checkbox" name="widget_enabled_cart_info" value="checked" <?php echo $model->getWidgetEnabledCartInfo();?> /> <?php echo _e('Enabled','cfshoppingcart'); ?></td></tr>
         
         <tr><th><?php _e('Head:','cfshoppingcart');?></th><td><textarea cols="80" name="cart_info_head" id="cart_info_head"><?php echo $model->getCartInfoHead();?></textarea></td></tr>
         <tr><th><?php _e('Loop:','cfshoppingcart');?></th><td><textarea cols="80" name="cart_info" id="cart_info"><?php echo $model->getCartInfo();?></textarea></td></tr>
         <tr><th><?php _e('Tail:','cfshoppingcart');?></th><td><textarea cols="80" name="cart_info_tail" id="cart_info_tail"><?php echo $model->getCartInfoTail();?></textarea></td></tr>

         <tr><th><?php _e('Example:','cfshoppingcart');?><br /> <?php _e('Head:','cfshoppingcart');?></th><td>&lt;table&gt;
  &lt;tr&gt; 
  &lt;td&gt;Product ID&lt;/td&gt;
  &lt;td&gt;Name&lt;/td&gt;
  &lt;td&gt;Price&lt;/td&gt;
  &lt;td&gt;Quantity&lt;/td&gt;
  &lt;td&gt;Sum&lt;/td&gt;
  &lt;/tr&gt;
</td></tr>
<tr><th><?php _e('Loop:','cfshoppingcart');?></th><td>  &lt;tr&gt; 
  &lt;td&gt;[Product ID]&lt;/td&gt;
  &lt;td&gt;[Name]&lt;/td&gt;
  &lt;td&gt;[Price]&lt;/td&gt;
  &lt;td&gt;[quantity]&lt;/td&gt;
  &lt;td&gt;[_sum_]&lt;/td&gt;
  &lt;/tr&gt;
</td></tr>
<tr><th><?php _e('Tail:','cfshoppingcart');?></th><td>&lt;/table&gt;</td></tr>

</table>
    <div class="submit">
        <input type="submit" name="update_widget_options" value="<?php _e('Update Options','cfshoppingcart');?> &raquo;" class="button-primary" />
    </div>


    </div>
  </div>
    
    
    <?php
  }
    
} // class



add_filter('cfshoppingcart_widget_put_configuration', 'cfshoppingcart_widget_put_configuration',12,1);
function cfshoppingcart_widget_put_configuration($obj) {
    //var_dump($args);
    //print_r($args);
    if (isset($_POST['update_widget_options'])) {
        $pp_msg = $obj->widget->save();
    }
    $obj->widget->edit($obj, $pp_msg);
}

?>
