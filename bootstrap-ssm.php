<?php

namespace AxelSpringer\WP\Config;

/**
 * Class SSM
 *
 * @package AxelSpringer\WP\Config;
 */
abstract class SSM {
  // database
  const DB_CHARSET        = 'SSM_DB_CHARSET';
  const DB_HOST           = 'SSM_DB_HOST';
  const DB_NAME           = 'SSM_DB_NAME';
  const DB_PASSWORD       = 'SSM_DB_PASSWORD';
  const DB_USER           = 'SSM_DB_USER';

  // wp auth
  const AUTH_KEY          = 'SSM_AUTH_KEY';
  const AUTH_SALT         = 'SSM_AUTH_SALT';
  const LOGGED_IN_KEY     = 'SSM_LOGGED_IN_KEY';
  const LOGGED_IN_SALT    = 'SSM_LOGGED_IN_SALT';
  const NONCE_KEY         = 'SSM_NONCE_KEY';
  const NONCE_SALT        = 'SSM_NONCE_SALT';
  const SECURE_AUTH_KEY   = 'SSM_SECURE_AUTH_KEY';
  const SECURE_AUTH_SALT  = 'SSM_SECURE_AUTH_SALT';

  // dev
  const RDS_AUTH          = 'SSM_RDS_AUTH';

  // MySQLi
  const MYSQL_CLIENT_FLAGS = 'SSM_MYSQL_CLIENT_FLAGS';
}
