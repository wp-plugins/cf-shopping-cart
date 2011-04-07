=== Cf Shopping Cart ===
Contributors: AI.Takeuchi
Tags: shopping, content, widget, plugin, custom field, Exec-PHP, Contact Form 7, Custom Field Template, QF-GetThumb
Requires at least: 2.6
Tested up to: 3.1
Stable tag: 0.6.0

Cf Shopping Cart is simple shopping cart plugin.
This plugin work together such as Custom Field and more plugins.
Thereby website can have flexible design.


== Description ==

Cf Shopping Cart is simple shopping cart plugin.
This plugin work together such as Custom Field and more plugins.
Thereby website can have flexible design.


= Translators =

* Russian (ru_RU) - [Evgeny Vakhteev](http://www.sdelanomnoy.ru/)
* Dutch (nl_NL) - [Rene](http://www.cesmehotels.com)
* Spanish (es_ES) - [Jorge Guerrero, Miguel Olivares, Estefan僘 Mu?z](http://www.tehacesver.com/)
* Chinese (zh_TW) - [Tsao Peter](mailto:tsaopeter@yahoo.com.tw)
* German (de_DE) - Carola Fichtner
* Japanese (ja) - [AI.Takeuchi](http://takeai.silverpigeon.jp/)

If you have created your own language pack, or have an update of an existing one, you can send [gettext PO and MO files](http://codex.wordpress.org/Translating_WordPress) to [me](http://takeai.silverpigeon.jp/) so that I can bundle it into Cf Shopping Cart. You can [download the latest POT file from here](http://plugins.svn.wordpress.org/cf-shopping-cart/trunk/lang/cfshoppingcart.pot).


== Installation ==

1. Install plugins and activate. Cf Shopping Cart, Contact Form 7, Custom Field Template, Exec-PHP(optional) and QF-GetThumb(optional).
2. Be place Cf Shopping Cart widget to sidebar.
3. Visit settings Contact Form 7, Add new contact form.
   Add code into the Form: '[cfshoppingcart* cartdata class:cfshoppingcart7]'.
   Add code into the Message body: '[cartdata]'.
   Add code into Additional Settings: 'on_sent_ok: "cfshoppingcart_empty_cart();"'.
4. Add new page (example 'Check out'), add code into the article: '[contact-form ? "???"]'. remember this page url.
5. Add new page (example 'Shopping Cart') and put in '[cfshoppingcart_cart 1]' to article. remember this page url.
6. Setting Custom Field Template, add new template. Field name example: 'Product ID', 'Name' and 'Price'... remember field names.
7. Settings Cf Shopping Cart.
(If choice manually at 7th step then edit theme file (archive.php, single.php and more). Insert php code '<?php cfshoppingcart(); ?>' into the loop of output-article.)
8. Make product pages. Add new page or post, input Custom Field, write article and set category.
9. Repeat 8th steps.

[For basic Installation, you can also have a look at the plugin homepage.](http://cfshoppingcart.silverpigeon.jp/?page_id=13)

If you be running WordPress on Windows, must be rewrite php.ini file, if necessary.
Thereby, Php don't say error message very well.
Reference: http://php.net/manual/en/function.error-reporting.php
php.ini: 
-- before --
error_reporting = E_ALL | E_STRICT
-- after --
;error_reporting = E_ALL | E_STRICT
error_reporting  =  E_ALL & ~E_NOTICE & ~E_DEPRECATED
------------

== Changelog ==

= 0.6.0 =
* Attention, this version has many changes. previous version is 0.3.6.
* Bug fix: css load path failed in admin screen.

= 0.5.9 =
* Call wpcf7_add_shortcode function in myself.

= 0.5.8 =
* Bug fix: number of products in cart not clear after check out.

= 0.5.7 =
* Bug fix: shop closed message for widget.

= 0.5.6 =
* Added setting: Don't load css.

= 0.5.5 =
* Be put such 'empty cart' and other messages on check out screen.
* Move output script to footer.
* Added do_action_ref_array 'before_clear_cart'.
* Bug fix: process of after about check stock.

= 0.5.4 =
* Hook to 'wpcf7_mail_sent' of Contact Form 7.
* Remove unnecessary php class object.
* Support to be working when no ajax.
* Added shortcode.
* Remove no use css color setting.
* Bug fix to update Stock Custom Field value function.

= 0.4.5 =
* Bug fix: check out of stock.

= 0.4.3 =
* Added to Custom Field keyword '#postid'. This keyword be replaced to formatted Post ID Number.
* Added to Custom Field keyword '#hidden'. This keyword use to hidden to the Custom Field.
* Added option chooser of product and supported extra charges.
* Fix internationalization message.
* Removed old shipping function.
* Others

= 0.3.7 =
* Changed way to ajax communication.

= 0.3.6 =
* Change coding:
   $value = & new Class(); /* before */
   $value =   new Class(); /* after */
  Will be able to run on more computers, without modification.

= 0.3.5 =
* Support to path separator for Windows.
* Stop the use of split function, use to explode function.

= 0.3.4 =
* Bug fix: Not display 'empty cart' in Cart Widget after check out.

= 0.3.3 =
* Translation for Russian has been newly created by Evgeny Vakhteev.

= 0.3.2 =
* Attention, this version has many changes. previous version is 0.2.13.

= 0.3.0 beta5 =
* Bug fix.

= 0.3.0 beta4 =
* Bug fix.
* Fix table tag and image tag.
* Can select table tag from table or dl.
* others

= 0.3.0 beta3 =
* Added some messages in setting screen.
* Using jQuery Form Plugin and jQuery Pines Notify (pnotify) Plugin.
* Fix: way to loading jQuery.
* others

= 0.3.0 beta2 =
* Bug fix: about 'Shop now closed option'.
* Put message if server don't have symlink function.

= 0.3.0 beta1 =
* Changed way to setting module for Contact Form 7.
* Changed way to setting of shipping.
* Added shop open/closed status option.
* Added number of stock manage.

= 0.2.13 =
* Added setting option: can setting Thanks page url, move this url after send order.

= 0.2.12 =
* Added setting options, can setting the text of 'Go To Cart' and 'Orderer Input screen'.

= 0.2.11 =
* Changed way to session start.
* Automatic create symbolic link cfshoppingcart.php for Contact Form 7 module, and add setting option for them. Accordingly removed installation step 3rd.

= 0.2.10 =
* Translation for Dutch has been newly created by Rene.

= 0.2.9 =
* Translation for Spanish has been newly created by Jorge Guerrero, Miguel Olivares, Estefan僘 Mu?z.

= 0.2.8 =
* Update Chinese language pack by Tsao Peter.

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


== Screenshots ==

1. screenshot-1.png
2. screenshot-2.png


== More plugins. Thank you! ==

Name: Custom Field Template
URL: http://wordpress.org/extend/plugins/custom-field-template/

Name: Contact Form 7
URL: http://wordpress.org/extend/plugins/contact-form-7/

Name: Exec-PHP
URL: http://wordpress.org/extend/plugins/exec-php/

Name: QF-GetThumb
URL: http://wordpress.org/extend/plugins/qf-getthumb/


== Others ==

#I can not speak english very well.
#I would like you to tell me mistake my English, code and others.
#thanks.
Cf Shopping Cart Website: http://cfshoppingcart.silverpigeon.jp/
Blog: http://takeai.silverpigeon.jp/
AI.Takeuchi <takeai@silverpigeon.jp>


