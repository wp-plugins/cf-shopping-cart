=== Cf Shopping Cart ===
Contributors: AI.Takeuchi
Tags: plugin, shopping cart, simple
Requires at least: 4.1
Tested up to: 4.2.4
Stable tag: 2.0.1
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

BE CAREFUL: This version 2.0 is NOT COMPATIBLE with 0.8. 
Cf Shopping Cart is simple shopping cart plugin for WordPress.
This plugin be working with Custom Field and MW WP Form plugins.
Thereby website can have flexible design.
Get previous version:
  https://downloads.wordpress.org/plugin/cf-shopping-cart.0.8.16.zip

== Description ==

BE CAREFUL: This version 2.0 is NOT COMPATIBLE with 0.8. 
Cf Shopping Cart is simple shopping cart plugin for WordPress.
This plugin be working with Custom Field and MW WP Form plugins.
Thereby website can have flexible design.
Get previous version:
  https://downloads.wordpress.org/plugin/cf-shopping-cart.0.8.16.zip

= Website =

http://cfshoppingcart.silverpigeon.jp/

= Translators (translate previous version) =

* Russian (ru_RU) - [kg69design](http://kg69design.com)
* Russian (ru_RU) - [Evgeny Vakhteev](http://www.sdelanomnoy.ru/)
* Dutch (nl_NL) - [G. J. van den Os](http://www.arrowhosting.nl)
* Dutch (nl_NL) - [Rene](http://www.cesmehotels.com)
* Spanish (es_ES) - [Jorge Guerrero, Miguel Olivares, Estefan僘 Mu?z](http://www.tehacesver.com/)
* Chinese (zh_TW) - [Tsao Peter](mailto:tsaopeter@yahoo.com.tw)
* German (de_DE) - Carola Fichtner

If you have created your own language pack, or have an update of an existing one, you can send to me so that I can bundle it into Cf Shopping Cart.


== Installation ==

1. Install plugins and activate

* Cf Shopping Cart
* Smart Custom Fields or Custom Field Template
* MW WP Form

2. Add post category for products

3. Setting MW WP Form
e.g.
------------------------
Name:
[mwform_text name="your-name" size="40"]
E-mail:
[mwform_text name="email" size="40"]
Phone:
[mwform_text name="tel" size="40"]
Address:
[mwform_text name="address"]
Note:
[mwform_textarea name="note"]
Order:
[cfshoppingcart_check_out]
[mwform_backButton value="Return"] [mwform_submitButton name="mwform_submitButton-422" confirm_value="Confirm" submit_value="Send"]
------------------------
Name: {your-name}
E-mail: {email}
Phone: {tel}
Address: {address}
Note:
{note}
Order:
{cfshoppingcart_check_out}
Order(admin):
{cfshoppingcart_email_admin}
Order(customer):
{cfshoppingcart_email_customer}
------------------------

4. Add Check out page
[mwform_formkey key="xxx"]

5. Add Complete Page
[mwform_formkey key="xxx"]
[cfshoppingcart_result]
Complete!
[else]
Has been changed stock before checkout.
Again order please.
[/cfshoppingcart_result]

6. Setting MW WP Form
Input Complete Page URL.
http://plugins.silverpigeon.jp/complete_page/

7. Add Cart page
[cfshoppingcart_cart]Shopping Cart is empty.[/cfshoppingcart_cart]

8. Appearance Widget
[cfshoppingcart_widget]Shopping Cart is empty.[/cfshoppingcart_widget]
[cfshoppingcart_cart_link text="Go to Cart"]
[cfshoppingcart_message]
[cfshoppingcart_reset_cart_link]

9. Setting Cf Shopping Cart

10. Setting Smart Custom Fields or Custom Field Template

e.g.
Custom Field Template:
------------------------
[Product ID]
type = text
size = 35

[Name]
type = text
size = 35

[Price]
type = text
size = 35

[Stock]
type = text
size = 35

[Color]
type = textarea
rows = 4
cols = 40
------------------------

== Settings ==

= Shortcode =

* [cfshoppingcart_reset_cart_link]
This shortcode output reset cart link.

- Options:
-- text    : link text
-- url_only: true/false

* [cfshoppingcart_message]
This shortcode output message.

- Options:
-- not_output_if_no_message: true/false
-- if_ajax_disabled        : true/false

* [cfshoppingcart_result]
Insert this shortcode and MW WP Form shortcode to Complete Page.

e.g.
[mwform_formkey key="xxx"]
[cfshoppingcart_result]
Complete!
[else]
Has been changed stock before checkout.
Again order please.
[/cfshoppingcart_result]

* [cfshoppingcart_cart]
Insert this shortcode to Cart Page.

- Options:
-- class         : class name
-- product_title : <caption>Caption</caption>
-- product_fileds: Product ID,Name,Color,Price,Stock,Quantity
-- total_title   : <caption>Caption</caption>
-- total_fileds  : Gross Number,Total Price
-- type          : table/dl

e.g.
[cfshoppingcart_cart]Shopping Cart is empty.[/cfshoppingcart_cart]

* [cfshoppingcart_widget]
Insert this shortcode to Text Widget.

- Options:
-- see cfshoppingcart_cart shortcode.

e.g.
[cfshoppingcart_widget]Shopping Cart is empty.[/cfshoppingcart_widget]

* [cfshoppingcart_check_out]
Insert this shortcode and MW WP Form shortcode to MW WP Form Content.

- Options:
-- see cfshoppingcart_cart shortcode.

e.g.
Name:
[mwform_text name="your-name" size="40"]
E-mail:
[mwform_text name="email" size="40"]
Phone:
[mwform_text name="tel" size="40"]
Address:
[mwform_text name="address"]
Note:
[mwform_textarea name="note"]
Order:
[cfshoppingcart_check_out]
[mwform_backButton value="Return"] [mwform_submitButton name="mwform_submitButton-422" confirm_value="Confirm" submit_value="Send"]

* [cfshoppingcart_cart_link]
This shortcode output Cart Page link.

- Options:
-- text    : link text
-- url_only: true/false

* [cfshoppingcart_check_out_link]
This shortcode output Check Out Page link.

- Options:
-- text    : link text
-- url_only: true/false


= Custom Field =

* Quantity Field:

Nothing or #text or #number

or

#select
0
10
20|default
30

* Stock Field:

Input number or -1 (Many)

or

#select
Color=Red|Size=S|=20
Color=Green|Size=M|=23
Color=Blue|Size=L|=-1

* User defined field:

Text

or

#select
Red
Green
Blue

or

#select
Red|add=10.25 (additonal price)
Green
Blue


= Example CSS =

@charset "UTF-8";

.cfshoppingcart table th {
    text-align: left;
}

.cfshoppingcart table {
    width: 94%;
}

article .cfshoppingcart table {
    border-collapse:collapse;
}

article .cfshoppingcart table th,
article .cfshoppingcart table td {
    padding: 4px;
    border: 1px solid #ccc;
}

.cfshoppingcart table th {
    width: 20%;
}

.cfshoppingcart_widget table {
    width: 100%;
}

.cfshoppingcart_widget table th {
    width: 50%;
}

.cfshoppingcart_widget table {
    border-bottom: 1px solid #aaa;
}

.cfshoppingcart_cart table .quantity {
    text-align: center;
    line-height: 1.5rem;
    min-width: 50px;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 4px;
    display: inline-block;
}

input[type="text"],
input[type="submit"],
select,
button {
    min-width: 80px;
    padding: 4px 10px; 
}

button[value="quantity_plus"],
button[value="quantity_minus"] {
    min-width: 40px;
}


= Filter =

* Add Header and Footer.

- Edit theme functions.php:
-- Appearance Editor Theme Functions (functions.php)

e.g.
function cfshoppingcart_filter_test($content, $post_id) {
    return '<p>Header Post-ID:'.$post_id ."</p>". $content ."<p>Footer!</p>";
}
add_filter('cfshoppingcart_filter_the_content', 'cfshoppingcart_filter_test', 11, 2);
add_filter('cfshoppingcart_filter_cart', 'cfshoppingcart_filter_test', 11, 2);
add_filter('cfshoppingcart_filter_widget', 'cfshoppingcart_filter_test', 11, 2);
add_filter('cfshoppingcart_filter_check_out', 'cfshoppingcart_filter_test', 11, 2);

* Add Image to Cart Page.

- Active QF-GetThumb-wb plugin.
- Edit theme functions.php:
-- Appearance Editor Theme Functions (functions.php)

e.g.
function put_image_to_cart($content, $post_id) {
    global $post;
    $save_post = $post;
    $post = get_post($post_id);
    $img = the_qf_get_thumb_one('width=120&crop_h=90&height=120&crop_w=120');
    $post = $save_post;
    return $img . $content;
}
add_filter('cfshoppingcart_filter_cart', 'put_image_to_cart', 10, 2);


== Upgrade Notice ==

= 2.0.0 =

BE CAREFUL: This version 2.0.0 is NOT COMPATIBLE with 0.8 series and less than 0.8 series.

== Changelog ==

= 2.0.1 =
* Fix, has been empty cart at mistaken timing.
* Add keyword: '<!-- cfshoppingcart-product-embed -->'
  This keyword will be replaced product information at a product page.

= 2.0.0 =
* This is not compatible with previous version.
* Compatible with WordPress 4.2.2.


== Screenshots ==

1. screenshot-1.png
2. screenshot-2.png
3. screenshot-3.png


