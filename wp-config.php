<?php
getenv('WP_ORIGIN') || exit;

require_once __DIR__ . '/../../vendor/autoload.php';

if ( ! defined( 'ASSE_XBOOKS_CONFIG' ) ) {
  define( 'ASSE_XBOOKS_CONFIG', '1.0.17' );
}

define( 'APP_DIR_NAME', 'app' );
define( 'DATA_DIR_NAME', 'data' );
define( 'UPLOADS_DIR_NAME', 'uploads' );

define( 'APP_DIR', realpath( __DIR__ . '/../../' . APP_DIR_NAME ) );
define( 'DATA_DIR', realpath( __DIR__ . '/../' . DATA_DIR_NAME ) );

// Books Links
if ( ! defined( 'ADMIN_LINKS' ) ) {
  define( 'ASSE_ADMIN_LINKS', array(
    'Stylebook'     => $originHost . '/wp/wp-admin/',
    'Techbook'      => $originHost . '/wp/wp-admin/',
    'Travelbook'    => $originHost . '/wp/wp-admin/'
  ) );
}

// Books Plugins
if ( ! defined( 'ENABLE_PLUGINS' ) ) {
  define( 'ENABLE_PLUGINS', array(
    'amazon-s3-and-cloudfront/wordpress-s3.php',
    'amazon-web-services/amazon-web-services.php',
    'asse-exporter/asse-exporter.php',
    'asse-helpers/asse-helpers.php',
    'asse-importer/asse-importer.php',
    'mashshare-floating-sidebar/mashshare-floating-sidebar.php',
    'mashshare-networks/mashshare-networks.php',
    'mashshare-select-and-share/mashshare-select-and-share.php',
    'mashshare-sharebar/mashshare-sharebar.php',
    'mashsharer/mashshare.php',
    'no-category-base-wpml/no-category-base-wpml.php',
    'wp-category-permalink/wp-category-permalink.php',
    'wp-meta-tags/meta-tags.php',
    'disable-wordpress-updates/disable-updates.php',
    'dynamic-featured-image/dynamic-featured-image.php'
  ) );
}

/**
 * Set Origin
 *
 * @return string
 */
function set_origin_host() {
  if ( ! defined( 'ORIGIN_HOST' ) ) {
    $wp_origin = getenv( 'WP_ORIGIN' );

    if ( false === IS_SSL ) {
      $wp_origin = str_replace( 'https', 'http', $wp_origin );
    }

    define( 'ORIGIN_HOST', $wp_origin );
  }
}

set_origin_host();

/**
 * Set Mobile Detection
 *
 * @return void
 */
function set_ua_device() {
  $ua_detect = new \Mobile_Detect();
  $ua_device = $ua_detect->isMobile() ? 'mobile' : 'desktop';
  $_SERVER['HTTP_X_UA_DEVICE'] = $ua_device;
}

set_ua_device();

/**
 * Bootstrap Environment
 *
 * @return void
 */
function bootstrap() {
  if ( ! $wp_environment = getenv( 'ENVIRONMENT' ) ) {
    $wp_environment = 'development';
  }

  if ( ! $wp_layer = getenv( 'WP_LAYER' ) ) {
    $wp_layer = 'frontend';
  }

  $wp_config_file = $wp_layer . '-' . $wp_environment . '.php';
  $wp_config_path = APP_DIR . '/config/' . $wp_config_file;

  if ( ! file_exists( $wp_config_path ) ) {
    exit( 'No config available.' );
  }

  $wp_config_data = require_once( $wp_config_path );

  foreach( $wp_config_data as $config_key => $config_value ) {
    if ( ! defined( $config_key ) ) {
      define( $config_key, $config_value );
    }
  }

  if ( 'true' === getenv( 'HTTPS_IS_ACTIVE' ) ) {
    $_SERVER['HTTPS'] = 'on';
  }
}

bootstrap();

/**
 * Set up cache
 *
 * @return void
 */
function init_cache() {
  $wp_memcached_available = false;

  if ( defined( 'MEMCACHED_HOST' ) && defined( 'MEMCACHED_PORT' ) ) {
    global $memcached_servers;
    $memcached_servers = [ [ MEMCACHED_HOST, MEMCACHED_PORT ] ];

    $memcached = new Memcached();
    $memcached->addServer( MEMCACHED_HOST, MEMCACHED_PORT );
    $memcached_stats  = @$memcached->getStats();
    $memcached_server = MEMCACHED_HOST . ':' . MEMCACHED_PORT;

    $wp_memcached_available = (
      is_array( $memcached_stats ) &&
      array_key_exists( $memcached_server, $memcached_stats ) &&
      array_key_exists( 'accepting_conns', $memcached_stats[ $memcached_server ] ) &&
      $memcached_stats[ $memcached_server ][ 'accepting_conns' ] >= 1
    );
  }

  if ( ! defined( 'MEMCACHE_AVAILABLE' ) ) {
    define( 'MEMCACHE_AVAILABLE', $wp_memcached_available );
  }
}

init_cache();

$table_prefix = 'wp_';

// load adtags config
if ( file_exists( APP_DIR . '/config/adtags.php' ) ) {
  define( 'ASSE_ADTAGS', require_once APP_DIR . '/config/adtags.php' );
}
