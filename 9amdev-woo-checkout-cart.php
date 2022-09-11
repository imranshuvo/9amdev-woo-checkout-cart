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

defined( 'ABSPATH' ) || exit;

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
    $label = get_label();
    ?>
    <h2><?php _e('Woocommerce Checkout Cart Update Settings','9amdev-woo-checkout-cart'); ?></h2>

    <form method="post" action="options.php">
        <input type="hidden" name="nineamdev_woo_checkout_cart_enable" value="no">
        <table class="form-table">
            <tbody>
                <tr valign="top" class="">
                    <th scope="row"><?php _e('Enable','9amdev-woo-checkout-cart'); ?></th>
                    <td>
                        <input type="checkbox" name="nineamdev_woo_checkout_cart_enable" value="yes" <?php echo $is_enabled; ?> > 
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row"><?php _e('Label','9amdev-woo-checkout-cart'); ?></th>
                    <td>
                        <input type="text" name="nineamdev_woo_check_cart_text" placeholder="" value="<?php echo $label; ?>">
                    </td>
                </tr>
            </tbody>
        </table>
    <?php 
}


add_action('admin_init','nineamdev_save_settings');

function nineamdev_save_settings(){
    if(isset($_POST['nineamdev_woo_checkout_cart_enable'])){
        update_option('nineamdev_woo_checkout_cart_enable', $_POST['nineamdev_woo_checkout_cart_enable']);
    }
    if(isset($_POST['nineamdev_woo_check_cart_text'])){
        update_option('nineamdev_woo_check_cart_text', sanitize_text_field($_POST['nineamdev_woo_check_cart_text']));
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

    // $ajax_url = admin_url('admin-ajax.php');
    // wp_localize_script('nineamdev-checkout-cart-script','nineamdev_checkout_ajax_object', array(
    //         'ajax_url' => $ajax_url,
    // ));
}


//Add the edit button
add_action('woocommerce_checkout_order_review','nineamdev_checkout_edit_button_html', 1);

function nineamdev_checkout_edit_button_html(){
    if(!nineamdev_is_enabled()){
        return;
    }

    $label = get_label();
    echo '<a href="#" class="nineamdev_edit-cart"><img src="'.plugins_url('assets/images/edit.png', __FILE__).'"> '.esc_html__($label,'9amdev-woo-checkout-cart').'</a>';
}


//Load the cart in a popup
add_action('wp_footer','nineamdev_checkout_cart_html');

function nineamdev_checkout_cart_html(){
    if(!nineamdev_is_enabled() || is_wc_endpoint_url('order-received') || !is_checkout()){
        return;
    }
    ?>
    <div class="nineamdev_modal-wrapper nineamdev_modal-cart">
        <div class="nineamdev_modal-cart-inner">
            <a href="" id="nineam_woo_cart_close" class="absolute top-0 right-0 bg-slate-700">
                <svg viewBox="0 0 24 24" focusable="false" height="24" width="24" jsname="lZmugf" class="fill-white">
                    <path d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"></path>
                </svg>
            </a>
            <?php  
                
                do_action( 'woocommerce_before_cart' ); ?>

            <form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
                <?php do_action( 'woocommerce_before_cart_table' ); ?>

                <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="product-remove">&nbsp;</th>
                            <th class="product-thumbnail">&nbsp;</th>
                            <th class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
                            <th class="product-price"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
                            <th class="product-quantity"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
                            <th class="product-subtotal"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php do_action( 'woocommerce_before_cart_contents' ); ?>

                        <?php
                        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                            $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                            $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

                            if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                                $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                                ?>
                                <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

                                    <td class="product-remove">
                                        <?php
                                            echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                                'woocommerce_cart_item_remove_link',
                                                sprintf(
                                                    '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
                                                    esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                                                    esc_html__( 'Remove this item', 'woocommerce' ),
                                                    esc_attr( $product_id ),
                                                    esc_attr( $_product->get_sku() )
                                                ),
                                                $cart_item_key
                                            );
                                        ?>
                                    </td>

                                    <td class="product-thumbnail">
                                    <?php
                                    $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

                                    if ( ! $product_permalink ) {
                                        echo $thumbnail; // PHPCS: XSS ok.
                                    } else {
                                        printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
                                    }
                                    ?>
                                    </td>

                                    <td class="product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">
                                    <?php
                                    if ( ! $product_permalink ) {
                                        echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
                                    } else {
                                        echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
                                    }

                                    do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

                                    // Meta data.
                                    echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

                                    // Backorder notification.
                                    if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
                                        echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
                                    }
                                    ?>
                                    </td>

                                    <td class="product-price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
                                        <?php
                                            echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
                                        ?>
                                    </td>

                                    <td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>">
                                    <?php
                                    if ( $_product->is_sold_individually() ) {
                                        $product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
                                    } else {
                                        $product_quantity = woocommerce_quantity_input(
                                            array(
                                                'input_name'   => "cart[{$cart_item_key}][qty]",
                                                'input_value'  => $cart_item['quantity'],
                                                'max_value'    => $_product->get_max_purchase_quantity(),
                                                'min_value'    => '0',
                                                'product_name' => $_product->get_name(),
                                            ),
                                            $_product,
                                            false
                                        );
                                    }

                                    echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
                                    ?>
                                    </td>

                                    <td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>">
                                        <?php
                                            echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>

                        <?php do_action( 'woocommerce_cart_contents' ); ?>

                        <tr>
                            <td colspan="6" class="actions">

                                <?php if ( wc_coupons_enabled() ) { ?>
                                    <div class="coupon">
                                        <label for="coupon_code"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" /> <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?></button>
                                        <?php do_action( 'woocommerce_cart_coupon' ); ?>
                                    </div>
                                <?php } ?>

                                <button type="submit" class="button" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button>

                                <?php do_action( 'woocommerce_cart_actions' ); ?>

                                <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
                            </td>
                        </tr>

                        <?php do_action( 'woocommerce_after_cart_contents' ); ?>
                    </tbody>
                </table>
                <?php do_action( 'woocommerce_after_cart_table' ); ?>
            </form>

            <?php do_action( 'woocommerce_before_cart_collaterals' ); ?>

            <div class="cart-collaterals">
                <?php
                    /**
                     * Cart collaterals hook.
                     *
                     * @hooked woocommerce_cross_sell_display
                     * @hooked woocommerce_cart_totals - 10
                     */
                    do_action( 'woocommerce_cart_collaterals' );
                ?>
            </div>

            <?php do_action( 'woocommerce_after_cart' ); ?>
        </div>
    </div>
    <?php 
}