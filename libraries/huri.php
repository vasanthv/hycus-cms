<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */


 defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

 class huri{
 	function parseuri(){

		//This function parses the SEF URL into original url.
		$db = new hdatabase();
		$sefconf = $db->get_rec("#__config", "enablesef, seftype, sefsuffix, showmodname", "identifier='1'");
		if($sefconf->enablesef){
	 		$count = 1;
			$currentsefurl =  str_ireplace(str_ireplace("http://".$_SERVER['HTTP_HOST']."/", "", hycus::getroot()), "", $_SERVER['REQUEST_URI'], $count );

			if(stripos($currentsefurl,"/")==0) { $currentsefurl = str_ireplace("/?", "", $currentsefurl, $count); }
			if(stripos($currentsefurl,"?")==0) { $currentsefurl = str_ireplace("?", "", $currentsefurl, $count); }

			/*Admin access.
			 * Change the following lines if you have renamed the admin.php file.
			 * If you have changed the following 2 lines consider changing the admin redirection after login at
			 * libraries/hycus_Auth.php line no 52*/
			if($currentsefurl=="admin")
			hycus::redirect(hycus::getroot()."admin.php");


			//removing the sef suffix from the url
			if($sefconf->sefsuffix){
				$currentsefurl = substr_replace($currentsefurl,"",-(strlen($sefconf->sefsuffix)),strlen($sefconf->sefsuffix));
			}

			//getting original url from database
			$orgurl = $db->get_rec("#__sefurls", "orgurl", "sefurl='$currentsefurl'");
			$orgurl = $orgurl->orgurl;

			$orgurlarr = explode("&",$orgurl);
			foreach($orgurlarr AS $orgurlar){
				$orgurlsp = explode("=",$orgurlar);
				$_REQUEST[$orgurlsp[0]]=hycus::cleanstring($orgurlsp[1]);
			}
		}
	}
 	function makeuri($orgurl){

		//This function converts non-sef URL to SEF url based on the configurations.
		$db = new hdatabase();
		$sefconf = $db->get_rec("#__config", "enablesef, seftype, sefsuffix, showmodname, showmenuid", "identifier='1'");
		$count="1";

		if($sefconf->enablesef && ($sefconf->seftype=="1" || $sefconf->seftype=="2")){

			$orgurl = str_ireplace("?", "", $orgurl, $count);
	 		$orgurlarr = explode("&",$orgurl);
	 		$newsef = "";
			foreach($orgurlarr AS $orgurlar){
				$orgurlsp = explode("=",$orgurlar);
				if($orgurlsp[0]=="sitename")
				{ $sitename .= $orgurlsp[1]."/"; }

				if($orgurlsp[0]=="module")
				{
					if($sefconf->showmodname) { $modname .= $orgurlsp[1]."/"; }
					if(is_file("modules/".$orgurlsp[1]."/sef.php")){
						include_once "modules/".$orgurlsp[1]."/sef.php";
						$class = "sef_".$orgurlsp[1];
						$sefclass= new $class;
						$modsefurl = $sefclass->makesefurl($orgurl);
					}
					$module=$orgurlsp[1];
				}
				if($orgurlsp[0]!="module" && $orgurlsp[0]!="menuid")
				{
					if($orgurlsp[1])
					$sefpart2 .= $orgurlsp[1]."/";
				}
				if($orgurlsp[0]=="menuid")
				{
					if($orgurlsp[1])
					$menuid .= $orgurlsp[1]."/";
				}
			}
			if($sitename)
				$newsef .= $sitename;
			if($modname)
				$newsef .= $modname;
			if($sefconf->showmenuid)
				$newsef .= $menuid;
			if(is_file("modules/".$module."/sef.php") && $modsefurl)
				$newsef .= $modsefurl;
			else
				$newsef .= $sefpart2;

			$newsef = substr_replace($newsef,"",-1,1);
			//if this is a new SEF url saving it in database.
			if($db->get_rec("#__sefurls", "sefurl", "orgurl='".$orgurl."' AND sefurl='".$newsef."'")){}
			else{
				$db->db_insert("#__sefurls", "sefurl, orgurl", "'".$newsef."', '".$orgurl."'");
			}

			if($sefconf->enablesef && $sefconf->seftype=="1")
			return hycus::getroot().$newsef.$sefconf->sefsuffix;
			elseif($sefconf->enablesef && $sefconf->seftype=="2")
			return hycus::getroot()."?".$newsef.$sefconf->sefsuffix;

 		}

		else
		return hycus::getroot().$orgurl;
 	}
 }
?>