function addToCart(product_id, quantity) {
	quantity = typeof (quantity) != 'undefined' ? quantity : 1;
	$.ajax({
		url: HOME_URL + '/cart/add',
		type: 'post',
		data: 'product_id=' + product_id + '&quantity=' + quantity,
		dataType: 'json',
		success: function (json) {
			if (json.saved)
				$('#count-cart-item').html(json.count);
			$('html, body').animate({
				scrollTop: $('#cart-content').offset().top
			}, 300);
		}
	});
}
;

function removeFromCart(product_id) {
	$.ajax({
		url: HOME_URL + '/cart/remove',
		type: 'post',
		data: {product_id: product_id},
		dataType: 'json',
		success: function (json) {
			window.location.reload();
		}
	});
}
;

function addToWishList(product_id) {
	$.ajax({
		url: HOME_URL + '/customer/wishlist/add',
		type: 'post',
		data: 'product_id=' + product_id,
		dataType: 'json',
		success: function (json) {
			if (json['success']) {
				window.location = HOME_URL + '/customer/wishlist';
			}
		}
	});
}
;

function removeFromWishList(product_id) {
	$.ajax({
		url: HOME_URL + '/customer/wishlist/remove',
		type: 'post',
		data: 'product_id=' + product_id,
		dataType: 'json',
		success: function (json) {
			$('.success, .warning, .attention, .information').remove();
			if (json['success']) {
				$('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
				$('.success').fadeIn('slow');
				window.location = HOME_URL + '/customer/wishlist';
			}
		}
	});
}
;

function addToCompare(product_id) {
	$.ajax({
		url: HOME_URL + '/product/compare/add',
		type: 'post',
		data: 'product_id=' + product_id,
		dataType: 'json',
		success: function (json) {
			$('.success, .warning, .attention, .information').remove();
			if (json['success']) {
				$('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
				$('.success').fadeIn('slow');
				$('#compare-total').html(json['total']);
				$('html, body').animate({scrollTop: 0}, 'slow');
			}
		}
	});
}
;

function setSelectedShipping(addressId) {
	if ($('#same_billing_shipping_address').is(':checked')) {
		$('#use_for_shipping_yes').attr('checked', true);
		$('#shipping-step').css({display: 'none'});
	} else {
		$('#use_for_shipping_no').attr('checked', true);
		$('#shipping-step').css({display: ''});
	}
}

function populateZonesByCountry(countryid, target) {
	if (target) {
		target.load(HOME_URL + '/customer/address/zone/' + countryid);
	} else {
		$('select[name="zone_id"]').load(HOME_URL + '/customer/address/zone/' + countryid);
	}
}