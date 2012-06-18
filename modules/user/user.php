<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

hycusLoader::hycusloadview("user");
$data = hycusLoader::loadmoduleconfig("user");

$task = hycus::getcleanvar("task");
switch($task){
	case "profile":
		profile();
		break;
	case "register":
		register();
		break;
	case "hregister":
		hregister($data);
		break;
	case "login":
		login($hview);
		break;
	case "hlogin":
		hlogin($hview);
		break;
	case "logout":
		logout($data);
		break;
	case "activate":
		activate();
		break;
	case "changepass":
		changepass();
		break;
	case "forgot":
		forgot();
		break;
	case "forgotpass":
		forgotpass();
		break;
	case "resetpass":
		resetpass();
		break;
	case "authresetpass":
		authresetpass();
		break;
	case "update":
		update();
		break;
	case "editprofile":
		editprofile();
		break;
	case "uploadavatar":
		uploadavatar();
		break;
	default:
		login();
		break;
}

function profile(){

	global $paginationlimit;

	$db = new hdatabase();
	/*Pagination Starts*/

	/* Pagination codes to get the start and end value */
	$page= hycus::getcleanvar("pge");
	if($page){$start = ($page-1)*$paginationlimit;}
	else{$start = 0;}

	/*Pagination codes to get the total number of results (for getting total page count)*/
	$totalblogs = $db->get_recs("#__contents", "*", "$where");
	$tot_results = count($totalblogs);

	/*Pagination Ends*/
	if(hycus::getcleanvar("id")){
		$rssbuid = hycus::getcleanvar("id");
		$where = "uid='".hycus::getcleanvar("id")."'";
	}
	else{
		$rssbuid = hycus::getthisuserid();
		$where = "uid='".hycus::getthisuserid()."'";
	}

	$blogs = $db->get_recs("#__contents", "*", "$where", "lastupdated_on DESC", "$start", "$paginationlimit");
	if($blogs)
	{
		$rsslink = "<a href='".huri::makeuri("?module=rss&response=module&task=user&id=$rssbuid")."' >"."<img src='".hycus::getroot()."images/rssicon.png' alt='rss' />"."</a>";
	}
	if($rssbuid)
		echo "<h4>".hycus::getusername($rssbuid).profilepage."</h4>";

	if($blogs){
		hycusLoader::hycusloadview("content");
		hycusContentView::catview($blogs);

		if(hycus::getcleanvar("id"))
			$thisurl = "?module=".hycus::getcleanvar("module")."&task=".hycus::getcleanvar("task")."&id=".hycus::getcleanvar("id")."&menuid=".hycus::getcleanvar("menuid");
		else
			$thisurl = "?module=".hycus::getcleanvar("module")."&task=".hycus::getcleanvar("task")."&menuid=".hycus::getcleanvar("menuid");
		/*Pagination Display Starts*/
		// Loads the pagination module and its respective function to display pagination results
		hycusLoader::loadModule("pagination");
		paginationlinks($thisurl, $tot_results, $paginationlimit);
		/*Pagination Display Ends*/
	}
	else{
		echo nopermissionforblog;
		if(!hycus::getthisuserid())
		echo logintoblog;
	}
	echo $rsslink;

}
function editprofile(){
	if(hycus::getthisuserid())
		hycusUserView::editprofile();
}

function login(){
	if(hycus::getthisuserid())
		hycus::redirect(huri::makeuri("?module=user&task=profile&menuid=".hycus::getcleanvar("menuid")));
	else{
		$hview = new hycusUserView();
		$hview->login();
	}
}
function logout($data){
	hycusSession::logout();

 	if($_SESSION["afterlogout"])
 		hycus::redirect($_SESSION["afterlogout"]);
	else
		hycus::redirect(huri::makeuri("?module=user&task=login&menuid=".hycus::getcleanvar("menuid")));
}
function register(){
	$hview = new hycusUserView();
	$hview->register();
}
function hlogin(){

	$user_email = hycus::getcleanvar("usr_email");
	$remember = hycus::getcleanvar("remember");
	$md5pass = md5(hycus::getcleanvar("pwd"));

	if(isset($_POST['remember'])){
		setcookie("usr_email", $user_email, time()+60*60*24*60, "/");
	}

	hycus_Auth::hAuth($user_email,$md5pass);
}
function hregister($data){
	$full_name = hycus::getcleanvar("full_name");
	$_SESSION['full_name'] = $full_name;

	$user_name = hycus::getcleanvar("user_name");
	$_SESSION['user_name']=$user_name;

	$usr_email = hycus::getcleanvar("usr_email");
	$_SESSION['usr_email']=$usr_email;

	$md5pass = md5(hycus::getcleanvar("pwd"));
	$security = hycus::getcleanvar("regcaptcha");
	$tos = hycus::getcleanvar("regtos");

	if(hycus::gethycusdata($data,"registrationcaptcha")){
		if($security==hycus::getcleanvar("captcha", "session")){
			if(hycus::gethycusdata($data,"registrationtos")){
				if($tos){
					hycus_Auth::register($full_name, $user_name, $usr_email, $md5pass);
				}
				else{
					hycus::redirect(huri::makeuri("?module=user&task=register&menuid=".hycus::getcleanvar("menuid")),"You need to accept our Terms.");
				}
			}
			else{
				hycus_Auth::register($full_name, $user_name, $usr_email, $md5pass);
			}
		}
		else{
			hycus::redirect(huri::makeuri("?module=user&task=register&menuid=".hycus::getcleanvar("menuid")),"Security Image Error. Please check it.");
		}
	}
	else{
		if(hycus::gethycusdata($data,"registrationtos")){
			if($tos){
				hycus_Auth::register($full_name, $user_name, $usr_email, $md5pass);
			}
			else{
				hycus::redirect(huri::makeuri("?module=user&task=register&menuid=".hycus::getcleanvar("menuid")),"You need to accept our Terms.");
			}
		}
		else{
			hycus_Auth::register($full_name, $user_name, $usr_email, $md5pass);
		}
	}
}
function activate(){

	$c = hycus::getcleanvar("c");
	$uid = hycus::getcleanvar("uid");
	hycus_Auth::activate($uid,$c);
}
function changepass(){

	$oldpwd = md5(hycus::getcleanvar("oldpwd"));
	$pwd = md5(hycus::getcleanvar("pwd"));
	$curent_uid = hycus::getcleanvar("curent_uid");

	$db = new hdatabase();
	$passobj = $db->get_rec("#__users", "password", "id = '$curent_uid'");
	if($passobj->password == $oldpwd)
	{
		if($db->db_update("#__users", "password = '".$pwd."'", "id = '".$curent_uid."'"))
		{
			$msg = passwordupdatesuccess;
		}
		else{
				$msg = updatingerrror;
		}
	}
	else{
			$msg = oldpasswrong;
	}
	hycus::redirect(huri::makeuri("?module=user&task=editprofile&menuid=".hycus::getcleanvar("menuid")), $msg);
}
function update(){

	$full_name = hycus::getcleanvar("full_name");
	$user_name = hycus::getcleanvar("user_name");
	$usr_email = hycus::getcleanvar("usr_email");
	$curent_uid = hycus::getcleanvar("curent_uid");

	$db = new hdatabase();
	$userobj = $db->get_rec("#__users", "name, username, email", "id = '$curent_uid'");

	if($userobj->name != $full_name){
		if($db->db_update("#__users", "name = '".$full_name."'", "id = '".$curent_uid."'"))
		{
			$msg = profileupdatesuccess;
		}
		else{
			$msg = updatingerrror;
			hycus::redirect(huri::makeuri("?module=user&task=editprofile&menuid=".hycus::getcleanvar("menuid")), $msg);

		}
	}

	$usernameobj = $db->get_rec("#__users", "id", "username = '$user_name'");
	if($userobj->username != $user_name && !$usernameobj->id){
		if($db->db_update("#__users", "username = '".$user_name."'", "id = '".$curent_uid."'"))
		{
			$msg = profileupdatesuccess;
		}
		else{
			$msg = updatingerrror;
			hycus::redirect(huri::makeuri("?module=user&task=editprofile&menuid=".hycus::getcleanvar("menuid")), $msg);
		}
	}

	$emailobj = $db->get_rec("#__users", "id", "email = '$usr_email'");
	if($userobj->email != $usr_email && !$emailobj->id){
		if($db->db_update("#__users", "email = '".$usr_email."'", "id = '".$curent_uid."'"))
		{
			$msg = profileupdatesuccess;
		}
		else{
			$msg = updatingerrror;
			hycus::redirect(huri::makeuri("?module=user&task=editprofile&menuid=".hycus::getcleanvar("menuid")), $msg);
		}
	}
	hycus::redirect(huri::makeuri("?module=user&task=editprofile&menuid=".hycus::getcleanvar("menuid")), $msg);

}
function forgot(){
	$hview = new hycusUserView();
	$hview->forgot();
}
function resetpass(){
	$hview = new hycusUserView();
	$hview->resetpass();
}
function forgotpass(){
	global $sitename, $adminemail;
	$db = new hdatabase();

	$user_email = hycus::getcleanvar("useremail");
	if (strpos($user_email,'@') === false) {

		$emailobj = $db->get_rec("#__users", "email", "username = '$user_email'");
		$emailto = $emailobj->email;
		$user_cond = "username='$user_email'";
	} else {
	    $emailto = $user_email;
	    $user_cond = "email = '$user_email'";
	}

	$udetails = $db->get_rec("#__users", "id, email, auth_token", "$user_cond");
	if($udetails->id && $udetails->auth_token){
		$link = huri::makeuri("?module=user&task=resetpass&uid=".$udetails->id."&c=".$udetails->auth_token);

		$mail = new hycus_Mailer("$sitename","$adminemail");
		$mail->subject("[Password Recovery Email] from ". $sitename);
		$mail->to($udetails->email);
		$mail->text("Click the following link to initiate password reset procedure. \n".$link." \n\nPlease dont reply to this email. \nThis is automatically generated.");
		$mail->send();

		hycus::redirect(huri::makeuri("?module=user&task=login&menuid=".hycus::getcleanvar("menuid")), "Check your email for the new password.");
	}
	else{
		echo entercorrectdetails;
	}
}
function authresetpass(){
	$authtoken=hycus::getcleanvar("authtoken");
	$password=md5(hycus::getcleanvar("pwd"));

	$db=new hdatabase();
	$auid = $db->get_rec("#__users", "id", "auth_token = '".$authtoken."'");
	if($auid->id){
		if($db->db_update("#__users", "password = '".$password."'", "id = '".$auid->id."'"))
		{
			$msg = passwordupdatesuccesslogin;
		}
		else{
			$msg = updatingerrror;
		}
	}
	else{
		$msg = updatingerrror;
	}
	hycus::redirect(huri::makeuri("?module=user&task=login&menuid=".hycus::getcleanvar("menuid")), $msg);
}
function uploadavatar(){
	$current_user = hycus::getthisuserid();
	$change="";
	$abc="";
	define ("MAX_SIZE","400");
	$errors=0;

	if($_SERVER["REQUEST_METHOD"] == "POST")
	{
	 	$image =$_FILES["file"]["name"];
		$uploadedfile = $_FILES['file']['tmp_name'];
	 	if ($image)
		{
	 		$filename = stripslashes($_FILES['file']['name']);
	  		$extension = getExtension($filename);
	 		$extension = strtolower($extension);
			if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif"))
			{
	 			$change=fileunknown;
	 			$errors=1;
			}
			else
			{
				$size=filesize($_FILES['file']['tmp_name']);
				if ($size > MAX_SIZE*1024)
				{
					$change=exceedsize;
					$errors=1;
				}
				$scr="";
				if($extension=="jpg" || $extension=="jpeg" )
				{
					$uploadedfile = $_FILES['file']['tmp_name'];
					$src = imagecreatefromjpeg($uploadedfile);
				}
				else if($extension=="png")
				{
					$uploadedfile = $_FILES['file']['tmp_name'];
					$src = imagecreatefrompng($uploadedfile);
				}
				else
				{
					$src = imagecreatefromgif($uploadedfile);
				}
				echo $scr;
				list($width,$height)=getimagesize($uploadedfile);
				$newwidth=100;
				$newheight = 100;
				$tmp=imagecreatetruecolor($newwidth,$newheight);

				$newwidth1=50;
				$newheight1 = 50;
				$tmp1=imagecreatetruecolor($newwidth1,$newheight1);
				imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
				imagecopyresampled($tmp1,$src,0,0,0,0,$newwidth1,$newheight1,$width,$height);

				$filename = "images/avatar/". $_FILES['file']['name'];
				$filename1 = "images/avatar/thumbs/". $_FILES['file']['name'];

				imagejpeg($tmp,$filename,100);
				imagejpeg($tmp1,$filename1,100);

				imagedestroy($src);
				imagedestroy($tmp);
				imagedestroy($tmp1);
			}
		}
	}
	//If no errors registred, print the success message
	if(isset($_POST['Submit']) && !$errors)
	{
		// mysql_query("update {$prefix}users set img='$big',img_small='$small' where user_id='$user'");
		$change= imguploadsuccess;
	}
	echo "<script>alert('$change');</script>";
	$db = new hdatabase();
	$oldavatar = $db->get_rec("#__avatar", "avatar", "user_id = '$current_user'");
	if($oldavatar->avatar){
		$db->db_update("#__avatar", "avatar = '".$_FILES['file']['name']."'", "user_id = '$current_user'");
	}
	else{
		$db->db_insert("#__avatar", "user_id, avatar", "'$current_user', '".$_FILES['file']['name']."'");
	}
	hycus::redirect(huri::makeuri("?module=user&task=editprofile&menuid=".hycus::getcleanvar("menuid")), $change);
}
function getExtension($str) {
         $i = strrpos($str,".");
         if (!$i) { return ""; }
         $l = strlen($str) - $i;
         $ext = substr($str,$i+1,$l);
         return $ext;
 }

?>
