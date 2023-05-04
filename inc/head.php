<!DOCTYPE html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<head>
    <title><?php echo ucfirst($fn->varv('page_title', $fn->cms)) . ' - ' . app_name; ?></title>
    <meta name="description" content="<?php echo $fn->varv('meta_desc', $fn->cms); ?>"/>
    <meta name="keywords" content="<?php echo $fn->varv('meta_keywords', $fn->cms); ?>"/>

    <meta charset="utf-8">

    <!--[if IE]>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">``
    <![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
 
 <?php
  $pre = ['https://adservice.google.co.in', 'https://adservice.google.com', 'https://www.google-analytics.com', 'https://tpc.googlesyndication.com'];
  foreach ($pre as $p) {
   ?>
      <link rel="preconnect" href="<?php echo $p; ?>" crossorigin="anonymous"/>
      <link rel="dns-prefetch" href="<?php echo $p; ?>" crossorigin="anonymous"/>
   <?php
  }
 ?>

    <link rel="icon" type="image/png" href="<?php echo $fn->permalink('favicon.png'); ?>"/>

    <link rel="preload" href="<?php echo $fn->permalink('assets/css/ie.style.css'); ?>" as="style" crossorigin="anonymous"/>
    <link rel="preload" href="<?php echo $fn->permalink('assets/css/custom.css'); ?>" as="style" crossorigin="anonymous"/>
    <link rel="preload" href="<?php echo $fn->permalink('assets/css/bundle.css'); ?>" as="style" crossorigin="anonymous"/>
    <link rel="preload" href="<?php echo $fn->permalink('assets/css/fonts/PTSans.woff'); ?>" as="font" crossorigin="anonymous"/>
    <link rel="preload" href="<?php echo $fn->permalink('assets/css/fonts/fontawesome-webfont.woff?v=4.1.0'); ?>" as="font" crossorigin="anonymous"/>
    <link rel="preload" href="<?php echo $fn->permalink('assets/css/fonts/Raleway.woff'); ?>" as="font" crossorigin="anonymous"/>

    <link rel="stylesheet" type="text/css" href="<?php echo $fn->permalink('assets/css/bundle.css'); ?>" crossorigin="anonymous"/>
    <link rel="stylesheet" href="<?php echo $fn->permalink('assets/css/ie.style.css'); ?>" crossorigin="anonymous"/>
    <link rel="stylesheet" href="<?php echo $fn->permalink('assets/css/custom.css'); ?>" crossorigin="anonymous"/>
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js" crossorigin="anonymous"></script>
    <script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js" crossorigin="anonymous"></script>
    <![endif]-->
    <!--[if IE 7]>
    <link rel="stylesheet" href="<?php echo $fn->permalink('assets/css/font-awesome-ie7.css'); ?>" crossorigin="anonymous" />
    <![endif]-->
 <?php echo $fn->style; ?>

    <script type="text/javascript">
        var root = '<?php echo $fn->permalink(); ?>', hostname = '<?php echo $fn->permalink(); ?>',
            token = '<?php echo $fn->get_token(); ?>', default_currency = '<?php echo $fn->company['default_currency']; ?>';
    </script>
</head>
<body>

<div class="web">
