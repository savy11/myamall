<?php
require dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'autoload.php';
$fn = new admin\controllers\controller;
$fn->page['name'] = 'Server error!';
include 'header_1.php';
?>
<div class="unauth-page error-page text-center">
 <h1>500<span><i class="s7-settings"></i></span></h1>
 <h5 class="text-uppercase">Server error</h5>
 <div class="error-message">The server encountered an internal error and was unable to complete your request. Either the server is overloaded or there was an error in a CGI script.<br/>If you think this is a server error, please contact the <a href="mailto:webmaster@<?php echo domain; ?>">webmaster</a>.</div>
 <a href="<?php echo $fn->permalink(); ?>" class="btn btn-secondary btn-sm">Go to home page</a>
</div>
<?php
include 'footer.php';
include 'foot.php';
?>
