<?php
require dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'autoload.php';
$fn = new admin\controllers\controller;
$fn->page['name'] = 'Unauthorized';
include 'header_1.php';
?>
<div class="unauth-page error-page text-center">
 <h1>401<span><i class="s7-settings"></i></span></h1>
 <h5 class="text-uppercase">Unauthorized</h5>
 <div class="error-message">You have attempted to access a page that you are not authorized to view. If you have any questions please contact the site administrator.</div>
 <a href="<?php echo $fn->permalink(); ?>" class="btn btn-secondary btn-sm">Go to home page</a>
</div>
<?php
include 'footer.php';
include 'foot.php';
?>
