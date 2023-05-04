<?php
require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'autoload.php';
exit();
$fn = new admin\controllers\controller;
$fn->page['name'] = '404 Not Found';
include 'header_1.php';
?>
<div class="unauth-page error-page text-center">
 <h1>404<span><i class="s7-settings"></i></span></h1>
 <h5 class="text-uppercase">Page not found</h5>
 <div class="error-message">The requested URL <?php echo $fn->server('REDIRECT_URL'); ?> was not found on this server.</div>
 <a href="<?php echo $fn->permalink(); ?>" class="btn btn-secondary btn-sm">Go to home page</a>
</div>
<?php
include 'footer.php';
include 'foot.php';
?>
