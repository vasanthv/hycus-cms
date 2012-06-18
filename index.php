<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

/*Defines "HYCUSPAGEPROTECT" which is used in all other .php files to protect those pages from direct access.*/
define( 'HYCUSPAGEPROTECT', 1 );

//comment the following line, in your development phase to see the warnings and notices.
error_reporting(0);

/*Includes the configuration.
 * If $_POST[site] is set then gets the config file from sites/$_POST[site]/config
 * else get the config file from sites/default/config
 * else redirects to installation directory */
if($_REQUEST['site']) { $config = "site/".$_REQUEST['site']."/config.php"; }
else{ $config = "sites/default/config.php"; }

if(is_file($config)){
	require $config;
}
else{
	$rurl = "http://".$_SERVER['HTTP_HOST'].substr($_SERVER['REQUEST_URI'], "0", strrpos($_SERVER['REQUEST_URI'], "/"));
	//PHP redirection script
	header( 'HTTP/1.1 301 Moved Permanently' );
	header( 'Location: '.$rurl.'/install/');
}

/*Loads the loader files*/
require 'libraries/loader.php';

$loader = new hycusLoader();

//loads the template function.
$loader->loadTemplate($template);
?>