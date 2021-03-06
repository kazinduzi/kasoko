<?php 
switch ($step) {
case 'checkout':
$step1 = ' active';
$step2 = ' disabled';
$step3 = ' disabled';
$step4 = ' disabled';
$step5 = ' disabled';        
break;
case 'billing':
case 'shipping':
$step1 = '';
$step2 = ' active';
$step3 = ' disabled';
$step4 = ' disabled';
$step5 = ' disabled';        
break;
case 'shipping_method':
$step1 = '';
$step2 = '';
$step3 = ' active';
$step4 = ' disabled';
$step5 = ' disabled';        
break;
case 'payment':
$step1 = '';
$step2 = '';
$step3 = '';
$step4 = '';
$step5 = ' active';        
break;
case 'review':
$step1 = '';
$step2 = '';
$step3 = '';
$step4 = '';
$step5 = ' active';        
break;
default: break;
}
?>

<div class="btn-group checkout-progress">
    <a href="/checkout/checkout/index" role="button" class="btn btn-step<?php echo $step1;?>"><span
			class="step-nb">1</span> <span class="step-label"><?php echo __("Checkout");?></span></a>
    <a href="/checkout/checkout/shipping_information" role="button" class="btn btn-step<?php echo $step2;?>"><span
			class="step-nb">2</span> <span class="step-label"><?php echo __("Billing and Shipping");?></span></a>
    <a href="/checkout/checkout/shipping_method" role="button" class="btn btn-step<?php echo $step3;?>"><span
			class="step-nb">3</span> <span class="step-label"><?php echo __("Shipping Method");?></span></a>
    <a href="/checkout/checkout/payment_method" role="button" class="btn btn-step<?php echo $step4;?>"><span
			class="step-nb">4</span> <span class="step-label"><?php echo __("Payment Info");?></span></a>
    <a href="/checkout/checkout/review" role="button" class="btn btn-step<?php echo $step5;?>"><span
			class="step-nb">5</span> <span class="step-label"><?php echo __("Order Review");?></span></a>
</div>