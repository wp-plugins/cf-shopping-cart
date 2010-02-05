=== Cf Shopping Cart ===
Contributors: AI.Takeuchi
Donate link: http://takeai.silverpigeon.jp/
Tags: shopping, content, widget, plugin, custom field, wordpress, Exec-PHP, Contact Form 7, Custom Field Template, QF-GetThumb
Requires at least: 2.6
Tested up to: 2.9.1
Stable tag: 0.1.3

Cf Shopping Cart is simple shopping cart plugin, useing Custom Field.

Demonstration site is here!!
http://takeai.silverpigeon.jp/donate


== Description ==

Cf Shopping Cart is simple shopping cart plugin, useing Custom Field.

== Installation ==

1. Install more plugins and activate. Exec-PHP, Contact Form 7, Custom Field Template and QF-GetThumb(optional).
2. Upload the entire cfshoppingcart folder to the `/wp-content/plugins/` directory.
3. Install extended module for Contact Form 7 plugin. Copy 'cfshoppingcart.php' file from '/wp-content/plugins/cfshoppingcart/contact-form-7-module/' to '/wp-content/plugins/contact-form-7/module/'.
4. Activate the Cf Shopping Cart 2 plugins. If use functionally of Shipping then edit '/wp-content/plugins/cfshoppingcart/extention/shipping.php' file.
5. Be place Cf Shopping Cart widget to sidebar.
6. Add new Contact Form 7's contact-form, put in '[cfshoppingcart* cartdata class:cfshoppingcart7]' to the form and '[cartdata]' to the email text. and put in short-code to add new page example 'Send order'. remember it url.
7. Add new page example 'Shopping Cart' and put in '<?php cfshoppingcart_cart(); ?>' to content (HTML mode). remember it url.
8. Add new category example 'commodity'.
9. Setting Custom Field Template, add new template. Field name example: 'commodity_name' and 'price'... remember filed names.
10. Add php code '<?php cfshoppingcart(get_post_custom()); ?>' on top of line '<?php comments_template(); // Get wp-comments.php template ?>' in archive.php and single.php.
11. Settings Cf Shopping Cart.
12. Make commodity page. Add new post, input Custom Field, write content and set category.


== More plugins. Thank you! ==

Name: Exec-PHP
URL: http://wordpress.org/extend/plugins/exec-php/

Name: Contact Form 7
URL: http://wordpress.org/extend/plugins/contact-form-7/

Name: Custom Field Template
URL: http://wordpress.org/extend/plugins/custom-field-template/

Name: QF-GetThumb
URL: http://wordpress.org/extend/plugins/qf-getthumb/


== Others ==

# If you notice my mistakes(Program, English...), Please tell me.
Web site: http://takeai.silverpigeon.jp/
AI.Takeuchi <takeai@silverpigeon.jp>


