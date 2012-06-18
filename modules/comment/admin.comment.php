<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

 defined( 'HYCUSADMINPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

$task = hycus::getcleanvar("task");
$commentid = hycus::getcleanvar("id");

switch($task){
	case "approvecomment":
		approvecomment($commentid);
		break;
	case "deletecomment":
		deletecomment($commentid);
		break;
	default:
		commentlist();
		break;
}

function commentlist(){

	$db = new hdatabase();
	$where="";
	$filtermodule = hycus::getcleanvar("filtermodule");
	$filtercontent = hycus::getcleanvar("filtercontent");
	if($filtermodule)
	{
		$where .= " module='".$filtermodule."' ";
		if($filtermodule=="content" && $filtercontent)
		{
			$where .= " AND item_id='".$filtercontent."' ";
		}

	}
	$commentobj = $db->get_recs("#__comments", "*", "$where", "time DESC");

	echo "<h4 style='text-transform:capitalize;'>Comments</h4>";

	/*Comment Filter Starts*/
	hycus::admin_form("commentfilterForm", "modulewrapper");
	echo "<form action='?adminmodule=comment' method='post' id='commentfilterForm' class='adminhycusforms' style='float:left;margin:0 2px;'>";
	$commentmodulesobj = $db->get_recs("#__comments", "DISTINCT module", "");

	echo "<select name='filtermodule' class='required'>";
	echo "<option value=''>Select Module</option>";
	foreach($commentmodulesobj AS $commentmodules)
	{
		echo "<option value='".$commentmodules->module."'";
		if($filtermodule==$commentmodules->module) { echo "SELECTED=SELECTED";}
		echo ">".$commentmodules->module."</option>";
	}
	echo "</select>&nbsp;";
	echo "<input type='submit' value='Go'/>";
	echo "</form>";

	if($filtermodule=="content")
	{
		hycus::admin_form("commentcontentfilterForm", "modulewrapper");
		echo "<form action='?adminmodule=comment' method='post' id='commentcontentfilterForm' class='adminhycusforms' style='float:left;margin:0 2px;'>";
		$commentcontentobj = $db->get_recs("#__comments", "DISTINCT item_id", "module='content'");

		echo "<select name='filtercontent' class='required'>";
		echo "<option value=''>Select Content</option>";
		foreach($commentcontentobj AS $commentcontent)
		{
			echo "<option value='".$commentcontent->item_id."'";
			if($filtercontent==$commentcontent->item_id) { echo "SELECTED=SELECTED";}
			echo " >";
			$contentnameobj = $db->get_rec("#__contents", "title", "id='".$commentcontent->item_id."'");
			echo $contentnameobj->title;
			echo "</option>";
		}
		echo "</select>&nbsp;";
		echo "<input type='hidden' name='filtermodule' value='content'/>";
		hycus::addformhash();
		echo "<input type='submit' value='Go'/>";
		echo "</form>";
	}

	/*Comment Filter Ends*/

	echo "<table cellpadding='5' width='100%' class='admintable' border='1'>" .
			"<thead><tr>" .
			"<td align='center' width='3%'>S.No</td>" .
			"<td align='center'>Comment Details</td>" .
			"<td align='center' width='3%'>Approve</td>" .
			"<td align='center' width='5%'>Delete</td>" .
			"</tr></thead>";
	if($commentobj){
	$count=1;
	echo "<tbody>";
		foreach($commentobj AS $comment)
		{
			if($count%2==0){ echo "<tr class='even'>"; }else{ echo "<tr class='odd'>"; }

			echo "<td align='center'>$count</td>";
			echo "<td>";
			echo '<div style="line-height:1.8em;"><span style="font-size:1.7em;font-weight:bold;font-family:Georgia;font-style:italic;">“ </span>'.$comment->comment.'<span style="font-size:1.7em;font-weight:bold;font-family:Georgia;font-style:italic;"> ”</span></div>';
			echo "<div style='color:#616161;'>";
				echo "<small>Added by: <b>".hycus::getusername($comment->uid)."</b>;</small>&nbsp;";
				echo "<small>Added on: <b>".hycus::showtime($comment->time)."</b>;</small>&nbsp;";
				echo "<small>Module: <b>".$comment->module."</b>;</small>&nbsp;";
				if($comment->module=="content")
				{
					$content = $db->get_rec("#__contents", "title", "id='$comment->item_id'");
					echo "<small>Post: <b><a href='".huri::makeuri("?module=content&task=blog&id=".$comment->item_id)."' target='_blank'>".$content->title."</a></b>;</small>&nbsp;";
				}
			echo "</div>";
			echo "</td>";
			echo "<td align='center'>";
			if(!$comment->approved){

				$approvelink = "?adminmodule=comment&task=approvecomment&id=$comment->id";
				if($filtermodule){ $approvelink .= "&filtermodule=$filtermodule"; }
				if($filtercontent){ $approvelink .= "&filtercontent=$filtercontent"; }

				hycus::adminlink("approvecomment_".$comment->id, "$approvelink", "modulewrapper", hycus::iconimage('pending.png'), "Approve?");
			}
			else{
				echo hycus::iconimage('yes.png');
			}
			echo "</td>";
			echo "<td align='center'>";
			$deletelink = "?adminmodule=comment&task=deletecomment&id=$comment->id";
			if($filtermodule){ $deletelink .= "&filtermodule=$filtermodule"; }
			if($filtercontent){ $deletelink .= "&filtercontent=$filtercontent"; }
			hycus::adminlink("deletecomment_".$comment->id, "$deletelink", "modulewrapper", hycus::iconimage('delete.png'), "Delete?", "Are you sure you want to delete this Comment? There is no UNDO.");
			echo "</td>";
			$count++;
		}
		echo "</tbody>";
	}
	echo "</table>";
}
function approvecomment($commentid){

	hycus::checkformhash() or die("Invalid Request");

	$db = new hdatabase();

	$filtermodule = hycus::getcleanvar("filtermodule");
	$filtercontent = hycus::getcleanvar("filtercontent");

	$db->db_update("#__comments", "approved='1'", "id = '$commentid'");

	$redirectlink = "?adminmodule=comment";
	if($filtermodule){ $redirectlink .= "&filtermodule=$filtermodule"; }
	if($filtercontent){ $redirectlink .= "&filtercontent=$filtercontent"; }

	hycus::ajax_redirect("$redirectlink", "modulewrapper");
}
function deletecomment($commentid){

	hycus::checkformhash() or die("Invalid Request");

	$db = new hdatabase();

	$filtermodule = hycus::getcleanvar("filtermodule");
	$filtercontent = hycus::getcleanvar("filtercontent");

	$db->db_delete("#__comments", "id = '$commentid'");

	$redirectlink = "?adminmodule=comment";
	if($filtermodule){ $redirectlink .= "&filtermodule=$filtermodule"; }
	if($filtercontent){ $redirectlink .= "&filtercontent=$filtercontent"; }

	hycus::ajax_redirect("$redirectlink", "modulewrapper");
}
?>
