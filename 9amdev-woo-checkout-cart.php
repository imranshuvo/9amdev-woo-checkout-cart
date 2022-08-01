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


add_filter( 'plugin_action_links', 'nineamdev_add_plugin_link', 10, 2 );
function nineamdev_add_plugin_link( $links ) {

    $action_links = array(
        'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=nineamdev-woo-checkout-cart' ) . '" aria-label="' . esc_attr__( 'View settings', '9amdev-woo-checkout-cart' ) . '">' . esc_html__( 'Settings', '9amdev-woo-checkout-cart' ) . '</a>',
    );

    return array_merge( $action_links, $links );
}


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

    $html = apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        'woocommerce_cart_item_remove_link',
                        sprintf(
                            '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
                            esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                            esc_html__( 'Remove this item', 'woocommerce' ),
                            esc_attr( $_product->get_id() ),
                            esc_attr( $_product->get_sku() )
                        ),
                        $cart_item_key
                    );
    $html .= $product_name;
    $html .= '';
    return $html;
}