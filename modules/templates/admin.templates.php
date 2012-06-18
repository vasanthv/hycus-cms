<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

defined( 'HYCUSADMINPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

$task = hycus::getcleanvar("task");

if($task=="templateparams"){
	$dtemplate = hycus::getcleanvar("dtemplate");
	$db = new hdatabase();
	$data = $db->get_rec("#__templates", "data", "templatename='$dtemplate'");
	$data = $data->data;

	$paramsfile="templates/".$dtemplate."/config.xml";
	echo "<h4 style='margin:5px 0px;'>Template Configuration</h4>";
	hycus::admin_form("templateForm", "modulewrapper"); ?>
	<form id="templateForm" action="?adminmodule=templates" method="post" class='adminhycusforms'>
	<?php
	if(is_file($paramsfile))
	{
		echo hycusParams::getParams($paramsfile, $data);
		?>
		<input type="hidden" name="dtemplate" value="<?php echo $dtemplate ?>" />
		<input type="hidden" name="task" value="savetemplatedata" />
		<?php hycus::addformhash(); ?>
		<input type="submit" value="Save" class="button"/>
		<?php
	}
	?>
	</form>
	<?php
	hycus::adminlink("gobacktemplate", "?adminmodule=templates", "modulewrapper", "<< Go Back");


}
elseif($task=="savetemplatedata"){

	hycus::checkformhash() or die("Invalid Request");

	$dtemplate = hycus::getcleanvar("dtemplate");
	unset($_POST['dtemplate']);
	unset($_POST['task']);
	if($_POST)
	{
		$configarray = $_POST;
		$configname = array_keys($_POST);
		$count=0;$configstring="";
		foreach($configarray AS $param)
		{
			$configstring .= $configname[$count]."=";
			$configstring .= $param.";";
			$count++;
		}
	}

	$db = new hdatabase();
	$templatecheck = $db->get_rec("#__templates", "templatename", "templatename='$dtemplate'");

	if($templatecheck){
		$db->db_update("#__templates", "data='$configstring'", "templatename = '$dtemplate'");
	}
	else{
		$db->db_insert("#__templates", "templatename, data", "'$dtemplate', '$configstring'");
	}

	hycus::ajax_redirect("?adminmodule=templates", "modulewrapper");
}
else{
global $template;
echo "<h3>Templates</h3>";
echo "<div style='text-align:right;margin: 0px 0px  5px;'>";
	hycus::adminlink("templateconfiglink", "?adminmodule=config", "modulewrapper", "Change active template in configuration page.");
echo "</div>";

$dir = "templates/";
// Open the Template directory, and proceed to read its contents
echo "<table cellpadding='4' width='100%' class='admintable' border='1'>";
echo"<thead>";
echo "<tr><td width='5%'>S.No</td><td align='center'>Template Name</td><td width='10%' align='center'>Active</td></tr>";
echo "</thead>";
$count=1;

if (is_dir($dir)) {
	if ($dh = opendir($dir)) {
		while (($file = readdir($dh)) !== false) {
			if(filetype($dir . $file)=="dir" && $file != "." && $file != "..")
			{
				if($count%2==0){ echo "<tr class='even'>"; }else{ echo "<tr class='odd'>"; }
				echo "<td>$count</td>";
				echo "<td>";
				hycus::adminlink("defaulttemplate_".$count, "?adminmodule=templates&task=templateparams&dtemplate=$file", "modulewrapper", $file);
				echo "</td>";
				echo "<td align='center'>";
				if($template == $file){
					echo hycus::iconimage('star_black.png');
				}
				echo "</td>";
				$count++;
				echo "</tr>";
			}
		}
		closedir($dh);
	}
}
echo "</table>";
}
?>
