<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

function setTitle(){
	if((hycus::getcleanvar("task")=="forgot"))
	{
		return "Forgot Password";
	}
	if((hycus::getcleanvar("task")=="register"))
	{
		return "Register";
	}
	if((hycus::getcleanvar("task")=="login"))
	{
		return "Login";
	}
	if((hycus::getcleanvar("task")=="editprofile"))
	{
		return "Edit profile";
	}
	if((hycus::getcleanvar("task")=="profile"))
	{
		if(hycus::getcleanvar("id")){
			$usersid = hycus::getcleanvar("id");
		}
		else{
			$usersid = hycus::getthisuserid();
		}

		return hycus::getusername($usersid);
	}
}

?>
