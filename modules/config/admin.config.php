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
	case "updateconfig":
		updateconfig();
		break;
	case "updatesef":
		updatesef();
		break;
	case "purgesef":
		purgesef();
		break;
	case "modcon":
		modcon();
		break;
	case "updateauthentication":
		updateauthentication();
		break;
	case "savemodcon":
		savemodcon();
		break;
	default:
		configview();
		break;
}

function updateconfig(){
	global $ajaxadmin;

	hycus::checkformhash() or die("Invalid Request");

	if(!hycus::getcleanvar("ajaxadmin")){ $ajaxadminreq = 0;}
	if($ajaxadminreq != $ajaxadmin){ $ajaxadminchanged=1; }
	$db = new hdatabase();
	$db->db_update("#__config", "sitename='".hycus::getcleanvar("sitename")."', metakeywords='".hycus::getcleanvar("metakey")."', metadesc='".hycus::getcleanvar("metadesc")."', siteurl='".hycus::getcleanvar("siteurl")."', adminemail='".hycus::getcleanvar("adminemail")."', timezone='".hycus::getcleanvar("timezone")."', timedisplayformat='".hycus::getcleanvar("timedisplayformat")."', template='".hycus::getcleanvar("template")."', language='".hycus::getcleanvar("language")."', sessionlimit='".hycus::getcleanvar("sessionlimit")."', paginationlimit='".hycus::getcleanvar("pagelimit")."', disperror='".hycus::getcleanvar("disperror")."', ajaxadmin='".hycus::getcleanvar("ajaxadmin")."'", "identifier = '1'");

	if($ajaxadminchanged){
		hycus::redirect(hycus::getroot()."?admin");
	}
	else{
		hycus::ajax_redirect("?adminmodule=config", "modulewrapper");
	}
}
function purgesef(){

	hycus::checkformhash() or die("Invalid Request");

	$db = new hdatabase();
	$db->db_delete("#__sefurls", "");
	hycus::ajax_redirect("?adminmodule=config", "modulewrapper");
}
function updatesef(){

	hycus::checkformhash() or die("Invalid Request");

	$db = new hdatabase();
	$db->db_update("#__config", "enablesef ='".hycus::getcleanvar("enablesef")."', seftype='".hycus::getcleanvar("seftype")."', sefsuffix='".hycus::getcleanvar("sefsuffix")."', showmodname ='".hycus::getcleanvar("showmodname")."', showmenuid ='".hycus::getcleanvar("showmenuid")."'", "identifier = '1'");
	hycus::ajax_redirect("?adminmodule=config", "modulewrapper");
}
function updateauthentication(){
	hycus::checkformhash() or die("Invalid Request");
	$db = new hdatabase();
	unset($_POST['task']);
	if($_POST)
	{
		$postarray = $_POST;
		$postname = array_keys($_POST);
		$count=0;
		foreach($postarray AS $param)
		{
			$authentication = $db->get_rec("#__auth", "*", "auth_method='".$postname[$count]."'");
			if($authentication)
				$db->db_update("#__auth", "enabled='".$param."'", "auth_method='".$postname[$count]."'");
			else
				$db->db_insert("#__auth", "auth_method, enabled", "'".$postname[$count]."', '".$param."'");
		}
	}

	hycus::ajax_redirect("?adminmodule=config", "modulewrapper");
}
function modcon()
{
	$mod = hycus::getcleanvar("mod");

	$db = new hdatabase();
	$data = $db->get_rec("#__modules", "data", "module='$mod'");
	$data = $data->data;

	$paramsfile="modules/".$mod."/config.xml";
	echo "<h4 id='poptitle'>$mod Module Configuration</h4>";
	hycus::admin_form("moduleconfigForm_".$mod, "modulewrapper"); ?>
	<form id="moduleconfigForm_<?php echo $mod?>" action="?adminmodule=config" method="post" class='adminhycusforms'>
	<?php
	if(is_file($paramsfile))
	{
		echo hycusParams::getParams($paramsfile, $data);
		?>
		<input type="hidden" name="smod" value="<?php echo $mod ?>" />
		<input type="hidden" name="task" value="savemodcon" />
		<input type="submit" value="Save" class="button"/>
		<?php
	}
	?>
	</form><?php
}
function savemodcon()
{
	$smod = hycus::getcleanvar("smod");

	unset($_POST['smod']);
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
	$templatecheck = $db->get_rec("#__modules", "id", "module='$smod'");

	if($templatecheck){
		$db->db_update("#__modules", "data='$configstring'", "module = '$smod'");
	}
	else{
		$db->db_insert("#__modules", "module, data", "'$smod', '$configstring'");
	}

	hycus::ajax_redirect("?adminmodule=config", "modulewrapper");
}
function configview(){
	$db = new hdatabase();

?>
<h4> Site Configuration</h4>
	<script>
	$(document).ready(function() {

		//When page loads...
		$(".tab_content").hide(); //Hide all content
		$("ul.tabs li:first").addClass("active").show(); //Activate first tab
		$(".tab_content:first").show(); //Show first tab content

		//On Click Event
		$("ul.tabs li").click(function() {

			$("ul.tabs li").removeClass("active"); //Remove any "active" class
			$(this).addClass("active"); //Add "active" class to selected tab
			$(".tab_content").hide(); //Hide all tab content

			var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
			$(activeTab).fadeIn(); //Fade in the active ID content
			return false;
		});
	});
	</script>
	<ul class="tabs">
	    <li><a href="#tab1">General</a></li>
	    <li><a href="#tab2">Module Configuration</a></li>
	    <li><a href="#tab3">Authentication</a></li>
	    <li><a href="#tab4">SEF configuration</a></li>
	</ul>

	<div class="tab_container">
	    <div id="tab1" class="tab_content">
			<?php

			$configs = $db->get_rec("#__config", "*");
			hycus::admin_form("hycusconfigForm", "modulewrapper"); ?>
			<form id="hycusconfigForm" action="?adminmodule=config" method="post" class='adminhycusforms'>
				<fieldset>
					<legend><b>Meta Data</b></legend>
					<table width="100%">
						<tr>
							<td width="20%">
								<label>Site Name</label>
							</td>
							<td>
								<input type="text" name="sitename" value="<?php echo $configs->sitename; ?>"  class="required textbox"/>
							</td>
						</tr>
						<tr>
							<td>
								<label>Meta Keywords</label>
							</td>
							<td>
								<textarea type="text" name="metakey" style="width:300px;height:80px;" class="required"><?php echo $configs->metakeywords; ?></textarea>
							</td>
						</tr>
						<tr>
							<td>
								<label>Meta Description</label>
							</td>
							<td>
								<textarea type="text" name="metadesc" style="width:300px;height:80px;" class="required"><?php echo $configs->metadesc; ?></textarea>
							</td>
						</tr>
						<tr>
							<td>
								<label>Site URL</label>
							</td>
							<td>
								<input type="text" name="siteurl" value="<?php echo $configs->siteurl; ?>"  class="required textbox"/>
							</td>
						</tr>
						</table>
					</fieldset>
					<br/>
					<fieldset>
						<legend><b>System Settings</b></legend>
						<table width="100%">
						<tr>
							<td width="20%">
								<label>Admin Email</label>
							</td>
							<td>
								<input type="text" name="adminemail" value="<?php echo $configs->adminemail; ?>"  class="required textbox"/>
							</td>
						</tr>
						<tr>
							<td>
								<label>Time Zone</label>
							</td>
							<td>
								<select name="timezone" class="required">
									<option value="">Select TimeZone</option>
									<option value="-12" <?php if($configs->timezone == "-12"){?>SELECTED=SELECTED<?php } ?> >(UTC -12:00) International Date Line West</option>
									<option value="-11" <?php if($configs->timezone == "-11"){?>SELECTED=SELECTED<?php } ?>>(UTC -11:00) Midway Island, Samoa</option>
									<option value="-10" <?php if($configs->timezone == "-10"){?>SELECTED=SELECTED<?php } ?>>(UTC -10:00) Hawaii</option>
									<option value="-9.3" <?php if($configs->timezone == "-9.30"){?>SELECTED=SELECTED<?php } ?>>(UTC -09:30) Taiohae, Marquesas Islands</option>
									<option value="-9" <?php if($configs->timezone == "-9"){?>SELECTED=SELECTED<?php } ?>>(UTC -09:00) Alaska</option>
									<option value="-8" <?php if($configs->timezone == "-8"){?>SELECTED=SELECTED<?php } ?>>(UTC -08:00) Pacific Time (US &amp; Canada)</option>
									<option value="-7" <?php if($configs->timezone == "-7"){?>SELECTED=SELECTED<?php } ?>>(UTC -07:00) Mountain Time (US &amp; Canada)</option>
									<option value="-6" <?php if($configs->timezone == "-6"){?>SELECTED=SELECTED<?php } ?>>(UTC -06:00) Central Time (US &amp; Canada), Mexico City</option>
									<option value="-5" <?php if($configs->timezone == "-5"){?>SELECTED=SELECTED<?php } ?>>(UTC -05:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
									<option value="-4.30" <?php if($configs->timezone == "-4.30"){?>SELECTED=SELECTED<?php } ?>>(UTC -04:30) Venezuela</option>
									<option value="-4" <?php if($configs->timezone == "-4"){?>SELECTED=SELECTED<?php } ?>>(UTC -04:00) Atlantic Time (Canada), Caracas, La Paz</option>
									<option value="-3.30" <?php if($configs->timezone == "-3.30"){?>SELECTED=SELECTED<?php } ?>>(UTC -03:30) St. John's, Newfoundland and Labrador</option>
									<option value="-3" <?php if($configs->timezone == "-3"){?>SELECTED=SELECTED<?php } ?>>(UTC -03:00) Brazil, Buenos Aires, Georgetown</option>
									<option value="-2" <?php if($configs->timezone == "-2"){?>SELECTED=SELECTED<?php } ?>>(UTC -02:00) Mid-Atlantic</option>
									<option value="-1" <?php if($configs->timezone == "-1"){?>SELECTED=SELECTED<?php } ?>>(UTC -01:00) Azores, Cape Verde Islands</option>
									<option value="0" <?php if($configs->timezone == "0"){?>SELECTED=SELECTED<?php } ?>>(UTC 00:00) Western Europe Time, London, Lisbon, Casablanca</option>
									<option value="1" <?php if($configs->timezone == "1"){?>SELECTED=SELECTED<?php } ?>>(UTC +01:00) Amsterdam, Berlin, Brussels, Copenhagen, Madrid, Paris</option>
									<option value="2" <?php if($configs->timezone == "2"){?>SELECTED=SELECTED<?php } ?>>(UTC +02:00) Istanbul, Jerusalem, Kaliningrad, South Africa</option>
									<option value="3" <?php if($configs->timezone == "3"){?>SELECTED=SELECTED<?php } ?>>(UTC +03:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
									<option value="3.30" <?php if($configs->timezone == "3.30"){?>SELECTED=SELECTED<?php } ?>>(UTC +03:30) Tehran</option>
									<option value="4" <?php if($configs->timezone == "4"){?>SELECTED=SELECTED<?php } ?>>(UTC +04:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
									<option value="4.30" <?php if($configs->timezone == "4.30"){?>SELECTED=SELECTED<?php } ?>>(UTC +04:30) Kabul</option>
									<option value="5" <?php if($configs->timezone == "5"){?>SELECTED=SELECTED<?php } ?>>(UTC +05:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
									<option value="5.30" <?php if($configs->timezone == "5.30"){?>SELECTED=SELECTED<?php } ?>>(UTC +05:30) Bombay, Calcutta, Madras, New Delhi, Colombo</option>
									<option value="5.45" <?php if($configs->timezone == "5.45"){?>SELECTED=SELECTED<?php } ?>>(UTC +05:45) Kathmandu</option>
									<option value="6" <?php if($configs->timezone == "6"){?>SELECTED=SELECTED<?php } ?>>(UTC +06:00) Almaty, Dhaka</option>
									<option value="6.30" <?php if($configs->timezone == "6.30"){?>SELECTED=SELECTED<?php } ?>>(UTC +06:30) Yagoon</option>
									<option value="7" <?php if($configs->timezone == "7"){?>SELECTED=SELECTED<?php } ?>>(UTC +07:00) Bangkok, Hanoi, Jakarta</option>
									<option value="8" <?php if($configs->timezone == "8"){?>SELECTED=SELECTED<?php } ?>>(UTC +08:00) Beijing, Perth, Singapore, Hong Kong</option>
									<option value="8.45" <?php if($configs->timezone == "8.45"){?>SELECTED=SELECTED<?php } ?>>(UTC +08:00) Ulaanbaatar, Western Australia</option>
									<option value="9" <?php if($configs->timezone == "9"){?>SELECTED=SELECTED<?php } ?>>(UTC +09:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
									<option value="9.30" <?php if($configs->timezone == "9.30"){?>SELECTED=SELECTED<?php } ?>>(UTC +09:30) Adelaide, Darwin, Yakutsk</option>
									<option value="10" <?php if($configs->timezone == "10"){?>SELECTED=SELECTED<?php } ?>>(UTC +10:00) Eastern Australia, Guam, Vladivostok</option>
									<option value="10.30" <?php if($configs->timezone == "10.30"){?>SELECTED=SELECTED<?php } ?>>(UTC +10:30) Lord Howe Island (Australia)</option>
									<option value="11" <?php if($configs->timezone == "11"){?>SELECTED=SELECTED<?php } ?>>(UTC +11:00) Magadan, Solomon Islands, New Caledonia</option>
									<option value="11.30" <?php if($configs->timezone == "11.30"){?>SELECTED=SELECTED<?php } ?>>(UTC +11:30) Norfolk Island</option>
									<option value="12" <?php if($configs->timezone == "12"){?>SELECTED=SELECTED<?php } ?>>(UTC +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
									<option value="12.45" <?php if($configs->timezone == "12.45"){?>SELECTED=SELECTED<?php } ?>>(UTC +12:45) Chatham Island</option>
									<option value="13" <?php if($configs->timezone == "13"){?>SELECTED=SELECTED<?php } ?>>(UTC +13:00) Tonga</option>
									<option value="14" <?php if($configs->timezone == "14"){?>SELECTED=SELECTED<?php } ?>>(UTC +14:00) Kiribati</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label>Time Display Format</label>
							</td>
							<td>
								<select name="timedisplayformat">
									<option>Select Time display format</option>
									<option value="1" <?php if($configs->timedisplayformat == "1"){?>SELECTED=SELECTED<?php } ?>>Thursday 24th of June 2010 02:41:22 PM</option>
									<option value="2" <?php if($configs->timedisplayformat == "2"){?>SELECTED=SELECTED<?php } ?>>Jun 24, 2010 - 18:16:51</option>
									<option value="3" <?php if($configs->timedisplayformat == "3"){?>SELECTED=SELECTED<?php } ?>>2010-06-24 14:41:22</option>
									<option value="4" <?php if($configs->timedisplayformat == "4"){?>SELECTED=SELECTED<?php } ?>>2 days and 15 minutes ago</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label>Design Template</label>
							</td>
							<td>
								<?php
									$dir = "templates/";
									echo "<select name='template' id='template' class='required'>";
									echo "<option value=''>Select Template</option>";
									// Open the Module directory, and proceed to read its contents
									if (is_dir($dir)) {
										if ($dh = opendir($dir)) {
											while (($file = readdir($dh)) !== false) {
												if(filetype($dir . $file)=="dir" && $file != "." && $file != "..")
												{
													echo "<option value='$file'";
													if($configs->template==$file){echo "SELECTED=SELECTED";}
													echo">".$file."</option>";
												}
											}
											closedir($dh);
										}
									}
									echo "</select>";
								?>
							</td>
						</tr>
						<tr>
							<td>
								<label>Defalt Language Folder</label>
							</td>
							<td>
								<?php
									$dir = "modules/language/";
									echo "<select name='language' id='language' class='required'>";
									echo "<option value=''>Select Language</option>";
									// Open the Module directory, and proceed to read its contents
									if (is_dir($dir)) {
										if ($dh = opendir($dir)) {
											while (($file = readdir($dh)) !== false) {
												if(filetype($dir . $file)=="dir" && $file != "." && $file != "..")
												{
													echo "<option value='$file'";
													if($configs->language==$file){echo "SELECTED=SELECTED";}
													echo">".$file."</option>";
												}
											}
											closedir($dh);
										}
									}
									echo "</select>";
								?>
							</td>
						</tr>
						<tr>
							<td>
								<label>Session Time Limit</label>
							</td>
							<td>
								<input type="text" name="sessionlimit" value="<?php echo $configs->sessionlimit; ?>"  class="required textbox" style='width:50px;'/>&nbsp; Seconds
							</td>
						</tr>
						<tr>
							<td>
								<label>Pagination Limit</label>
							</td>
							<td>
								<input type="text" name="pagelimit" value="<?php echo $configs->paginationlimit; ?>"  class="required textbox" style='width:50px;'/>
							</td>
						</tr>
						<tr>
							<td>
								<label>Display Errors</label>
							</td>
							<td>
								<input type="radio" name="disperror" value="1" <?php if($configs->disperror){ ?>CHECKED=CHECKED<?php } ?>/>Yes
								<input type="radio" name="disperror" value="0" <?php if(!$configs->disperror){ ?>CHECKED=CHECKED<?php } ?>/>No
							</td>
						</tr>
						<tr>
							<td>
								<label>Enable Ajax'ed Admin side</label>
							</td>
							<td>
								<input type="checkbox" name="ajaxadmin" value="1" <?php if($configs->ajaxadmin){ ?>CHECKED=CHECKED<?php } ?> style='height:25px;'/>
							</td>
						</tr>
					</table>
				</fieldset>
				<br/>
				<table width="100%">
					<tr>
						<td colspan="2">
							<input type="hidden" name="task" value="updateconfig" />
							<?php hycus::addformhash(); ?>
							<input type="submit" value="Update" class="button"/>
						</td>
					</tr>
				</table>
			</form>
	    </div>
	    <div id="tab2" class="tab_content">
	    	<link rel="stylesheet" href="assets/popup/popup.css" type="text/css" />
	    	<script src="assets/popup/popup.js" type="text/javascript"></script>
	    	<?php
	    	echo "<style>.admintable tr:hover{background:#F0FFF0;cursor:pointer;}</style>";
	    	echo "<table id='button' cellpadding='5' class='admintable' border='1'>";
	    	echo "<thead><tr><td>Module name</td><td></td></tr></thead>";
			$dir = "modules/";
			// Open the Module directory, and proceed to read its contents
			if (is_dir($dir)) {
				if ($dh = opendir($dir)) {
					while (($file = readdir($dh)) !== false) {
						if(filetype($dir . $file)=="dir" && $file != "." && $file != "..")
						{
							if(is_file("modules/$file/config.xml"))
							{
								$modcheck = $db->get_rec("#__modules", "id", "module='$file'");
								if($modcheck->id)
								{$conftext = "Edit Configuration"; }
								else
								{$conftext = "Configure"; }
								echo "<tr>";
								echo "<td>$file</td>";
								echo "<td>";
									hycus::adminlink("modcon_".$file, "?response=ajax&adminmodule=config&task=modcon&mod=$file", "contactArea", "$conftext", "", "", "1");
								echo "</td>";
								echo "</tr>";
							}
						}
					}
					closedir($dh);
				}
			}
			echo "</table>";
	    	?>
			<div id="popupContact">
				<a id="popupContactClose">x</a>
				<p id="contactArea">
					Please Click on the link for module configuration.
				</p>
			</div>
			<div id="backgroundPopup"></div>
	    </div>
	    <div id="tab3" class="tab_content">
			<?php hycus::admin_form("hycusauthenticationForm", "modulewrapper"); ?>
	    	<form id="hycusauthenticationForm" action="?adminmodule=config" method="post" class='adminhycusforms'>
				<?php
					$dir = "libraries/auth/";
					echo "<table cellpadding='5' class='admintable' border='1'>";
					echo "<thead><tr><td align='center'>Authentication Method</td><td align='center'>Enabled</td></tr></thead>";
					// Open the Module directory, and proceed to read its contents
					if (is_dir($dir)) {
						if ($dh = opendir($dir)) {
							while (($file = readdir($dh)) !== false) {
								if(filetype($dir . $file)=="file" && $file!="index.html")
								{
									echo "<tr>";
										echo "<td>".str_replace(".php", "", "$file")."</td>";
										$enabled = $db->get_rec("#__auth", "enabled", "auth_method='auth_".str_replace(".php", "", "$file")."'");
										echo "<td align='center'>";
											echo "<input name='auth_".str_replace(".php", "", "$file")."' type='radio' value='enabled' ";
											if($enabled->enabled=="enabled"){echo "CHECKED=CHECKED"; }
											echo "/>Yes";
											echo "<input name='auth_".str_replace(".php", "", "$file")."' type='radio' value='disabled' ";
											if($enabled->enabled=="disabled"){echo "CHECKED=CHECKED"; }
											echo "/>No";
										echo "</td>";
									echo"</tr>";
								}
							}
							closedir($dh);
						}
					}
					echo "</table>";
				?>
				<div>
					<input type="hidden" name="task" value="updateauthentication" />
					<?php hycus::addformhash(); ?>
					<input class="button" type="submit" value="Save" />
				</div>
			</form>
	    </div>
	     <div id="tab4" class="tab_content">
			<fieldset>
			<?php hycus::admin_form("hycussefconfForm", "modulewrapper"); ?>
				<form id="hycussefconfForm" action="?adminmodule=config" method="post" class='adminhycusforms'>
					<legend><b>SEF Settings</b></legend>
					<table width="100%">
						<tr>
							<td width="20%">
								<label>Enable SEF:</label>
							</td>
							<td>
								<input type="radio" name="enablesef" value="1" <?php if($configs->enablesef){ ?>CHECKED=CHECKED<?php } ?>/>Yes
								<input type="radio" name="enablesef" value="0" <?php if(!$configs->enablesef){ ?>CHECKED=CHECKED<?php } ?>/>No
							</td>
						</tr>
						<tr>
							<td>
								<label>SEF Type:</label>
							</td>
							<td>
								<select name="seftype">
									<option value="1" <?php if($configs->seftype == "1"){?>SELECTED=SELECTED<?php } ?>>With .htaccess</option>
									<option value="2" <?php if($configs->seftype == "2" || !$configs->seftype){?>SELECTED=SELECTED<?php } ?>>Without .htaccess</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label>SEF suffix:</label>
							</td>
							<td>
								<input type="text" name="sefsuffix" value="<?php echo $configs->sefsuffix; ?>"  class="textbox" style='width:50px;'/>
							</td>
						</tr>
						<tr>
							<td width="20%">
								<label>Show Module name:</label>
							</td>
							<td>
								<input type="radio" name="showmodname" value="1" <?php if($configs->showmodname){ ?>CHECKED=CHECKED<?php } ?>/>Yes
								<input type="radio" name="showmodname" value="0" <?php if(!$configs->showmodname){ ?>CHECKED=CHECKED<?php } ?>/>No
								<small>&nbsp;&nbsp;&nbsp;( Note: We recommend this to be enabled.)</small>
							</td>
						</tr>
						<tr>
							<td width="20%">
								<label>Show Menu id:</label>
							</td>
							<td>
								<input type="radio" name="showmenuid" value="1" <?php if($configs->showmenuid){ ?>CHECKED=CHECKED<?php } ?>/>Yes
								<input type="radio" name="showmenuid" value="0" <?php if(!$configs->showmenuid){ ?>CHECKED=CHECKED<?php } ?>/>No
								<small>&nbsp;&nbsp;&nbsp;( Note: We recommend this to be enabled.)</small>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<?php hycus::adminlink("addcontent", "?adminmodule=config&task=purgesef", "modulewrapper", "Purge old sef urls"); ?>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="hidden" name="task" value="updatesef" />
								<?php hycus::addformhash(); ?>
								<input type="submit" value="Save" class="button"/>
							</td>
						</tr>
					</table>
				</form>
			</fieldset>

		</div>
	</div>
<?php } ?>