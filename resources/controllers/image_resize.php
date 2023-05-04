<?php

namespace resources\controllers;

class image_resize {

 var $image;
 var $image_type;

 function ratio($req_w, $req_h) {
  $w1 = $this->get_width();
  $h1 = $this->get_height();
  $r1 = $w1 / $h1;
  $r2 = $req_w / $req_h;
  if (($h1 > $req_h) && ($w1 > $req_w)) {
   if ($r1 > $r2) {
    $imgW1 = $req_w;
    $iw1 = $w1 - $req_w;
    $iw1 = ($iw1 / $w1) * 100;
    $ih1 = ($h1 / 100) * $iw1;
    $imgH1 = $h1 - $ih1;
   } else if ($r1 < $r2) {
    $imgH1 = $req_h;
    $ih1 = $h1 - $req_h;
    $ih1 = ($ih1 / $h1) * 100;
    $iw1 = ($w1 / 100) * $ih1;
    $imgW1 = $w1 - $iw1;
   } else {
    $imgH1 = $req_h;
    $imgW1 = $req_w;
   }
  } else if (($h1 <= $req_h) && ($w1 > $req_w)) {
   $imgW1 = $req_w;
   $iw1 = $w1 - $req_w;
   $iw1 = ($iw1 / $w1) * 100;
   $ih1 = ($h1 / 100) * $iw1;
   $imgH1 = $h1 - $ih1;
  } else if (($h1 > $req_h) && ($w1 <= $req_w)) {
   $imgH1 = $req_h;
   $ih1 = $h1 - $req_h;
   $ih1 = ($ih1 / $h1) * 100;
   $iw1 = ($w1 / 100) * $ih1;
   $imgW1 = $w1 - $iw1;
  } else if (($h1 <= $req_h) && ($w1 <= $req_w)) {
   $imgH1 = $h1;
   $imgW1 = $w1;
  }
  $arr = [$imgW1, $imgH1];
  return $arr;
 }

 function get_ratio($r) {
  $w = $h = $r;
  $w1 = $this->get_width();
  $h1 = $this->get_height();
  $ratio = $w1 / $h1;
  if ($w1 < $r || $h1 < $r) {
   $w = $w1;
   $h = $h1;
  } else if ($w1 > $h1) {
   $w = ($r * $ratio);
  } else if ($w1 < $h1) {
   $h = $r / $ratio;
  }
  $w = (int)$w;
  $h = (int)$h;
  return [$w, $h];
 }

 function get_wh($min, $file, $w, $h) {
  if ($min > 0) {
   list($tw, $th) = @getimagesize($file);
   if ($tw < $w && $th < $h) {
    if ($tw < $min && $th < $min) {
     $w = $h = 500;
    } else {
     if ($tw >= $min && $th < $min) {
      $w = $h = $tw;
     } else if ($tw < $min && $th >= $min) {
      $w = $h = $th;
     } else if ($tw >= $min && $th >= $min) {
      if ($tw > $th) {
       $w = $h = $tw;
      } else {
       $w = $h = $th;
      }
     }
    }
   }
  }
  return [$w, $h];
 }

 function square_resize($oldfilename, $newfilename, $w, $h) {
  $this->load($oldfilename);
  $size = '';
  if ($w > 0 && $h > 0) {
   if ($w == $h) {
    $size = '_ss' . $w . '_';
   } else {
    $size = '_s' . $w . ',' . $h . '_';
   }
  } else if ($w > 0 && !$h > 0) {
   $size = '_sx' . $w . '_';
  } else if (!$w > 0 && $h > 0) {
   $size = '_sy' . $h . '_';
  }

  $s = str_replace("_s", "", $size);
  $s = str_replace("_", "", $s);
  if (strpos($s, "y") !== FALSE) {
   $y = str_replace("y", "", $s);
   $type = 'h';
  } else if (strpos($s, "x") !== FALSE) {
   $x = str_replace("x", "", $s);
   $type = 'w';
  } else if (strpos($s, "s") !== FALSE) {
   $type = 'sq';
   $y = $x = str_replace("s", "", $s);
  } else if (strpos($s, ",") !== FALSE) {
   $type = 'wh';
   $s = explode(",", $s);
   $x = $s[0];
   $y = $s[1];
  }

  $w = $this->get_width();
  $h = $this->get_height();

  if ($type == 'h') {
   if (!$x) {
    $x = $w;
   }
   if ($y > 0) {
    if ($y > $h) {
     $y = $h;
    } else if ($y < $h) {
     $r = ($w / $h);
     $x = round((intval($y) * floatval($r)));
    }
   } else {
    $y = $h;
   }
  } else if ($type == 'w') {
   if (!$y) {
    $y = $h;
   }
   if ($x > 0) {
    if ($x > $w) {
     $x = $w;
    } else if ($x < $w) {
     $r = ($w / $h);
     $y = round((intval($x) / floatval($r)));
    }
   } else {
    $x = $w;
   }
  } else if ($type == 'wh') {
   if (!is_numeric($x) && !is_numeric($y)) {
    $x = $w;
    $y = $h;
   } else if (is_numeric($x) && !is_numeric($y)) {
    $y = $h;
    if ($x > 0) {
     if ($x > $w) {
      $x = $w;
     } else if ($x < $w) {
      $r = ($w / $h);
      $y = round((intval($x) / floatval($r)));
     }
    } else {
     $x = $w;
    }
   } else if (!is_numeric($x) && is_numeric($y)) {
    $x = $w;
    if ($y > 0) {
     if ($y > $h) {
      $y = $h;
     } else if ($y < $h) {
      $r = ($w / $h);
      $x = round((intval($y) * floatval($r)));
     }
    } else {
     $y = $h;
    }
   }
  } else {
   if (!$x && !$y) {
    $x = $w;
    $y = $h;
   }
  }
  $this->image = $this->white_box($x, $y);
  $this->save($newfilename);
 }

 function white_box($box_w, $box_h) {
  $img = $this->image;
  $new = imagecreatetruecolor($box_w, $box_h);
  if ($new === FALSE) {
   return NULL;
  }
  $fill = imagecolorallocate($new, 255, 255, 255);
  imagefill($new, 0, 0, $fill);
  imagecolortransparent($new, $fill);
  $hratio = $box_h / imagesy($img);
  $wratio = $box_w / imagesx($img);
  $ratio = min($hratio, $wratio);
  if ($ratio > 1.0)
   $ratio = 1.0;
  $sy = floor(imagesy($img) * $ratio);
  $sx = floor(imagesx($img) * $ratio);
  $m_y = floor(($box_h - $sy) / 2);
  $m_x = floor(($box_w - $sx) / 2);
  if (!imagecopyresampled($new, $img, $m_x, $m_y, 0, 0, $sx, $sy, imagesx($img), imagesy($img))) {
   imagedestroy($new);
   return NULL;
  }
  return $new;
 }

 function default_image($w, $h, $filename, $newfilename) {
  $this->load($filename);
  $this->image = $this->white_box($w, $h);

  /*imagealphablending($this->image, FALSE);
  imagesavealpha($this->image, TRUE);
  imagefilter($this->image, IMG_FILTER_GRAYSCALE);*/

  $this->save($newfilename);
 }


 function resize_ratio($oldfilename, $newfilename, $r) {
  $this->load($oldfilename);
  $newarr = $this->get_ratio($r);
  $this->resize($newarr[0], $newarr[1]);
  $this->save($newfilename);
 }

 function resize_wh($oldfilename, $newfilename, $w, $h) {
  $this->load($oldfilename);
  $newarr = $this->ratio($w, $h);
  $this->resize($newarr[0], $newarr[1]);
  $this->save($newfilename);
 }

 function resize_crop_wh($oldfilename, $newfilename, $w, $h, $x, $y, $scale) {
  $this->load($oldfilename);
  $this->resize_crop($w, $h, $x, $y, $scale);
  $this->save($newfilename);
 }

 function resize_crop_full($oldfilename, $newfilename, $w, $h, $x, $y) {
  $this->load($oldfilename);
  $this->resize_full_crop($w, $h, $x, $y);
  $this->save($newfilename);
 }

 function fix_wh($oldfilename, $newfilename, $w, $h) {
  $this->load($oldfilename);
  $this->resize($w, $h);
  $this->save($newfilename);
 }

 function resize_h($oldfilename, $newfilename, $h) {
  $this->load($oldfilename);
  if ($h > $this->get_height())
   $h = $this->get_height();
  $ratio = $h / $this->get_height();
  $width = (int)($this->get_width() * $ratio);
  $this->resize($width, $h);
  $this->save($newfilename);
 }

 function resize_w($oldfilename, $newfilename, $w) {
  $this->load($oldfilename);
  if ($w > $this->get_width())
   $w = $this->get_width();
  $ratio = $w / $this->get_width();
  $height = (int)($this->get_height() * $ratio);
  $this->resize($w, $height);
  $this->save($newfilename);
 }

 function scale($oldfilename, $newfilename, $scale) {
  $this->load($oldfilename);
  $width = (int)($this->get_width() * ($scale) / 100);
  $height = (int)($this->get_height() * ($scale) / 100);
  $this->resize($width, $height);
  $this->save($newfilename);
 }

 function load($filename) {
  $image_info = @getimagesize($filename);
  $this->image_type = $image_info[2];
  if ($this->image_type == IMAGETYPE_JPEG) {
   $this->image = imagecreatefromjpeg($filename);
  } else if ($this->image_type == IMAGETYPE_GIF) {
   $this->image = imagecreatefromgif($filename);
  } else if ($this->image_type == IMAGETYPE_PNG) {
   $this->image = imagecreatefrompng($filename);
  } else if ($this->image_type == IMAGETYPE_WEBP) {
   $this->image = imagecreatefromwebp($filename);
  }
 }

 function save($filename, $compression = 70, $permissions = NULL) {
  if ($this->image_type == IMAGETYPE_JPEG) {
   imagejpeg($this->image, $filename, $compression);
  } else if ($this->image_type == IMAGETYPE_GIF) {
   imagegif($this->image, $filename);
  } else if ($this->image_type == IMAGETYPE_PNG) {
   imagepng($this->image, $filename);
  } else if ($this->image_type == IMAGETYPE_WEBP) {
   imagewebp($this->image, $filename);
  }
  if ($permissions != NULL) {
   chmod($filename, $permissions);
  }
 }

 function output($image_type = IMAGETYPE_JPEG) {
  if ($image_type == IMAGETYPE_JPEG) {
   imagejpeg($this->image);
  } else if ($image_type == IMAGETYPE_GIF) {
   imagegif($this->image);
  } else if ($image_type == IMAGETYPE_PNG) {
   imagepng($this->image);
  } else if ($this->image_type == IMAGETYPE_WEBP) {
   imagewebp($this->image);
  }
 }

 function get_width() {
  return imagesx($this->image);
 }

 function get_height() {
  return imagesy($this->image);
 }

 function resize($width, $height) {
  if ($this->image_type == IMAGETYPE_PNG) {
   $this->fix_png($width, $height);
  } else {
   $new_image = imagecreatetruecolor($width, $height);
   imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->get_width(), $this->get_height());
   $this->image = $new_image;
  }
 }

 function fix_png($width, $height) {
  $resized_image = imagecreatetruecolor($width, $height);
  imagealphablending($resized_image, FALSE); // Overwrite alpha
  imagesavealpha($resized_image, TRUE);

  $alpha_image = imagecreatetruecolor($this->get_width(), $this->get_height());
  imagealphablending($alpha_image, FALSE); // Overwrite alpha
  imagesavealpha($alpha_image, TRUE);
  for ($x = 0; $x < $this->get_width(); $x++) {
   for ($y = 0; $y < $this->get_height(); $y++) {
    $alpha = (imagecolorat($this->image, $x, $y) >> 24) & 0xFF;
    $color = imagecolorallocatealpha($alpha_image, 0, 0, 0, $alpha);
    imagesetpixel($alpha_image, $x, $y, $color);
   }
  }
  imagegammacorrect($this->image, 2.0, 1.0);
  imagecopyresampled($resized_image, $this->image, 0, 0, 0, 0, $width, $height, $this->get_width(), $this->get_height());
  imagegammacorrect($resized_image, 1.0, 2.0);
  $alpha_resized_image = imagecreatetruecolor($width, $height);
  imagealphablending($alpha_resized_image, FALSE);
  imagesavealpha($alpha_resized_image, TRUE);
  imagecopyresampled($alpha_resized_image, $alpha_image, 0, 0, 0, 0, $width, $height, $this->get_width(), $this->get_height());
  for ($x = 0; $x < $width; $x++) {
   for ($y = 0; $y < $height; $y++) {
    $alpha = (imagecolorat($alpha_resized_image, $x, $y) >> 24) & 0xFF;
    $rgb = imagecolorat($resized_image, $x, $y);
    $r = ($rgb >> 16) & 0xFF;
    $g = ($rgb >> 8) & 0xFF;
    $b = $rgb & 0xFF;
    $color = imagecolorallocatealpha($resized_image, $r, $g, $b, $alpha);
    imagesetpixel($resized_image, $x, $y, $color);
   }
  }
  $this->image = $resized_image;
 }

 function resize_crop($width, $height, $x, $y, $scale) {
  $newimagewidth = ceil($width * $scale);
  $newimageheight = ceil($height * $scale);
  $new_image = imagecreatetruecolor($newimagewidth, $newimageheight);
  imagecopyresampled($new_image, $this->image, 0, 0, $x, $y, $newimagewidth, $newimageheight, $width, $height);
  $this->image = $new_image;
 }

 function resize_full_crop($width, $height, $x, $y) {
  $newimagewidth = ceil($width);
  $newimageheight = ceil($height);
  $new_image = imagecreatetruecolor($newimagewidth, $newimageheight);
  imagecopyresampled($new_image, $this->image, 0, 0, $x, $y, $newimagewidth, $newimageheight, $width, $height);
  $this->image = $new_image;
 }

 // Crop
 function image_get_preview_ratio($w, $h) {
  $max = max($w, $h);
  return $max > 400 ? (400 / $max) : 1;
 }

 function crop($image, $data = []) {
  if (!is_resource($image)) {
   $this->load($image);
  } else {
   $this->image = $image;
  }
  $this->image = imagecrop($this->image, $data);
  return $this->image;
 }

 function resize_to_wh($image, $w, $h, $mime_type, $file = '') {
  if (!is_resource($image)) {
   $this->load($image);
  } else {
   $this->image = $image;
   $this->image_type = $mime_type;
  }
  $newarr = $this->ratio($w, $h);
  $this->resize($newarr[0], $newarr[1]);
  if ($file) {
   $this->save($file);
  } else {
   return $this->view();
  }
 }

 /* function view() {
   if ($this->image_type == IMAGETYPE_JPEG) {
    header('Content-Type: image/jpeg');
    return imagejpeg($this->image, NULL, 90);
   } else if ($this->image_type == IMAGETYPE_GIF) {
    header('Content-Type: image/gif');
    return imagegif($this->image, $filename);
   } else if ($this->image_type == IMAGETYPE_PNG) {
    header('Content-Type: image/png');
    return imagepng($this->image, $filename);
   }
   return FALSE;
  }*/

}
