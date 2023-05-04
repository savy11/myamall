<?php
 require dirname(__FILE__) . DIRECTORY_SEPARATOR . "autoload.php";
 $fn = new controllers\login;
 if ($fn->post('btn_login')) {
  $fn->login_validation();
 }
 include app_path . 'inc' . ds . 'head.php';
 include app_path . 'inc' . ds . 'header.php';
 include app_path . 'inc' . ds . 'breadcrumb.php';
?>
<section>
    <div class="block">
        <div class="container">
            <div class="header-for-light">
                <h1 class="wow fadeInRight animated" data-wow-duration="1s"><span>Login</span> or <span>Register</span>
                </h1>
            </div>
            <div class="row">
                <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <div class="block-form box-border wow fadeInLeft animated" data-wow-duration="1s">
                        <h3><i class="fa fa-unlock"></i>Login</h3>
                        <p>Please login using your existing account</p>
                        <form method="post" name="login-frm" id="login-frm" class="form-validate"
                              autocomplete="off">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="email" class="form-control" placeholder="Email address"
                                           name="login[email]" id="login-email"
                                           value="<?php echo $fn->post('email'); ?>" required/>
                                </div>
                                <div class="col-md-6">
                                    <input type="password" class="form-control" placeholder="Password"
                                           name="login[password]" id="login-passworrd" required/>
                                </div>
                             <?php if ($fn->session('lstep') > 2) { ?>
                                 <div class="col-sm-6 captcha">

                                     <img src="<?php echo $fn->permalink('captcha') . '?key=' . $fn->encrypt_post_data(array('for' => 'login', 'color' => 1)) . '&' . ((float)rand() / (float)getrandmax()); ?>" alt="Captcha" class="captcha-code"/>
                                     <br/>
                                     <a class="btn btn-link refresh-captcha" tabindex="-1">Refresh captcha</a>
                                 </div>
                                 <div class="col-sm-6">
                                     <input type="text" name="login[captcha]" id="login-captcha"
                                            placeholder="Security Code"
                                            class="form-control"
                                            maxlength="6" required/>
                                 </div>
                             <?php } ?>
                                <div class="col-sm-6">
                                    <label class="checkbox">
                                        <input type="checkbox" name="login[remember]" id="login-remember" value="1"/>
                                        <label for="login-remember">Remember me</label>
                                    </label>
                                </div>
                                <div class="col-sm-6">
                                    <a href="<?php echo $fn->permalink('forgot-password'); ?>" class="pull-right">Forgot Password?</a>
                                </div>
                                <div class="col-md-12">
                                    <hr>
                                    <input type="hidden" name="token" value="<?php echo $fn->post_token(); ?>"/>
                                    <input type="submit" name="btn_login" value="Login" class="btn-default-1"/>
                                    <input type="reset" value="Reset" class="btn-default-1"/>
                                </div>
                            </div>
                        </form>
                    </div>
                </article>
                <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <div class="block-form box-border wow fadeInRight animated" data-wow-duration="1s">
                        <h3><i class="fa fa-pencil"></i>Create new account</h3>
                        <p>Registration allows you to avoid filling in billing and shipping forms every time you
                            checkout on this website.</p>
                        <hr>
                        <a href="<?php echo $fn->permalink('register'); ?>" class="btn-default-1">Register</a>
                    </div>
                </article>
            </div>
        </div>
    </div>
</section>
<?php
 include app_path . 'inc' . ds . 'footer.php';
 include app_path . 'inc' . ds . 'foot.php';
?>
	