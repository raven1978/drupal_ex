<!DOCTYPE html>
<html lang="<?php print $language->language; ?>" xml:lang="<?php print $language->language; ?>" dir="<?php print $language->dir; ?>">

<head><!-- zp6-4.x -->
<title><?php print $head_title ?></title>
<?php if (theme_get_setting('grid_responsive')): ?>
<meta name="HandheldFriendly" content="true" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="MobileOptimized" content="width" />
<?php endif ?>
<?php print $head ?>

<?php if (theme_get_setting('css_zone')): ?>
<link rel="stylesheet" media="all" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css" />
<?php else: ?>
<link rel="stylesheet" media="all" href="<?php print base_path() . drupal_get_path('theme', 'zeropoint') ?>/css/yui/pure-min.css" />
<?php endif; ?>

<?php if (theme_get_setting('grid_responsive')): ?>
<?php if (theme_get_setting('css_zone')): ?>
<!--[if IE 8]>
<link rel="stylesheet" media="all" href="http://yui.yahooapis.com/pure/0.6.0/grids-responsive-old-ie-min.css">
<![endif]-->
<!--[if gt IE 8]><!-->
<link rel="stylesheet" media="all" href="http://yui.yahooapis.com/pure/0.6.0/grids-responsive-min.css">
<!--<![endif]-->
<?php else: ?>
<!--[if IE 8]>
<link rel="stylesheet" media="all" href="<?php print base_path() . drupal_get_path('theme', 'zeropoint') ?>/css/yui/grids-responsive-old-ie-min.css">
<![endif]-->
<!--[if gt IE 8]><!-->
<link rel="stylesheet" media="all" href="<?php print base_path() . drupal_get_path('theme', 'zeropoint') ?>/css/yui/grids-responsive-min.css">
<!--<![endif]-->
<?php endif; ?>
<?php endif; ?>

<?php if (theme_get_setting('headerimg')): ?>
<?php if ($language->dir == 'rtl'): ?>
<link rel="stylesheet" media="all" href="<?php print base_path() . drupal_get_path('theme', 'zeropoint') ?>/_custom/headerimg-rtl/rotate.php" />
<?php else: ?>
<link rel="stylesheet" media="all" href="<?php print base_path() . drupal_get_path('theme', 'zeropoint') ?>/_custom/headerimg/rotate.php" />
<?php endif; ?>
<?php endif; ?>

<?php print $styles ?>
<?php print $scripts ?>
<script type="text/javascript"><?php /* Needed to avoid Flash of Unstyle Content in IE */ ?> </script>
</head>

<body class="<?php print $body_classes; ?>">
<div id="skip-link">
  <a href="#main" class="element-invisible element-focusable"><?php print t('Skip to main content') ?></a>
  <a href="#search" class="element-invisible element-focusable"><?php print t('Skip to search') ?></a>
</div>

<div id="top_bg">
<div class="sizer0 clearfix"<?php print wrapper_width() ?>>
<div id="top_left">
<div id="top_right">
<div id="headimg">

<div id="header">
<div class="clearfix">
<?php if (theme_get_setting('loginlinks') || $search_box || $topreg): ?>
  <div id="top-elements">
    <?php if (theme_get_setting('loginlinks')): ?><div id="user_links"><?php print login_links() ?></div><?php endif; ?>
    <?php if ($search_box): ?><div id="search-box"><?php print $search_box; ?></div><?php endif; ?>
    <?php if ($topreg): ?><div id="banner"><?php print $topreg; ?></div><?php endif; ?>
  </div>
<?php endif; ?>
  <?php if ($logo): ?><a href="<?php print check_url($front_page); ?>" title="<?php print t('Home'); ?>"><img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" class="logoimg" /></a><?php endif; ?>
  <div id="name-and-slogan">
  <?php if ($site_name): ?>
    <?php if ($title): ?>
      <p id="site-name"><a href="<?php print check_url($front_page); ?>" title="<?php print t('Home'); ?>"><?php print $site_name; ?></a></p>
    <?php else: ?>
      <h1 id="site-name"><a href="<?php print check_url($front_page); ?>" title="<?php print t('Home'); ?>"><?php print $site_name; ?></a></h1>
    <?php endif; ?>
  <?php endif; ?>
  <?php if ($site_slogan): ?><div id="site-slogan"><?php print $site_slogan; ?></div><?php endif; ?>
  </div>
</div>
<?php if ($header): ?><?php print $header; ?><?php endif; ?>
<div class="menuband clearfix">
  <div id="menu" class="menu-wrapper">
  <?php if ($logo || $site_name): ?>
    <a href="<?php print check_url($front_page); ?>" class="pure-menu-heading" title="<?php if ($site_slogan) print $site_slogan; ?>">
      <?php if ($logo): ?><img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" class="logomob" /><?php endif; ?>
      <?php if ($site_name) print $site_name; ?>
    </a>
  <?php endif; ?>
  <?php if(isset($primary_links)) { ?>
    <a href="#" id="toggles" class="menu-toggle"><s class="bars"></s><s class="bars"></s><div class="element-invisible">toggle</div></a>
    <div class="pure-menu pure-menu-horizontal menu-transform">
      <h2 class="element-invisible"><?php print t('Main menu'); ?></h2>
      <?php print zeropoint_main_menu(); ?>
    </div>
  <?php } ?>
  </div>
</div>
</div>

</div></div></div></div></div>

<div id="body_bg">
<div class="sizer0 clearfix"<?php print wrapper_width() ?>>
<div id="body_left">
<div id="body_right">

<?php if (isset($secondary_links)) { ?>
  <h2 class="element-invisible"><?php print t('Secondary menu'); ?></h2>
  <?php print theme('links', $secondary_links, array('class' =>'links', 'id' => 'submenu')); ?>
<?php } ?>

<div id="breadcrumb" class="clearfix"><?php print $breadcrumb; ?></div>

<?php if($user1 || $user2 || $user3 || $user4) : ?>
<div id="section1" class="sections pure-g">
<?php if($user1) : ?><div class="<?php print section_class($variables); ?>"><div class="u1"><?php print $user1; ?></div></div><?php endif; ?>
<?php if($user2) : ?><div class="<?php print section_class($variables); ?>"><div class="u2 <?php print divider() ?>"><?php print $user2; ?></div></div><?php endif; ?>
<?php if($user3) : ?><div class="<?php print section_class($variables); ?>"><div class="u3 <?php print divider() ?>"><?php print $user3; ?></div></div><?php endif; ?>
<?php if($user4) : ?><div class="<?php print section_class($variables); ?>"><div class="u4 <?php print divider() ?>"><?php print $user4; ?></div></div><?php endif; ?>
</div>
<?php endif; ?>

<div class="clearfix">
<div id="middlecontainer" class="pure-g">
<?php if ($left) { ?>
  <div class="<?php print first_class(); ?>">
    <div id="sidebar-left"><?php print $left ?></div>
  </div>
<?php } ?>
<div class="<?php print cont_class($variables); ?>">
  <div id="main">
    <?php if ($mission) { ?><div id="mission"><?php print $mission ?></div><?php } ?>
    <?php if ($content_top):?><div id="content-top"><?php print $content_top; ?></div><?php endif; ?>
    <?php if ($title): if ($is_front){ print '<h2 class="title">'. $title .'</h2>'; } else { print '<h1 class="title">'. $title .'</h1>'; } endif; ?>
    <div class="tabs"><?php print $tabs ?></div>
    <?php print $help ?>
    <?php print $messages ?>
    <?php print $content; ?>
    <?php print $feed_icons; ?>
    <?php if ($content_bottom): ?><div id="content-bottom"><?php print $content_bottom; ?></div><?php endif; ?>
  </div>
</div>
<?php if ($right) { ?>
  <div class="<?php print second_class(); ?>">
    <div id="sidebar-right"><?php print $right ?></div>
  </div>
<?php } ?>
</div></div>

<?php if($user5 || $user6 || $user7 || $user8) : ?>
<div id="section2" class="sections pure-g">
<?php if($user5) : ?><div class="<?php print section_class($variables, false); ?>"><div class="u1"><?php print $user5; ?></div></div><?php endif; ?>
<?php if($user6) : ?><div class="<?php print section_class($variables, false); ?>"><div class="u2 <?php print divider() ?>"><?php print $user6; ?></div></div><?php endif; ?>
<?php if($user7) : ?><div class="<?php print section_class($variables, false); ?>"><div class="u3 <?php print divider() ?>"><?php print $user7; ?></div></div><?php endif; ?>
<?php if($user8) : ?><div class="<?php print section_class($variables, false); ?>"><div class="u4 <?php print divider() ?>"><?php print $user8; ?></div></div><?php endif; ?>
</div>
<?php endif; ?>

<?php if ((isset($primary_links)) && theme_get_setting('menu2')) { ?>
  <h2 class="element-invisible"><?php print t('Main menu'); ?></h2>
  <?php print theme('links', $primary_links, array('class' =>'links', 'id' => 'menu2')) ?>
<?php } ?>

</div></div></div></div>

<div id="bottom_bg">
<div class="sizer0 clearfix"<?php print wrapper_width() ?>>
<div id="bottom_left">
<div id="bottom_right">

<div id="footer">
<div><?php print $footer_message ?></div>
</div>
<div id="brand"></div>

<?php if ($below) { ?><div id="belowme"><?php print $below; ?></div><?php } ?>

</div></div></div></div>

<?php print $closure ?>

<!--[if IE 9]>
<script type="text/javascript" async src="<?php print base_path() . drupal_get_path('theme', 'zeropoint') ?>/js/classList.min.js"></script>
<![endif]-->
<!--[if gte IE 9]><!-->
<script type="text/javascript" async src="<?php print base_path() . drupal_get_path('theme', 'zeropoint') ?>/js/toggles.min.js"></script>
<!--<![endif]-->
</body>
</html>
