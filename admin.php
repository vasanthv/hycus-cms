<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

define( 'HYCUSPAGEPROTECT', 1 );
define( 'HYCUSADMINPAGEPROTECT', 1 );

//comment the following line, in your development phase to see the warnings and notices.
error_reporting(0);

if($_REQUEST['site']) { $config = "site/".$_REQUEST['site']."/config.php"; }
else{ $config = "sites/default/config.php"; }

if(is_file($config)){
	require $config;
}
else{
	$rurl = "http://".$_SERVER['HTTP_HOST'].substr($_SERVER['REQUEST_URI'], "0", strrpos($_SERVER['REQUEST_URI'], "/"));
	//PHP redirection script
	header( 'HTTP/1.1 301 Moved Permanently' );
	header( 'Location: '.$rurl.'/install/');
}

require 'libraries/loader.php';
$loader = new hycusLoader();

error_reporting(0);

$adminusername = hycus::getcleanvar("adminusername");
$adminpass = hycus::getcleanvar("adminpass");

$usertypeid = hycus::getusertype(hycus::getthisuserid());
$db = new hdatabase();
$adminaccess = $db->get_rec("#__usertypes", "adminaccess", "id='$usertypeid'");

if(hycus::getthisuserid() && $adminaccess->adminaccess)
	adminaccess();
else
	adminloginform();

function adminloginform(){
	?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<meta name="robots" content="noindex, nofollow" />
			<title>Hycus - Administrator</title>
			<style>
				.admin-key-field {color:#878787;width:400px;height:40px;padding:5px;border:thin solid #A0A0A0;-webkit-border-radius: 5px;-moz-border-radius: 5px;font-size:30px;}
				.admin-key-field:hover, .admin-key-field:hover, .admin-key-field:hover {border:thin solid #000000;}
				#adminsubmit{ border:none;background: url(images/installnow.png) no-repeat;height:54px;width:154px;cursor:pointer;font-size:16px;font-weight:bold;}
				#adminsubmit:hover{ background: url(images/installnow-hover.png) no-repeat;color:#828282; }
			</style>
			<link rel="icon" href="<?php echo hycus::getroot(); ?>templates/hycus_template/favicon.ico" type="image/x-icon" />
		</head>
		<body>
		<div id="enter-admin-key" style="width:420px;margin:200px auto 0px;">
			<form action="<?php echo huri::makeuri("?module=user&task=hlogin"); ?>" method="POST" name="adminlogin">
				<input type="text" name="usr_email" class="admin-key-field" value="Admin Username" autocomplete="off" onfocus="javascript:document.adminlogin.usr_email.value=''"/>
				<br/><br/>
				<input type="password" name="pwd" class="admin-key-field" value="Passwordtext" autocomplete="off" onfocus="javascript:document.adminlogin.pwd.value=''"/>
				<br/><br/>
				<input type="hidden" name="adminloginredirect" value="1" />
				<div style="margin:0px auto;"><input type="submit" id="adminsubmit" value="Submit" /></div>
			</form>
			<div style="width:110px;margin:0px auto;font-size:10px;color:#828282;text-align:center;"></div><br/>
			<div style="width:330px;margin:50px auto 0px;font-size:11px;color:#828282;"><a href="http://www.hycus.com/" style="color:#828282;" target="_blank">Hycus</a> is Free PHP Based CMS released under the GNU/GPL License. </div>
		</div>
		</body>
		</html>
	<?php
}
function adminaccess(){
	global $ajaxadmin;
	if(hycus::getcleanvar("adminmodule") && $ajaxadmin){
		echo "<script>window.location.hash='?".$_SERVER['QUERY_STRING']."';</script>";
		hycusLoader::loadAdminModule(hycus::getcleanvar("adminmodule"));
	}
	elseif(!$ajaxadmin && hycus::getcleanvar("response")=="ajax")
	{hycusLoader::loadAdminModule(hycus::getcleanvar("adminmodule"));}
	else{
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="robots" content="noindex, nofollow" />
		<?php hycusLoader::loadAdminHead(); ?>
		<title><?php global $sitename;echo $sitename; ?> - Administrator</title>
		<!--[if lte IE 7]>
		<style>#admin-title {float:left;padding:0px 10px;position:relative;bottom:0px;top:15px;}</style>
		<![endif]-->
		<?php if($ajaxadmin){ ?>
			<script type="text/javascript">
			$(window).bind('beforeunload', function(){
			return 'Note: Refreshing the page in the middle of a session may cause some technical problem and lose of data.';
			});
			</script>
		<?php } ?>
		<link rel="icon" href="<?php echo hycus::getroot(); ?>templates/hycus_template/favicon.ico" type="image/x-icon" />
		</head>
		<body>
		<?php
		/*Setting the after logout redirect URL starts*/
		if($_SESSION["afterlogout"]){
			unset($_SESSION["afterlogout"]);
		}
		$_SESSION["afterlogout"] = hycus::getroot();
		/*Setting the after logout redirect URL ends*/
		?>
		<div id="ajax-loader"></div>
		<div id="admin-wrap">
			<div id="admin-header">
				<table width="100%" ><tr><td valign='top'>
				<div id="admin-title"><a href="<?php echo hycus::getroot()."?admin";?>"><?php echo $sitename; ?> - Administrator</a></div>
				</td><td valign='top'>
				<div id="moduleselector" style='width:120px;'>
					<a href="<?php echo hycus::getroot(); ?>" target="_blank">Preview</a>&nbsp;&nbsp;
					<span>|</span>
					<span style='float:right;'><form style='margin:0px;'action="<?php echo huri::makeuri("module=user&task=logout"); ?>" method="post"><input class="logoutsubmit" type="submit" value="logout"></form></span>
				</div>
				</td></tr></table>
			</div>
			<div id="adminmainmenu">
				<?php
					hycus::adminlink("admintopmenuconfig", "?adminmodule=config", "modulewrapper", "Configuration");
					hycus::adminlink("admintopmenucontent", "?adminmodule=content", "modulewrapper", "Content Manager");
					hycus::adminlink("admintopmenumenus", "?adminmodule=menus", "modulewrapper", "Menu Manager");
					hycus::adminlink("admintopmenublocks", "?adminmodule=blocks", "modulewrapper", "Block Manager");
					hycus::adminlink("admintopmenuusers", "?adminmodule=user", "modulewrapper", "User Manager");
					hycus::adminlink("admintopmenutemplate", "?adminmodule=templates", "modulewrapper", "Templates");
				?>
			</div>
			<table width='100%' id='adminbody'><tr>
			<td width='auto' valign='top'id="modulelisttd">
				<div id="modulelist">
					<?php hycusLoader::loadAdminModules(); ?>
				</div>
			</td>
			<td valign='top' width='100%' style='border:thin solid #B3B3B3;'>
				<div id="modulewrap">
					<div id="modulewrapper">
						<?php
							if(!$ajaxadmin)
							{
								hycusLoader::loadAdminModule(hycus::getcleanvar("adminmodule"));
							}
							if(!hycus::getcleanvar("adminmodule")){ adminintro(); }
						?>
					</div>
				</div>
			</td>
			</tr></table>
		<br/>
		</div>
		<div style="width:370px;margin:5px auto;font-size:11px;color:#828282;"><a href="http://www.hycus.com/" style="color:#828282;" target="_blank">Hycus</a> is Free PHP Based CMS released under the GNU/GPL License. </div>
		<?php
		if($ajaxadmin)
		{
			?>
			<script type="text/javascript">
				$("div#ajax-loader").show();
				var tothashvalue = window.location.hash;
				if(tothashvalue){
					var tothashvalue = tothashvalue.substr(2);
					var tothashvalue = tothashvalue.split("&");
					var adminlink = "?";
					for (i=0;i<tothashvalue.length;i++)
					{
						if(i==0)
						{
							adminlink = adminlink + tothashvalue[i];
						}
						else{
							adminlink = adminlink + "&" +tothashvalue[i];
						}
					}
					var destination = "#modulewrapper";
					$.ajax({
						url: adminlink,
						success: function(html){
								$("div#ajax-loader").show();
								$(destination).empty();
								$(destination).prepend(html);
								$(destination).fadeIn("slow");
								$("div#ajax-loader").hide();
							}
					});
				}
				else{
					$("div#ajax-loader").hide();
				}
			</script>
			<?php
		} ?>
		<?php if($ajaxadmin){ hycusLoader::loadMessage(); } ?>
		</body>
		</html>

	<?php
	}
}
function adminintro(){
	$currentversion = getcurrenthycuscmsversion();
	$latestversion = getlatesthycuscmsversion();
	if(($currentversion < $latestversion) && $latestversion)
	{
		echo "<div style='padding:10px;text-align:center;background:#FD686A;font-weight:bold;'>Hycus CMS $latestversion is available now. We reccommend you to upgrade soon. <a href='http://www.hycus.com/' target='_blank'>Download</a></div>";
	}
	else{
		echo "<div style='padding:10px;text-align:center;background:#BCFDB4;font-weight:bold;'>You are using the latest version of Hycus cms.</div>";
	}
	hycusLoader::loadAdminModule("statistics");
	hycusdonationlink();
}
function getcurrenthycuscmsversion()
{
	return "1.0.3";
}
function getlatesthycuscmsversion()
{
	$result = file_get_contents('http://www.hycus.com/others/hycuscmsversion.php');
	if($result)
	return $result ;
}
function hycusdonationlink(){
	echo "<div style='text-align:right;'>We need your help. <a href='http://www.hycus.com/' target='_blank'>Help us</a>.</div>";
}
?>