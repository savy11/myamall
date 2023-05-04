<?php
 
 @ini_set('zlib.output_compression', 1);
 @ini_set('post_max_size', '20M');
 @ini_set('upload_max_size', '20M');
 define('environment', 'development');
 define('default_timezone', 'Asia/Kolkata');
 
 if (environment) {
  switch (environment) {
   case 'development':
    error_reporting(E_ALL);
    break;
   case 'production':
    error_reporting(0);
    break;
   default:
    exit('The application environment is not set correctly.');
  }
 }
 date_default_timezone_set(default_timezone);
 define('app_name', 'Mya Mall');
 define('app_email', 'info@myamall.com');
 define('ds', '/');
 define('encrypt_key', 'MYA&%$MALL%$#$%%');
 define('cookie_key', 'mml');
 define('cookie_encrypt', true);
 define('date_format', 'Y-m-d');
 define('date_disp_format', 'd M, Y');
 define('time_format', 'H:i:s');
 define('copyright_year', 2017);
 define('domain', $_SERVER['HTTP_HOST']);
 define('local', (domain == 'localhost' || strpos($_SERVER['REMOTE_ADDR'], '192.168') !== false) ? true : false);
 if (local) {
  define('domain_path', ds . 'myamall.com' . ds);
 } else {
  define('domain_path', ds);
 }
 define('request_scheme', 'http' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's' : '') . '://');
 define('app_url', request_scheme . domain . domain_path);
 define('app_path', $_SERVER['DOCUMENT_ROOT'] . domain_path);
 define('admin_url', app_url . 'admin' . ds);
 define('admin_path', app_path . 'admin' . ds);
 define('upload_url', app_url . 'resources' . ds . 'files' . ds);
 define('upload_path', app_path . 'resources' . ds . 'files' . ds);
 
 // Twitter
 define('tw_site', '');
 
 //Paystack
 define('ps_secret_key', 'PAYSTACK_SECRET_KEY');
 define('ps_public_key', 'PAYSTACK_PUBLIC_KEY');

 //UBA
// Live Keys
define('uba_secret_key', 'UBA_SECRET_KEY');
define('uba_public_key', 'UBA_PUBLIC_KEY');
