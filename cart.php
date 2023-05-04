<?php
 require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new controllers\cart;
 if (!$fn->session('cart')) {
  if (!$fn->cart) {
   $fn->redirecting('products');
  }
 }
 include app_path . 'inc' . ds . 'head.php';
 include app_path . 'inc' . ds . 'header.php';
 include app_path . 'inc' . ds . 'breadcrumb.php';
?>
    <div class="container">
        <div class="header-for-light">
            <h1 class="wow fadeInRight animated" data-wow-duration="1s">Shopping<span> Cart</span></h1>
        </div>
        <div id="cart">
         <?php echo include_once app_path . 'views' . ds . 'cart.php'; ?>
        </div>
    </div>
<?php
 ob_start();
?>
    <script type="text/javascript">
        $(function () {
            app.check_input();
        })
    </script>
<?php
 $fn->script = ob_get_clean();
 include_once app_path . 'inc' . ds . 'footer.php';
 include_once app_path . 'inc' . ds . 'foot.php';
?>