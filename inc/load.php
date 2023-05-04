<?php

 if (isset($_GET['page_url']) && !empty($_GET['page_url'])) {
  $path = dirname(__DIR__) . DIRECTORY_SEPARATOR;
  $name = $_GET['page_url'];
  $filename = str_replace('-', '_', $name);

  if (strpos($name, '_') !== false) {
   $url = str_replace($name, str_replace('_', '-', $name), $_SERVER['REQUEST_URI']);
   header('Location:' . $url);
   exit();
  }

  $file = $path . $filename . '.php';
  if (file_exists($file)) {
   include_once($file);
   exit();
  } else {
   include_once($path . 'pages.php');
   exit();
  }
 }
