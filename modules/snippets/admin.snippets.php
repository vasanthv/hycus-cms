<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

 defined( 'HYCUSADMINPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

$task = hycus::getcleanvar("task");
$snippetid = hycus::getcleanvar("id");

switch($task){
	case "sortsnippetlist":
		sortsnippetlist();
		break;
	case "snippetform":
		addeditsnippet($snippetid);
		break;
	case "addsnippet":
		addeditsnippet();
		break;
	case "editsnippet":
		addeditsnippet($snippetid);
		break;
	case "enablesnippet":
		enablesnippet($snippetid);
		break;
	case "disablesnippet":
		disablesnippet($snippetid);
		break;
	case "deletesnippet":
		deletesnippet($snippetid);
		break;
	default:
		snippetlist();
		break;
}

function snippetlist(){

	$db = new hdatabase();
	$snippetobj = $db->get_recs("#__snippets", "*", "enabled = '0' OR enabled = '1' ORDER by ordering");

	echo "<div style='padding:0 0 5px;line-height:25px;'>These are the list of all the snippets you have in your website. You can edit the snippet by clicking on it. For adding a new snippet click 'Add a snippet' in the top right corner. You can sort the snippets by just 'Drag and Drop' by licking at the left end of the list. Click on the ".hycus::iconimage('delete.png')." to delete a snippet. Also use ".hycus::iconimage('yes.png')."/".hycus::iconimage('no.png')." to enable and disable the snippet.</div>";

	echo "<h4 style='text-transform:capitalize;'>snippets</h4>";

	echo "<div style='text-align:right;margin: 0 10px;'> + ";
	hycus::adminlink("addsnippet", "?adminmodule=snippets&task=snippetform", "modulewrapper", "Add a snippet");
	echo "</div>";

	?>
	<script type="text/javascript">
	  // When the document is ready set up our sortable with it's inherant function(s)
	  $(document).ready(function() {
	  $("#snippetslist").sortable();
	});
	</script>

	<?php
	if($snippetobj){
		echo "<ul id='snippetslist' class='sortable'>";
		foreach($snippetobj AS $snippet)
		{
			echo "<li id='snippetlist_".$snippet->id."'>";
			echo "<img src='images/sort-arrow.png' alt='move' width='16' height='16' class='handle' />";
			hycus::adminlink("editsnippet_".$snippet->id, "?adminmodule=snippets&task=snippetform&id=$snippet->id", "modulewrapper", "$snippet->title");

			echo "<div class='adminlistbuttons'>";
			echo "<span>";
			if($snippet->enabled){
				hycus::adminlink("disablesnippet_".$snippet->id, "?adminmodule=snippets&task=disablesnippet&id=$snippet->id", "modulewrapper", hycus::iconimage('yes.png'), "Disable?");
			}else{
				hycus::adminlink("enablesnippet_".$snippet->id, "?adminmodule=snippets&task=enablesnippet&id=$snippet->id", "modulewrapper", hycus::iconimage('no.png'), "Enable?");
			}
			echo "</span>";
			echo "<span>";
				hycus::adminlink("deletesnippet_".$snippet->id, "?adminmodule=snippets&task=deletesnippet&id=$snippet->id", "modulewrapper", hycus::iconimage('delete.png'), "Delete?", "Are you sure you want to delete this snippet? There is no UNDO.");
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
		var order = $('#snippetslist').sortable('serialize');
		$("#modulewrapper").load("?response=ajax&adminmodule=snippets&task=sortsnippetlist&"+order);
      });
	</script>

	<?php
}
function addeditsnippet($snippetid = null){

	$task = hycus::getcleanvar("task");

	$db = new hdatabase();
	if($snippetid)
		$snippetobj = $db->get_rec("#__snippets", "*", "id='$snippetid'");


	if($task == "addsnippet"){
		hycus::checkformhash() or die("Invalid Request");

		$snippettitle =  hycus::getcleanvar("snippettitle");
		$snippetcode =  str_replace('"', '\"', $_POST["snippetcode"]);

		if($snippettitle && $snippetcode)
			$db->db_insert("#__snippets", "title, code, enabled", "'$snippettitle', '$snippetcode', '1'");
		hycus::ajax_redirect("?adminmodule=snippets&task=snippetlist", "modulewrapper");
	}
	elseif($task == "editsnippet"){
		hycus::checkformhash() or die("Invalid Request");

		$snippettitle =  hycus::getcleanvar("snippettitle");
		$snippetcode =  str_replace('"', '\"', $_POST["snippetcode"]);

		if($snippettitle && $snippetcode)
		{
			$db->db_update("#__snippets", "title='$snippettitle', code='$snippetcode'", "id = '$snippetid'");
		}
		hycus::ajax_redirect("?adminmodule=snippets&task=snippetlist", "modulewrapper");
	}
	else{


		echo "<h4 style='text-transform:capitalize;'>";
		if($snippetid){
			echo " Edit this snippet - ".$snippetobj->title."</h4>";
		}
		else {
			echo "Add snippet</h4>";
		}

		hycus::admin_form("menuitemForm", "modulewrapper"); ?>
		<br/>
		<form id="menuitemForm" action="?adminmodule=snippets" method="post" class='adminhycusforms'>
			<label>Snippet Title</label>
			<div>
				<input type="text" name="snippettitle" value="<?php echo $snippetobj->title; ?>"  class="textbox"/>
				<small>* This snippet will be called with this name, so make it unique with nospace. Also avoid special characters other than _(underscore).</small>
			</div>

			<script type="text/javascript" src="assets/textarearesizer/jquery.textarearesizer.js"></script>
			<script type="text/javascript">
				/* jQuery textarea resizer plugin usage */
				$(document).ready(function() {
					$('textarea.resizable:not(.processed)').TextAreaResizer();
					$('iframe.resizable:not(.processed)').TextAreaResizer();
				});
			</script>
			<style type="text/css">
				div.grippie {
					background:#EEEEEE url(assets/textarearesizer/grippie.png) no-repeat scroll center 2px;
					border-color:#DDDDDD;
					border-style:solid;
					border-width:0pt 1px 1px;
					cursor:s-resize;
					height:0px;
					overflow:hidden;
				}
				.resizable-textarea textarea {
					display:block;
					margin-bottom:0pt;
					width:95%;
					height: 20%;
				}
			</style>
			<label>Snippet Code</label>
			<div><textarea name="snippetcode"  class="resizable"><?php echo str_replace('\"', '"', $snippetobj->code); ?></textarea></div>

			<?php if($snippetid){ ?><input type="hidden" name="id" value="<?php echo $snippetid ?>" /><input type="hidden" name="task" value="editsnippet" /><?php }else{ ?><input type="hidden" name="task" value="addsnippet" /><?php } ?>
			<?php hycus::addformhash(); ?>
			<input type="submit" value="Save" class="button"/>
		</form><?php
		hycus::adminlink("goback_snippet", "?adminmodule=snippets", "modulewrapper", "<< Cancel & Go back");

	}
}
function deletesnippet($snippetid){
	hycus::checkformhash() or die("Invalid Request");
	$db = new hdatabase();
	$db->db_delete("#__snippets", "id = '$snippetid'");

	hycus::ajax_redirect("?adminmodule=snippets&task=snippetlist", "modulewrapper");
}
function enablesnippet($snippetid){
	hycus::checkformhash() or die("Invalid Request");
	$db = new hdatabase();
	$db->db_update("#__snippets", "enabled = '1'", "id = '$snippetid'");

	hycus::ajax_redirect("?adminmodule=snippets&task=snippetlist", "modulewrapper");
}
function disablesnippet($snippetid){
	hycus::checkformhash() or die("Invalid Request");
	$db = new hdatabase();
	$db->db_update("#__snippets", "enabled = '0'", "id = '$snippetid'");

	hycus::ajax_redirect("?adminmodule=snippets&task=snippetlist", "modulewrapper");
}
function sortsnippetlist()
{
	$db = new hdatabase();

	foreach (hycus::getcleanvar('snippetlist') as $position => $item) :
		$db->db_update("#__snippets", "ordering = '$position'", "id = '$item'");
	endforeach;
	hycus::ajax_redirect("?response=ajax&adminmodule=snippets&task=snippetlist", "modulewrapper");

}

?>
