<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

defined( 'HYCUSADMINPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

$task = hycus::getcleanvar("task");
$contactid = hycus::getcleanvar("id");

switch($task){
	case "contactform":
		addeditcontact($contactid);
		break;
	case "addcontact":
		addeditcontact();
		break;
	case "editcontact":
		addeditcontact($contactid);
		break;
	case "enablecontact":
		enablecontact($contactid);
		break;
	case "disablecontact":
		disablecontact($contactid);
		break;
	case "deletecontact":
		deletecontact($contactid);
		break;
	default:
		contactlist();
		break;
}

function contactlist(){

	$db = new hdatabase();
	$contactobj = $db->get_recs("#__contact", "*", "");

	echo "<h4 style='text-transform:capitalize;'>Contacts</h4>";

	echo "<div style='text-align:right;margin:10px 0;'> + ";
	hycus::adminlink("addcontact", "?adminmodule=contact&task=contactform", "modulewrapper", "Add a contact");
	echo "</div>";

	echo "<table cellpadding='4' width='100%' class='admintable' border='1'>" .
			"<thead><tr>" .
			"<td align='center' width='3%'>S.No</td>" .
			"<td align='center'>Contact Title</td>" .
			"<td align='center' width='3%'>Id</td>" .
			"<td align='center' width='5%'>Delete</td>" .
			"</tr></thead>";
	$count=1;
	echo "<tbody>";
	foreach($contactobj AS $contact)
	{
		if($count%2==0){ echo "<tr class='even'>"; }else{ echo "<tr class='odd'>"; }

		echo "<td align='center'>$count</td>";
		echo "<td>";
		hycus::adminlink("editcontact_".$contact->id, "?adminmodule=contact&task=contactform&id=$contact->id", "modulewrapper", "$contact->title");
		echo "</td>";
		echo "<td align='center'>".$contact->id."</td>";
		echo "<td align='center'>";
		hycus::adminlink("deletecontact_".$contact->id, "?adminmodule=contact&task=deletecontact&id=$contact->id", "modulewrapper", hycus::iconimage('delete.png'), "Delete?", "Are you sure you want to delete this contact? There is no UNDO.");
		echo "</td>";
		$count++;
	}
	echo "</tbody></table>";
}
function addeditcontact($contactid = null){

	$task = hycus::getcleanvar("task");

	$db = new hdatabase();
	if($contactid)
		$contactobj = $db->get_rec("#__contact", "*", "id='$contactid'");

	if($task == "addcontact"){

		hycus::checkformhash() or die("Invalid Request");

	 	$title = hycus::getcleanvar("title");
	 	$introtext = hycus::getcleanvar("introtext");
	 	$sendto = hycus::getcleanvar("sendto");

		if($title && $sendto)
		$db->db_insert("#__contact", "title, introtext, sendto", "'$title', '$introtext', '$sendto'");

		hycus::ajax_redirect("?adminmodule=contact", "modulewrapper");
	}
	elseif($task == "editcontact"){

		hycus::checkformhash() or die("Invalid Request");

	 	$title = hycus::getcleanvar("title");
	 	$introtext = hycus::getcleanvar("introtext");
	 	$sendto = hycus::getcleanvar("sendto");

		if($title && $sendto)
		$db->db_update("#__contact", "title='$title', introtext='$introtext', sendto='$sendto'", "id = '$contactid'");

		hycus::ajax_redirect("?adminmodule=contact", "modulewrapper");
	}
	else{

		echo "<h4 style='text-transform:capitalize;'>";
		if($contactid){
			echo " Edit ".$contactobj->title."</h4>";
		}
		else {
			echo "Add contact</h4>";
		}

		echo "<h3>Contact Details</h3>";
		hycus::admin_form("admincontactForm", "modulewrapper");
		echo "<form action='?adminmodule=contact' method='post' name='admincontactForm' id='admincontactForm'>";
		echo "<table cellpadding='5' style='padding:15px 0 0'>";

		echo "<tr>";
			echo "<td><label>Page title: </label></td>";
			echo "<td><span><input type='text' name='title' size='30' value='".$contactobj->title."' class='required'/></span></td>";
		echo "</tr>";

		echo "<tr>";
			echo "<td><label>Send to Email: </label></td>";
			echo "<td><span><input type='text' name='sendto' size='30' value='".$contactobj->sendto."' class='required'/></span></td>";
		echo "</tr>";

		echo "<tr>";
			echo "<td valign='top'><label>Intro Text: </label></td>";
			echo "<td><span><textarea name='introtext' rows='6' cols='30' >".$contactobj->introtext."</textarea></span></td>";
		echo "</tr>";

		echo "<tr>";
			echo "<td>";
			if($contactid){ ?><input type="hidden" name="id" value="<?php echo $contactid ?>" /><input type="hidden" name="task" value="editcontact" /><?php }else{ ?><input type="hidden" name="task" value="addcontact" /><?php }
			echo "</td>";
			echo "<td>".hycus::addformhash()."<input type='submit' value='Submit' class='button'/></td>";
		echo "</tr>";

		echo "</form>";
		echo "</table>";
		hycus::adminlink("goback_contact", "?adminmodule=contact", "modulewrapper", "<< Cancel & Go back");
	}
}
function deletecontact($contactid){
	hycus::checkformhash() or die("Invalid Request");
	$db = new hdatabase();
	$db->db_delete("#__contact", "id = '$contactid'");

	hycus::ajax_redirect("?adminmodule=contact&task=contactlist", "modulewrapper");
}
function enablecontact($contactid){
	hycus::checkformhash() or die("Invalid Request");
	$db = new hdatabase();
	$db->db_update("#__contact", "enabled = '1'", "id = '$contactid'");

	hycus::ajax_redirect("?adminmodule=contact&task=contactlist", "modulewrapper");
}
function disablecontact($contactid){
	hycus::checkformhash() or die("Invalid Request");
	$db = new hdatabase();
	$db->db_update("#__contact", "enabled = '0'", "id = '$contactid'");

	hycus::ajax_redirect("?adminmodule=contact&task=contactlist", "modulewrapper");
}

?>
