jQuery(document).ready(function($){
	$('a.nineamdev_edit-cart').on('click', function(e){
		e.preventDefault();

		//Show the cart in popup 
		$('.nineamdev_modal-wrapper.nineamdev_modal-cart').css('display','flex');
	});

	$('a#nineam_woo_cart_close').on('click', function(e){
		e.preventDefault();

		//Show the cart in popup 
		$('.nineamdev_modal-wrapper.nineamdev_modal-cart').css('display','none');
	});
});