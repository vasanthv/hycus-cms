<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

 defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

 class hycusBlock_breadcrumb {
	function loadthisblock($id,$data) {

		$currentmenuid = hycus::getcleanvar("menuid");
		$db = new hdatabase();
		$defaultmenuid = $db->get_rec("#__menuitems", "id", "defaultmenu='1'");
		$defaultmenuid = $defaultmenuid->id;
		$this->breadcrumbitems = array("$currentmenuid");

		if($defaultmenuid->id == $currentmenuid){}
		elseif($currentmenuid){
			$menuobj = $db->get_rec("#__menuitems", "itemtitle, itemlink, parentid", "id='$currentmenuid'");
			if(!$menuobj->parentid)
				$this->addbreadcrumbitem($currentmenuid);
			else
				$this->getparentitem($currentmenuid);
		}
		array_push($this->breadcrumbitems, "$defaultmenuid");

		$this->printbreadcrumb($this->breadcrumbitems, $currentmenuid, $defaultmenuid);
	}
	function addbreadcrumbitem($printmenuid){
		//adds an item to the breadcrum array
		$db = new hdatabase();
		$menuobj = $db->get_rec("#__menuitems", "itemtitle", "id='$printmenuid'");
		if($menuobj->itemtitle){
			array_push($this->breadcrumbitems, "$printmenuid");
		}
	}
	function getparentitem($getmenuid){
		$db = new hdatabase();
		$menuobj = $db->get_rec("#__menuitems", "itemtitle, itemlink, parentid", "id='$getmenuid'");
		if(!$menuobj->parentid){
			$this->addbreadcrumbitem($menuobj->parentid);
		}
		else{
			$this->addbreadcrumbitem($menuobj->parentid);
			$this->getparentitem($menuobj->parentid);
		}
	}
	function printbreadcrumb($breadcrumarray, $currentmenuid, $defaultmenuid){

		// prints the breamcrumb from the array.
		$breadcrumarray = array_reverse(array_unique($breadcrumarray));

		$db = new hdatabase();

		foreach($breadcrumarray AS $breadcrum)
		{
			$menuobj = $db->get_rec("#__menuitems", "itemtitle, itemlink, parentid", "id='$breadcrum'");
			if($defaultmenuid!=$breadcrum)
			echo " > ";

			if($defaultmenuid==$breadcrum)
			$thisbreadcrumburl = hycus::getroot();
			else
			$thisbreadcrumburl = huri::makeuri($menuobj->itemlink."&menuid=".$breadcrum);

			if($currentmenuid != $breadcrum || hycus::getcurrenturl()!=$thisbreadcrumburl){
				echo "<a href='";
				if($defaultmenuid==$breadcrum){
					echo hycus::getroot();
				}
				else{
					echo huri::makeuri($menuobj->itemlink."&menuid=".$breadcrum);
				}
				echo "'>";
			}
			echo $menuobj->itemtitle;

			if($currentmenuid != $breadcrum || hycus::getcurrenturl()!=$thisbreadcrumburl)
				echo "</a>";
		}

		//prints the page title if the menuid in the url and the menuitem link are different.
		if(hycus::getcurrenturl()!=$thisbreadcrumburl)
		echo " > ". getpagetitle();
	}

 }

?>