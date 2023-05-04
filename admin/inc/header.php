</head>
<body class="top-left-sidebar">
 <div class="loader">
  <div class="spinner">
   <div class="spin"></div>
  </div>
 </div>
 <header>
  <nav class="navbar navbar-default top-header">
   <div class="container-fluid">
    <div class="navbar-header">
     <button type="button" class="toggle-btn"><span class="icon-bar"><span></span><span></span><span></span></span></button>
     <a class="navbar-brand text-logo" href="<?php echo $fn->permalink(); ?>"><?php echo str_replace('Technologies', '<span>Diagnostics</span>', app_name); ?></a>
    </div>
    <div class="collapse navbar-collapse">
     <?php if ($fn->menus) { ?>
       <ul class="nav navbar-nav top-menu">
        <?php
        foreach ($fn->menus as $k => $v) {
         $sel = false;
         if ($fn->varv('data', $v) != '') {
          foreach ($v['data'] as $k1 => $v1) {
           if ($fn->varv('page_url', $fn->page) == $k1) {
            $sel = true;
            $breadcrums = $k;
           }
          }
          ?>
          <li class="dropdown<?php echo $sel ? ' active' : ''; ?>">
           <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="icon <?php echo $v['icon']; ?>"></span><span class="text"><?php echo _($k); ?></span></a>
           <ul class="dropdown-menu">
            <?php foreach ($v['data'] as $k1 => $v1) { ?>
             <li<?php echo ($fn->varv('page_url', $fn->page) == $k1 ? ' class="active"' : '' ); ?>><a href="<?php echo $fn->permalink($k1); ?>"><span class="icon <?php echo $v1['icon']; ?>"></span> <?php echo _($v1['name']); ?><?php echo $v1['seo'] == 'Y' ? '<span class="label label-success pull-right" style="margin-top: 2px;">SEO</span>': '';?></a></li>
            <?php } ?>
           </ul>
          </li>
          <?php
         } else {
          if ($v['form_code'] != '' || $v['form_code'] != '#') {
           ?>
           <li<?php echo $fn->varv('page_url', $fn->page) == $v['form_code'] ? ' class="active"' : ''; ?>>
            <a href="<?php echo $fn->permalink($v['form_code']); ?>"><span class="icon <?php echo $v['icon']; ?>"></span><span class="text"><?php echo _($v['name']); ?></span></a>
           </li>
           <?php
          }
         }
        }
        ?>
       </ul>
      <?php } ?>
     <form id="lock-frm" action="<?php echo $fn->permalink('lock'); ?>" method="post" class="hide">
      <input type="hidden" name="lock[token]" value="<?php echo $fn->post_token(); ?>" />
     </form>
     <ul class="nav navbar-nav navbar-right user-menu">
      <li class="dropdown">
       <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><img src="<?php echo $fn->file_exists($fn->user['image']) ? $fn->get_file($fn->user['image']) : $fn->permalink('assets/img/no-user.jpg'); ?>" class="img-circle user-img" alt="<?php echo $fn->user['display_name']; ?>" /><span class="angle-down s7-angle-down"></span></a>
       <ul class="dropdown-menu right-arrow">
        <li><a href="<?php echo $fn->permalink('profile'); ?>"><span class="icon s7-user"></span> Profile</a></li>
        <li><a href="<?php echo $fn->permalink('lock'); ?>" class="lock"><span class="icon s7-door-lock"></span> Lock Screen</a></li>
        <li><a href="<?php echo $fn->permalink('logout'); ?>"><span class="icon s7-power"></span> Logout</a></li>
       </ul>
      </li>
     </ul>
    </div>
   </div>
  </nav>
  <div class="mobile-menu">
   <div class="main-profile">
    <div class="profile-wrap">
     <a href="#profile-nav" data-toggle="collapse" aria-expanded="true">
      <img class="img-circle user-img" src="<?php echo $fn->file_exists($fn->user['image']) ? $fn->get_file($fn->user['image']) : $fn->permalink('assets/img/no-user.jpg'); ?>" alt="<?php echo $fn->user['display_name']; ?>" />
      <span class="box-block">
       <p>Welcome <?php echo $fn->user['first_name']; ?></p>
       <span><?php echo $fn->user['group_name']; ?></span>
      </span>
      <span class="pull-right">
       <i class="s7-angle-down"></i>
      </span>
     </a>
    </div>
    <div id="profile-nav" class="list-group collapse" aria-expanded="false">
     <a href="<?php echo $fn->permalink('profile'); ?>" class="list-group-item"><span class="icon s7-user"></span> Profile</a>
     <a href="<?php echo $fn->permalink('lock'); ?>" class="list-group-item lock"><span class="icon s7-door-lock"></span> Lock Screen</a>
     <a href="<?php echo $fn->permalink('logout'); ?>" class="list-group-item"><span class="icon s7-power"></span> Logout</a>
    </div>
   </div>
   <?php if ($fn->menus) { ?>
     <ul id="main-nav-menu" class="list-group" role="tablist" aria-multiselectable="false">
      <?php
      $i = 0;
      foreach ($fn->menus as $k => $v) {
       $sel = false;
       if ($fn->varv('data', $v) != '') {
        foreach ($v['data'] as $k1 => $v1) {
         if ($fn->varv('page_url', $fn->page) == $k1) {
          $sel = true;
          $breadcrums = $k;
         }
        }
        ?>
        <li<?php echo $sel ? ' class="active"' : ''; ?>>
         <a href="#col-<?php echo $i; ?>" data-toggle="collapse" aria-expanded="false"><span class="icon <?php echo $v['icon']; ?>"></span> <?php echo _($k); ?> <span class="pull-right angle-down s7-angle-down"></span></a>
         <ul id="col-<?php echo $i; ?>" class="collapse<?php echo $sel ? ' in' : ''; ?>" aria-expanded="false">
          <?php foreach ($v['data'] as $k1 => $v1) { ?>
           <li<?php echo ($fn->varv('page_url', $fn->page) == $k1 ? ' class="active"' : '' ); ?>><a href="<?php echo $fn->permalink($k1); ?>"><span class="icon <?php echo $v1['icon']; ?>"></span> <?php echo _($v1['name']); ?><?php echo $v1['seo'] == 'Y' ? '<span class="label label-success pull-right" style="margin-top: 2px;">SEO</span>': '';?></a></li>
          <?php } ?>
         </ul>
        </li>
        <?php
       } else {
        if ($v['form_code'] != '' || $v['form_code'] != '#') {
         ?>
         <li<?php echo $fn->varv('page_url', $fn->page) == $v['form_code'] ? ' class="active"' : ''; ?>>
          <a href="<?php echo $fn->permalink($v['form_code']); ?>"><span class="icon <?php echo $v['icon']; ?>"></span><span class="text"><?php echo _($v['name']); ?></span></a>
         </li>
         <?php
        }
       }
       $i++;
      }
      ?>
     </ul>
    <?php } ?>
  </div>
 </header>
 <div class="content-container">
  <?php if ($fn->varv('page_url', $fn->page) != '' && $fn->varv('name', $fn->page) != '') { ?>
    <div class="page-head">
     <h2><span class="icon <?php echo $fn->page['icon']; ?>"></span> <?php echo $fn->page['name']; ?></h2>
     <ol class="breadcrumb">
      <li><a href="<?php echo $fn->permalink(); ?>">Home</a></li>
      <?php if (isset($breadcrums)) { ?>
       <li><a><?php echo _($breadcrums); ?></a></li>
      <?php } ?>
      <li class="active"><?php echo $fn->page['name']; ?></li>
     </ol>
     <div class="top-btns">
      <?php
      if ($fn->show_buttons) {
       if ($fn->per_add || $fn->per_edit) {
        if (in_array($fn->get('action'), array('add', 'edit', 'sort', 'view')) == false) {
         if ($fn->per_add) {
          ?>
          <a class="btn btn-success btn-sm" title="<?php echo _('Add New'); ?>" href="<?php echo $fn->get_action_url('add'); ?>"><i class="s7-plus"></i> <?php echo _('Add New'); ?></a>
         <?php } if ($fn->show_sort) { ?>
          <a href="<?php echo $fn->get_action_url('sort'); ?>" class="btn btn-info btn-sm"><?php echo _('Sort'); ?></a>
         <?php } if ($fn->show_search) {
          ?>
          <a href="javascript:;" class="btn btn-warning btn-sm" onclick="$('.search-panel').slideToggle(500);"><i class="s7-search"></i> <?php echo _('Search'); ?></a>
          <?php
         }
        } else {
         ?>
         <a class="btn btn-default btn-sm" title="<?php echo _('Back'); ?>" href="<?php echo $fn->return_ref(true); ?>"><i class="s7-left-arrow"></i> <?php echo _('Back'); ?></a>
         <?php
        }
       } else if ($fn->get('action') == 'view') {
        ?>
        <a class="btn btn-default btn-sm" title="<?php echo _('Back'); ?>" href="<?php echo $fn->return_ref(true); ?>"><i class="s7-left-arrow"></i> <?php echo _('Back'); ?></a>
        <?php
       }
      }
      ?>
     </div>
    </div>
   <?php } ?>
  <div class="main-content">
