<?php

namespace AxelSpringer\WP\Config;

use Aws\Credentials\CredentialProvider;
use Aws\Rds\AuthTokenGenerator;

/**
 *
 */
final class Config {
  /**
   *
   */
  public $auth = false;

  /**
   *
   */
  public $token;

  /**
   *
   */
  public $provider;

  /**
   *
   */
  public $rds_auth_generator;

	/**
	 * Constructor
	 */
  public function __construct() {
    // only auth if so required
    $this->auth = get_env( WP::RDS_AUTH );
    $this->auth = get_env( SSM::RDS_AUTH );
	}

  /**
   *
   */
  public function auth() {
    if ( ! $this->auth )
      return;

    // get authentication token for RDS
    $this->provider = CredentialProvider::defaultProvider();
    $this->rds_auth_generator = new AuthTokenGenerator( $this->provider );
    $this->token = $this->rds_auth_generator->createToken( WP::DB_HOST, 'eu-west-1', WP::DB_USER );

    // define wp
    define( WP::DB_PASSWORD, $this->token );
    define( WP::MYSQL_CLIENT_FLAGS, MYSQLI_CLIENT_SSL );
  }

  /**
   *
   */
  public function proxy() {
    if ( isset( $_SERVER[ 'HTTP_X_FORWARDED_HOST' ] ) )
	    $_SERVER[ 'HTTP_HOST' ] = $_SERVER[ 'HTTP_X_FORWARDED_HOST' ];
  }

  /**
   *
   */
  public function bootstrap() {
    // load wp defaults
    $reflect_wp   = new \ReflectionClass('\AxelSpringer\WP\Config\WP');
    $reflect_ssm  = new \ReflectionClass('\AxelSpringer\WP\Config\SSM');

    // get settings
    $wp_params   = $reflect_wp->getConstants();
    $ssm_params  = $reflect_ssm->getConstants();

    // merge wp with ssm
    foreach( $wp_params as $param => $default ) {
      if ( defined( $param ) )
        continue;

      $value = $default;

      $wp_env   = getenv( $param ); // a bit obsolete ;)
      $ssm_env  = array_key_exists( $param, $ssm_params ) ? get_env( $ssm_params[ $param ] ) : false;

      if ( $ssm_env )
        $value = $ssm_env;

      if ( $wp_env )
        $value = $wp_env;

      define( $param, $value );
    }
  }
}
