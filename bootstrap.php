<?php

// respect autoloader
if ( file_exists( __DIR__ . '/../../vendor/autoload.php' ) )
  require_once __DIR__ . '/../../vendor/autoload.php';

// force set content dir to dedicated folder
define( 'WP_CONTENT_DIR', dirname(__FILE__) );

// set for files
if ( ! defined( 'ABSPATH' ) )
  define( 'ABSPATH', dirname( __FILE__ ) . '/../' );

// load config
require_once __DIR__ . '/bootstrap.inc.php';

// create new config
$wp_config = new \WP\Config\Config( getenv( \WP\Config\SSM::DEV_MODE ) && getenv( \WP\Config\SSM::DEV_MODE ) === 'true' );
$wp_config->auth();
$wp_config->proxy();
$wp_config->bootstrap();

// set global database table prefix
$table_prefix = 'wp_';

// set for WP-CLI
require_once ABSPATH . 'wp-settings.php';
