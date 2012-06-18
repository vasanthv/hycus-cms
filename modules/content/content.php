<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

hycusLoader::hycusloadview("content");

$task = hycus::getcleanvar("task");
$id = hycus::getcleanvar("id");
switch($task){
	case "catview":
		catview();
		break;
	case "addblog":
		addeditblog();
		break;
	case "editblog":
		addeditblog($id);
		break;
	case "haddeditblog":
		haddeditblog($id);
		break;
	case "blog":
		blog($id);
		break;
	case "page";
		page($id);
		break;
	case "menucontentlist";
		menucontentlist();
		break;
	case "menucategorylist";
		menucategorylist();
		break;
	default:
		break;
}
function menucontentlist()
{
	$db = new hdatabase();
	$contents = $db->get_recs("#__contents", "id, title", "enabled = '1'");
	if($contents){
		echo "<select id='contentselect'>";
		echo "<option value=''>Select Content</option>";
		foreach($contents as $content)
		{
			echo "<option value='".$content->id."'>".$content->title."</option>";
		}
		echo "</select>";
		?>
		<script type="text/javascript">
			$(function() {
				$("#contentselect").change(function () {
					$("select#contentselect").attr("disabled", true);
					var contentselectvalue = $('select#contentselect option:selected').val();
					var existinglink = $('#menuitemlink').val();
					if(contentselectvalue){
						$('#menuitemlink').val(existinglink+"&id="+contentselectvalue);
					}
				});
			});
		</script>
		<?php
	}
}
function menucategorylist()
{
	$db = new hdatabase();
	$categories = $db->get_recs("#__categories", "id, title", "user_cat = '1'");
	if($categories){
		echo "<select id='contentselect'>";
		echo "<option value=''>Select Category</option>";
		foreach($categories as $category)
		{
			echo "<option value='".$category->id."'>".$category->title."</option>";
		}
		echo "</select>";
		?>
		<script type="text/javascript">
			$(function() {
				$("#contentselect").change(function () {
					$("select#contentselect").attr("disabled", true);
					var contentselectvalue = $('select#contentselect option:selected').val();
					var existinglink = $('#menuitemlink').val();
					if(contentselectvalue){
						$('#menuitemlink').val(existinglink+"&id="+contentselectvalue);
					}
				});
			});
		</script>
		<?php
	}
}

function catview()
{
	global $paginationlimit;
	$catid = hycus::getcleanvar("id");

	$db = new hdatabase();

	$catintro = $db->get_rec("#__categories", "title, showtitle, enablerss", "id = '$catid'");
	if($catintro->showtitle=="yes"){
		echo "<h3>".$catintro->title."</h3>";
	}
	if($catintro->enablerss){
		$rsslink = "<a href='".huri::makeuri("?module=rss&response=module&task=category&id=$catid")."' >"."<img src='".hycus::getroot()."images/rssicon.png' alt='rss' />"."</a>";
	}

	if($catid){
		$cat_ids = $db->get_recs("#__categories", "id", "id = '$catid' OR parentid = '$catid'");
		$count=1;
		$where = "";
		foreach($cat_ids AS $cat_id)
		{
			if($count==1)
				$where .= "catid=".$cat_id->id;
			else
				$where .= " OR catid=".$cat_id->id;
			$count++;
		}
	}

	if($where)
	{
		$where = "($where) AND enabled='1'";
	}else{
		$where = "enabled='1'";
	}

	/*Pagination Starts*/

	/* Pagination codes to get the start and end value */
	$page= hycus::getcleanvar("pge");
	if($page){$start = ($page-1)*$paginationlimit;}
	else{$start = 0;}

	/*Pagination codes to get the total number of results (for getting total page count)*/
	$totalblogs = $db->get_recs("#__contents", "*", "$where");
	$tot_results = count($totalblogs);

	/*Pagination Ends*/

	/*Gets the results based on the start and end value we got previously*/
	$blogs = $db->get_recs("#__contents", "*", "$where", "ordering", "$start", "$paginationlimit");

	if($blogs){
		hycusContentView::catview($blogs);

		$thisurl = "?module=".hycus::getcleanvar("module")."&task=".hycus::getcleanvar("task")."&id=".$catid."&menuid=".hycus::getcleanvar("menuid");

		/*Pagination Display Starts*/
		// Loads the pagination module and its respective function to display pagination results
		hycusLoader::loadModule("pagination");
		paginationlinks($thisurl, $tot_results, $paginationlimit);
		/*Pagination Display Ends*/

		echo $rsslink;
	}
	else{
		echo no_result;
	}
}

function blog($id)
{
	if($id){
		$db = new hdatabase();
		$blogdata = $db->get_rec("#__contents", "*", "id = '$id' AND enabled='1'");
		hycusContentView::blog($blogdata);
	}
	else{
		echo id_missing;
	}
	updatehit($id);
}
function page($id)
{
	if($id){
		$db = new hdatabase();
		$blogdata = $db->get_rec("#__contents", "*", "id = '$id' AND enabled='1'");
		hycusContentView::page($blogdata);
	}
	else{
		echo id_missing;
	}
	updatehit($id);
}
function addeditblog($id=null)
{
	$db = new hdatabase();
	$blogdata = $db->get_rec("#__contents", "*", "id = '$id'");
	hycusContentView::addblog($blogdata);
}
function haddeditblog($id)
{
	$blog_title = hycus::getcleanvar("blog_title");
	$blog_desc = hycus::getcleaneditorvar("blog_desc");
	$blog_desc = str_replace('"', '\"', str_replace("'", "\'", $blog_desc));
	$blog_cat = hycus::getcleanvar("blog_cat");
	$enable_comments = hycus::getcleanvar("enable_comments");
	$current_user_id = hycus::getthisuserid();
	$menuid = hycus::getcleanvar("menuid");

	$time= time();
	$db = new hdatabase();
	if(hycus::getcleanvar("editblog")){
		echo $db->db_update("#__contents", "catid = '$blog_cat', title = '$blog_title', data = '$blog_desc', enable_comments = '$enable_comments', lastupdated_on='$time' ", "id = '".hycus::getcleanvar("editblog")."'");
		hycus::redirect(huri::makeuri("?module=content&task=blog&id=".hycus::getcleanvar("editblog")."&menuid=".$menuid));
	}
	else {
		if($blog_title && $blog_desc && $blog_cat){
			$new_id = $db->db_insert("#__contents", "catid, uid, title, showtitle, data, enable_comments, added_on, lastupdated_on, enabled", "'$blog_cat', '$current_user_id', '$blog_title', 'yes', '$blog_desc', '$enable_comments', '$time', '$time', '1'");
			hycus::redirect(huri::makeuri("?module=content&task=blog&id=".$new_id."&menuid=".$menuid));
		}
		else{
			hycus::redirect(huri::makeuri("?module=content&task=addblog&menuid=".$menuid));
		}
	}
}
function updatehit($id){
	$db = new hdatabase();
	$hits = $db->get_rec("#__contents", "hits", "id = '$id'");
	$db->db_update("#__contents", "hits = '".($hits->hits + 1)."'", "id = '$id'");
}
?>
