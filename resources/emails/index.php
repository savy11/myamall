<?php
  header('HTTP/1.1 404 Not Found');
  include_once( $_SERVER['DOCUMENT_ROOT']. '/404.php');
  exit;