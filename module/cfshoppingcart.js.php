/*
 * cfshoppingcart.js.php
 * -*- Encoding: utf8n -*-
 */

var cfshoppingcart_js = { version: '1' }

<?php
$wpCfshoppingcart = new WpCFShoppingcart();
$model = $wpCfshoppingcart->model;
if ($model->is_debug()) {
  echo 'cfshoppingcart_js.cfshoppingcart_debug = 1;';
} else {
  echo 'cfshoppingcart_js.cfshoppingcart_debug = 0;';
}
$widgetEmptyCartHtml = $model->getWidgetEmpyCartHtml();
//
$pnotify_obj = new WpCFShoppingcartPnotify($wpCfshoppingcart);
$pnotify = $pnotify_obj->model;
?>

cfshoppingcart_js.body_height = 0;

jQuery(document).ready(function(){
    //alert('cfshoppingcart.js is ready');
    cfshoppingcart_js.init();
    // Reset Ready.functions
    jQuery('*').click(function(){
        var body_height = jQuery("body").height();
        if (cfshoppingcart_js.body_height != body_height) {
            cfshoppingcart_js.body_height = body_height;
            //document.title = document.title + '.';
            // Reset Cf Shopping Cart plugin
            cfshoppingcart_js.init();
            // Reset WP FancyZoom plugin version 1.2
            if (typeof setupZoom == "function") {
                setupZoom();
            }
        }
    });
});


cfshoppingcart_js.find_key_value = '';

cfshoppingcart_js.find_key = function(obj, keyname) {
    cfshoppingcart_js.find_key_value = '';
    for (keys in obj) {
        if (typeof obj[keys] == 'object') {
            if (cfshoppingcart_js.find_key(obj[keys], keyname) == true) { return true; }
        }
        if (keys == 'name' && obj[keys] == keyname) {
            //alert(obj[keys]);
            //alert(obj['value']);
            cfshoppingcart_js.find_key_value = obj['value'];  // global value
            return true;
        }
    }
    return false;
}

cfshoppingcart_js.init = function init() {
    // propaties array
    var options = {
      //target: '.cfshoppingcart_widget_cart', // Out put return html
      beforeSubmit: cfshoppingcart_js.cfshoppingcart_request, // call function before send
      success: cfshoppingcart_js.cfshoppingcart_response, // call function after send
      //url: '<?php echo $cfshoppingcart_plugin_commu_uri; ?>', // form action
      type: 'post', // post or get
      datatype:'json', // type of server respons
      timeout: 3000 // timeout
    };
    
    // Ajax form
    jQuery('.cfshoppingcart_product_id_x').ajaxForm(options);
    jQuery('.cfshoppingcart_in_cart_product_id_x').ajaxForm(options);
}

// 
cfshoppingcart_js.cfshoppingcart_request = function(formData, jqForm, options) {
    //jQuery('.cfshoppingcart_commodity_op').html('');
    //alert(cfshoppingcart_js.obj2text(formData));
    //alert(cfshoppingcart_js.obj2text(jqForm));
    //alert(jqForm);
    if (cfshoppingcart_js.find_key(formData, 'cancel') == true ||
        (cfshoppingcart_js.find_key(formData, 'change_quantity') == true &&
         cfshoppingcart_js.find_key(formData, 'quantity') == true && cfshoppingcart_js.find_key_value == 0)) {
        // click cancel button to product form scroll up hide.
        jqForm.slideToggle("fast");
    }
    jqForm.find('.cfshoppingcart_waiting_anm').css('display','inline');
    //alert(find_key_value);
    if (!cfshoppingcart_js.cfshoppingcart_debug) { return true; }
    // convert to request string from form object
    var queryString = jQuery.param(formData);
    alert('About to submit: \n\n' + queryString);
    return true;
}

//
cfshoppingcart_js.cfshoppingcart_response = function(responseText, statusText) {
    jQuery('.cfshoppingcart_waiting_anm').css('display','none');
    if (cfshoppingcart_js.cfshoppingcart_debug) {
        alert('status: ' + statusText + '\n\nresponseText: \n' + responseText);
        //alert('status: ' + statusText + '\n\nresponseText: \n' + obj2text(responseText));
    }
    var json = eval(responseText);  // decode JSON
    if (json == null) {
        if (cfshoppingcart_js.cfshoppingcart_debug) {
            alert('json == null');
        }
        return;
    }
    //jQuery('.cfshoppingcart_widget_cart').html(json[1]);
    if (json['widget']) {
        jQuery('.cfshoppingcart_widget_cart').html(json['widget']);
    }
    if (json['cart_html']) {
        jQuery('#cfshoppingcart_form').html(json['cart_html']);
    }
    if (json['msg']) {
        cfshoppingcart_js.cfshoppingcart_pnotify('notice', json['msg'], json['title']);
    }
    if (json['msg_red']) {
        cfshoppingcart_js.cfshoppingcart_pnotify('error', json['msg_red'], json['title']);
    }
    // important. Need cancel product.
    cfshoppingcart_js.init();
}

<?php echo $pnotify->getJsFunction(); ?>

cfshoppingcart_js.obj2text = function(obj) {
    var ret = '';
    for (keys in obj) {
        if (typeof obj[keys] == 'object') {
            ret += cfshoppingcart_js.obj2text(obj[keys]);
        } else {
            ret += keys + ' => ' + obj[keys];
        }
    }
    return ret;
}

function cfshoppingcart_empty_cart() {
    var thanks = "<?php echo $model->getThanksUrl(); ?>";
    //alert('cfshoppingcart_empty_cart()');
    jQuery('.cfshoppingcart_widget_cart').html('<?php echo preg_replace('/[\r\n]/', '', $widgetEmptyCartHtml);?>');
        if (thanks) { location.replace(thanks); }
}

