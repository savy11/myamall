<?php
 
 if (version_compare(PHP_VERSION, '5.4.0', '<')) {
  throw new Exception('The Facebook SDK requires PHP version 5.4 or higher.');
 }
 
 spl_autoload_register(function ($class) {
  
  // Project Specific namespace prefix
  $prefix = '';
  
  
  // Base directory for the namespace prefix
  $base_dir = __DIR__ . '/';
  
  
  // Does the class use the namespace prefix?
  $len = strlen($prefix);
  
  // Get the relative class name
  $relative_class = substr($class, $len);

// replace the namespace prefix with the base directory, replace namespace
  // separators with directory separators in the relative class name, append
  // with .php
  $file = rtrim($base_dir, '/') . '/' . str_replace('\\', '/', $relative_class) . '.php';
  
  if (file_exists($file)) {
   require $file;
  }
 });
