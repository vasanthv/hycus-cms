<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

 defined( 'HYCUSADMINPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );


$task = hycus::getcleanvar("task");
$usertypeid = hycus::getcleanvar("id");
$usertypeitemid = hycus::getcleanvar("usertypeitemid");

switch($task){
	case "deleteusertype":
		deleteusertype($usertypeid);
		break;
	case "usertypeform":
		addeditusertype($usertypeid);
		break;
	case "addusertype":
		addeditusertype();
		break;
	case "editusertype":
		addeditusertype($usertypeid);
		break;
	case "edituserperms":
		edituserperms($usertypeid);
		break;
	case "saveuserperms":
		saveuserperms($usertypeid);
		break;
	case "moduleperms":
		moduleperms();
		break;
	default:
		usertypelist();
		break;
}

function usertypelist()
{
	echo "<h4>User types</h4>";


	$db = new hdatabase();
	$usertypesarray = $db->get_recs("#__usertypes", "*");
	echo "<div style='text-align:right;margin:5px 0;'>+";
	hycus::adminlink("addusertype", "?adminmodule=usertypes&task=usertypeform", "modulewrapper", "Add New Usertype");
	echo "</div>";

	echo "<table cellpadding='4' width='100%' class='admintable' border='1'>" .
			"<thead><tr>" .
			"<td align='center' width='5%'>S.No</td>" .
			"<td align='center'>Usertype</td>" .
			"<td align='center' width='5%'>Edit</td>" .
			"<td align='center' width='5%'>Delete</td>" .
			"</tr></thead>";
	$count = 1;
	if($usertypesarray){
		echo "<tbody>";
		foreach($usertypesarray AS $usertype)
		{
			if($count%2==0){ echo "<tr class='even'>"; }else{ echo "<tr class='odd'>"; }

			echo "<td align='center'>".$count."</td>";
			echo "<td>";
			hycus::adminlink("listusertypeid".$usertype->id, "?adminmodule=usertypes&task=edituserperms&id=".$usertype->id, "modulewrapper", "$usertype->usertype");
			echo "</td>";
			echo "<td align='center'>";
			hycus::adminlink("editusertypeid".$usertype->id, "?adminmodule=usertypes&task=usertypeform&id=".$usertype->id, "modulewrapper", hycus::iconimage('edit.png'));
			echo "</td>";
			echo "<td align='center'>";
			hycus::adminlink("delusertypeid".$usertype->id, "?adminmodule=usertypes&task=deleteusertype&id=".$usertype->id, "modulewrapper", hycus::iconimage('delete.png'),"", "Are you sure you want to delete this Usertype? There is no UNDO.");
			echo "</td></tr>";
			$count++;
		}
		echo "</tbody>";
	}
	echo "</table>";
}
function edituserperms($id){
	$db = new hdatabase();
	$usertypeobj = $db->get_rec("#__usertypes", "usertype, adminaccess, contentae, moduleaccess", "id='$id'");

	echo "<h4>Edit usertype - ".$usertypeobj->usertype."</h4>";

	hycus::admin_form("userpermsForm", "modulewrapper"); ?>
	<br/>
	<form id="userpermsForm" action="?adminmodule=usertypes" method="post">
		<div>
			<label>Allow this usertype to add/edit content?</label> :
			<input type="checkbox" name="contentae" id="contentae" value="1" <?php if($usertypeobj->contentae){ echo "CHECKED=CHECKED"; } ?>/>
		</div><br/>

		<div>
			<label>Admin access for this usertype?</label> :
			<input type="checkbox" name="adminaccess" id="adminaccess" value="1" <?php if($usertypeobj->adminaccess){ echo "CHECKED=CHECKED"; } ?>/>
		</div>

		<div id='moduleusertypeperms'>
		<?php if($usertypeobj->adminaccess){ moduleperms($usertypeobj->moduleaccess); } ?>
		</div>

		<script type="text/javascript">
			$(function() {
				$("#adminaccess").click(function () {
					var adminaccessval = $('input#adminaccess:checked').val();
					if(adminaccessval){
						$.ajax({
				 		url: "?response=ajax&adminmodule=usertypes&task=moduleperms",
				  		success: function(html){
				  			$("div#ajax-loader").show();
							$("div#moduleusertypeperms").empty();
							$("div#moduleusertypeperms").prepend(html);
						  	$("div#moduleusertypeperms").fadeIn("slow");
				  			$("div#ajax-loader").hide();
				  			}
				 		});
					}
					else{
						$("div#moduleusertypeperms").empty();
					}
				});
			});
		</script>

		<input type="hidden" name="id" value="<?php echo $id; ?>" />

		<input type="hidden" name="task" value="saveuserperms" />
		<?php hycus::addformhash(); ?>
		<input type="submit" value="Submit" class="button"/>
	</form><?php
	hycus::adminlink("goback_usertype", "?adminmodule=usertypes", "modulewrapper", "<< Cancel & Go back");

}
function saveuserperms($id){
	hycus::checkformhash() or die("Invalid Request");
	$db = new hdatabase();
	$contentae =  hycus::getcleanvar("contentae");
	unset($_POST['contentae']);
	$adminaccess =  hycus::getcleanvar("adminaccess");
	unset($_POST['adminaccess']);
	unset($_POST['id']);
	unset($_POST['task']);
	if(!$adminaccess)
	{
		$db->db_update("#__usertypes", "adminaccess='0', contentae='$contentae'", "id='$id'");
	}
	else{
		$moduleaccess="";
		$count=0;
		$moduleaccessvals = $_POST;
		$modulenames = array_keys($_POST);
		foreach($moduleaccessvals AS $moduleaccessval)
		{
			if($count){$moduleaccess .=";"; }else{}
			$moduleaccess .= str_replace("allow_", "", $modulenames[$count]).":".$moduleaccessval;
			$count++;
		}
		if($moduleaccess)
			$db->db_update("#__usertypes", "adminaccess='1', contentae='$contentae', moduleaccess='$moduleaccess'", "id='$id'");
		else
			$db->db_update("#__usertypes", "adminaccess='1', contentae='$contentae'", "id='$id'");
	}


	hycus::ajax_redirect("?adminmodule=usertypes", "modulewrapper");
}
function deleteusertype($id){
	hycus::checkformhash() or die("Invalid Request");
	$db = new hdatabase();
	if($id == 1 || $id==2 || $id==3){echo "<script>alert('This is one of the default usertype, so this usertype cannot be deleted.');</script>";}
	else{ $db->db_delete("#__usertypes", "id='$id'"); }
	hycus::ajax_redirect("?adminmodule=usertypes", "modulewrapper");

}
function addeditusertype($usertypeid = null){

	$task = hycus::getcleanvar("task");
	$usertype = hycus::getcleanvar("usertype");

	$db = new hdatabase();
	if($usertypeid){ $usertypeobj = $db->get_rec("#__usertypes", "usertype", "id='$usertypeid'"); $current_usertype = $usertypeobj->usertype; }

	if($task == "addusertype"){
		hycus::checkformhash() or die("Invalid Request");
		if($usertype)
			$db->db_insert("#__usertypes", "usertype", "'$usertype'");
		hycus::ajax_redirect("?adminmodule=usertypes", "modulewrapper");
	}
	elseif($task == "editusertype"){
		hycus::checkformhash() or die("Invalid Request");
		$db->db_update("#__usertypes", "usertype='$usertype'", "id='$usertypeid'");
		hycus::ajax_redirect("?adminmodule=usertypes", "modulewrapper");
	}
	else{

	if($current_usertype){echo "<h4>Edit - ".$current_usertype. "</h4><br/>This will change the <b>".$current_usertype."</b> user type in your website. "; }
	else{ echo "<h4>Add new usertype</h4><br/>This will add a new user type to your website. You can assign permissions based on the usertype id."; }


	hycus::admin_form("axForm", "modulewrapper"); ?>

	<form id="axForm" action="?adminmodule=usertypes" method="post">
		<input type="text" name="usertype" value="<?php echo $current_usertype ?>" />
		<?php if($usertypeid){ ?><input type="hidden" name="id" value="<?php echo $usertypeid ?>" /><input type="hidden" name="task" value="editusertype" /><?php }else{ ?><input type="hidden" name="task" value="addusertype" /><?php } ?>
	    <?php hycus::addformhash(); ?>
	    <input type="submit" value="Submit" class="button"/>
	</form><?php
	hycus::adminlink("goback_usertype", "?adminmodule=usertypes", "modulewrapper", "<< Cancel & Go back");
	}
}
function moduleperms($moduleaccess=null){

	if($moduleaccess){
		$pieces = explode(";", $moduleaccess);
		foreach($pieces AS $piece)
		{
			$singledata = explode(":", $piece);

			$moduleacc[$singledata[0]] = $singledata[1];
		}
	}
	$dir = "modules/";
	echo "<br/><table cellpadding='5' class='admintable' border='1'>" .
		"<thead><tr><td>Module Name</td><td>Allow Access?</td></tr></thead>";
		// Open the Module directory, and proceed to read its contents
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					if(filetype($dir . $file)=="dir" && $file != "." && $file != "..")
					{
						if(is_file("modules/$file/admin.$file.php")){
						echo "<tr>";
							echo "<td>$file</td>";
							echo "<td align='center'>";
								echo "<input type='checkbox' name='allow_$file' value='1' ";
								if($moduleacc[$file]){ echo "CHECKED=CHECKED"; }
								echo "/>";
							echo "</td>";
						echo "</tr>";
						}
					}
				}
				closedir($dh);
			}
		}
	echo "</tr></table>";

}
?>
