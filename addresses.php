<?php
 require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new controllers\account;
 if ($fn->get('for') != 'account') {
  $fn->not_found();
 }
 $fn->require_login();
 $fn->cms_page('addresses');
 if ($fn->post('btn_update') == 'update') {
  try {
   $fn->edit_address();
   $fn->session_msg('Address has been updated successfully!', 'success', $fn->cms['page_title']);
   $fn->redirecting('account/addresses');
  } catch (Exception $ex) {
   $fn->session_msg($fn->replace_sql($ex->getMessage()), 'error', $fn->cms['page_title']);
  }
 }
 
 if ($fn->get('action') == 'delete') {
  try {
   $fn->delete_address();
   $fn->session_msg('Address has been deleted successfully!', 'success', $fn->cms['page_title']);
  } catch (Exception $ex) {
   $fn->session_msg($fn->replace_sql($ex->getMessage()), 'error', $fn->cms['page_title']);
  }
  $fn->redirecting('account/addresses');
 }
 
 include app_path . 'inc' . ds . 'head.php';
 include app_path . 'inc' . ds . 'header.php';
 $breadcrumb = array('Account' => $fn->permalink('account'));
 include app_path . 'inc' . ds . 'breadcrumb.php';
?>
    <div class="container">
        <div class="block-form box-border wow fadeInLeft animated" data-wow-duration="1s">
            <div class="row">
             
             <?php $address = $fn->get_addresses(true); ?>
                <div class="col-md-12">
                    <div class="header-for-light">
                        <h1 class="wow fadeInRight animated" data-wow-duration="1s">My <span>Addresses</span></h1>
                    </div>
                 <?php
                  if ($fn->get('action') == 'edit' && $fn->get('id') != '') {
                   $fn->get_address();
                   ?>
                      <form class="form-validate" name="address-frm" id="address-frm" method="post" autocomplete="off">
                          <div class="row">
                              <div class="col-sm-6">
                                  <div class="form-group">
                                      <label class="control-label">First Name <span class="required">*</span></label>
                                      <input type="text" class="form-control border-form-control" name="edit[first_name]" id="edit-first-name" value="<?php echo $fn->post('first_name'); ?>" placeholder="e.g. John" required/>
                                  </div>
                              </div>
                              <div class="col-sm-6">
                                  <div class="form-group">
                                      <label class="control-label">Last Name <span class="required">*</span></label>
                                      <input type="text" class="form-control border-form-control" name="edit[last_name]" id="edit-last-name" value="<?php echo $fn->post('last_name'); ?>" placeholder="e.g. Hopkins" required/>
                                  </div>
                              </div>
                              <div class="col-sm-6">
                                  <div class="form-group">
                                      <label class="control-label">Phone <span class="required">*</span></label>
                                      <input type="text" class="form-control border-form-control" name="edit[mobile_no]" id="edit-mobile-no" value="<?php echo $fn->post('mobile_no'); ?>" placeholder="123 456 7890" required/>
                                  </div>
                              </div>
                              <div class="col-sm-6">
                                  <div class="form-group">
                                      <label class="control-label">Email Address <span class="required">*</span></label>
                                      <input type="email" class="form-control border-form-control" name="edit[email]" id="edit-email" value="<?php echo $fn->post('email'); ?>" placeholder="e.g. demo@example.com" required/>
                                  </div>
                              </div>
                              <div class="col-sm-12">
                                  <div class="form-group">
                                      <label class="control-label">Billing Address <span class="required">*</span></label>
                                      <textarea class="form-control border-form-control" name="edit[address]" id="edit-address" rows="5" required><?php echo $fn->post('address'); ?></textarea>
                                      <small class="text-danger">Please provide the number and street.</small>
                                  </div>
                              </div>
                              <div class="col-sm-4">
                                  <div class="form-group">
                                      <label class="control-label">City <span class="required">*</span></label>
                                      <input type="text" class="form-control border-form-control" name="edit[city]" id="edit-city" value="<?php echo $fn->post('city'); ?>" placeholder="e.g. Abuja" required/>
                                  </div>
                              </div>
                              <div class="col-sm-4">
                                  <div class="form-group">
                                      <label class="control-label">State</label>
                                      <input type="text" class="form-control border-form-control" name="edit[state]" id="edit-state" value="<?php echo $fn->post('state'); ?>" placeholder="e.g. Abia"/>
                                  </div>
                              </div>
                              <div class="col-sm-4">
                                  <div class="form-group">
                                      <label class="control-label">Country <span class="required">*</span></label>
                                      <input type="text" class="form-control border-form-control" name="edit[country]" id="edit-country" value="<?php echo $fn->post('country'); ?>" placeholder="e.g. Nigeria" required/>
                                  </div>
                              </div>
                              <div class="text-right">
                                  <input type="hidden" name="token" value="<?php echo $fn->post_token(); ?>"/>
                                  <button type="submit" name="btn_update" value="update" class="btn-default-1">Update</button>
                              </div>
                          </div>
                      </form>
                   <?php
                  } else {
                   if ($address) { ?>
                       <div class="table-responsive">
                           <table class="table table-striped table-hover">
                               <thead>
                               <th width="5%" class="text-center">#</th>
                               <th>Name</th>
                               <th>Email</th>
                               <th>Mobile No.</th>
                               <th>Address</th>
                               <th>Action</th>
                               </thead>
                               <tbody>
                               <?php
                                $srno = 0;
                                foreach ($address as $k => $v) {
                                 ?>
                                    <tr>
                                        <td align="center"><?php echo ++$srno; ?></td>
                                        <td><?php echo $v['display_name']; ?></td>
                                        <td><?php echo $v['email']; ?></td>
                                        <td><?php echo $v['mobile_no']; ?></td>
                                        <td><?php echo $v['address']; ?></td>
                                        <td>
                                            <a href="<?php echo $fn->permalink('account/addresses') . '?action=edit&id=' . $k; ?>"
                                               class="btn btn-warning btn-sm">Edit</a>
                                         <?php /*<a href="<?php echo $fn->permalink('account/addresses') . '?action=delete&id=' . $k; ?>"
                                               class="btn btn-danger btn-sm">Delete</a>*/ ?>
                                        </td>
                                    </tr>
                                 <?php
                                } ?>
                               </tbody>
                           </table>
                       </div>
                   <?php } else {
                    ?>
                       <div class="alert alert-danger">No address found.</div>
                    <?php
                   }
                  } ?>
                </div>
            </div>
        </div>
    </div>
<?php
 include 'inc' . ds . 'footer.php';
 include 'inc' . ds . 'foot.php';
?>