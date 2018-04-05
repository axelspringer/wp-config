<?php

namespace WP\Config;

use Aws\Credentials\CredentialProvider;
use Aws\Rds\AuthTokenGenerator;

/**
 *
 */
abstract class SSM {
  // database
  const DB_CHARSET  = 'SSM_DB_CHARSET';
  const DB_HOST     = 'SSM_DB_HOST';
  const DB_NAME     = 'SSM_DB_NAME';
  const DB_PASSWORD = 'SSM_DB_PASSWORD';
  const DB_USER     = 'SSM_DB_USER';

  // wp auth
  const AUTH_KEY          = 'SSM_AUTH_KEY';
  const AUTH_SALT         = 'SSM_AUTH_SALT';
  const LOGGED_IN_KEY     = 'SSM_LOGGED_IN_KEY';
  const LOGGED_IN_SALT    = 'SSM_LOGGED_IN_SALT';
  const NONCE_KEY         = 'SSM_NONCE_KEY';
  const NONCE_SALT        = 'NONCE_SALT';
  const SECURE_AUTH_KEY   = 'SSM_SECURE_AUTH_KEY';
  const SECURE_AUTH_SALT  = 'SSSM_SECURE_AUTH_SALT';

  // dev
  const DEV_MODE = 'SSM_DEV_MODE';

  // MySQLi
  const MYSQL_CLIENT_FLAGS = 'SSM_MYSQL_CLIENT_FLAGS';
}

/**
 *
 */
final class Config {

  /**
   *
   */
  public $params = [
    // set some defaults
    SSM::DB_HOST          => 'localhost:3306',
    SSM::DB_NAME          => 'wordpress',
    SSM::DB_USER          => 'wordpress', // these are default salts, so no magic
    SSM::DB_PASSWORD      => 'wordpress',
    SSM::DB_CHARSET       => 'utf8mb4',
    SSM::AUTH_KEY         => 'g$<|uOx~IO[#D${0%$SAG)sZ<8SxC&E1UE}/-d&+{n@SpwR5<cLb9/G/-H6B,;Dp',
    SSM::AUTH_SALT        => 'dfeEc[n>-{%W.[[qaAAKYnU/M^=&}w4ul^}5MDSi6c>w0(++jY:L@5NIZqB*QIaK',
    SSM::LOGGED_IN_KEY    => 'wZB/8(?{{&jJX.]+m%W>+R3@YI|zS W93 ysvh=~$glEt}b[+/?T[@:IpeYT)k[v',
    SSM::LOGGED_IN_SALT   => 'T*@i0iO`$-Y~~-Qb.s`Y^NdCC>oI-@nzSxDl2dd|5YMcr|+@}km yB~,ef6xy,B[',
    SSM::NONCE_KEY        => 'UiK6X2+.= c%=5oH CL~jJ<<qvQ2QU%[pG:H-L|Tw*4+sr<?UG(9u^CcX#TeyR_N',
    SSM::NONCE_SALT       => 'p-V++V<N=G+^Aa1<}o|L^`+o&AKos=#`5breS(HNGTe%zAGTUxc ^W@o0Vw`%%S@',
    SSM::SECURE_AUTH_KEY  => '8%5/h+m4E%g{QYk]-~:=cq3D|74jX>r-#+`68=83}kUidA58WZA,6{HE8e{`5TbC',
    SSM::SECURE_AUTH_SALT => 'h8eecI:x&~~;Sdk<vyYKa&oLX:sP]#Tw#bgehJ6<Hzx][/@S5War-x.WdauIv}eo',
    SSM::MYSQL_CLIENT_FLAGS => NULL
  ];

  /**
   *
   */
  public $dev_mode = false;

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
  public function __construct( $dev_mode ) {
    $this->dev_mode = $dev_mode; // noop

    // setup params by env
    foreach ( $this->params as $param => $default ) {
      $this->params[$param] = ! getenv( $param ) ? $default : getenv( $param );
    }
	}

  /**
   *
   */
  public function auth() {
    if ( $this->dev_mode ) // break on dev
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
    // get constants
    $reflect = new \ReflectionClass('\WP\Config\SSM');
    $ssm = $reflect->getConstants();

    // iterate needed params
    foreach ( $this->params as $param => $value ) {
      // map and define the wordpress constants
      define( $key = array_search( $param, $ssm ), $this->params[$param] );
    }
  }
}
