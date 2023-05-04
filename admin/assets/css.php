<?php

require dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'autoload.php';
$fn = new admin\controllers\controller(true, false);

$expires_offset = 31536000;
$compress = 1;
$styles = array(
 'vendor' => array('bootstrap/bootstrap.min.css', 'custom-file/custom-file.css', 'uploader/uploader.css'),
 'css' => array('icons.css', 'themify-icons.css', 'main.css')
);

$out = '';
foreach ($styles as $k => $v) {
 foreach ($v as $f) {
  $vendor_url = '';
  if ($k == 'vendor') {
   $p = explode('/', $f);
   unset($p[count($p) - 1]);
   $p = implode('/', $p);
   $vendor_url = app_url . 'resources' . ds . 'vendor' . ds . $p . ds;
   $path = app_path . 'resources' . ds . 'vendor' . ds . $f;
  } else {
   $path = admin_path . 'assets' . ds . $k . ds . $f;
  }
  $out .= str_replace('#vendor_url#', $vendor_url, $fn->get_file_data($path));
  $out .= "\n";
 }
}

header('Content-Type: text/css; charset=UTF-8');
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
