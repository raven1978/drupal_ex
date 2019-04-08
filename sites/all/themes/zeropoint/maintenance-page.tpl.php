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

<div id="top_bg">
<div class="sizer0 clearfix"<?php print wrapper_width() ?>>
<div id="top_left">
<div id="top_right">
<div id="headimg">

<div id="header">
<div class="clearfix">
  <?php if ($logo): ?><a href="<?php print check_url($front_page); ?>" title="<?php print t('Home'); ?>"><img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" class="logoimg" /></a><?php endif; ?>
  <div id="name-and-slogan">
  <?php if ($site_name): ?>
    <h1 id="site-name"><a href="<?php print check_url($front_page); ?>" title="<?php print t('Home'); ?>"><?php print $site_name; ?></a></h1>
  <?php endif; ?>
  <?php if ($site_slogan): ?><div id="site-slogan"><?php print $site_slogan; ?></div><?php endif; ?>
  </div>
</div>
<div class="menuband clearfix">
  <div id="menu" class="menu-wrapper">
  <?php if ($logo || $site_name): ?>
    <a href="<?php print check_url($front_page); ?>" class="pure-menu-heading" title="<?php if ($site_slogan) print $site_slogan; ?>">
      <?php if ($logo): ?><img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" class="logomob" /><?php endif; ?>
      <?php if ($site_name) print $site_name; ?>
    </a>
  <?php endif; ?>
  </div>
</div>
</div>

</div></div></div></div></div>

<div id="body_bg">
<div class="sizer0 clearfix"<?php print wrapper_width() ?>>
<div id="body_left">
<div id="body_right">

<div class="clearfix"></div>

<div class="clearfix">
<div id="middlecontainer" class="pure-g">
  <div class="pure-u-1">
    <div id="main">
      <h2 class="title"><?php print $title ?></h2>
      <?php print $content; ?>
    </div>
  </div>
</div></div>

</div></div></div></div>

<div id="bottom_bg">
<div class="sizer0 clearfix"<?php print wrapper_width() ?>>
<div id="bottom_left">
<div id="bottom_right">

<div id="footer"></div>
<div id="brand"></div>

</div></div></div></div>

<?php print $closure ?>

</body>
</html>
