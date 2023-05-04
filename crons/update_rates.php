<?php
 require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new \controllers\crons;
 $fn->update_exchange_rates();
 echo "Exchange Rates Updated";
