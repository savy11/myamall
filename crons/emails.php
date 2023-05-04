<?php
 require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new \controllers\crons;
 $fn->send_cron_email();
 echo "Emails Sent";
