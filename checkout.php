<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autoload.php';
$fn = new controllers\checkout;
if (!$fn->session('checkout')) {
    $fn->redirecting('cart');
}
$fn->checkout_step();
$fn->cms_page('checkout');
include app_path . 'inc' . ds . 'head.php';
include app_path . 'inc' . ds . 'header.php';
include app_path . 'inc' . ds . 'breadcrumb.php';
?>
    <div class="container">
        <div class="row" id="checkout">
            <?php echo include app_path . 'views' . ds . 'checkout.php'; ?>
        </div>
    </div>
<?php
include 'inc' . ds . 'footer.php';
include 'inc' . ds . 'foot.php';
?>