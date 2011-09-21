<?php
/*
 * Pnotify module
 * -*- Encoding: utf8n -*-
 */

class WpCFShoppingcartPnotifyModel {
    // member variable
    var $item_to_cart_title;
    var $item_to_cart;
    var $change_quantity_is_faild_title;
    var $change_quantity_is_faild;
    var $sorry_stock_is_no_title;
    var $sorry_stock_is_no;
    var $out_of_stock_title;
    var $out_of_stock;
    var $max_quantity_title;
    var $max_quantity;
    var $max_quantity_product_title;
    var $max_quantity_product;
    var $off_the_item_title;
    var $off_the_item;
    var $cart_is_empty_title;
    var $cart_is_empty;
    var $quantity_changed_title;
    var $quantity_changed;
    //
    var $js_function;
    //
    var $dont_load_css;
    
    // constructor
    function WpCFShoppingcartPnotifyModel() {
        // default value
        $this->item_to_cart_title = __('Shopping Cart','cfshoppingcart');
        $this->item_to_cart = __('Item to cart.','cfshoppingcart');
        $this->change_quantity_is_faild_title = __('Shopping Cart','cfshoppingcart');
        $this->change_quantity_is_faild = __('Change quantity is faild.','cfshoppingcart');
        $this->sorry_stock_is_no_title = __('Shopping Cart','cfshoppingcart');
        $this->sorry_stock_is_no = __('Sorry, stock is no.','cfshoppingcart');
        $this->out_of_stock_title = __('Shopping Cart','cfshoppingcart');
        $this->out_of_stock = __('Out of stock.','cfshoppingcart');
        $this->max_quantity_title = __('Shopping Cart','cfshoppingcart');
        $this->max_quantity = __('Max quantity of total order is %d.','cfshoppingcart');
        $this->max_quantity_product_title = __('Shopping Cart','cfshoppingcart');
        $this->max_quantity_product = __('Max quantity of one product is %d.','cfshoppingcart');
        $this->off_the_item_title = __('Shopping Cart','cfshoppingcart');
        $this->off_the_item = __('Off the item.','cfshoppingcart');
        $this->cart_is_empty_title = __('Shopping Cart','cfshoppingcart');
        $this->cart_is_empty = __('Shopping Cart is empty.','cfshoppingcart');
        $this->quantity_changed_title = __('Shopping Cart','cfshoppingcart');
        $this->quantity_changed = __('Quantity has changed.','cfshoppingcart');
        //
        $this->js_function = $this->get_js_function();
        //
        $this->dont_load_css = '';
    }

    function get_js_function() {
$jsf = <<< EOJSF
cfshoppingcart_js.cfshoppingcart_pnotify = function(type, msg, title) {
    jQuery.pnotify({
      pnotify_title: title,
      pnotify_text: msg,
      //pnotify_type: 'error',
      //pnotify_type: 'notice',
      pnotify_type: type,
      pnotify_hide: false,
      pnotify_closer: true,
      pnotify_nonblock: false,
      pnotify_animate_speed: 0,
      pnotify_shadow: true,
      pnotify_opacity: 1.0,
      pnotify_mouse_reset: false,
      pnotify_history: false,
      pnotify_notice_icon: "ui-icon ui-icon-comment",
      pnotify_after_init: function(pnotify){
          // Remove the notice if the user mouses over it.
          pnotify.mouseout(function(){
              pnotify.pnotify_remove();
          });
      },
      pnotify_before_open: function(pnotify){
          var timer = setInterval(function(){
              // Remove the interval.
              window.clearInterval(timer);
              pnotify.pnotify_remove();
          }, 2000); // ms
      }
    });
}
EOJSF;
return $jsf;
    }

    //
    function setJsFunction($field) {
        $this->js_function = $field;
    }
    function getJsFunction() {
        return stripslashes($this->js_function);
    }
    //
    function setItemToCartTitle($field) {
        $this->item_to_cart_title = $field;
    }
    function getItemToCartTitle() {
        return $this->item_to_cart_title;
    }
    //
    function setItemToCart($field) {
        $this->item_to_cart = $field;
    }
    function getItemToCart() {
        return $this->item_to_cart;
    }
    //
    function setChangeQuantityIsFaildTitle($field) {
        $this->change_quantity_is_faild_title = $field;
    }
    function getChangeQuantityIsFaildTitle() {
        return $this->change_quantity_is_faild_title;
    }
    //
    function setChangeQuantityIsFaild($field) {
        $this->change_quantity_is_faild = $field;
    }
    function getChangeQuantityIsFaild() {
        return $this->change_quantity_is_faild;
    }
    //
    function setSorryStockIsNoTitle($field) {
        $this->sorry_stock_is_no_title = $field;
    }
    function getSorryStockIsNoTitle() {
        return $this->sorry_stock_is_no_title;
    }
    //
    function setSorryStockIsNo($field) {
        $this->sorry_stock_is_no = $field;
    }
    function getSorryStockIsNo() {
        return $this->sorry_stock_is_no;
    }
    //
    function setOutOfStockTitle($field) {
        $this->out_of_stock_title = $field;
    }
    function getOutOfStockTitle() {
        return $this->out_of_stock_title;
    }
    //
    function setOutOfStock($field) {
        $this->out_of_stock = $field;
    }
    function getOutOfStock() {
        return $this->out_of_stock;
    }
    //
    function setMaxQuantityTitle($field) {
        $this->max_quantity_title = $field;
    }
    function getMaxQuantityTitle() {
        return $this->max_quantity_title;
    }
    //
    function setMaxQuantity($field) {
        $this->max_quantity = $field;
    }
    function getMaxQuantity() {
        return $this->max_quantity;
    }
    //
    function setMaxQuantityProductTitle($field) {
        $this->max_quantity_product_title = $field;
    }
    function getMaxQuantityProductTitle() {
        return $this->max_quantity_product_title;
    }
    //
    function setMaxQuantityProduct($field) {
        $this->max_quantity_product = $field;
    }
    function getMaxQuantityProduct() {
        return $this->max_quantity_product;
    }
    //
    function setOffTheItemTitle($field) {
        $this->off_the_item_title = $field;
    }
    function getOffTheItemTitle() {
        return $this->off_the_item_title;
    }
    //
    function setOffTheItem($field) {
        $this->off_the_item = $field;
    }
    function getOffTheItem() {
        return $this->off_the_item;
    }
    //
    function setCartIsEmptyTitle($field) {
        $this->cart_is_empty_title = $field;
    }
    function getCartIsEmptyTitle() {
        return $this->cart_is_empty_title;
    }
    //
    function setCartIsEmpty($field) {
        $this->cart_is_empty = $field;
    }
    function getCartIsEmpty() {
        return $this->cart_is_empty;
    }
    //
    function setQuantityChangedTitle($field) {
        $this->quantity_changed_title = $field;
    }
    function getQuantityChangedTitle() {
        return $this->quantity_changed_title;
    }
    //
    function setQuantityChanged($field) {
        $this->quantity_changed = $field;
    }
    function getQuantityChanged() {
        return $this->quantity_changed;
    }
    //
    function setDontLoadPnotifyCss($fields) {
        $this->dont_load_css = $fields;
    }
    function getDontLoadPnotifyCss() {
        return $this->dont_load_css;
    }
}

/* main class */
class WpCFShoppingcartPnotify {
    var $wpCFShoppingcart;
    var $view;
    var $model;
    var $common;
    var $request;
    var $plugin_name;
    var $plugin_fullpath, $plugin_path, $plugin_folder, $plugin_uri;
    
    // constructor
    function WpCFShoppingcartPnotify($obj) {
        $this->plugin_name = 'cfshoppingcart_pnotify';
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
        
        //printf("<p>Debug[%s, %s]</p>", strtolower(get_class($option)), strtolower('WpCFShoppingcartPnotifyModel'));
        
        // Restore the model object if it is registered
        if (strtolower(get_class($option)) === strtolower('WpCFShoppingcartPnotifyModel') && $data_clear == 0) {
            $model = $option;
        } else {
            // create model instance if it is not registered,
            // register it to Wordpress
            $model = new WpCFShoppingcartPnotifyModel();
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
        $model->setJsFunction($js_function);
        //
        $model->setItemToCartTitle($item_to_cart_title);
        $model->setItemToCart($item_to_cart);
        $model->setChangeQuantityIsFaildTitle($change_quantity_is_faild_title);
        $model->setChangeQuantityIsFaild($change_quantity_is_faild);
        $model->setSorryStockIsNoTitle($sorry_stock_is_no_title);
        $model->setSorryStockIsNo($sorry_stock_is_no);
        $model->setOutOfStockTitle($out_of_stock_title);
        $model->setOutOfStock($out_of_stock);
        $model->setMaxQuantityTitle($max_quantity_title);
        $model->setMaxQuantity($max_quantity);
        $model->setMaxQuantityProductTitle($max_quantity_product_title);
        $model->setMaxQuantityProduct($max_quantity_product);
        $model->setOffTheItemTitle($off_the_item_title);
        $model->setOffTheItem($off_the_item);
        $model->setCartIsEmptyTitle($cart_is_empty_title);
        $model->setCartIsEmpty($cart_is_empty);
        $model->setQuantityChangedTitle($quantity_changed_title);
        $model->setQuantityChanged($quantity_changed);
        //
        $model->setDontLoadPnotifyCss($dont_load_css);
        
        //
        $this->updateWpOption($model); // Update database-model
        $msg .= __('Updated', 'cfshoppingcart');
        return $msg;
    }
    
    function getVisualEditor() {
        $model = $this->wpCFShoppingcart->model;
        //var_dump($model);
        return $model->getVisualEditor();
        //return $this->visual_editor;
    }
    
    function edit($obj, $msg = '') {
        $model = & $obj->model;
        if ($msg) {
            echo '<div id="message" class="updated"><p>' . $msg . '</p></div>';
        }
        
?>

<div class="postbox cfshoppingcart_postbox closed">
  <div class="handlediv" title="Click to toggle"><br /></div>
  <h3><?php _e('Pnotify Options','cfshoppingcart');?></h3>
  <div class="inside">

<table class="form-table">
  <caption><?php _e('Title and messages','cfshoppingcart');?></caption>

<tr>
<th scope="row"><?php _e('"Item to cart."','cfshoppingcart');?></th>
<td><input type="text" name="item_to_cart" value="<?php echo $model->getItemToCart();?>" size="50" /></td>
</tr>
<tr>
<th scope="row"><?php _e('Title of "Item to cart."','cfshoppingcart');?></th>
<td><input type="text" name="item_to_cart_title" value="<?php echo $model->getItemToCartTitle();?>" size="50" /></td>
</tr>

<tr>
<th scope="row"><?php _e('"Change quantity is faild."','cfshoppingcart');?></th>
<td><input type="text" name="change_quantity_is_faild" value="<?php echo $model->getChangeQuantityIsFaild();?>" size="50" /></td>
</tr>
<tr>
<th scope="row"><?php _e('Title of "Change quantity is faild."','cfshoppingcart');?></th>
<td><input type="text" name="change_quantity_is_faild_title" value="<?php echo $model->getChangeQuantityIsFaildTitle();?>" size="50" /></td>
</tr>

<tr>
<th scope="row"><?php _e('"Sorry, stock is no."','cfshoppingcart');?></th>
<td><input type="text" name="sorry_stock_is_no" value="<?php echo $model->getSorryStockIsNo();?>" size="50" /></td>
</tr>
<tr>
<th scope="row"><?php _e('Title of "Sorry, stock is no."','cfshoppingcart');?></th>
<td><input type="text" name="sorry_stock_is_no_title" value="<?php echo $model->getSorryStockIsNoTitle();?>" size="50" /></td>
</tr>

<tr>
<th scope="row"><?php _e('"Out of stock."','cfshoppingcart');?></th>
<td><input type="text" name="out_of_stock" value="<?php echo $model->getOutOfStock();?>" size="50" /></td>
</tr>
<tr>
<th scope="row"><?php _e('Title of "Out of stock."','cfshoppingcart');?></th>
<td><input type="text" name="out_of_stock_title" value="<?php echo $model->getOutOfStockTitle();?>" size="50" /></td>
</tr>

<tr>
<th scope="row"><?php _e('"Max quantity of total order is ..."','cfshoppingcart');?></th>
<td><input type="text" name="max_quantity" value="<?php echo $model->getMaxQuantity();?>" size="50" /></td>
</tr>
<tr>
<th scope="row"><?php _e('Title of "Max quantity of total order is ..."','cfshoppingcart');?></th>
<td><input type="text" name="max_quantity_title" value="<?php echo $model->getMaxQuantityTitle();?>" size="50" /></td>
</tr>

<tr>
<th scope="row"><?php _e('"Max quantity of one product is ..."','cfshoppingcart');?></th>
<td><input type="text" name="max_quantity_product" value="<?php echo $model->getMaxQuantityProduct();?>" size="50" /></td>
</tr>
<tr>
<th scope="row"><?php _e('Title of "Max quantity of one product is ..."','cfshoppingcart');?></th>
<td><input type="text" name="max_quantity_product_title" value="<?php echo $model->getMaxQuantityProductTitle();?>" size="50" /></td>
</tr>

<tr>
<th scope="row"><?php _e('"Off the item."','cfshoppingcart');?></th>
<td><input type="text" name="off_the_item" value="<?php echo $model->getOffTheItem();?>" size="50" /></td>
</tr>
<tr>
<th scope="row"><?php _e('Title of "Off the item."','cfshoppingcart');?></th>
<td><input type="text" name="off_the_item_title" value="<?php echo $model->getOffTheItemTitle();?>" size="50" /></td>
</tr>

<tr>
<th scope="row"><?php _e('"Shopping Cart is empty."','cfshoppingcart');?></th>
<td><input type="text" name="cart_is_empty" value="<?php echo $model->getCartIsEmpty();?>" size="50" /></td>
</tr>
<tr>
<th scope="row"><?php _e('Title of "Shopping Cart is empty."','cfshoppingcart');?></th>
<td><input type="text" name="cart_is_empty_title" value="<?php echo $model->getCartIsEmptyTitle();?>" size="50" /></td>
</tr>

<tr>
<th scope="row"><?php _e('"Quantity has changed."','cfshoppingcart');?></th>
<td><input type="text" name="quantity_changed" value="<?php echo $model->getQuantityChanged();?>" size="50" /></td>
</tr>
<tr>
<th scope="row"><?php _e('Title of "Quantity has changed."','cfshoppingcart');?></th>
<td><input type="text" name="quantity_changed_title" value="<?php echo $model->getQuantityChangedTitle();?>" size="50" /></td>
</tr>

</table>

<table class="form-table">
  <caption><?php _e('css','cfshoppingcart');?></caption>
  <tr><th><?php _e("Don't load css", 'cfshoppingcart');?></th><td><input type="checkbox" name="dont_load_css" value="checked" <?php echo $model->getDontLoadPnotifyCss();?> /> <?php _e('Enabled','cfshoppingcart');?></td></tr>
</table>

<table class="form-table">
  <caption><?php _e('Javascript function','cfshoppingcart');?></caption>
<tr>
<th scope="row"> </th>
<td><textarea cols="40" rows="15" name="js_function"><?php echo $model->getJsFunction();?></textarea></td>
</tr>
<tr>
<th scope="row"> </th>
<td><input type="submit" name="js_function_set_default" value="<?php _e('Load default function and Save','cfshoppingcart');?>" /></td>
</tr>
</table>

    <div class="submit">
        <input type="submit" name="update_pnotify_options" value="<?php _e('Update Options','cfshoppingcart');?> &raquo;" class="button-primary" />
    </div>


    </div>
  </div>
    
    
    <?php
  }
    
} // class

/*
  // add_wp_head.php...
add_action('wp_head', function() {
    if (!$this->model->getDontLoadPnotifyCss()) {
        $plugin_uri = $this->wpCFShoppingcart->common->get_plugin_uri();
        echo '<link type="text/css" rel="stylesheet" href="';
        echo $plugin_uri . '/js/jquery.pnotify.default.css" />' . "\n";
        echo '<link type="text/css" rel="stylesheet" href="';
        echo $plugin_uri . '/js/jquery-ui.css" />' . "\n";
    }
});
*/

add_filter('cfshoppingcart_pnotify_put_configuration', 'cfshoppingcart_pnotify_put_configuration',12,1);
function cfshoppingcart_pnotify_put_configuration($obj) {
    //var_dump($args);
    //print_r($args);
    //$obj = $args->WpCFShoppingcart;
    //var_dump($obj);
    $pp = new WpCFShoppingcartPnotify($obj);
    if (isset($_POST['update_pnotify_options'])) {
        $pp_msg = $pp->save();
    }
    if (isset($_POST['js_function_set_default'])) {
        $model = $pp->model;
        $_REQUEST['js_function'] = $model->get_js_function();
        $pp_msg = $pp->save();
        $pp_msg .= '<br />' . __('Load default function.','cfshoppingcart');
    }
    $pp->edit($pp, $pp_msg);
}

?>
