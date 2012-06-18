<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

 defined( 'HYCUSADMINPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

$task = hycus::getcleanvar("task");

 switch($task){
	case "deletefile":
	deletefile();
	break;
	case "deletefolder":
	deletefolder();
	break;
	case "makefolder":
	makefolder();
	break;
	default:
	defaultfilefolder();
	break;
 }

function deletefile(){
	hycus::checkformhash() or die("Invalid Request");
	if(hycus::getcleanvar("dir")){
		$deletedir = hycus::getcleanvar("dir");
	}
	else{
		$deletedir = "images/media";
	}
	$deletefile = hycus::getcleanvar("file");
	unlink($deletedir."/".$deletefile);

	hycus::ajax_redirect("?adminmodule=media&dir=".$deletedir, "modulewrapper");
}
function deletefolder(){
	if(hycus::getcleanvar("dir")){
		$deletedir = hycus::getcleanvar("dir");
	}
	else{
		$deletedir = "images/media";
	}
	$deletefolder = hycus::getcleanvar("folder");

	SureRemoveDir($deletedir."/".$deletefolder, true);
	hycus::ajax_redirect("?adminmodule=media&dir=".$deletedir, "modulewrapper");
}
function makefolder(){
	if(hycus::getcleanvar("dir")){
		$dir = hycus::getcleanvar("dir");
	}
	else{
		$dir = "images/media";
	}
	if(hycus::getcleanvar("foldername"))
		$makefolder = hycus::getcleanvar("foldername");
	else
		$makefolder = "New Folder";

	if(is_dir($dir."/".$makefolder))
	{}
	else
	mkdir($dir."/".$makefolder, 0, true);
	hycus::ajax_redirect("?adminmodule=media&dir=".$dir, "modulewrapper");
}
function defaultfilefolder(){
	if(!hycus::getcleanvar("dir") || (!strncmp(hycus::getcleanvar("dir"),"images/",7))){

		if(hycus::getcleanvar("dir")){$dir = hycus::getcleanvar("dir")."/"; }
		else{ $dir = "images/media/"; }
		?>
		<style>
			.adminmedia tr:hover{background:#F0FFF0;}
		</style>
		<?php

		echo "<table width='100%' cellpadding='3' class='adminmedia'>";
		// Open the Module directory, and proceed to read its contents
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				$count=1;
				while (($dirfile = readdir($dh)) !== false) {
					if(filetype($dir . $dirfile)=="dir"){
						echo "<tr><td>";
						echo hycus::iconimage('icon_folder_closed_16.png')."&nbsp;";
						if($dirfile=="."){
						hycus::adminlink("insidefolder_".$count, "?adminmodule=media", "modulewrapper", $dirfile);
						}
						elseif($dirfile==".."){
							$prevdirarray = explode("/", $dir);
							unset($prevdirarray[count($prevdirarray)-2]);
							array_pop($prevdirarray);
							$prevdir = implode("/", $prevdirarray);
							if((hycus::getcleanvar("dir") == "images/media") || !hycus::getcleanvar("dir"))
							hycus::adminlink("insidefolder_".$count, "?adminmodule=media", "modulewrapper", $dirfile);
							else
							hycus::adminlink("insidefolder_".$count, "?adminmodule=media&dir=".($prevdir), "modulewrapper", $dirfile);
						}
						else{
						hycus::adminlink("insidefolder_".$count, "?adminmodule=media&dir=".($dir . $dirfile), "modulewrapper", $dirfile);
						echo "<span style='float:right;'>";
						hycus::adminlink("deletefile_".$count, "?adminmodule=media&task=deletefolder&dir=".hycus::getcleanvar("dir")."&folder=$dirfile", "modulewrapper", hycus::iconimage('delete.png'),"", "Are you sure you want to delete this Folder? There is no UNDO.");
						echo "</span>";
						}
						echo "</td></tr>";
					}
		            $count++;
				}
				closedir($dh);
			}
			if ($dh = opendir($dir)) {
				$count=1;
				while (($file = readdir($dh)) !== false) {
					if(filetype($dir . $file)=="file"){
					echo "<tr><td>";
					echo hycus::iconimage('file_icon.gif')."&nbsp;";
		            echo "<a href='".($dir . $file)."' target='_blank'>" .$file. "</a>&nbsp;";
					echo "<span style='float:right;'>";
					hycus::adminlink("deletefile_".$count, "?adminmodule=media&task=deletefile&dir=".hycus::getcleanvar("dir")."&file=$file", "modulewrapper", hycus::iconimage('delete.png'),"", "Are you sure you want to delete this File? There is no UNDO.");
					echo "</span>";
		            echo "</td></tr>";
					}
				$count++;
				}
				closedir($dh);
			}
		}
		echo "</table>";

		?>
		<div id="id">
		<link href="assets/uploadify/css/uploadify.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="assets/uploadify/scripts/swfobject.js"></script>
		<script type="text/javascript" src="assets/uploadify/scripts/jquery.uploadify.v2.1.0.min.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {
			$("#uploadify").uploadify({
				'uploader'       : 'assets/uploadify/scripts/uploadify.swf',
				'script'         : 'assets/uploadify/scripts/adminmedia.php',
				'cancelImg'      : 'assets/uploadify/cancel.png',
				'folder'         : '<?php echo $dir; ?>',
				'queueID'        : 'fileQueue',
				'auto'           : false,
				'multi'          : true
			});
		});
		</script>
		<div id="fileQueue"></div>
		<table><tr>
		<td>
			<input type="file" name="uploadify" id="uploadify" />
			<p>
				<?php hycus::adminlink("refreshlist", "?adminmodule=media&dir=".hycus::getcleanvar("dir"), "modulewrapper", "Refresh List"); ?> |
				<a href="javascript:$('#uploadify').uploadifyUpload();">Start Upload</a> |
				<a href="javascript:jQuery('#uploadify').uploadifyClearQueue()">Cancel All Uploads</a>
			</p>
		</td>
		<td valign='top'>
			<?php hycus::admin_form("makefolderForm", "modulewrapper"); ?>
			<form id="makefolderForm" action="?adminmodule=media&task=makefolder&dir=<?php echo hycus::getcleanvar("dir"); ?>" method="post" class='adminhycusforms' name='makefolderForm'>
				<input type="text" name='foldername' value="" />
				<?php hycus::addformhash(); ?>
				<input type="submit" value='Create Folder'/>
			</form>
		</td>
		</tr>
		<tr><td colspan="2"><span style='font-size:13px;'>Note: Please click "<b>Refresh List</b>" after any file upload.</span></td></tr>
		</table>
		</div>

	<?php
	}
}
function SureRemoveDir($dir, $DeleteMe) {
    if(!$dh = @opendir($dir)) return;
    while (false !== ($obj = readdir($dh))) {
        if($obj=='.' || $obj=='..') continue;
        if (!@unlink($dir.'/'.$obj)) SureRemoveDir($dir.'/'.$obj, true);
    }

    closedir($dh);
    if ($DeleteMe){
        @rmdir($dir);
    }
}

?>