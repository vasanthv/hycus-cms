<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

 defined( 'HYCUSADMINPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

$task = hycus::getcleanvar("task");
$blockid = hycus::getcleanvar("id");

switch($task){
	case "sortblocklist":
		sortblocklist();
		break;
	case "blockform":
		addeditblock($blockid);
		break;
	case "addblock":
		addeditblock();
		break;
	case "editblock":
		addeditblock($blockid);
		break;
	case "enableblock":
		enableblock($blockid);
		break;
	case "disableblock":
		disableblock($blockid);
		break;
	case "deleteblock":
		deleteblock($blockid);
		break;
	case "getblockparams":
		getblockparams();
		break;
	default:
		blocklist();
		break;
}

function blocklist(){

	$db = new hdatabase();

	$where = "";

	if(hycus::getcleanvar("posfilter")){
		$where .= " position = '".hycus::getcleanvar("posfilter")."' AND ";
	}
	if(hycus::getcleanvar("btypefilter")){
		$where .= " block_name = '".hycus::getcleanvar("btypefilter")."' AND ";
	}

	$where .= "(enabled = '0' OR enabled = '1') ORDER by ordering";
	$blockobj = $db->get_recs("#__blocks", "*", "$where");

	echo "<div style='padding:0 0 5px;line-height:25px;'>These are the list of all the blocks you have in your website. You can edit the block by clicking on it. For adding a new block click 'Add a Block' in the top right corner. You can sort the blocks by just 'Drag and Drop' by clicking at the left end of the list. Click on the ".hycus::iconimage('delete.png')." to delete a block (Note: There is no undo.). Also use ".hycus::iconimage('yes.png')." / ".hycus::iconimage('no.png')." to enable and disable the block.</div>";

	echo "<h4 style='text-transform:capitalize;'>Blocks</h4>";

	echo "<table width='100%'><tr>";

		echo "<td>";
		hycus::admin_form("blockfilterForm", "modulewrapper");
		echo "<form action='?adminmodule=blocks' method='post' id='blockfilterForm' class='adminhycusforms'>";
		$positions = $db->get_recs("#__blocks", "DISTINCT position", "");
		echo "<select name='posfilter' id='posfilter' style='margin:0 5px;'>";
			echo "<option value=''>---Select Position---</option>";
			foreach($positions AS $position)
			{
				echo "<option value='".$position->position."'";
				if(hycus::getcleanvar("posfilter")==$position->position){echo "SELECTED=SELECTED";}
				echo ">". $position->position ."</option>";
			}
		echo "</select>";
		$btypes = $db->get_recs("#__blocks", "DISTINCT block_name", "");
		echo "<select name='btypefilter' id='btypefilter' style='margin:0 5px;'>";
			echo "<option value=''>---Select Blocktype---</option>";
			foreach($btypes AS $btype)
			{
				echo "<option value='".$btype->block_name."'";
				if(hycus::getcleanvar("btypefilter")==$btype->block_name){echo "SELECTED=SELECTED";}
				echo ">". $btype->block_name ."</option>";
			}
		echo "</select>";
		echo "<input type='submit' value='Go'/>";
		echo "</form>";
		echo "</td>";

		echo "<td>";
			echo "<div style='text-align:right;margin: 0 10px;'> + ";
			hycus::adminlink("addblock", "?adminmodule=blocks&task=blockform", "modulewrapper", "Add a Block");
			echo "</div>";
		echo "</td>";

	echo "</tr></table>";


	?>
	<script type="text/javascript">
	  // When the document is ready set up our sortable with it's inherant function(s)
	  $(document).ready(function() {
	  $("#blockslist").sortable();
		});
	</script>

	<?php
	if($blockobj){
	echo "<ul id='blockslist' class='sortable'>";
	foreach($blockobj AS $block)
	{
		echo "<li id='blocklist_".$block->id."'>";
		echo "<img src='images/sort-arrow.png' alt='move' width='16' height='16' class='handle' />";
		hycus::adminlink("editblock_".$block->id, "?adminmodule=blocks&task=blockform&id=$block->id", "modulewrapper", "$block->title");
		echo "<small>Id: <b>".$block->id."</b></small>";
		echo "<div class='adminlistbuttons'>";
		echo "<span>";
		if($block->enabled){
			hycus::adminlink("disableblock_".$block->id, "?adminmodule=blocks&task=disableblock&id=$block->id", "modulewrapper", hycus::iconimage('yes.png'), "Disable?");
		}else{
			hycus::adminlink("enableblock_".$block->id, "?adminmodule=blocks&task=enableblock&id=$block->id", "modulewrapper", hycus::iconimage('no.png'), "Enable?");
		}
		echo "</span>";
		echo "<span>";
			hycus::adminlink("deleteblock_".$block->id, "?adminmodule=blocks&task=deleteblock&id=$block->id", "modulewrapper", hycus::iconimage('delete.png'), "Delete?", "Are you sure you want to delete this Block? There is no UNDO.");
		echo "</span>";
		echo "</div>";
		echo "</li>";
	}
	echo "</ul>";
	}
	?>
	<button id="saveorder">Save order</button>
	<script type="text/javascript">
	  $("#saveorder").click(function () {
		var order = $('#blockslist').sortable('serialize');
		$("#modulewrapper").load("?response=ajax&adminmodule=blocks&task=sortblocklist&"+order);
      });
	</script>

	<?php
}
function addeditblock($blockid = null){

	$task = hycus::getcleanvar("task");

	$db = new hdatabase();
	if($blockid)
		$blockobj = $db->get_rec("#__blocks", "*", "id='$blockid'");


	if($task == "addblock"){

		hycus::checkformhash() or die("Invalid Request");

		$blocktitle =  hycus::getcleanvar("blocktitle");
		unset($_POST['blocktitle']);
		$showblocktitle =  hycus::getcleanvar("showblocktitle");
		unset($_POST['showblocktitle']);
		$enabled =  hycus::getcleanvar("benabled");
		unset($_POST['benabled']);
		$blockposition =  hycus::getcleanvar("blockposition");
		unset($_POST['blockposition']);
		$blockitem =  hycus::getcleanvar("blockitem");
		unset($_POST['blockitem']);
		$blockpermission=  hycus::getcleanvar("blockpermission");
		unset($_POST['blockpermission']);
		$blockclass =  hycus::getcleanvar("blockclass");
		unset($_POST['blockclass']);
		$blockmenusc =  hycus::getcleanvar("blockmenu");
		if($blockmenusc=="all"){
			$blockmenus="all";
		}
		else{

			$blockmenusarr =  hycus::getcleanvar("blockmenus");
			$blockmenus="";$count=1;
			foreach($blockmenusarr AS $blockmenusar)
			{
				if($count=="1")
				$blockmenus .= $blockmenusar;
				else
				$blockmenus .= ";".$blockmenusar;

				$count++;
			}
		}
		unset($_POST['blockmenu']);
		unset($_POST['blockmenus']);
		unset($_POST['id']);
		unset($_POST['task']);


		if($_POST)
		{
			$paramsarray = $_POST;
			$paramsname = array_keys($_POST);
			$count=0;$paramsstring="";
			foreach($paramsarray AS $param)
			{
				$paramsstring .= $paramsname[$count]."=";
				$paramsstring .= $param.";";
				$count++;
			}
		}
		if($blocktitle && $blockposition && $blockitem)
		$db->db_insert("#__blocks", "title, position, block_name, enabled, showtitle, data, menuids, blockperms, blockclass", "'$blocktitle', '$blockposition', '$blockitem', '$enabled', '$showblocktitle', '$paramsstring', '$blockmenus', '$blockpermission', '$blockclass'");
		hycus::ajax_redirect("?adminmodule=blocks&task=blocklist", "modulewrapper");
	}
	elseif($task == "editblock"){

		hycus::checkformhash() or die("Invalid Request");

		$blocktitle =  hycus::getcleanvar("blocktitle");
		unset($_POST['blocktitle']);
		$showblocktitle =  hycus::getcleanvar("showblocktitle");
		unset($_POST['showblocktitle']);
		$enabled =  hycus::getcleanvar("benabled");
		unset($_POST['benabled']);
		$blockposition =  hycus::getcleanvar("blockposition");
		unset($_POST['blockposition']);
		$blockitem =  hycus::getcleanvar("blockitem");
		unset($_POST['blockitem']);
		$blockpermission =  hycus::getcleanvar("blockpermission");
		unset($_POST['blockpermission']);
		$blockclass =  hycus::getcleanvar("blockclass");
		unset($_POST['blockclass']);
		$blockmenusc =  hycus::getcleanvar("blockmenu");
		if($blockmenusc=="all"){
			$blockmenus="all";
		}
		else{

			$blockmenusarr =  hycus::getcleanvar("blockmenus");
			$blockmenus="";$count=1;
			foreach($blockmenusarr AS $blockmenusar)
			{
				if($count=="1")
				$blockmenus .= $blockmenusar;
				else
				$blockmenus .= ";".$blockmenusar;

				$count++;
			}
		}
		unset($_POST['blockmenu']);
		unset($_POST['blockmenus']);
		unset($_POST['id']);
		unset($_POST['task']);



		if($_POST)
		{
			$paramsarray = $_POST;
			$paramsname = array_keys($_POST);
			$count=0;$paramsstring="";
			foreach($paramsarray AS $param)
			{
				$paramsstring .= $paramsname[$count]."=";
				$paramsstring .= $param.";";
				$count++;
			}
		}

		if($blocktitle && $blockposition && $blockitem)
		{
			if($paramsstring)
				$db->db_update("#__blocks", "title='$blocktitle', position='$blockposition', block_name='$blockitem', showtitle='$showblocktitle', enabled='$enabled', data='$paramsstring', menuids='$blockmenus', blockperms='$blockpermission', blockclass='$blockclass'", "id = '$blockid'");
			else
				$db->db_update("#__blocks", "title='$blocktitle', position='$blockposition', block_name='$blockitem', showtitle='$showblocktitle', enabled='$enabled', menuids='$blockmenus', blockperms='$blockpermission', blockclass='$blockclass'", "id = '$blockid'");
		}
		hycus::ajax_redirect("?adminmodule=blocks&task=blocklist", "modulewrapper");
	}
	else{

		echo "<h4 style='text-transform:capitalize;'>";
		if($blockid){
			echo " Edit this Block - ".$blockobj->title."</h4>";
		}
		else {
			echo "Add Block</h4>";
		}

		hycus::admin_form("menuitemForm", "modulewrapper"); ?>
		<br/>
		<form id="menuitemForm" action="?adminmodule=blocks" method="post" class='adminhycusforms'>
			<label>Block Title</label>
			<div><input type="text" name="blocktitle" value="<?php echo $blockobj->title; ?>"  class="required textbox"/></div>

			<div style="margin-bottom:10px;">
				<label>Show Block Title: </label>
				<input type="radio" name="showblocktitle" value="0" <?php if($blockobj->showtitle=="0"){ ?>CHECKED=CHECKED<?php } ?>  />No
				<input type="radio" name="showblocktitle" value="1" <?php if($blockobj->showtitle=="1"){ ?>CHECKED=CHECKED<?php } ?>  />Yes
			</div>

			<div style="margin-bottom:10px;">
				<label>Enable this block: </label>
				<input type="radio" name="benabled" value="0" <?php if($blockobj->enabled=="0"){ ?>CHECKED=CHECKED<?php } ?>  />No
				<input type="radio" name="benabled" value="1" <?php if($blockobj->enabled=="1"){ ?>CHECKED=CHECKED<?php } ?>  />Yes
			</div>

			<label>Position</label>
			<div>
			<?php global $template ?>
			<select name="blockposition" id="blockposition" class="required">
			<option value="">Select Position</option>
			<?php
				$file = "templates/$template/positions.xml";
		 		$xml = simplexml_load_file($file);
		 		$count=1;
 				$html = "<table>";
				foreach($xml->children() as $child)
				{
					echo "<option value='$child'";
					if($child==$blockobj->position){echo "SELECTED=SELECTED";}
					echo ">".$child."</option>";
				}
			?>
			</select>
			</div>

			<label>Select Block</label>
			<div>
			<span style='margin-right:5px;'>
			<?php
			/*Get the module list*/
			$dir = "blocks/";
			echo "<select name='blockitem' id='blockitem'class='required' >";
			echo "<option value=''>Select Block</option>";

			if (is_dir($dir)) {
				if ($dh = opendir($dir)) {
					while (($file = readdir($dh)) !== false) {
						if(filetype($dir . $file)=="dir" && $file != "." && $file != "..")
						{
							if(is_file("blocks/$file/$file.php"))
							echo "<option value='$file'";
							if($blockobj->block_name == $file)
							echo "SELECTED=SELECTED";
							echo ">".$file."</option>";
						}
					}
					closedir($dh);
				}
			}
			echo "</select></span><a href='#' id='getparamslink'>Get Block Configuration</a>";
			?>
			</div>

			<div>
				<input type="radio" name="blockpermission" value="0" <?php if($blockobj->blockperms == '0'){ ?>checked<?php } ?> /><label>All users</label>
				<input type="radio" name="blockpermission" value="1" <?php if($blockobj->blockperms == '1'){ ?>checked<?php } ?> /><label>Only Registered</label>
				<input type="radio" name="blockpermission" value="2" <?php if($blockobj->blockperms == '2'){ ?>checked<?php } ?> /><label>Only non-registered</label>
			</div><br/>

			<div>
			<label>Select menus on which this block is to be displayed.</label>
			<div>
				<?php if($blockobj->menuids=="all"){ echo "<script>document.getElementById('blockmenusid').disabled=true;document.getElementById('blockmenusid').multiple=false;</script>"; } ?>
				<input type="radio" name="blockmenu" value="all" <?php if($blockobj->menuids=="all"){ ?>CHECKED=CHECKED<?php } ?> onclick="document.getElementById('blockmenusid').disabled=true;document.getElementById('blockmenusid').multiple=false;"/>All
				<?php if($blockobj->menuids!="all"){ echo "<script>document.getElementById('blockmenusid').disabled=false;document.getElementById('blockmenusid').multiple=true;</script>"; } ?>
				<input type="radio" name="blockmenu" value="0" <?php if($blockobj->menuids!="all"){ ?>CHECKED=CHECKED <?php } ?> onclick="document.getElementById('blockmenusid').disabled=false;document.getElementById('blockmenusid').multiple=true;" />Selected
			</div>
			<div>
				<?php $menus = $db->get_recs("#__menus", "*"); ?>
				<select name="blockmenus[]" id="blockmenusid" size="7" MULTIPLE <?php if($blockobj->menuids=="all"){ echo "Disabled"; } ?>>
				<?php
					foreach($menus AS $menu)
					{
						$menuitems = $db->get_recs("#__menuitems", "*", "menuid='$menu->id'", "ordering");
						echo "<option disabled>".$menu->menuname."</option>";
						if($menuitems)
						{
							foreach($menuitems AS $menuitem){
								echo "<option value='".$menuitem->id."'";
								foreach(explode(";", $blockobj->menuids) AS $thismenuid){
									if($menuitem->id==$thismenuid){echo "SELECTED=SELECTED"; }
								}
								echo ">".$menuitem->itemtitle."</option>";
							}
						}
					}
				?>
				</select>
			</div>
			</div>

			<label>Block Class</label>
			<div><input type="text" name="blockclass" value="<?php echo $blockobj->blockclass; ?>" class="textbox"/>&nbsp;<small><i>Adds a css class to this block.</i></small></div>

			<div id='blockparams'></div>
			<script type="text/javascript">
				$(function() {
					$("#blockitem").change(function () {
						var blockvalue = $('select#blockitem option:selected').val();
						if(blockvalue){
							$.ajax({
					 		url: "?response=ajax&adminmodule=blocks&task=getblockparams&blockid=<?php echo $blockid; ?>&item="+blockvalue,
					  		success: function(html){
					  			$("div#ajax-loader").show();
								$("div#blockparams").empty();
								$("div#blockparams").prepend(html);
							  	$("div#blockparams").fadeIn("slow");
							  	$("div#blockparams").css("border", "2px solid #234A8B");
							  	$("div#blockparams").css("padding", "5px");
								window.scrollTo(0,parseInt($("body").css("height")));
					  			$("div#ajax-loader").hide();
					  			}
					 		});
						}
					});
					$("#getparamslink").click(function () {
						var blockvalue = $('select#blockitem option:selected').val();
						if(blockvalue){
							$.ajax({
					 		url: "?response=ajax&adminmodule=blocks&task=getblockparams&blockid=<?php echo $blockid; ?>&item="+blockvalue,
					  		success: function(html){
					  			$("div#ajax-loader").show();
								$("div#blockparams").empty();
								$("div#blockparams").prepend(html);
							  	$("div#blockparams").fadeIn("slow");
							  	$("div#blockparams").css("border", "2px solid #234A8B");
							  	$("div#blockparams").css("padding", "5px");
								window.scrollTo(0,parseInt($("body").css("height")));
					  			$("div#ajax-loader").hide();
					  			}
					 		});
						}
					});
				});
			</script>

			<?php if($blockid){ ?><input type="hidden" name="id" value="<?php echo $blockid ?>" /><input type="hidden" name="task" value="editblock" /><?php }else{ ?><input type="hidden" name="task" value="addblock" /><?php } ?>
			<?php hycus::addformhash(); ?>
			<input type="submit" value="Save" class="button"/>
		</form><?php
		hycus::adminlink("goback_block", "?adminmodule=blocks", "modulewrapper", "<< Cancel & Go back");

	}
}
function deleteblock($blockid){

	hycus::checkformhash() or die("Invalid Request");

	$db = new hdatabase();
	$db->db_delete("#__blocks", "id = '$blockid'");

	hycus::ajax_redirect("?adminmodule=blocks&task=blocklist", "modulewrapper");
}
function enableblock($blockid){

	hycus::checkformhash() or die("Invalid Request");

	$db = new hdatabase();
	$db->db_update("#__blocks", "enabled = '1'", "id = '$blockid'");

	hycus::ajax_redirect("?adminmodule=blocks&task=blocklist", "modulewrapper");
}
function disableblock($blockid){

	hycus::checkformhash() or die("Invalid Request");

	$db = new hdatabase();
	$db->db_update("#__blocks", "enabled = '0'", "id = '$blockid'");

	hycus::ajax_redirect("?adminmodule=blocks&task=blocklist", "modulewrapper");
}
function sortblocklist()
{
	$db = new hdatabase();

	foreach (hycus::getcleanvar('blocklist') as $position => $item) :
		$db->db_update("#__blocks", "ordering = '$position'", "id = '$item'");
	endforeach;
	hycus::ajax_redirect("?adminmodule=blocks&task=blocklist&response=ajax", "modulewrapper");

}
function getblockparams()
{

	$blockid = hycus::getcleanvar("blockid");
	$db = new hdatabase();
	$data = $db->get_rec("#__blocks", "data", "id='$blockid'");
	$data = $data->data;

	$paramsfile="blocks/".hycus::getcleanvar("item")."/params.xml";
	echo "<h4 style='margin:5px 0px;'>Block Parameters</h4>";
	if(is_file($paramsfile))
	{
		echo hycusParams::getParams($paramsfile, $data);
	}
}
?>
