/* JQUERY PREVENT CONFLICT */
(function ($) {

	$(document).ready(function () {

		// CART
		$(document.body).on('click', '#button-cart', function () {
			$.ajax({
				url: '/cart/add',
				type: 'post',
				data: $('.product-info input[type=text], .product-info input[type=hidden], .product-info input[type=radio]:checked, .product-info input[type=checkbox]:checked, .product-info select, .product-info textarea'),
				dataType: 'json',
				success: function (json) {
					window.location = '/cart';
				}
			});
		});

		// CHECKOUT

		$(document.body).on('click', '#button-account', function () {
			$.ajax({
				url: '/checkout/checkout/register',
				dataType: 'json',
				beforeSend: function () {
				},
				complete: function () {
				},
				success: function (json) {
					$('#checkout .checkout-heading').html('Step2: Billing information');
					$('#checkout .checkout-content').html(json['output']);
				}
			});
		});

		$(document.body).on('click', '#button-register', function () {
			$.ajax({
				url: '/checkout/checkout/register',
				type: 'post',
				dataType: 'json',
				data: $('#step1 input[type=text], #step1 input[type=tel], #step1 input[type=email], #step1 input[type=password], #step1 input[type=checkbox]:checked, #step1 input[type=radio]:checked, #step1 select'),
				success: function (json) {
					console.log(json);
					if (json['error']) {
						// perfom here all that is occured when there errors
					} else if (json['redirect'])
						window.location = json['redirect'];
				}
			});
		});

		$('#button-login').on('click', function () {
			$.ajax({
				url: '/checkout/checkout/login',
				type: 'post',
				dataType: 'json',
				data: $('#step1 input[type=text], #step1 input[type=email], #step1 input[type=password]'),
				success: function (json) {
					console.log(json);
					if (json['error']) {
						// perfom here all that is occured when there errors
					} else if (json['redirect']) {
						window.location = json['redirect'];
					}
				}
			});
		});

		// Shipping information
		$('#button-shipping-information').on('click', function () {
			var formData = '';
			formData = $('#shipping-information').serialize();
			$.ajax({
				url: '/checkout/checkout/shipping_information',
				type: 'post',
				dataType: 'json',
				data: formData,
				success: function (json) {
					console.log(json);
					if (json['error']) {
						alert('perfom here all that is occured when there errors');
					} else if (json['redirect']) {
						window.location = json['redirect'];
					}
				}
			});
		});

		// Shipping method
		$('#button-shipping-method').on('click', function () {
			$.ajax({
				url: '/checkout/checkout/shipping_method',
				type: 'post',
				dataType: 'json',
				data: $("#shipping_methods").serialize(),
				success: function (json) {
					console.log(json);
					if (json['error']) {
						// perfom here all that is occured when there errors
					} else if (json['redirect'])
						window.location = json['redirect'];
				}
			});
		});

		// Payment
		$('#button-payment-method').on('click', function () {
			$.ajax({
				url: '/checkout/checkout/payment_method',
				type: 'post',
				dataType: 'json',
				data: $('#payment_method_form').serialize(),
				success: function (json) {
					console.log(json);
					if (json['error']) {
						// perfom here all that is occured when there errors
					} else if (json['redirect'])
						window.location = json['redirect'];
				}
			});
		});

		// Review
		$('#button-confirm').on('click', function () {
			$.ajax({
				url: "/checkout/checkout/confirm",
				type: 'post',
				success: function (data, status, xhr) {
					var obj = jQuery.parseJSON(xhr.responseText);
					notify(data.toString());
					notify(status.toString());
				},
				error: function (xhr, status, error) {
					notify(status);
				}
			});
		});

		$('#content').on('change', '#limit-top, #sortby-top', function () {
			window.location = $(this).val();
		});
	});

})(jQuery);

function populateZonesByCountry(countryid) {
	$('#shipping-information select[name="shipping[zone_id]"]').load('/customer/address/zone/' + countryid);
}
