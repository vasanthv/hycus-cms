<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

 defined( 'HYCUSADMINPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );


$task = hycus::getcleanvar("task");
$menuid = hycus::getcleanvar("id");
$menuitemid = hycus::getcleanvar("menuitemid");

switch($task){
	case "deletemenu":
		deletemenu($menuid);
		break;
	case "menuform":
		addeditmenu($menuid);
		break;
	case "addmenu":
		addeditmenu();
		break;
	case "editmenu":
		addeditmenu($menuid);
		break;
	case "menuitemlist":
		menuitemlist($menuid);
		break;
	case "sortmenulist":
		sortmenulist($menuid);
		break;
	case "menuitemform":
		addeditmenuitem($menuid, $menuitemid);
		break;
	case "addmenuitem":
		addeditmenuitem($menuid);
		break;
	case "editmenuitem":
		addeditmenuitem($menuid, $menuitemid);
		break;
	case "deletemenuitem":
		deletemenuitem($menuid, $menuitemid);
		break;
	case "enablemenuitem":
		enablemenuitem($menuid, $menuitemid);
		break;
	case "disablemenuitem":
		disablemenuitem($menuid, $menuitemid);
		break;
	case "defaultmenuitem":
		defaultmenuitem($menuid, $menuitemid);
		break;
	case "gettaskvalues":
		gettaskvalues();
		break;
	default:
		menulist();
		break;
}

function menulist()
{
	echo "<div style='padding:0 0 5px;line-height:25px;'>This menu manager holds all the menus of this website. You can manage, edit, delete, sort, publish/unpublish these menuitems from here. You can edit a menu by clicking on the edit icon. View the menuitems under each menu by clicking on its name. For adding a new menu click 'Add a menu' in the top right corner. Click on the ".hycus::iconimage('delete.png')." to delete a menu (Note: There is no undo.). </div>";

	$db = new hdatabase();
	$menusarray = $db->get_recs("#__menus", "*");
	echo "<h4 style='text-transform:capitalize;'>Menulist</h4>";
	echo "<div style='text-align:right;margin:5px 0;'>+";
	hycus::adminlink("addmenu", "?adminmodule=menus&task=menuform", "modulewrapper", "Add New Menu");
	echo "</div>";

	echo "<table cellpadding='4' width='100%' class='admintable' border='1'>" .
			"<thead><tr>" .
			"<td align='center' width='5%'>S.No</td>" .
			"<td align='center'>Menuname</td>" .
			"<td align='center' width='5%'>Delete</td>" .
			"<td align='center' width='5%'>Edit</td>" .
			"</tr></thead>";
	$count = 1;
	if($menusarray){
		echo "<tbody>";
		foreach($menusarray AS $menu)
		{
			if($count%2==0){ echo "<tr class='even'>"; }else{ echo "<tr class='odd'>"; }

			echo "<td align='center'>".$count."</td>";
			echo "<td>";
			hycus::adminlink("listmenuid".$menu->id, "?adminmodule=menus&task=menuitemlist&id=".$menu->id, "modulewrapper", "$menu->menuname");
			echo "</td><td align='center'>";
			hycus::adminlink("delmenuid".$menu->id, "?adminmodule=menus&task=deletemenu&id=".$menu->id, "modulewrapper", hycus::iconimage('delete.png'),"", "Are you sure you want to delete this Menu? There is no UNDO.");
			echo "</td><td align='center'>";
			hycus::adminlink("editmenuid".$menu->id, "?adminmodule=menus&task=menuform&id=".$menu->id, "modulewrapper", hycus::iconimage('edit.png'));
			echo "</td></tr>";
			$count++;
		}
		echo "</tbody>";
	}
	echo "</table>";
}
function deletemenu($id){
	hycus::checkformhash() or die("Invalid Request");
	$db = new hdatabase();
	$checkdefaultmenu = $db->get_rec("#__menuitems", "id", "menuid='$id' AND defaultmenu='1'");
	if($checkdefaultmenu){}
	else{ $db->db_delete("#__menus", "id='$id'"); }
	hycus::ajax_redirect("?adminmodule=menus", "modulewrapper");

}
function addeditmenu($menuid = null){

	$task = hycus::getcleanvar("task");
	$menuname = hycus::getcleanvar("menuname");

	$db = new hdatabase();
	if($menuid){ $menunameobj = $db->get_rec("#__menus", "menuname", "id='$menuid'"); $current_menu = $menunameobj->menuname; }

	if($task == "addmenu"){
		hycus::checkformhash() or die("Invalid Request");

		if($menuname)
			$db->db_insert("#__menus", "menuname", "'$menuname'");
		hycus::ajax_redirect("?adminmodule=menus", "modulewrapper");
	}
	elseif($task == "editmenu"){
		hycus::checkformhash() or die("Invalid Request");

		$db->db_update("#__menus", "menuname='$menuname'", "id='$menuid'");
		hycus::ajax_redirect("?adminmodule=menus", "modulewrapper");
	}
	else{
	if($current_menu){ echo "<h4>Edit - $current_menu</h4>";}
	else{ echo "<h4>Add new Menu</h4><br/> This will add a new menu to you website. You can add menu items to this menu later.";}

	hycus::admin_form("axForm", "modulewrapper"); ?>

	<form id="axForm" action="?adminmodule=menus" method="post">
		<input type="text" name="menuname" value="<?php echo $current_menu ?>" class="required"/>
		<?php if($menuid){ ?><input type="hidden" name="id" value="<?php echo $menuid ?>" /><input type="hidden" name="task" value="editmenu" /><?php }else{ ?><input type="hidden" name="task" value="addmenu" /><?php } ?>
	    <?php hycus::addformhash(); ?>
	    <input type="submit" value="Submit" class="button"/>
	</form><?php
	hycus::adminlink("addmenuitem", "?adminmodule=menus", "modulewrapper", "<< Cancel & Go back");
	}
}
function menuitemlist($menuid){


	$db = new hdatabase();
	$menuitemsobj = $db->get_recs("#__menuitems","*", "menuid='$menuid' AND parentid='0' ORDER by ordering");
	$menunameobj = $db->get_rec("#__menus", "menuname", "id='$menuid'");

	echo "<h4 style='text-transform:capitalize;'>$menunameobj->menuname - menuitem list</h4>";

	echo "<div style='text-align:right;margin: 0 10px;'> + ";
	hycus::adminlink("addmenuitem", "?adminmodule=menus&task=menuitemform&id=$menuid", "modulewrapper", "Add a Menu Item");
	echo "</div>";

	?>
	<script type="text/javascript">
	  // When the document is ready set up our sortable with it's inherant function(s)
	  $(document).ready(function() {
	  $("#menuitemslist").sortable();
	});
	</script>

	<?php
	echo "<ul id='menuitemslist' class='sortable'>";
	if($menuitemsobj){
		foreach($menuitemsobj AS $menuitem)
		{
			echo "<li id='menuitemlist_".$menuitem->id."'>";
			echo "<img src='images/sort-arrow.png' alt='move' width='16' height='16' class='handle' />";
			hycus::adminlink("editmenuitem_".$menuitem->id, "?adminmodule=menus&task=menuitemform&id=$menuid&menuitemid=".$menuitem->id, "modulewrapper", "$menuitem->itemtitle");

			echo "<div class='adminlistbuttons'>";
			echo "<span>";
			if($menuitem->enabled){
				hycus::adminlink("disablemenuitem_".$menuitem->id, "?adminmodule=menus&task=disablemenuitem&id=$menuid&menuitemid=".$menuitem->id, "modulewrapper", hycus::iconimage('yes.png'), "Disable?");
			}else{
				hycus::adminlink("enablemenuitem_".$menuitem->id, "?adminmodule=menus&task=enablemenuitem&id=$menuid&menuitemid=".$menuitem->id, "modulewrapper", hycus::iconimage('no.png'), "Enable?");
			}
			echo "</span>";
			echo "<span>";
			if($menuitem->defaultmenu){
				hycus::adminlink("defaultmenuitem_".$menuitem->id, "?adminmodule=menus&task=defaultmenuitem&id=$menuid&menuitemid=".$menuitem->id, "modulewrapper", hycus::iconimage('home.png'), "This is your Homepage Link");
			}else{
				hycus::adminlink("defaultmenuitem_".$menuitem->id, "?adminmodule=menus&task=defaultmenuitem&id=$menuid&menuitemid=".$menuitem->id, "modulewrapper", hycus::iconimage('not_home.png'), "Make as Homepage Link");
			}
			echo "</span>";
			echo "<span>";
				hycus::adminlink("deletemenuitem_".$menuitem->id, "?adminmodule=menus&task=deletemenuitem&id=$menuid&menuitemid=".$menuitem->id, "modulewrapper", hycus::iconimage('delete.png'), "Delete?", "Are you sure you want to delete this Menuitem? There is no UNDO.");
			echo "</span>";
			echo "</div>";

			$checkparentobj = $db->get_recs("#__menuitems", "id", "parentid='".$menuitem->id."'");
			if(count($checkparentobj)){ submenulist($menuid, $menuitem->id, 1); }
			echo "</li>";
		}
	}
	else {
		echo "<li>No menu item to be listed..</li>";
	}
	echo "</ul>";
	?>
	<div><i>Note: After saving the order, the submenus will be automatically assigned next to the parent menu.</i></div><br/>
	<button id="saveorder">Save order</button>
	<script type="text/javascript">
	  $("#saveorder").click(function () {
		var order = $('#menuitemslist').sortable('serialize');
		$("#modulewrapper").load("?&response=ajax&adminmodule=menus&task=sortmenulist&menuid=<?php echo $menuid; ?>&"+order);
      });
	</script>
	<br/><br/>
	<?php
	/*Back Button Link*/
	hycus::adminlink("goback_menus", "?adminmodule=menus", "modulewrapper", hycus::iconimage("arrow_left.png"));
}
function addeditmenuitem($menuid, $menuitemid = null){

	$task = hycus::getcleanvar("task");

	$db = new hdatabase();
	$menunameobj = $db->get_rec("#__menus", "menuname", "id='$menuid'");
	if($menuitemid){ $currentmenuitemobj = $db->get_rec("#__menuitems", "*", "id='$menuitemid'"); }


	if($task == "addmenuitem"){
		hycus::checkformhash() or die("Invalid Request");

		$menuitemtitle =  hycus::getcleanvar("menuitemtitle");
		$enabled =  hycus::getcleanvar("menabled");
		$menuitemlink =  hycus::getcleanvar("menuitemlink");
		$parentmenuselect =  hycus::getcleanvar("parentmenuselect");
		$menupermission =  hycus::getcleanvar("menupermission");
		$menupagetitle =  hycus::getcleanvar("menupagetitle");
		$pageshowtitle =  hycus::getcleanvar("pageshowtitle");
		$menuitemtarget =  hycus::getcleanvar("menuitemtarget");

		$db->db_insert("#__menuitems", "menuid, parentid, itemtitle, itemlink, pagetitle, showtitle, menuperms, target, enabled", "'$menuid', '$parentmenuselect', '$menuitemtitle', '$menuitemlink', '$menupagetitle', '$pageshowtitle', '$menupermission', '$menuitemtarget', '$enabled'");
		hycus::ajax_redirect("?adminmodule=menus&task=menuitemlist&id=".$menuid, "modulewrapper");
	}
	elseif($task == "editmenuitem"){
		hycus::checkformhash() or die("Invalid Request");

		$menuitemtitle =  hycus::getcleanvar("menuitemtitle");
		$enabled =  hycus::getcleanvar("menabled");
		$menuitemlink =  hycus::getcleanvar("menuitemlink");
		$parentmenuselect =  hycus::getcleanvar("parentmenuselect");
		$menupermission =  hycus::getcleanvar("menupermission");
		$menupagetitle =  hycus::getcleanvar("menupagetitle");
		$pageshowtitle =  hycus::getcleanvar("pageshowtitle");
		$menuitemtarget =  hycus::getcleanvar("menuitemtarget");

		$db->db_update("#__menuitems", "itemtitle='$menuitemtitle', itemlink='$menuitemlink', pagetitle='$menupagetitle', showtitle='$pageshowtitle', parentid='$parentmenuselect', menuperms='$menupermission', target='$menuitemtarget', enabled='$enabled'", "id = '$menuitemid'");
		hycus::ajax_redirect("?adminmodule=menus&task=menuitemlist&id=".$menuid, "modulewrapper");
	}
	else{

	echo "<h4 style='text-transform:capitalize;'>$menunameobj->menuname";
	if($menuitemid){
		echo " - ".$currentmenuitemobj->itemtitle."</h4>";
	}
	else {
		echo " - Add Menu Item</h4>";
	}

	hycus::admin_form("menuitemForm", "modulewrapper"); ?>

	<form id="menuitemForm" name="menuitemForm" action="?adminmodule=menus" method="post" class='adminhycusforms'>
		<label>Menu Title</label>
		<div><input type="text" name="menuitemtitle" value="<?php echo $currentmenuitemobj->itemtitle; ?>" class="required"/></div>

		<div style="margin-bottom:10px;">
			<label>Enable this menuitem: </label>
			<input type="radio" name="menabled" value="0" <?php if($currentmenuitemobj->enabled=="0"){ ?>CHECKED=CHECKED<?php } ?>  />No
			<input type="radio" name="menabled" value="1" <?php if($currentmenuitemobj->enabled=="1"){ ?>CHECKED=CHECKED<?php } ?>  />Yes
		</div>

		<label>Link</label>
		<div><input type="text" name="menuitemlink" id="menuitemlink" value="<?php echo $currentmenuitemobj->itemlink; ?>" size='60' class="required" />
		<img src="<?php hycus::getroot(); ?>images/icon_refresh.png" onClick="document.menuitemForm.menuitemlink.value=''; $('select#menumodulelink').attr('disabled', false); $('span#pager').empty(); $('span#subpagervalue').empty(); " style="position:relative;top:5px;cursor:pointer;">
		</div>

		<div style="clear:both;height:50px;">
		<span class='linkdefiner'>
		<?php
		/*Get the module list*/
		$dir = "modules/";
		echo "<label>Link Creator: </label> <select name='menumodulelink' id='menumodulelink' >";
		echo "<option value=''>Select Module</option>";

		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					if(filetype($dir . $file)=="dir" && $file != "." && $file != "..")
					{
						if(is_file("modules/$file/$file.php"))
						echo "<option value='$file'>".$file."</option>";
					}
				}
				closedir($dh);
			}
		}
		echo "</select></span>";
		echo "<span id='pager' class='linkdefiner'></span>";
		echo "<span id='subpagervalue' class='linkdefiner'></span>";
		?>
		<script type="text/javascript">
			$(function() {
				$("#menumodulelink").change(function () {
					$("select#menumodulelink").attr("disabled", true);
					var menumodulevalue = $('select#menumodulelink option:selected').val();
					if(menumodulevalue){
						$('#menuitemlink').val("?module="+menumodulevalue);
					}
						$.ajax({
				 			url: "?response=ajax&adminmodule=menus&task=gettaskvalues&item="+menumodulevalue,
				  			success: function(html){
				  				$("div#ajax-loader").show();
						 		$("span#pager").empty();
						 		$("span#pager").prepend(html);
						  		$("span#pager").fadeIn("slow");
				  				$("div#ajax-loader").hide();
				  			}
				 		});
				});
			});
		</script>

		</div>

		<label>Parent Menu</label>
		<div>
		<?php
			$pmenus = $db->get_recs("#__menuitems", "*", "menuid='$menuid' AND parentid='0'", "ordering");
			echo "<select name='parentmenuselect' id='parentmenuselect'>";
			echo "<option value=''>Select Parent Menu</option>";
			foreach($pmenus AS $pmenu)
			{
				if($menuitemid == $pmenu->id){}else{
					echo "<option value='".$pmenu->id."'";
					if($currentmenuitemobj->parentid == $pmenu->id){ echo "selected=SELECTED"; }
					echo ">". $pmenu->itemtitle ."</option>";
					getsubmenus($menuitemid, $currentmenuitemobj->parentid, $pmenu->id, 1);
				}
			}
			echo "</select>";
		?>
		</div>



		<label>Select Target</label>
		<div>
			<select name="menuitemtarget">
				<option value="">Same Window</option>
				<option value="blank" <?php if($currentmenuitemobj->target=="blank"){echo "SELECTED=SELECTED";} ?>>New Window</option>
			</select>
		</div>

		<label>Page Title</label>
		<div>
			<input type="text" name="menupagetitle" id="menupagetitle" value="<?php echo $currentmenuitemobj->pagetitle; ?>" size='40' />
			<label>Show title</label>
			<input type="radio" name="pageshowtitle" value="1" <?php if($currentmenuitemobj->showtitle == '1'){ ?>checked<?php } ?> />Yes
			<input type="radio" name="pageshowtitle" value="0" <?php if($currentmenuitemobj->showtitle == '0'){ ?>checked<?php } ?> />No


		</div>

		<div>
			<input type="radio" name="menupermission" value="0" <?php if($currentmenuitemobj->menuperms == '0'){ ?>checked<?php } ?> /><label>All users</label>
			<input type="radio" name="menupermission" value="1" <?php if($currentmenuitemobj->menuperms == '1'){ ?>checked<?php } ?> /><label>Only Registered</label>
			<input type="radio" name="menupermission" value="2" <?php if($currentmenuitemobj->menuperms == '2'){ ?>checked<?php } ?> /><label>Only non-registered</label>
		</div>

		<input type="hidden" name="id" value="<?php echo $menuid ?>" />
		<?php if($menuitemid){ ?><input type="hidden" name="menuitemid" value="<?php echo $menuitemid ?>" /><input type="hidden" name="task" value="editmenuitem" /><?php }else{ ?><input type="hidden" name="task" value="addmenuitem" /><?php } ?>
		<?php hycus::addformhash(); ?>
		<input type="submit" value="Save" class="button"/>
	</form><?php
	hycus::adminlink("goback_menuitemlist", "?adminmodule=menus&task=menuitemlist&id=".$menuid, "modulewrapper", "<< Cancel & Go back");
	}
}
function deletemenuitem($menuid, $menuitemid){

	hycus::checkformhash() or die("Invalid Request");

	$db = new hdatabase();

	$checkdefaultmenuitem = $db->get_rec("#__menuitems", "id", "defaultmenu='1'");
	if($checkdefaultmenuitem->id==$menuitemid){}
	else{ $db->db_delete("#__menuitems", "id = '$menuitemid'"); }

	hycus::ajax_redirect("?adminmodule=menus&task=menuitemlist&id=".$menuid, "modulewrapper");
}
function enablemenuitem($menuid, $menuitemid){

	hycus::checkformhash() or die("Invalid Request");

	$db = new hdatabase();
	$db->db_update("#__menuitems", "enabled = '1'", "id = '$menuitemid'");

	hycus::ajax_redirect("?adminmodule=menus&task=menuitemlist&id=".$menuid, "modulewrapper");
}
function disablemenuitem($menuid, $menuitemid){

	hycus::checkformhash() or die("Invalid Request");

	$db = new hdatabase();
	$db->db_update("#__menuitems", "enabled = '0'", "id = '$menuitemid'");

	hycus::ajax_redirect("?adminmodule=menus&task=menuitemlist&id=".$menuid, "modulewrapper");
}
function defaultmenuitem($menuid, $menuitemid){

	hycus::checkformhash() or die("Invalid Request");

	$db = new hdatabase();
	$db->db_update("#__menuitems", "defaultmenu = '0'", "defaultmenu = '1'");
	$db->db_update("#__menuitems", "defaultmenu = '1'", "id = '$menuitemid'");

	hycus::ajax_redirect("?adminmodule=menus&task=menuitemlist&id=".$menuid, "modulewrapper");
}

function gettaskvalues(){
	if(is_file("modules/".hycus::getcleanvar("item")."/pager.xml"))
	{
		$xml = simplexml_load_file("modules/".hycus::getcleanvar("item")."/pager.xml");
		$pager = $xml->getName();
		echo "<select id='pagerselect'>";
		echo "<option value=''>Select $pager</option>";
		foreach($xml->children() as $child)
		{
			echo "<option value='$child'";
			if($child['subval']){echo "subval='".$child['subval']."'";}
			echo ">".$child."</option>";
		}
		echo "</select>";
		?>
		<script type="text/javascript">
			$(function() {
				$("#pagerselect").change(function () {
					$("select#pagerselect").attr("disabled", true);

					var menupagervalue = $('select#pagerselect option:selected').val();
					var menusubpagervalue = $('select#pagerselect option:selected').attr("subval");
					var existinglink = $('#menuitemlink').val();
					if(menusubpagervalue){
						var subpagerlink = "<?php echo hycus::getroot(); ?>"+existinglink+"&response=module&<?php echo $pager; ?>="+menusubpagervalue;
					}
					if(menupagervalue){
						$('#menuitemlink').val(existinglink+"&<?php echo $pager; ?>="+menupagervalue);
					}
					if(menusubpagervalue){
						$.ajax({
							url: subpagerlink,
							success: function(html){
								$("span#subpagervalue").empty();
								$("span#subpagervalue").prepend(html);
								$("span#subpagervalue").fadeIn("slow");
							}
						});
					}
				});
			});
		</script>

		<?php
	}
}
function sortmenulist()
{
	$db = new hdatabase();

	foreach (hycus::getcleanvar('menuitemlist') as $position => $item) :
		$db->db_update("#__menuitems", "ordering = '$position'", "id = '$item'");
	endforeach;
	hycus::ajax_redirect("?adminmodule=menus&task=menuitemlist&id=".hycus::getcleanvar('menuid')."&response=ajax", "modulewrapper");

}
function getsubmenus($currentid, $currentparentid, $pmenuid, $level){

	$db = new hdatabase();
	$pmenus = $db->get_recs("#__menuitems", "*", "parentid='$pmenuid'");
	foreach($pmenus AS $pmenu)
	{
	if($currentid == $pmenu->id){}else{
	echo "<option value='".$pmenu->id."'";
		if($currentparentid == $pmenu->id){ echo "selected=SELECTED"; }
		echo ">";
		for($i=1; $i<=$level; $i++){
			echo "&nbsp;&nbsp;&nbsp;";
		}

		echo "|_".$pmenu->itemtitle ."</option>";
		getsubmenus($currentid, $pmenu->id,($level+1));
	}
	}
}
function submenulist($menuid, $pmenuid, $level){

	$db = new hdatabase();
	$menuitemsobj = $db->get_recs("#__menuitems","*", "parentid='".$pmenuid."' ORDER by ordering");
	foreach($menuitemsobj AS $menuitem)
	{
		echo "<li id='menuitemlist_".$menuitem->id."'>";
		echo "<img src='images/sort-arrow.png' alt='move' width='16' height='16' class='handle' />";
		for($i=1; $i<=$level; $i++){
			echo "&nbsp;&nbsp;&nbsp;";
		}
		hycus::adminlink("editmenuitem_".$menuitem->id, "?adminmodule=menus&task=menuitemform&id=$menuid&menuitemid=".$menuitem->id, "modulewrapper", "--$menuitem->itemtitle");

		echo "<div class='adminlistbuttons'>";
		echo "<span>";
			hycus::adminlink("deletemenuitem_".$menuitem->id, "?adminmodule=menus&task=deletemenuitem&id=$menuid&menuitemid=".$menuitem->id, "modulewrapper", hycus::iconimage('delete.png'), "Delete?", "Are you sure you want to delete this Category? There is no UNDO.");
		echo "</span>";
		echo "<span>";
		if($menuitem->enabled){
			hycus::adminlink("disablemenuitem_".$menuitem->id, "?adminmodule=menus&task=disablemenuitem&id=$menuid&menuitemid=".$menuitem->id, "modulewrapper", hycus::iconimage('yes.png'), "Disable?");
		}else{
			hycus::adminlink("enablemenuitem_".$menuitem->id, "?adminmodule=menus&task=enablemenuitem&id=$menuid&menuitemid=".$menuitem->id, "modulewrapper", hycus::iconimage('no.png'), "Enable?");
		}
		echo "</span>";
		echo "<span>";
		if($menuitem->defaultmenu){
			hycus::adminlink("defaultmenuitem_".$menuitem->id, "?adminmodule=menus&task=defaultmenuitem&id=$menuid&menuitemid=".$menuitem->id, "modulewrapper", hycus::iconimage('home.png'), "This is your Homepage Link");
		}else{
			hycus::adminlink("defaultmenuitem_".$menuitem->id, "?adminmodule=menus&task=defaultmenuitem&id=$menuid&menuitemid=".$menuitem->id, "modulewrapper", hycus::iconimage('not_home.png'), "Make as Homepage Link");
		}
		echo "</span>";
		echo "</div>";

		$checkparentobj = $db->get_recs("#__menuitems", "id", "parentid='".$menuitem->id."'");
		if(count($checkparentobj)){ submenulist($menuid, $menuitem->id, ($level+1)); }
		echo "</li>";
	}
}
?>
