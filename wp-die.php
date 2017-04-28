<?php
// @codingStandardsIgnoreFile

// die on maintenance, can be triggered via Marathon
$maintenance = getenv('WP_MAINTENANCE') === 'true';
if ($maintenance) {
  include_once('error/maintenance.html');
  die;
}

// die on welcome, or other immediate notice, can be triggered via Marathon
$welcome = getenv('WP_WELCOME') === 'true';
if (true == $welcome) {
  include_once('error/welcome.html');
  die;
}
