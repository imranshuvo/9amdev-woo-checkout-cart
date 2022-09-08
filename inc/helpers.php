<?php

function nineamdev_is_enabled(){
	$enabled = get_option('nineamdev_woo_checkout_cart_enable');
	return $enabled == 'yes' ? true : false;
}


function get_label(){
	$label = get_option('nineamdev_woo_check_cart_text');
	return $label == '' ? esc_html__('Edit items','9amdev-woo-checkout-cart') : $label;
}