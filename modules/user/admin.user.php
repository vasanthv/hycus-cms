<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

 defined( 'HYCUSADMINPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

$task = hycus::getcleanvar("task");
$userid = hycus::getcleanvar("id");

switch($task){
	case "userform":
		addedituser($userid);
		break;
	case "adduser":
		addedituser();
		break;
	case "edituser":
		addedituser($userid);
		break;
	case "enableuser":
		enableuser($userid);
		break;
	case "disableuser":
		disableuser($userid);
		break;
	case "deleteuser":
		deleteuser($userid);
		break;
	case "activateuser":
		activateuser($userid);
		break;
	case "inactivateuser":
		inactivateuser($userid);
		break;
	default:
		userlist();
		break;
}

function userlist(){

	$db = new hdatabase();
	$userobj = $db->get_recs("#__users", "*");

	echo "<h4 style='text-transform:capitalize;'>users</h4>";

	echo "<div style='text-align:right;margin:10px 0;'> + ";
	hycus::adminlink("adduser", "?adminmodule=user&task=userform", "modulewrapper", "Add a user");
	echo "</div>";


	echo "<table cellpadding='4' width='100%' class='admintable' border='1'>" .
			"<thead><tr>" .
			"<td align='center' width='5%'>S.No</td>" .
			"<td align='center'>Username</td>" .
			"<td align='center'>Name</td>" .
			"<td align='center'>Email</td>" .
			"<td align='center'>Type</td>" .
			"<td align='center' width='5%'>Active</td>" .
			"<td align='center' width='5%'>Delete</td>" .
			"</tr></thead>";
	$count=1;
	echo "<tbody>";
	foreach($userobj AS $user)
	{
		if($count%2==0){ echo "<tr class='even'>"; }else{ echo "<tr class='odd'>"; }

		echo "<td align='center'>$count</td>";
		echo "<td>";
		hycus::adminlink("edituser_".$user->id, "?adminmodule=user&task=userform&id=$user->id", "modulewrapper", "$user->username");
		echo "</td>";
		echo "<td>";
		echo $user->name;
		echo "</td>";
		echo "<td>";
		echo "<a href='mailto:".$user->email."' target='_blank'>".$user->email."</a>";
		echo "</td>";
		echo "<td>";
		$usertypeobj = $db->get_rec("#__usertypes", "usertype", "id='$user->typeid'");
		echo "$usertypeobj->usertype";
		echo "</td>";
		echo "<td align='center'>";
			if($user->approved)
				hycus::adminlink("activeuser_".$user->id, "?adminmodule=user&task=inactivateuser&id=$user->id", "modulewrapper", hycus::iconimage('yes.png'), "Block?");
			else
				hycus::adminlink("activeuser_".$user->id, "?adminmodule=user&task=activateuser&id=$user->id", "modulewrapper", hycus::iconimage('no.png'), "Activate?");
		echo "</td>";
		echo "<td align='center'>";
			if($user->id != 1)
				hycus::adminlink("deleteuser_".$user->id, "?adminmodule=user&task=deleteuser&id=$user->id", "modulewrapper", hycus::iconimage('delete.png'), "Delete?", "Are you sure you want to delete this User? There is no UNDO.");
		echo "</td>";
		$count++;
	}
	echo "</tbody></table>";
}
function addedituser($userid = null){

	$task = hycus::getcleanvar("task");

	$db = new hdatabase();
	if($userid)
		$userobj = $db->get_rec("#__users", "*", "id='$userid'");

	if($task == "adduser"){

		hycus::checkformhash() or die("Invalid Request");

		$username =  hycus::getcleanvar("username");
		$fullname =  hycus::getcleanvar("fullname");
		$email =  hycus::getcleanvar("email");
		$pwdorg =  hycus::getcleanvar("pwd");
		$pwd =  md5(hycus::getcleanvar("pwd"));
		$pwd2 =  md5(hycus::getcleanvar("password2"));
		$blockuser =  hycus::getcleanvar("blockuser");
		$usertype =  hycus::getcleanvar("usertype");
		$time = time();
		$auth_token =  md5( uniqid('hycus_') );

		$checkuservalue = $db->get_rec("#__users", "id", "email='$email' OR username='$username'");


		if(!$username || !$fullname || !$email)
		hycus::ajax_redirect("?adminmodule=user&task=userform", "modulewrapper", "Please enter all the details required.");

		elseif(!hycus::isvalidemail($email))
		hycus::ajax_redirect("?adminmodule=user&task=userform", "modulewrapper", "Please Enter a valid Email");

		elseif($pwdorg && $pwd != $pwd2)
		hycus::ajax_redirect("?adminmodule=user&task=userform", "modulewrapper", "Oops!! Passwords do not match");

		elseif(!$usertype)
		hycus::ajax_redirect("?adminmodule=user&task=userform", "modulewrapper", "Oops!! Please choose a User type");

		elseif($checkuservalue->id)
		hycus::ajax_redirect("?adminmodule=user&task=userform", "modulewrapper", "The Email / Username is already registered.");

		else{
		$db->db_insert("#__users", "name, username, email, typeid, registeredon, auth_token, block, password", "'$fullname', '$username', '$email', '$usertype', '$time', '$auth_token', '$blockuser', '$pwd' ");
		hycus::ajax_redirect("?adminmodule=user&task=userlist", "modulewrapper");
		}
	}
	elseif($task == "edituser"){

		hycus::checkformhash() or die("Invalid Request");

		$username =  hycus::getcleanvar("username");
		$fullname =  hycus::getcleanvar("fullname");
		$email =  hycus::getcleanvar("email");
		$pwdorg =  hycus::getcleanvar("pwd");
		$pwd =  md5(hycus::getcleanvar("pwd"));
		$pwd2 =  md5(hycus::getcleanvar("password2"));
		$usertype =  hycus::getcleanvar("usertype");
		$blockuser =  hycus::getcleanvar("blockuser");

		if(!$username || !$fullname)
		hycus::ajax_redirect("?adminmodule=user&task=userform&id=".$userid, "modulewrapper", "Please enter all the details required.");

		elseif(!hycus::isvalidemail($email))
		hycus::ajax_redirect("?adminmodule=user&task=userform&id=".$userid, "modulewrapper", "Please Enter a valid Email");

		elseif($pwd != $pwd2)
		hycus::ajax_redirect("?adminmodule=user&task=userform&id=".$userid, "modulewrapper", "Oops!! Passwords do not match");

		elseif(!$usertype)
		hycus::ajax_redirect("?adminmodule=user&task=userform&id=".$userid, "modulewrapper", "Oops!! Please choose a User type");

		else{
			if($pwdorg)
				$db->db_update("#__users", "name='$fullname', username='$username', email='$email', typeid='$usertype', block='$blockuser', password='$pwd'", "id = '$userid'");
			else
				$db->db_update("#__users", "name='$fullname', username='$username', email='$email', typeid='$usertype', block='$blockuser'", "id = '$userid'");
			hycus::ajax_redirect("?adminmodule=user&task=userlist", "modulewrapper");
		}
	}
	else{

		echo "<h4 style='text-transform:capitalize;'>";
		if($userid){
			echo " Edit ".$userobj->title."</h4>";
		}
		else {
			echo "Add user</h4>";
		}

		hycus::admin_form("userForm", "modulewrapper");
		?>
		<form id="userForm" action="?adminmodule=user" method="post" class='adminhycusforms'>
			<label>Username</label>
			<div><input type="text" name="username" value="<?php echo $userobj->username; ?>" class="textbox required"/></div>

			<label>Name</label>
			<div><input type="text" name="fullname" value="<?php echo $userobj->name; ?>" class="textbox required"/></div>

			<label>Email</label>
			<div><input type="text" name="email" value="<?php echo $userobj->email; ?>" class="textbox email required"/></div>

			<label>Password</label>
			<div><input type="password" name="pwd" id="pwd" value="" class="textbox password"/></div>

			<label>Re-type password</label>
			<div><input type="password" name="password2" value="" class="textbox password" equalto="#pwd"/></div>

			<?php
				$db = new hdatabase();
				$usertypeobj = $db->get_recs("#__usertypes", "*");
				echo "<div>";
				echo "<label style='margin:0 10px 0 0;'>Select Usertype</label>";
				echo "<select name='usertype' id='usertype' class='required'>";
				echo "<option value=''>Select Category</option>";
				foreach($usertypeobj AS $usertype)
				{
					echo "<option value='".$usertype->id."'";
					if($userobj->typeid == $usertype->id){ echo "selected=SELECTED"; }
					echo ">". $usertype->usertype ."</option>";
				}
				echo "</select>";
				echo "</div>";
			?>

			<div><label>Block this user? </label> <input type="checkbox" name="blockuser" value="1" <?php if($userobj->block){ ?>CHECKED=CHECKED<?php } ?> /></div>

			<?php if($userid){ ?><input type="hidden" name="id" value="<?php echo $userid ?>" /><input type="hidden" name="task" value="edituser" /><?php }else{ ?><input type="hidden" name="task" value="adduser" /><?php } ?>
			<?php hycus::addformhash(); ?>
			<input type="submit" value="Save" class="button"/>
		</form><?php
		hycus::adminlink("goback_user", "?adminmodule=user", "modulewrapper", "<< Cancel & Go back");
	}
}
function deleteuser($userid){
	hycus::checkformhash() or die("Invalid Request");
	if($userid != "1")
	{
		$db = new hdatabase();
		$db->db_delete("#__users", "id = '$userid'");
	}
	hycus::ajax_redirect("?adminmodule=user&task=userlist", "modulewrapper");
}
function activateuser($userid){
	hycus::checkformhash() or die("Invalid Request");
	$db = new hdatabase();
	$db->db_update("#__users", "approved = '1'", "id = '$userid'");

	hycus::ajax_redirect("?adminmodule=user&task=userlist", "modulewrapper");
}
function inactivateuser($userid){
	hycus::checkformhash() or die("Invalid Request");
	$db = new hdatabase();
	if($userid!=1)
	$db->db_update("#__users", "approved = '0'", "id = '$userid'");

	hycus::ajax_redirect("?adminmodule=user&task=userlist", "modulewrapper");
}

?>
