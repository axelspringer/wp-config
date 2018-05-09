<?php

// respect autoloader
if ( file_exists( __DIR__ . '/../../vendor/autoload.php' ) )
  require_once __DIR__ . '/../../vendor/autoload.php';

// force set content dir to dedicated folder
define( 'WP_CONTENT_DIR', dirname( __FILE__ ) );

// set for files
if ( ! defined( 'ABSPATH' ) )
  define( 'ABSPATH', dirname( __FILE__ ) . '/../' );

// load classes
require_once __DIR__ . '/bootstrap-ssm.php';
require_once __DIR__ . '/bootstrap-wp.php';
require_once __DIR__ . '/bootstrap-config.php';

// allow overload env
// either define a constant to constants in \AxelSpringer\WP\Config;
// or set an env variable as to these constants
if ( file_exists( __DIR__ . '/boostrap.inc.php' ) )
  include_once __DIR__ . '/boostrap.inc.php';

use AxelSpringer\WP\Config\Config;

// create new config
global $wp_config;
$wp_config = new Config();
$wp_config->auth();
$wp_config->proxy();
$wp_config->bootstrap();

// set global database table prefix
$table_prefix = 'wp_';

// set for WP-CLI
require_once ABSPATH . 'wp-settings.php';
