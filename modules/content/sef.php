<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */


 class sef_content{

	function makesefurl($orgurl){

		$newsef ="";
 		$orgurlarr = explode("&",$orgurl);
		foreach($orgurlarr AS $orgurlar){
			$orgurlsp = explode("=",$orgurlar);
			if($orgurlsp[0]=="task"){
				$task = $orgurlsp[1];
				$newsef .= $this->gettask($task);
			}
			if($orgurlsp[0]=="id" || $orgurlsp[0]=="catid"){
				$urlid = $orgurlsp[1];
			}
			if($orgurlsp[0]=="pge"){
				$pageid = $orgurlsp[1];
			}
		}
		$urlpart2 = $this->geturlpart2($task, $urlid, $pageid);
		return $newsef.$urlpart2;
	}
	function gettask($task){
		switch ($task){
			case "page":
				return "page/";
				break;
			case "blog":
				return "blog/";
				break;
			case "catview":
				return "category/";
				break;
			case "addblog":
				return "addblog/";
				break;
			case "editblog":
				return "edit/";
				break;

		}
	}
	function geturlpart2($task, $urlid, $pageid){
		$db =new hdatabase();
		switch ($task){
			case "page":
				$nameobj = $db->get_rec("#__contents", "title", "id='$urlid'");
				return strtolower(str_ireplace(" ", "-", $nameobj->title))."/";
				break;
			case "blog":
				$nameobj = $db->get_rec("#__contents", "title", "id='$urlid'");
				return strtolower(str_ireplace(" ", "-", $nameobj->title))."/";
				break;
			case "catview":
				$nameobj = $db->get_rec("#__categories", "title", "id='$urlid'");
				if($pageid)
				return strtolower(str_ireplace(" ", "-", $nameobj->title))."/".$pageid."/";
				else
				return strtolower(str_ireplace(" ", "-", $nameobj->title))."/";
				break;
			case "addblog":
				break;
			case "editblog":
				$nameobj = $db->get_rec("#__contents", "title", "id='$urlid'");
				return strtolower(str_ireplace(" ", "-", $nameobj->title))."/";
				break;

		}
	}
 }
?>
