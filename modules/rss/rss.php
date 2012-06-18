<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

 defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

$response = hycus::getcleanvar("response");
$task = hycus::getcleanvar("task");
$id = hycus::getcleanvar("id");

if($response == "ajax" || $response == "module"){}
else { hycus::redirect("?module=rss&response=module&task=$task&id=$id"); }

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<!-- generator="Hycus CMS" -->';
echo '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
$db = new hdatabase();
if($task=="user"){
	if($id){
		echo "<channel>";
			echo "<title>".hycus::getusername($id)."</title>";
			echo "<link>".huri::makeuri("?module=user&task=profile&id=$id")."</link>";
			echo "<generator>Hycus CMS</generator>";
		$contents = $db->get_recs("#__contents", "id, title, data, lastupdated_on", "uid= '$id' AND enabled = '1'", "lastupdated_on", 0, 10);
		if($contents){
			foreach($contents AS $content){
				echo "<item>";
					echo "<title>".$content->title."</title>";
					echo "<description>";
						echo substr(hycus::cleanstring($content->data),0,"500");
					echo "</description>";
					echo "<pubDate>".date(DATE_ATOM, $content->lastupdated_on)."</pubDate>";
					echo "<link>".huri::makeuri("?module=content&task=blog&id=".$content->id)."</link>";
					echo "<guid>".huri::makeuri("?module=content&task=blog&id=".$content->id)."</guid>";
				echo "</item>";
			}
		}
		echo "</channel>";
	}
}
else{
	$where ="";
	if($id){
		$category = $db->get_rec("#__categories", "title, description", "id = '$id' AND user_cat='1'");
		if($category->title){
			echo "<channel>";
				echo "<title>$category->title</title>";
				echo "<link>".huri::makeuri("?module=content&task=catview&id=$id")."</link>";
				echo "<description>$category->description</description>";
				echo "<generator>Hycus CMS</generator>";
			$where .= "catid = '$id' AND ";
		}
	}
	else{
		global $sitename;
		echo "<channel>";
			echo "<title>".$sitename."</title>";
			echo "<link>".hycus::getroot()."</link>";
			echo "<description>".$sitename."</description>";
			echo "<generator>Hycus CMS</generator>";
	}
	$contents = $db->get_recs("#__contents", "id, title, data, lastupdated_on", "$where enabled = '1'", "lastupdated_on", 0, 10);
	if($contents){
		foreach($contents AS $content){
			echo "<item>";
				echo "<title>".$content->title."</title>";
				echo "<description>";
					echo substr(hycus::cleanstring($content->data),0,"500");
				echo "</description>";
				echo "<pubDate>".date(DATE_ATOM, $content->lastupdated_on)."</pubDate>";
				echo "<link>".huri::makeuri("?module=content&task=blog&id=".$content->id)."</link>";
				echo "<guid>".huri::makeuri("?module=content&task=blog&id=".$content->id)."</guid>";
			echo "</item>";
		}
	}
	echo "</channel>";
}
echo '</rss>';
?>
