<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

 defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );
hycusLoader::hycusloadview("comment");
$task = hycus::getcleanvar("task");
switch($task){
	case "addcomment";
		addcomment();
		break;
	case "approvecomment";
		approvecomment();
		break;
	case "deletecomment";
		deletecomment();
		break;
	case "viewcomments";
		break;
	default:
		viewcomments();
		commentform();
		break;
}
function viewcomments(){

	$module = hycus::getcleanvar("module");
	$item_id = hycus::getcleanvar("id");
	$db = new hdatabase();

	if($module=="content"){
		$cuid = $db->get_rec("#__contents", "uid", "id ='$item_id'");

		if($cuid->uid == hycus::getthisuserid())
			$comment_data = $db->get_recs("#__comments", "*", "module = '$module' AND item_id ='$item_id'", "time DESC");
		else
			$comment_data = $db->get_recs("#__comments", "*", "module = '$module' AND item_id ='$item_id' AND (approved='1' OR uid='".hycus::getthisuserid()."')", "time DESC");
	}
	else {
		$comment_data = $db->get_recs("#__comments", "*", "module = '$module' AND item_id ='$item_id'", "time DESC");
	}
	echo "<div id='comments'>";
	hycusCommentView::viewcomment($comment_data);
	echo "</div>";
}

function addcomment(){
	$module = hycus::getcleanvar("cm");
	$item_id = hycus::getcleanvar("item_id");
	$uid = hycus::getthisuserid();
	$comment = hycus::getcleanvar("comment");
	$time = time();
	if($comment){
		$db = new hdatabase();
		$commentid = $db->db_insert("#__comments", "item_id, uid, module, time, comment", "'$item_id', '$uid', '$module', '$time', '$comment'");
		$comment_data = $db->get_recs("#__comments", "*", "id = '$commentid'");
		hycusCommentView::viewcomment($comment_data);

		if($module=="content"){
			$content = $db->get_rec("#__contents", "uid, title", "id = '$item_id'");
			$mail = new hycus_Mailer(hycus::getuserfullname(), hycus::getuseremail());
			$mail->subject("New comment added to your post '$content->title'");
			$mail->to(hycus::getuseremail($content->uid));
			$emailcontent = "A new comment has been added to your blog post '$content->title' \n" .
					"click the following link to check it \n ".
					huri::makeuri("?module=content&task=blog&id=$item_id")." \n\n Please do not reply to this email";
			$mail->text($emailcontent);
			$mail->send();
		}
	}
}
function approvecomment(){

	$commentid = hycus::getcleanvar("commentid");
	$cm = hycus::getcleanvar("cm");
	$itemid = hycus::getcleanvar("itemid");

	if($cm=="content")
	{
		$db = new hdatabase();
		$uid = $db->get_rec("#__contents", "uid", "id = '$itemid'");

		if($uid->uid == hycus::getthisuserid()){
			$db->db_update("#__comments", "approved = '1' ", " id = '$commentid'");
		}
	}
}
function deletecomment(){

	$commentid = hycus::getcleanvar("commentid");
	$commentuid = hycus::getcleanvar("commentuid");
	$db = new hdatabase();
	if($commentuid == hycus::getthisuserid()){
		$db->db_delete("#__comments", " id = '$commentid'");
	}
}
function commentform(){
	hycusCommentView::commentform();
}
?>
