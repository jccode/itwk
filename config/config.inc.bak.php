<?php
define('ADMIN_UID','1');

define ( 'DBHOST', 'localhost');
define ( 'DBNAME', 'keke');
define ( 'DBUSER', 'epweike');
define ( 'DBPASS', 'fswWXDVgPNDQ');

define('DBCHARSET','utf8');
define('CHARSET', 'utf-8');
define('DBTYPE','mysql');
define ( 'TABLEPRE', 'keke_'); //表前缀
define('ENCODE_KEY','s5nRLJJ61oeM'); //加密KEY
define('GZIP',TRUE ); //开启GIZP
define("IS_REWRITE", 0 ); //开记伪静态，0关闭，1开启
define('IMGDIR','resource/img/'); //公共图片路径
define('KEKE_DEBUG', 0);    //开启调试模式
define("TPL_CACHE",1);   //模板缓存
define('IS_CACHE',1);
define('CACHE_TYPE', 'file');  //缓存类型
//define('S_ROOT', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR); //S_ROOT ：为系统跟目录
define('COOKIE_DOMAIN','.itbangshou.com'); //Cookie 作用域
define ( 'COOKIE_PATH', ''); //Cookie 作用路径
define('COOKIE_PRE', 'kekeWitkey' );
define('COOKIE_TTL', 0 ); //Cookie 生命周期，0 表示随浏览器进程
define('SESSION_MODULE','files');
define('SYS_START_TIME', time());
$_K['cache_config'] = array(0=>array("host"=>"localhost","port"=>"11211"));

//设置时区
function_exists ( 'date_default_timezone_set' ) and date_default_timezone_set ( 'PRC' );
//设置默认上传 相对 path
$___y = date ( 'Y' );
$___m = date ( 'm' );
$___d = date ( 'd' );
define ( 'UPLOAD_RULE', "$___y/$___m/$___d/" );

ini_set('session.cookie_domain', COOKIE_DOMAIN);
