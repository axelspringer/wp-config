<?php

// @codingStandardsIgnoreFile

if (MEMCACHE_AVAILABLE !== true) {
    require_once (ABSPATH . WPINC . '/cache.php');

    add_action('muplugins_loaded', function() {
        wp_using_ext_object_cache(false);
    });
} else {
    require_once __DIR__ . '/object-cache.inc.php';
}
