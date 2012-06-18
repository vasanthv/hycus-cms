<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

defined( 'HYCUSADMINPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );
?>
<style>
.statblocks
{
	border:thin solid #828282;
	margin:5px;
	padding:5px;
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
}
.statblockstitle{
	font-style:italic;
	padding:2px;
	color:#828282;
	font-size:16px;
	font-weight:bold;
	border-bottom:thin solid #828282;
}
.statblocksdiv
{
	padding:5px;
}
.statblocksdiv h5{
	font-size:14px;
	font-weight:bold;
	padding:0px;
	margin:0px;
}
.statblock{
	padding:5px 0px;
	border-bottom:thin dotted #828282;
}
</style>
<?php $db = new hdatabase();  ?>
<h3>Statistics</h3>
<table width="100%">
<tr>
<td width="50%" valign='top'>
<div id="actionsneeded" class="statblocks">
	<div class='statblockstitle'>Action needed</div>
	<div class='statblocksdiv'>
		<?php
		//get comment actions needed
		//gets the number of unapproved comments
		$unappovedcomments = $db->get_recs("#__comments", "id", "approved='0'");
		$noofunappovedcomments = count($unappovedcomments);
		if($noofunappovedcomments){
			echo "<style>#pendingcommentlink a{color:red;font-weight:bold;}</style><div id='pendingcommentlink'>";
			hycus::adminlink("pendingcommentapproval", "?adminmodule=comment", "modulewrapper", "Pending approval for $noofunappovedcomments comments");
			echo "</div>";
		}


		//no action required now
		if(!$noofunappovedcomments)
		{
			echo "<div style='color:green;font-weight:bold;'>No urgent action required now.</div>";
		}

		?>

	<div>
</div>
</td>
<td width="50%" valign='top'>
<div id="actionsneeded" class="statblocks">
	<div class='statblockstitle'>Statistics</div>
	<div class='statblocksdiv'>
		<?php
		//statistics of block
		echo "<div class='statblock'>";
			echo "<h5>";
				hycus::adminlink("statisticsblock", "?adminmodule=blocks", "modulewrapper", "Blocks");
			echo "</h5>";
			$disabledblocksarr = $db->get_recs("#__blocks", "id", "enabled='0'");
			$disabledblocks = count($disabledblocksarr);
			$enabledblocksarr = $db->get_recs("#__blocks", "id", "enabled='1'");
			$enabledblocks = count($enabledblocksarr);
			echo "<span>Total: <b>".($disabledblocks + $enabledblocks)."</b>; </span>";
			echo "<span style='color:green'>Enabled: <b>".$enabledblocks."</b>; </span>";
			echo "<span style='color:red'>Disabled: <b>".$disabledblocks."</b>; </span>";
		echo "</div>";

		//statistics of content
		echo "<div class='statblock'>";
			echo "<h5>";
				hycus::adminlink("statisticscontent", "?adminmodule=content", "modulewrapper", "Contents");
			echo "</h5>";
			$disabledcontentsarr = $db->get_recs("#__contents", "id", "enabled='0'");
			$disabledcontents = count($disabledcontentsarr);
			$enabledcontentsarr = $db->get_recs("#__contents", "id", "enabled='1'");
			$enabledcontents = count($enabledcontentsarr);
			echo "<span>Total: <b>".($disabledcontents + $enabledcontents)."</b>; </span>";
			echo "<span style='color:green'>Enabled: <b>".$enabledcontents."</b>; </span>";
			echo "<span style='color:red'>Disabled: <b>".$disabledcontents."</b>; </span>";
		echo "</div>";

		//statistics of menus
		echo "<div class='statblock'>";
			echo "<h5>";
				hycus::adminlink("statisticsmenus", "?adminmodule=menus", "modulewrapper", "Menus");
			echo "</h5>";
			$menusarr = $db->get_recs("#__menus", "id", "");
			$menus = count($menusarr);
			$disabledmenuitemsarr = $db->get_recs("#__menuitems", "id", "enabled='0'");
			$disabledmenuitems = count($disabledmenuitemsarr);
			$enabledmenuitemsarr = $db->get_recs("#__menuitems", "id", "enabled='1'");
			$enabledmenuitems = count($enabledmenuitemsarr);

			echo "<span>No. of Menus: <b>".$menus."</b>; </span>";
			echo "<span>Total Menuitems: <b>".($disabledmenuitems + $enabledmenuitems)."</b>; </span>";
			echo "<span style='color:green'>Enabled: <b>".$enabledmenuitems."</b>; </span>";
			echo "<span style='color:red'>Disabled: <b>".$disabledmenuitems."</b>; </span>";
		echo "</div>";

		//statistics of users
		echo "<div class='statblock'>";
			echo "<h5>";
				hycus::adminlink("statisticsmenus", "?adminmodule=menus", "modulewrapper", "Users");
			echo "</h5>";
			$usersarr = $db->get_recs("#__users", "id", "");
			$users  = count($usersarr);
			$loggedusersarr = $db->get_recs("#__session", "time", "guest='0'");
			$loggedusers = count($loggedusersarr);
			$guestusersarr = $db->get_recs("#__session", "time", "guest='1'");
			$guestusers = count($guestusersarr);

			echo "<span>Total no. of users: <b>".$users."</b>; </span>";
			echo "<span style='color:green'>Currently logged in users: <b>".$loggedusers."</b>; </span>";
			echo "<span>Guest visitors: <b>".$guestusers."</b>; </span>";
		echo "</div>";
		?>
	<div>
</div>
</td>


</tr>
</table>