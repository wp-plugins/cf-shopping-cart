<?php
/*
 * put_shipping.php
 * -*- Encoding: utf8n -*-
 */

/*
 * shortcode: 
 * [cfshoppingcart_put_shipping]
 *   content...
 * [/cfshoppingcart_put_shipping]
 */


/*
 * Example:
[cfshoppingcart_put_shipping]
<table>
<tr><td>shipping1</td><td>:</td><td>min1</td>
  <td>gta1</td><td>Total price</td><td>gtb1</td><td>max1</td></tr>
<tr><td>shipping2</td><td>:</td><td>min2</td>
  <td>gta2</td><td>Total price</td><td>gtb2</td><td>max2</td></tr>
<tr><td>shipping3</td><td>:</td><td>min3</td>
  <td>gta3</td><td>Total price</td><td>gtb3</td><td>max3</td></tr>
<tr><td>shipping4</td><td>:</td><td>min4</td>
  <td>gta4</td><td>Total price</td><td>gtb4</td><td>max4</td></tr>
<tr><td>shipping5</td><td>:</td><td>min5</td>
  <td>gta5</td><td>Total price</td><td>gtb5</td><td>max5</td></tr>
</table>
[/cfshoppingcart_put_shipping]
*/

function cfshoppingcart_put_shipping($atts, $content = NULL, $code = '') {
    //print_r($args, $content);
    global $wpCFShoppingcart;
    $model = $wpCFShoppingcart->shipping->model;
    $currency_format = $wpCFShoppingcart->model->getCurrencyFormat();
    
    for ($l = 0; $l < 5; $l++) {
        $content = str_replace('shipping' . ($l+1), sprintf($currency_format,$model->getShipping($l, 0)), $content);
        $content = str_replace('min' . ($l+1), sprintf($currency_format,$model->getShipping($l, 1)), $content);
        $content = str_replace('gta' . ($l+1), esc_html($model->getShipping($l, 2)), $content);
        $content = str_replace('gtb' . ($l+1), esc_html($model->getShipping($l, 3)), $content);
        $content = str_replace('max' . ($l+1), sprintf($currency_format,$model->getShipping($l, 4)), $content);
    }
    return $content;
}
add_shortcode('cfshoppingcart_put_shipping', 'cfshoppingcart_put_shipping');

?>
