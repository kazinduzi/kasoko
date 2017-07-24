/* JQUERY PREVENT CONFLICT */
(function ($) {    
       // Newsletter      
	$(document.body).on('submit','#newsletter-form', function(e) {
            e.preventDefault();            
            var email = $('input#newsletter');
            var regex = /[\w-]+@([\w-]+\.)+[\w-]+/;
            if (!regex.test(email.val())) {                
                email.addClass('fail');                
            } else {            
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',                
                    data: $(this).serialize()
                }).done(function(response) {
                    if (JSON.parse(response) === true){
                        $(location).attr('href', HOME_URL);
                    }                   
                }).fail(function() {                    
                    email.addClass('fail');
                });
            }
	});    
         
       // CART
        $(document.body).on('click', '#button-cart', function() {
            $.ajax({
                url: '/cart/add',
                type: 'post',
                data: $('.product-info input[type=text], .product-info input[type=number], .product-info input[type=hidden], .product-info input[type=radio]:checked, .product-info input[type=checkbox]:checked, .product-info select, .product-info textarea'),
                dataType: 'json',
                success: function(json) {
                    window.location = '/cart';
                }
            });
        });
       
        // CHECKOUT
        $(document.body).on('click', '#button-account', function() {
            $.ajax({
                url: '/checkout/checkout/register',
                dataType: 'json',
                beforeSend: function() {},
                complete: function() {},
                success: function(json) {
                    $('#checkout .checkout-heading').html('Step2: Billing information');
                    $('#checkout .checkout-content').html(json['output']);
                }
            });
        });
        
        $(document.body).on('click', '#button-register', function() {
            $.ajax({
                url: '/checkout/checkout/register',
                type: 'post',
                dataType: 'json',
                data: $('#step1 input[type=text], #step1 input[type=tel], #step1 input[type=email], #step1 input[type=password], #step1 input[type=checkbox]:checked, #step1 input[type=radio]:checked, #step1 select'),
                success: function(json) {
                    console.log(json);
                    if (json['error']) {
                        // perfom here all that is occured when there errors
                    }
                    else if (json['redirect']) window.location = json['redirect'];
                }
            });
        });
        
        $('#button-login').on('click', function() {
            $.ajax({
                url: '/checkout/checkout/login',
                type: 'post',
                dataType: 'json',
                data: $('#step1 input[type=text], #step1 input[type=email], #step1 input[type=password]'),
                success: function(json) {
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
        $('#button-shipping-information').on('click', function() {
            var formData = '';
            formData = $('#shipping-information').serialize();            
            $.ajax({
                url: '/checkout/checkout/shipping_information',
                type: 'post',
                dataType: 'json',
                data: formData,
                success: function(json) {
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
        $('#button-shipping-method').on('click', function() {
            $.ajax({
                url: '/checkout/checkout/shipping_method',
                type: 'post',
                dataType: 'json',
                data: $("#shipping_methods").serialize(),
                success: function(json) {
                    console.log(json);
                    if (json['error']) {
                        // perfom here all that is occured when there errors
                    }
                    else if (json['redirect'])
                        window.location = json['redirect'];
                }
            });
        });
        
        // Payment
        $('#button-payment-method').on('click', function() {
            $.ajax({
                url: '/checkout/checkout/payment_method',
                type: 'post',
                dataType: 'json',
                data: $('#payment_method_form').serialize(),
                success: function(json) {
                    console.log(json);
                    if (json['error']) {
                        // perfom here all that is occured when there errors
                    }
                    else if (json['redirect'])
                        window.location = json['redirect'];
                }
            });
        });
        
        // Review
        $('#button-confirm').on('click', function() {
            $.ajax({
                url: "/checkout/checkout/confirm",
                type: 'post',
		dataType: 'json',
                success: function(json) {
		    console.log(json);
                    if (json['error']) {
                        // perfom here all that is occured when there errors
                    } else if (json['redirect']) {
                        window.location = json['redirect'];
		    }
                }
            });
        });
        
        $('#content').on('change', '#limit-top, #sortby-top', function () {
            window.location = $(this).val();
        });
	
	// Adjustment for the nav-menu
	ww = document.body.clientWidth;
	adjustMenu();
    
})(jQuery);

var populateZonesByCountry = function(countryid) {
    $('#shipping-information select[name="shipping[zone_id]"]').load('/customer/address/zone/' + countryid);
}

// Responsive adjust menu
var ww = document.body.clientWidth;

$(window).bind('resize orientationchange', function() {
    ww = document.body.clientWidth;
    adjustMenu();
});

function adjustMenu() {    
    if (ww < 768) {
        $('.toggleMenu').css('display', 'inline-block');
        if (!$('.toggleMenu').hasClass('active')) {
	    $('.nav').parent().hide();            
        } else {
            $('.nav').parent().show();
        }
        $('.nav li').unbind('mouseenter mouseleave');            
        $('.nav li a.parent ~ span.touch-button').unbind('click').bind('click', function(e) {
            // must be attached to anchor element to prevent bubbling
            e.preventDefault();
            $(this).parent('li').toggleClass('hover');
        });
    } else if (ww >= 768) {
        $('.toggleMenu').css('display', 'none');
        $('.nav').parent().show();
        $('.nav li').removeClass('hover');
        $('.nav li a').unbind('click');
        $('.nav li').unbind('mouseenter mouseleave').bind('mouseenter mouseleave', function() {
            // must be attached to li so that mouseleave is not triggered when hover over submenu
            $(this).toggleClass('hover');
        });
    }
}

$('.opener').click(function() {

});
