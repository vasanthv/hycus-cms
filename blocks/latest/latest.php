<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

 defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );
class hycusBlock_latest {
	function loadthisblock($id,$data) {
		$catids = hycus::gethycusdata($data,"catids");
		$contentcount = hycus::gethycusdata($data,"count");
		$showdate = hycus::gethycusdata($data,"showdate");
		$blogblocktype = hycus::gethycusdata($data,"blogblocktype");

		echo "<div>";
		$db=new hdatabase();
		$where ="";
		if($catids){
			$catids =explode(",", $catids);
			$count = 0;
			foreach($catids AS $catid)
			{
				if($count==0){
					$where .= " catid = '$catid'";
				}
				else{
					$where .= " OR catid = '$catid' ";
				}
				$count++;
			}
		}

		if($where){
			$where = "($where) AND enabled='1'";
		}else{
			$where = "enabled='1'";
		}
		if($blogblocktype == "popular"){
			$order = "hits DESC";
		}
		else{
			$order = "lastupdated_on DESC";
		}

		$latests = $db->get_recs("#__contents", "id, title, lastupdated_on", $where, "$order", "0", "$contentcount");
		if($latests){
			echo "<ul>";
			foreach($latests AS $latest){
				echo "<li>";
				echo "<a href='".huri::makeuri("?module=content&task=blog&id=".$latest->id."&menuid=".hycus::getcleanvar("menuid"))."'>";
				echo $latest->title;
				echo "</a>";
				if($showdate){
					echo "<div><small>".hycus::showtime($latest->lastupdated_on)."</small></div>";
				}
				echo "</li>";
			}
			echo "</ul>";
		}
		echo "</div>";
	}
}
?>
