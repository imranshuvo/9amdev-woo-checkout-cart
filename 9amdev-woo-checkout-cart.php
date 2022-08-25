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


include dirname(__FILE__).'/inc/helpers.php';


add_filter( 'plugin_action_links_'.plugin_basename(__FILE__),'nineamdev_add_plugin_link', 10, 1);

function nineamdev_add_plugin_link( $links) {
    $action_links = array(
        'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=nineamdev-woo-checkout-cart' ) . '" aria-label="' . esc_attr__( 'View WooCommerce settings', 'woocommerce' ) . '">' . esc_html__( 'Settings', 'woocommerce' ) . '</a>',
    );

    return array_merge( $action_links, $links );
    
}


add_action('woocommerce_settings_tabs','nineamdev_woo_checkout_cart_settings_tab');

function nineamdev_woo_checkout_cart_settings_tab(){
    $current_tab = '';
    if(isset($_GET['tab'])){
        $current_tab = $_GET['tab'] == 'nineamdev-woo-checkout-cart' ? 'nav-tab-active' : '';
    }
    
    echo '<a href="admin.php?page=wc-settings&tab=nineamdev-woo-checkout-cart" class="nav-tab '.$current_tab.'">'.__("Checkout Cart", "9amdev-woo-checkout-cart").'</a>';
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




/***
 * *
 * *
 * */

//Add the required styles
add_action('wp_enqueue_scripts','nineamdev_woo_checkout_cart_scripts');

function nineamdev_woo_checkout_cart_scripts(){
    wp_enqueue_style('nineamdev-checkout-cart-style', plugins_url('assets/css/style.css', __FILE__));

    wp_register_script('nineamdev-checkout-cart-script', plugins_url('assets/js/custom.js', __FILE__), array('jquery'));
    wp_enqueue_script('nineamdev-checkout-cart-script');

    $ajax_url = admin_url('admin-ajax.php');
    wp_localize_script('nineamdev-checkout-cart-script','nineamdev_checkout_ajax_object', array(
            'ajax_url' => $ajax_url,
    ));
}


//Add the edit button
add_action('woocommerce_checkout_before_order_review','nineamdev_checkout_edit_button_html', 1);

function nineamdev_checkout_edit_button_html(){
    if(!nineamdev_is_enabled()){
        return;
    }
    echo '<a href="#" class="nineamdev_edit-cart"><img src="'.plugins_url('assets/images/edit.png', __FILE__).'"> '.__('Edit items','9amdev-woo-checkout-cart').'</a>';
}


//Load the cart in a popup
add_action('wp_footer','nineamdev_checkout_cart_html');

function nineamdev_checkout_cart_html(){
    if(!nineamdev_is_enabled() || is_wc_endpoint_url('order-received') || !is_checkout()){
        return;
    }
    ?>


    <?php 
}