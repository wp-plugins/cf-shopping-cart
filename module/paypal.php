<?php
/*
 * PayPal module
 * -*- Encoding: utf8n -*-
 */

class WpCFShoppingcartPaypalModel {
    // member variable
    var $enable_paypal_options;
    var $paypal_action_url;
    var $paypal_email_address;
    var $paypal_currency;
    var $paypal_collect_address;
    var $use_paypal_profile_shipping;
    var $paypal_return_url;
    var $hide_paypal_cart_image;
    var $automatic_redirection_checkout_page;
    var $paypal_orderer_input_screen_text;
    
    // constructor
    function WpCFShoppingcartPaypalModel() {
        // default value
        $this->enable_paypal_options = '';
        $this->paypal_action_url = 'https://www.paypal.com/cgi-bin/webscr';
        $this->paypal_email_address = get_bloginfo('admin_email');
        $this->paypal_currency = 'USD';
        $this->paypal_collect_address = '';
        $this->use_paypal_profile_shipping = '';
        $this->paypal_return_url = get_bloginfo('url') . '?cfshoppingcart_after_payment_processing=successful';
        $this->hide_paypal_cart_image = '';
        $this->automatic_redirection_checkout_page = '';
        $this->paypal_orderer_input_screen_text = '&raquo;&nbsp;Check out with PayPal';
    }

    //
    function setOrdererInputScreenText($field) {
        //$this->paypal_orderer_input_screen_text = strip_tags($field);
        $this->paypal_orderer_input_screen_text = $field;
    }
    function getOrdererInputScreenText() {
        return $this->paypal_orderer_input_screen_text;
        //return stripslashes($this->paypal_orderer_input_screen_text);
    }
    //
    function setPaypalActionUrl($fields) {
        $this->paypal_action_url = strip_tags($fields);
    }
    function getPaypalActionUrl() {
        return $this->paypal_action_url;
    }
    //
    function setAutomaticRedirectionCheckoutPage($fields) {
        $this->automatic_redirection_checkout_page = strip_tags($fields);
    }
    function getAutomaticRedirectionCheckoutPage() {
        return $this->automatic_redirection_checkout_page;
    }
    //
    function setEnablePaypalOptions($fields) {
        $this->enable_paypal_options = strip_tags($fields);
    }
    function getEnablePaypalOptions() {
        return $this->enable_paypal_options;
    }
    //
    function setPaypalEmailAddress($fields) {
        $this->paypal_email_address = strip_tags($fields);
    }
    function getPaypalEmailAddress() {
        return $this->paypal_email_address;
    }
    //
    function setPaypalCurrency($fields) {
        $this->paypal_currency = strip_tags($fields);
    }
    function getPaypalCurrency() {
        return $this->paypal_currency;
    }
    //
    function setPaypalCollectAddrss($fields) {
        $this->paypal_collect_address = strip_tags($fields);
    }
    function getPaypalCollectAddrss() {
        return $this->paypal_collect_address;
    }
    //
    function setUsePaypalProfileShipping($fields) {
        $this->use_paypal_profile_shipping = strip_tags($fields);
    }
    function getUsePaypalProfileShipping() {
        return $this->use_paypal_profile_shipping;
    }
    //
    function setPaypalReturnUrl($fields) {
        $this->paypal_return_url = strip_tags($fields);
    }
    function getPaypalReturnUrl() {
        return $this->paypal_return_url;
    }
    //
    function setHidePaypalCartImage($fields) {
        $this->hide_paypal_cart_image = strip_tags($fields);
    }
    function getHidePaypalCartImage() {
        return $this->hide_paypal_cart_image;
    }
}

/* main class */
class WpCFShoppingcartPaypal {
    var $wpCFShoppingcart;
    var $view;
    var $model;
    var $common;
    var $request;
    var $plugin_name;
    var $plugin_fullpath, $plugin_path, $plugin_folder, $plugin_uri;
    
    // constructor
    function WpCFShoppingcartPaypal($obj) {
        $this->plugin_name = 'cfshoppingcart_paypal';
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
        
        //printf("<p>Debug[%s, %s]</p>", strtolower(get_class($option)), strtolower('WpCFShoppingcartPaypalModel'));
        
        // Restore the model object if it is registered
        if (strtolower(get_class($option)) === strtolower('WpCFShoppingcartPaypalModel') && $data_clear == 0) {
            $model = $option;
        } else {
            // create model instance if it is not registered,
            // register it to Wordpress
            $model = new WpCFShoppingcartPaypalModel();
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
        $model->setEnablePaypalOptions($enable_paypal_options);
        $model->setPaypalActionUrl($paypal_action_url);
        $model->setPaypalCurrency($paypal_currency);
        $model->setPaypalEmailAddress($paypal_email_address);
        $model->setPaypalCollectAddrss($paypal_collect_address);
        $model->setUsePaypalProfileShipping($use_paypal_profile_shipping);
        $model->setPaypalReturnUrl($paypal_return_url);
        $model->setHidePaypalCartImage($hide_paypal_cart_image);
        $model->setAutomaticRedirectionCheckoutPage($automatic_redirection_checkout_page);
        $model->setOrdererInputScreenText($paypal_orderer_input_screen_text);
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
    
    
    function get_paypal_form() {
        $model = $this->model;
        require_once('common.php');
        $common = new cfshoppingcart_common();
        $cfname = $common->get_session_key();
        $obj = $this->wpCFShoppingcart;
        $cfmodel = $obj->model;
        $product_name_field_name = $cfmodel->getProductNameFieldName();
        $price_field_name = $cfmodel->getPriceFieldName();
        
        $html .= '<form name="cfshoppingcart_paypal_form" action="' . $model->getPaypalActionUrl() . '" method="post">';

        /*
        $html .= '<input type="hidden" name="item_name_1" value="pay2(A)(L)" />';
        $html .= '<input type="hidden" name="amount_1" value="333" />';
        $html .= '<input type="hidden" name="quantity_1" value="1" />';
        $html .= '<input type="hidden" name="item_number" value="" />';

        $html .= '<input type="hidden" name="item_name_2" value="paypal_1" />';
        $html .= '<input type="hidden" name="amount_2" value="100" />';
        $html .= '<input type="hidden" name="quantity_2" value="3" />';
        $html .= '<input type="hidden" name="item_number" value="" />';
         */

        //print_r($_SESSION[$cfname]);
        //print_r($_SESSION[$cfname]['commodities']);
        $shipping = $_SESSION[$cfname]['sum']['shipping'];
        $commodities = $_SESSION[$cfname]['commodities'];
        $i = 0;
        foreach ($commodities as $product_id => $commodity) {
            $i++;
            $item_name = $commodity[$product_name_field_name];
            list($postid, $str) = $this->product_id_to_string($product_id);
            if (!$item_name) {
                $post_obj = get_post($postid);
                $item_name = $post_obj->post_title;
            }
            if ($str) {
                $item_name .= '(' . $str . ')';
            }
            $amount = $commodity[$price_field_name];
            $quantity = $commodity['quantity'];
            
            $html .= '<input type="hidden" name="item_name_' . $i . '" value="' . $item_name . '" />';
            $html .= '<input type="hidden" name="amount_' . $i . '" value="' . $amount . '" />';
            $html .= '<input type="hidden" name="quantity_' . $i . '" value="' . $quantity . '" />';
            $html .= '<input type="hidden" name="item_number_' . $i . '" value="' . $postid . '" />';
        }
        
        //<!-- Use PayPal Profile Based Shipping -->
        //<!-- Not will be displayed when checked -->
        if (!$model->getUsePaypalProfileShipping()) {
            // Don't use PayPal Profile Based Shipping
            $html .= '<input type="hidden" name="shipping_1" value="' . sprintf("%d", $shipping) . '" />';
        }
        
        if (0) {
            //<!-- Must Collect Shipping Address on PayPal -->
            //<!-- Will be displayed when checked -->
            if ($model->getPaypalCollectAddrss()) {
                // Don't Collect Shipping Address on PayPal
                $html .= '<input type="hidden" name="no_shipping" value="1" />';
            }
        } else {
            $html .= '<input type="hidden" name="no_shipping" value="0" />';
        }
        
        $html .= '<input class="paypal_form_submit_button" type="image" src="' . $common->get_plugin_uri() . '/images/paypal_checkout.png" name="submit" class="cfshoppingcart_paypal_checkout_button" alt="' . __("Make payments with PayPal - it's fast, free and secure!",'cfshoppingcart') . '" title="' . __("Make payments with PayPal - it's fast, free and secure!",'cfshoppingcart') . '" />';

        //<!-- Return page -->
        if ($model->getPaypalReturnUrl()) {
            $html .= '<input type="hidden" name="return" value="' . $model->getPaypalReturnUrl() . '" />';
        }
        
        $html .= '<input type="hidden" name="business" value="' . $model->getPaypalEmailAddress() . '" />';
        $html .= '<input type="hidden" name="currency_code" value="' . $model->getPaypalCurrency() . '" />';
        $html .= '<input type="hidden" name="cmd" value="_cart" />';
        $html .= '<input type="hidden" name="upload" value="1" />';
        $html .= '</form>';

        return $html;
    }
    
    
    function product_id_to_string($product_id) {
        $a = array();
        $vl = explode('|', $product_id);
        $postid = array_shift($vl);
        foreach ($vl as $i => $eq) {
            if (preg_match('/^(.*)=(-{0,1}[0-9]*|-{0,1}[0-9]*\.[0-9]*)$/', $eq, $match)) {
                $select = $match[1];
                if (preg_match('/^(.*)_(.*?)$/', $select, $match)) {
                    $select = $match[1] . ' ' . $match[2];
                }
            } else {
                $select = $eq;
            }
            $a[] = $select;
        }
        return array($postid, join(',', $a));
    }
    
    
    function edit($obj, $msg = '') {
        $model = & $obj->model;
        if ($msg) {
            echo '<div id="message" class="updated"><p>' . $msg . '</p></div>';
        }
        
?>

<div class="postbox cfshoppingcart_postbox closed">
  <div class="handlediv" title="Click to toggle"><br /></div>
  <h3><?php _e('PayPal Options','cfshoppingcart');?></h3>
  <div class="inside">

    <table class="form-table">

<tr>
<th scope="row"><?php _e('Enable PayPal Options','cfshoppingcart');?></th>
<td><input type="checkbox" name="enable_paypal_options" value="checked" <?php echo $model->getEnablePaypalOptions();?>/> <?php _e('Enabled','cfshoppingcart');?></td>
</tr>

<tr>
<th scope="row"><?php _e('PayPal Email Address','cfshoppingcart');?></th>
<td><input type="text" name="paypal_email_address" value="<?php echo $model->getPaypalEmailAddress();?>" size="50" /></td>
</tr>

<tr>
<th scope="row"><?php _e('PayPal action URL','cfshoppingcart');?></th>
<td><input type="text" name="paypal_action_url" value="<?php echo $model->getPaypalActionUrl();?>" size="50" /><br />e.g. https://www.paypal.com/cgi-bin/webscr (sandbox: https://www.sandbox.paypal.com/cgi-bin/webscr)</td>
</tr>

<tr>
<th scope="row"><?php _e('Currency','cfshoppingcart');?></th>
<td><input type="text" name="paypal_currency" value="<?php echo $model->getPaypalCurrency();?>" size="6" /> (e.g. USD, EUR, GBP, AUD, JPY)</td>

<tr>
<th scope="row"><?php _e('Use PayPal Profile Based Shipping','cfshoppingcart');?></th>
<td><input type="checkbox" name="use_paypal_profile_shipping" value="checked" <?php echo $model->getUsePaypalProfileShipping();?> /> <?php _e('Check this if you want to use');?> <a href="<?php _e('https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_html_ProfileAndTools#id08A9EF00IQY','cfshoppingcart');?>" target="_blank"><?php _e('PayPal profile based shipping','cfshoppingcart');?></a>. <?php _e('Using this will ignore any other shipping options that you have specified in this plugin.','cfshoppingcart');?></td>
</tr>

<tr>
<th scope="row"><?php _e('Return URL','cfshoppingcart');?></th>
<td><input type="text" name="paypal_return_url" value="<?php echo $model->getPaypalReturnUrl();?>" size="50" /><br /> <?php _e('This is the URL the customer will be redirected to after a successful payment','cfshoppingcart');?><br /><?php _e('example: http://example.com/thanks/<strong>?cfshoppingcart_after_payment_processing=successful</strong>','cfshoppingcart');?></td>
</tr>


<?php if ($this->getVisualEditor()) { ?>
  <tr><th><?php _e('"Check out" text', 'cfshoppingcart');?></th><td><div class="postarea postdivrich"><?php the_editor(stripslashes($model->getOrdererInputScreenText()), 'paypal_orderer_input_screen_text','paypal_orderer_input_screen_text',true); ?></div></td></tr>
<?php } else { ?>
  <tr><td><?php _e('"Orderer Input screen" text', 'cfshoppingcart');?></td><td><textarea name="paypal_orderer_input_screen_text" id="paypal_orderer_input_screen_text" cols="50" rows="5"><?php echo stripslashes($model->getOrdererInputScreenText());?></textarea></td></tr>
<?php } ?>



</table>
    <div class="submit">
        <input type="submit" name="update_paypal_options" value="<?php _e('Update Options','cfshoppingcart');?> &raquo;" class="button-primary" />
    </div>


    </div>
  </div>
    
    
    <?php
  }
    
} // class



add_filter('cfshoppingcart_paypal_put_configuration', 'cfshoppingcart_paypal_put_configuration',12,1);
function cfshoppingcart_paypal_put_configuration($obj) {
    //var_dump($args);
    //print_r($args);
    //$obj = $args->WpCFShoppingcart;
    //var_dump($obj);
    $pp = new WpCFShoppingcartPaypal($obj);
    if (isset($_POST['update_paypal_options'])) {
        $pp_msg = $pp->save();
    }
    $pp->edit($pp, $pp_msg);
}



add_filter('cfshoppingcart_put_checkout_button', 'cfshoppingcart_paypal_put_checkout_button',12,1);
function cfshoppingcart_paypal_put_checkout_button($obj) {
    $content = '';
    //require_once('paypal.php');
    $paypal = new WpCFShoppingcartPaypal($obj);
    $paypal_model = $paypal->model;
    $paypal_mode = $paypal_model->getEnablePaypalOptions();
    if ($paypal_model->getEnablePaypalOptions()) {
        $paypal_html = $paypal->get_paypal_form();
        $paypal_checkout = nl2br(stripslashes($paypal_model->getOrdererInputScreenText()));
    }
    
    $paypal_checkout_link = '<div class="paypal_checkout_link"><a href="javascript:void(0)" onClick="document.cfshoppingcart_paypal_form.submit();">' . $paypal_checkout . '</a></div>';
    
    if ($paypal_mode) {
        $content .= $paypal_html;
        $content .= $paypal_checkout_link;
    }
    return $content;
}

?>
