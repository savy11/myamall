<?php
 require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new \controllers\crons;
 $fn->clear_cart();
 echo 'Cart greater than 30 days is cleared';
