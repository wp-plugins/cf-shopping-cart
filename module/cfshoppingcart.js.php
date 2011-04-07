/*
 * cfshoppingcart.js.php
 * -*- Encoding: utf8n -*-
 */

<?php
$wpCfshoppingcart = /* php4_110323 & new */ new WpCFShoppingcart();
$model = $wpCfshoppingcart->model;
if ($model->is_debug()) {
  echo 'var cfshoppingcart_debug = 1;';
} else {
  echo 'var cfshoppingcart_debug = 0;'; // 
}
//$cfshoppingcart_justamomentplease = $model->getJustAMomentPlease();
//$cfshoppingcart_plugin_commu_uri = $cfshoppingcart_common->get_plugin_module_uri() . '/commu.php';
$widgetEmptyCartHtml = $model->getWidgetEmpyCartHtml();
?>

jQuery(document).ready(function(){
  //alert('cfshoppingcart.js is ready');
  cfshoppingcart_js_init();
});


function cfshoppingcart_pnotify(type, msg, title) {
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

function cfshoppingcart_js_init() {
  //cfshoppingcart_set_message_layer();
  var find_key_value;

  // propaties array
  var options = {
    //target: '.cfshoppingcart_widget', // Out put return html
    beforeSubmit: cfshoppingcart_request, // call function before send
    success: cfshoppingcart_response, // call function after send
    //url: '<?php echo $cfshoppingcart_plugin_commu_uri; ?>', // form action
    type: 'post', // post or get
    datatype:'json', // type of server respons
    timeout: 3000 // timeout
  };

  // Ajax form
  jQuery('.cfshoppingcart_product_id_x').ajaxForm(options);


  /***** In cart ******************/

  //各種プロパティの配列
  var options = {
    //target: '.cfshoppingcart_widget', // Out put return html
    beforeSubmit: cfshoppingcart_request, // call function before send
    success: cfshoppingcart_response, // call function after send
    //url: '<?php echo $cfshoppingcart_plugin_commu_uri; ?>', // form action
    type: 'post', // post or get
    datatype:'json', // type of server respons
    timeout: 3000 // timeout
  };

  // Ajax form
  jQuery('.cfshoppingcart_in_cart_product_id_x').ajaxForm(options);
  
  /***************************************************/
  
  // 
  function cfshoppingcart_request(formData, jqForm, options) {
    //jQuery('.cfshoppingcart_commodity_op').html('');
    //alert(obj2text(formData));
    if (find_key(formData, 'cancel') == true ||
        (find_key(formData, 'change_quantity') == true &&
         find_key(formData, 'quantity') == true && find_key_value == 0)) {
      // click cancel button to product form scroll up hide.
      jqForm.slideToggle("fast");
    }
    //alert(find_key_value);
    if (!cfshoppingcart_debug) { return true; }
    // convert to request string from form object
    var queryString = jQuery.param(formData);
    alert('About to submit: \n\n' + queryString);
    return true;
  }

  //
  function cfshoppingcart_response(responseText, statusText) {
    if (cfshoppingcart_debug) {
      alert('status: ' + statusText + '\n\nresponseText: \n' + responseText);
      //alert('status: ' + statusText + '\n\nresponseText: \n' + obj2text(responseText));
    }
    var json = eval(responseText);  // decode JSON
    if (json == null) {
        if (cfshoppingcart_debug) {
            alert('json == null');
        }
        return;
    }
    //jQuery('.cfshoppingcart_widget').html(json[1]);
    if (json['widget']) {
      jQuery('.cfshoppingcart_widget').html(json['widget']);
    }
    if (json['cart_html']) {
      jQuery('#cfshoppingcart_form').html(json['cart_html']);
    }
    if (json['msg']) {
      cfshoppingcart_pnotify('notice', json['msg'], '<?php _e('Shopping Cart','cfshoppingcart');?>');
    }
    if (json['msg_red']) {
      cfshoppingcart_pnotify('error', json['msg_red'], '<?php _e('Shopping Cart','cfshoppingcart');?>');
    }
    // important. Need cancel product.
    cfshoppingcart_js_init();
  }

  function find_key(obj, keyname) {
    find_key_value = '';
    for (keys in obj) {
      if (typeof obj[keys] == 'object') {
          if (find_key(obj[keys], keyname) == true) { return true; }
      }
      if (keys == 'name' && obj[keys] == keyname) {
        //alert(obj[keys]);
        //alert(obj['value']);
        find_key_value = obj['value'];  // global value
        return true;
      }
    }
    return false;
  }
    
  function obj2text(obj) {
    var ret = '';
    for (keys in obj) {
      if (typeof obj[keys] == 'object') {
          ret += obj2text(obj[keys]);
      } else {
          ret += keys + ' => ' + obj[keys];
      }
    }
    return ret;
  }
    
}

function cfshoppingcart_empty_cart() {
    var thanks = "<?php echo $model->getThanksUrl(); ?>";
    //alert('cfshoppingcart_empty_cart()');
    jQuery('.cfshoppingcart_widget').html('<?php echo $widgetEmptyCartHtml;?>');
    if (thanks) { location.replace(thanks); }
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
  return true;
}

function checkIsNumber (value) {
  return (value.match(/[0-9]+/g) == value);
}

