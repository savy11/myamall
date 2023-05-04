<?php
 require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new controllers\account;
 $fn->require_login();
 $fn->cms_page('account');
 if ($fn->post('btn_update') != '') {
  try {
   $fn->update_details();
   $fn->session_msg('Your changes has been saved successfully!', 'success', $fn->cms['page_title']);
  } catch (Exception $ex) {
   $fn->session_msg($fn->replace_sql($ex->getMessage()), 'error', $fn->cms['page_title']);
  }
  $fn->redirecting('account');
 }
 $fn->auth();
 include app_path . 'inc' . ds . 'head.php';
 include app_path . 'inc' . ds . 'header.php';
 include app_path . 'inc' . ds . 'breadcrumb.php';
?>
    <div class="container">
        <div class="block-form box-border wow fadeInLeft animated" data-wow-duration="1s">
            <div class="row">
             <?php if ($fn->get('action') == 'edit') { ?>
                 <div class="header-for-light">
                     <h1 class="wow fadeInRight animated" data-wow-duration="1s">Edit <span>Profile</span></h1>
                 </div>
                 <form method="post" class="form-validate" id="update-frm" name="update-frm" autocomplete="off">
                     <div class="row">
                         <div class="col-sm-6">
                             <div class="form-group">
                                 <label class="control-label">First Name <span class="required">*</span></label>
                                 <input class="form-control" name="update[first_name]" value="<?php echo $fn->user['first_name'] ?>" type="text" required/>
                             </div>
                         </div>
                         <div class="col-sm-6">
                             <div class="form-group">
                                 <label class="control-label">Last Name <span class="required">*</span></label>
                                 <input class="form-control" name="update[last_name]" value="<?php echo $fn->user['last_name']; ?>" type="text" required/>
                             </div>
                         </div>
                         <div class="col-sm-6">
                             <div class="form-group">
                                 <label class="control-label">Phone <span class="required">*</span></label>
                                 <input class="form-control" name="update[mobile_no]" value="<?php echo $fn->user['mobile_no']; ?>" placeholder="123 456 7890" type="text" required/>
                             </div>
                         </div>
                         <div class="col-sm-6">
                             <div class="form-group">
                                 <label class="control-label">Email Address <span class="required">*</span></label>
                                 <input class="form-control " name="update[email]" value="<?php echo $fn->user['email']; ?>" disabled type="email" required/>
                             </div>
                         </div>
                         <div class="text-right">
                             <input type="hidden" name="token" value="<?php echo $fn->post_token(); ?>"/>
                             <a href="<?php echo $fn->permalink('account'); ?>" class="btn-default-1">Cancel</a>
                             <button type="submit" name="btn_update" value="Save" class="btn-default-1">Save Changes
                             </button>
                         </div>
                     </div>
                 </form>
             <?php } else { ?>
                <div class="header-for-light">
                    <h1 class="wow fadeInRight animated" data-wow-duration="1s">My <span>Profile</span></h1>
                </div>
                <h4>Hello <span class="color-active"><?php echo $fn->user['display_name']; ?></span> (<a href="mailto:<?php echo $fn->user['email']; ?>"><?php echo $fn->user['email']; ?></a>)</h4>
                <div class="clearfix"></div>
                <ul class="list-unstyled list-inline">
                    <li>
                        <a href="<?php echo $fn->permalink('account?action=edit'); ?>" class="btn-default-1">Edit Profile</a>
                    </li>
                    <li>
                        <a href="<?php echo $fn->permalink('account/addresses'); ?>" class="btn-default-1">My Addresses</a>
                    </li>
                    <li>
                        <a href="<?php echo $fn->permalink('account/orders'); ?>" class="btn-default-1">My Orders</a>
                    </li>
                    <li>
                        <a href="<?php echo $fn->permalink('account/change-password'); ?>" class="btn-default-1">Change Password</a>
                    </li>
                    <li>
                        <a href="<?php echo $fn->permalink('logout'); ?>" class="btn-default-1">Logout</a>
                    </li>
                </ul>
            </div>
         <?php } ?>
        </div>
    </div>
    </div>
<?php
 include app_path . 'inc' . ds . 'footer.php';
 include app_path . 'inc' . ds . 'foot.php';
?>