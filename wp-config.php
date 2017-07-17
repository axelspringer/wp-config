<?php
// exit, if not there is an origin to relate to
getenv( 'WP_ORIGIN' ) || exit;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/wp-config.inc.php';

if ( ! defined( 'WP_CONFIG' ) ) {
  define( 'WP_CONFIG', '1.4.12' );
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
  protected $debug_env = array(
    'WP_DEBUG',
    'WP_DEBUG_DISPLAY',
    'SCRIPT_DEBUG'
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
    global $asse_wp_admin_links;
    global $asse_wp_enable_plugins;

    foreach ( $this->debug_env as $env_var ) {
      if ( $env = getenv( $env_var ) ) {
        define( $env_var, filter_var( $env, FILTER_VALIDATE_BOOLEAN ) );
      }
    }

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

    // https
    if ( 'true' === getenv( 'HTTPS_IS_ACTIVE' ) ) {
      $_SERVER['HTTPS'] = 'on';
    }

    // xBooks Links
    if ( ! defined( 'ASSE_ADMIN_LINKS' ) ) {
      define( 'ASSE_ADMIN_LINKS', $asse_wp_admin_links[ $wp_environment ] );
    }

    // enable plugins
    if ( ! defined( 'ENABLE_PLUGINS' ) ) {
      define( 'ENABLE_PLUGINS', $asse_wp_enable_plugins );
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
    global $memcached_servers;

    if( ! class_exists( 'Memcache' )
      || ! ( defined( 'MEMCACHED_HOST' ) && defined( 'MEMCACHED_PORT' ) ) ){
      return;
    }

    $wp_memcached_available = false;
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

    if ( ! defined( 'MEMCACHE_AVAILABLE' ) ) {
      define( 'MEMCACHE_AVAILABLE', $wp_memcached_available );
    }
  }
}

$wp_config = new WPConfig();

$table_prefix = 'wp_';
