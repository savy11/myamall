<?php

if (isset($_GET['name']) && !empty($_GET['name'])) {
 $path = dirname(__DIR__) . DIRECTORY_SEPARATOR;
 $name = str_replace('-', '_', $_GET['name']);

 $file = $path . $name . '.php';
 if (file_exists($file)) {
  include_once($file);
  exit();
 } else {
  header('HTTP/1.1 404 Not Found');
  include_once('404.php');
  exit();
 }
}

