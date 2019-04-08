<?php

/**
 * Theme setting defaults
 */
function zeropoint_default_theme_settings() {
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

// Add site-wide theme settings
  $defaults = array_merge($defaults, theme_get_settings());

// Set initial content-type-specific settings to defaults
  $node_types = node_get_types('names');
  foreach ($node_types as $type => $name) {
    $defaults["taxonomy_display_{$type}"]    = $defaults['taxonomy_display_default'];
    $defaults["taxonomy_format_{$type}"]     = $defaults['taxonomy_format_default'];
    $defaults["submitted_by_author_{$type}"] = $defaults['submitted_by_author_default'];
    $defaults["submitted_by_date_{$type}"]   = $defaults['submitted_by_date_default'];
  }

  return $defaults;
}


/**
 * Theme setting initialization
 * if updated, unsaved, or registry rebuild mode
 */
function zeropoint_initialize_theme_settings($theme_name) {
  $theme_settings = theme_get_settings($theme_name);
  if (is_null($theme_settings['fix_css_limit']) || $theme_settings['rebuild_registry'] == 1) {
// Rebuild theme registry & notify user
    if($theme_settings['rebuild_registry'] == 1) {
      drupal_rebuild_theme_registry();
      drupal_set_message(t('Theme registry rebuild completed. <a href="!link">Turn off</a> this feature for production websites.', array('!link' => url('admin/build/themes/settings/' . $GLOBALS['theme']))), 'warning');
    }
  
// Retrieve saved or site-wide theme settings
    $theme_setting_name = str_replace('/', '_', 'theme_'. $theme_name .'_settings');
    $settings = (variable_get($theme_setting_name, FALSE)) ? theme_get_settings($theme_name) : theme_get_settings();
  
// Skip toggle_node_info_ settings
    if (module_exists('node')) {
      foreach (node_get_types() as $type => $name) {
        unset($settings['toggle_node_info_'. $type]);
      }
    }
  
// Retrieve default theme settings
    $defaults = zeropoint_default_theme_settings();
  
// Set combined default & saved theme settings
    variable_set($theme_setting_name, array_merge($defaults, $settings));
  
// Force theme settings refresh
    theme_get_setting('', TRUE);
  }
}


/**
* Implementation of THEMEHOOK_settings() function.
*
* @param $saved_settings
*   array An array of saved settings for this theme.
* @return
*   array A form array.
*/
function zeropoint_settings($saved_settings) {
  global $base_url;

// Retrieve & combine default and saved theme settings
  $defaults = zeropoint_default_theme_settings();
  $settings = array_merge($defaults, $saved_settings);


// Create theme settings form widgets using Forms API


// Pure Grid settings
  $form['tnt_container']['puregrid'] = array(
    '#type' => 'fieldset',
    '#title' => t('Pure Grid settings'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  $form['tnt_container']['puregrid']['css_zone'] = array(
    '#type'          => 'checkbox',
    '#title'         => t('<b>Use Yahoo CDN</b> to serve the responsive CSS files. If you use https leave this option unchecked to load any responsive CSS files locally.'),
    '#description'   => t(''),
    '#default_value' => $settings['css_zone']
  );
  $form['tnt_container']['puregrid']['wrapper'] = array(
    '#type' => 'textfield',
    '#title' => t('Layout width'),
   	'#description' => t('Set the width of the layout in <b>em</b> (preferably), px or percent. Leave empty or 100% for fluid layout.'),
    '#default_value' => $settings['wrapper'],
    '#size' => 10,
	);
  $form['tnt_container']['puregrid']['first_width'] = array(
    '#type' => 'select',
    '#title' => t('First (left) sidebar width'),
   	'#description' => t('Set the width of the first (left) sidebar.'),
    '#default_value' => $settings['first_width'],
    '#options' => array(
      4 => t('narrow'),
      5 => t('NORMAL'),
      6 => t('wide'),
      7 => t('wider'),
    ),
	);
  $form['tnt_container']['puregrid']['second_width'] = array(
    '#type' => 'select',
    '#title' => t('Second (right) sidebar width'),
   	'#description' => t('Set the width of the second (right) sidebar.'),
    '#default_value' => $settings['second_width'],
    '#options' => array(
      4 => t('narrow'),
      5 => t('NORMAL'),
      6 => t('wide'),
      7 => t('wider'),
    ),
	);
  $form['tnt_container']['puregrid']['grid_responsive'] = array(
    '#type'          => 'select',
    '#title'         => t('Non-Responsive/Responsive Grid'),
    '#description'   => t(''),
    '#default_value' => $settings['grid_responsive'],
    '#options' => array(
      0 => t('Non-Responsive'),
      1 => t('Responsive'),
    ),
  );
  $form['tnt_container']['puregrid']['mobile_blocks'] = array(
    '#type' => 'select',
    '#title' => t('Hide blocks on mobile devices'),
   	'#description' => t('If the theme is responsive and there are many blocks you may want to hide some of them when on mobile devices.'),
    '#default_value' => $settings['mobile_blocks'],
    '#options' => array(
      0 => t('Show all blocks'),
      1 => t('Hide blocks on user regions 1-4'),
      2 => t('Hide blocks on user regions 1-4 and left sidebar'),
      3 => t('Hide blocks on all user regions'),
      4 => t('Hide blocks on all user regions and left sidebar'),
      5 => t('Hide blocks on all user regions and both sidebars'),
    ),
	);

// Layout Settings
  $form['tnt_container']['layout_settings'] = array(
    '#type' => 'fieldset',
    '#title' => t('Layout settings'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#attributes' => array('class' => 'layout_settings'),
  );
  $form['tnt_container']['layout_settings']['style'] = array(
    '#type' => 'select',
    '#title' => t('Style'),
    '#default_value' => $settings['style'],
    '#options' => array(
      'grey' => t('0 Point'),
      'sky' => t('Sky'),
      'nature' => t('Nature'),
      'ivy' => t('Ivy'),
      'ink' => t('Ink'),
      'orange' => t('Orange'),
      'sangue' => t('Sangue'),
      'lime' => t('Lime'),
      'themer' => t('- Themer -'),
    ),
  );
  $form['tnt_container']['layout_settings']['themedblocks'] = array(
    '#type' => 'select',
    '#title' => t('Themed blocks'),
    '#default_value' => $settings['themedblocks'],
    '#options' => array(
      0 => t('Sidebars only'),
      1 => t('Sidebars + User regions'),
      2 => t('User regions only'),
      3 => t('None'),
    )
  );
  $form['tnt_container']['layout_settings']['blockicons'] = array(
    '#type' => 'select',
    '#title' => t('Block icons'),
    '#default_value' => $settings['blockicons'],
    '#options' => array(
      0 => t('No'),
      1 => t('Yes (32x32 pixels)'),
      2 => t('Yes (48x48 pixels)'),
    )
  );
  $form['tnt_container']['layout_settings']['pageicons'] = array(
    '#type' => 'checkbox',
    '#title' => t('Page icons'),
    '#default_value' => $settings['pageicons'],
  );
  $form['tnt_container']['layout_settings']['navpos'] = array(
    '#type' => 'select',
    '#title' => t('Drop-down and secondary menus position'),
    '#default_value' => $settings['navpos'],
    '#options' => array(
      0 => t('Left'),
      1 => t('Center'),
      2 => t('Right'),
    )
  );
  $form['tnt_container']['layout_settings']['menu2'] = array(
    '#type' => 'checkbox',
    '#title' => t('Duplicate the Main Menu at the bottom of the page.'),
    '#default_value' => $settings['menu2'],
  );
  $form['tnt_container']['layout_settings']['fntsize'] = array(
    '#type' => 'select',
    '#title' => t('Font size'),
    '#default_value' => $settings['fntsize'],
    '#options' => array(
      0 => t('Normal'),
      1 => t('Large'),
    )
  );
  $form['tnt_container']['layout_settings']['roundcorners'] = array(
    '#type' => 'checkbox',
    '#title' => t('Rounded corners'),
    '#description' => t('Some page elements (mission, comments, search, blocks) and main menu will have rounded corners.'),
    '#default_value' => $settings['roundcorners'],
  );
  $form['tnt_container']['layout_settings']['headerimg'] = array(
    '#type' => 'checkbox',
    '#title' => t('Header image rotator'),
    '#description' => t('Rotates images in the _custom/headerimg folder.'),
    '#default_value' => $settings['headerimg'],
  );
  $form['tnt_container']['layout_settings']['loginlinks'] = array(
    '#type' => 'checkbox',
    '#title' => t('Login/register links'),
    '#default_value' => $settings['loginlinks'],
  );
  $form['tnt_container']['layout_settings']['devlink'] = array(
    '#type' => 'checkbox',
    '#title' => t('Developer link'),
    '#default_value' => $settings['devlink'],
  );

// General Settings
  $form['tnt_container']['general_settings'] = array(
    '#type' => 'fieldset',
    '#title' => t('General settings'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#attributes' => array('class' => 'general_settings'),
  );

// Mission Statement
  $form['tnt_container']['general_settings']['mission_statement'] = array(
    '#type' => 'fieldset',
    '#title' => t('Mission statement'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  $form['tnt_container']['general_settings']['mission_statement']['mission_statement_pages'] = array(
    '#type' => 'radios',
    '#title' => t('Where should your mission statement be displayed?'),
    '#default_value' => $settings['mission_statement_pages'],
    '#options' => array(
      'home' => t('Display mission statement only on front page'),
      'all' => t('Display mission statement on all pages'),
    ),
  );

// Breadcrumb
  $form['tnt_container']['general_settings']['breadcrumb'] = array(
    '#type' => 'fieldset',
    '#title' => t('Breadcrumb'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  $form['tnt_container']['general_settings']['breadcrumb']['breadcrumb_display'] = array(
    '#type' => 'checkbox',
    '#title' => t('Display breadcrumb'),
    '#default_value' => $settings['breadcrumb_display'],
  );

// Username
  $form['tnt_container']['general_settings']['username'] = array(
    '#type' => 'fieldset',
    '#title' => t('Username'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  $form['tnt_container']['general_settings']['username']['user_notverified_display'] = array(
    '#type' => 'checkbox',
    '#title' => t('Display "not verified" for unregistered usernames'),
    '#default_value' => $settings['user_notverified_display'],
  );

// Search Settings
  if (module_exists('search')) {
    $form['tnt_container']['general_settings']['search_container'] = array(
      '#type' => 'fieldset',
      '#title' => t('Search results'),
      '#description' => t('What additional information should be displayed on your search results page?'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    );
    $form['tnt_container']['general_settings']['search_container']['search_results']['search_snippet'] = array(
      '#type' => 'checkbox',
      '#title' => t('Display text snippet'),
      '#default_value' => $settings['search_snippet'],
    );
    $form['tnt_container']['general_settings']['search_container']['search_results']['search_info_type'] = array(
      '#type' => 'checkbox',
      '#title' => t('Display content type'),
      '#default_value' => $settings['search_info_type'],
    );
    $form['tnt_container']['general_settings']['search_container']['search_results']['search_info_user'] = array(
      '#type' => 'checkbox',
      '#title' => t('Display author name'),
      '#default_value' => $settings['search_info_user'],
    );
    $form['tnt_container']['general_settings']['search_container']['search_results']['search_info_date'] = array(
      '#type' => 'checkbox',
      '#title' => t('Display posted date'),
      '#default_value' => $settings['search_info_date'],
    );
    $form['tnt_container']['general_settings']['search_container']['search_results']['search_info_comment'] = array(
      '#type' => 'checkbox',
      '#title' => t('Display comment count'),
      '#default_value' => $settings['search_info_comment'],
    );
    $form['tnt_container']['general_settings']['search_container']['search_results']['search_info_upload'] = array(
      '#type' => 'checkbox',
      '#title' => t('Display attachment count'),
      '#default_value' => $settings['search_info_upload'],
    );
  }

// Node Settings
  $form['tnt_container']['node_type_specific'] = array(
    '#type' => 'fieldset',
    '#title' => t('Node settings'),
    '#description' => t('Here you can make adjustments to which information is shown with your content, and how it is displayed.  You can modify these settings so they apply to all content types, or check the "Use content-type specific settings" box to customize them for each content type.  For example, you may want to show the date on stories, but not pages.'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#attributes' => array('class' => 'node_settings'),
  );
  
// Author & Date Settings
  $form['tnt_container']['node_type_specific']['submitted_by_container'] = array(
    '#type' => 'fieldset',
    '#title' => t('Author & date'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

// Default & content-type specific settings
  if (module_exists('submitted_by') == FALSE) {
    foreach ((array('default' => 'Default') + node_get_types('names')) as $type => $name) {
      $form['tnt_container']['node_type_specific']['submitted_by_container']['submitted_by'][$type] = array(
        '#type' => 'fieldset',
        '#title' => t('!name', array('!name' => t($name))),
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
      );
      $form['tnt_container']['node_type_specific']['submitted_by_container']['submitted_by'][$type]["submitted_by_author_{$type}"] = array(
        '#type' => 'checkbox',
        '#title' => t('Display author\'s username'),
        '#default_value' => $settings["submitted_by_author_{$type}"],
      );
      $form['tnt_container']['node_type_specific']['submitted_by_container']['submitted_by'][$type]["submitted_by_date_{$type}"] = array(
        '#type' => 'checkbox',
        '#title' => t('Display date posted (you can customize this format on your Date and Time settings page)'),
        '#default_value' => $settings["submitted_by_date_{$type}"],
      );
// Options for default settings
      if ($type == 'default') {
        $form['tnt_container']['node_type_specific']['submitted_by_container']['submitted_by']['default']['#title'] = t('Default');
        $form['tnt_container']['node_type_specific']['submitted_by_container']['submitted_by']['default']['#collapsed'] = $settings['submitted_by_enable_content_type'] ? TRUE : FALSE;
        $form['tnt_container']['node_type_specific']['submitted_by_container']['submitted_by']['submitted_by_enable_content_type'] = array(
          '#type' => 'checkbox',
          '#title' => t('Use custom settings for each content type instead of the default above'),
          '#default_value' => $settings['submitted_by_enable_content_type'],
        );
      }
// Collapse content-type specific settings if default settings are being used
      else if ($settings['submitted_by_enable_content_type'] == 0) {
        $form['submitted_by'][$type]['#collapsed'] = TRUE;
      }
    }
  } else {
      $form['tnt_container']['node_type_specific']['submitted_by_container']['#description'] = 'NOTICE: You currently have the "Submitted By" module installed and enabled, so the Author & Date theme settings have been disabled to prevent conflicts.  If you wish to re-enable the Author & Date theme settings, you must first disable the "Submitted By" module.';
  }

// Taxonomy Settings
  if (module_exists('taxonomy')) {
    $form['tnt_container']['node_type_specific']['display_taxonomy_container'] = array(
      '#type' => 'fieldset',
      '#title' => t('Taxonomy terms'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    );
// Default & content-type specific settings
    foreach ((array('default' => 'Default') + node_get_types('names')) as $type => $name) {
// taxonomy display per node
      $form['tnt_container']['node_type_specific']['display_taxonomy_container']['display_taxonomy'][$type] = array(
        '#type' => 'fieldset',
        '#title' => t('!name', array('!name' => t($name))),
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
      );
// display
      $form['tnt_container']['node_type_specific']['display_taxonomy_container']['display_taxonomy'][$type]["taxonomy_display_{$type}"] = array(
        '#type' => 'select',
        '#title' => t('When should taxonomy terms be displayed?'),
        '#default_value' => $settings["taxonomy_display_{$type}"],
        '#options' => array(
          '' => '',
          'never' => t('Never display taxonomy terms'),
          'all' => t('Always display taxonomy terms'),
          'only' => t('Only display taxonomy terms on full node pages'),
        ),
      );
// format
      $form['tnt_container']['node_type_specific']['display_taxonomy_container']['display_taxonomy'][$type]["taxonomy_format_{$type}"] = array(
        '#type' => 'radios',
        '#title' => t('Taxonomy display format'),
        '#default_value' => $settings["taxonomy_format_{$type}"],
        '#options' => array(
          'vocab' => t('Display each vocabulary on a new line'),
          'list' => t('Display all taxonomy terms together in single list'),
        ),
      );
// Get taxonomy vocabularies by node type
      $vocabs = array();
      $vocabs_by_type = ($type == 'default') ? taxonomy_get_vocabularies() : taxonomy_get_vocabularies($type);
      foreach ($vocabs_by_type as $key => $value) {
        $vocabs[$value->vid] = $value->name;
      }
// Display taxonomy checkboxes
      foreach ($vocabs as $key => $vocab_name) {
        $form['tnt_container']['node_type_specific']['display_taxonomy_container']['display_taxonomy'][$type]["taxonomy_vocab_hide_{$type}_{$key}"] = array(
          '#type' => 'checkbox',
          '#title' => t('Hide vocabulary: @vocabulary', array('@vocabulary' => $vocab_name)),
          '#default_value' => $settings["taxonomy_vocab_hide_{$type}_{$key}"], 
        );
      }
// Options for default settings
      if ($type == 'default') {
        $form['tnt_container']['node_type_specific']['display_taxonomy_container']['display_taxonomy']['default']['#title'] = t('Default');
        $form['tnt_container']['node_type_specific']['display_taxonomy_container']['display_taxonomy']['default']['#collapsed'] = $settings['taxonomy_enable_content_type'] ? TRUE : FALSE;
        $form['tnt_container']['node_type_specific']['display_taxonomy_container']['display_taxonomy']['taxonomy_enable_content_type'] = array(
          '#type' => 'checkbox',
          '#title' => t('Use custom settings for each content type instead of the default above'),
          '#default_value' => $settings['taxonomy_enable_content_type'],
        );
      }
// Collapse content-type specific settings if default settings are being used
      else if ($settings['taxonomy_enable_content_type'] == 0) {
        $form['display_taxonomy'][$type]['#collapsed'] = TRUE;
      }
    }
  }

// SEO settings
  $form['tnt_container']['seo'] = array(
    '#type' => 'fieldset',
    '#title' => t('Search engine optimization (SEO) settings'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
// Page titles
  $form['tnt_container']['seo']['page_format_titles'] = array(
    '#type' => 'fieldset',
    '#title' => t('Page titles'),
    '#description' => t('This is the title that displays in the title bar of your web browser. Your site title, slogan, and mission can all be set on your Site Information page. [NOTE: For more advanced page title functionality, consider using the "Page Title" module.  However, the Page titles theme settings do not work in combination with the "Page Title" module and will be disabled if you have it enabled.]'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  if (module_exists('page_title') == FALSE) {
// front page title
    $form['tnt_container']['seo']['page_format_titles']['front_page_format_titles'] = array(
      '#type' => 'fieldset',
      '#title' => t('Front page title'),
      '#description' => t('Your front page in particular should have important keywords for your site in the page title'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    );
    $form['tnt_container']['seo']['page_format_titles']['front_page_format_titles']['front_page_title_display'] = array(
      '#type' => 'select',
      '#title' => t('Set text of front page title'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#default_value' => $settings['front_page_title_display'],
      '#options' => array(
        'title_slogan' => t('Site title | Site slogan'),
        'slogan_title' => t('Site slogan | Site title'),
        'title_mission' => t('Site title | Site mission'),
        'custom' => t('Custom (below)'),
      ),
    );
    $form['tnt_container']['seo']['page_format_titles']['front_page_format_titles']['page_title_display_custom'] = array(
      '#type' => 'textfield',
      '#title' => t('Custom'),
      '#size' => 60,
      '#default_value' => $settings['page_title_display_custom'],
      '#description' => t('Enter a custom page title for your front page'),
    );
// other pages title
    $form['tnt_container']['seo']['page_format_titles']['other_page_format_titles'] = array(
      '#type' => 'fieldset',
      '#title' => t('Other page titles'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    );
    $form['tnt_container']['seo']['page_format_titles']['other_page_format_titles']['other_page_title_display'] = array(
      '#type' => 'select',
      '#title' => t('Set text of other page titles'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#default_value' => $settings['other_page_title_display'],
      '#options' => array(
        'ptitle_slogan' => t('Page title | Site slogan'),
        'ptitle_stitle' => t('Page title | Site title'),
        'ptitle_smission' => t('Page title | Site mission'),
        'ptitle_custom' => t('Page title | Custom (below)'),
        'custom' => t('Custom (below)'),
      ),
    );
    $form['tnt_container']['seo']['page_format_titles']['other_page_format_titles']['other_page_title_display_custom'] = array(
      '#type' => 'textfield',
      '#title' => t('Custom'),
      '#size' => 60,
      '#default_value' => $settings['other_page_title_display_custom'],
      '#description' => t('Enter a custom page title for all other pages'),
    );
// SEO configurable separator
    $form['tnt_container']['seo']['page_format_titles']['configurable_separator'] = array(
      '#type' => 'textfield',
      '#title' => t('Title separator'),
      '#description' => t('Customize the separator character used in the page title'),
      '#size' => 60,
      '#default_value' => $settings['configurable_separator'],
    );
  } else {
      $form['tnt_container']['seo']['page_format_titles']['#description'] = 'NOTICE: You currently have the "Page Title" module installed and enabled, so the Page titles theme settings have been disabled to prevent conflicts.  If you wish to re-enable the Page titles theme settings, you must first disable the "Page Title" module.';
      $form['tnt_container']['seo']['page_format_titles']['configurable_separator']['#disabled'] = 'disabled';
  }
// Metadata
  $form['tnt_container']['seo']['meta'] = array(
    '#type' => 'fieldset',
    '#title' => t('Meta tags'),
    '#description' => t('Meta tags are not used as much by search engines anymore, but the meta description is important: it will be shown as the description of your link in search engine results. [NOTE: For more advanced meta tag functionality, consider using the "Meta Tags (or nodewords)" module.  However, the Meta tags theme settings do not work in combination with the "Meta Tags" module and will be disabled if you have it enabled.]'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  if (module_exists('nodewords') == FALSE) {
    $form['tnt_container']['seo']['meta']['meta_keywords'] = array(
      '#type' => 'textfield',
      '#title' => t('Meta keywords'),
      '#description' => t('Enter a comma-separated list of keywords'),
      '#size' => 60,
      '#default_value' => $settings['meta_keywords'],
    );
    $form['tnt_container']['seo']['meta']['meta_description'] = array(
      '#type' => 'textarea',
      '#title' => t('Meta description'),
      '#cols' => 60,
      '#rows' => 6,
      '#default_value' => $settings['meta_description'],
    );
  } else {
      $form['tnt_container']['seo']['meta']['#description'] = 'NOTICE: You currently have the "Meta Tags (or nodewords)" module installed and enabled, so the Meta tags theme settings have been disabled to prevent conflicts.  If you wish to re-enable the Meta tags theme settings, you must first disable the "Meta Tags" module.';
      $form['tnt_container']['seo']['meta']['meta_keywords']['#disabled'] = 'disabled';
      $form['tnt_container']['seo']['meta']['meta_description']['#disabled'] = 'disabled';
  }
// Development settings
  $form['tnt_container']['themedev'] = array(
    '#type' => 'fieldset',
    '#title' => t('Theme development settings'),
    '#collapsible' => TRUE,
    '#collapsed' => $settings['rebuild_registry'] ? FALSE : TRUE,
  );
  $form['tnt_container']['themedev']['rebuild_registry'] = array(
    '#type' => 'checkbox',
    '#title' => t('Rebuild theme registry on every page.'),
    '#default_value' => $settings['rebuild_registry'],
    '#description' => t('During theme development, it can be very useful to continuously <a href="https://drupal.org/node/173880#theme-registry">rebuild the theme registry</a>. <br /> <div class="alert alert-warning messages warning"><b>WARNING</b>: this is a huge performance penalty and must be turned off on production websites.</div>'),
  );
  $form['tnt_container']['themedev']['siteid'] = array(
    '#type' => 'textfield',
    '#title' => t('Site ID bodyclass.'),
   	'#description' => t('In order to have different styles of Zero Point in a multisite/multi-theme environment you may find usefull to choose an "ID" and customize the look of each site/theme in custom-style.css file.'),
    '#default_value' => $settings['siteid'],
    '#size' => 10,
	);
 $form['tnt_container']['themedev']['fix_css_limit'] = array(
    '#type' => 'checkbox',
    '#title' => t('Fix IE stylesheet limit.'),
    '#default_value' => $settings['fix_css_limit'],
    '#description' => t('This setting groups css files so Internet Explorer can see more than 30 of them. This is useful when you cannot use aggregation (e.g., when developing or using private file downloads), especially for RTL sites. But because it degrades performance and can load files out of order, CSS aggregation (<a href="!link">Optimize CSS files</a>) is <b>strongly</b> recommended instead for any production website.', array('!link' => $base_url .'/admin/settings/performance')),
  );
// Info
  $form['tnt_container']['info'] = array(
    '#type' => 'fieldset',
    '#description'   => t('<div class="messages info">Some of the theme settings are <b>multilingual variables</b>. You may have different settings for each language.</div>'),
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
  );

// Return theme settings form
  return $form;
}  

?>