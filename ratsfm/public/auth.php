<form class="form-signin" id="loginForm" onkeydown="key_return_click('btn-signin')">
    <div class="text-center mb-4">
        <img class="mb-4" src="<?php echo $_STATIC_URI; ?>/img/bootstrap-solid.svg" alt="" width="72" height="72">
        <h1 class="h3 mb-3 font-weight-normal"><?php echo $_SITE_NAME; ?></h1>
        <p>This is the description</p>
        <div class="alert alert-warning text-left fade show" style="display: none" role="alert">
            <span id="alertText"></span>
        </div>
    </div>

    <div class="form-label-group">
        <input type="text" id="inputUsername" class="form-control" name="username" placeholder="Username" required autofocus>
        <label for="inputUsername">Username</label>
    </div>

    <div class="form-label-group">
        <input type="password" id="inputPassword" class="form-control" name="password" placeholder="Password" required>
        <label for="inputPassword">Password</label>
    </div>

    <div class="checkbox mb-3">
        <label>
            <input type="checkbox" value="remember-me"> Remember me
        </label>
    </div>
    <label><input type="hidden" name="action" value="auth"></label>
    <button class="btn btn-lg btn-primary btn-block" id="btn-signin" type="button" onclick="user_auth(this.form)">Sign in</button>
    <p class="mt-5 mb-3 text-muted text-center">&copy; 2017-2018 <?php echo $_SITE_NAME; ?></p>
</form>
