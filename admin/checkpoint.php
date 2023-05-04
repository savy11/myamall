<?php
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';
$fn = new admin\controllers\checkpoint;
if ($fn->post('btn_submit') != '') {
 $fn->checkpoint_validation();
}
$fn->page['name'] = 'Checkpoint';
include 'inc/header_1.php';
?>
<div class="unauth-page">
 <div class="panel panel-default">
  <div class="panel-heading">
   <h2>Verify User Accessibility</h2>
   <span>You need to enter 6-digit verification code when anyone tries to access your account from a new device or browser. Select <strong>"Remember me"</strong> if you don't want this again on this browser. Code expire after <?php echo $fn->expiry_mins; ?> mintues.</span>
  </div>
  <div class="panel-body">
   <form id="data-frm" name="data-frm" method="post" class="form-validate" autocomplete="off">
    <div class="form-group">
     <div class="input-group">
      <span class="input-group-addon"><i class="s7-key"></i></span>
      <input type="number" name="checkpoint[code]" id="checkpoint-code" class="form-control input-lg" placeholder="Verification Code" required />
     </div>
    </div>    
    <?php if ($fn->session('cstep') >= 2) { ?>
     <div class="form-group">
      <label class="input-label req text-grey">Type the word in the image below:</label>
      <div class="captcha">
       <img src="<?php echo $fn->permalink('captcha') . '?key=' . $fn->encrypt_post_data(array('for' => 'checkpoint', 'color' => 3)) . '&' . ((float) rand() / (float) getrandmax()); ?>" alt="Captcha" class="captcha-code">
       <p class="text-grey">Can't read the text above? <a class="refresh-captcha" tabindex="-1">Try another text</a></p>
       <div class="input-group">
        <span class="input-group-addon"><i class="s7-key"></i></span>
        <input type="text" name="checkpoint[captcha]" id="checkpoint-captcha" class="form-control input-lg" placeholder="Enter above security code" required />
       </div>
      </div>
     </div>
    <?php } ?>
    <div class="form-group">
     <div class="checkbox">
      <input type="checkbox" name="checkpoint[remember]" id="checkpoint-remember" value="1" checked />
      <label for="checkpoint-remember">Remember Me</label>
     </div>
    </div>
    <div class="form-group login-submit">
     <input type="hidden" name="token" class="form-control" value="<?php echo $fn->post_token(); ?>" />
     <button type="submit" name="btn_submit" value="Submit" class="btn btn-secondary btn-block btn-lg">Submit</button>
    </div>
   </form>
   <div class="text-center">
    <span class="text-grey">If you don't want to proceed this verification then <a href="<?php echo $fn->permalink('logout'); ?>" tabindex="-1">click here</a> to cancel this.</span>
   </div>
  </div>
 </div>
</div>
<?php
include 'inc/footer.php';
include 'inc/foot.php';
?>
