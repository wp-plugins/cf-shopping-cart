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
    if (isset($save)) {
        $msg = save($obj);
    }
    edit($obj, $msg);
    
    echo '</form>';
}

function save(&$obj) {
    //print_r($_REQUEST);
    if (is_array($_REQUEST)) {
        // Array extract to variable
        extract($_REQUEST);
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

function edit(&$obj, $msg = '') {
    if ($msg) {
        printf("<div class=\"msg\">%s</div>", $msg);
    }

    $model = $obj->model;
    
    ?>

    <fieldset><legend><?php _e('Options', 'cfshoppingcart');?></legend>
      <div class="infield">
        <table>
        <tr><td><?php _e('Custom field names', 'cfshoppingcart');?> </td><td><input type="text" name="custom_fields" id="custom_fields" value="<?php echo join(',',$model->getCustomFields());?>" size="60" /> </td></tr>
        <tr><td><?php _e('Price field name', 'cfshoppingcart');?> </td><td><input type="text" name="price_field_name" id="price_field_name" value="<?php echo $model->getPriceFieldName();?>" size="60" /></td></tr>
        <tr><td><?php _e('Quantity', 'cfshoppingcart');?></td><td><input type="text" name="quantity" id="quantity" value="<?php echo $model->getQuantity();?>" size="60" /></td></tr>
        <tr><td><?php _e('Currency format', 'cfshoppingcart');?></td><td><input type="text" name="currency_format" id="currency_format" value="<?php echo $model->getCurrencyFormat();?>" size="10" /> <?php _e('example: $%.02f','cfshoppingcart');?></td></tr>
        <tr><td><?php _e('Max quantity of one commodity', 'cfshoppingcart');?></td><td><input type="text" name="max_quantity_of_one_commodity" id="max_quantity_of_one_commodity" value="<?php echo $model->getMaxQuantityOfOneCommodity();?>" size="10" /> <?php _e('Zero is no limit.','cfshoppingcart');?></td></tr>
        <tr><td><?php _e('Max quantity of total order', 'cfshoppingcart');?></td><td><input type="text" name="max_quantity_of_total_order" id="max_quantity_of_total_order" value="<?php echo $model->getMaxQuantityOfTotalOrder();?>" size="10" /> <?php _e('Zero is no limit.','cfshoppingcart');?></td></tr>
        <tr><td><?php _e('Shipping', 'cfshoppingcart');?></td><td><input type="checkbox" name="is_use_shipping" value="checked" <?php echo $model->getIsUseShipping();?> /> <?php _e('Must edit plugin/cfshoppingcart/extention/shipping.php.','cfshoppingcart');?></td></tr>
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
      
    <?php
}

?>
