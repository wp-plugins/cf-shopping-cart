<?php
/*
 * Shipping module
 * -*- Encoding: utf8n -*-
 */

class WpCFShoppingcartShippingModel {
    // member variable
    var $shipping;
    var $shipping_enabled;
    
    // constructor
    function WpCFShoppingcartShippingModel() {
        // default value
        $this->shipping = array();
        $this->shipping_enabled = '';
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
    function setShippingEnabled($fields) {
        $fields = preg_replace('/[^a-zA-Z]/', '', $fields);
        $this->shipping_enabled = $fields;
    }
    function getShippingEnabled() {
        return $this->shipping_enabled;
    }
    function setShipping($fields) {
        //print_r($fields);
        for ($l = 0; $l < 5; $l++) {
            //echo 'k0 = ' . $fields[$l][0];
            $fields[$l][0] = $this->toDouble($fields[$l][0]);
            $fields[$l][1] = $this->toDouble($fields[$l][1]);
            if ($fields[$l][2] !== '<' && $fields[$l][2] !== '<=') {
                $fields[$l][2] = '';
            }
            if ($fields[$l][3] !== '<' && $fields[$l][3] !== '<=') {
                $fields[$l][3] = '';
            }
            $fields[$l][4] = $this->toDouble($fields[$l][4]);
        }
        $this->shipping = $fields;
    }
    function getShipping($l, $c) {
        return $this->shipping[$l][$c];
    }
    function getShippingListHtml($l, $c) {
        $gt = $this->shipping[$l][$c];
        
        $h = '<select name="shipping[' . $l . '][' . $c . ']" id="shipping[' . $l . '][' . $c . ']" >';
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
class WpCFShoppingcartShipping {
    var $wpCFShoppingcart;
    var $view;
    var $model;
    var $common;
    var $request;
    var $plugin_name;
    var $plugin_fullpath, $plugin_path, $plugin_folder, $plugin_uri;
    
    // constructor
    function WpCFShoppingcartShipping($obj) {
        $this->plugin_name = 'cfshoppingcart_shipping';
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
        
        //printf("<p>Debug[%s, %s]</p>", strtolower(get_class($option)), strtolower('WpCFShoppingcartShippingModel'));
        
        // Restore the model object if it is registered
        if (strtolower(get_class($option)) === strtolower('WpCFShoppingcartShippingModel') && $data_clear == 0) {
            $model = $option;
        } else {
            // create model instance if it is not registered,
            // register it to Wordpress
            $model = new WpCFShoppingcartShippingModel();
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
        $model->setShipping($shipping);
        $model->setShippingEnabled($shipping_enabled);
        //
        $this->updateWpOption($model); // Update database-model
        $msg .= __('Updated', 'cfshoppingcart');
        return $msg;
    }
    
    //function cfshoppingcart_shipping(&$model, $quantity, $total_price) {
    function get_shipping($quantity, $total_price) {
        $model = $this->model;
        
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
    
    function edit($obj, $msg = '') {
        $model = & $this->model;
        if ($msg) {
            echo '<div id="message" class="updated"><p>' . $msg . '</p></div>';
        }
        
?>

<div class="postbox cfshoppingcart_postbox closed">
  <div class="handlediv" title="Click to toggle"><br /></div>
  <h3><?php _e('Shipping Options','cfshoppingcart');?></h3>
  <div class="inside">

    <table class="form-table">

       <tr><th><?php _e("Shipping", 'cfshoppingcart');?></th><td><input type="checkbox" name="shipping_enabled" value="checked" <?php echo $model->getShippingEnabled();?> /> <?php echo _e('Enabled','cfshoppingcart'); ?></td></tr>
       
       <tr><th> </th><td><input type="text" name="shipping[0][0]" id="shipping[0][0]" value="<?php echo $model->getShipping(0,0);?>" style="width:90px" />: <input type="text" name="shipping[0][1]" id="shipping[0][1]" value="<?php echo $model->getShipping(0,1);?>" style="width:90px" /> <?php echo $model->getShippingListHtml(0,2);?> <?php _e('Total price','cfshoppingcart');?> <?php echo $model->getShippingListHtml(0,3);?> <input type="text" name="shipping[0][4]" id="shipping[0][4]" value="<?php echo $model->getShipping(0,4);?>" style="width:90px" /></td></tr>
       <tr><th> </th><td><input type="text" name="shipping[1][0]" id="shipping[1][0]" value="<?php echo $model->getShipping(1,0);?>" style="width:90px" />: <input type="text" name="shipping[1][1]" id="shipping[1][1]" value="<?php echo $model->getShipping(1,1);?>" style="width:90px" /> <?php echo $model->getShippingListHtml(1,2);?> <?php _e('Total price','cfshoppingcart');?> <?php echo $model->getShippingListHtml(1,3);?> <input type="text" name="shipping[1][4]" id="shipping[1][4]" value="<?php echo $model->getShipping(1,4);?>" style="width:90px" /></td></tr>
       <tr><th> </th><td><input type="text" name="shipping[2][0]" id="shipping[2][0]" value="<?php echo $model->getShipping(2,0);?>" style="width:90px" />: <input type="text" name="shipping[2][1]" id="shipping[2][1]" value="<?php echo $model->getShipping(2,1);?>" style="width:90px" /> <?php echo $model->getShippingListHtml(2,2);?> <?php _e('Total price','cfshoppingcart');?> <?php echo $model->getShippingListHtml(2,3);?> <input type="text" name="shipping[2][4]" id="shipping[2][4]" value="<?php echo $model->getShipping(2,4);?>" style="width:90px" /></td></tr>
       <tr><th> </th><td><input type="text" name="shipping[3][0]" id="shipping[3][0]" value="<?php echo $model->getShipping(3,0);?>" style="width:90px" />: <input type="text" name="shipping[3][1]" id="shipping[3][1]" value="<?php echo $model->getShipping(3,1);?>" style="width:90px" /> <?php echo $model->getShippingListHtml(3,2);?> <?php _e('Total price','cfshoppingcart');?> <?php echo $model->getShippingListHtml(3,3);?> <input type="text" name="shipping[3][4]" id="shipping[3][4]" value="<?php echo $model->getShipping(3,4);?>" style="width:90px" /></td></tr>
       <tr><th> </th><td><input type="text" name="shipping[4][0]" id="shipping[4][0]" value="<?php echo $model->getShipping(4,0);?>" style="width:90px" />: <input type="text" name="shipping[4][1]" id="shipping[4][1]" value="<?php echo $model->getShipping(4,1);?>" style="width:90px" /> <?php echo $model->getShippingListHtml(4,2);?> <?php _e('Total price','cfshoppingcart');?> <?php echo $model->getShippingListHtml(4,3);?> <input type="text" name="shipping[4][4]" id="shipping[4][4]" value="<?php echo $model->getShipping(4,4);?>" style="width:90px" /></td></tr>


</table>
    <div class="submit">
        <input type="submit" name="update_shipping_options" value="<?php _e('Update Options','cfshoppingcart');?> &raquo;" class="button-primary" />
    </div>


    </div>
  </div>
    
    
    <?php
  }
    
} // class



add_filter('cfshoppingcart_shipping_put_configuration', 'cfshoppingcart_shipping_put_configuration',12,1);
function cfshoppingcart_shipping_put_configuration($obj) {
    //var_dump($args);
    //print_r($args);
    if (isset($_POST['update_shipping_options'])) {
        $pp_msg = $obj->shipping->save();
    }
    $obj->shipping->edit($obj, $pp_msg);
}

?>
