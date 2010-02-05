<?php
/* add_wp_head.php
 * -*- Encoding: utf8n -*-
 */

function cfshoppingcart_add_wp_head() {
    echo '<script type="text/javascript">' . "\n";
    echo "//<![CDATA[\n";
    require_once('cfshoppingcart.js.php');
    echo "//]]>\n";
    echo '</script>' . "\n";
}
?>
