<?php
//	error_reporting(0);
	define('DS', DIRECTORY_SEPARATOR);
	define('APP_DIR', 'app');
	define('DIR_UP', 'uploadfiles');
	define('DIR_UP_IMG', 'images');
	define('DIR_UP_IMG_S', 'small');
	define('DIR_ADMIN', 'root');
	define('DOMAIN', $_SERVER['HTTP_HOST']);

	define('SITE_PATH', dirname(__FILE__).DS);
	define('SITE_URL', 'http://'.DOMAIN.'/');

	define('PATH_APP', SITE_PATH.APP_DIR.DS);
	define('PATH_FRAME', SITE_PATH.'frame'.DS);
	define('PATH_CLASS', PATH_FRAME.'class'.DS);
	define('PATH_TMP', SITE_PATH.APP_DIR.DS.'tmp'.DS);
	define('PATH_CACHES', PATH_TMP.'caches'.DS);
	define('PATH_CONTROLLER', SITE_PATH.APP_DIR.DS.'controller'.DS);
	define('PATH_CONFIG', SITE_PATH.APP_DIR.DS.'config'.DS);

	define('URL_APP', SITE_URL.APP_DIR.'/');
	define('URL_JS', URL_APP.'js/');
	define('URL_JS_JQUERY', URL_JS.'jquery/');
	define('URL_CSS', URL_APP.'css/');
	define('URL_IMAGES', URL_APP.'images/');
	define('URL_ROOT', SITE_URL.DIR_ADMIN.'/');

	define('PATH_UP', SITE_PATH.DIR_UP.DS);
	define('PATH_UP_IMAGES', PATH_UP.DIR_UP_IMG.DS);
	define('PATH_UP_IMAGES_SMLLL', PATH_UP_IMAGES.DIR_UP_IMG_S.DS);

	define('URL_UPLOAD', SITE_URL.DIR_UP.'/');
	define('URL_UP_IMAGES', URL_UPLOAD.DIR_UP_IMG.'/');
	define('URL_UP_IMAGES_SMLLL', URL_UP_IMAGES.DIR_UP_IMG_S.'/');

	header("content-type:text/html;charset=utf-8");
	include(PATH_FRAME.'function.php');
	include(PATH_FRAME.'function_string.php');
?>