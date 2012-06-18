<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

function setTitle(){
	if((hycus::getcleanvar("task")=="page" || hycus::getcleanvar("task")=="blog") && hycus::getcleanvar("id") )
	{
		$db=new hdatabase();
		$rec = $db->get_rec("#__contents", "title", "id=".hycus::getcleanvar("id"));
		return $rec->title;
	}
}

function setMetakeywords(){
	if((hycus::getcleanvar("task")=="page" || hycus::getcleanvar("task")=="blog") && hycus::getcleanvar("id") )
	{
		$db=new hdatabase();
		$rec = $db->get_rec("#__contents", "metakeywords", "id=".hycus::getcleanvar("id"));
		return $rec->metakeywords;
	}
}

function setMetadesc(){
	if((hycus::getcleanvar("task")=="page" || hycus::getcleanvar("task")=="blog") && hycus::getcleanvar("id") )
	{
		$db=new hdatabase();
		$rec = $db->get_rec("#__contents", "metadescription", "id=".hycus::getcleanvar("id"));
		return $rec->metadescription;
	}
}

?>
