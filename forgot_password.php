<?php
 require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new controllers\forgot_password;
 $fn->cms_page('forgot-password');
 $fn->cms['page_title'] = $fn->page['name'];
 if ($fn->post('btn_forgot') != '') {
  try {
   $fn->check_forgot();
   $fn->session_msg('Reset link has sent to your email address.', 'success', $fn->page['name']);
   $fn->redirecting('forgot-password');
  } catch (Exception $ex) {
   $fn->session_msg($ex->getMessage(), 'error', $fn->page['name']);
  }
 }
 include app_path . 'inc' . ds . 'head.php';
 include app_path . 'inc' . ds . 'header.php';
 include app_path . 'inc' . ds . 'breadcrumb.php';
?>
    <div class="container">
        <div class="block-form box-border wow fadeInLeft animated" data-wow-duration="1s">
            <div class="row">
                <div class="header-for-light">
                    <h1 class="wow fadeInRight animated" data-wow-duration="1s">Forgot <span>Password</span></h1>
                    <p>If you forgot your password then enter your email click on submit button.We will send
                        conformation after that you will be able to reset your password by clicking on the reset link
                        received in your email.</p>
                </div>
                <form class="form-validate" id="forgot-frm" name="forgot-frm" method="post" autocomplete="off">
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label class="control-label">Enter Email <span class="required">*</span></label>
                            <input type="email" class="form-control" name="forgot[email]" id="forgot-email"
                                   placeholder="e.g. demo@example.com" value="<?php echo $fn->post('email'); ?>"
                                   required/>
                        </div>
                        <div class="col-sm-6 captcha">
                            <img src="<?php echo $fn->permalink('captcha') . '?key=' . $fn->encrypt_post_data(array('for' => 'forgot', 'color' => 1)) . '&' . ((float)rand() / (float)getrandmax()); ?>" alt="Captcha" class="captcha-code" />
                            <br/>
                            <a class="btn-link float-right refresh-captcha" tabindex="-1">Refresh Captcha</a>
                        </div>
                        <div class="col-sm-6 form-group">
                            <input type="text" name="forgot[captcha]" id="forgot-captcha"
                                   class="form-control" placeholder="Security code" maxlength="6"
                                   required/>
                        </div>
                        <input type="hidden" name="token" value="<?php echo $fn->post_token(); ?>"/>
                        <button type="submit" name="btn_forgot" value="Submit" class="btn-default-1 pull-right">
                            Submit and Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php
 include app_path . 'inc' . ds . 'footer.php';
 include app_path . 'inc' . ds . 'foot.php';
?>