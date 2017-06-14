<?php

$document_root    = $_SERVER['DOCUMENT_ROOT'];
$document_error   = $document_root . '/error/error.html';

if ( file_exists ( $document_error ) ) {
  include_once $document_error;
}

exit;
