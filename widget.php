<?php
/*
Plugin Name: Cf Shopping Cart widget
Plugin URI: http://takeai.silverpigeon.jp/
Description: Placement simply shopping cart to content.
Author: AI.Takeuchi
Version: 0.6.19
Author URI: http://takeai.silverpigeon.jp/
*/

// -*- Encoding: utf8n -*-
// If you notice a my mistake(Program, English...), Please tell me.

/*  Copyright 2009-2011 AI Takeuchi (email: takeai@silverpigeon.jp)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


/**
 * CfshoppingcartWidget Class
 */

class CfshoppingcartWidget extends WP_Widget {
    var $widget_title;

    // widget actual processes
    /** constructor */
    function CfshoppingcartWidget() {
        $this->widget_title = __('Cf Shopping Cart', 'cfshoppingcart');
        parent::WP_Widget(false, $this->widget_title);
    }

    // outputs the content of the widget
    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
        echo $before_widget;
        if (!$title) $title = $this->widget_title;
        echo $before_title . $title . $after_title;
        echo '<div class="cfshoppingcart_widget">';
        echo '<div class="cfshoppingcart_widget_cart">';
        //
        if (!$html = $_SESSION['cfshoppingcart']['sum']['html']) {
            global $WpCFShoppingcart;
            $model = $WpCFShoppingcart->model;
            $html = $model->getWidgetEmpyCartHtml();
            //$html = '<span class="cart_empty">'. __('Shopping Cart is empty','cfshoppingcart') . '</span>';
        }
        echo $html;
        echo '</div>';
        echo '<div class="cfshoppingcart_widget_note">' . nl2br($instance['note']) . '</div>';
        echo '</div>';
        echo $after_widget;
    }

    // processes widget options to be saved
    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
        return $new_instance;
    }

    // outputs the options form on admin
    /** @see WP_Widget::form */
    function form($instance) {
        $title = esc_attr($instance['title']);
        $note = esc_attr($instance['note']);
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>

        <p><label for="<?php echo $this->get_field_id('note'); ?>"><?php _e('Note:'); ?><br /><textarea class="widefat" id="<?php echo $this->get_field_id('note'); ?>" name="<?php echo $this->get_field_name('note'); ?>"><?php echo $note; ?></textarea></label></p>
        <?php 
    }

} // class CfshoppingcartWidget


// register CfshoppingcartWidget widget
add_action('widgets_init', create_function('', 'return register_widget("CfshoppingcartWidget");'));

?>
