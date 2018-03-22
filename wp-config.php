<?php
// exit, if not there is an origin to relate to
// getenv( 'WP_ORIGIN' ) || exit;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/wp-config.inc.php';

// Initialize new config
$wp_config = new WPConfig();

// Set global prefix
$table_prefix = 'wp_';
