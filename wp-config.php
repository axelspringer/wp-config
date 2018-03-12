<?php
// exit, if not there is an origin to relate to
getenv( 'WP_ORIGIN' ) || exit;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/wp-config.inc.php';


if ( ! defined( 'WP_CONFIG' ) ) {
  define( 'WP_CONFIG', '3.0.0' );
}

if ( ! defined( 'APP_DIR_NAME' ) ) {
  define( 'APP_DIR_NAME', 'app' );
}

if ( ! defined( 'DATA_DIR_NAME' ) ) {
  define( 'DATA_DIR_NAME', 'data' );
}

if ( ! defined( 'UPLOADS_DIR_NAME' ) ) {
  define( 'UPLOADS_DIR_NAME', 'uploads' );
}

if ( ! defined( 'APP_DIR' ) ) {
  define( 'APP_DIR', realpath( __DIR__ . '/../../' . APP_DIR_NAME ) );
}

if ( ! defined( 'DATA_DIR' ) ) {
  define( 'DATA_DIR', realpath( __DIR__ . '/../' . DATA_DIR_NAME ) );
}

final class WPConfig {

  /**
   * Environment variables to listen on
   *
   * @var array
   */
  private $allowed_env_vars = array(
    'WP_DEBUG',
    'WP_DEBUG_DISPLAY',
    'SCRIPT_DEBUG',
    'WP_CACHE'
  );

  /**
   * Constructor
   */
  public function __construct () {
    $this->set_origin_host();
    $this->bootstrap();
    $this->init_memcached();
  }

  /**
   * Set ORIGIN_HOST for environment
   *
   * @return void
   */
  public function set_origin_host() {
    if ( ! $wp_origin = getenv( 'WP_ORIGIN' ) ) {
      exit;
    }

    if ( false === getenv( 'IS_SSL' ) ) {
      $wp_origin = str_replace( 'https', 'http', $wp_origin );
    }

    if ( ! defined( 'ORIGIN_HOST' ) ) { // legacy, needs to be replaced later
      define( 'ORIGIN_HOST', $wp_origin );
    }
  }

  /**
   * Bootstrap WordPress
   *
   * @return void
   */
  public function bootstrap() {
    foreach ( $this->allowed_env_vars as $env_var ) {
      if ( $env = getenv( $env_var ) ) {
        define( $env_var, filter_var( $env, FILTER_VALIDATE_BOOLEAN ) );
      }
    }

    if ( ! $wp_environment = getenv( 'ENVIRONMENT' ) ) {
      $wp_environment = 'development';
    }
    define("WP_ENVIRONMENT", $wp_environment);

    if ( ! $wp_layer = getenv( 'WP_LAYER' ) ) {
      $wp_layer = 'frontend';
    }
    $defaults = [];
    // plugins config
    if (defined( 'APP_DIR' ) && file_exists( APP_DIR . '/config/defaults.php' ) ) {
      $defaults = require_once APP_DIR . '/config/defaults.php';
    }

    $wp_config_file = $wp_layer . '-' . $wp_environment . '.php';
    $wp_config_path = APP_DIR . '/config/' . $wp_config_file;

    if ( ! file_exists( $wp_config_path ) ) {
      exit( 'No config available.' );
    }

    $wp_config_data = require_once( $wp_config_path );
    $wp_config_data = array_merge($wp_config_data, $defaults);

    foreach( $wp_config_data as $config_key => $config_value ) {
      if ( ! defined( $config_key ) ) {
        define( $config_key, $config_value );
      }
    }

    // https
    if ( 'true' === getenv( 'HTTPS_IS_ACTIVE' ) ) {
      $_SERVER['HTTPS'] = 'on';
    }

    // adtags config
    if ( defined( 'APP_DIR' ) && file_exists( APP_DIR . '/config/adtags.php' ) ) {
      define( 'ASSE_ADTAGS', require_once APP_DIR . '/config/adtags.php' );
    }

    // asse-http config
    if ( $wp_layer === 'frontend' && defined( 'ORIGIN_HOST' ) ) {
      define( 'HTTP_ORIGIN', ORIGIN_HOST );
    }
  }

  /**
   * Init Memcached
   *
   * @return mixded
   */
  public function init_memcached() {
      $memcacheAvailable = false;
      if (defined('MEMCACHED_HOST') && defined('MEMCACHED_PORT')) {

          global $memcached_servers;
          $memcached_servers = [[MEMCACHED_HOST, MEMCACHED_PORT]];

          $memCache = new Memcached();
          $memCache->addServer(MEMCACHED_HOST, MEMCACHED_PORT);

          $stats = @$memCache->getStats();
          $server = MEMCACHED_HOST . ':' . MEMCACHED_PORT;

          $memcacheAvailable = (
              is_array($stats) &&
              array_key_exists($server, $stats) &&
              array_key_exists('accepting_conns', $stats[$server]) &&
              $stats[$server]['accepting_conns'] >= 1
          );
      }
      define('MEMCACHE_AVAILABLE', $memcacheAvailable);

      if (MEMCACHE_AVAILABLE === true) {
          define('PODS_ALT_CACHE_TYPE', 'memcached');
          define('PODS_ALT_CACHE_MEMCACHED_SERVER', MEMCACHED_HOST);
          define('PODS_ALT_CACHE_MEMCACHED_PORT', MEMCACHED_PORT);
      }
  }
}

$wp_config = new WPConfig();

$table_prefix = 'wp_';
