jQuery(document).ready(function($){
	$('a.nineamdev_edit-cart').on('click', function(e){
		e.preventDefault();

		console.log('Yes clicked');

		// $.ajax({
		// 	type: 'post',
		// 	url: nineamdev_checkout_ajax_object.ajax_url,
		// 	data: {
		// 		action: "some_action"
		// 	},
		// 	beforeSend: function(){
		// 		console.log('Sending');
		// 	},
		// 	complete: function(response){
		// 		console.log('Done '+ response);
		// 	}
		// });

		//Show the cart in popup 
		$('.nineamdev_popup-cart').addClass('active');
	});
});