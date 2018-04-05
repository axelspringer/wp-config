<?php

namespace AxelSpringer\WP;

// respect autoloader
if ( file_exists( __DIR__ . '/../vendor/autoload.php' ) )
  require_once __DIR__ . '/../vendor/autoload.php';

// force set content dir to dedicated folder
define( 'WP_CONTENT_DIR', dirname(__FILE__) . '/wp-content' );

// set for files
if ( ! defined( 'ABSPATH' ) )
  define( 'ABSPATH', dirname( __FILE__ ) . '/' );

// load WPConfig
require_once __DIR__ . '/wp-config.inc.php';

// create new config
$wp_config = new WPConfig(getenv( SSM::DEV_MODE ) && getenv( SSM::DEV_MODE ) === 'true');
$wp_config->auth();

// set global database table prefix
$table_prefix = 'wp_';

// set for WP-CLI
require_once ABSPATH . 'wp-settings.php';
