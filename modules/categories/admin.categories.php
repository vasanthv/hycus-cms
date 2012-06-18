<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

 defined( 'HYCUSADMINPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

$task = hycus::getcleanvar("task");
$categoryid = hycus::getcleanvar("id");

switch($task){
	case "sortcategorylist":
		sortcategorylist();
		break;
	case "categoryform":
		addeditcategory($categoryid);
		break;
	case "addcategory":
		addeditcategory();
		break;
	case "editcategory":
		addeditcategory($categoryid);
		break;
	case "enablecategory":
		enablecategory($categoryid);
		break;
	case "disablecategory":
		disablecategory($categoryid);
		break;
	case "deletecategory":
		deletecategory($categoryid);
		break;
	default:
		categorylist();
		break;
}

function categorylist(){

	$db = new hdatabase();
	$categoryobj = $db->get_recs("#__categories", "*", "parentid='0'");

	echo "<h4 style='text-transform:capitalize;'>Categories</h4>";

	echo "<div style='text-align:right;margin:10px 0;'> + ";
	hycus::adminlink("addcategory", "?adminmodule=categories&task=categoryform", "modulewrapper", "Add a category");
	echo "</div>";

	echo "<table cellpadding='4' width='100%' class='admintable' border='1'>" .
			"<thead><tr>" .
			"<td align='center' width='3%'>S.No</td>" .
			"<td align='center'>Category Title</td>" .
			"<td align='center' width='3%'>Id</td>" .
			"<td align='center' width='5%'>Delete</td>" .
			"</tr></thead>";
	$count=1;
	echo "<tbody>";
	foreach($categoryobj AS $category)
	{
		if($count%2==0){ echo "<tr class='even'>"; }else{ echo "<tr class='odd'>"; }

		echo "<td align='center'>$count</td>";
		echo "<td>";
		hycus::adminlink("editcategory_".$category->id, "?adminmodule=categories&task=categoryform&id=$category->id", "modulewrapper", "$category->title");
		echo "</td>";
		echo "<td align='center'>".$category->id."</td>";
		echo "<td align='center'>";
		hycus::adminlink("deletecategory_".$category->id, "?adminmodule=categories&task=deletecategory&id=$category->id", "modulewrapper", hycus::iconimage('delete.png'), "Delete?", "Are you sure you want to delete this Category? There is no UNDO.");
		echo "</td>";
		$count++;
		$count = getsubcategories($category->id, 1, $count);
	}
	echo "</tbody></table>";
}
function addeditcategory($categoryid = null){

	$task = hycus::getcleanvar("task");

	$db = new hdatabase();
	if($categoryid)
		$categoryobj = $db->get_rec("#__categories", "*", "id='$categoryid'");

	if($task == "addcategory"){

		hycus::checkformhash() or die("Invalid Request");

		$categorytitle =  hycus::getcleanvar("categorytitle");
		$showtitle =  hycus::getcleanvar("showtitle");
		$parentcat =  hycus::getcleanvar("parent_cat");
		$description =  hycus::getcleanvar("categorydesc");
		$usercat =  hycus::getcleanvar("usercategory");
		$enablecomments =  hycus::getcleanvar("enablecomments");
		$enablerss =  hycus::getcleanvar("enablerss");

		if($categorytitle)
		$db->db_insert("#__categories", "parentid, title, showtitle, description, enable_comments, user_cat, enablerss", "'$parentcat', '$categorytitle', '$showtitle', '$description', '$enablecomments', '$usercat', '$enablerss' ");
		hycus::ajax_redirect("?adminmodule=categories&task=categorylist", "modulewrapper");
	}
	elseif($task == "editcategory"){

		hycus::checkformhash() or die("Invalid Request");

		$categorytitle =  hycus::getcleanvar("categorytitle");
		$showtitle =  hycus::getcleanvar("showtitle");
		$parentcat =  hycus::getcleanvar("parent_cat");
		$description =  hycus::getcleanvar("categorydesc");
		$usercat =  hycus::getcleanvar("usercategory");
		$enablecomments =  hycus::getcleanvar("enablecomments");
		$enablerss =  hycus::getcleanvar("enablerss");

		if($categorytitle)
		$db->db_update("#__categories", "parentid='$parentcat', title='$categorytitle', showtitle='$showtitle', description='$description', user_cat='$usercat', enable_comments='$enablecomments', enablerss='$enablerss'", "id = '$categoryid'");
		hycus::ajax_redirect("?adminmodule=categories&task=categorylist", "modulewrapper");
	}
	else{

		echo "<h4 style='text-transform:capitalize;'>";
		if($categoryid){
			echo " Edit ".$categoryobj->title."</h4>";
		}
		else {
			echo "Add category</h4>";
		}

		hycus::admin_form("categoryForm", "modulewrapper");
		?>

		<form id="categoryForm" action="?adminmodule=categories" method="post" class='adminhycusforms'>
			<label>Category Title</label>
			<div><input type="text" name="categorytitle" value="<?php echo $categoryobj->title; ?>" class="textbox required"/></div>
			<div style="margin-bottom:10px;">
				<label>Show Title: </label>
				<input type="radio" name="showtitle" value="no" <?php if($categoryobj->showtitle=="no"){ ?>CHECKED=CHECKED<?php } ?>  />No
				<input type="radio" name="showtitle" value="yes" <?php if($categoryobj->showtitle=="yes"){ ?>CHECKED=CHECKED<?php }elseif(!$categoryobj->showtitle){ ?>CHECKED=CHECKED<?php } ?>  />Yes
			</div>

			<label>Select Parent Category</label>
			<div>
			<?php
			/*Get the Category list*/
				$cats = $db->get_recs("#__categories", "*", "parentid='0'");
				echo "<select name='parent_cat' id='parent_cat'>";
				echo "<option value=''>Select Category</option>";
				foreach($cats AS $cat)
				{
					if($categoryobj->id==$cat->id){}else{
						echo "<option value='".$cat->id."'";
						if($categoryobj->parentid == $cat->id){ echo " selected=SELECTED "; }
						echo ">". $cat->title ."</option>";
						getsubcats($categoryobj->parentid, $categoryobj->id, $cat->id, 1);
					}
				}
				echo "</select>";
			?>
			</div>

			<label>Description</label>
			<div><textarea name="categorydesc" id="categorydesc" ><?php echo $categoryobj->description; ?></textarea></div>

			<div>
				<label>Enable Comments in this category?</label>
				<input type="checkbox" name="enablecomments" value='1' <?php if($categoryobj->enable_comments){ echo " CHECKED=CHECKED "; } ?> />
			</div>

			<div>
				<label>Check this only if this category is to be listed in user side while user adds a blog.</label>
				<input type="checkbox" name="usercategory" value='1' <?php if($categoryobj->user_cat){ echo " CHECKED=CHECKED "; } ?> />
			</div>

			<div>
				<label>Show RSS icon.</label>
				<input type="checkbox" name="enablerss" value='1' <?php if($categoryobj->enablerss){ echo " CHECKED=CHECKED "; } ?> />
			</div>

			<?php if($categoryid){ ?><input type="hidden" name="id" value="<?php echo $categoryid ?>" /><input type="hidden" name="task" value="editcategory" /><?php }else{ ?><input type="hidden" name="task" value="addcategory" /><?php } ?>
			<?php hycus::addformhash(); ?>
			<input type="submit" value="Save" class="button"/>
		</form><?php
		hycus::adminlink("goback_categories", "?adminmodule=categories", "modulewrapper", "<< Cancel & Go back");
	}
}
function deletecategory($categoryid){

	hycus::checkformhash() or die("Invalid Request");

	$db = new hdatabase();
	$db->db_delete("#__categories", "id = '$categoryid'");

	hycus::ajax_redirect("?adminmodule=categories&task=categorylist", "modulewrapper");
}
function enablecategory($categoryid){

	hycus::checkformhash() or die("Invalid Request");

	$db = new hdatabase();
	$db->db_update("#__categories", "enabled = '1'", "id = '$categoryid'");

	hycus::ajax_redirect("?adminmodule=categories&task=categorylist", "modulewrapper");
}
function disablecategory($categoryid){
	hycus::checkformhash() or die("Invalid Request");
	$db = new hdatabase();
	$db->db_update("#__categories", "enabled = '0'", "id = '$categoryid'");

	hycus::ajax_redirect("?adminmodule=categories&task=categorylist", "modulewrapper");
}

function getsubcategories($catid, $level, $count){
	$db = new hdatabase();
	$categoryobj = $db->get_recs("#__categories", "*", "parentid = '".$catid."'");
	if($categoryobj)
	{
		foreach($categoryobj AS $category){

				if($count%2==0){ echo "<tr class='even'>"; }else{ echo "<tr class='odd'>"; }

				echo "<td align='center'>$count</td>";
				echo "<td>";
				for($i=1; $i<=$level; $i++){
					echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				}
				hycus::adminlink("editcategory_".$category->id, "?adminmodule=categories&task=categoryform&id=$category->id", "modulewrapper", "-$category->title");
				echo "(id -> <b>$category->id</b>)";
				echo "</td>";

				echo "<td align='center'>";
				hycus::adminlink("deletecategory_".$category->id, "?adminmodule=categories&task=deletecategory&id=$category->id", "modulewrapper", hycus::iconimage('delete.png'), "Delete?");
				echo "</td>";
				$count++;
				$count = getsubcategories($category->id,$level+1,$count);
		}
	}
	return $count;
}
function getsubcats($parentid, $selectedid, $catid,$level){
	$db = new hdatabase();
	$cats = $db->get_recs("#__categories", "*", "parentid = '".$catid."'");
	if($cats)
	{
		foreach($cats AS $cat){
			if($selectedid == $cat->id){}else{
				echo "<option value='".$cat->id."'";
				if($parentid == $cat->id){ echo "selected=SELECTED"; }
				echo ">";
				for($i=1; $i<=$level; $i++){
					echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				}
				echo "|_".$cat->title."</option>";
				getsubcats($parentid, $selectedid, $cat->id,$level+1);
			}
		}
	}
}

?>
