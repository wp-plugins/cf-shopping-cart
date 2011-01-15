<?php
/*
 * Setting Screen
 * call: execute_admin($this);
 * -*- Encoding: utf8n -*-
 */
function execute_admin(&$obj) {
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
    symbolic_link_for_contactForm7Modules($obj);
    if (isset($save)) {
        $msg = save($obj);
    }
    edit($obj, $msg);
    
    echo '</form>';
}

function symbolic_link_for_contactForm7Modules ($obj) {
    //print $custom_fields;
    $model = &$obj->model;

    $ver = $model->get_version();
    //echo 'ver = ' . $ver;
    
    if ($model->is_dontCreateSymbolicLinkCF7Module()) {
        //echo 'dont crate';
    } else {
        //echo 'create';
        // create symbolic link for contact-form-7 modules
        //$f1 = dirname(__FILE__);
        $f1 = get_plugin_fullpath();
        //$f2 = $f1;
        $f2dir = $f1;
        $f1 .= '/contact-form-7-module/cfshoppingcart.php';
        $f2dir = str_replace('/cf-shopping-cart', '/contact-form-7/modules', $f2dir);
        $f2 = $f2dir . '/cfshoppingcart.php';
        //echo "$f1 $f2 $f2dir";
        
        //unlink($f2);
        //chmod($f2, 0777);
        if (!file_exists($f2)) {
            //echo 'create';
            $old_umask = umask(0);
            $old_dir = fileperms($f2dir);
            chmod($f2dir, 0777);
            symlink($f1, $f2);
            chmod($f2, 0755);
            chmod($f2dir, $old_dir);
            umask($old_umask);
        }
        //unlink($f2);
    }
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
    $model->setDontCreateSymbolicLinkCF7Module($dont_create_symbolic_link_cf7_module);
    $model->setShowCommodityOnHome($show_commodity_on_home);
    $model->setShowCommodityOnPage($show_commodity_on_page);
    $model->setShowCommodityOnArchive($show_commodity_on_archive);
    $model->setShowCommodityOnSingle($show_commodity_on_single);
    $model->setShowCommodityOnManually($show_commodity_on_manually);
    //
    $model->setGoToCartText($go_to_cart_text);
    $model->setOrdererInputScreenText($orderer_input_screen_text);
    $model->setThanksUrl($thanks_url);
    
    //print_r($model);

    $obj->updateWpOption($model); // Save database-model
    $msg .= __('Saved', 'cfshoppingcart');
    return $msg;
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

    <fieldset><legend><?php _e('Options', 'cfshoppingcart');?></legend>
      <div class="infield">
        <table>
        <tr><td><?php _e('Custom field names', 'cfshoppingcart');?> </td><td><input type="text" name="custom_fields" id="custom_fields" value="<?php echo $custom_fields;?>" size="60" /> </td></tr>
        <tr><td><?php _e('Price field name', 'cfshoppingcart');?> </td><td><input type="text" name="price_field_name" id="price_field_name" value="<?php echo $model->getPriceFieldName();?>" size="60" /></td></tr>
        <tr><td><?php _e('Quantity', 'cfshoppingcart');?></td><td><input type="text" name="quantity" id="quantity" value="<?php echo $model->getQuantity();?>" size="60" /></td></tr>
        <tr><td><?php _e('Currency format', 'cfshoppingcart');?></td><td><input type="text" name="currency_format" id="currency_format" value="<?php echo $model->getCurrencyFormat();?>" size="10" /> <?php _e('example: $%.02f','cfshoppingcart');?></td></tr>
        <tr><td><?php _e('Max quantity of one commodity', 'cfshoppingcart');?></td><td><input type="text" name="max_quantity_of_one_commodity" id="max_quantity_of_one_commodity" value="<?php echo $model->getMaxQuantityOfOneCommodity();?>" size="10" /> <?php _e('Zero is no limit.','cfshoppingcart');?></td></tr>
        <tr><td><?php _e('Max quantity of total order', 'cfshoppingcart');?></td><td><input type="text" name="max_quantity_of_total_order" id="max_quantity_of_total_order" value="<?php echo $model->getMaxQuantityOfTotalOrder();?>" size="10" /> <?php _e('Zero is no limit.','cfshoppingcart');?></td></tr>
        <tr><td><?php _e('"Go to Cart" text', 'cfshoppingcart');?></td><td><input type="text" name="go_to_cart_text" id="go_to_cart_text" value="<?php echo $model->getGoToCartText();?>" size="40" /></td></tr>
        <tr><td><?php _e('"Orderer Input screen" text', 'cfshoppingcart');?></td><td><input type="text" name="orderer_input_screen_text" id="orderer_input_screen_text" value="<?php echo $model->getOrdererInputScreenText();?>" size="40" /></td></tr>
        <tr><td><?php _e('Shipping', 'cfshoppingcart');?></td><td><input type="checkbox" name="is_use_shipping" value="checked" <?php echo $model->getIsUseShipping();?> /> <?php echo __('Must be edit','cfshoppingcart') . ': ' . $shipping_php_path; ?></td></tr>
        <tr><td><?php _e('Cart Url', 'cfshoppingcart');?></td><td><input type="text" name="cart_url" id="cart_url" value="<?php echo $model->getCartUrl();?>" size="60" /></td></tr>
        <tr><td><?php _e('Send order Url', 'cfshoppingcart');?></td><td><input type="text" name="send_order_url" id="send_order_url" value="<?php echo $model->getSendOrderUrl();?>" size="60" /></td></tr>
        <tr><td><?php _e('Thanks Url', 'cfshoppingcart');?></td><td><input type="text" name="thanks_url" id="thanks_url" value="<?php echo $model->getThanksUrl();?>" size="60" /></td></tr>
        <tr><td><?php _e('In Cart, QF-GetThumb option 1', 'cfshoppingcart');?></td><td><input type="text" name="qfgetthumb_option_1" id="qfgetthumb_option_1" value="<?php echo $model->getQfgetthumbOption1();?>" size="60" /></td></tr>
        <tr><td><?php _e('In Cart, QF-GetThumb default image', 'cfshoppingcart');?></td><td><input type="text" name="qfgetthumb_default_image" id="qfgetthumb_default_image" value="<?php echo $model->getQfgetthumbDefaultImage();?>" size="60" /></td></tr>
        <tr><td><?php _e('CSS "Just a moment please"', 'cfshoppingcart');?></td><td><input type="text" name="cfshoppingcart_justamomentplease" id="cfshoppingcart_justamomentplease" value="<?php echo $model->getJustAMomentPlease();?>" size="60" /></td></tr>
        <tr><td><?php _e('Choice show commodity on page', 'cfshoppingcart');?></td><td><input type="checkbox" name="show_commodity_on_home" value="checked" <?php echo $model->getShowCommodityOnHome();?> /> <?php _e('home','cfshoppingcart');?> <input type="checkbox" name="show_commodity_on_page" value="checked" <?php echo $model->getShowCommodityOnPage();?> /> <?php _e('page','cfshoppingcart');?> <input type="checkbox" name="show_commodity_on_archive" value="checked" <?php echo $model->getShowCommodityOnArchive();?> /> <?php _e('archive','cfshoppingcart');?> <input type="checkbox" name="show_commodity_on_single" value="checked" <?php echo $model->getShowCommodityOnSingle();?> /> <?php _e('single','cfshoppingcart');?> <input type="checkbox" name="show_commodity_on_manually" value="checked" <?php echo $model->getShowCommodityOnManually();?> /> <?php _e('manually (must edit theme)','cfshoppingcart');?> </td></tr>
        <tr><td colspan="2"><?php _e('* Choice manually then insert PHP code: ', 'cfshoppingcart');?> '&lt;?php cfshoppingcart(); ?&gt;' to 'archive.php' and 'single.php' files(and more page.php, index.php...) in '<?php echo get_bloginfo( 'template_directory' );?>' directory.</td></tr>
        <tr><td><?php _e('Debug mode', 'cfshoppingcart');?></td><td><input type="checkbox" name="is_debug" value="checked" <?php echo $model->getDebug();?> /></td></tr>
        <tr><td><?php _e("Don't create symbolic link for Contact-Form-7 Module", 'cfshoppingcart');?></td><td><input type="checkbox" name="dont_create_symbolic_link_cf7_module" value="checked" <?php echo $model->getDontCreateSymbolicLinkCF7Module();?> /></td></tr>
        <tr><td><input type="submit" name="save" value="<?php _e('Save', 'cfshoppingcart')?>" /></td><td></td></tr>
        </table>
      </div>
    </fieldset>
      
    <?php
}

?>
