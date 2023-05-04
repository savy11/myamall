<?php
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';
$fn = new admin\controllers\forgot;
if ($fn->post('btn_forgot') != '') {
 try {
  $fn->check_forgot();
  $fn->session_msg('Verification Code has sent to your email address.', 'success', 'Verification');
  $fn->redirecting('verification');
 } catch (Exception $ex) {
  $fn->session_msg($ex->getMessage(), 'error', 'Forgot Password');
 }
}
$fn->page['name'] = 'Forgot Password';
include 'inc/header_1.php';
?>
<div class="unauth-page">
 <div class="panel panel-default">
  <div class="panel-heading">
   <h2>Forgot Password?</h2>
   <span>Please enter your e-mail address so we can send an email to reset your password.</span>
  </div>
  <div class="panel-body">
   <form id="data-frm" name="data-frm" method="post" class="form-validate" autocomplete="off">
    <div class="form-group">
     <div class="input-group">
      <span class="input-group-addon"><i class="s7-mail"></i></span>
      <input type="email" name="forgot[email]" id="forgot-email" class="form-control input-lg" placeholder="Email Address" value="<?php echo $fn->post('email'); ?>" required />
     </div>
    </div>    
    <div class="form-group">
     <label class="input-label req text-grey">Type the word in the image below:</label>
     <div class="captcha">
      <img src="<?php echo $fn->permalink('captcha') . '?key=' . $fn->encrypt_post_data(array('for' => 'forgot', 'color' => 3)) . '&' . ((float) rand() / (float) getrandmax()); ?>" alt="Captcha" class="captcha-code">
      <p class="text-grey">Can't read the text above? <a class="refresh-captcha" tabindex="-1">Try another text</a></p>
      <div class="input-group">
       <span class="input-group-addon"><i class="s7-key"></i></span>
       <input type="text" name="forgot[captcha]" id="forgot-captcha" class="form-control input-lg" placeholder="Enter above security code" required />
      </div>
     </div>
    </div>
    <div class="form-group login-submit">
     <input type="hidden" name="token" class="form-control" value="<?php echo $fn->post_token(); ?>" />
     <button type="submit" name="btn_forgot" value="Send Email" class="btn btn-secondary btn-block btn-lg">Send Email</button>
    </div>
   </form>
   <div class="text-center out-links">
    <a href="<?php echo $fn->permalink('login'); ?>">Back to Login</a>
   </div>
  </div>
 </div>
</div>
<?php
include 'inc/footer.php';
include 'inc/foot.php';
?>
