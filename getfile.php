<?php
 
 require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new controllers\controller(false, false);
 
 if ($fn->get('sf') == '') {
  return;
 }
 if ($fn->get('name') == '') {
  return;
 }
 
 // Make File Path
 $path = upload_path . $fn->decrypt($fn->get('sf'));
 //$path = ASSETS_PATH . 'images/' . $fn->Decrypt($fn->GETValue('sf'));
 $filename = $fn->get('name');
 $file = $path . $filename;
 $filearr = explode('.', $filename);
 
 if ($fn->get('size') != '') {
  if (file_exists($file)) {
   $size = $_GET['size'];
   $size = str_replace('s', '', $size);
   $newfilename = $filearr[0] . '_';
   list($ow, $oh) = @getimagesize($file);
   $ratio = ($ow / $oh);
   
   if (strpos($size, 'p') !== false) {
    $size = explode('x', str_replace('p', '', $size));
    $w = $size[0];
    $h = $size[1];
    $newfilename .= $w . 'x' . $h;
   } else if (strpos($size, 'w') !== false) {
    $w = str_replace('w', '', $size);
    $h = (int)($w / $ratio);
    $newfilename .= $w . 'x' . $h;
   } else if (strpos($size, 'h') !== false) {
    $h = str_replace('h', '', $size);
    $w = (int)($h * $ratio);
    $newfilename .= $w . 'x' . $h;
   } else if (strpos($size, 'q') !== false) {
    $r = $size = str_replace('q', '', $size);
    $w = $h = $size;
    if ($ow < $r || $oh < $r) {
     $w = $ow;
     $h = $oh;
    } else if ($ow > $oh) {
     $w = (int)($r * $ratio);
    } else if ($ow < $oh) {
     $h = (int)($r / $ratio);
    }
    $newfilename .= $w . 'x' . $h;
   } else {
    $w = $h = $size;
    $newfilename .= $w . 'x' . $h;
   }
   
   $im = new resources\controllers\image_resize;
   
   $newfilename .= '.' . $filearr[1];
   $newfile = $path . $newfilename;
   
   if (!($ow == $w && $oh == $h)) {
    if (file_exists($newfile)) {
     $file = $newfile;
    } else {
     //if ($fn->SERVERValue('HTTP_REFERER') != '' && strpos($fn->SERVERValue('HTTP_REFERER'), APP_URL) !== false) {
     if ($w == $h) {
      if ($ow == $oh) {
       $im->resize_ratio($file, $newfile, $w);
       $file = $newfile;
      } else {
       $tmp = $path . rand(0000000, 9999999) . '_' . $filename;
       $im->resize_ratio($file, $tmp, $w);
       $im->resize_crop_full($tmp, $newfile, $w, $h, 0, 0);
       @unlink($tmp);
       $file = $newfile;
      }
     } else {
      $im->resize_wh($file, $newfile, $w, $h);
      $file = $newfile;
     }
     //}
    }
   }
  }
 }
 
 if (file_exists($file)) {
  $info = pathinfo($file);
  if (stripos($fn->server('HTTP_ACCEPT'), 'image/webp') != false) {
   header('Content-Type: image/webp');
   if (in_array($info['extension'], ['jpg', 'jpeg'])) {
    $img = imagecreatefromjpeg($file);
    imagewebp($img);
    imagedestroy($img);
   } else if (in_array($info['extension'], ['png'])) {
    $img = imagecreatefrompng($file);
    imagepalettetotruecolor($img);
    imagealphablending($img, false);
    imagesavealpha($img, true);
    imagewebp($img);
    imagedestroy($img);
   }
  } else {
   header('Content-Type: ' . $fn->mime_types[$info['extension']]);
   readfile($file);
   exit();
  }
 }
