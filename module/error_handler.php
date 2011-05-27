<?php
/* error_handler.php
 *
 * -*- Encoding: utf8n -*-
 */

$wpCfShoppingcartErrorHandler = new WpCFShoppingcartErrorHandler();
if ($wpCfShoppingcartErrorHandler->model->getEnabled()) {
    set_error_handler("cfshoppingcart_errorHandler");
}


function cfshoppingcart_errorHandler($errno, $errstr, $errfile, $errline, $errcontext) {

    global $wpCfShoppingcartErrorHandler;
    $model = $wpCfShoppingcartErrorHandler->model;
    
    if (!(error_reporting() & $errno)) {
        // error_reporting 設定に含まれていないエラーコードです
        return;
    }
    if (!strstr($errfile, 'cf-shopping-cart')) {
        return;
    }
    
    $content = "\n";
    $content .= "errfile: $errfile\n";
    $content .= "errline: $errline\n";
    $content .= "errorno: $errno\n";
    $content .= "errstr: \n$errstr\n--\n";
    $content .= "_SESSION: \n" . var_export($_SESSION, true) . "\n--\n";
    $content .= "_REQUEST: \n" . var_export($_REQUEST, true) . "\n--\n";
    $content .= "var_export: \n" . var_export($errcontext, true);
    $content .= "\n----------------------\n";

    if ($model->getOutput()) {
        echo "<h3>cfshoppingcart_errorHandler</h3><p><pre>$content</pre></p>";
    }

    if ($model->getOutputLog()) {
        require_once('common.php');
        $common = new cfshoppingcart_common();
        $path = $common->get_plugin_fullpath() . '/log';
        $today = getdate();
        $fnf = sprintf("%s/%04d%02d%02d_%02d%02d%02d", $path, $today['year'],$today['mon'],$today['mday'], $today['hours'],$today['minutes'],$today['second']);
        $i = 0;
        do {
            $i++;
            $fn = sprintf("%s_%05d.txt", $fnf, $i);
        } while (file_exists($fn));
        
        
        $fh = fopen($fn, 'a');
        if (!$fh) {
            echo "<p>cfshoppingcart_errorHandler: Can not open: $fn</p>";
            return false;
        }
        fwrite($fh, $content, strlen($content));
        fclose($fh);
    }
    
    $from = $model->getFromEmail();
    $to = $model->getToEmail();
    if ($from && $to) {
        //言語設定、内部エンコーディングを指定する
        if (0) {
            mb_language("japanese");
            mb_internal_encoding("UTF-8");
        }
        //日本語メール送信
        //$from = "takeai@silverpigeon.jp";
        //$to = "takeai@silverpigeon.jp";
        //$subject = "Cf Shopping Cart Error Handler";
        $subject = $model->getSubject();
        $body = $content;
        //$from = "takeai@silverpigeon.jp";
        //$from = get_bloginfo('admin_email');
        //ちゃんと日本語メールが送信できます
        mb_send_mail($to,$subject,$body,"From:".$from);
    }
    
    /* PHP の内部エラーハンドラを実行しません */
    return true;
}


class WpCFShoppingcartErrorHandlerModel {
    var $enabled;
    var $from_email;
    var $to_email;
    var $subject;
    var $output;
    var $output_log;

    function WpCFShoppingcartErrorHandlerModel() {
        $this->enabled = '';
        $this->from_email = get_bloginfo('admin_email');
        $this->to_email = get_bloginfo('admin_email');
        $this->subject = 'Cf Shopping Cart Error Handler';
        $this->output_log = 'checked';
        $this->output = '';
    }
    
    function setEnabled($v) {
        $this->enabled = $v;
    }
    function getEnabled() {
        return $this->enabled;
    }
    //
    function setFromEmail($v) {
        $this->from_email = $v;
    }
    function getFromEmail() {
        return $this->from_email;
    }
    //
    function setToEmail($v) {
        $this->to_email = $v;
    }
    function getToEmail() {
        return $this->to_email;
    }
    //
    function setSubject($v) {
        $this->subject = $v;
    }
    function getSubject() {
        return $this->subject;
    }
    //
    function setOutput($v) {
        $this->output = $v;
    }
    function getOutput() {
        return $this->output;
    }
    //
    function setOutputLog($v) {
        $this->output_log = $v;
    }
    function getOutputLog() {
        return $this->output_log;
    }
    
}

class WpCFShoppingcartErrorHandler {
    //var $wpCFShoppingcart;
    var $model;
    //var $common;
    var $plugin_name;
    
    // constructor
    //function WpCFShoppingcartErrorHandler($obj) {
    function WpCFShoppingcartErrorHandler() {
        $this->plugin_name = 'cfshoppingcart_error_handler';
        $this->model = $this->getModelObject();
        //$this->wpCFShoppingcart = $obj;
        //require_once('module/common.php');
        //$this->common = /* php4_110323 & new */ new cfshoppingcart_common();
        //$this->plugin_uri = $this->common->get_plugin_uri();
    }
    
    // create model object
    function getModelObject() {
        $data_clear = 0; // Debug: 1: Be empty to data
        
        // get option from Wordpress
        $option = $this->getWpOption();
        
        // Restore the model object if it is registered
        if (strtolower(get_class($option)) === strtolower('WpCFShoppingcartErrorHandlerModel') && $data_clear == 0) {
            $model = $option;
        } else {
            // create model instance if it is not registered,
            // register it to Wordpress
            $model = new WpCFShoppingcartErrorHandlerModel();
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
        $model->setEnabled($enabled);
        $model->setFromEmail($from_email);
        $model->setToEmail($to_email);
        $model->setSubject($subject);
        $model->setOutputLog($output_log);
        $model->setOutput($output);
        //
        $this->updateWpOption($model); // Update database-model
        $msg .= __('Updated', 'cfshoppingcart');
        return $msg;
    }
    



    function edit($obj, $msg = '') {
        $model = & $obj->model;
        require_once('common.php');
        $common = new cfshoppingcart_common();
        $output_log_path = $common->get_plugin_fullpath() . '/log';
        
        if ($msg) {
            echo '<div id="message" class="updated"><p>' . $msg . '</p></div>';
        }
        
?>

<div class="postbox cfshoppingcart_postbox closed">
  <div class="handlediv" title="Click to toggle"><br /></div>
  <h3><?php _e('Error Handler Options','cfshoppingcart');?></h3>
  <div class="inside">

    <table class="form-table">

<tr>
<th scope="row"><?php _e('Enable Error Handler','cfshoppingcart');?></th>
<td><input type="checkbox" name="enabled" value="checked" <?php echo $model->getEnabled();?>/> <?php _e('Enabled','cfshoppingcart');?></td>
</tr>

<tr>
<th scope="row"><?php _e('From Email Address','cfshoppingcart');?></th>
<td><input type="text" name="from_email" value="<?php echo $model->getFromEmail();?>" size="100" /></td>
</tr>

<tr>
<th scope="row"><?php _e('To Email Address','cfshoppingcart');?></th>
<td><input type="text" name="to_email" value="<?php echo $model->getToEmail();?>" size="100" /></td>
</tr>

<tr>
<th scope="row"><?php _e('Email subject','cfshoppingcart');?></th>
<td><input type="text" name="subject" value="<?php echo $model->getSubject();?>" size="100" /></td>
</tr>

<tr>
<th scope="row"><?php _e('Output log','cfshoppingcart');?></th>
<td><input type="checkbox" name="output_log" value="checked" <?php echo $model->getOutputLog();?>/> <?php _e('Enabled','cfshoppingcart');?> (<?php _e('Log file will be output to folder:'); echo ' ' . $output_log_path;?>)</td>
</tr>

<tr>
<th scope="row"><?php _e('Output display','cfshoppingcart');?></th>
<td><input type="checkbox" name="output" value="checked" <?php echo $model->getOutput();?>/> <?php _e('Enabled','cfshoppingcart');?></td>
</tr>


</table>
    <div class="submit">
        <input type="submit" name="update_error_handler_options" value="<?php _e('Update Options','cfshoppingcart');?> &raquo;" class="button-primary" />
    </div>


    </div>
  </div>
    
    
    <?php
  }
    
    
} // class



add_filter('cfshoppingcart_put_configuration', 'cfshoppingcart_error_handler_configuration',13,1);
function cfshoppingcart_error_handler_configuration($obj) {
    //var_dump($args);
    //print_r($args);
    //$obj = $args->WpCFShoppingcart;
    //var_dump($obj);
    //$pp = new WpCFShoppingcartErrorHandler($obj);
    $pp = new WpCFShoppingcartErrorHandler();
    if (isset($_POST['update_error_handler_options'])) {
        $pp_msg = $pp->save();
    }
    $pp->edit($pp, $pp_msg);
}

?>