<?php

namespace cfshoppingcart;

use Exception;

class serial {

    private $session;
    private $cart_id;

    public function __construct() {
        $this->cart_id = 'c1';
    }

    public function &load() {
        //print_r($_SESSION);
        if (isset($_SESSION[DOMAIN_CF_SHOPPING_CART][$this->cart_id])) {
            $this->session = unserialize($_SESSION[DOMAIN_CF_SHOPPING_CART][$this->cart_id]);
        } else {
            $this->session = new cart();
        }
        return $this->session;
    }

    public function save() {
        $_SESSION[DOMAIN_CF_SHOPPING_CART][$this->cart_id] = serialize($this->session);
    }

}

class cart {

    public $items;
    private $products;
    public $sum;

    public function __construct() {
        $this->items = array();
        $this->products = array();
        $this->sum = new sum();
    }

    private function check_stock() {
        foreach ($this->items as $item) {
            $msg = $item->check_stock();
            if ($msg !== CF_SHOPPING_CART_MSG_TRUE) {
                return $msg;
            }
        }
        return CF_SHOPPING_CART_MSG_TRUE;
    }

    private function sold() {
        foreach ($this->items as $item) {
            $msg = $item->sold();
            if ($msg !== CF_SHOPPING_CART_MSG_TRUE) {
                return $msg;
            }
        }
        return CF_SHOPPING_CART_MSG_TRUE;
    }

    /**
     * 
     * @return type CF_SHOPPING_CART_MSG_TRUE or ohter
     */
    public function check_out() {
        $msg = $this->check_stock();
        if ($msg !== CF_SHOPPING_CART_MSG_TRUE) {
            return $msg;
        }
        $msg = $this->sold();
        if ($msg !== CF_SHOPPING_CART_MSG_TRUE) {
            return $msg;
        }
        return CF_SHOPPING_CART_MSG_TRUE;
    }

    public function calc() {
        foreach ($this->items as $product_key => $item) {
            if (!$item->quantity) {
                unset($this->items[$product_key]);
            }
        }
    }

    public function add_to_cart() {
        $this->calc();

        $product_id = to_int(get_POSTvalue('product_id'));

        if (!$product_id) {
            return CF_SHOPPING_CART_MSG_QUANTITY_ADD_TO_CART_FAILED;
        }

        if (!array_key_exists($product_id, $this->products)) {
            $this->products[$product_id] = new product();
        }
        $item = new item($product_id, $this->products[$product_id], $this->sum);
        if (!array_key_exists($item->product_key, $this->items)) {
            $this->items[$item->product_key] = clone $item;
        }
        return $this->items[$item->product_key]->add_to_cart();
    }

    public function change_quantity() {
        $product_key = name_decode(getPostValue('product_key'), CF_SHOPPING_CART_PRODUCT_KEY_PREFIX);
        //echo "[product_key: $product_key]";
        if (!$product_key) {
            // add to cart
            $product_id = to_int(get_POSTvalue('product_id'));
            //echo "[product_id: $product_id]";

            if (!$product_id) {
                return CF_SHOPPING_CART_MSG_QUANTITY_ADD_TO_CART_FAILED;
            }

            if (!array_key_exists($product_id, $this->products)) {
                //echo "[not exists array]";
                $this->products[$product_id] = new product();
            }
            $item = new item($product_id, $this->products[$product_id], $this->sum);
            $product_key = $item->product_key;
            //var_dump($item);
            if (!array_key_exists($item->product_key, $this->items)) {
                //echo "[not exists array 2]";
                $this->items[$item->product_key] = clone $item;
            }
        }

        /*
          if (!$product_key) {
          return CF_SHOPPING_CART_MSG_QUANTITY_ADD_TO_CART_FAILED;
          }

          if (!array_key_exists($product_key, $this->items)) {
          return CF_SHOPPING_CART_MSG_QUANTITY_ADD_TO_CART_FAILED;
          }
         * 
         */
        return $this->items[$product_key]->change_quantity();
    }

    public function quantity_plus() {
        $product_key = name_decode(getPostValue('product_key'), CF_SHOPPING_CART_PRODUCT_KEY_PREFIX);

        if (!$product_key) {
            return CF_SHOPPING_CART_MSG_QUANTITY_ADD_TO_CART_FAILED;
        }

        if (!array_key_exists($product_key, $this->items)) {
            return CF_SHOPPING_CART_MSG_QUANTITY_ADD_TO_CART_FAILED;
        }
        return $this->items[$product_key]->quantity_plus();
    }

    public function quantity_minus() {
        $product_key = name_decode(getPostValue('product_key'), CF_SHOPPING_CART_PRODUCT_KEY_PREFIX);

        if (!$product_key) {
            return CF_SHOPPING_CART_MSG_DECREASE_QUANTITY_HAS_FAILED;
        }

        if (!array_key_exists($product_key, $this->items)) {
            return CF_SHOPPING_CART_MSG_DECREASE_QUANTITY_HAS_FAILED;
        }
        return $this->items[$product_key]->quantity_minus();
    }

}

class sum {

    public $total;
    public $gross;
    public $shipping;
    public $inclusive_sum;

    public function __construct() {
        $this->total = 0;
        $this->gross = 0;
        $this->shipping = 0;
        $this->inclusive_sum = 0;
    }

    public function shipping() {
        $ship = new shipping();
        list($shipping_price, $shipping_message) = $ship->calc($this->total);
        if ($shipping_message) {
            // error
            $shipping_price = 0;
        }
        $this->shipping = $shipping_price;
        $this->inclusive_sum = $this->total + $this->shipping;
    }

}

class product {

    public $subtotal;
    public $quantity;

    public function __construct() {
        $this->subtotal = 0;
        $this->quantity = 0;
    }

}

class item {

    public $product_id;     // post_id
    public $product_key;
    public $price;
    public $options;
    //
    public $quantity;
    public $subtotal;
    //
    private $product;
    private $sum;
    //
    private $max_quantity_of_total;
    private $max_quantity_of_a_product;

    public function __construct($product_id, $product, $sum) {
        $this->product = $product;
        $this->sum = $sum;
        $this->options = array();
        //
        $this->max_quantity_of_total = to_int(opt::get_option('max_quantity_of_total'));
        $this->max_quantity_of_a_product = to_int(opt::get_option('max_quantity_of_a_product'));

        //
        $this->product_id = $product_id;
        $product_key = $product_id;

        $price = to_float(get_cf($product_id, opt::get_option('price_field_name')));
        if ($price === null) {
            throw new Exception(__('Class item construct error. Price error.', DOMAIN_CF_SHOPPING_CART));
        }

        foreach ($_POST as $post_key => $post_value) {
            $post_key = name_decode($post_key, CF_SHOPPING_CART_OPTION_KEY_PREFIX);
            if ($post_key === false) {
                continue;
            }
            $opt = $this->get_opt($product_id, $post_key, $post_value);
            if (!array_key_exists($post_key, $opt)) {
                throw new Exception(__('Class item construct error. Custom Field error.', DOMAIN_CF_SHOPPING_CART));
            }
            $product_key .= '|' . $post_key . '=' . $post_value;
            $price += $opt['add'];
            $this->options[$post_key] = $post_value;
        }

        $this->product_key = $product_key;
        $this->price = $price;
        $this->quantity = 0;
        $this->subtotal = 0;
    }

    public function sold() {
        $stock_array = get_stock($this->product_key);
        $stock = $stock_array['stock'];
        $stock_type = $stock_array['type'];

        if (set_stock($this->product_key, $stock - $this->quantity)) {
            return CF_SHOPPING_CART_MSG_TRUE;
        } else {
            return CF_SHOPPING_CART_MSG_FAILED_IN_CHANGE_STOCK_QUANTITY;
        }
    }

    public function check_stock() {
        $stock_array = get_stock($this->product_key);
        $stock = $stock_array['stock'];
        $stock_type = $stock_array['type'];

        if ($stock < 0 ||
                (($this->quantity <= $stock && $this->product->quantity <= $this->max_quantity_of_a_product && $this->sum->gross <= $this->max_quantity_of_total) &&
                (($stock_type === 'number' && $this->product->quantity <= $stock) || ($stock_type === 'select')))) {
            return CF_SHOPPING_CART_MSG_TRUE;
        }
        return CF_SHOPPING_CART_MSG_ORDER_QUANTITY_WAS_MORE_THAN_NUMBER_OF_STOCK;
    }

    public function change_quantity() {
        $new_quantity = to_int(getPostValue('quantity'));
        if ($new_quantity === null || $new_quantity < 0) {
            return CF_SHOPPING_CART_MSG_UNKNOWN_QUANTITY;
        }

        $stock_array = get_stock($this->product_key);
        $stock = $stock_array['stock'];
        $stock_type = $stock_array['type'];

        $quantity_input_type = get_quantity_input_type($this->product_id, $new_quantity);
        //$new_quantity = $quantity_input_type['value'];
        if ($quantity_input_type['type'] == 'text' || $quantity_input_type['type'] == 'number') {
            
        } else if ($quantity_input_type['type'] == 'select') {
            if (array_search($new_quantity, $quantity_input_type['array']) === false) {
                return CF_SHOPPING_CART_MSG_UNKNOWN_QUANTITY;
            }
        } else {
            return CF_SHOPPING_CART_MSG_UNKNOWN_QUANTITY;
        }

        if ($stock == 0) {
            return CF_SHOPPING_CART_MSG_STOCK_QUANTITY_IS_EMPTY;
        } else if ($stock > 0) {
            /*
              if ($this->sum->gross >= $this->max_quantity_of_total) {
              return CF_SHOPPING_CART_MSG_QUANTITIES_WAS_MORE_THAN_ORDER_MAX_QUANTITY_OF_TOTAL;
              }
              if ($this->product->quantity >= $this->max_quantity_of_a_product) {
              return CF_SHOPPING_CART_MSG_QUANTITIES_WAS_MORE_THAN_ORDER_MAX_QUANTITY_OF_THE_PRODUCT;
              }
              if ($stock_type === 'number' && $this->product->quantity >= $stock) {
              return CF_SHOPPING_CART_MSG_ORDER_QUANTITY_WAS_MORE_THAN_NUMBER_OF_STOCK;
              }
              if ($stock_type === 'select' && $this->quantity >= $stock) {
              return CF_SHOPPING_CART_MSG_ORDER_QUANTITY_WAS_MORE_THAN_NUMBER_OF_STOCK;
              }
             * 
             */
            //if ($stock_type === 'number') {
            // the product
            if ($this->sum->gross - $this->quantity + $new_quantity > $this->max_quantity_of_total) {
                return CF_SHOPPING_CART_MSG_QUANTITIES_WAS_MORE_THAN_ORDER_MAX_QUANTITY_OF_TOTAL;
            }
            //echo "(".$this->product->quantity." - ".$this->quantity." + ".$new_quantity." > ".$this->max_quantity_of_a_product.")";
            if ($this->product->quantity - $this->quantity + $new_quantity > $this->max_quantity_of_a_product) {
                return CF_SHOPPING_CART_MSG_QUANTITIES_WAS_MORE_THAN_ORDER_MAX_QUANTITY_OF_THE_PRODUCT;
            }
            if (($stock_type === 'text' || $stock_type === 'number') && $this->product->quantity - $this->quantity + $new_quantity > $stock) {
                return CF_SHOPPING_CART_MSG_ORDER_QUANTITY_WAS_MORE_THAN_NUMBER_OF_STOCK;
            }
            if ($stock_type === 'select' && $this->quantity - $this->quantity + $new_quantity > $stock) {
                return CF_SHOPPING_CART_MSG_ORDER_QUANTITY_WAS_MORE_THAN_NUMBER_OF_STOCK;
            }
            //} else if ($stock_type === 'select') {
            //}
        } else if ($stock < 0) {
            if ($this->sum->gross - $this->quantity + $new_quantity > $this->max_quantity_of_total) {
                return CF_SHOPPING_CART_MSG_QUANTITIES_WAS_MORE_THAN_ORDER_MAX_QUANTITY_OF_TOTAL;
            }
            if ($this->product->quantity - $this->quantity + $new_quantity > $this->max_quantity_of_a_product) {
                return CF_SHOPPING_CART_MSG_QUANTITIES_WAS_MORE_THAN_ORDER_MAX_QUANTITY_OF_THE_PRODUCT;
            }
        }

        // save now value
        $quantity = $this->quantity;
        $subtotal = $this->subtotal;
        //
        $this->quantity = $new_quantity;
        $this->subtotal = $this->price * $new_quantity;
        //
        $this->product->quantity = $this->product->quantity - $quantity + $new_quantity;
        $this->product->subtotal = $this->product->subtotal - $subtotal + $this->price * $new_quantity;
        //
        $this->sum->gross = $this->sum->gross - $quantity + $new_quantity;
        $this->sum->total = $this->sum->total - $subtotal + $this->price * $new_quantity;
        $this->sum->shipping();

        //echo "quantity: ". $this->quantity ." subtotal: ".$this->subtotal." quantity: ".$this->product->quantity." subtotal: ".$this->product->subtotal." gross: ".$this->sum->gross." total: ".$this->sum->total;
        return CF_SHOPPING_CART_MSG_HAVE_CHANGED_THE_QUANTITY;
    }

    public function add_to_cart() {
        $stock_array = get_stock($this->product_key);
        $stock = $stock_array['stock'];
        $stock_type = $stock_array['type'];

        /*
          if ($stock < 0 ||
          (($this->quantity < $stock && $this->product->quantity < $this->max_quantity_of_a_product && $this->sum->gross < $this->max_quantity_of_total) &&
          (($stock_type === 'number' && $this->product->quantity < $stock) || ($stock_type === 'select')))) {
         */

        if ($stock == 0) {
            return CF_SHOPPING_CART_MSG_STOCK_QUANTITY_IS_EMPTY;
        } else if ($stock > 0) {
            if ($this->sum->gross >= $this->max_quantity_of_total) {
                return CF_SHOPPING_CART_MSG_QUANTITIES_WAS_MORE_THAN_ORDER_MAX_QUANTITY_OF_TOTAL;
            }
            if ($this->product->quantity >= $this->max_quantity_of_a_product) {
                return CF_SHOPPING_CART_MSG_QUANTITIES_WAS_MORE_THAN_ORDER_MAX_QUANTITY_OF_THE_PRODUCT;
            }
            if (($stock_type === 'text' || $stock_type === 'number') && $this->product->quantity >= $stock) {
                return CF_SHOPPING_CART_MSG_ORDER_QUANTITY_WAS_MORE_THAN_NUMBER_OF_STOCK;
            }
            if ($stock_type === 'select' && $this->quantity >= $stock) {
                return CF_SHOPPING_CART_MSG_ORDER_QUANTITY_WAS_MORE_THAN_NUMBER_OF_STOCK;
            }
        } else if ($stock < 0) {
            if ($this->sum->gross >= $this->max_quantity_of_total) {
                return CF_SHOPPING_CART_MSG_QUANTITIES_WAS_MORE_THAN_ORDER_MAX_QUANTITY_OF_TOTAL;
            }
            if ($this->product->quantity >= $this->max_quantity_of_a_product) {
                return CF_SHOPPING_CART_MSG_QUANTITIES_WAS_MORE_THAN_ORDER_MAX_QUANTITY_OF_THE_PRODUCT;
            }
        }

        $this->quantity++;
        $this->subtotal += $this->price;
        //
        $this->product->quantity++;
        $this->product->subtotal += $this->price;
        //
        $this->sum->gross++;
        $this->sum->total += $this->price;
        $this->sum->shipping();

        //echo "quantity: ". $this->quantity ." subtotal: ".$this->subtotal." quantity: ".$this->product->quantity." subtotal: ".$this->product->subtotal." gross: ".$this->sum->gross." total: ".$this->sum->total;
        return CF_SHOPPING_CART_MSG_ADDED_TO_CART;
    }

    public function quantity_plus() {
        //$stock = to_int(get_cf($this->product_id, opt::get_option('stock_quantity_field_name')));
        $stock_array = get_stock($this->product_key);
        $stock = $stock_array['stock'];
        $stock_type = $stock_array['type'];

        /*
          if ($stock < 0 ||
          (($this->quantity < $stock && $this->product->quantity < $this->max_quantity_of_a_product && $this->sum->gross < $this->max_quantity_of_total) &&
          (($stock_type === 'number' && $this->product->quantity < $stock) || ($stock_type === 'select')))) {
         */

        if ($stock == 0) {
            return CF_SHOPPING_CART_MSG_STOCK_QUANTITY_IS_EMPTY;
        } else if ($stock > 0) {
            if ($this->sum->gross >= $this->max_quantity_of_total) {
                return CF_SHOPPING_CART_MSG_QUANTITIES_WAS_MORE_THAN_ORDER_MAX_QUANTITY_OF_TOTAL;
            }
            if ($this->product->quantity >= $this->max_quantity_of_a_product) {
                return CF_SHOPPING_CART_MSG_QUANTITIES_WAS_MORE_THAN_ORDER_MAX_QUANTITY_OF_THE_PRODUCT;
            }
            if (($stock_type === 'text' || $stock_type === 'number') && $this->product->quantity >= $stock) {
                return CF_SHOPPING_CART_MSG_ORDER_QUANTITY_WAS_MORE_THAN_NUMBER_OF_STOCK;
            }
            if ($stock_type === 'select' && $this->quantity >= $stock) {
                return CF_SHOPPING_CART_MSG_ORDER_QUANTITY_WAS_MORE_THAN_NUMBER_OF_STOCK;
            }
        } else if ($stock < 0) {
            if ($this->sum->gross >= $this->max_quantity_of_total) {
                return CF_SHOPPING_CART_MSG_QUANTITIES_WAS_MORE_THAN_ORDER_MAX_QUANTITY_OF_TOTAL;
            }
            if ($this->product->quantity >= $this->max_quantity_of_a_product) {
                return CF_SHOPPING_CART_MSG_QUANTITIES_WAS_MORE_THAN_ORDER_MAX_QUANTITY_OF_THE_PRODUCT;
            }
        }

        $this->quantity++;
        $this->subtotal += $this->price;
        //
        $this->product->quantity++;
        $this->product->subtotal += $this->price;
        //
        $this->sum->gross++;
        $this->sum->total += $this->price;
        $this->sum->shipping();

        return CF_SHOPPING_CART_MSG_ADDED_TO_CART;
    }

    public function quantity_minus() {
        //echo "[quantity_minus]";
        //$stock = to_int(get_cf($this->product_id, opt::get_option('stock_quantity_field_name')));
        $stock_array = get_stock($this->product_key);
        $stock = $stock_array['stock'];
        $stock_type = $stock_array['type'];

        $this->quantity--;
        $this->subtotal -= $this->price;
        //
        $this->product->quantity--;
        $this->product->subtotal -= $this->price;
        //
        $this->sum->gross--;
        $this->sum->total -= $this->price;
        $this->sum->shipping();

        if ($this->quantity < 0 || $this->subtotal < 0 ||
                $this->product->quantity < 0 || $this->product->subtotal < 0 ||
                $this->sum->gross < 0 || $this->sum->total < 0) {
            // 
            //throw new Exception(__('Quantity minus error.', DOMAIN_CF_SHOPPING_CART));
            return CF_SHOPPING_CART_MSG_DECREASE_QUANTITY_HAS_FAILED;
        }
        return CF_SHOPPING_CART_MSG_QUANTITY_WAS_DECREASED;
    }

    /**
     * 
     * @param type $product_id
     * @param type $post_key
     * @param type $post_value
     * @return type array
     */
    private function get_opt($product_id, $post_key, $post_value) {
        $result['add'] = 0;

        if (!$product_id || !$post_key || !$post_value) {
            return array();
        }

        /*
         * $custom_field_value:
         *   #select
         *   Red|add=100.00
         *   Green|add=200.00
         *   Blue
         */
        $custom_field_value = get_cf($product_id, $post_key);
        if (!$custom_field_value) {
            return array();
        }

        $lines = explode("\n", $custom_field_value);

        if (trim(array_shift($lines)) !== '#select') {
            return array();
        }

        /*
         * $lines:
         *   post_value|opt_name=opt_value
         *   ...
         */
        foreach ($lines as $line) {
            $opts = explode('|', $line);
            if (trim(array_shift($opts)) !== $post_value) {
                continue;
            }
            if (array_key_exists($post_key, $result)) {
                return array();
            }
            $result[$post_key] = $post_value;
            foreach ($opts as $o) {
                $a2 = explode('=', $o, 2);
                if (count($a2) != 2) {
                    continue;
                }
                switch ($a2[0]) {
                    case 'add':
                        $a2[1] = to_float($a2[1]);
                        if ($a2[1] === null) {
                            return array();
                        }
                        $result['add'] += $a2[1];
                        break;
                    default:
                        return array();
                }
            }
        }
        return $result;
    }

}
