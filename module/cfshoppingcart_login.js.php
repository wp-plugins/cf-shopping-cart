/*
 * cfshoppingcart_login.js.php
 * -*- Encoding: utf8n -*-
 */

<?php
$wpCfshoppingcart = & new WpCFShoppingcart();
$model = $wpCfshoppingcart->model;
$cfshoppingcart_justamomentplease = $model->getJustAMomentPlease();
?>

jQuery(document).ready(function(){
    alert('cfshoppingcart_login.js is ready');
    //load_html();
    
    <?php
    require_once('common.php');
    //$plugin_fullpath = get_plugin_fullpath();
    //$plugin_path = get_plugin_path();
    //$plugin_folder = get_plugin_folder();
    //$plugin_uri = get_plugin_uri();
    $plugin_module_uri = get_plugin_module_uri();
    echo 'var plugin_module_uri = "' . $plugin_module_uri . '";';
    ?>
      
    jQuery('.cfshoppingcart_login_button').click(function(){
        var id = (jQuery(this).attr('name').split('=',2))[1];
        quantitySelector = '.cfshoppingcart_quantity_' + id;
        var username = jQuery('.cfshoppingcart_username').attr('value');
        var password = jQuery('.cfshoppingcart_password').attr('value');
        if (checkID(username, password) == false) {
            return;
        }
        jQuery.ajax({
          url: get_get(plugin_module_uri, 'login', username, password),
          cache: function(){alert('<?php _e('Communication error login','cfshoppingcart');?>');},
          success: function(html){
              //alert(html);
              if (!html) { alert('<?php _e('Login faild, please try again.','cfshoppingcart');?>'); return; }
              var json = eval(html);  // decode JSON
              jQuery('.cfshoppingcart_login_widget').html(json[0]);
              if (json[1]) { alert(json[1]); }
          }
        });
    });

    function get_get(plugin_module_uri, cmd, username, password) {
        return plugin_module_uri + '/commu_login.php?cmd=' + cmd + '&username=' + username + '&password=' + password;
    }

    function checkID(username, password) {
        return true;
    }
    
});

