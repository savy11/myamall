<section>
    <div class="color-scheme-white-90">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <article class="payment-service">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <i class="fa fa-thumbs-up"></i>
                                <h3 class="color-active">Safe Payments</h3>
                                <p>We offer a secure online payment system, quick and reliable.</p>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="payment-service">
                        <a href="<?php echo $fn->permalink('free-shipping'); ?>"></a>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <i class="fa fa-truck"></i>
                                <h3 class="color-active">Free shipping</h3>
                                <p>Our shipping process is swift and hassel free. Our goods are carefully and professionally packaged.</p>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="payment-service">
                        <a href="<?php echo $fn->permalink('support'); ?>"></a>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <i class="fa fa-phone"></i>
                                <h3 class="color-active">24/7 Support</h3>
                                <p>We are available to respond to all inquiries. Contact us now.</p>
                            </div>
                        </div>
                    </article>
                </div>
            </div>


        </div>
    </div>
</section>

<footer id="footer-block">
    <div class="footer-information">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="header-footer">
                        <h3>Quick Links</h3>
                    </div>
                    <ul class="footer-categories list-unstyled">
                     <?php
                      $quick_links = $fn->get_quick_links(8);
                      if ($quick_links) {
                       foreach ($quick_links as $l) {
                        ?>
                           <li>
                               <a href="<?php echo $fn->permalink($l['page_url']); ?>"><?php echo $l['page_title'];
                                ?></a>
                           </li>
                        <?php
                       }
                      } ?>
                    </ul>
                </div>
                <div class="col-md-3">
                    <div class="header-footer">
                        <h3>My Account</h3>
                    </div>
                    <ul class="footer-categories list-unstyled">
                     <?php if ($fn->validate_login()) { ?>
                         <li><a href="<?php echo $fn->permalink('account'); ?>">My Account</a></li>
                         <li><a href="<?php echo $fn->permalink('account/addresses'); ?>">My Addresses</a></li>
                         <li><a href="<?php echo $fn->permalink('account/orders'); ?>">My Orders</a></li>
                         <li><a href="<?php echo $fn->permalink('account/change-password'); ?>">Change Password</a></li>
                         <li><a href="<?php echo $fn->permalink('logout'); ?>">Logout</a></li>
                     <?php } else { ?>
                         <li><a href="<?php echo $fn->permalink('login'); ?>">Login</a></li>
                         <li><a href="<?php echo $fn->permalink('register'); ?>">Register</a></li>
                         <li><a href="<?php echo $fn->permalink('forgot-password'); ?>">Forgot Password</a></li>
                     <?php } ?>
                        <li><a href="<?php echo $fn->permalink('cart'); ?>">Cart</a></li>
                        <li><a href="<?php echo $fn->permalink('checkout'); ?>">Checkout</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <div class="header-footer">
                        <h3>Enquiries</h3>
                    </div>
                    <p>
                        You can post enquiry after filling form:
                    </p>
                    <div class="want">
                        <form class="form-horizontal form-validate" name="quote-frm" id="quote-frm" method="post"
                              autocomplete="off"
                              data-ajax="true" data-url="quote" data-action="send_quote">
                            <textarea class="form-control" name="quote" id="quote" placeholder="I want ..."
                                      required></textarea>
                            <input type="hidden" name="token" value="<?php echo $fn->post_token(); ?>"/>
                            <button type="submit" name="btn_quote" value="Send Quote"> Send us <i class="fa
                            fa-angle-right"></i></button>
                        </form>
                    </div>


                </div>
                <div class="col-md-3">
                    <div class="header-footer">
                        <h3>Get In Touch</h3>
                    </div>
                    <p>
                        <strong>Phone: <?php echo $fn->varv('phone_no', $fn->company); ?></strong><br>
                        <strong>Email:</strong> <?php echo $fn->varv('company', $fn->company['email']); ?></p>
                    <p><strong><?php echo $fn->company['name']; ?></strong><br> <?php echo $fn->company['address'];
                     ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-copy color-scheme-1">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <a href="<?php echo $fn->permalink(); ?>" class="logo-copy"></a>
                </div>
                <div class="col-md-4">
                    <p class="text-center">
                        Copyright &copy; <?php echo date('Y') . ' ' . app_name; ?> | All rights reserved.<br>
                        Designed by <a href="https://www.xamaranoict.com" target="_blank">Xamaranoict</a>
                    </p>
                </div>
                <div class="col-md-4">
                    <div class="footer-payments pull-right">
                        <img src="<?php echo $fn->permalink('assets/img/payment-icons.png'); ?>" alt="Payment Icons"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<div class="modal fade" id="modal" tabindex="-1" role="dialog" data-keyboard="false"></div>