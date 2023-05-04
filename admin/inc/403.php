<?php
require dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'autoload.php';
$fn = new admin\controllers\controller;
$fn->page['name'] = '403 Forbidden';
include 'header_1.php';
?>
<div class="unauth-page error-page text-center">
 <h1>403<span><i class="s7-settings"></i></span></h1>
 <h5 class="text-uppercase">Forbidden</h5>
 <div class="error-message">You don't have permission to access <?php echo $fn->server('REDIRECT_URL'); ?> on this server.</div>
 <a href="<?php echo $fn->permalink(); ?>" class="btn btn-secondary btn-sm">Go to home page</a>
</div>
<?php
include 'footer.php';
include 'foot.php';
?>
