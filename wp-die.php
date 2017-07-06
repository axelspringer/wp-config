<?php

// exit, if there is no document root defined
if ( ! isset( $_SERVER['DOCUMENT_ROOT'] ) ) {
  exit;
}

// die on maintenance, can be triggered via Marathon
$maintenance = getenv('WP_MAINTENANCE') === 'true';
$maintenance_doc = $_SERVER['DOCUMENT_ROOT'] . '/error/maintenance.html';
if ( $maintenance ) {
  if ( file_exists( $maintenance_doc ) ) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/error/maintenance.html';
  }
  exit;
}
