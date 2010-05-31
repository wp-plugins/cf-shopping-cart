=== Cf Shopping Cart ===
Contributors: AI.Takeuchi
Donate link: http://takeai.silverpigeon.jp/
Tags: shopping, content, widget, plugin, custom field, wordpress, Exec-PHP, Contact Form 7, Custom Field Template, QF-GetThumb
Requires at least: 2.6
Tested up to: 2.9.2
Stable tag: 0.2.7

Cf Shopping Cart is simple shopping cart plugin.
This plugin work together such as Custom Field and more plugins.
Thereby website can have flexible design.


== Description ==

Cf Shopping Cart is simple shopping cart plugin.
This plugin work together such as Custom Field and more plugins.
Thereby website can have flexible design.


= Translators =

* Chinese (zh_TW) - [Tsao Peter](mailto:tsaopeter@yahoo.com.tw)
* German (de_DE) - Carola Fichtner
* Japanese (ja) - [AI.Takeuchi](http://takeai.silverpigeon.jp/)

If you have created your own language pack, or have an update of an existing one, you can send [gettext PO and MO files](http://codex.wordpress.org/Translating_WordPress) to [me](http://takeai.silverpigeon.jp/) so that I can bundle it into Cf Shopping Cart. You can [download the latest POT file from here](http://plugins.svn.wordpress.org/cf-shopping-cart/trunk/lang/cfshoppingcart.pot).


== Installation ==

1. Install more plugins and activate. Exec-PHP, Contact Form 7, Custom Field Template and QF-GetThumb(optional).
2. Upload the entire 'cf-shopping-cart' folder to the '/wp-content/plugins/' directory.
3. Install extended module for Contact Form 7 plugin. Copy 'cfshoppingcart.php' file from '/wp-content/plugins/cf-shopping-cart/contact-form-7-module/' to '/wp-content/plugins/contact-form-7/module/'.
4. Activate Cf Shopping Cart 2 plugins. If use Shipping then copy 'shipping.php' file from '/wp-content/plugins/cf-shopping-cart/extention/' to '/wp-content/cfshoppingcart/', and edit it file.
5. Be place Cf Shopping Cart widget to sidebar.
6. Visit settings Contact Form 7, Add new contact form.
   Add code into the Form: '[cfshoppingcart* cartdata class:cfshoppingcart7]'.
   Add code into the Message body: '[cartdata]'.
   Add code into Additional Settings: 'on_sent_ok: "cfshoppingcart_empty_cart();"'.
   Add new page (example 'Send order'), add code into the article: '[contact-form ? "???"]'. remember this page url.
7. Add new page (example 'Shopping Cart') and put in '[cfshoppingcart_cart 1]' to article. remember this page url.
8. Add new category example 'commodity'.
9. Setting Custom Field Template, add new template. Field name example: 'Product ID', 'Name' and 'Price'... remember field names.
10. Settings Cf Shopping Cart.
11. If choice manually at 10th step then edit theme file (archive.php, single.php and more). Insert php code '<?php cfshoppingcart(); ?>' into the loop of output-article.
12. Make commodity pages. Add new page/post, input Custom Field, write article and set category.
13. Repeat 12th step.


== Changelog ==

= 0.2.7 =
* Update Chinese language pack by Tsao Peter.

= 0.2.6 =
* Bug fixed.

= 0.2.5 =
* Translation for Chinese has been newly created by Tsao Peter.

= 0.2.4 =
* Added error message.

= 0.2.3 =
* Bug fixed. Don't work QF-GetThumb plugin on Cf Shopping Cart

= 0.2.2 =
* Translation for German has been newly created by Carola Fichtner.

= 0.2.1 =
* Additions: submit order to empty the cart. (Installation 6th step changed.)

= 0.2.0 =
* Include language file (pot file).
* Bug fixed.

= 0.1.7 =
* Bug fixed.
* Shipping configuration file location change. Measure against automatic upgrade.
* Add any tag and css class.

= 0.1.6 =
* bug fixed.


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


