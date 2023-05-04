<?php
 // unset($_SESSION['user'], $_SESSION['ch_step']);
 $str = '';
 ob_start();
?>
    <article class="col-sm-12">
        <div class="box-border block-form wow fadeInLeft" data-wow-duration="1s">
            <!-- Nav tabs -->
            <ul class="nav nav-pills  nav-justified">
                <li<?php echo $fn->session('ch_step') == 1 ? ' class="active"' : ''; ?>>
                    <a href="<?php echo $fn->permalink('checkout'); ?>" data-url="checkout" data-type="ch_step"
                       data-app="<?php echo $fn->encrypt_post_data(['ch_step' => 1]); ?>" data-recid="checkout"<?php
                     echo $fn->validate_login() && $fn->session('ch_step') > 1 ? ' data-ajaxify="true"' : ''; ?>><i
                                class="fa fa-thumb-tack"></i>Delivery Address</a></li>
                <li<?php echo $fn->session('ch_step') == 2 ? ' class="active"' : ''; ?>>
                    <a href="<?php echo $fn->permalink('checkout'); ?>" data-url="checkout" data-type="ch_step"
                       data-app="<?php echo $fn->encrypt_post_data(['ch_step' => 2]); ?>" data-recid="checkout"<?php
                     echo $fn->validate_login() && $fn->session('ch_step') > 2 ? ' data-ajaxify="true"' : ''; ?>><i
                                class="fa fa-check"></i>Order Review</a></li>
                <li<?php echo $fn->session('ch_step') == 3 ? ' class="active"' : ''; ?>>
                    <a href="<?php echo $fn->permalink('checkout'); ?>" data-url="checkout" data-type="ch_step"
                       data-app="<?php echo $fn->encrypt_post_data(['ch_step' => 3]); ?>" data-recid="checkout"<?php
                     echo $fn->validate_login() && $fn->session('ch_step') > 3 ? ' data-ajaxify="true"' : ''; ?>><i
                                class="fa fa-money"></i>Payment Method</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div id="ch_step_1"
                     class="<?php echo($fn->session('ch_step') == 1 && $fn->validate_login() ? '' : ' hide'); ?>">
                    <div>
                        <br>
                        <h3>Delivery Details</h3>
                        <hr>
                     <?php
                      $addresses = [];
                      if ($fn->validate_login()) {
                       $addresses = $fn->get_addresses(true);
                      }
                      if ($addresses) {
                       ?>
                          <div class="row">
                           <?php foreach ($addresses as $k => $v) { ?>
                               <div class="col-sm-6">
                                   <div class="box-border block-form wow fadeInLeft" data-wow-duration="1s">
                                       <div class="panel panel-default">
                                           <div class="panel-heading"><strong
                                                       class="panel-title"><?php echo $v['display_name']; ?></strong>
                                           </div>
                                           <div class="panel-body">
                                               <p><strong><?php echo $v['email']; ?></strong></p>
                                               <p><strong><?php echo $v['mobile_no']; ?></strong></p>
                                               <p> <?php echo $v['address']; ?></p>
                                               <button type="button" class="btn-default-1" data-ajaxify="true"
                                                       data-url="checkout"
                                                       data-action="billing"
                                                       data-app="<?php echo $fn->encrypt_post_data(['address_type' => 0, 'address_id' => $k])
                                                       ?>">Use this address
                                               </button>
                                           </div>
                                       </div>
                                   </div>
                               </div>

                            <?php
                           }
                           ?>
                              <div class="clearfix"></div>
                              <div class="col-sm-12">
                                  <div class="checkbox">
                                      <input type="checkbox" name="addnew" id="addnew" value="1"
                                             onclick="$('#address-frm').toggleClass('hide');"/>
                                      <label for="addnew">I want to add new address</label>
                                  </div>
                              </div>
                          </div>
                       <?php
                      }
                     ?>

                        <form class="form-validate<?php echo $addresses ? ' hide' : ''; ?>" name="address-frm"
                              id="address-frm"
                              data-ajax="true"
                              data-url="checkout" data-action="billing">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">First Name <span
                                                    class="required">*</span></label>
                                        <input type="text" class="form-control border-form-control"
                                               name="bill[first_name]"
                                               id="bill-first-name"
                                               value="<?php echo $fn->user['first_name']; ?>"
                                               placeholder="e.g. John" required/>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Last Name <span
                                                    class="required">*</span></label>
                                        <input type="text" class="form-control border-form-control"
                                               name="bill[last_name]"
                                               id="bill-last-name"
                                               value="<?php echo $fn->user['last_name']; ?>"
                                               placeholder="e.g. Hopkins" required/>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Phone <span class="required">*</span></label>
                                        <input type="text" class="form-control border-form-control"
                                               name="bill[mobile_no]"
                                               id="bill-mobile-no"
                                               value="<?php echo $fn->user['mobile_no']; ?>"
                                               placeholder="123 456 7890" required/>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Email Address <span
                                                    class="required">*</span></label>
                                        <input type="email" class="form-control border-form-control"
                                               name="bill[email]"
                                               id="bill-email"
                                               value="<?php echo $fn->user['email']; ?>"
                                               placeholder="e.g. demo@example.com" required/>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label">Billing Address <span
                                                    class="required">*</span></label>
                                        <textarea class="form-control border-form-control" name="bill[address]"
                                                  id="bill-address" rows="5"
                                                  required><?php echo $fn->session('bill', 'address'); ?></textarea>
                                        <small class="text-danger">Please provide the number and street.</small>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label">City <span class="required">*</span></label>
                                        <input type="text" class="form-control border-form-control"
                                               name="bill[city]"
                                               id="bill-city" value="<?php echo $fn->session('bill', 'city'); ?>"
                                               placeholder="e.g. Abuja" required/>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label">State</label>
                                        <input type="text" class="form-control border-form-control"
                                               name="bill[state]"
                                               id="bill-state" value="<?php echo $fn->session('bill', 'state'); ?>"
                                               placeholder="e.g. Abia"/>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label">Country <span class="required">*</span></label>
                                        <input type="text" class="form-control border-form-control"
                                               name="bill[country]"
                                               id="bill-country"
                                               value="<?php echo $fn->session('bill', 'country'); ?>"
                                               placeholder="e.g. Nigeria" required/>
                                    </div>
                                </div>
                                <div class="col-sm-12 text-right">
                                    <hr/>
                                    <input type="hidden" name="address_type" value="1"/>
                                    <input type="hidden" name="token" value="<?php echo $fn->post_token(); ?>"/>
                                    <button type="submit" name="btn_submit" class="btn-default-1">NEXT
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div id="ch_step_2" <?php echo($fn->session('ch_step') == 2 && $fn->validate_login() ? '' : ' class="hide"'); ?>>
                    <br>
                    <h3>Review</h3>
                    <br>
                    <div class="row">
                        <div class="col-md-12" id="cart">
                         <?php
                          $type = 'overview';
                          $fn->tmp_cart($fn->session('checkout'));
                          echo include_once app_path . 'views' . ds . 'cart.php'; ?>
                        </div>
                    </div>
                </div>
                <div id="ch_step_3"<?php echo($fn->session('ch_step') == 3 && $fn->validate_login() ? ' ' : ' class="hide"'); ?>>
                    <br>
                    <div class="row">
                        <form name="payment-frm" id="payment-frm" class="form-validate" data-ajax="true"
                              data-url="checkout">
                            <?php /*<div class="col-sm-6">
                                <h3>Cash on Delivery</h3>
                                <hr>
                                <p>
                                    You have to be pay when you will receive your items at your doorstep.
                                </p>
                                <div class="radio">
                                    <input type="radio" name="pay_mode" id="pay_delivery" value="delivery"/>
                                    <label for="pay_delivery">Cash on Delivery</label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <h3>Paystack</h3>
                                <hr>
                                <p>
                                    Pay online using paystack with your bank account, credit or debit cards.
                                </p>
                                <div class="radio">
                                    <input type="radio" name="pay_mode" id="pay_paystack" value="paystack"/>
                                    <label for="pay_paystack">Paystack</label>
                                </div>
                            </div>*/?>
                         <div class="col-sm-6">
                                <h3>United Bank for Africa</h3>
                                <hr>
                                <p>
                                    Pay online using uba with your bank account, credit or debit cards.
                                </p>
                                <div class="radio">
                                <input type="radio" name="pay_mode" id="pay_uba" value="uba"/>
                                    <label for="pay_uba">UBA</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <hr>
                                <div class="alert alert-info">By clicking on <b>Pay Now</b> button, the order will be created with the selected cart items and you will be redirected to the payment page. While you're on the payment page please do not press back button.</div>
                                <button type="submit" name="action" value="payment"
                                        class="btn-default-1 pull-right">Pay Now
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </article>
<?php
 $str = preg_replace('/^\s+|\n|\r|\s+$/m', '', ob_get_clean());
 return $str;
