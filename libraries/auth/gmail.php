<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

class hycusauth_gmail{
	function authenticate($user_email,$pass){

		//this plugin authenticates with the curl way of gmail authentication.
		echo $user_email." / ".$pass;
		if(function_exists("curl_init")){
			$curl = curl_init('https://mail.google.com/mail/feed/atom');
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			//curl_setopt($curl, CURLOPT_HEADER, 1);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($curl, CURLOPT_USERPWD, $user_email.':'.$pass);
			$result = curl_exec($curl);
			$code = curl_getinfo ($curl, CURLINFO_HTTP_CODE);
			if($code=="200"){
				$md5pass = md5($pass);
				$db = new hdatabase();
				$checkuservalue = $db->get_rec("#__users", "id", "email='$user_email' OR username='$user_email'");
				if($checkuservalue->id)
				{
					$msg = emailuseralreadychoosed;
					hycus::redirect(huri::makeuri("?module=user&task=register&menuid=".hycus::getcleanvar("menuid")), $msg);
				}
				else
				{
					$auth_token =  md5( uniqid('hycus_') );
					$log_id = hycus_Auth::adduser($user_email, $user_email, $user_email, $md5pass, "1");
				}
				hycus_Auth::login_authenticated($log_id);
			}
			else{hycus_Auth::login_failed();}
		}
		else{hycus_Auth::login_failed();}
	}
}
?>
