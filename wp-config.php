<?php
// @codingStandardsIgnoreFile

require_once __DIR__ . '/../../vendor/autoload.php';

define('APP_DIR_NAME', 'app');
define('DATA_DIR_NAME', 'data');
define('UPLOADS_DIR_NAME', 'uploads');

define('APP_DIR', realpath(__DIR__ . '/../../' . APP_DIR_NAME));
define('DATA_DIR', realpath(__DIR__ . '/../' . DATA_DIR_NAME));

/**
 * Set Origin
 *
 * @return string
 */
function getOriginHost()
{
    $origin = getenv('WP_ORIGIN');

    if (false === $origin) {
        return 'http://192.168.33.10:41500';
    }

    if (false === IS_SSL) {
        // if the current request is served over http
        // but the origin host is configured as https
        // rewrite the protocol
        $origin = str_replace('https', 'http', $origin);
    }

    return $origin;
}

define('ORIGIN_HOST', getOriginHost());

/**
 * Set Mobile Detection
 *
 * @return void
 */
function setUADevice()
{
    $uaDetect = new \Mobile_Detect();

    if ($uaDetect->isMobile()) {
        $device = 'mobile';
    } else {
        $device = 'desktop';
    }
    $_SERVER['HTTP_X_UA_DEVICE'] = $device;
}

setUADevice();

/**
 * Bootstrap Environment
 *
 * @return void
 */
function bootstrap()
{
    // load config that depends on the container environment
    $environment = getenv('ENVIRONMENT');
    if (false === $environment
        || false === in_array($environment, ['development', 'testing', 'integration', 'production'])
    ) {
        $environment = 'development';
    }

    $layer = getenv('WP_LAYER');
    if (false === $layer || false === in_array($layer, ['backend', 'frontend'])) {
        $layer = 'frontend';
    }
    if ("true" === getenv('HTTPS_IS_ACTIVE')) {
        $_SERVER['HTTPS'] = 'on';
    }

    $configFile = "{$layer}-{$environment}.php";
    $configPath = APP_DIR . '/config/' . $configFile;

    if (false === file_exists($configPath)) {
        die('no config available');
    } else {
        $configData = require_once($configPath);
        foreach ($configData as $key => $value) {
            if (false === defined($key)) {
                define($key, $value);
            }
        }
    }

}

bootstrap();

/**
 * Set up cache
 *
 * @return void
 */
function initCache()
{
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

    if (defined('MEMCACHED_HOST') && defined('MEMCACHED_PORT')) {
        global $memcached_servers;
        $memcached_servers = [[MEMCACHED_HOST, MEMCACHED_PORT]];
    }
}

initCache();

$table_prefix = 'wp_';

// load adtags config
if (file_exists(APP_DIR . '/config/adtags.php')) {
  define('ASSE_ADTAGS', require_once APP_DIR . '/config/adtags.php');
}
