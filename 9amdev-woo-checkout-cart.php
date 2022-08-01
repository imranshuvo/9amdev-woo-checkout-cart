<?php 
/**
 * Plugin Name:       Woocommerce Checkout Cart Update
 * Plugin URI:        http://9amdev.com/plugins/9amdev-woo-checkout-cart
 * Description:       This plugin allows you to update cart items quantity, remove items from checkout page.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            9amdev
 * Author URI:        http://9amdev.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       9amdev-woo-checkout-cart
 * Domain Path:       /languages
 */


add_action('woocommerce_settings_tabs','nineamdev_woo_checkout_cart_settings_tab');

function nineamdev_woo_checkout_cart_settings_tab(){
    $current_tab = '';
    if(isset($_GET['tab'])){
        $current_tab = $_GET['tab'] == 'nineamdev-woo-checkout-cart' ? 'nav-tab-active' : '';
    }
    
    echo '<a href="admin.php?page=wc-settings&tab=nineamdev-woo-checkout-cart" class="nav-tab "'.$current_tab.'">'.__("Checkout Cart", "9amdev-woo-checkout-cart").'</a>';
}

add_action('woocommerce_settings_nineamdev-woo-checkout-cart','nineamdev_woo_checkout_tab_content');

function nineamdev_woo_checkout_tab_content(){
    $is_enabled = get_option('nineamdev_woo_checkout_cart_enable') == 'yes' ? 'checked' : '';

    ?>
    <h2><?php _e('Woocommerce Checkout Cart Update Settings','9amdev-woo-checkout-cart'); ?></h2>

    <form method="post" action="options.php">
        <input type="hidden" name="nineamdev_woo_checkout_cart_enable" value="no">
        <input type="checkbox" name="nineamdev_woo_checkout_cart_enable" value="yes" <?php echo $is_enabled; ?> > <?php _e('Enable','9amdev-woo-checkout-cart'); ?>
    <?php 
}


add_action('admin_init','nineamdev_save_settings');

function nineamdev_save_settings(){
    if(isset($_POST['nineamdev_woo_checkout_cart_enable'])){
        update_option('nineamdev_woo_checkout_cart_enable', $_POST['nineamdev_woo_checkout_cart_enable']);
    }
}


//
add_filter('woocommerce_cart_item_name','nineamdev_add_remove_and_quantity_field', 10, 3);

function nineamdev_add_remove_and_quantity_field($product_name, $cart_item, $cart_item_key){
    //Get the product/variation id first
    $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

    $html = '<a href="#" style="color: red;margin-right: 10px;">X</a>';
    $html .= $product_name;
    $html .= '<form></form>';

    return $html;
}