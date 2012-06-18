<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

define( 'HYCUSPAGEPROTECT', "installation" );
error_reporting(0);

$config = "../sites/default/config.php";
if(is_file($config)){
	require $config;
}

require '../libraries/loader.php';
$loader = new hycusLoader();

$task = hycus::getcleanvar("task");

switch($task){
	case "checkdatabase":
		checkdatabase();
		break;
	case "installhycus":
		hycusinstallation();
		break;
	default:
		installationform();
		break;
}

function checkdatabase(){

	$hycus_db_type = hycus::getcleanvar("hycus_db_type");
	$hycus_db_host = hycus::getcleanvar("hycus_db_host");
	$hycus_db_user = hycus::getcleanvar("hycus_db_user");
	$hycus_db_pwd = hycus::getcleanvar("hycus_db_pwd");
	$hycus_db_name = hycus::getcleanvar("hycus_db_name");
	$hycus_db_tbprefix = hycus::getcleanvar("hycus_db_tbprefix");

	//calls the function to write on the configuration file.
	$saveconfig_result = hycusLoader::saveconfigfile($hycus_db_type, $hycus_db_host, $hycus_db_user, $hycus_db_pwd, $hycus_db_name, $hycus_db_tbprefix);

	if($saveconfig_result=="success"){
		if($hycus_db_type == "MySQL"){
			$db = mysql_connect($hycus_db_host, $hycus_db_user, $hycus_db_pwd);
			mysql_select_db($hycus_db_name);
			mysql_query("set names 'utf8'");
			mysql_query("set character set utf8");
		} else if($hycus_db_type == "PostgreSQL"){
			$db = pg_connect("host={$hycus_db_host} dbname={$hycus_db_name} user={$hycus_db_user} password={$hycus_db_pwd}");
		}

		/*Deleting existing tables starts*/
		/* query all tables */
		$sql = "SHOW TABLES FROM $hycus_db_name";
		if($result = mysql_query($sql)){
		  /* add table name to array */
		  while($row = mysql_fetch_row($result)){
		    $found_tables[]=$row[0];
		  }
		}
		else{
		  die("Error, could not list tables. MySQL Error: " . mysql_error());
		}
		/* loop through and drop each table */
		foreach($found_tables as $table_name){
		  $sql = "DROP TABLE $hycus_db_name.$table_name";
		  if($result = mysql_query($sql)){}
		  else{
		    echo "Error deleting $table_name. MySQL Error: " . mysql_error() . "";
		  }
		}
		/*Deleting existing tables starts*/

		$querys = file_get_contents("query.sql");
		$querya = explode('/*End of query*/', $querys);
		foreach ($querya as $query) {
			if (!empty($query)) {
				$r = mysql_query(str_replace("#__", $hycus_db_tbprefix, "$query"));
			}
		}
		echo "<div style='color:green;font-weight:bold;'>Database built successfully.</div>";
	}
	else
	{
		echo "<div style='color:red;font-weight:bold;'>Some problem in database connectivity. Please check the details.</div>";
	}

}
function hycusinstallation(){

	$hycus_site_url = hycus::getcleanvar("hycus_site_url");
	$hycus_site_name = hycus::getcleanvar("hycus_site_name");
	$hycus_admin_name = hycus::getcleanvar("hycus_admin_name");
	$hycus_admin_username = hycus::getcleanvar("hycus_admin_username");
	$hycus_admin_email = hycus::getcleanvar("hycus_admin_email");
	$hycus_admin_pwd = md5(hycus::getcleanvar("hycus_admin_pwd"));
	$hycus_admin_pwd2 = md5(hycus::getcleanvar("hycus_admin_pwd2"));
	$hycus_timezone = hycus::getcleanvar("hycus_timezone");

	$db = new hdatabase();

	$configid = $db->db_insert("#__config", "`identifier`, `sitename`, `metakeywords`, `metadesc`, `siteurl`, `adminemail`, `timezone`, `timedisplayformat`, `template`, `language`, `sessionlimit`, `paginationlimit`, `disperror`, `enablesef`, `seftype`, `sefsuffix`, `showmodname`, `showmenuid` , `ajaxadmin`", "'1', '$hycus_site_name', 'hycus cms, hycus content management system, open source, php based cms', 'Hycus is a free opensource PHP based cms.', '$hycus_site_url', '$hycus_admin_email', '$hycus_timezone', 4, 'hycus_template', 'en', '1000', 5, 0, 1, 2, '.html', 1, 1, 0");

	//Inserting new user
	$auth_token =  md5( uniqid('hycus_') );
	$log_id = $db->db_insert("#__users", "`name`, `username`, `email`, `typeid`, `approved`, `registeredon`, `lastvisiton`, `lastvistfrom`, `auth_token`, `block`, `password`", "'$hycus_admin_name', '$hycus_admin_username', '$hycus_admin_email', '1', '1', '".time()."', '0', '0', '$auth_token', '0', '$hycus_admin_pwd'");

	if($log_id){
		hycus::redirect($hycus_site_url."install/welcome.php?url=".$hycus_site_url);
	}
	else{
		echo "<script>alert('Please build the database first.')</script>";
		hycus::redirect($hycus_site_url."install/");
	}


}
function installationform(){
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="robots" content="noindex, nofollow" />
		<title>HycusCMS Installation</title>
		<script type="text/javascript" src="../assets/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="../assets/form/jquery.form.js"></script>
		<script type="text/javascript" src="../assets/validator/jquery.validate.js"></script>

	<style type="text/css">
	body{
		background:#DFDFDF;
	}
	h3{
		border-bottom:thin dotted #828282;padding-bottom:3px;
		margin:10px 0;
	}
	.error{color:red;}
	.msg{
		border:thin solid #828282;
		padding:5px 10px;
		background:#E7E7E7;
	}
	#installationsubmit{
		border:none;
		background: url(../images/installnow.png) no-repeat;
		height:54px;
		width:154px;
		cursor:pointer;
		font-size:16px;
		font-weight:bold;
	}
	#installationsubmit:hover{
		background: url(../images/installnow-hover.png) no-repeat;
		color:#828282;
	}


	</style>

	</head>
	<body style="margin:0px;padding:0px;">
		<div id="id" style="background:#234A8B;color:#E4E8F0;font-size:30px;padding:10px;"><div style="width:800px;margin:0 auto;">Welcome to Hycus-CMS Installation.</div></div>
		<div id="wrapper" style="border:thin solid #828282;width:800px;margin:0 auto;padding:0 10px;background:#ffffff;">
			<h3>Requirements</h3>
			<div>
				<div class="msg">Please make sure all these requirments are met.</div>
				<table cellpadding="5">
					<tr>
						<td>Php Version > 5.0</td>
						<td>:</td>
						<td><?php echo phpversion() < '5.0' ? '<span style="color:red">No</span> (Please upgrade your PHP version.)' : '<span style="color:green">Yes</span>';	?></td>
					</tr>
					<tr>
						<td>XML Support</td>
						<td>:</td>
						<td><?php echo extension_loaded('xml') ? '<span style="color:green">Yes</span>' : '<span style="color:red">No</span>';	?></td>
					</tr>
					<tr>
						<td>MySQL</td>
						<td>:</td>
						<td><?php echo (function_exists('mysql_connect') || function_exists('mysqli_connect')) ? '<span style="color:green">Yes</span>' : '<span style="color:red">No</span>';	?></td>
					</tr>
					<tr>
						<td>Configuration File</td>
						<td>:</td>
						<td><?php echo (is_writable('../sites/default/')) ? "'sites/default' folder is <span style='color:green'>Writable</span>" : "'sites/default' folder is <span style='color:red'>Not Writable</span>" ;	?></td>
					</tr>
					<tr>
						<td>PHP File Upload</td>
						<td>:</td>
						<td><?php echo ini_get("file_uploads") ? '<span style="color:green">On</span>' : '<span style="color:red">Off</span> (We recommend to Switch it on for proper functioning)';	?></td>
					</tr>
					<tr>
						<td>Browser Cookie</td>
						<td>:</td>
						<td><script type="text/javascript">if(navigator.cookieEnabled == true){ document.write('<span style="color:green">Enabled</span>'); }else{ document.write('<span style="color:red">Disabled</span> (We recommend to Switch it on for proper functioning) '); }</script></td>
					</tr>
				</table>
			</div>
			<script>
				//prepare the form when the DOM is ready
				$(document).ready(function(){
					$("#hycusdatabaseform").validate({
			            submitHandler: function(form) {
			                jQuery(form).ajaxSubmit({
			                    target: "#hycusdatabaseresult"
			                });
			            }
			        });
				});
			</script>
			<form action="" method="post" id="hycusdatabaseform">
				<h3>Database Details</h3>
				<div>
					<table cellpadding="5">
						<tr>
							<td>Database Type</td>
							<td>:</td>
							<td><select name="hycus_db_type" class="required"><option></option><option value="MySQL" SELECTED="SELECTED">MySQL</option><option value="PostgreSQL">PostgreSQL</option></select></td>
						</tr>
						<tr>
							<td>Host</td>
							<td>:</td>
							<td><input type="text" name="hycus_db_host" value="<?php echo $_SESSION['hycus_db_host'] ?>" class="required"/>&nbsp;*</td>
						</tr>
						<tr>
							<td>Database User</td>
							<td>:</td>
							<td><input type="text" name="hycus_db_user" value="<?php echo $_SESSION['hycus_db_user'] ?>" class="required"/>&nbsp;*</td>
						</tr>
						<tr>
							<td>Password</td>
							<td>:</td>
							<td><input type="password" name="hycus_db_pwd" /></td>
						</tr>
						<tr>
							<td>Database Name</td>
							<td>:</td>
							<td><input type="text" name="hycus_db_name" value="<?php echo $_SESSION['hycus_db_name'] ?>" class="required"/>&nbsp;*&nbsp;<small style="color:red;"><i>Note: Existing tables in this database will be deleted.</i></small></td>
						</tr>
						<tr>
							<td>Table Prefix</td>
							<td>:</td>
							<td><input type="text" name="hycus_db_tbprefix" value="hycus_" class="required"/>&nbsp;*</td>
						</tr>
					</table>
					<input type="hidden" name="task" value="checkdatabase"/>
					<input type="submit" value="Check and build database" id='hycusdbbuildbut' onclick='$("#dbajaximg").show();'/>
				</div>
			</form>
			<div id="hycusdatabaseresult"><img id="dbajaximg" style='display:none;' src='../images/ajax-loader.gif'/></div>
			<script type="text/javascript">
				$(document).ready(function(){
					$("#hycusinstallform").validate();
				});
			</script>

			<form action="" method="post" id="hycusinstallform">
				<h3>Default User Details</h3>
				<div>
					<table cellpadding="5">
						<tr>
							<td>Name</td>
							<td>:</td>
							<td><input type="text" name="hycus_admin_name" value="<?php echo $_SESSION['hycus_admin_name'] ?>" class="required"/>&nbsp;*</td>
						</tr>
						<tr>
							<td>Username</td>
							<td>:</td>
							<td><input type="text" name="hycus_admin_username" value="<?php echo $_SESSION['hycus_admin_username'] ?>" class="required"/>&nbsp;*</td>
						</tr>
						<tr>
							<td>Email</td>
							<td>:</td>
							<td><input type="text" name="hycus_admin_email" value="<?php echo $_SESSION['hycus_admin_email'] ?>" class="required email"/>&nbsp;*</td>
						</tr>
						<tr>
							<td>Password</td>
							<td>:</td>
							<td><input type="password" name="hycus_admin_pwd" minlength="5" class="required" id="pwd"/>&nbsp;*</td>
						</tr>
						<tr>
							<td>Re-type Password</td>
							<td>:</td>
							<td><input type="password" name="hycus_admin_pwd2" class="required" minlength="5" equalto="#pwd"/>&nbsp;*</td>
						</tr>
					</table>
				</div>
				<h3>Site Details</h3>
				<div>
					<table cellpadding="5">
						<tr>
							<td>Site Name</td>
							<td>:</td>
							<td><input type="text" name="hycus_site_name" size="30" class="required"/>&nbsp;* <small style="font-size:10px;"> This name can also be edited later.</small></td>
						</tr>
						<tr>
							<td>Site URL</td>
							<td>:</td>
							<td><input type="text" name="hycus_site_url" value="<?php echo "http://".$_SERVER['HTTP_HOST'].substr($_SERVER['REQUEST_URI'], "0", strpos($_SERVER['REQUEST_URI'], "install/")); ?>" size="30"/>&nbsp;* <small style="font-size:10px;"> This is the actual URL of the website. Change this only if it is not the correct root path. </small></td>
						</tr>
						<tr>
							<td>Select TimeZone</td>
							<td>:</td>
							<td>
								<select name="hycus_timezone">
									<option value="">Select TimeZone</option>
									<option value="-12">(UTC -12:00) International Date Line West</option>
									<option value="-11">(UTC -11:00) Midway Island, Samoa</option>
									<option value="-10">(UTC -10:00) Hawaii</option>
									<option value="-9.3">(UTC -09:30) Taiohae, Marquesas Islands</option>
									<option value="-9">(UTC -09:00) Alaska</option>
									<option value="-8">(UTC -08:00) Pacific Time (US &amp; Canada)</option>
									<option value="-7">(UTC -07:00) Mountain Time (US &amp; Canada)</option>
									<option value="-6">(UTC -06:00) Central Time (US &amp; Canada), Mexico City</option>
									<option value="-5">(UTC -05:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
									<option value="-4.30">(UTC -04:30) Venezuela</option>
									<option value="-4">(UTC -04:00) Atlantic Time (Canada), Caracas, La Paz</option>
									<option value="-3.30">(UTC -03:30) St. John's, Newfoundland and Labrador</option>
									<option value="-3">(UTC -03:00) Brazil, Buenos Aires, Georgetown</option>
									<option value="-2">(UTC -02:00) Mid-Atlantic</option>
									<option value="-1">(UTC -01:00) Azores, Cape Verde Islands</option>
									<option value="0" SELECTED=SELECTED>(UTC 00:00) Western Europe Time, London, Lisbon, Casablanca</option>
									<option value="1">(UTC +01:00) Amsterdam, Berlin, Brussels, Copenhagen, Madrid, Paris</option>
									<option value="2">(UTC +02:00) Istanbul, Jerusalem, Kaliningrad, South Africa</option>
									<option value="3">(UTC +03:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
									<option value="3.30">(UTC +03:30) Tehran</option>
									<option value="4">(UTC +04:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
									<option value="4.30">(UTC +04:30) Kabul</option>
									<option value="5">(UTC +05:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
									<option value="5.30">(UTC +05:30) Bombay, Calcutta, Madras, New Delhi, Colombo</option>
									<option value="5.45">(UTC +05:45) Kathmandu</option>
									<option value="6">(UTC +06:00) Almaty, Dhaka</option>
									<option value="6.30">(UTC +06:30) Yagoon</option>
									<option value="7">(UTC +07:00) Bangkok, Hanoi, Jakarta</option>
									<option value="8">(UTC +08:00) Beijing, Perth, Singapore, Hong Kong</option>
									<option value="8.45">(UTC +08:00) Ulaanbaatar, Western Australia</option>
									<option value="9">(UTC +09:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
									<option value="9.30">(UTC +09:30) Adelaide, Darwin, Yakutsk</option>
									<option value="10">(UTC +10:00) Eastern Australia, Guam, Vladivostok</option>
									<option value="10.30">(UTC +10:30) Lord Howe Island (Australia)</option>
									<option value="11">(UTC +11:00) Magadan, Solomon Islands, New Caledonia</option>
									<option value="11.30">(UTC +11:30) Norfolk Island</option>
									<option value="12">(UTC +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
									<option value="12.45">(UTC +12:45) Chatham Island</option>
									<option value="13">(UTC +13:00) Tonga</option>
									<option value="14">(UTC +14:00) Kiribati</option>
								</select>
							</td>
						</tr>
					</table>
					<div class="msg"><div>Note:</div><ol><li>Before Installing, please build the database.</li><li>Before Installing, please check whether all the required fields are filled.</li></ol></div>
					<h3>License Information - GPL License</h3>
					<div>
						<iframe src="../gpl.html" class="license" frameborder="0" marginwidth="25" scrolling="auto" style="width:680px;height:100px;"></iframe>
					</div>

					<div style="width:154px;margin:15px auto 20px;">
						<input type="hidden" name="task" value="installhycus" />
						<input type="submit" id="installationsubmit" value="Agree and Install"/>
					</div>
				</div>
			</form>
		</div>
		<div style="width:330px;margin:30px auto;font-size:11px;color:#828282;"><a href="http://www.hycus.com/" style="color:#828282;" target="_blank">Hycus</a> is Free PHP Based CMS released under the GNU/GPL License. </div>
	</body>
	</html>
	<?php
}
?>