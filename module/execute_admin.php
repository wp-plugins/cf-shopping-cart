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
    <div class="wrap cfshoppingcart_admin">
    <div id="icon-plugins" class="icon32"><br/></div>
    <h2><?php _e('Cf Shopping Cart', 'cfshoppingcart'); ?></h2>
    <form name="formCFShoppingcart" method="post">
    <div id="poststuff" class="meta-box-sortables" style="position: relative; margin-top:10px;">
      
    <?php
    if (isset($save)) {
        $msg = save($obj);
    }
    edit($obj, $msg);
    
    echo '</div>';
    echo '</form>';
    echo '</div>';
}

function save(&$obj) {
    //print_r($_REQUEST);
    if (is_array($_REQUEST)) {
        // Array extract to variable
        extract($_REQUEST);
    }
    
    //require_once('common.php');
    /*
    $shipping_php_path = get_shipping_php_path();
    if (!file_exists($shipping_php_path)) {
        $is_use_shipping = '';
    }
      */
    
    //print $custom_fields;
    $model = &$obj->model;
    
    $model->set_version($model->get_current_version());
    
    $model->setCustomFields($custom_fields);
    $model->setPriceFieldName($price_field_name);
    //
    $model->setNumberOfStockFieldName($number_of_stock_field_name);
    $model->setProductNameFieldName($product_name_field_name);
    $model->setSoldOutMessage($sold_out_message);
    $model->setTypeOfShowSoldOutMessage($type_of_show_sold_out_message);
    //
    $model->setLinkToProductFieldName($link_to_product_field_name);
    $model->setOpenProductLinkToAnotherWindow($open_product_link_to_another_window);
    //
    //$model->setCurrency_before($currency_before);
    //$model->setCurrency_after($currency_after);
    $model->setCurrencyFormat($currency_format);
    $model->setPostidFormat($postid_format);
    $model->setQuantity($quantity);
    $model->setCartUrl($cart_url);
    $model->setSendOrderUrl($send_order_url);
    $model->setQfgetthumbOption1($qfgetthumb_option_1);
    $model->setQfgetthumbDefaultImage($qfgetthumb_default_image);
    //$model->setIsUseShipping($is_use_shipping);
    //$model->setJustAMomentPlease($cfshoppingcart_justamomentplease);
    $model->setMaxQuantityOfOneCommodity($max_quantity_of_one_commodity);
    $model->setMaxQuantityOfTotalOrder($max_quantity_of_total_order);
    $model->setDontLoadCss($dont_load_css);
    $model->setDisplayWaitingAnimation($display_waiting_animation);
    $model->setDebug($is_debug);
    $model->setVisualEditor($visual_editor);

    $model->setShowCommodityOnHome($show_commodity_on_home);
    $model->setShowCommodityOnPage($show_commodity_on_page);
    $model->setShowCommodityOnArchive($show_commodity_on_archive);
    $model->setShowCommodityOnSingle($show_commodity_on_single);
    $model->setShowCommodityOnManually($show_commodity_on_manually);
    $model->setShowProductsCategoryNumbers($show_products_category_numbers);
    //
    $model->setGoToCartText($go_to_cart_text);
    $model->setOrdererInputScreenText($orderer_input_screen_text);
    $model->setThanksUrl($thanks_url);
    //
    $model->setShipping($shipping);
    $model->setShippingEnabled($shipping_enabled);
    //
    $model->setShopNowClosed($shop_now_closed);
    $model->setClosedMessageForSidebarWidget($closed_message_for_sidebar_widget);
    //print_r($model);
    $model->setBeDontShowEmptyField($be_dont_show_empty_field);
    //
    $model->setTableTag($table_tag);
    //
    $model->setCustomFieldDefaultValue($custom_field_default_value);
    $model->setCustomFieldDefaultValueRaw($custom_field_default_value);
    //
    $model->setShowCustomFieldWhenPriceFieldIsEmpty($show_custom_field_when_price_field_is_empty);

    $obj->updateWpOption($model); // Save database-model
    $msg .= __('Saved', 'cfshoppingcart');
    return $msg;
}

function edit(&$obj, $msg = '') {
    //require_once('common.php');
    //$cfshoppingcart_common = /* php4_110323 & new */ new cfshoppingcart_common();
    global $cfshoppingcart_common;

    // old version shipping
    //$shipping_php_path = get_shipping_php_path();
    /*
    if (!file_exists($shipping_php_path)) {
        $msg .= '<p>' . __('Can not use Shipping.','cfshoppingcart') . '<br />' . __('Shipping setting file not found','cfshoppingcart') . ': "' . $shipping_php_path . '"</p>';
    }
      */
    

    $model = $obj->model;

    // update message.
    $current_version = $model->get_current_version();
    if ($model->get_version() !== $current_version) {
        $msg .= '<p><a href="http://takeai.silverpigeon.jp/?page_id=727" target="_blank">Cf Shopping Cart needs your support. Please donate today. Your contribution is needed for making this plugin better.</a></p>';
    }
    if ($msg) {
        echo '<div id="message" class="updated"><p>' . $msg . '</p></div>';
    }

    // Custom Field array and string
    $custom_fields_array = $model->getCustomFields();
    $custom_fields = $model->getCustomFieldsString();
    //echo '$model->getCustomFields() = ' . $model->getCustomFields() . ']';
    ?>

    <div class="cfshoppingcart_admin-links"><a href="http://takeai.silverpigeon.jp/">blog</a> | <a href="http://cfshoppingcart.silverpigeon.jp/">website</a> | <a href="http://takeai.silverpigeon.jp/?page_id=727">donate</a></div>


      
  <div class="postbox cfshoppingcart_postbox">
    <div class="handlediv" title="Click to toggle"><br /></div>
    <h3><?php _e('Options','cfshoppingcart');?></h3>
    <div class="inside">
      
        <table class="form-table">
        
        <tr><td><?php _e('Shop now closed', 'cfshoppingcart');?></td><td><input type="checkbox" name="shop_now_closed" value="checked" <?php echo $model->getShopNowClosed();?> /> <?php _e('Closed','cfshoppingcart');?> <?php _e("(Be 'Shop Closed' is user level less than 6.)",'cfshoppingcart');?></td></tr>
        <tr><td><?php _e('Closed message for Sidebar widget', 'cfshoppingcart');?> </td><td><input type="text" name="closed_message_for_sidebar_widget" id="closed_message_for_sidebar_widget" value="<?php echo $model->getClosedMessageForSidebarWidget();?>" size="60" /></td></tr>

        <tr><td><?php _e('Custom field names', 'cfshoppingcart');?> </td><td><input type="text" name="custom_fields" id="custom_fields" value="<?php echo $custom_fields;?>" size="60" /> </td></tr>
        <tr><td><?php _e('Price field name', 'cfshoppingcart');?> </td><td><input type="text" name="price_field_name" id="price_field_name" value="<?php echo $model->getPriceFieldName();?>" size="60" /></td></tr>
          
        <tr><td><?php _e('Be Custom Field name of linking to product page', 'cfshoppingcart');?> </td><td><input type="text" name="link_to_product_field_name" id="link_to_product_field_name" value="<?php echo $model->getLinkToProductFieldName();?>" size="60" /> <input type="checkbox" name="open_product_link_to_another_window" value="checked" <?php echo $model->getOpenProductLinkToAnotherWindow();?> /> <?php _e('Open another window','cfshoppingcart');?></td></tr>
          
        <tr><td><?php _e('Number of stock field name', 'cfshoppingcart');?> </td><td><input type="text" name="number_of_stock_field_name" id="number_of_stock_field_name" value="<?php echo $model->getNumberOfStockFieldName();?>" size="60" /> <?php _e("Empty is don't manage stock.",'cfshoppingcart');?></td></tr>
        <tr><td><?php _e('Product name field name', 'cfshoppingcart');?> </td><td><input type="text" name="product_name_field_name" id="product_name_field_name" value="<?php echo $model->getProductNameFieldName();?>" size="60" /></td></tr>
        <tr><td><?php _e("Show Custom Field when price field is empty.", 'cfshoppingcart');?></td><td><input type="checkbox" name="show_custom_field_when_price_field_is_empty" value="checked" <?php echo $model->getShowCustomFieldWhenPriceFieldIsEmpty();?> /> <?php _e('Enabled','cfshoppingcart');?></td></tr>
        <tr><td><?php _e("Be don't show empty field", 'cfshoppingcart');?></td><td><input type="checkbox" name="be_dont_show_empty_field" value="checked" <?php echo $model->getBeDontShowEmptyField();?> /> <?php _e('Enabled','cfshoppingcart');?></td></tr>

        <tr><td><?php _e('Custom Field default value', 'cfshoppingcart');?> <p> <?php _e("example: <br />FieldName1=value1<br />FieldName2=value2<br />...", 'cfshoppingcart');?></p></td><td><textarea name="custom_field_default_value" id="custom_field_default_value" cols="50" rows="5"><?php echo $model->getCustomFieldDefaultValueRaw();?></textarea></td></tr>
          
        <tr><td><?php _e('Type of show sold out message', 'cfshoppingcart');?> </td><td><?php echo $model->getTypeOfShowSoldOutMessageListHtml();?> <?php _e('if select "Don\'t show the post" then to be private the post at sold out.','cfshoppingcart');?></td></tr>
        <tr><td><?php _e('Sold out message', 'cfshoppingcart');?> </td><td><input type="text" name="sold_out_message" id="sold_out_message" value="<?php echo $model->getSoldOutMessage();?>" size="60" /></td></tr>

       <tr><td><?php _e('Quantity', 'cfshoppingcart');?></td><td><input type="text" name="quantity" id="quantity" value="<?php echo $model->getQuantity();?>" size="60" /></td></tr>
        <tr><td><?php _e('Currency format', 'cfshoppingcart');?></td><td><input type="text" name="currency_format" id="currency_format" value="<?php echo $model->getCurrencyFormat();?>" size="10" /> <?php _e('example: $%.02f','cfshoppingcart');?></td></tr>
        <tr><td><?php _e('"#postid" keyword format', 'cfshoppingcart');?></td><td><input type="text" name="postid_format" id="postid_format" value="<?php echo $model->getPostidFormat();?>" size="10" /> <?php _e('example: %05d','cfshoppingcart');?></td></tr>
        <tr><td><?php _e('Max quantity of one commodity', 'cfshoppingcart');?></td><td><input type="text" name="max_quantity_of_one_commodity" id="max_quantity_of_one_commodity" value="<?php echo $model->getMaxQuantityOfOneCommodity();?>" size="10" /> <?php _e('Zero is no limit.','cfshoppingcart');?></td></tr>
        <tr><td><?php _e('Max quantity of total order', 'cfshoppingcart');?></td><td><input type="text" name="max_quantity_of_total_order" id="max_quantity_of_total_order" value="<?php echo $model->getMaxQuantityOfTotalOrder();?>" size="10" /> <?php _e('Zero is no limit.','cfshoppingcart');?></td></tr>
        <tr><td><?php _e('"Go to Cart" text', 'cfshoppingcart');?></td><td><input type="text" name="go_to_cart_text" id="go_to_cart_text" value="<?php echo $model->getGoToCartText();?>" size="40" /></td></tr>
          
          
        <?php if ($model->getVisualEditor()) { ?>
          <tr><td><?php _e('"Orderer Input screen" text', 'cfshoppingcart');?></td><td><div class="postarea postdivrich"><?php the_editor(stripslashes($model->getOrdererInputScreenText()), 'orderer_input_screen_text','orderer_input_screen_text',true); ?></div></td></tr>
        <?php } else { ?>
          <tr><td><?php _e('"Orderer Input screen" text', 'cfshoppingcart');?></td><td><textarea name="orderer_input_screen_text" id="orderer_input_screen_text" cols="50" rows="5"><?php echo stripslashes($model->getOrdererInputScreenText());?></textarea></td></tr>
        <?php } ?>
          
          
        <tr><td><?php _e('Table tag type','cfshoppingcart');?></td><td><?php echo $model->getTableTagListHtml();?></td></tr>
<?php/*
          <tr><td><?php _e('Shipping', 'cfshoppingcart');?></td><td><input type="checkbox" name="is_use_shipping" value="checked" <?php echo $model->getIsUseShipping();?> /> <?php echo __('Must be edit','cfshoppingcart') . ': ' . $shipping_php_path; ?> <font color="red"><?php echo __('* I think this option will be removed, next update.','cfshoppingcart');?></font></td></tr>
       */?>
        <tr><td><?php _e('Cart Url', 'cfshoppingcart');?></td><td><input type="text" name="cart_url" id="cart_url" value="<?php echo $model->getCartUrl();?>" size="60" /></td></tr>
        <tr><td><?php _e('Send order Url', 'cfshoppingcart');?></td><td><input type="text" name="send_order_url" id="send_order_url" value="<?php echo $model->getSendOrderUrl();?>" size="60" /></td></tr>
        <tr><td><?php _e('Thanks Url', 'cfshoppingcart');?></td><td><input type="text" name="thanks_url" id="thanks_url" value="<?php echo $model->getThanksUrl();?>" size="60" /></td></tr>
        <tr><td><?php _e('In Cart, QF-GetThumb option 1', 'cfshoppingcart');?></td><td><input type="text" name="qfgetthumb_option_1" id="qfgetthumb_option_1" value="<?php echo $model->getQfgetthumbOption1();?>" size="60" /></td></tr>
        <tr><td><?php _e('In Cart, QF-GetThumb default image', 'cfshoppingcart');?></td><td><input type="text" name="qfgetthumb_default_image" id="qfgetthumb_default_image" value="<?php echo $model->getQfgetthumbDefaultImage();?>" size="60" /></td></tr>
        <tr><td><?php _e('Choice show commodity on page', 'cfshoppingcart');?></td><td><input type="checkbox" name="show_commodity_on_home" value="checked" <?php echo $model->getShowCommodityOnHome();?> /> <?php _e('home','cfshoppingcart');?> <input type="checkbox" name="show_commodity_on_page" value="checked" <?php echo $model->getShowCommodityOnPage();?> /> <?php _e('page','cfshoppingcart');?> <input type="checkbox" name="show_commodity_on_archive" value="checked" <?php echo $model->getShowCommodityOnArchive();?> /> <?php _e('archive','cfshoppingcart');?> <input type="checkbox" name="show_commodity_on_single" value="checked" <?php echo $model->getShowCommodityOnSingle();?> /> <?php _e('single','cfshoppingcart');?> <input type="checkbox" name="show_commodity_on_manually" value="checked" <?php echo $model->getShowCommodityOnManually();?> /> <?php _e('manually (must edit theme)','cfshoppingcart');?> <br /><?php _e('Show products category numbers (Example: 1,2,..)', 'cfshoppingcart');?>: <input type="text" name="show_products_category_numbers" id="show_products_category_numbers" value="<?php echo $model->getShowProductsCategoryNumbers();?>" size="30" /></td></tr>
        <tr><td colspan="2"><?php _e('* Choice manually then insert PHP code: ', 'cfshoppingcart');?> '&lt;?php cfshoppingcart(); ?&gt;' to 'archive.php' and 'single.php' files(and more page.php, index.php...) in '<?php echo get_bloginfo( 'template_directory' );?>' directory.</td></tr>
        <tr><td><?php _e("Display waiting animation", 'cfshoppingcart');?></td><td><input type="checkbox" name="display_waiting_animation" value="checked" <?php echo $model->getDisplayWaitingAnimation();?> /> <?php _e('Enabled','cfshoppingcart');?></td></tr>
        <tr><td><?php _e("Don't load css", 'cfshoppingcart');?></td><td><input type="checkbox" name="dont_load_css" value="checked" <?php echo $model->getDontLoadCss();?> /> <?php _e('Enabled','cfshoppingcart');?></td></tr>
        <tr><td><?php _e('Debug mode', 'cfshoppingcart');?></td><td><input type="checkbox" name="is_debug" value="checked" <?php echo $model->getDebug();?> /> <?php _e('Enabled','cfshoppingcart');?></td></tr>
        <tr><td><?php _e('Visual Editor', 'cfshoppingcart');?></td><td><input type="checkbox" name="visual_editor" value="checked" <?php echo $model->getVisualEditor();?> /> <?php _e('Enable the visual editor when setting. Reload this page after update options when change this checkbox.','cfshoppingcart');?></td></tr>

       <?php /* Shipping ****************************************/?>
       <tr><td><?php _e("Shipping", 'cfshoppingcart');?></td><td><input type="checkbox" name="shipping_enabled" value="checked" <?php echo $model->getShippingEnabled();?> /> <?php echo _e('Enabled','cfshoppingcart'); ?></td></tr>
       
       <tr><td> </td><td><input type="text" name="shipping[0][0]" id="shipping[0][0]" value="<?php echo $model->getShipping(0,0);?>" size="10" />: <input type="text" name="shipping[0][1]" id="shipping[0][1]" value="<?php echo $model->getShipping(0,1);?>" size="10" /> <?php echo $model->getShippingListHtml(0,2);?> <?php _e('Total price','cfshoppingcart');?> <?php echo $model->getShippingListHtml(0,3);?> <input type="text" name="shipping[0][4]" id="shipping[0][4]" value="<?php echo $model->getShipping(0,4);?>" size="10" /></td></tr>
       <tr><td> </td><td><input type="text" name="shipping[1][0]" id="shipping[1][0]" value="<?php echo $model->getShipping(1,0);?>" size="10" />: <input type="text" name="shipping[1][1]" id="shipping[1][1]" value="<?php echo $model->getShipping(1,1);?>" size="10" /> <?php echo $model->getShippingListHtml(1,2);?> <?php _e('Total price','cfshoppingcart');?> <?php echo $model->getShippingListHtml(1,3);?> <input type="text" name="shipping[1][4]" id="shipping[1][4]" value="<?php echo $model->getShipping(1,4);?>" size="10" /></td></tr>
       <tr><td> </td><td><input type="text" name="shipping[2][0]" id="shipping[2][0]" value="<?php echo $model->getShipping(2,0);?>" size="10" />: <input type="text" name="shipping[2][1]" id="shipping[2][1]" value="<?php echo $model->getShipping(2,1);?>" size="10" /> <?php echo $model->getShippingListHtml(2,2);?> <?php _e('Total price','cfshoppingcart');?> <?php echo $model->getShippingListHtml(2,3);?> <input type="text" name="shipping[2][4]" id="shipping[2][4]" value="<?php echo $model->getShipping(2,4);?>" size="10" /></td></tr>
       <tr><td> </td><td><input type="text" name="shipping[3][0]" id="shipping[3][0]" value="<?php echo $model->getShipping(3,0);?>" size="10" />: <input type="text" name="shipping[3][1]" id="shipping[3][1]" value="<?php echo $model->getShipping(3,1);?>" size="10" /> <?php echo $model->getShippingListHtml(3,2);?> <?php _e('Total price','cfshoppingcart');?> <?php echo $model->getShippingListHtml(3,3);?> <input type="text" name="shipping[3][4]" id="shipping[3][4]" value="<?php echo $model->getShipping(3,4);?>" size="10" /></td></tr>
       <tr><td> </td><td><input type="text" name="shipping[4][0]" id="shipping[4][0]" value="<?php echo $model->getShipping(4,0);?>" size="10" />: <input type="text" name="shipping[4][1]" id="shipping[4][1]" value="<?php echo $model->getShipping(4,1);?>" size="10" /> <?php echo $model->getShippingListHtml(4,2);?> <?php _e('Total price','cfshoppingcart');?> <?php echo $model->getShippingListHtml(4,3);?> <input type="text" name="shipping[4][4]" id="shipping[4][4]" value="<?php echo $model->getShipping(4,4);?>" size="10" /></td></tr>
       <?php /* End of Shipping **********************************/?>


         <tr><td><input type="submit" name="save" value="<?php _e('Update Options', 'cfshoppingcart')?>&nbsp;&raquo;" class="button-primary" /></td><td></td></tr>

        </table>
    </div>
  </div>



<?php apply_filters('cfshoppingcart_put_configuration', $obj); ?>


<div class="postbox cfshoppingcart_postbox closed">
  <div class="handlediv" title="Click to toggle"><br /></div>
  <h3><?php _e('License', 'cfshoppingcart');?></h3>
  <div class="inside">
    <textarea name="license" class="license" cols="81" rows="21" readonly="readonly"><?php echo $model->getLicenceText(); ?></textarea>
  </div>
</div>



<?php /*
<div class="postbox cfshoppingcart_postbox">
  <div class="handlediv" title="Click to toggle"><br /></div>
  <h3>Custom Field Template Options</h3>
  <div class="inside">
  </div>
</div>

<div class="postbox cfshoppingcart_postbox closed">
  <div class="handlediv" title="Click to toggle"><br /></div>
  <h3>Global Settings</h3>
  <div class="inside">
  </div>
</div>
*/ ?>


<?php
}

?>
