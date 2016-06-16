<form enctype="multipart/form-data" method="post" action="/page/contact">
    <h2><?php echo __('contact.contactform');?></h2>
    <div class="form-group">
        <label for=""><?php echo __('contact.firstname');?></label>
        <input class="form-control" type="text" name="contact[firstname]" value="" placeholder="Firstname" />
    </div>
    <div class="form-group">
        <label for=""><?php echo __('contact.lastname');?></label>
        <input class="form-control" type="text" name="contact[lastname]" value="" placeholder="Lastname" />
    </div>
    <div class="form-group">
        <label for=""><?php echo __('contact.email');?></label>
        <input class="form-control grey validate" type="email" name="contact[email]" value="" placeholder="john@doe.com" />
    </div>
    <div class="form-group">
        <label for="message"><?php echo __('contact.message');?></label>
        <textarea class="form-control" id="message" name="contact[message]" rows="10"></textarea>
    </div>    
    <div class="captcha-block">
        <div>
            <img src="/captcha/index" id="captcha" alt="" />
        </div>
        <div>
            <a href="#" onclick="document.getElementById('captcha').src = '/captcha/index/?' + Math.random(); return false;" id="change-image">Not readable? Change text.</a>
            <input type="text" name="contact[captcha]" id="captcha-form" autocomplete="off" />
        </div>        
    </div>
    <div class="buttons">
        <input type="hidden" name="contact[form_token]" value="<?php echo $this->form_token;?>" />
        <button type="submit" class="btn btn-success"><span><?php echo __('contact.submit');?></span></button>
    </div>
</form>