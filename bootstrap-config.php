<?php

namespace AxelSpringer\WP\Config;

use Aws\Credentials\CredentialProvider;
use Aws\Rds\AuthTokenGenerator;

/**
 *
 */
final class Config {
  /**
   * Set
   */
  public $rds_auth = false;

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
    // set dev mode
    $this->rds_auth = getenv( SSM::RDS_AUTH ) && getenv( SSM::RDS_AUTH ) === 'true';

    // setup params by env
    foreach ( $this->params as $param => $default ) {
      $this->params[$param] = ! getenv( $param ) ? $default : getenv( $param );
    }
	}

  /**
   *
   */
  public function auth() {
    if ( ! $this->rds_auth ) // break on non rds auth use
      return;

    // get authentication token for RDS
    $this->provider = CredentialProvider::defaultProvider();
    $this->rds_auth_generator = new AuthTokenGenerator( $this->provider );
    $this->token = $this->rds_auth_generator->createToken( $this->params[ SSM::DB_HOST ], 'eu-west-1', $this->params[ SSM::DB_USER ] );

    // overwrite password with auth token
    $this->params[ SSM::DB_PASSWORD ] = $this->token;

    // enforce ssl
    $this->params[ SSM::MYSQL_CLIENT_FLAGS ] = MYSQLI_CLIENT_SSL;
  }

  /**
   *
   */
  public function proxy() {
    if ( $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
      $_SERVER['HTTPS']='on';

    if ( isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) {
	    $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_X_FORWARDED_HOST'];
    }
  }

  /**
   *
   */
  public function bootstrap() {
    // reflect upon WP defaults and SSM
    $reflect_wp = new \ReflectionClass('\AxelSpringer\WP\Config\WP');
    $reflect_ssm = new \ReflectionClass('\AxelSpringer\WP\Config\SSM');

    // get settings
    $wp   = $reflect_wp->getConstants();
    $ssm  = $reflect_ssm->getConstants();

    // set ssm
    foreach( $ssm as $wp_define => $param ) {
      if ( defined( $wp_define ) )
        continue;

      $env = getenv( $param ); // get env variable

      if ( ! $env && array_key_exists( $wp_define, $wp ) ) {
        define( $env, $wp[ $wp_define ] ); continue; // set ssm to default, if exists
      }

      define( $wp_define, $env ); // set wp to ssm
    }
  }
}
