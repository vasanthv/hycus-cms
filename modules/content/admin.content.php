<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

  defined( 'HYCUSADMINPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );
?><script>
	$(document).ready(function(){
	    $("div#modulelist").show();
	    $("div#adminmainmenu").show();
	});
</script><?php

 $task = hycus::getcleanvar("task");
 $contentid = hycus::getcleanvar("id");

switch($task){
	case "sortcontentlist":
		sortcontentlist();
		break;
	case "contentform":
		addeditcontent($contentid);
		break;
	case "addcontent":
		addeditcontent();
		break;
	case "editcontent":
		addeditcontent($contentid);
		break;
	case "enablecontent":
		enablecontent($contentid);
		break;
	case "disablecontent":
		disablecontent($contentid);
		break;
	case "deletecontent":
		deletecontent($contentid);
		break;
	default:
		contentlist();
		break;
}

function contentlist(){

	echo "<div style='padding:0 0 5px;line-height:25px;'>This content manager holds all the contents of this website. You can manage, edit, delete, sort, publish/unpublish these contents from here. You can edit a content by clicking on its name. For adding a new content click 'Add a Content' in the top right corner. You can sort the contents by just 'Drag and Drop' by clicking at the left end of the list. Click on the ".hycus::iconimage('delete.png')." to delete a content (Note: There is no undo.). Also use ".hycus::iconimage('yes.png')." / ".hycus::iconimage('no.png')." to enable and disable the content.</div>";

	$db = new hdatabase();
	$where = "";
	if(hycus::getcleanvar("contentsearch"))
		$where .= "title LIKE '%".hycus::getcleanvar("contentsearch")."%' AND ";
	if(hycus::getcleanvar("catfilter"))
		$where .= "catid = '".hycus::getcleanvar("catfilter")."' AND ";

	$where .= "(enabled = '0' OR enabled = '1') ORDER by ordering ";

	$contentobj = $db->get_recs("#__contents", "*", "$where");

	echo "<h4 style='text-transform:capitalize;'>Contents</h4>";

	echo "<table width='100%'><tr>";

		echo "<td>";
		$cats = $db->get_recs("#__categories", "*");
		hycus::admin_form("catfilterForm", "modulewrapper");
		echo "<form action='?adminmodule=content' method='post' id='catfilterForm' class='adminhycusforms'>";
		echo "<input type='text' name='contentsearch' value='".hycus::getcleanvar("contentsearch")."' class='textbox' style='width:150px;margin:0 5px;'/>";
		echo "<select name='catfilter' id='catfilter' style='margin:0 5px;'>";
			echo "<option value=''>---Category Filter---</option>";
			foreach($cats AS $cat)
			{
				echo "<option value='".$cat->id."'";
				if(hycus::getcleanvar("catfilter")==$cat->id){echo "SELECTED=SELECTED";}
				echo ">". $cat->title ."</option>";
			}
		echo "</select>";
		hycus::addformhash();
		echo "<input type='submit' value='Go'/>";
		echo "</form>";
		echo "</td>";

		echo "<td>";

			if(hycus::getcleanvar("catfilter"))
			$addnewcontentlink="&catfilter=".hycus::getcleanvar("catfilter");

			echo "<div style='text-align:right;margin: 0 10px;'> + ";
			hycus::adminlink("addcontent", "?adminmodule=content&task=contentform".$addnewcontentlink, "modulewrapper", "Add a content");
			echo "</div>";
		echo "</td>";

	echo "</tr></table>";

	?>
	<script type="text/javascript">
	  // When the document is ready set up our sortable with it's inherant function(s)
	  $(document).ready(function() {
		  $("#contentslist").sortable();
	});
	</script>
	<?php

	echo "<ul id='contentslist' class='sortable'>";
	if($contentobj){
		foreach($contentobj AS $content)
		{
			echo "<li id='contentlist_".$content->id."'>";
			echo "<table width='100%'><tr>";
			echo "<td width='1%'>";
			echo "<img src='images/sort-arrow.png' alt='move' width='16' height='16' class='handle' />";
			echo "</td>";
			echo "<td>";
			hycus::adminlink("editcontent_".$content->id, "?adminmodule=content&task=contentform&id=$content->id", "modulewrapper", "$content->title");
			echo "<br/><small>id: <b>$content->id</b>; Added by: <b>".hycus::getusername($content->uid)."</b>; Hits: <b>".$content->hits."</b></small>";

			echo "</td>";

			echo "<td width='10%'>";
			echo "<div class='adminlistbuttons'>";
			echo "<span>";
			if($content->enabled){
				hycus::adminlink("disablecontent_".$content->id, "?adminmodule=content&task=disablecontent&id=$content->id", "modulewrapper", hycus::iconimage('yes.png'), "Disable?");
			}else{
				hycus::adminlink("enablecontent_".$content->id, "?adminmodule=content&task=enablecontent&id=$content->id", "modulewrapper", hycus::iconimage('no.png'), "Enable?");
			}
			echo "</span>";
			echo "<span>";
				hycus::adminlink("deletecontent_".$content->id, "?adminmodule=content&task=deletecontent&id=$content->id", "modulewrapper", hycus::iconimage('delete.png'), "Delete?", "Are you sure you want to delete this Content? There is no UNDO.");
			echo "</span>";
			echo "</div>";
			echo "</td>";
			echo "</tr></table>";

			echo "</li>";
		}
	}
	echo "</ul>"; ?>
	<button id="saveorder">Save order</button>
	<script type="text/javascript">
	  $("#saveorder").click(function() {
			var order = $('#contentslist').sortable('serialize');
			$("#modulewrapper").load("?response=ajax&adminmodule=content&task=sortcontentlist&"+order);
	});
	</script>

	<?php
}
function addeditcontent($contentid = null){

	$task = hycus::getcleanvar("task");

	$db = new hdatabase();
	if($contentid)
		$contentobj = $db->get_rec("#__contents", "*", "id='$contentid'");


	if($task == "addcontent"){

		hycus::checkformhash() or die("Invalid Request");

		$contenttitle =  hycus::getcleanvar("contenttitle");
		$showtitle =  hycus::getcleanvar("showtitle");
		$enabled =  hycus::getcleanvar("cenabled");
		$contentdata =  trim(hycus::getadmineditorvar("contentdata"));
		$contentdata = str_replace('"', '\"', str_replace("'", "\'", $contentdata));
		$contentcat =  hycus::getcleanvar("contentcat");
		$enable_comments =  hycus::getcleanvar("enable_comments");
		$metakeys =  hycus::getcleanvar("metakeys");
		$metadesc =  hycus::getcleanvar("metadesc");
		$subbutton =  hycus::getcleanvar("subbutton");
		$uid = "1";
		$time = time();

		if($contenttitle && $contentdata)
		$lcid = $db->db_insert("#__contents", "catid, uid, title, showtitle, data, enable_comments, added_on, lastupdated_on, enabled, metakeywords, metadescription", "'$contentcat', '$uid', '$contenttitle', '$showtitle', '$contentdata', '$enable_comments', '$time', '$time', '$enabled', '$metakeys', '$metadesc' ");
		if($subbutton == "Apply")
			hycus::ajax_redirect("?adminmodule=content&task=contentform&id=$lcid", "modulewrapper");
		else
			hycus::ajax_redirect("?adminmodule=content&task=contentlist", "modulewrapper");
	}
	elseif($task == "editcontent"){

		hycus::checkformhash() or die("Invalid Request");

		$contenttitle =  hycus::getcleanvar("contenttitle");
		$showtitle =  hycus::getcleanvar("showtitle");
		$enabled =  hycus::getcleanvar("cenabled");
		$contentdata =  trim(hycus::getadmineditorvar("contentdata"));
		$contentdata = str_replace('"', '\"', str_replace("'", "\'", $contentdata));
		$contentcat =  hycus::getcleanvar("contentcat");
		$enable_comments =  hycus::getcleanvar("enable_comments");
		$metakeys =  hycus::getcleanvar("metakeys");
		$metadesc =  hycus::getcleanvar("metadesc");
		$subbutton =  hycus::getcleanvar("subbutton");
		$lastupdated_on = time();

		if($contenttitle && $contentdata)
		$db->db_update("#__contents", "catid='$contentcat', title='$contenttitle', showtitle='$showtitle', data='$contentdata', enable_comments='$enable_comments', lastupdated_on='$lastupdated_on', enabled='$enabled', metakeywords='$metakeys', metadescription='$metadesc'", "id = '$contentid'");
		if($subbutton == "Apply")
		hycus::ajax_redirect("?adminmodule=content&task=contentform&id=$contentid", "modulewrapper");
		else
		hycus::ajax_redirect("?adminmodule=content&task=contentlist", "modulewrapper");
	}
	else{

		echo "<h4 style='text-transform:capitalize;'>";
		if($contentid){
			echo " Edit ".$contentobj->title."</h4>";
		}
		else {
			echo "Add content</h4>";
		}

		hycus::admin_form("contentForm", "modulewrapper");
		?>

		<script>
			$(document).ready(function(){
			    $("div#modulelist").hide();
			    $("div#adminmainmenu").hide();
			});
		</script>

		<form id="contentForm" action="?adminmodule=content" method="post" class='adminhycusforms'>

			<label>Title</label>
			<div><input name="contenttitle" type="text" id="blog_title" class="required textbox" value="<?php echo $contentobj->title; ?>"/></div>

			<div style="margin-bottom:5px;">
				<label>Show Title: </label>
				<input type="radio" name="showtitle" value="no" <?php if($contentobj->showtitle=="no"){ ?>CHECKED=CHECKED<?php } ?>  />No
				<input type="radio" name="showtitle" value="yes" <?php if($contentobj->showtitle=="yes"){ ?>CHECKED=CHECKED<?php }elseif(!$contentobj->showtitle){ ?>CHECKED=CHECKED<?php } ?>  />Yes
			</div>

			<div style="margin-bottom:10px;">
				<label>Enable this content: </label>
				<input type="radio" name="cenabled" value="0" <?php if($contentobj->enabled=="0"){ ?>CHECKED=CHECKED<?php } ?>  />No
				<input type="radio" name="cenabled" value="1" <?php if($contentobj->enabled=="1"){ ?>CHECKED=CHECKED<?php } ?>  />Yes
			</div>

			<!-- Admin editor starts -->


			<label>Description</label>
			<div style='width:810px;'><textarea name="contentdata" id="contentdata" class="required" cols="51"><?php echo str_replace('\"', '"', str_replace("\'", "'", $contentobj->data)); ?></textarea></div>
			<script type="text/javascript">
			//<![CDATA[
				function Insertreadmore()
				{
					// Get the editor instance that we want to interact with.
					var oEditor = CKEDITOR.instances.contentdata;
					var value = document.getElementById( 'readmorecontent' ).value;

					// Check the active editing mode.
					if ( oEditor.mode == 'wysiwyg' )
					{
						// Insert the desired HTML.
						oEditor.insertHtml( value );
					}
					else
						alert( 'You must be on WYSIWYG mode!' );
				}
			//]]>
				function Insertotherlanguage()
				{
					// Get the editor instance that we want to interact with.
					var oEditor = CKEDITOR.instances.contentdata;
					var value = document.getElementById( 'insertotherlangage' ).value;

					// Check the active editing mode.
					if ( oEditor.mode == 'wysiwyg' )
					{
						// Insert the desired HTML.
						oEditor.insertHtml( value );
					}
					else
						alert( 'You must be on WYSIWYG mode!' );
				}
			//]]>
			</script>
			<script type="text/javascript">
			//<![CDATA[
				CKEDITOR.replace( 'contentdata',
				{
					fullPage : true
				});
			//]]>
			</script>
			<input onclick="Insertreadmore();" type="button" value="Insert Readmore" />

			<label>Insert other language text</label><input type="checkbox" onclick="$('#otherlanguages').toggle('slow');"/>
			<textarea id="readmorecontent" style="visibility:hidden;height:0px;width:0px;">&lt;hr id='readmore' /&gt;</textarea><br /><br />

			<div id='otherlanguages' style='width:500px;display:none;border:2px solid #234A8B;padding:5px;'>
				<input onclick="Insertotherlanguage();" type="button" value="Insert the following text into editor" />
				<select name='lansel' class="lang" id='vasanth'>
					<option value="english" selected="selected">English</option>
	                <option value="hindi" >Hindi</option>
					<option value="telugu">Telugu</option>
	                <option value="tamil" >Tamil</option>
					<option value="malayalam">Malayalam</option>
	                <option value="kannada" >Kannada</option>
	                <option value="bengali" >Bengali</option>
	                <option value="punjabi" >Punjabi</option>
	                <option value="gujarathi" >Gujarathi</option>
				</select>
				<div>
					<textarea id="insertotherlangage" style='height:100px' charset="utf-8" onKeyPress="javascript:convertThis(event)" onKeyDown="toggleKBMode(event)"></textarea>
				</div>
			</div>
			<script type="text/javascript" src="<?php hycus::getroot(); ?>assets/editorlang/hycuslanguagechanger.js"></script>

			<!-- Admin editor Ends -->

			<?php
				$db = new hdatabase();
				$cats = $db->get_recs("#__categories", "*", "parentid='0'");
				echo "<div><label>Select Category</label>";
				echo "<select name='contentcat' id='contentcat'>";
				echo "<option value=''>Select Category</option>";
				foreach($cats AS $cat)
				{
					echo "<option value='".$cat->id."'";
					if($contentobj->catid == $cat->id || $cat->id == hycus::getcleanvar('catfilter')){ echo "selected=SELECTED"; }
					echo ">". $cat->title ."</option>";
					getsubcats($contentobj->catid, $cat->id, 1);
				}
				echo "</select>";
				echo "</div>";

			?>
			<div>
				<input type="checkbox" name="enable_comments" <?php if(!$contentobj->enable_comments){}else{ ?>checked="checked" <?php } ?> value="1"/><label>Enable Comments</label>
			</div>

			<table cellpadding="5">
				<tr>
					<td>
						<div><label>Meta Keywords</label></div>
						<textarea name="metakeys" style="height:80px;width:320px;"><?php echo $contentobj->metakeywords; ?></textarea>
					</td>
					<td>
						<div><label>Meta Description</label></div>
						<textarea name="metadesc" style="height:80px;width:320px;"><?php echo $contentobj->metadescription; ?></textarea>
					</td>
				</tr>
			</table>

			<?php if($contentid){ ?><input type="hidden" name="id" value="<?php echo $contentid ?>" /><input type="hidden" name="task" value="editcontent" /><?php }else{ ?><input type="hidden" name="task" value="addcontent" /><?php } ?>
			<?php hycus::addformhash(); ?>
			<input type="submit" name="subbutton" value="Save" class="button" onclick="document.getElementById('contentdata').value=CKEDITOR.instances.contentdata.getData(); CKEDITOR.instances.contentdata.destroy()"/>
			<input type="submit" name="subbutton" value="Apply" class="button" onclick="document.getElementById('contentdata').value=CKEDITOR.instances.contentdata.getData(); CKEDITOR.instances.contentdata.destroy()"/>
		</form>
		<?php hycus::admin_form("contentback", "modulewrapper"); ?>
		<form id="contentback" action="?adminmodule=content" method="post" class='adminhycusforms'>
			<?php hycus::addformhash(); ?>
			<input type="submit" value="Cancel & Go Back" class="button" onclick="document.getElementById('contentdata').value=CKEDITOR.instances.contentdata.getData(); CKEDITOR.instances.contentdata.destroy()"/>
		</form>
		<?php
	}
}
function deletecontent($contentid){
	hycus::checkformhash() or die("Invalid Request");
	$db = new hdatabase();
	$db->db_delete("#__contents", "id = '$contentid'");

	hycus::ajax_redirect("?adminmodule=content&task=contentlist", "modulewrapper");
}
function enablecontent($contentid){
	hycus::checkformhash() or die("Invalid Request");
	$db = new hdatabase();
	$db->db_update("#__contents", "enabled = '1'", "id = '$contentid'");

	hycus::ajax_redirect("?adminmodule=content&task=contentlist", "modulewrapper");
}
function disablecontent($contentid){
	hycus::checkformhash() or die("Invalid Request");
	$db = new hdatabase();
	$db->db_update("#__contents", "enabled = '0'", "id = '$contentid'");

	hycus::ajax_redirect("?adminmodule=content&task=contentlist", "modulewrapper");
}
function sortcontentlist()
{
	$db = new hdatabase();

	foreach (hycus::getcleanvar('contentlist') as $position => $item) :
		$db->db_update("#__contents", "ordering = '$position'", "id = '$item'");
	endforeach;
	hycus::ajax_redirect("?adminmodule=content&task=contentlist", "modulewrapper");

}
function getsubcats($selectedid, $catid, $level){
	$db = new hdatabase();
	$cats = $db->get_recs("#__categories", "*", "parentid = '".$catid."'");
	if($cats)
	{
		foreach($cats AS $cat){
			echo "<option value='".$cat->id."'";
			if($selectedid == $cat->id){ echo "selected=SELECTED"; }
			echo ">";
			for($i=1; $i<=$level; $i++){
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			}
			echo "|_".$cat->title."</option>";
			getsubcats($selectedid, $cat->id,$level+1);
		}
	}
}
?>
