<div class="col-md-6">
    <form id="form_login" method="post" action="<?php echo CURRENT_URL;?>" novalidate="novalidate">
        <div style="display:none" id="allErrors" class="msg_box msg_error"></div>
        <div class="rowA input form-group">
            <label class="lbl" for="username">Username:</label>
            <div class="input-group">
                <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                <input type="text" class="form-control" name="username" id="username" autocomplete="off">
            </div>
        </div>
        <div class="rowB input form-group">
            <label class="lbl" for="password">Password:</label>
            <div class="input-group">
                <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
                <input type="password" class="form-control" name="password" id="password" autocomplete="off">
            </div>
        </div>
        <div class="checkbox">
            <label for="remember_me">
                <input type="checkbox" name="remember_me" id="remember_me" value="yes"> Remember me ?
            </label>
        </div>
        <button type="submit" class="btn"><span class="glyphicon glyphicon-off"></span> Login</button>
    </form>
</div>