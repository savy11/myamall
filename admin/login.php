<?php
 require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new admin\controllers\login;
 if ($fn->post('btn_login') != '') {
  $fn->login_validation();
 }
 $fn->page['name'] = 'Login';
 include 'inc/header_1.php';
?>
<div class="unauth-page">
 <div class="panel panel-default">
  <div class="panel-heading">
   <h2><?php echo app_name; ?></h2>
   <span>Please enter login credentials.</span>
  </div>
  <div class="panel-body">
   <form id="data-frm" name="data-frm" method="post" class="form-validate" autocomplete="off">
    <div class="form-group">
     <div class="input-group">
      <span class="input-group-addon"><i class="s7-user"></i></span>
      <input type="text" name="login[email]" id="login-email" class="form-control input-lg" placeholder="Email Address / Username" value="<?php echo $fn->post('email'); ?>" required />
     </div>
    </div>
    <div class="form-group">
     <div class="input-group">
      <span class="input-group-addon"><i class="s7-lock"></i></span>
      <input type="password" name="login[password]" id="login-password" class="form-control input-lg" placeholder="Password" required />
     </div>
    </div>
    <?php if ($fn->session('lstep') >= 2) { ?>
      <div class="form-group">
       <label class="input-label req text-grey">Type the word in the image below:</label>
       <div class="captcha">
        <img src="<?php echo $fn->permalink('captcha') . '?key=' . $fn->encrypt_post_data(array('for' => 'login', 'color' => 3)) . '&' . ((float) rand() / (float) getrandmax()); ?>" alt="Captcha" class="captcha-code">
        <p class="text-grey">Can't read the text above? <a class="refresh-captcha" tabindex="-1">Try another text</a></p>
        <div class="input-group">
         <span class="input-group-addon"><i class="s7-key"></i></span>
         <input type="text" name="login[captcha]" id="login-captcha" class="form-control input-lg" placeholder="Enter above security code" required />
        </div>
       </div>
      </div>
     <?php } ?>
    <div class="form-group">
     <div class="checkbox">
      <input type="checkbox" name="login[remember]" id="login-remember" value="1" checked="checked" />
      <label for="login-remember">Remember Me</label>
     </div>
    </div>
    <div class="form-group login-submit">
     <input type="hidden" name="token" class="form-control" value="<?php echo $fn->post_token(); ?>" />
     <button type="submit" name="btn_login" value="Log In" class="btn btn-secondary btn-block btn-lg">Log In</button>
    </div>
   </form>
   <div class="text-center out-links">
    <a href="<?php echo $fn->permalink('forgot'); ?>">Forget your password?</a>
   </div>
  </div>
 </div>
</div>
<?php
 include 'inc/footer.php';
 include 'inc/foot.php';
?>
