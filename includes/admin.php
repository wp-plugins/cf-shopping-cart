<?php

namespace cfshoppingcart;

function wp_custom_admin_head() {
    ?>
    <link rel="stylesheet" href="<?php echo CF_SHOPPING_CART_PLUGIN_URL; ?>/css/admin.css" type="text/css" media="screen" />
    <?php
}

add_action('admin_head', 'cfshoppingcart\wp_custom_admin_head', 100);

function wp_custom_admin_footer() {
    ?>
    <script type="text/javascript" src="<?php echo CF_SHOPPING_CART_PLUGIN_URL; ?>/lib/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>
    <script type="text/javascript" src="<?php echo CF_SHOPPING_CART_PLUGIN_URL; ?>/lib/jquery.cookie.js"></script>
    <script type="text/javascript" src="<?php echo CF_SHOPPING_CART_PLUGIN_URL; ?>/js/admin.js"></script>
    <?php
}

add_action('admin_footer', 'cfshoppingcart\wp_custom_admin_footer', 100);

function plugin_menu() {
    add_options_page(__('Plugin Options', DOMAIN_CF_SHOPPING_CART), __('Cf Shopping Cart', DOMAIN_CF_SHOPPING_CART), 'administrator', DOMAIN_CF_SHOPPING_CART . '-settings.php', 'cfshoppingcart\plugin_options');
}

add_action('admin_menu', 'cfshoppingcart\plugin_menu');

function get_option_keys_array() {
    global $wpdb;
    $options = $wpdb->get_results("SELECT * FROM $wpdb->options ORDER BY option_name");

    $keys = array();
    foreach ((array) $options as $option) {
        if (!preg_match('/^' . DOMAIN_CF_SHOPPING_CART . '/', $option->option_name)) {
            continue;
        }
        if (is_serialized($option->option_value)) {
            if (is_serialized_string($option->option_value)) {
                // This is a serialized string, so we should display it.
                $value = maybe_unserialize($option->option_value);
            } else {
                $value = 'SERIALIZED DATA';
            }
        } else {
            $value = $option->option_value;
        }
        $k = esc_html($option->option_name);
        if (strpos($value, "\n") !== false) :
            $v = esc_textarea($value);
        else:
            $v = esc_attr($value);
        endif;
        $keys[] = $k;
    }
    return $keys;
}

function uninstall_function() {
    $keys = get_option_keys_array();
    foreach ($keys as $key) {
        delete_option($key);
    }
}

register_uninstall_hook(__FILE__, 'cfshoppingcart\uninstall_function');

function getHtmlSelectArrayAssociative($name, $array, $key_field, $value_field, $defaultValue = null) {
    if (!is_array($array)) {
        $array = array();
    }
    $tag = '<select name="' . $name . '">';
    foreach ($array as $key => $asso) {
        if ($asso->$key_field == $defaultValue) {
            $selected = 'selected';
        } else {
            $selected = '';
        }
        $tag .= '<option value="' . esc_attr($asso->$key_field) . '" ' . $selected . '>' . esc_html($asso->$value_field) . '</option>';
    }
    $tag .= '</select>';
    return $tag;
}

function getHtmlSelectAssociative($name, $array, $defaultValue = null) {
    if (!is_array($array)) {
        $array = array();
    }
    $tag = '<select name="' . $name . '">';
    foreach ($array as $key => $value) {
        if ($value == $defaultValue) {
            $selected = 'selected';
        } else {
            $selected = '';
        }
        $tag .= '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($key) . '</option>';
    }
    $tag .= '</select>';
    return $tag;
}

function getHtmlSelect($name, $array, $defaultValue = null) {
    if (!is_array($array)) {
        $array = array();
    }
    $tag = '<select name="' . $name . '">';
    foreach ($array as $value) {
        if ($value == $defaultValue) {
            $selected = 'selected';
        } else {
            $selected = '';
        }
        $tag .= '<option ' . $selected . '>' . esc_html($value) . '</option>';
    }
    $tag .= '</select>';
    return $tag;
}

function updateShippingOption() {
    $shipping = array();
    $k = 0;
    $i = 0;
    while ($i < count($_POST['shipping'])) {
        $s['price'] = $_POST['shipping'][$i++];
        $s['value1'] = $_POST['shipping'][$i++];
        $s['operator1'] = $_POST['shipping'][$i++];
        $s['operator2'] = $_POST['shipping'][$i++];
        $s['value2'] = $_POST['shipping'][$i++];
        $i++; // separator ;
        if ($s['price']) {
            $shipping[$k++] = $s;
        }
    }

    return update_option(DOMAIN_CF_SHOPPING_CART . '_' . 'shipping', $shipping);
}

function getHtmlShipping() {
    $shipping = opt::get_option('shipping');

    $li_format = "<li><input type=\"text\" name=\"shipping[]\" value=\"%s\" placeholder=\"%s\" />: <input type=\"text\" name=\"shipping[]\" value=\"%s\" placeholder=\"%s\" /> %s %s %s <input type=\"text\" name=\"shipping[]\" value=\"%s\" placeholder=\"%s\" /> <button class=\"remove\">%s</button><input type=\"hidden\" name=\"shipping[]\" value=\";\" /></li>";

    $tag = '<div id="shipping">';
    $tag .= '<ul id="sortable">';
    if (is_array($shipping)) {
        foreach ($shipping as $array) {
            $operator1 = getHtmlSelectAssociative('shipping[]', array('&lt;' => '<', '&lt;=' => '<='), $array['operator1']);
            $operator2 = getHtmlSelectAssociative('shipping[]', array('&lt;' => '<', '&lt;=' => '<='), $array['operator2']);
            $tag .= sprintf($li_format, esc_attr($array['price']), esc_attr__("postage", DOMAIN_CF_SHOPPING_CART), esc_attr($array['value1']), esc_attr__("total price", DOMAIN_CF_SHOPPING_CART), $operator1, esc_html__("Total price", DOMAIN_CF_SHOPPING_CART), $operator2, esc_attr($array['value2']), esc_attr__("total price", DOMAIN_CF_SHOPPING_CART), esc_html__("Remove", DOMAIN_CF_SHOPPING_CART));
        }
    }
    $tag .= '</ul>';
    if (!isset($array) || !is_array($array)) {
        $array = array();
        $array['operator1'] = '';
        $array['operator2'] = '';
    }
    $tag .= '<button class="add_row">' . __('Add row', DOMAIN_CF_SHOPPING_CART) . '</button>';
    $operator1 = getHtmlSelectAssociative('shipping[]', array('&lt;' => '<', '&lt;=' => '<='), $array['operator1']);
    $operator2 = getHtmlSelectAssociative('shipping[]', array('&lt;' => '<', '&lt;=' => '<='), $array['operator2']);
    $li_clone = sprintf($li_format, '', esc_attr__("postage", DOMAIN_CF_SHOPPING_CART), '', esc_attr__("total price", DOMAIN_CF_SHOPPING_CART), $operator1, esc_html__("Total price", DOMAIN_CF_SHOPPING_CART), $operator2, '', esc_attr__("total price", DOMAIN_CF_SHOPPING_CART), esc_html__("Remove", DOMAIN_CF_SHOPPING_CART));
    $tag .= '<div class="li_clone" style="display:none;">' . $li_clone . '</div>';
    $tag .= '</div>';
    return $tag;
}

function custom_field_default_value_to_array() {
    $default = opt::get_option('custom_field_default_value');

    $default = explode("\n", $default);
    foreach ($default as $i => $v) {
        if (!preg_match('/^(.*?)=(.*)$/', $v, $match)) {
            continue;
        }
        $def[$match[1]] = trim($match[2]);
    }
    opt::update_option('custom_field_default_value_array', $def);
}

function custom_field_names_to_array() {
    $fields = opt::get_option('custom_field_names');
    $a = array();
    $f = explode(',', $fields);
    foreach ($f as $key => $value) {
        $s = strip_tags(trim($value));
        if ($s) {
            array_push($a, $s);
        }
    }
    array_push($a, opt::get_option('quantity_text'));
    array_push($a, opt::get_option('subtotal_text'));
    opt::update_option('custom_field_names_array', $a);
}

function get_pnotify_css_ok() {
    $url = CF_SHOPPING_CART_PLUGIN_URL;
    $css = <<<EOD
div.ui-pnotify.cfshoppingcart .ui-pnotify-container {
    background-color: #F2E6D0;
    border-radius: 4px;
    z-index: 9999;
    opacity: 1.0!important;
}
div.ui-pnotify.cfshoppingcart .ui-pnotify-container .ui-pnotify-icon {
    float: left;
    width: 16px;
    height: 16px;
    background-image: url("${url}/lib/pnotify-master/oxygen/16/actions/dialog-ok.png");
}
div.ui-pnotify.cfshoppingcart .ui-pnotify-container .ui-pnotify-title {
    float: left;
    clear: none;
    padding-left: 4px;
}
div.ui-pnotify.cfshoppingcart .ui-pnotify-container .ui-pnotify-text {
    float: left;
    clear: both;
    padding-left: 24px;
}
EOD;
    return $css;
}

function get_pnotify_css_error() {
    $url = CF_SHOPPING_CART_PLUGIN_URL;
    $css = <<<EOD
div.ui-pnotify.cfshoppingcart.error .ui-pnotify-container {
    background-color: #F2E6D0;
    border-radius: 4px;
    z-index: 9999;
    opacity: 1.0!important;
}
div.ui-pnotify.cfshoppingcart.error .ui-pnotify-container .ui-pnotify-icon {
    float: left;
    width: 16px;
    height: 16px;
    background-image: url("${url}/lib/pnotify-master/oxygen/16/status/dialog-warning.png");
}
div.ui-pnotify.cfshoppingcart.error .ui-pnotify-container .ui-pnotify-title {
    float: left;
    clear: none;
    padding-left: 4px;
}
div.ui-pnotify.cfshoppingcart.error .ui-pnotify-container .ui-pnotify-text {
    float: left;
    clear: both;
    padding-left: 24px;
}
EOD;
    return $css;
}

function plugin_options() {
    $opts = new opts();
    $opts->set('closed_shop', 0);
    $opts->set('custom_field_names', __('Product ID,Name,Price,Stock', DOMAIN_CF_SHOPPING_CART));
    $opts->set('field_name_of_link_to_product_page', __('Name', DOMAIN_CF_SHOPPING_CART));
    $opts->set('price_field_name', __('Price', DOMAIN_CF_SHOPPING_CART));
    $opts->set('stock_quantity_field_name', __('Stock', DOMAIN_CF_SHOPPING_CART));
    $opts->set('quantity_field_name', __('Quantity', DOMAIN_CF_SHOPPING_CART));
    $opts->set('show_empty_field', 0);
    $opts->set('custom_field_default_value', __('Product ID', DOMAIN_CF_SHOPPING_CART) . "={post_id}\n" . __('Name', DOMAIN_CF_SHOPPING_CART) . "={post_title}");
    $opts->set('in_the_case_of_sold_out', '');
    $opts->set('sold_out_message', __('Sold Out', DOMAIN_CF_SHOPPING_CART));
    $opts->set('add_to_cart_button_text', __('Add to Cart', DOMAIN_CF_SHOPPING_CART));
    $opts->set('change_quantity_button_text', __('Change Quantity', DOMAIN_CF_SHOPPING_CART));
    $opts->set('quantity_text', __('Quantity', DOMAIN_CF_SHOPPING_CART));
    $opts->set('subtotal_text', __('Subtotal', DOMAIN_CF_SHOPPING_CART));
    $opts->set('shipping_text', __('Shipping', DOMAIN_CF_SHOPPING_CART));
    $opts->set('gross_number_text', __('Gross Number', DOMAIN_CF_SHOPPING_CART));
    $opts->set('product_total_text', __('Product Total', DOMAIN_CF_SHOPPING_CART));
    $opts->set('total_price_text', __('Total Price', DOMAIN_CF_SHOPPING_CART));
    $opts->set('cart_link_text', __('Cart', DOMAIN_CF_SHOPPING_CART));
    $opts->set('check_out_link_text', __('Check Out', DOMAIN_CF_SHOPPING_CART));
    $opts->set('currency_format', __('$%.02f', DOMAIN_CF_SHOPPING_CART));
    $opts->set('post_id_format', '%05d');
    $opts->set('max_quantity_of_a_product', 12);
    $opts->set('max_quantity_of_total', 36);
    $opts->set('cart_screen_post_id', 0);
    $opts->set('check_out_screen_post_id', 0);
    $opts->set('show_product_home', 'checked');
    $opts->set('show_product_page', 'checked');
    $opts->set('show_product_category', 'checked');
    $opts->set('show_product_single', 'checked');
    $opts->set('category_of_product');
    $opts->set('check_out_page_content_if_cart_is_empty', __('Shopping cart is empty.', DOMAIN_CF_SHOPPING_CART));
    $opts->set('disable_ajax', 0);
    $opts->set('email_field_separator', "\t");
    $opts->set('disable_pnotify', 0);
    $opts->set('pnotify_css_ok', get_pnotify_css_ok());
    $opts->set('pnotify_css_error', get_pnotify_css_error());
    //
    $opts->set('message_title', __('Cf Shopping Cart', DOMAIN_CF_SHOPPING_CART));
    $opts->set('message_title_if_failed', __('Confirm', DOMAIN_CF_SHOPPING_CART));
    // message
    //$opts->set("CF_SHOPPING_CART_MSG_TRUE", true);
    //$opts->set("CF_SHOPPING_CART_MSG_FALSE", false);
    $opts->set(CF_SHOPPING_CART_MSG_UNKNOWN_ERROR, __('Unknown error.', DOMAIN_CF_SHOPPING_CART)); // error
    //
    $opts->set(CF_SHOPPING_CART_MSG_ADDED_TO_CART, __('Added to cart.', DOMAIN_CF_SHOPPING_CART));
    $opts->set(CF_SHOPPING_CART_MSG_QUANTITY_ADD_TO_CART_FAILED, __('Add to cart failed.', DOMAIN_CF_SHOPPING_CART)); // error
    //
    $opts->set(CF_SHOPPING_CART_MSG_HAVE_CHANGED_THE_QUANTITY, __('Have changed the quantity.', DOMAIN_CF_SHOPPING_CART));
    //
    $opts->set(CF_SHOPPING_CART_MSG_QUANTITY_WAS_DECREASED, __('Quantity was decreased.', DOMAIN_CF_SHOPPING_CART));
    $opts->set(CF_SHOPPING_CART_MSG_DECREASE_QUANTITY_HAS_FAILED, __('Decrease quantity has failed.', DOMAIN_CF_SHOPPING_CART)); // error
    //
    $opts->set(CF_SHOPPING_CART_MSG_QUANTITIES_WAS_MORE_THAN_ORDER_MAX_QUANTITY_OF_THE_PRODUCT, __('Quantities was more than order max quantity of the product.', DOMAIN_CF_SHOPPING_CART));   // error
    $opts->set(CF_SHOPPING_CART_MSG_QUANTITIES_WAS_MORE_THAN_ORDER_MAX_QUANTITY_OF_TOTAL, __('Quantities was more than order max quantity of total.', DOMAIN_CF_SHOPPING_CART));   // error
    $opts->set(CF_SHOPPING_CART_MSG_ORDER_QUANTITY_WAS_MORE_THAN_NUMBER_OF_STOCK, __('Order quantities was more than stock quantity.', DOMAIN_CF_SHOPPING_CART)); // error
    $opts->set(CF_SHOPPING_CART_MSG_FAILED_IN_CHANGE_STOCK_QUANTITY, __('Failed in change stock quantity.', DOMAIN_CF_SHOPPING_CART));   // error
    $opts->set(CF_SHOPPING_CART_MSG_STOCK_QUANTITY_IS_EMPTY, __('Stock quantity is empty.', DOMAIN_CF_SHOPPING_CART));   // error
    $opts->set(CF_SHOPPING_CART_MSG_UNKNOWN_QUANTITY, __('Unknown quantity.', DOMAIN_CF_SHOPPING_CART));   // error
    //
    $opts->set('has_been_changed_stock_before_checkout', __("Has been changed stock before checkout.\nAgain order please.", DOMAIN_CF_SHOPPING_CART));

    // updae, check nonce
    if (array_key_exists('reset_options', $_POST) && array_key_exists('checkbox_reset_options', $_POST) && check_wp_verify_nonce()) {
        uninstall_function(); // delete all options
        ?>
        <div class="error"><p><strong><?php _e('Has been reset.', DOMAIN_CF_SHOPPING_CART); ?></strong></p></div>
        <?php
    } else if (array_key_exists('submit_hidden', $_POST) && $_POST['submit_hidden'] == 'Y' && check_wp_verify_nonce()) {
        // Update Options!
        $opts->updateOptions();
        updateShippingOption();

        $ship = new shipping();
        $shipping_check_message = $ship->check_setting();
        if ($shipping_check_message) {
            // error
            ?>
            <div class="error"><p><strong><?php _e('Check shipping setting.', DOMAIN_CF_SHOPPING_CART); ?></strong></p></div>
            <?php
        }


        custom_field_names_to_array();
        custom_field_default_value_to_array();


        // Updated message to display
        ?>
        <div class="updated"><p><strong><?php _e('Options saved.', DOMAIN_CF_SHOPPING_CART); ?></strong></p></div>
        <?php
    }


    $field_name_of_link_to_product_page = getHtmlSelect('field_name_of_link_to_product_page', opt::get_option('custom_field_names_array'), $opts->getOption('field_name_of_link_to_product_page'));
    $price_field_name = getHtmlSelect('price_field_name', opt::get_option('custom_field_names_array'), $opts->getOption('price_field_name'));
    $stock_quantity_field_name = getHtmlSelect('stock_quantity_field_name', opt::get_option('custom_field_names_array'), $opts->getOption('stock_quantity_field_name'));
    $quantity_field_name = getHtmlSelect('quantity_field_name', opt::get_option('custom_field_names_array'), $opts->getOption('quantity_field_name'));


    $args = array(
        'type' => 'post',
        'child_of' => 0,
        'parent' => '',
        'orderby' => 'name',
        'order' => 'DESC',
        'hide_empty' => 0,
        'hierarchical' => 1,
        'exclude' => '',
        'include' => '',
        'number' => '',
        'taxonomy' => 'category',
        'pad_counts' => false);
    $categories = get_categories($args);
    $category_of_product = getHtmlSelectArrayAssociative('category_of_product', $categories, 'term_id', 'name', $opts->getOption('category_of_product'));
    //
    $args = array(
        'sort_order' => 'DESC',
        'sort_column' => 'post_title',
        'hierarchical' => 1,
        'exclude' => '',
        'include' => '',
        'meta_key' => '',
        'meta_value' => '',
        'authors' => '',
        'child_of' => 0,
        'parent' => -1,
        'exclude_tree' => '',
        'number' => '',
        'offset' => 0,
        'post_type' => 'page',
        'post_status' => 'publish');
    $pages = get_pages($args);
    //print_r($pages);
    $cart_screen_post_id = getHtmlSelectArrayAssociative('cart_screen_post_id', $pages, 'ID', 'post_title', $opts->getOption('cart_screen_post_id'));
    $check_out_screen_post_id = getHtmlSelectArrayAssociative('check_out_screen_post_id', $pages, 'ID', 'post_title', $opts->getOption('check_out_screen_post_id'));

    /*
    $key = "cfshoppingcart-welcome-panel";
    if (array_key_exists($key, $_COOKIE)) {
        $welcom_panel_hidden = 'hidden';
    }
     */
    $welcom_panel_hidden = 'hidden';
    ?>



    <div class="wrap <?php echo DOMAIN_CF_SHOPPING_CART; ?>_options">
        <h2><?php _e('Cf Shopping Cart Plugin Options', DOMAIN_CF_SHOPPING_CART); ?><div class="support-link"><a class="button" href="https://wordpress.org/plugins/cf-shopping-cart/" target="_blank">WordPress.org</a>&nbsp;<a class="button" href="http://cfshoppingcart.silverpigeon.jp/" target="_blank">Website</a>&nbsp;<form style="float:right;" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top"><input type="hidden" name="cmd" value="_s-xclick"><input type="hidden" name="hosted_button_id" value="LJYZS6NE9H8N8"><button type="submit" class="button" border="0" name="submit">Donate</button></form></div></h2>


        <div id="welcome-panel" class="<?php echo DOMAIN_CF_SHOPPING_CART; ?> welcome-panel <?php echo $welcom_panel_hidden; ?>">
            <a class="welcome-panel-close button" href=""><?php echo esc_html(__('Dismiss', DOMAIN_CF_SHOPPING_CART)); ?></a>

            <div class="welcome-panel-content">
                <div class="welcome-panel-column-container">
                    <div class="welcome-panel-column-one">
                        <h4><?php echo esc_html__('Cf Shopping Cart Needs Your Support', DOMAIN_CF_SHOPPING_CART); ?></h4>
                        <p class="message"><?php echo esc_html__("If you liked this plugin, please make a donation via paypal! Any amount is welcome. Your support is much appreciated.", DOMAIN_CF_SHOPPING_CART); ?></p>
                        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                            <input type="hidden" name="cmd" value="_s-xclick">
                            <input type="hidden" name="hosted_button_id" value="LJYZS6NE9H8N8">
                            <p><button class="button button-primary" type="submit" border="0" name="submit"><?php echo esc_html__('Donate', DOMAIN_CF_SHOPPING_CART); ?></button></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <form name="<?php echo DOMAIN_CF_SHOPPING_CART; ?>_admin" method="post" action="<?php echo esc_url(str_replace('%7E', '~', $_SERVER['REQUEST_URI'])); ?>" enctype="multipart/form-data">
            <?php echo get_wp_nonce_field(); ?>
            <input type="hidden" name="submit_hidden" value="Y">
            <hr />
            <button type="submit" class="button-primary"><?php _e('Update Options', DOMAIN_CF_SHOPPING_CART) ?></button>
            <hr />

            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="headingOne">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                <?php /* _e('General'); */ ?>
                            </a>
                        </h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                        <div class="panel-body" style="width:100%">
                            <?php /* <h3><?php _e('General', DOMAIN_CF_SHOPPING_CART); ?></h3> */ ?>
                            <table class="table table-bordered">
                                <tr><th><?php _e("Closed Shop", DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="checkbox" name="closed_shop" <?php echo $opts->getChecked('closed_shop'); ?>></td></tr>
                                <tr><th><?php _e("Custom field names", DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="custom_field_names" value="<?php esc_attr_e($opts->getOption('custom_field_names')); ?>" size="40"></td></tr>
                                <tr><th class="eg"><?php _e("e.g.", DOMAIN_CF_SHOPPING_CART); ?></th><td>Product ID,Name,Color,Price,Stock</td></tr>
                                <tr><th><?php _e("Field name of link to a product page", DOMAIN_CF_SHOPPING_CART); ?></th><td><?php echo $field_name_of_link_to_product_page; ?></td></tr>
                                <tr><th><?php _e("Price field name", DOMAIN_CF_SHOPPING_CART); ?></th><td><?php echo $price_field_name; ?></td></tr>
                                <tr><th><?php _e("Stock quantity field name", DOMAIN_CF_SHOPPING_CART); ?></th><td><?php echo $stock_quantity_field_name; ?></td></tr>
                                <tr><th><?php _e("Quantity field name", DOMAIN_CF_SHOPPING_CART); ?></th><td><?php echo $quantity_field_name; ?></td></tr>
                                <tr><th><?php _e("Category of Product", DOMAIN_CF_SHOPPING_CART); ?></th><td><?php echo $category_of_product; ?></td></tr>
                                <tr><th><?php _e("Show empty field", DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="checkbox" name="show_empty_field" <?php echo $opts->getChecked('show_empty_field'); ?>></td></tr>
                                <tr><th><?php _e("Custom Field default value", DOMAIN_CF_SHOPPING_CART); ?></th><td><textarea name="custom_field_default_value" cols="40" rows="5"><?php echo esc_textarea($opts->getOption('custom_field_default_value')); ?></textarea></td></tr>
                                <tr><th class="eg"><?php _e("e.g.", DOMAIN_CF_SHOPPING_CART); ?></th><td>FieldName1=value1<br />FieldName2=value2<br />...</td></tr>
                                <tr><th><?php _e("In the case of sold out", DOMAIN_CF_SHOPPING_CART); ?></th><td><?php echo getHtmlSelectAssociative('in_the_case_of_sold_out', array(__('Sold Out message on Price', DOMAIN_CF_SHOPPING_CART) => 'sold_out_message_on_price', __('Sold Out message on Stock', DOMAIN_CF_SHOPPING_CART) => 'sold_out_message_on_stock', __('Not to be Product', DOMAIN_CF_SHOPPING_CART) => 'not_to_be_product'), $opts->getOption('in_the_case_of_sold_out')); ?></td></tr>
                                <tr><th><?php _e("Sold Out message", DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="sold_out_message" value="<?php esc_attr_e($opts->getOption('sold_out_message')); ?>" size="20"></td></tr>
                                <tr><th><?php _e("Add to Cart button text", DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="add_to_cart_button_text" value="<?php esc_attr_e($opts->getOption('add_to_cart_button_text')); ?>" size="20"></td></tr>
                                <tr><th><?php _e("Change Quantity button text", DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="change_quantity_button_text" value="<?php esc_attr_e($opts->getOption('change_quantity_button_text')); ?>" size="20"></td></tr>
                                <tr><th><?php _e("Quantity text", DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="quantity_text" value="<?php esc_attr_e($opts->getOption('quantity_text')); ?>" size="20"></td></tr>
                                <tr><th><?php _e("Subtotal text", DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="subtotal_text" value="<?php esc_attr_e($opts->getOption('subtotal_text')); ?>" size="20"></td></tr>
                                <tr><th><?php _e("Shipping text", DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="shipping_text" value="<?php esc_attr_e($opts->getOption('shipping_text')); ?>" size="20"></td></tr>
                                <tr><th><?php _e("Gross Number text", DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="gross_number_text" value="<?php esc_attr_e($opts->getOption('gross_number_text')); ?>" size="20"></td></tr>
                                <tr><th><?php _e("Product Total text", DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="product_total_text" value="<?php esc_attr_e($opts->getOption('product_total_text')); ?>" size="20"></td></tr>
                                <tr><th><?php _e("Total Price text", DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="total_price_text" value="<?php esc_attr_e($opts->getOption('total_price_text')); ?>" size="20"></td></tr>
                                <tr><th><?php _e("Cart link text", DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="cart_link_text" value="<?php esc_attr_e($opts->getOption('cart_link_text')); ?>" size="20"></td></tr>
                                <tr><th><?php _e("Check Out link text", DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="check_out_link_text" value="<?php esc_attr_e($opts->getOption('check_out_link_text')); ?>" size="20"></td></tr>
                                <tr><th><?php _e("Currency format", DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="currency_format" value="<?php esc_attr_e($opts->getOption('currency_format')); ?>" size="20"></td></tr>
                                <tr><th><?php _e("Post ID format", DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="post_id_format" value="<?php esc_attr_e($opts->getOption('post_id_format')); ?>" size="20"></td></tr>
                                <tr><th><?php _e("Max quantity of a product", DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="max_quantity_of_a_product" value="<?php esc_attr_e($opts->getOption('max_quantity_of_a_product')); ?>" size="20"></td></tr>
                                <tr><th><?php _e("Max quantity of total", DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="max_quantity_of_total" value="<?php echo esc_html($opts->getOption('max_quantity_of_total')); ?>" size="20"></td></tr>
                                <tr><th><?php _e("Cart page", DOMAIN_CF_SHOPPING_CART); ?></th><td><?php echo $cart_screen_post_id; ?></td></tr>
                                <tr><th><?php _e("Check Out page", DOMAIN_CF_SHOPPING_CART); ?></th><td><?php echo $check_out_screen_post_id; ?></td></tr>
                                <tr><th><?php _e("Post to be product when this type", DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="checkbox" name="show_product_home" <?php echo $opts->getChecked('show_product_home'); ?>>Home <input type="checkbox" name="show_product_page" <?php echo $opts->getChecked('show_product_page'); ?>>Page <input type="checkbox" name="show_product_category" <?php echo $opts->getChecked('show_product_category'); ?>>Category <input type="checkbox" name="show_product_single" <?php echo $opts->getChecked('show_product_single'); ?>> Single</td></tr>
                                <tr><th><?php _e("Disable Ajax", DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="checkbox" name="disable_ajax" <?php echo $opts->getChecked('disable_ajax'); ?>></td></tr>
                                <tr><th><?php _e("E-mail field separator", DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="radio" name="email_field_separator" id="email_field_separator_tab" value="tab" <?php if ($opts->getOption('email_field_separator') == "tab") {
                                    echo 'checked';
                                } ?> /><label for="email_field_separator_tab"><?php esc_html_e('Tab', DOMAIN_CF_SHOPPING_CART); ?></label> <input type="radio" name="email_field_separator" id="email_field_separator_comma" value="comma" <?php if ($opts->getOption('email_field_separator') == "comma") {
                                    echo 'checked';
                                } ?> /><label for="email_field_separator_comma"><?php esc_html_e('Comma', DOMAIN_CF_SHOPPING_CART); ?></label></td></tr>
                            </table>
                            <hr />
                            <button type="submit" class="button-primary"><?php _e('Update Options', DOMAIN_CF_SHOPPING_CART) ?></button>
                            <hr />
                            <h3><?php _e('Check Out page content if cart is empty', DOMAIN_CF_SHOPPING_CART); ?></h3>
                            <?php wp_editor(stripslashes($opts->getOption('check_out_page_content_if_cart_is_empty')), 'check_out_page_content_if_cart_is_empty', 'check_out_page_content_if_cart_is_empty', true); ?>
                            <hr />
                            <h3><?php _e('Shipping', DOMAIN_CF_SHOPPING_CART); ?></h3>
                            <?php echo getHtmlShipping(); ?>
                            <hr />
                            <h3><?php _e('Shortcodes', DOMAIN_CF_SHOPPING_CART); ?></h3>
                            <label><?php _e('Copy this code and paste it into your cart page content.', DOMAIN_CF_SHOPPING_CART); ?></label>
                            <br /><textarea name="shortcode_at_cart_page" cols="70" rows="3">[<?php echo DOMAIN_CF_SHOPPING_CART; ?>_cart]<?php _e('Shopping cart is empty.', DOMAIN_CF_SHOPPING_CART); ?>[/<?php echo DOMAIN_CF_SHOPPING_CART; ?>_cart]</textarea>
                            <hr />
                            <label><?php _e('Copy this code and paste it into your text widget content.', DOMAIN_CF_SHOPPING_CART); ?></label>
                            <br /><textarea name="shortcode_at_widget" cols="70" rows="3">[<?php echo DOMAIN_CF_SHOPPING_CART; ?>_widget]<?php _e('Shopping cart is empty.', DOMAIN_CF_SHOPPING_CART); ?>[/<?php echo DOMAIN_CF_SHOPPING_CART; ?>_widget]</textarea>
                            <hr />
                            <label><?php _e('Copy this code and paste it into your check out page content.', DOMAIN_CF_SHOPPING_CART); ?></label>
                            <br /><textarea name="shortcode_at_check_out" cols="70" rows="3">[<?php echo DOMAIN_CF_SHOPPING_CART; ?>_check_out]</textarea>
                            <hr />
                            <h3><?php _e('PNotify', DOMAIN_CF_SHOPPING_CART); ?></h3>
                            <input id="disable_pnotify" type="checkbox" name="disable_pnotify" <?php echo $opts->getChecked('disable_pnotify'); ?>> <label for="disable_pnotify"><?php _e("Disable PNotify", DOMAIN_CF_SHOPPING_CART); ?></label>
                            <hr />
                            <h4><?php _e('PNotify CSS', DOMAIN_CF_SHOPPING_CART); ?></h4>
                            <br /><textarea name="pnotify_css_ok" cols="70" rows="10"><?php echo esc_textarea(stripslashes($opts->getOption('pnotify_css_ok'))); ?></textarea>
                            <hr />
                            <h4><?php _e('PNotify CSS Error', DOMAIN_CF_SHOPPING_CART); ?></h4>
                            <br /><textarea name="pnotify_css_error" cols="70" rows="10"><?php echo esc_textarea(stripslashes($opts->getOption('pnotify_css_error'))); ?></textarea>
                            <hr />
                            <h3><?php _e('Popup messages', DOMAIN_CF_SHOPPING_CART); ?></h3>
                            <table class="table table-bordered">
                                <tr><th><?php _e("Title", DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="message_title" value="<?php esc_attr_e($opts->getOption('message_title')); ?>" size="40"></td></tr>
                                <tr><th><?php _e("Title if failed", DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="message_title_if_failed" value="<?php esc_attr_e($opts->getOption('message_title_if_failed')); ?>" size="40"></td></tr>
                                <?php $msg = CF_SHOPPING_CART_MSG_UNKNOWN_ERROR; ?>
                                <tr><th><?php _e('Unknown error.', DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="<?php echo $msg; ?>" value="<?php _e($opts->getOption($msg), DOMAIN_CF_SHOPPING_CART); ?>" size="40"></td></tr>
                                <?php $msg = CF_SHOPPING_CART_MSG_ADDED_TO_CART; ?>
                                <tr><th><?php _e('Added to cart.', DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="<?php echo $msg; ?>" value="<?php _e($opts->getOption($msg), DOMAIN_CF_SHOPPING_CART); ?>" size="40"></td></tr>
                                <?php $msg = CF_SHOPPING_CART_MSG_QUANTITY_ADD_TO_CART_FAILED; ?>
                                <tr><th><?php _e('Add to cart failed.', DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="<?php echo $msg; ?>" value="<?php _e($opts->getOption($msg), DOMAIN_CF_SHOPPING_CART); ?>" size="40"></td></tr>
                                <?php $msg = CF_SHOPPING_CART_MSG_HAVE_CHANGED_THE_QUANTITY; ?>
                                <tr><th><?php _e('Have changed the quantity.', DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="<?php echo $msg; ?>" value="<?php _e($opts->getOption($msg), DOMAIN_CF_SHOPPING_CART); ?>" size="40"></td></tr>
                                <?php $msg = CF_SHOPPING_CART_MSG_QUANTITY_WAS_DECREASED; ?>
                                <tr><th><?php _e('Quantity was decreased.', DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="<?php echo $msg; ?>" value="<?php _e($opts->getOption($msg), DOMAIN_CF_SHOPPING_CART); ?>" size="40"></td></tr>
                                <?php $msg = CF_SHOPPING_CART_MSG_DECREASE_QUANTITY_HAS_FAILED; ?>
                                <tr><th><?php _e('Decrease quantity has failed.', DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="<?php echo $msg; ?>" value="<?php _e($opts->getOption($msg), DOMAIN_CF_SHOPPING_CART); ?>" size="40"></td></tr>
                                <?php $msg = CF_SHOPPING_CART_MSG_QUANTITIES_WAS_MORE_THAN_ORDER_MAX_QUANTITY_OF_THE_PRODUCT; ?>
                                <tr><th><?php _e('Quantities was more than order max quantity of the product.', DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="<?php echo $msg; ?>" value="<?php _e($opts->getOption($msg), DOMAIN_CF_SHOPPING_CART); ?>" size="40"></td></tr>
                                <?php $msg = CF_SHOPPING_CART_MSG_QUANTITIES_WAS_MORE_THAN_ORDER_MAX_QUANTITY_OF_TOTAL; ?>
                                <tr><th><?php _e('Quantities was more than order max quantity of total.', DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="<?php echo $msg; ?>" value="<?php _e($opts->getOption($msg), DOMAIN_CF_SHOPPING_CART); ?>" size="40"></td></tr>
                                <?php $msg = CF_SHOPPING_CART_MSG_ORDER_QUANTITY_WAS_MORE_THAN_NUMBER_OF_STOCK; ?>
                                <tr><th><?php _e('Order quantities was more than stock quantity.', DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="<?php echo $msg; ?>" value="<?php _e($opts->getOption($msg), DOMAIN_CF_SHOPPING_CART); ?>" size="40"></td></tr>
                                <?php $msg = CF_SHOPPING_CART_MSG_FAILED_IN_CHANGE_STOCK_QUANTITY; ?>
                                <tr><th><?php _e('Failed in change stock quantity.', DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="<?php echo $msg; ?>" value="<?php _e($opts->getOption($msg), DOMAIN_CF_SHOPPING_CART); ?>" size="40"></td></tr>
                                <?php $msg = CF_SHOPPING_CART_MSG_STOCK_QUANTITY_IS_EMPTY; ?>
                                <tr><th><?php _e('Stock quantity is empty.', DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="<?php echo $msg; ?>" value="<?php _e($opts->getOption($msg), DOMAIN_CF_SHOPPING_CART); ?>" size="40"></td></tr>
                                <?php $msg = CF_SHOPPING_CART_MSG_UNKNOWN_QUANTITY; ?>
                                <tr><th><?php _e('Unknown quantity.', DOMAIN_CF_SHOPPING_CART); ?></th><td><input type="text" name="<?php echo $msg; ?>" value="<?php _e($opts->getOption($msg), DOMAIN_CF_SHOPPING_CART); ?>" size="40"></td></tr>
                            </table>
                            <hr />
                            <h3><?php _e('E-mail messages', DOMAIN_CF_SHOPPING_CART); ?></h3>
                            <label><?php _e('In case of has been changed stock before checkout', DOMAIN_CF_SHOPPING_CART); ?>: </label>
                            <br /><textarea name="has_been_changed_stock_before_checkout" cols="70" rows="3"><?php echo esc_textarea($opts->getOption('has_been_changed_stock_before_checkout')); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <hr />
            <input type="checkbox" name="checkbox_reset_options" value="reset_options" id="reset_options"><label for="reset_options"><?php _e('Reset Options', DOMAIN_CF_SHOPPING_CART) ?></label> <button type="submit" name="reset_options" value="reset_options" class="button-secondary"><?php _e('Reset Options', DOMAIN_CF_SHOPPING_CART) ?></button> <button type="submit" class="button-primary"><?php _e('Update Options', DOMAIN_CF_SHOPPING_CART) ?></button>
            <hr />
        </form>
    </div>
<?php
}
