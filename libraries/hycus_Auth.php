<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

 defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

 class hycus_Auth{

 	function hAuth($user_email,$md5pass){
		//main authentication function

		//get whether the user typed username or email
		if (strpos($user_email,'@') === false) {
		    $user_cond = "username='$user_email'";
		} else {
		      $user_cond = "email = '$user_email'";
		}

		$db = new hdatabase();
		$rec = $db->get_rec("#__users", "id,name", "$user_cond AND password = '$md5pass' AND approved='1' AND block='0' ");
		if ( $rec ) {
			hycus_Auth::login_authenticated($rec->id);
		}
		else
		{
			//gets other authentication methods enabled and tries to authenticate through it.
			$authentications = $db->get_recs("#__auth", "auth_method", "enabled ='enabled '");
			if($authentications){
				foreach($authentications AS $authentication)
				{
					include_once("auth/".str_replace("auth_", "", $authentication->auth_method).".php");
					$class = "hycus".$authentication->auth_method;
					$hycus_authenticate = new $class;
					$hycus_authenticate->authenticate($user_email,hycus::getcleanvar("pwd"));
				}
			}
			else{hycus_Auth::login_failed();}
		}
 	}
 	function login_authenticated($log_id){

		// this sets variables in the session
		$db = new hdatabase();
		$db->db_update("#__session", "time = '".time()."', userid = '$log_id', guest = '0'", "sessionid = '".hycusSession::getthissession()."'");
		$session_ip = $_SERVER['REMOTE_ADDR'];
		$db->db_update("#__users", "lastvisiton = '".time()."', lastvistfrom = '$session_ip'", "id = '$log_id'");

		/* Change the following lines if you need to change the admin url.*/
		if(hycus::getcleanvar("adminloginredirect")){
			hycus::redirect(hycus::getroot()."?admin");exit;
		}

		$msg = loginsuccess;
		if($_SESSION["afterlogin"])
			hycus::redirect($_SESSION["afterlogin"],$msg);
		else
			hycus::redirect(huri::makeuri("?module=user&task=profile&id=$log_id&menuid=".hycus::getcleanvar("menuid")), $msg);
 	}

 	function login_failed(){
 		//triggers if the login details are not correct
 		$msg = invalidloging;
 		if($_SESSION["afterlogin"])
 			hycus::redirect($_SESSION["afterlogin"],$msg);
 		else
			hycus::redirect(huri::makeuri("?module=user&task=login&menuid=".hycus::getcleanvar("menuid")), $msg);
 	}
 	function register($full_name, $user_name, $usr_email, $md5pass){
 		//registration function
		if($full_name && $user_name && $usr_email && $md5pass)
 		{
			$db = new hdatabase();
			$checkuservalue = $db->get_rec("#__users", "id", "email='$usr_email' OR username='$user_name'");
			if($checkuservalue->id)
			{
				$msg = emailuseralreadychoosed;
				hycus::redirect(huri::makeuri("?module=user&task=register&menuid=".hycus::getcleanvar("menuid")), $msg);
			}
			else
			{
				$log_id = hycus_Auth::adduser($full_name, $user_name, $usr_email, $md5pass);

				/*removing session variables*/
				unset($_SESSION['full_name']);
				unset($_SESSION['user_name']);
				unset($_SESSION['usr_email']);

				$msg = registrationsuccess;

				hycus::redirect(huri::makeuri("?module=user&task=login&menuid=".hycus::getcleanvar("menuid")), $msg);
			}
 		}
 		else{
 			$msg = invaliddetails;
			hycus::redirect(huri::makeuri("?module=user&task=register&menuid=".hycus::getcleanvar("menuid")), $msg);
 		}
 	}
 	function adduser($full_name, $user_name, $usr_email, $md5pass, $approved=null){

		//adds the user and send activation email
		global $sitename, $adminemail;
		$db = new hdatabase();
		$dataobj = $db->get_rec("#__modules", "data", "module = 'user'");
		$typeid = hycus::gethycusdata($dataobj->data,"defaultusertype");
		$activation = hycus::gethycusdata($dataobj->data,"useractivation");
		$auth_token =  md5( uniqid('hycus_') );
		if($activation && !$approved){
			$log_id = $db->db_insert("#__users", "`name`, `username`, `email`, `typeid`, `approved`, `registeredon`, `lastvisiton`, `lastvistfrom`, `auth_token`, `block`, `password`", "'$full_name', '$user_name', '$usr_email', '$typeid', '0', '".time()."', '0', '0', '$auth_token', '0', '$md5pass'");

			// Code for sending activation email.
			$udetails = $db->get_rec("#__users", "email, auth_token", "id = '$log_id'");
			$link = huri::makeuri("?module=user&task=activate&uid=".$log_id."&c=".$udetails->auth_token);
			$mail = new hycus_Mailer("$sitename","$adminemail");
			$mail->subject("[Account Activation Email] from ". $sitename);
			$mail->to($udetails->email);
			$mail->text("Click the following link to activate your account. \n ".$link." \n\nPlease dont reply to this email. \nThis is automatically generated.");
			$mail->send();
		}
		else{
			$log_id = $db->db_insert("#__users", "`name`, `username`, `email`, `typeid`, `approved`, `registeredon`, `lastvisiton`, `lastvistfrom`, `auth_token`, `block`, `password`", "'$full_name', '$user_name', '$usr_email', '$typeid', '1', '".time()."', '0', '0', '$auth_token', '0', '$md5pass'");

			//user confirmation email
			$mail = new hycus_Mailer("$sitename","$adminemail");
			$mail->subject("[Your Account Details] from ". $sitename);
			$mail->to($usr_email);
			$mail->text("Your account was successfully created. Details\nName : $full_name \nUsername : $user_name \nEmail : $usr_email \n\nPlease dont reply to this email. \nThis is automatically generated.");
			$mail->send();
		}

		$amail = new hycus_Mailer("$sitename","$adminemail");
		$amail->subject("[New User Registered] from ". $sitename);
		$amail->to($adminemail);
		$amail->text("New User Registered. Details\nName : $full_name \nUsername : $user_name \nEmail : $usr_email \n\nPlease dont reply to this email. \nThis is automatically generated.");
		$amail->send();

		return $log_id;
 	}
 	function activate($uid,$c){

		//activates the account from the link which is sent to the registered users email
 		$db = new hdatabase();
		$udetails = $db->get_rec("#__users", "email", "id = '$uid' AND auth_token='$c' AND approved='0'");

		if($udetails->email)
		{
			$db->db_update("#__users", "approved = '1'", "id = '".$uid."'");

			global $sitename, $adminemail;
			$mail = new hycus_Mailer($sitename, $adminemail);
			$mail->subject("[Account Activated] from ". $sitename);
			$mail->to($udetails->email);
			$mail->text("Your account has been activated. \n\nPlease dont reply to this email. \nThis is automatically generated.");
			$mail->send();
			$msg="Account activated Successfully!";

			if($_SESSION["afterlogin"])
				hycus::redirect($_SESSION["afterlogin"], $msg);
			else
				hycus::redirect(huri::makeuri("?module=user&task=login"), $msg);

		}
		else
		{
			$msg = "Something went wrong. Account not activated or already activated.";
			if($_SESSION["afterlogin"])
				hycus::redirect($_SESSION["afterlogin"], $msg);
			else
				hycus::redirect(huri::makeuri("?module=user&task=login"), $msg);

		}

	}

 }
?>
