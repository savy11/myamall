<?php

 require dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new admin\controllers\controller(false, false);

 $expires_offset = 31536000;
 $compress = 1;

 $scripts = array(
     'vendor' => array('jquery/jquery.min.js', 'bootstrap/popper.js', 'bootstrap/bootstrap.min.js', 'moment/moment.js', 'custom-file/jquery.custom-file.js', 'common.js'),
     'js' => array('main.js', 'custom.js')
 );

 $out = 'var hostname = \'' . admin_url . '\', root = \'' . app_url . '\', token = \'' . $fn->get_token() . '\';' . "\n";
 foreach ($scripts as $k => $v) {
  foreach ($v as $f) {
   if ($k == 'vendor') {
    $path = app_path . 'resources' . ds . 'vendor' . ds . $f;
   } else {
    $path = admin_path . 'assets' . ds . $k . ds . $f;
   }
   $out .= $fn->get_file_data($path) . "\n";
  }
 }

 header('Content-Type: application/javascript; charset=UTF-8');
 header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires_offset) . ' GMT');
 header('Cache-Control: public, max-age=' . $expires_offset);

 if ($compress && !ini_get('zlib.output_compression') && 'ob_gzhandler' != ini_get('output_handler') && $fn->server('HTTP_ACCEPT_ENCODING')) {
  header('Vary: Accept-Encoding');
  if (false !== stripos($fn->server('HTTP_ACCEPT_ENCODING'), 'deflate') && function_exists('gzdeflate') && !$force_gzip) {
   header('Content-Encoding: deflate');
   $out = gzdeflate($out, 3);
  } elseif (false !== stripos($fn->server('HTTP_ACCEPT_ENCODING'), 'gzip') && function_exists('gzencode')) {
   header('Content-Encoding: gzip');
   $out = gzencode($out, 3);
  }
 }

 echo $out;
 exit;
 