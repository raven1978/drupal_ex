<?php

/**
 * Initialize theme settings
 */
if (is_null(theme_get_setting('user_notverified_display')) || theme_get_setting('rebuild_registry')) {

// Auto-rebuild the theme registry during theme development.
  if(theme_get_setting('rebuild_registry')) {
    drupal_set_message(t('The theme registry has been rebuilt. <a href="!link">Turn off</a> this feature on production websites.', array('!link' => url('admin/build/themes/settings/' . $GLOBALS['theme']))), 'warning');
  }

  global $theme_key;
// Get node types
  $node_types = node_get_types('names');

/**
 * The default values for the theme variables. Make sure $defaults exactly
 * matches the $defaults in the theme-settings.php file.
 */
  $defaults = array(
// Pure Grid
    'css_zone'        => 0,
    'wrapper'         => '85em',
    'first_width'     => 5,
    'second_width'    => 5,
    'grid_responsive' => 1,
    'mobile_blocks'   => 2,
// Layout
    'style'           => 'ink',
    'themedblocks'    => 0,
    'blockicons'      => 2,
    'pageicons'       => 1,
    'navpos'          => 1,
    'menu2'           => 1,
    'fntsize'         => 0,
    'roundcorners'    => 1,
    'headerimg'       => 1,
    'loginlinks'      => 1,
    'devlink'         => 0,
// General
    'mission_statement_pages'          => 'home',
    'breadcrumb_display'               => 1,
    'user_notverified_display'         => 1,
    'search_snippet'                   => 1,
    'search_info_type'                 => 0,
    'search_info_user'                 => 1,
    'search_info_date'                 => 1,
    'search_info_comment'              => 1,
    'search_info_upload'               => 1,
// Node
    'submitted_by_author_default'      => 1,
    'submitted_by_date_default'        => 1,
    'submitted_by_enable_content_type' => 0,
    'taxonomy_display_default'         => 'only',
    'taxonomy_format_default'          => 'list',
    'taxonomy_enable_content_type'     => 0,
// SEO
    'front_page_title_display'         => 'title_slogan',
    'page_title_display_custom'        => '',
    'other_page_title_display'         => 'ptitle_stitle',
    'other_page_title_display_custom'  => '',
    'configurable_separator'           => ' | ',
    'meta_keywords'                    => '',
    'meta_description'                 => '',
// Theme dev.
    'rebuild_registry'                 => 0,
    'siteid'                           => '',
    'fix_css_limit'                    => 0,
  );

// Make the default content-type settings the same as the default theme settings,
// so we can tell if content-type-specific settings have been altered.
  $defaults = array_merge($defaults, theme_get_settings());

// Set the default values for content-type-specific settings
  foreach ($node_types as $type => $name) {
    $defaults["taxonomy_display_{$type}"]    = $defaults['taxonomy_display_default'];
    $defaults["taxonomy_format_{$type}"]     = $defaults['taxonomy_format_default'];
    $defaults["submitted_by_author_{$type}"] = $defaults['submitted_by_author_default'];
    $defaults["submitted_by_date_{$type}"]   = $defaults['submitted_by_date_default'];
  }

// Get default theme settings.
  $settings = theme_get_settings($theme_key);

// Don't save the toggle_node_info_ variables
  if (module_exists('node')) {
    foreach (node_get_types() as $type => $name) {
      unset($settings['toggle_node_info_'. $type]);
    }
  }

// Save default theme settings
  variable_set(
    str_replace('/', '_', 'theme_'. $theme_key .'_settings'),
    array_merge($defaults, $settings)
  );

// Force refresh of Drupal internals
  theme_get_setting('', TRUE);
}


// Get styles (add css stylesheets here to avoid IE 30 stylesheets limit)
function get_zeropoint_style() {
  $style = theme_get_setting('style');
  return $style;
}

  drupal_add_css(drupal_get_path('theme','zeropoint').'/css/style-zero.css', 'theme');
  drupal_add_css(drupal_get_path('theme','zeropoint').'/css/'.get_zeropoint_style().'.css', 'theme');
  drupal_add_css(drupal_get_path('theme','zeropoint').'/_custom/custom-style.css', 'theme');


/**
 * Modify theme variables
 */
function zeropoint_preprocess(&$vars) {
  global $user;                                           // Get the current user
  $vars['is_admin'] = in_array('ADMIN', $user->roles);    // Check for Admin, logged in
  $vars['logged_in'] = ($user->uid > 0) ? TRUE : FALSE;
}


function zeropoint_preprocess_page(&$vars) {
  global $language;
// Remove the duplicate meta content-type tag, a bug in Drupal 6
  $vars['head'] = preg_replace('/<meta http-equiv=\"Content-Type\"[^>]*>/', '', $vars['head']);
// Remove sidebars if disabled
  if (!$vars['show_blocks']) {
    $vars['left'] = '';
    $vars['right'] = '';
  }

// Build array of helpful body classes
  $body_classes = array();
  $body_classes[] = ($vars['is_admin']) ? 'admin' : 'not-admin';                                    // Page user is admin
  $body_classes[] = ($vars['logged_in']) ? 'logged-in' : 'not-logged-in';                           // Page user is logged in
  $body_classes[] = ($vars['is_front']) ? 'front' : 'not-front';                                    // Page is front page
  if (isset($vars['node'])) {
    $body_classes[] = ($vars['node']) ? 'full-node' : '';                                           // Page is one full node
    $body_classes[] = (($vars['node']->type == 'forum') || (arg(0) == 'forum')) ? 'forum' : '';     // Page is Forum page
    $body_classes[] = ($vars['node']->type) ? 'node-type-'. $vars['node']->type : '';               // Page has node-type-x, e.g., node-type-page
    $body_classes[] = ($vars['node']->nid) ? 'nid-'. $vars['node']->nid : '';                       // Page has id-x, e.g., id-32
  }
  else {
    $body_classes[] = (arg(0) == 'forum') ? 'forum' : '';                                           // Page is Forum page
  }

// Add any taxonomy terms for node pages
  if (isset($vars['node']->taxonomy)) {
    foreach ($vars['node']->taxonomy as $taxonomy_id => $term_info) {
      $body_classes[] = 'tag-'. $taxonomy_id;                                                       // Page has terms (tag-x)
//      $taxonomy_name = id_safe($term_info->name);
//      if ($taxonomy_name) {
//        $body_classes[] = 'tag-'. $taxonomy_name;                                                 // Page has terms (tag-name)
//      }
    }
  }

// Add unique classes for each page and website section
  if (!$vars['is_front']) {
    $path = drupal_get_path_alias(check_plain($_GET['q']));
    list($section, ) = explode('/', $path, 2);
    $body_classes[] = id_safe('section-' . $section);
    $body_classes[] = id_safe('page-' . $path);
    if (arg(0) == 'node') {
      if (arg(1) == 'add') {
        if ($section == 'node') {
          array_pop($body_classes); // Remove 'section-node'
        }
        $body_classes[] = 'section-node-add'; // Add 'section-node-add'
      }
      elseif (is_numeric(arg(1)) && (arg(2) == 'edit' || arg(2) == 'delete')) {
        if ($section == 'node') {
          array_pop($body_classes); // Remove 'section-node'
        }
        $body_classes[] = 'section-node-' . arg(2); // Add 'section-node-edit' or 'section-node-delete'
      }
    }
  }

// Build array of additional body classes and retrieve custom theme settings
$blockicons = theme_get_setting('blockicons');
  if ($blockicons == '1'){
    $body_classes[] = 'bi32';
  }
  if ($blockicons == '2'){
    $body_classes[] = 'bi48';
  }
$navpos = theme_get_setting('navpos');
  if ($navpos == '0'){
    $body_classes[] = 'ml';
  }
  if ($navpos == '1'){
    $body_classes[] = 'mc';
  }
  if ($navpos == '2'){
    $body_classes[] = 'mr';
  }
$fntsize = theme_get_setting('fntsize');
  if ($fntsize == '0'){
	  $body_classes[] = 'fs0';
  }
  if ($fntsize == '1'){
	  $body_classes[] = 'fs1';
  }
if (theme_get_setting('grid_responsive') == 1 ){
$mob = theme_get_setting('mobile_blocks');
  if ($mob == '1'){
	  $body_classes[] = 'nb1';
  }
  if ($mob == '2'){
	  $body_classes[] = 'nb1 nbl';
  }
  if ($mob == '3'){
	  $body_classes[] = 'nb1 nb2';
  }
  if ($mob == '4'){
	  $body_classes[] = 'nb1 nb2 nbl';
  }
  if ($mob == '5'){
	  $body_classes[] = 'nb1 nb2 nbl nbr';
  }
}
if(theme_get_setting('roundcorners')) {
  $body_classes[] = 'rnd';
}
if(theme_get_setting('pageicons')) {
  $body_classes[] = 'pi';
}
if(theme_get_setting('headerimg')) {
  $body_classes[] = 'himg';
}

// Add Panels classes and lang
  $body_classes[] = (module_exists('panels_page') && (panels_page_get_current())) ? 'panels' : '';  // Page is Panels page
  $body_classes[] = ($vars['language']->language) ? 'lg-'. $vars['language']->language : '';        // Page has lang-x

$siteid = check_plain(theme_get_setting('siteid'));
  $body_classes[] = $siteid;

  $body_classes = array_filter($body_classes);                                                      // Remove empty elements
  $vars['body_classes'] = implode(' ', $body_classes);                                              // Create class list separated by spaces

// Generate menu tree from source of primary links
  if (module_exists('i18nmenu')) {
    $vars['primary_links_tree'] = i18nmenu_translated_tree(variable_get('menu_primary_links_source', 'primary-links'));
    }
    else {
      $vars['primary_links_tree'] = menu_tree(variable_get('menu_primary_links_source', 'primary-links'));
    }


// TNT THEME SETTINGS SECTION
// Display mission statement on all pages
  if (theme_get_setting('mission_statement_pages') == 'all') {
    $vars['mission'] = theme_get_setting('mission', false);
  }

// Hide breadcrumb on all pages
  if (theme_get_setting('breadcrumb_display') == 0) {
    $vars['breadcrumb'] = '';
  }

// Set site title, slogan, mission, page title & separator (unless using Page Title module)
  if (!module_exists('page_title')) {
    $title = t(variable_get('site_name', ''));
    $slogan = t(variable_get('site_slogan', ''));
    $mission = t(variable_get('site_mission', ''));
    $page_title = t(drupal_get_title());
    $title_separator = theme_get_setting('configurable_separator');
    if (drupal_is_front_page()) {                                                // Front page title settings
      switch (theme_get_setting('front_page_title_display')) {
        case 'title_slogan':
          $vars['head_title'] = drupal_set_title(check_plain($title . $title_separator . $slogan));
          break;
        case 'slogan_title':
          $vars['head_title'] = drupal_set_title(check_plain($slogan . $title_separator . $title));
          break;
        case 'title_mission':
          $vars['head_title'] = drupal_set_title(check_plain($title . $title_separator . $mission));
          break;
        case 'custom':
          if (theme_get_setting('page_title_display_custom') !== '') {
            $vars['head_title'] = drupal_set_title(check_plain(t(theme_get_setting('page_title_display_custom'))));
          }
      }
    }
    else {                                                                       // Non-front page title settings
      switch (theme_get_setting('other_page_title_display')) {
        case 'ptitle_slogan':
          $vars['head_title'] = drupal_set_title(check_plain($page_title . $title_separator . $slogan));
          break;
        case 'ptitle_stitle':
          $vars['head_title'] = drupal_set_title(check_plain($page_title . $title_separator . $title));
          break;
        case 'ptitle_smission':
          $vars['head_title'] = drupal_set_title(check_plain($page_title . $title_separator . $mission));
          break;
        case 'ptitle_custom':
          if (theme_get_setting('other_page_title_display_custom') !== '') {
            $vars['head_title'] = drupal_set_title(check_plain($page_title . $title_separator . t(theme_get_setting('other_page_title_display_custom'))));
          }
          break;
        case 'custom':
          if (theme_get_setting('other_page_title_display_custom') !== '') {
            $vars['head_title'] = drupal_set_title(check_plain(t(theme_get_setting('other_page_title_display_custom'))));
          }
      }
    }
    $vars['head_title'] = strip_tags($vars['head_title']);                       // Remove any potential html tags
  }

// Set meta keywords and description (unless using Meta tags module)
  if (!module_exists('nodewords')) {
    if (theme_get_setting('meta_keywords') !== '') {
      $keywords = '<meta name="keywords" content="'. check_plain(theme_get_setting('meta_keywords')) .'" />';
      $vars['head'] .= $keywords ."\n";
    }
    if (theme_get_setting('meta_description') !== '') {
      $keywords = '<meta name="description" content="'. check_plain(theme_get_setting('meta_description')) .'" />';
      $vars['head'] .= $keywords ."\n";
    }
  }

// Use grouped import technique for more than 30 un-aggregated stylesheets (css limit fix for IE)
  $css = drupal_add_css();
  if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)) {
    if (theme_get_setting('fix_css_limit') && !variable_get('preprocess_css', FALSE) && zeropoint_css_count($css) > 26) {
      $styles = '';
      $suffix = "\n".'</style>'."\n";
      foreach ($css as $media => $types) {
        $prefix = '<style type="text/css" media="'. $media .'">'."\n";
        $imports = array();
        foreach ($types as $files) {
          foreach ($files as $file => $preprocess) {
            $imports[] = '@import "'. base_path() . $file .'";';
            if (count($imports) == 30) {
              $styles .= $prefix . implode("\n", $imports) . $suffix;
              $imports = array();
            }
          }
        }
        $styles .= (count($imports) > 0) ? ($prefix . implode("\n", $imports) . $suffix) : '';
      }
      $vars['styles'] = $styles;
    }
    else {
      $vars['styles'] = drupal_get_css();                                   // Use normal link technique
    }
  }

$devlink = theme_get_setting('devlink');
  if ($devlink == '0'){
	  $dvlk = 'byy';
  }
  if ($devlink == '1'){
	  $dvlk = 'by';
  }
  $momo = array();
  $momo[] = ($vars['node']->type) ? $vars['node']->type .' | ' : '';
  $momo = array_filter($momo);
  $vars['momo'] = implode(' ', $momo);
  $vars['closure'] .= ($vars['is_front']) ? '<div class="'.$dvlk.'"><a href="http://www.radut.net">by Dr. Radut</a></div>' : '<div class="'.$dvlk.'"><a href="http://www.radut.net">'.$vars['momo'].'by Dr. Radut</a></div>';
}


function zeropoint_preprocess_block(&$vars) {
// Add regions with themed blocks (e.g., left, right) to $themed_regions array and retrieve custom theme settings
$themedblocks = theme_get_setting('themedblocks');
  if ($themedblocks == '0'){
  $themed_regions = array('left','right');
}
  if ($themedblocks == '1'){
  $themed_regions = array('left','right','user1','user2','user3','user4','user5','user6','user7','user8');
}
  if (is_array($themed_regions))
  $vars['themed_block'] = (in_array($vars['block']->region, $themed_regions)) ? TRUE : FALSE;
  else $vars['themed_block'] = FALSE;
}


function zeropoint_preprocess_node(&$vars) {
// Build array of handy node classes
  $node_classes = array();
  $node_classes[] = $vars['zebra'];                                              // Node is odd or even
  $node_classes[] = (!$vars['node']->status) ? 'node-unpublished' : '';          // Node is unpublished
  $node_classes[] = ($vars['sticky']) ? 'sticky' : '';                           // Node is sticky
  $node_classes[] = (isset($vars['node']->teaser)) ? 'teaser' : 'full-node';     // Node is teaser or full-node
  $node_classes[] = 'node-type-'. $vars['node']->type;                           // Node is type-x, e.g., node-type-page

// Add any taxonomy terms for node teasers
  if ($vars['teaser'] && isset($vars['taxonomy'])) {
    foreach ($vars['taxonomy'] as $taxonomy_id_string => $term_info) {
//      $taxonomy_id = array_pop(explode('_', $taxonomy_id_string));
      $node_classes[] = 'tag-'. $taxonomy_id;                                    // Node teaser has terms (tag-x)
//      $taxonomy_name = id_safe($term_info['title']);
//      if ($taxonomy_name) {
//        $node_classes[] = 'tag-'. $taxonomy_name;                              // Node teaser has terms (tag-name)
//      }
    }
  }

  $node_classes = array_filter($node_classes);                                  // Remove empty elements
  $vars['node_classes'] = implode(' ', $node_classes);                          // Implode class list with spaces

// Add node regions
  $vars['node_middle'] = theme('blocks', 'node_middle');
  $vars['node_bottom'] = theme('blocks', 'node_bottom');

// Render Ubercart fields into separate variables for node-product.tpl.php
if (module_exists('uc_product') && uc_product_is_product($vars) && $vars['template_files'][0] == 'node-product') {
  $node = node_build_content(node_load($vars['nid']));
  $vars['uc_image'] = drupal_render($node->content['image']);
  $vars['uc_body'] = drupal_render($node->content['body']);
  $vars['uc_display_price'] = drupal_render($node->content['display_price']);
  $vars['uc_add_to_cart'] = drupal_render($node->content['add_to_cart']);
  $vars['uc_weight'] = drupal_render($node->content['weight']);
  $vars['uc_dimensions'] = drupal_render($node->content['dimensions']);
  $vars['uc_model'] = drupal_render($node->content['model']);
  $vars['uc_list_price'] = drupal_render($node->content['list_price']);
  $vars['uc_sell_price'] = drupal_render($node->content['sell_price']);
  $vars['uc_cost'] = drupal_render($node->content['cost']);
  $vars['uc_additional'] = drupal_render($node->content);
}


// Node Theme Settings

// Date & author
  if (!module_exists('submitted_by')) {
    $date = t('') . format_date($vars['node']->created, 'medium');               // Format date as small, medium, or large
    $author = theme('username', $vars['node']);
    $author_only_separator = t('');
    $author_date_separator = t(' &#151; ');
    $submitted_by_content_type = (theme_get_setting('submitted_by_enable_content_type') == 1) ? $vars['node']->type : 'default';
    $date_setting = (theme_get_setting('submitted_by_date_'. $submitted_by_content_type) == 1);
    $author_setting = (theme_get_setting('submitted_by_author_'. $submitted_by_content_type) == 1);
    $author_separator = ($date_setting) ? $author_date_separator : $author_only_separator;
    $date_author = ($date_setting) ? $date : '';
    $date_author .= ($author_setting) ? $author_separator . $author : '';
    $vars['submitted'] = $date_author;
  }

// Taxonomy
  $taxonomy_content_type = (theme_get_setting('taxonomy_enable_content_type') == 1) ? $vars['node']->type : 'default';
  $taxonomy_display = theme_get_setting('taxonomy_display_'. $taxonomy_content_type);
  $taxonomy_format = theme_get_setting('taxonomy_format_'. $taxonomy_content_type);
  if ((module_exists('taxonomy')) && ($taxonomy_display == 'all' || ($taxonomy_display == 'only' && $vars['page']))) {
    $vocabularies = taxonomy_get_vocabularies($vars['node']->type);
    $output = '';
    $term_delimiter = ' | ';
    foreach ($vocabularies as $vocabulary) {
      if (theme_get_setting('taxonomy_vocab_hide_'. $taxonomy_content_type .'_'. $vocabulary->vid) != 1) {
        $terms = taxonomy_node_get_terms_by_vocabulary($vars['node'], $vocabulary->vid);
        if ($terms) {
          $term_items = '';
          foreach ($terms as $term) {                        // Build vocabulary term items
            if (module_exists('i18nstrings')) {
              $term_link = l(i18nstrings("taxonomy:term:$term->tid:name", $term->name), taxonomy_term_path($term), array('attributes' => array('rel' => 'tag', 'title' => strip_tags($term->description))));
              }
              else {
                $term_link = l($term->name, taxonomy_term_path($term), array('attributes' => array('rel' => 'tag', 'title' => strip_tags($term->description))));
              }
            $term_items .= '<li class="vocab-term">'. $term_link . $term_delimiter .'</li>';
          }
          if ($taxonomy_format == 'vocab') {                 // Add vocabulary labels if separate
            $output .= '<li class="vocab vocab-'. $vocabulary->vid .'"><span class="vocab-name">'. t($vocabulary->name) .':</span> <ul class="vocab-list">';
            $output .= substr_replace($term_items, '</li>', -(strlen($term_delimiter) + 5)) .'</ul></li>';
          }
          else {
            $output .= $term_items;
          }
        }
      }
    }
    if ($output != '') {
      $output = ($taxonomy_format == 'list') ? substr_replace($output, '</li>', -(strlen($term_delimiter) + 5)) : $output;
      $output = '<ul class="taxonomy">'. $output .'</ul>';
    }
    $vars['terms'] = $output;
  }
  else {
    $vars['terms'] = '';
  }
}


function zeropoint_preprocess_comment(&$vars) {
  global $user;
// Build array of handy comment classes
  $comment_classes = array();
  static $comment_odd = TRUE;                                                                             // Comment is odd or even
  $comment_classes[] = $comment_odd ? 'odd' : 'even';
  $comment_odd = !$comment_odd;
  $comment_classes[] = ($vars['comment']->status == COMMENT_NOT_PUBLISHED) ? 'comment-unpublished' : '';  // Comment is unpublished
  $comment_classes[] = ($vars['comment']->new) ? 'comment-new' : '';                                      // Comment is new
  $comment_classes[] = ($vars['comment']->uid == 0) ? 'comment-by-anon' : '';                             // Comment is by anonymous user
  $comment_classes[] = ($user->uid && $vars['comment']->uid == $user->uid) ? 'comment-mine' : '';         // Comment is by current user
  $node = node_load($vars['comment']->nid);                                                               // Comment is by node author
  $vars['author_comment'] = ($vars['comment']->uid == $node->uid) ? TRUE : FALSE;
  $comment_classes[] = ($vars['author_comment']) ? 'comment-by-author' : '';
  $comment_classes = array_filter($comment_classes);                                                      // Remove empty elements
  $vars['comment_classes'] = implode(' ', $comment_classes);                                              // Create class list separated by spaces
  // Date & author
  $submitted_by = t('') .'<span class="comment-name">'.  theme('username', $vars['comment']) .'</span>';
  $submitted_by .= t(' - ') .'<span class="comment-date">'.  format_date($vars['comment']->timestamp, 'small') .'</span>';  // Format date as small, medium, or large
  $vars['submitted'] = $submitted_by;
}


/**
 * Set defaults for comments display
 * (Requires comment-wrapper.tpl.php file in theme directory)
 */
function zeropoint_preprocess_comment_wrapper(&$vars) {
  $vars['display_mode']  = COMMENT_MODE_FLAT_EXPANDED;
  $vars['display_order'] = COMMENT_ORDER_OLDEST_FIRST;
  $vars['comment_controls_state'] = COMMENT_CONTROLS_HIDDEN;
}


/**
 * Adds a class for the style of view
 * (e.g., node, teaser, list, table, etc.)
 * (Requires views-view.tpl.php file in theme directory)
 */
function zeropoint_preprocess_views_view(&$vars) {
  $vars['css_name'] = $vars['css_name'] .' view-style-'. views_css_safe(strtolower($vars['view']->type));
}


/**
 * Modify search results based on theme settings
 */
function zeropoint_preprocess_search_result(&$variables) {
  static $search_zebra = 'even';

  $search_zebra = ($search_zebra == 'even') ? 'odd' : 'even';
  $variables['search_zebra'] = $search_zebra;
  $result = $variables['result'];
  $variables['url'] = check_url($result['link']);
  $variables['title'] = check_plain($result['title']);

// Check for existence. User search does not include snippets.
  $variables['snippet'] = '';
  if (isset($result['snippet']) && theme_get_setting('search_snippet')) {
    $variables['snippet'] = $result['snippet'];
  }

  $info = array();
  if (!empty($result['type']) && theme_get_setting('search_info_type')) {
    $info['type'] = check_plain($result['type']);
  }
  if (!empty($result['user']) && theme_get_setting('search_info_user')) {
    $info['user'] = $result['user'];
  }
  if (!empty($result['date']) && theme_get_setting('search_info_date')) {
    $info['date'] = format_date($result['date'], 'small');
  }
  if (isset($result['extra']) && is_array($result['extra'])) {
    // $info = array_merge($info, $result['extra']);  Drupal bug?  [extra] array not keyed with 'comment' & 'upload'
    if (!empty($result['extra'][0]) && theme_get_setting('search_info_comment')) {
      $info['comment'] = $result['extra'][0];
    }
    if (!empty($result['extra'][1]) && theme_get_setting('search_info_upload')) {
      $info['upload'] = $result['extra'][1];
    }
  }

// Provide separated and grouped meta information.
  $variables['info_split'] = $info;
  $variables['info'] = implode(' - ', $info);

// Provide alternate search result template.
  $variables['template_files'][] = 'search-result-'. $variables['type'];
}


/**
 * Hide or show username '(not verified)' text
 */
function zeropoint_username($object) {
  if ((!$object->uid) && $object->name) {
    $output = (!empty($object->homepage)) ? l($object->name, $object->homepage, array('attributes' => array('rel' => 'nofollow'))) : check_plain($object->name);
    $output .= (theme_get_setting('user_notverified_display') == 1) ? ' ('. t('not verified') .')' : '';
  }
  else {
    $output = theme_username($object);
  }
  return $output;
}


/**
 * Set default form file input size
 */
function zeropoint_file($element) {
  $element['#size'] = ($element['#size'] > 40) ? 40 : $element['#size'];
  return theme_file($element);
}


/**
 * Count the total number of CSS files in $vars['css']
 */
function zeropoint_css_count($array) {
  $count = 0;
  foreach ($array as $item) {
    $count = (is_array($item)) ? $count + zeropoint_css_count($item) : $count + 1;
  }
  return $count;
}


/**
 * Creates a link with prefix and suffix text
 *
 * @param $prefix
 *   The text to prefix the link.
 * @param $suffix
 *   The text to suffix the link.
 * @param $text
 *   The text to be enclosed with the anchor tag.
 * @param $path
 *   The Drupal path being linked to, such as "admin/content/node". Can be an external
 *   or internal URL.
 *     - If you provide the full URL, it will be considered an
 *   external URL.
 *     - If you provide only the path (e.g. "admin/content/node"), it is considered an
 *   internal link. In this case, it must be a system URL as the url() function
 *   will generate the alias.
 * @param $options
 *   An associative array that contains the following other arrays and values
 *     @param $attributes
 *       An associative array of HTML attributes to apply to the anchor tag.
 *     @param $query
 *       A query string to append to the link.
 *     @param $fragment
 *       A fragment identifier (named anchor) to append to the link.
 *     @param $absolute
 *       Whether to force the output to be an absolute link (beginning with http:).
 *       Useful for links that will be displayed outside the site, such as in an RSS
 *       feed.
 *     @param $html
 *       Whether the title is HTML or not (plain text)
 * @return
 *   an HTML string containing a link to the given path.
 */
function zeropoint_themesettings_link($prefix, $suffix, $text, $path, $options) {
  return $prefix . (($text) ? l($text, $path, $options) : '') . $suffix;
}


/**
 * Breadcrumb override
 */
function zeropoint_breadcrumb($breadcrumb) {
  if (!empty($breadcrumb)) {
    $breadcrumb[] = drupal_get_title();  // full breadcrumb ( › = â€º , » = &#187; &raquo;)
    return '<div class="breadcrumb">'. implode(' &raquo; ', $breadcrumb) .'</div>';
  }
}


/**
 * Converts a string to a suitable html ID attribute.
 *
 * http://www.w3.org/TR/html4/struct/global.html#h-7.5.2 specifies what makes a
 * valid ID attribute in HTML. This function:
 *
 * - Ensure an ID starts with an alpha character by optionally adding an 'id'.
 * - Replaces any character except A-Z, numbers, and underscores with dashes.
 * - Converts entire string to lowercase.
 *
 * @param $string
 *   The string
 * @return
 *   The converted string
 */
function id_safe($string) {
  // Replace with dashes anything that isn't A-Z, numbers, dashes, or underscores.
  $string = strtolower(preg_replace('/[^a-zA-Z0-9_-]+/', '-', $string));
  // If the first character is not a-z, add 'n' in front.
  if (!ctype_lower($string{0})) { // Don't use ctype_alpha since its locale aware.
    $string = 'id' . $string;
  }
  return $string;
}


// Quick fix for the validation error: 'ID "edit-submit" already defined'
$elementCountForHack = 0;
function zeropoint_submit($element) {
	global $elementCountForHack;
	return str_replace('edit-submit', 'edit-submit-' . ++$elementCountForHack, theme('button', $element));
}


/**
 * Pure Grid settings
 */
function wrapper_width() {
  $wrapper = check_plain(theme_get_setting('wrapper'));
    return ' style="max-width:' . $wrapper . ';"';
}

function section_class($variables, $onefour=true){
  if($onefour) {
    $cols = (bool) $variables['user1'] + (bool) $variables['user2'] + (bool) $variables['user3'] + (bool) $variables['user4'];
  } else {
    $cols = (bool) $variables['user5'] + (bool) $variables['user6'] + (bool) $variables['user7'] + (bool) $variables['user8'];
  }
  if((theme_get_setting('grid_responsive') == '1') && ((preg_match('/(?i)msie [2-7]/',$_SERVER['HTTP_USER_AGENT']))) == FALSE) {
    if ($cols == '1') {
      return 'pure-u-1';
    }
    if ($cols == '2') {
      return 'pure-u-1 pure-u-sm-1-2';
    }
    if ($cols == '3') {
      return 'pure-u-1 pure-u-md-1-3';
    }
    if ($cols == '4') {
      return 'pure-u-1 pure-u-sm-1-2 pure-u-md-1-4';
    }
  } else {
      return 'pure-u-1-'.$cols;
    }
}

function first_class(){
  $w1 = (theme_get_setting('first_width'));
  if((theme_get_setting('grid_responsive') == '1') && ((preg_match('/(?i)msie [2-7]/',$_SERVER['HTTP_USER_AGENT']))) == FALSE) {
    return 'pure-u-1 pure-u-md-'.$w1.'-24';
  } else {
    return 'pure-u-'.$w1.'-24';
  }
}

function second_class(){
  $w2 = (theme_get_setting('second_width'));
  if((theme_get_setting('grid_responsive') == '1') && ((preg_match('/(?i)msie [2-7]/',$_SERVER['HTTP_USER_AGENT']))) == FALSE) {
    return 'pure-u-1 pure-u-md-'.$w2.'-24';
  } else {
    return 'pure-u-'.$w2.'-24';
  }
}

function cont_class($variables){
  $w1 = (theme_get_setting('first_width'));
  $w2 = (theme_get_setting('second_width'));
  $cont1 = 24 - $w1;
  $cont2 = 24 - $w2;
  $cont0 = 24 - ($w1+$w2);
  if (($variables['left']) && (!$variables['right'])) {
    if((theme_get_setting('grid_responsive') == '1') && ((preg_match('/(?i)msie [2-7]/',$_SERVER['HTTP_USER_AGENT']))) == FALSE) {
      return 'pure-u-1 pure-u-md-'.$cont1.'-24';
    } else {
      return 'pure-u-'.$cont1.'-24';
    }
  }
  if ((!$variables['left']) && ($variables['right'])) {
    if((theme_get_setting('grid_responsive') == '1') && ((preg_match('/(?i)msie [2-7]/',$_SERVER['HTTP_USER_AGENT']))) == FALSE) {
      return 'pure-u-1 pure-u-md-'.$cont2.'-24';
    } else {
      return 'pure-u-'.$cont2.'-24';
    }
  }
  if (($variables['left']) && ($variables['right'])) {
    if((theme_get_setting('grid_responsive') == '1') && ((preg_match('/(?i)msie [2-7]/',$_SERVER['HTTP_USER_AGENT']))) == FALSE) {
      return 'pure-u-1 pure-u-md-'.$cont0.'-24';
    } else {
      return 'pure-u-'.$cont0.'-24';
    }
  } else {
    if((theme_get_setting('grid_responsive') == '1') && ((preg_match('/(?i)msie [2-7]/',$_SERVER['HTTP_USER_AGENT']))) == FALSE) {
      return 'pure-u-1 pure-u-md-24-24';
    } else {
      return 'pure-u-24-24';
    }
  }
}

function resp_class(){
  if(theme_get_setting('grid_responsive') == '1') {
    return 'pure-u-1 pure-u-md-';
  } else {
    return 'pure-u-';
  }
}


/**
 * Add pure-img class to images to make them fit within their fluid parent wrapper while maintaining aspect ratio.
 */
function zeropoint_image($path, $alt = '', $title = '', $attributes = NULL, $getsize = TRUE) {
  if (!$getsize || (is_file($path) && (list($width, $height, $type, $image_attributes) = @getimagesize($path)))) {
    $attributes['class']='pure-img '.(isset($attributes['class'])?$attributes['class']:'');
    $attributes = drupal_attributes($attributes);
    $url = (url($path) == $path) ? $path : (base_path() . $path);
    return '<img src="' . check_url($url) . '" alt="' . check_plain($alt) . '" title="' . check_plain($title) . '" ' . (isset($image_attributes) ? $image_attributes : '') . $attributes . ' />';
  }
}


/**
 * Returns HTML for a form element and buttons.
 */
function zeropoint_form($element) {
  // Anonymous div to satisfy XHTML compliance.
  $action = $element['#action'] ? 'action="' . check_url($element['#action']) . '" ' : '';
  $element['#attributes']['class']='pure-form '.(isset($element['#attributes']['class'])?$element['#attributes']['class']:'');
  return '<form ' . $action . ' accept-charset="UTF-8" method="' . $element['#method'] . '" id="' . $element['#id'] . '"' . drupal_attributes($element['#attributes']) . ">\n<div>" . $element['#children'] . "\n</div></form>\n";
}

function zeropoint_button($element) {
  // Make sure not to overwrite classes.
  if (isset($element['#attributes']['class'])) {
    $element['#attributes']['class'] = 'pure-button form-' . $element['#button_type'] . ' ' . $element['#attributes']['class'];
  }
  else {
    $element['#attributes']['class'] = 'pure-button form-' . $element['#button_type'];
  }

  return '<input type="submit" ' . (empty($element['#name']) ? '' : 'name="' . $element['#name'] . '" ') . 'id="' . $element['#id'] . '" value="' . check_plain($element['#value']) . '" ' . drupal_attributes($element['#attributes']) . " />\n";
}

function zeropoint_image_button($element) {
  // Make sure not to overwrite classes.
  if (isset($element['#attributes']['class'])) {
    $element['#attributes']['class'] = 'pure-button form-' . $element['#button_type'] . ' ' . $element['#attributes']['class'];
  }
  else {
    $element['#attributes']['class'] = 'pure-button form-' . $element['#button_type'];
  }

  return '<input type="image" name="' . $element['#name'] . '" ' . (!empty($element['#value']) ? ('value="' . check_plain($element['#value']) . '" ') : '') . 'id="' . $element['#id'] . '" ' . drupal_attributes($element['#attributes']) . ' src="' . base_path() . $element['#src'] . '" ' . (!empty($element['#title']) ? 'alt="' . check_plain($element['#title']) . '" title="' . check_plain($element['#title']) . '" ' : '') . "/>\n";
}


/**
 * Theme's pager
 */
function zeropoint_pager($tags = array(), $limit = 10, $element = 0, $parameters = array(), $quantity = 9) {
  global $pager_page_array, $pager_total;

  // Calculate various markers within this pager piece:
  // Middle is used to "center" pages around the current page.
  $pager_middle = ceil($quantity / 2);
  // current is the page we are currently paged to
  $pager_current = $pager_page_array[$element] + 1;
  // first is the first page listed by this pager piece (re quantity)
  $pager_first = $pager_current - $pager_middle + 1;
  // last is the last page listed by this pager piece (re quantity)
  $pager_last = $pager_current + $quantity - $pager_middle;
  // max is the maximum page number
  $pager_max = $pager_total[$element];
  // End of marker calculations.

  // Prepare for generation loop.
  $i = $pager_first;
  if ($pager_last > $pager_max) {
    // Adjust "center" if at end of query.
    $i = $i + ($pager_max - $pager_last);
    $pager_last = $pager_max;
  }
  if ($i <= 0) {
    // Adjust "center" if at start of query.
    $pager_last = $pager_last + (1 - $i);
    $i = 1;
  }
  // End of generation loop preparation.

  $li_first = theme('pager_first', (isset($tags[0]) ? $tags[0] : t('« first')), $limit, $element, $parameters);
  $li_previous = theme('pager_previous', (isset($tags[1]) ? $tags[1] : t('‹ previous')), $limit, $element, 1, $parameters);
  $li_next = theme('pager_next', (isset($tags[3]) ? $tags[3] : t('next ›')), $limit, $element, 1, $parameters);
  $li_last = theme('pager_last', (isset($tags[4]) ? $tags[4] : t('last »')), $limit, $element, $parameters);

  if ($pager_total[$element] > 1) {
    if ($li_first) {
      $items[] = array(
        'class' => 'pager-first pure-button',
        'data' => $li_first,
      );
    }
    if ($li_previous) {
      $items[] = array(
        'class' => 'pager-previous pure-button',
        'data' => $li_previous,
      );
    }

    // When there is more than one page, create the pager list.
    if ($i != $pager_max) {
      if ($i > 1) {
        $items[] = array(
          'class' => 'pager-ellipsis pure-button',
          'data' => '…',
        );
      }
      // Now generate the actual pager piece.
      for (; $i <= $pager_last && $i <= $pager_max; $i++) {
        if ($i < $pager_current) {
          $items[] = array(
            'class' => 'pager-item pure-button',
            'data' => theme('pager_previous', $i, $limit, $element, ($pager_current - $i), $parameters),
          );
        }
        if ($i == $pager_current) {
          $items[] = array(
            'class' => 'pager-current pure-button pure-button-selected',
            'data' => $i,
          );
        }
        if ($i > $pager_current) {
          $items[] = array(
            'class' => 'pager-item pure-button',
            'data' => theme('pager_next', $i, $limit, $element, ($i - $pager_current), $parameters),
          );
        }
      }
      if ($i < $pager_max) {
        $items[] = array(
          'class' => 'pager-ellipsis pure-button',
          'data' => '…',
        );
      }
    }
    // End generation.
    if ($li_next) {
      $items[] = array(
        'class' => 'pager-next pure-button',
        'data' => $li_next,
      );
    }
    if ($li_last) {
      $items[] = array(
        'class' => 'pager-last pure-button',
        'data' => $li_last,
      );
    }
    return theme('item_list', $items, NULL, 'ul', array('class' => 'pager pure-paginator'));
  }
}


/**
 * Overrides theme_menu_tree().
 */
/*
function zeropoint_menu_tree($tree) {
  return '<ul class="pure-menu-list">' . $tree . '</ul>';
}*/


/*
* Theme's main navigation menu
*/
function zeropoint_links__system_main_menu($vars, $is_child=false){
  $html = '<ul class="'.($is_child ? 'pure-menu-children': 'pure-menu-list').'">';

  foreach($vars as $link){
    // Test for localization options and apply them if they exist.
    if (isset($link['link']['localized_options']['attributes']) && is_array($link['link']['localized_options']['attributes'])) {
      $link['link']['options']['attributes'] = array_merge($link['link']['localized_options']['attributes'], $link['link']['options']['attributes']);
    }
    // Output html for drop-down menu.
    if(empty($link['link']['title']) || $link['link']['hidden']==1)
      continue;
    else{
      $link['link']['options']['attributes']['class'] = 'pure-menu-link menu-' . $link['link']['mlid'];

      if(!empty($link['below'])){
        $html .= '<li class="pure-menu-item pure-menu-has-children pure-menu-allow-hover">';
        $html .= l($link['link']['title'], $link['link']['href'], array('attributes' => $link['link']['options']['attributes']));
        $html .= zeropoint_links__system_main_menu($link['below'], true);
        $html .= '</li>';
      }
      else
        $html .= '<li class="pure-menu-item">'.l($link['link']['title'], $link['link']['href'], array('attributes' => $link['link']['options']['attributes'])).'</li>';
    }
  }

  $html .= "</ul>\r\n";
  return $html;
}

function zeropoint_main_menu(){
  $menu = menu_tree_page_data(variable_get('menu_primary_links_source', 'primary-links'));
  if(module_exists('i18nmenu')){
    i18nmenu_localize_tree($menu);
  }
  return zeropoint_links__system_main_menu($menu);
}


/**
 * Other theme settings
 */
function login_links(){
  global $user;
  $loginlinks = theme_get_setting('loginlinks');
  if ($loginlinks == '1'){
    if ($user->uid != 0) {
      print '<h2 class="element-invisible">'.t('Login links').'</h2><ul class="links inline"><li class="uin first"><a href="' .url('user/'.$user->uid). '">' .$user->name. '</a></li><li class="uout"><a href="' .url('logout'). '">' .t('Logout'). '</a></li></ul>';
    }
    else {
      print '<h2 class="element-invisible">'.t('Login links').'</h2><ul class="links inline"><li class="ulog first"><a href="' .url('user'). '" rel="nofollow">' .t('Login'). '</a></li><li class="ureg"><a href="' .url('user/register'). '" rel="nofollow">' .t('Register'). '</a></li></ul>';
    }
  }
}

function divider() {
  $divider = theme_get_setting('themedblocks');
    if ($divider == '0' || $divider == '3') {
      return 'divider';
  }
}

