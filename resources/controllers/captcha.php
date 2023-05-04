<?php
 
 namespace resources\controllers;
 
 class captcha
 {
  
  protected $fn = null;
  public $width = 150;
  public $height = 50;
  public $font_path = '';
  public $min_word_length = 6;
  public $max_word_length = 6;
  public $session_var = 'captcha';
  public $fonts = array('Raleway' => array('spacing' => 5, 'min_size' => 15, 'max_size' => 20, 'font' => 'Raleway.ttf'));
  public $x_period = 11;
  public $x_amplitude = 5;
  public $y_period = 10;
  public $y_amplitude = 10;
  public $max_rotation = 5;
  public $scale = 2;
  public $blur = false;
  public $debug = false;
  public $image_format = 'png';
  public $im;
  public $shadow_color = array();
  public $color = array(0, 0, 0);
  public $background_color = array();
  public $text_colors = array(1 => array(0, 0, 0), 2 => array(255, 255, 255), 3 => array(0, 0, 0), 4 => array(0, 0, 0));
  public $bg_colors = array(1 => array(255, 255, 255), 2 => array(0, 0, 0), 3 => array(246, 246, 246), 4 => array(249, 249, 249));
  public $key = array();
  
  public function __construct($fn)
  {
   $this->fn = $fn;
   
   $this->background_color = $this->bg_colors[1];
   $this->color = $this->text_colors[1];
   
   $this->key = $this->fn->decrypt_post_data($fn->get('key'));
   if ($this->fn->varv('color', $this->key) != '') {
    $this->background_color = $this->bg_colors[$this->key['color']];
    $this->color = $this->text_colors[$this->key['color']];
   }
  }
  
  public function create_image($font_path = '')
  {
   $this->font_path = $font_path;
   $ini = microtime(true);
   $this->image_allocate();
   $text = $this->get_captcha_text();
   $font_cfg = $this->fonts[array_rand($this->fonts)];
   $this->write_text($text, $font_cfg);
   unset($_SESSION[$this->session_var]);
   $_SESSION[$this->session_var][$this->key['for']] = $text;
   // $this->wave_image();
   if ($this->blur && function_exists('imagefilter')) {
    imagefilter($this->im, IMG_FILTER_GAUSSIAN_BLUR);
   }
   $this->reduce_image();
   if ($this->debug) {
    imagestring($this->im, 1, 1, $this->height - 8, "$text {$font_cfg['font']} " . round((microtime(true) - $ini) * 1000) . "ms", $this->gd_fg_color);
   }
   $this->write_image();
   $this->cleanup();
  }
  
  protected function image_allocate()
  {
   if (!empty($this->im)) {
    imagedestroy($this->im);
   }
   $this->im = imagecreatetruecolor($this->width * $this->scale, $this->height * $this->scale);
   $this->gd_bg_color = imagecolorallocate($this->im, $this->background_color[0], $this->background_color[1], $this->background_color[2]);
   imagefilledrectangle($this->im, 0, 0, $this->width * $this->scale, $this->height * $this->scale, $this->gd_bg_color);
   
   // $color = $this->color[mt_rand(0, sizeof($this->color) - 1)];
   $this->gd_fg_color = imagecolorallocate($this->im, $this->color[0], $this->color[1], $this->color[2]);
   if (!empty($this->shadow_color) && is_array($this->shadow_color) && sizeof($this->shadow_color) >= 3) {
    $this->gd_shadow_color = imagecolorallocate($this->im, $this->shadow_color[0], $this->shadow_color[1], $this->shadow_color[2]);
   }
  }
  
  protected function get_captcha_text()
  {
   $text = $this->get_random_captcha_text();
   return $text;
  }
  
  protected function get_random_captcha_text($length = null)
  {
   if (empty($length)) {
    $length = rand($this->min_word_length, $this->max_word_length);
   }
   $words = 'abcdefghijklmnopqrstuvwxyz1234567890';
   $vocals = 'aeiou';
   $text = '';
   for ($i = 0; $i < $length; $i++) {
    $text .= substr($words, mt_rand(0, 22), 1);
   }
   return $text;
  }
  
  protected function write_text($text, $font_cfg = array())
  {
   if (empty($font_cfg)) {
    $font_cfg = $this->fonts[array_rand($this->fonts)];
   }
   $font_file = $this->font_path . '/' . $font_cfg['font'];
   $letters_missing = $this->max_word_length - strlen($text);
   $font_size_factor = 1 + ($letters_missing * 0.09);
   $x = 20 * $this->scale;
   $y = round(($this->height * 27 / 40) * $this->scale);
   $length = strlen($text);
   for ($i = 0; $i < $length; $i++) {
    $degree = rand($this->max_rotation * -1, $this->max_rotation);
    $font_size = rand($font_cfg['min_size'], $font_cfg['max_size']) * $this->scale * $font_size_factor;
    $letter = substr($text, $i, 1);
    if ($this->shadow_color) {
     $coords = imagettftext($this->im, $font_size, $degree, $x + $this->scale, $y + $this->scale, $this->gd_shadow_color, $font_file, $letter);
    }
    $coords = imagettftext($this->im, $font_size, $degree, $x, $y, $this->gd_fg_color, $font_file, $letter);
    $x += ($coords[2] - $x) + ($font_cfg['spacing'] * $this->scale);
   }
  }
  
  protected function wave_image()
  {
   $xp = $this->scale * $this->x_period * rand(1, 3);
   $k = rand(0, 100);
   for ($i = 0; $i < ($this->width * $this->scale); $i++) {
    imagecopy($this->im, $this->im, $i - 1, sin($k + $i / $xp) * ($this->scale * $this->x_amplitude), $i, 0, 1, $this->height * $this->scale);
   }
   $k = rand(0, 100);
   $yp = $this->scale * $this->y_period * rand(1, 2);
   for ($i = 0; $i < ($this->height * $this->scale); $i++) {
    imagecopy($this->im, $this->im, sin($k + $i / $yp) * ($this->scale * $this->y_amplitude), $i - 1, 0, $i, $this->width * $this->scale, 1);
   }
  }
  
  protected function reduce_image()
  {
   $img_resampled = imagecreatetruecolor($this->width, $this->height);
   imagecopyresampled($img_resampled, $this->im, 0, 0, 0, 0, $this->width, $this->height, $this->width * $this->scale, $this->height * $this->scale);
   imagedestroy($this->im);
   $this->im = $img_resampled;
  }
  
  protected function write_image()
  {
   if ($this->image_format == 'png' && function_exists('imagepng')) {
    header('Content-type: image/png');
    imagepng($this->im);
   } else {
    header('Content-type: image/jpeg');
    imagejpeg($this->im, null, 80);
   }
  }
  
  protected function cleanup()
  {
   imagedestroy($this->im);
  }
  
 }

?>
