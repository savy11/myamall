<?php

 if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

  class qqUploadedFileXhr
  {

   /**
    * Save the file to the specified path
    * @return boolean TRUE on success
    */
   function save($path)
   {
    $input = fopen('php://input', 'r');
    $temp = tmpfile();
    $realSize = stream_copy_to_stream($input, $temp);
    fclose($input);

    if ($realSize != $this->getSize()) {
     return false;
    }

    $target = fopen($path, 'w');
    fseek($temp, 0, SEEK_SET);
    stream_copy_to_stream($temp, $target);
    fclose($target);

    return true;
   }

   function getName()
   {
    return $_GET['qqfile'];
   }

   function getSize()
   {
    if (isset($_SERVER['CONTENT_LENGTH'])) {
     return (int)$_SERVER['CONTENT_LENGTH'];
    }/* else if (isset($_GET['qqsize'])) {
      return $_GET['qqsize'];
      } */ else {
     throw new Exception('Error__Getting content length is not supported.');
    }
   }

  }

  /**
   * Handle file uploads via regular form post (uses the $_FILES array)
   */
  class qqUploadedFileForm
  {

   /**
    * Save the file to the specified path
    * @return boolean TRUE on success
    */
   function save($path)
   {
    if (!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)) {
     return false;
    }
    return true;
   }

   function getName()
   {
    return $_FILES['qqfile']['name'];
   }

   function getSize()
   {
    return $_FILES['qqfile']['size'];
   }

  }

  class qqFileUploader
  {

   private $allowedExtensions = array();
   private $sizeLimit = 10485760;
   private $file;

   function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760)
   {
    $allowedExtensions = array_map("strtolower", $allowedExtensions);

    $this->allowedExtensions = $allowedExtensions;
    $this->sizeLimit = $this->toBytes(ini_get('upload_max_filesize'));

    $this->checkServerSettings();
    if (isset($_GET['qqfile'])) {
     $this->file = new qqUploadedFileXhr();
    } elseif (isset($_FILES['qqfile'])) {
     $this->file = new qqUploadedFileForm();
    } else {
     $this->file = false;
    }
   }

   public function getName()
   {
    if ($this->file)
     return $this->file->getName();
   }

   private function checkServerSettings()
   {
    $postSize = $this->toBytes(ini_get('post_max_size'));
    $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));

    if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit) {
     $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
     throw new Exception('Server error__Increase post_max_size and upload_max_filesize to ' . $size);
    }
   }

   private function toBytes($val)
   {
    $val = trim($val);
    $last = strtolower($val[strlen($val) - 1]);
    $val = substr($val, 0, -1);
    switch ($last) {
     case 'g':
      $val *= 1024;
     case 'm':
      $val *= 1024;
     case 'k':
      $val *= 1024;
    }
    return $val;
   }

   private function formatSize($str)
   {
    if (round($str) > 1024 * 1024) {
     return round(($str / 1024) / 1024, 2) . " MB";
    } else {
     return round($str / 1024, 2) . " KB";
    }
    $val = trim($str);
    $last = strtolower($str[strlen($str) - 1]);
    switch ($last) {
     case 'g':
      $val *= 1024;
     case 'm':
      $val *= 1024;
     case 'k':
      $val *= 1024;
    }
    return $val;
   }

   /**
    * Returns array('success' => true, 'newFilename' => 'myDoc123.doc') or array('error' => 'error message')
    */
   function handleUpload($replaceOldFile = false)
   {
    try {
     require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';
     $fn = new admin\controllers\controller;

     $this->allowedExtensions = $fn->allowed_file_formats;
     $this->sizeLimit = ($fn->allowed_max_size * 1024 * 1024);

     $type = $fn->get('type');
     $upload_dir = $fn->tmp_path();

     if (!is_writable($upload_dir)) {
      throw new Exception('Server error__Server error. Upload directory isn\'t writable. ' . $upload_dir);
     }
     if (!$this->file) {
      throw new Exception('No File__Please upload at least one file.');
     }

     $pathinfo = pathinfo($this->file->getName());
     $filename = $fn->img_replace($pathinfo['basename']);
     $ext = strtolower($pathinfo['extension']);
     $size = $this->file->getSize();

     if ($size == 0) {
      throw new Exception('File too small__<span class="fw-600">(' . $filename . ')</span> is empty, please select file again without it.');
     }
     if (!in_array(strtolower($ext), $this->allowedExtensions)) {
      $these = implode(', ', $this->allowedExtensions);
      throw new Exception('Can\'t Read File__Your file couldn\'t be uploaded because <span class="fw-600">' . $filename . '</span> has invalid extension. Only ' . $these . ' are allowed.');
     }
     if ($size > $this->sizeLimit) {
      throw new Exception('Can\'t Read File__Your file couldn\'t be uploaded. File <span class="fw-600">(' . $filename . ')</span> should be less than ' . $this->toBytes($this->sizeLimit) . '.');
     }

     $new_filename = date('YmdHis') . '_' . rand(0000000, 9999999) . '.' . $ext;
     $source_path = $upload_dir . $new_filename;

     if (!$this->file->save($source_path)) {
      throw new Exception('Something went wrong!__Could not save uploaded file. The upload was cancelled, or server error encountered.');
     }

     if (in_array($ext, $fn->allowed_image_formats) !== false) {
      $im = new \resources\controllers\image_resize;
      if ($type == 'products') {
       list($w, $h) = $im->get_wh(500, $source_path, 2000, 2000);
       $im->square_resize($source_path, $source_path, $w, $h);
      } else {

       $im->resize_wh($source_path, $source_path, 2000, 2000);
      }
     }

     $tmp_thumb = $fn->tmp_file_data($new_filename);
     $_SESSION[$type][$new_filename] = array('name' => $filename, 'filename' => $new_filename, 'size' => $size, 'ext' => $ext);

     $thumb = $fn->is_image($ext) ? $fn->get_file($tmp_thumb, 0, 0, 200) : $fn->get_default_icon($ext);

     return array('success' => true, 'type' => $type, 'name' => $filename, 'file' => $fn->get_file($tmp_thumb), 'thumb' => $thumb, 'app' => $fn->encrypt_post_data(array('for' => $type, 'filename' => $new_filename)), 'filename' => $new_filename);
    } catch (Exception $ex) {
     $error = explode('__', $ex->getMessage());
     return array('title' => $error[0], 'error' => $error[1]);
    }
   }

  }

  $qq = new qqFileUploader();
  $result = $qq->handleUpload();
  echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
  exit();
 } else {
  echo "<h2>Bad Request</h2><p>Your browser sent a request that this server could not understand.</p>";
 }
