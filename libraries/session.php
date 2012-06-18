<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

class hycusSession {

function hycusSession(){
	session_start();
	$this->purgesessions();
	$this->setthissession();
}
function setthissession()
{
	//unset($_SESSION['hycus_session']);
	if(!$_SESSION['hycus_session']){
		$sessionid = md5(uniqid('hycussession_'));
		$_SESSION['hycus_session'] = $sessionid;
	}
	$session_start_time = time();
	$session_ip = $_SERVER['REMOTE_ADDR'];

	$db = new hdatabase();

	$sessioncheck = $db->get_rec("#__session", "*", "sessionid = '".$this->getthissession()."'");
	if($sessioncheck){
		$db->db_update("#__session", "time = '$session_start_time'", "sessionid = '".$this->getthissession()."'");
	}
	else{
		$db->db_insert("#__session", "`time`, `sessionid`, `userid`, `guest`, `sessionip`", "'$session_start_time', '".$_SESSION['hycus_session']."', '0', '1', '$session_ip'");
	}
}
function getthissession(){
	return $_SESSION['hycus_session'];
}
function getthisuserid(){
	$db = new hdatabase();
	$currentuserid = $db->get_rec("#__session", "userid", "sessionid='".$_SESSION['hycus_session']."'");
	return $currentuserid->userid;
}
function purgesessions()
{
	global $sessionlimit;
	$db = new hdatabase();
	$db->db_delete("#__session", "time < '".(time()-$sessionlimit)."'");

}
function logout()
{
	$db = new hdatabase();
	$db->db_delete("#__session", "sessionid = '".$_SESSION['hycus_session']."'");
	unset($_SESSION['hycus_session']);
}
}
?>
