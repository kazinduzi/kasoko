var addToCart = function(product_id, quantity) {
    quantity = typeof(quantity)!='undefined'?quantity : 1;
    $.ajax({
        url: '/cart/add',
        type: 'post',
        data: 'product_id='+product_id+'&quantity='+quantity,
        dataType: 'json',
        success: function(json) {
            if (json.saved) $('#count-cart-item').html(json.count);
            $('html, body').animate({
                scrollTop: $('#cart-content').offset().top
            }, 300);
        }
    });
};

var removeFromCart = function(product_id) {
    $.ajax({
        url: '/cart/remove',
        type: 'post',
        data: 'product_id=' + product_id,
        dataType: 'json',
        success: function(json) {
        }
    });
};

var addToWishList = function(product_id) {
    $.ajax({
        url: '/customer/account/wishlist/add',
        type: 'post',
        data: 'product_id=' + product_id,
        dataType: 'json',
        success: function(json) {
            $('.success, .warning, .attention, .information').remove();
            if (json['success']) {
                $('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
                $('.success').fadeIn('slow');
                $('#wishlist-total').html(json['total']);
                $('html, body').animate({ 
		    scrollTop: 0 
		}, 300);
            }
        }
    });
};

var addToCompare = function(product_id) {
    $.ajax({
        url: '/product/compare/add',
        type: 'post',
        data: 'product_id=' + product_id,
        dataType: 'json',
        success: function(json) {
            $('.success, .warning, .attention, .information').remove();
            if (json['success']) {
                $('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
                $('.success').fadeIn('slow');
                $('#compare-total').html(json['total']);
                $('html, body').animate({ scrollTop: 0 }, 'slow');
            }
        }
    });
};

var setSelectedShipping = function(addressId) {
    if ($('#same_billing_shipping_address').is(':checked')) {
        $('#use_for_shipping_yes').attr('checked', true);  
        $('#shipping-step').css({display: 'none'});
    } else {
        $('#use_for_shipping_no').attr('checked', true);    
        $('#shipping-step').css({display: ''});
    }
}

var populateZonesByCountry = function(countryid) {    
    $('select[name="zone_id"]').load('/customer/address/zone/' + countryid);
}