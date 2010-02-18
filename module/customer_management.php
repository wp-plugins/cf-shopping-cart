<?php
/*
 * Setting Screen
 * call: customer_management($this);
 * -*- Encoding: utf8n -*-
 */
function customer_management(&$obj) {
    //print_r($_REQUEST);
    //print_r($obj->model);
    //echo phpinfo();

    if (is_array($_REQUEST)) {
        // Array extract to variable
        extract($_REQUEST);
    }

    ?>
    <h2><?php _e('Cf Shopping Cart', 'cfshoppingcart'); ?></h2>
    <form name="formCFShoppingcart" method="post">
      
    <?php
    if (isset($save)) {
        $msg = save($obj);
        edit($obj, $msg);
    } else if (isset($change_customer_management_configuration)) {
        config($obj, $msg);
    } else {
        edit($obj, $msg);
    }
    
    echo '</form>';
}

function save(&$obj) {
    //print_r($_REQUEST);
    if (is_array($_REQUEST)) {
        // Array extract to variable
        extract($_REQUEST);
    }
    
    require_once('common.php');
    $shipping_php_path = get_shipping_php_path();
    if (!file_exists($shipping_php_path)) {
        $is_use_shipping = '';
    }
    
    //print $custom_fields;
    $model = &$obj->model;
    $model->setCustomFields($custom_fields);
    $model->setPriceFieldName($price_field_name);
    //$model->setCurrency_before($currency_before);
    //$model->setCurrency_after($currency_after);
    $model->setCurrencyFormat($currency_format);
    $model->setQuantity($quantity);
    $model->setCartUrl($cart_url);
    $model->setSendOrderUrl($send_order_url);
    $model->setQfgetthumbOption1($qfgetthumb_option_1);
    $model->setQfgetthumbDefaultImage($qfgetthumb_default_image);
    $model->setIsUseShipping($is_use_shipping);
    $model->setJustAMomentPlease($cfshoppingcart_justamomentplease);
    $model->setMaxQuantityOfOneCommodity($max_quantity_of_one_commodity);
    $model->setMaxQuantityOfTotalOrder($max_quantity_of_total_order);
    $model->setDebug($is_debug);
    $model->setShowCommodityOnHome($show_commodity_on_home);
    $model->setShowCommodityOnPage($show_commodity_on_page);
    $model->setShowCommodityOnArchive($show_commodity_on_archive);
    $model->setShowCommodityOnSingle($show_commodity_on_single);
    $model->setShowCommodityOnManually($show_commodity_on_manually);
    
    //print_r($model);

    $obj->updateWpOption($model); // Save database-model
    $msg .= __('Saved', 'cfshoppingcart');
    return $msg;
}

function config(&$obj, $msg = '') {
    if (is_array($_REQUEST)) {
        // Array extract to variable
        extract($_REQUEST);
    }
    if (isset($exit_configuration)) {
        edit(&$obj, $msg);
    }
    if ($msg) {
        printf("<div class=\"msg\">%s</div>", $msg);
    }
    ?>
    <script type="text/javascript">
    //<![CDATA[
    jQuery(document).ready(function(){
        //alert('cfshoppingcart_login.js is ready');
        //load_html();
        jQuery('.add_field').click(function(){
            var t = jQuery('.field').val();
            if (check(t) == false) return;
            var list = jQuery('.select_field_list option').each(function(){
                //alert(jQuery(this).text());
                //alert(jQuery(this).val());
            });
            jQuery('.select_field_list').append(jQuery('<option>').attr({ value: t }).text(t));
            
        });
        
        jQuery('.remove_field').click(function(){
            var list = jQuery('.select_field_list option:selected').each(function(){
                //alert(jQuery(this).text());
                //alert(jQuery(this).val());
                if (!window.confirm('本当にいいんですね？')){
                    return;
                }
                jQuery(this).remove();
            });
        });

        jQuery('.up_field').click(function(){
            var s;
            var a = new Array();
            var list = jQuery('.select_field_list option:selected').each(function(){
                s = jQuery(this).text();
                //jQuery(this).remove();
            });
            if (!s) return;
            var i = 0;
            var j = -1;
            var list = jQuery('.select_field_list option').each(function(){
                var n = jQuery(this).text();
                //alert(n);
                a[i] = n;
                if (n == s) j = i;
                i++;
                //alert(i);
                jQuery(this).remove();
            });
            jj = j;
            if (j > 0) {
                m = a[j-1];
                n = a[j];
                a[j-1] = n;
                a[j] = m;
                jj = j-1;
            }
            for (k = 0; k < i; k++) {
                if (k == jj) {
                    jQuery('.select_field_list').append(jQuery('<option>').attr({ value: a[k], 'selected': 'selected' }).text(a[k]));
                } else {
                    jQuery('.select_field_list').append(jQuery('<option>').attr({ value: a[k] }).text(a[k]));
                }
            }
        });
        
        jQuery('.down_field').click(function(){
            var s;
            var a = new Array();
            var list = jQuery('.select_field_list option:selected').each(function(){
                s = jQuery(this).text();
                //jQuery(this).remove();
            });
            if (!s) return;
            var i = 0;
            var j = -1;
            var list = jQuery('.select_field_list option').each(function(){
                var n = jQuery(this).text();
                //alert(n);
                a[i] = n;
                if (n == s) j = i;
                i++;
                //alert(i);
                jQuery(this).remove();
            });
            jj = j;
            if (j+1 < i) {
                m = a[j+1];
                n = a[j];
                a[j+1] = n;
                a[j] = m;
                jj = j+1;
            }
            for (k = 0; k < i; k++) {
                if (k == jj) {
                    jQuery('.select_field_list').append(jQuery('<option>').attr({ value: a[k], 'selected': 'selected' }).text(a[k]));
                } else {
                 jQuery('.select_field_list').append(jQuery('<option>').attr({ value: a[k] }).text(a[k]));
                }
            }
        });

        function check(t) {
            var a = t.match(/^[a-zA-Z][a-zA-Z0-9_]+[a-zA-Z0-9]$/);
            //alert(a);
            if (!a) {
                msg('Field name size is longer than 3 letter or 3. First letter is a-z and A-Z.');
                return false;
            }
            
            var ret = true;
            var list = jQuery('.select_field_list option').each(function(){
                if (t == jQuery(this).text()) {
                    msg('This field name is already exist.');
                    ret = false;
                    return;
                }
                //alert(jQuery(this).val());
            });
            return ret;
        }
        function msg(s) {
            jQuery('.msg').html(s);
        }
    });
    //]]>
    </script>
    <style type="text/css">
    </style>
    <h2><?php _e('Customer data fields','cfshoppingcart');?></h2>
    <form name="formCFShoppingcart" method="post">
    <div class="msg"></div>
    <input type="hidden" name="change_customer_management_configuration" value="1" />
    <input type="submit" name="save_configuration" value="Save Configuration" />
    <input type="submit" name="exit_configuration" value="Exit Configuration" />

    <p>
    <input type="text" class="field" value="" />
    <input type="button" class="add_field" value="Add field" />
    <input type="button" class="remove_field" value="Remove field" />
    </p>
    <div class="field_list">
    <select class="select_field_list" size="10" style="height:200px;width:200px;">
    <option value="1">aaa</option>
    <option value="2">bbb</option>
    </select>
    </div><!-- /field_list -->
    <input type="button" class="up_field" value="Up" />
    <input type="button" class="down_field" value="Down" />

      </form>
    <?php
}

function edit(&$obj, $msg = '') {
    require_once('common.php');
    $shipping_php_path = get_shipping_php_path();
    if (!file_exists($shipping_php_path)) {
        $msg .= '<p>' . __('Can not use Shipping.','cfshoppingcart') . '<br />' . __('Shipping setting file not found','cfshoppingcart') . ': "' . $shipping_php_path . '"</p>';
    }
    
    if ($msg) {
        printf("<div class=\"msg\">%s</div>", $msg);
    }

    $model = $obj->model;

    // Custom Field array and string
    $custom_fields_array = $model->getCustomFields();
    $custom_fields = $model->getCustomFieldsString();
    //echo '$model->getCustomFields() = ' . $model->getCustomFields() . ']';
    ?>
    <form name="formCFShoppingcart" method="post">

    <input type="submit" name="change_customer_management_configuration" value="Change Customer Management Configuration">

      
    <fieldset><legend><?php _e('Options', 'cfshoppingcart');?></legend>
      <div class="infield">
        <table>
        <tr><td><?php _e('Custom field names', 'cfshoppingcart');?> </td><td><input type="text" name="custom_fields" id="custom_fields" value="<?php echo $custom_fields;?>" size="60" /> </td></tr>
        <tr><td><?php _e('Price field name', 'cfshoppingcart');?> </td><td><input type="text" name="price_field_name" id="price_field_name" value="<?php echo $model->getPriceFieldName();?>" size="60" /></td></tr>
        <tr><td><?php _e('Quantity', 'cfshoppingcart');?></td><td><input type="text" name="quantity" id="quantity" value="<?php echo $model->getQuantity();?>" size="60" /></td></tr>
        <tr><td><?php _e('Currency format', 'cfshoppingcart');?></td><td><input type="text" name="currency_format" id="currency_format" value="<?php echo $model->getCurrencyFormat();?>" size="10" /> <?php _e('example: $%.02f','cfshoppingcart');?></td></tr>
        <tr><td><?php _e('Max quantity of one commodity', 'cfshoppingcart');?></td><td><input type="text" name="max_quantity_of_one_commodity" id="max_quantity_of_one_commodity" value="<?php echo $model->getMaxQuantityOfOneCommodity();?>" size="10" /> <?php _e('Zero is no limit.','cfshoppingcart');?></td></tr>
        <tr><td><?php _e('Max quantity of total order', 'cfshoppingcart');?></td><td><input type="text" name="max_quantity_of_total_order" id="max_quantity_of_total_order" value="<?php echo $model->getMaxQuantityOfTotalOrder();?>" size="10" /> <?php _e('Zero is no limit.','cfshoppingcart');?></td></tr>
        <tr><td><?php _e('Shipping', 'cfshoppingcart');?></td><td><input type="checkbox" name="is_use_shipping" value="checked" <?php echo $model->getIsUseShipping();?> /> <?php echo __('Must be edit','cfshoppingcart') . ': ' . $shipping_php_path; ?></td></tr>
        <tr><td><?php _e('Cart Url', 'cfshoppingcart');?></td><td><input type="text" name="cart_url" id="cart_url" value="<?php echo $model->getCartUrl();?>" size="60" /></td></tr>
        <tr><td><?php _e('Send order Url', 'cfshoppingcart');?></td><td><input type="text" name="send_order_url" id="send_order_url" value="<?php echo $model->getSendOrderUrl();?>" size="60" /></td></tr>
        <tr><td><?php _e('In Cart, QF-GetThumb option 1', 'cfshoppingcart');?></td><td><input type="text" name="qfgetthumb_option_1" id="qfgetthumb_option_1" value="<?php echo $model->getQfgetthumbOption1();?>" size="60" /></td></tr>
        <tr><td><?php _e('In Cart, QF-GetThumb default image', 'cfshoppingcart');?></td><td><input type="text" name="qfgetthumb_default_image" id="qfgetthumb_default_image" value="<?php echo $model->getQfgetthumbDefaultImage();?>" size="60" /></td></tr>
        <tr><td><?php _e('CSS "Just a moment please"', 'cfshoppingcart');?></td><td><input type="text" name="cfshoppingcart_justamomentplease" id="cfshoppingcart_justamomentplease" value="<?php echo $model->getJustAMomentPlease();?>" size="60" /></td></tr>
        <tr><td><?php _e('Choice show commodity on page', 'cfshoppingcart');?></td><td><input type="checkbox" name="show_commodity_on_home" value="checked" <?php echo $model->getShowCommodityOnHome();?> /> <?php _e('home','cfshoppingcart');?> <input type="checkbox" name="show_commodity_on_page" value="checked" <?php echo $model->getShowCommodityOnPage();?> /> <?php _e('page','cfshoppingcart');?> <input type="checkbox" name="show_commodity_on_archive" value="checked" <?php echo $model->getShowCommodityOnArchive();?> /> <?php _e('archive','cfshoppingcart');?> <input type="checkbox" name="show_commodity_on_single" value="checked" <?php echo $model->getShowCommodityOnSingle();?> /> <?php _e('single','cfshoppingcart');?> <input type="checkbox" name="show_commodity_on_manually" value="checked" <?php echo $model->getShowCommodityOnManually();?> /> <?php _e('manually (must edit theme)','cfshoppingcart');?> </td></tr>
        <tr><td colspan="2"><?php _e('* Choice manually then insert PHP code: ', 'cfshoppingcart');?> '&lt;?php cfshoppingcart(); ?&gt;' to 'archive.php' and 'single.php' files(and more page.php, index.php...) in '<?php echo get_bloginfo( 'template_directory' );?>' directory.</td></tr>
        <tr><td><?php _e('Debug mode', 'cfshoppingcart');?></td><td><input type="checkbox" name="is_debug" value="checked" <?php echo $model->getDebug();?> /></td></tr>
        <tr><td><input type="submit" name="save" value="<?php _e('Save', 'cfshoppingcart')?>" /></td><td></td></tr>
        </table>
      </div>
    </fieldset>
    </form>
    <?php
}

?>
