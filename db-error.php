<?php

// exit, if there is no document root defined
if ( ! isset( $_SERVER['DOCUMENT_ROOT'] ) ) {
  exit;
}

// define error document
$error_doc =  $_SERVER['DOCUMENT_ROOT'] . '/error/error.html';

// check and include
if ( file_exists ( $error_doc ) ) {
  include_once $error_doc;
}

// exit anyway
exit;
