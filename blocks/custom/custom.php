<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

 defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );
class hycusBlock_custom {
	function loadthisblock($id,$data) {

		//get the content item and prints it.

		$contentid = hycus::gethycusdata($data,"contentid");
		$db = new hdatabase();
		$content = $db->get_rec("#__contents", "data", "id='$contentid'");
		echo "<div>";
		echo $content->data;
		echo "</div>";

	}
}
?>
