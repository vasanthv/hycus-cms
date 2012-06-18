<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

 defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

 class hycusSearch_category{
 	function search($q){
 		$result = array();
 		$db = new hdatabase();
 		$recs = $db->get_recs("#__categories", "id, title, description", "title LIKE '%$q%' OR description LIKE '%$q%'");
 		$count=0;

 		if($recs){
			foreach($recs AS $rec){
				$result[$count][title]=trim(hycus::searchdisp($rec->title, 50, $q));
				$result[$count][link]=huri::makeuri("?module=content&task=catview&catid=".$rec->id."&menuid=".hycus::getcleanvar("menuid"));
				$result[$count][data]=trim(hycus::searchdisp($rec->description, 250, $q));
				$count++;
			}
 		}
 		return $result;
 	}
 }
?>
