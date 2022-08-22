<?php

function nineamdev_is_enabled(){
	$enabled = get_option('nineamdev_woo_checkout_cart_enable');
	return $enabled == 'yes' ? true : false;
}