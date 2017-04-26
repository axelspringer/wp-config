<?php
// @codingStandardsIgnoreFile

require_once __DIR__ . '/../vendor/autoload.php';

define('APP_DIR_NAME', 'app');
define('DATA_DIR_NAME', 'data');
define('UPLOADS_DIR_NAME', 'uploads');

define('APP_DIR', realpath(__DIR__ . '/../' . APP_DIR_NAME));
define('DATA_DIR', realpath(__DIR__ . '/' . DATA_DIR_NAME));

// load config that depends on the container environment
$environment = getenv('ENVIRONMENT');
if (false === $environment
    || false === in_array($environment, ['development', 'testing', 'integration', 'production'])
) {
    $environment = 'development';
}

if ($environment != 'development') {
    $_SERVER['HTTPS'] = 'on';
}

$layer = getenv('WP_LAYER');
if (false === $layer || false === in_array($layer, ['backend', 'frontend'])) {
    $layer = 'frontend';
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

//load adtags config
define('ASSE_ADTAGS', require_once APP_DIR . '/config/adtags.php');

/**
 * Set up cache
 *
 * @return void
 */
function initCache() {
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

// Load WordPress settings
require_once ABSPATH . 'wp-settings.php';
