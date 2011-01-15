/*
 * cfshoppingcart.js.php
 * -*- Encoding: utf8n -*-
 */

<?php
$wpCfshoppingcart = & new WpCFShoppingcart();
$model = $wpCfshoppingcart->model;
$cfshoppingcart_justamomentplease = $model->getJustAMomentPlease();
?>

jQuery(document).ready(function(){
  //alert('cfshoppingcart.js is ready');
  //load_html();

  cfshoppingcart_set_message_layer();

  jQuery('.add_to_cart_button').click(function(){
    var id = (jQuery(this).attr('name').split('=',2))[1];
    quantitySelector = '.cfshoppingcart_quantity_' + id;
    var quantity = jQuery(quantitySelector).attr('value');
    if (checkNumber(quantity) == false) {
      return;
    }
    jQuery.ajax({
      url: get_get('add_to_cart', id, quantity),
      cache: function(){alert('<?php _e('Communication error','cfshoppingcart');?>');},
      success: function(html){
        //alert(html);
        if (!html) { alert('<?php _e('Add to cart faild, please try again.','cfshoppingcart');?>'); return; }
        var json = eval(html);  // decode JSON
        jQuery('.cfshoppingcart_widget').html(json[0]);
        if (json[1]) { alert(json[1]); }
      }
    });
  });

  jQuery('.change_quantity_button').click(function(){
    var id = (jQuery(this).attr('name').split('=',2))[1];
    quantitySelector = '.cfshoppingcart_quantity_' + id;
    var quantity = jQuery(quantitySelector).attr('value');
    if (checkNumber(quantity) == false) {
      return;
    }
    jQuery.ajax({
      url: get_get('change_quantity_commodity', id, quantity),
      cache: function(){alert('<?php _e('Communication error','cfshoppingcart');?>');},
      success: function(html){
        //alert(html);
        if (!html) { alert('<?php _e('Change quantity faild, please try again.','cfshoppingcart');?>'); return; }
        var json = eval(html);  // decode JSON
        jQuery('.cfshoppingcart_widget').html(json[0]);
        if (json[1]) { alert(json[1]); }
      }
    });
  });

  /***** In cart ******************/

  jQuery('.cfshoppingcart_change_quantity_button').click(function(){
    var id = (jQuery(this).attr('name').split('=',2))[1];
    quantitySelector = '.cfshoppingcart_quantity_' + id;
    var quantity = jQuery(quantitySelector).attr('value');
    if (checkNumber(quantity) == false) {
      return;
    }
    jQuery('#cfshoppingcart_form input').attr("disabled", "disabled");
    cfshoppingcart_message('<?php _e('Just a moment please.', 'cfshoppingcart');?>');
    jQuery.ajax({
      url: get_get('change_quantity', id, quantity),
      cache: function(){alert('<?php _e('Communication error','cfshoppingcart');?>');},
      success: function(html){
        if (!html) {
            alert('<?php _e('Change quantity faild, please try again.','cfshoppingcart');?>');
            jQuery('#cfshoppingcart_form input').attr("disabled", "");
            cfshoppingcart_message('');
            return;
        }
        var json = eval(html);  // decode JSON
        document.location = json[0]; // move url
        if (json[1]) { alert(json[1]); }
      }
    });
  });

  jQuery('.cfshoppingcart_cancel_button').click(function(){
    var id = (jQuery(this).attr('name').split('=',2))[1];
    quantitySelector = '.cfshoppingcart_quantity_' + id;
    var quantity = jQuery(quantitySelector).attr('value');
    if (checkNumber(quantity) == false) {
      return;
    }
    jQuery('#cfshoppingcart_form input').attr("disabled", "disabled");
    cfshoppingcart_message('<?php _e('Just a moment please.', 'cfshoppingcart');?>');
    jQuery.ajax({
      url: get_get('cancel', id, quantity),
      cache: function(){alert('<?php _e('Communication error','cfshoppingcart');?>');},
      success: function(html){
        if (!html) {
            alert('<?php _e('Cancel commodity faild, please try again.','cfshoppingcart');?>');
            jQuery('#cfshoppingcart_form input').attr("disabled", "");
            cfshoppingcart_message('');
            return;
        }
        var json = eval(html);  // decode JSON
        document.location = json[0]; // move url
      }
    });
  });

});

function cfshoppingcart_empty_cart() {
    var thanks = "<?php echo $model->getThanksUrl(); ?>";
    //alert('cfshoppingcart_empty_cart()');
    jQuery.ajax({
      url: get_get('empty_cart', -1, 0),
      cache: function(){alert('<?php _e('Communication error','cfshoppingcart');?>');},
      success: function(html){
        //alert(html);
        if (!html) { alert('<?php _e('Empty cart faild.','cfshoppingcart');?>'); return; }
        var json = eval(html);  // decode JSON
        jQuery('.cfshoppingcart_widget').html(json[0]);
        if (json[1]) { alert(json[1]); }
        if (thanks) { location.replace(thanks); }
      }
    });
}

function get_get(cmd, id, quantity) {
    <?php
    //
    require_once('common.php');
    //$plugin_fullpath = get_plugin_fullpath();
    //$plugin_path = get_plugin_path();
    //$plugin_folder = get_plugin_folder();
    //$plugin_uri = get_plugin_uri();
    $cfshoppingcart_plugin_module_uri = get_plugin_module_uri();
    echo 'var cfshoppingcart_plugin_module_uri = "' . $cfshoppingcart_plugin_module_uri . '";';
    ?>
    return cfshoppingcart_plugin_module_uri + '/commu.php?cmd=' + cmd + '&include=' + id + '&quantity=' + quantity;
}

function checkNumber(quantity) {
  if (checkIsNumber(quantity) == false) {
    alert('<?php _e('Please enter the quantity of the commodity.','cfshoppingcart');?>');
    return false;
  }
  if (quantity < 0) {
    alert('<?php _e('Please enter the quantity of the commodity.','cfshoppingcart');?>');
    return false;
  }
  /*
  if (quantity > 100) {
    alert('<?php _e('Commodity must be less than 100.','cfshoppingcart');?>');
    return false;
  }
  */
  return true;
}

function checkIsNumber (value) {
  return (value.match(/[0-9]+/g) == value);
}


function cfshoppingcart_message(msg) {
    var layer = '#cfshoppingcart_message_layer';
    if (msg) {
        jQuery(layer).html(msg);
        
        var width = jQuery(window).width();
        var height = jQuery(window).height();
        var w = width / 2;
        var h = height / 2;
        var x = (width - w) / 2 + jQuery(window).scrollLeft();;
        var y = (height - h) / 2 + jQuery(window).scrollTop();;
        
        jQuery(layer).css('left',x);
        jQuery(layer).css('top',y);
        
        jQuery(layer).css("width", w + "px");
        jQuery(layer).css("height", h + "px");
        jQuery(layer).css("line-height", h + "px");
        
        jQuery(layer).css("visibility", "visible");
    } else {
        jQuery(layer).css("visibility", "hidden");
    }
}

function cfshoppingcart_set_message_layer() {
    var popuphtml = '<!-- cfshoppingcart_message_layer --><div id="cfshoppingcart_message_layer" style="position: absolute; z-index: 99; visibility: hidden; left: 45px; top: 33px;<?php echo $cfshoppingcart_justamomentplease; ?>"></div><!-- end of cfshoppingcart_message_layer -->';
    jQuery("body").append(popuphtml);
}

