<?php

namespace AxelSpringer\WP\Config;

/**
 * Class WP
 *
 * @package AxelSpringer\WP\Config;
 */
abstract class WP {
  const DB_HOST             = 'localhost:3306';
  const DB_NAME             = 'wordpress';
  const DB_USER             = 'wordpress'; // these are default salts, so no magic
  const DB_PASSWORD         = 'wordpress';
  const DB_CHARSET          = 'utf8mb4';

  const AUTH_KEY            = 'g$<|uOx~IO[#D${0%$SAG)sZ<8SxC&E1UE}/-d&+{n@SpwR5<cLb9/G/-H6B,;Dp';
  const AUTH_SALT           = 'dfeEc[n>-{%W.[[qaAAKYnU/M^=&}w4ul^}5MDSi6c>w0(++jY:L@5NIZqB*QIaK';
  const LOGGED_IN_KEY       = 'wZB/8(?{{&jJX.]+m%W>+R3@YI|zS W93 ysvh=~$glEt}b[+/?T[@:IpeYT)k[v';
  const LOGGED_IN_SALT      = 'T*@i0iO`$-Y~~-Qb.s`Y^NdCC>oI-@nzSxDl2dd|5YMcr|+@}km yB~,ef6xy,B[';
  const NONCE_KEY           = 'UiK6X2+.= c%=5oH CL~jJ<<qvQ2QU%[pG:H-L|Tw*4+sr<?UG(9u^CcX#TeyR_N';
  const NONCE_SALT          = 'p-V++V<N=G+^Aa1<}o|L^`+o&AKos=#`5breS(HNGTe%zAGTUxc ^W@o0Vw`%%S@';
  const SECURE_AUTH_KEY     = '8%5/h+m4E%g{QYk]-~:=cq3D|74jX>r-#+`68=83}kUidA58WZA,6{HE8e{`5TbC';
  const SECURE_AUTH_SALT    = 'h8eecI:x&~~;Sdk<vyYKa&oLX:sP]#Tw#bgehJ6<Hzx][/@S5War-x.WdauIv}eo';

  const MYSQL_CLIENT_FLAGS  = NULL;
}
