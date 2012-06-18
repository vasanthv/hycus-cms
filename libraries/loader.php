<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );
/*Main loading function
 * which loads all the blocks, modules and template*/
require_once 'class.inputfilter_clean.php';
require_once 'convert_timezone.php';
require_once 'database.php';
require_once 'functions.php';
require_once 'hTemplate.class.php';
require_once 'huri.php';
require_once 'hycus_Auth.php';
require_once 'hycus_mailer.php';
require_once 'hycusparams.php';
require_once 'session.php';

class hycusLoader {
	function hycusLoader()
	{

		//Loading the global configurations
		$db = new hdatabase();
		$configs = $db->get_rec("#__config", "*");
		$GLOBALS['sitename']= $configs->sitename;
		$GLOBALS['globalmetakeys']= $configs->metakeywords;
		$GLOBALS['globalmetadesc']= $configs->metadesc;
		$GLOBALS['adminemail']= $configs->adminemail;
		$GLOBALS['template']= $configs->template;
		$GLOBALS['defaultlang']= $configs->language;
		$GLOBALS['sessionlimit']= $configs->sessionlimit;
		$GLOBALS['timezone']= $configs->timezone;
		$GLOBALS['timedisplayformat']= $configs->timedisplayformat;
		$GLOBALS['paginationlimit']= $configs->paginationlimit;
		$GLOBALS['siteurl']= $configs->siteurl;
		$GLOBALS['disperror']= $configs->disperror;
		$GLOBALS['ajaxadmin']= $configs->ajaxadmin;

		$hycusSession = new hycusSession();

	}
	function loadTemplate($template)
	{
		global $disperror;
		if(!$disperror)
		{
			error_reporting(0);
		}
		$templatepath = "./templates/".$template;
		hTemplate::setBaseDir($templatepath);

		//Parse URL
		huri::parseuri();


		//if there is no menuid, assigning the menuid to be the menuid of the default homepage.
		//This avoid collapsing of pages.
		$db = new hdatabase();
		if(hycus::getcleanvar("menuid")){}
		else{
			$homemenuid = $db->get_rec("#__menuitems", "id" , "defaultmenu='1'");
			if(!hycus::getcleanvar("module"))
			$_REQUEST["menuid"]=$homemenuid->id;
		}

		/* These are the template variables which can be used directly in the template file.
		 * But we haven't used this feature in this cms project. You can make use of it, if it is necessary for your project.
		 * This sample variable can be accessed in the template as "echo $sample" directly.
		*/
		$response = hycus::getcleanvar("response");
		if($response == "module" || $response == "ajax"){
			hycusLoader::loadModule();
		}else{
			$templatedata = $db->get_rec("#__templates", "data" , "templatename='$template'");
			$templatevars = array ('templatedata'=> $templatedata->data);

			$html = hTemplate::loadTemplate('template', $templatevars);
			echo $html;
		}
	}
	function loadHead()
	{
		//loads the header file
		$header_path = "modules/header/header.php";
		include_once $header_path;
	}
	function loadAdminHead()
	{
		//loads the header file for admin
		$header_path = "modules/header/admin.php";
		include_once $header_path;
	}
	function loadAdminModules()
	{
		//loads the list of admin modules in the admin side
		$usertypeid = hycus::getusertype(hycus::getthisuserid());
		$db = new hdatabase();
		$adminaccess = $db->get_rec("#__usertypes", "adminaccess", "id='$usertypeid'");

		if($adminaccess->adminaccess){
			$dir = "modules/";
			echo "<div id='modulelisttitle'>Select Module</div>";

			echo "<ul name='adminmodule' id='adminmodule' >";
			// Open the Module directory, and proceed to read its contents
			if (is_dir($dir)) {
				if ($dh = opendir($dir)) {
					while (($file = readdir($dh)) !== false) {
						if(filetype($dir . $file)=="dir" && $file != "." && $file != "..")
						{
							if(is_file("modules/$file/admin.$file.php")){
							echo "<li>";
								hycus::adminlink("adminmodulelist_$file", "?adminmodule=$file", "modulewrapper", "$file");
							echo "</li>";
							}
						}
					}
					closedir($dh);
				}
			}
			echo "</ul>";
		}
	}
	function loadAdminModule($module)
	{
		//loads the admin module based on the request.
		$usertypeid = hycus::getusertype(hycus::getthisuserid());
		$db = new hdatabase();
		$adminaccess = $db->get_rec("#__usertypes", "adminaccess, moduleaccess", "id='$usertypeid'");

		if($adminaccess->moduleaccess){
			$pieces = explode(";", $adminaccess->moduleaccess);
			foreach($pieces AS $piece)
			{
				$singledata = explode(":", $piece);

				$moduleacc[$singledata[0]] = $singledata[1];
			}
		}

		if($adminaccess->adminaccess && $moduleacc[$module]){
			$adminmodule = "modules/$module/admin.".$module.".php";
			include $adminmodule;
		}
		elseif(!$module){}
		else{
			echo "You don't have enough permission to access this part of the administrator.";
		}
	}

	function loadModule($module_to_beloaded=null)
	{
		//loads the user module based on the request else default link is loaded.
		$db = new hdatabase();

		if($module_to_beloaded){
			$module = $module_to_beloaded;
		}
		else{
			if(hycus::getcleanvar("module")){
				$module = hycus::getcleanvar("module");
			}
			else{
				$homepageobj = $db->get_rec("#__menuitems", "itemlink", "defaultmenu ='1'" );
				$queryurl = parse_url($homepageobj->itemlink);
				$separatevalues = explode("&", $queryurl['query']);

				foreach($separatevalues AS $separatevalue)
				{
					$getvalues = explode("=", $separatevalue);
					if($getvalues[0]=="module")
					{
						$module=$getvalues[1];
					}
					else{
						$_REQUEST[$getvalues[0]]=$getvalues[1];
					}
				}
			}
		}

		if($module){

			//To show the page title if enabled
			if(hycus::getcleanvar("menuid") && !$module_to_beloaded && (hycus::getcleanvar("response") != "module" && hycus::getcleanvar("response") != "ajax"))
			{
				$menuobj = $db->get_rec("#__menuitems", "itemlink, showtitle, defaultmenu", "id ='".hycus::getcleanvar("menuid")."'" );
				if($menuobj->defaultmenu)
				$menulinkitem = hycus::getroot();
				else
				$menulinkitem = huri::makeuri($menuobj->itemlink."&menuid=".hycus::getcleanvar("menuid"));

				if($menuobj->showtitle && hycus::getcurrenturl()==$menulinkitem){
					echo "<h3>".getpagetitle()."</h3>";
				}
			}

			//Calling module's language file
			global $defaultlang;

			if(is_file("modules/language/$defaultlang/module_".$module.".php")){
				echo "<div style='height:0px;'>";
					include_once "modules/language/$defaultlang/module_".$module.".php";
				echo "</div>";

			}
			elseif(is_file("modules/language/en/module_".$module.".php")){
				echo "<div style='height:0px;'>";
					include_once "modules/language/en/module_".$module.".php";
				echo "</div>";
			}

			$module_path =  "modules/".$module."/".$module.".php";
			include_once $module_path;
		}
	}
	function loadBlocks($position)
	{
		//loads the block enabled in a particular position
		if(hycus::getthisuserid())
			$where .= "(blockperms='0' OR blockperms='1') ";
		else
			$where .= "(blockperms='0' OR blockperms='2') ";

		$db = new hdatabase();
		$recs = $db->get_recs("#__blocks", "id", "position = '$position' AND enabled = '1' AND ($where)", "ordering");
		if($recs){
			foreach($recs AS $rec){
				$mods = $db->get_rec("#__blocks", "block_name, menuids, blockclass, title, showtitle, data", "id = '$rec->id'");

				$showblock=false;
				if($mods->menuids == "all")
				{ $showblock=true; }
				elseif(!$mods->menuids)
				{}
				else{
					foreach(explode(";",$mods->menuids) AS $blockmenuid)
					{
						if($blockmenuid == hycus::getcleanvar("menuid")){$showblock=true;}
					}
				}

				if($showblock==true){
					//Calling block's language file
					global $defaultlang;
					echo "<div style='height:0px;'>";
					if(is_file("modules/language/$defaultlang/block_".$mods->block_name.".php")){
						include_once "modules/language/$defaultlang/block_".$mods->block_name.".php";
					}
					elseif(is_file("modules/language/en/block_".$mods->block_name.".php")){
						include_once "modules/language/en/block_".$mods->block_name.".php";
					}
					echo "</div>";


					$block_path =  "blocks/".$mods->block_name."/".$mods->block_name.".php";
					include_once $block_path;
					$class = "hycusBlock_".$mods->block_name;
					$newmod = new $class;

					echo "<div class='";if($mods->blockclass){ echo $mods->blockclass; }else{ echo "hycusblock"; } echo "'>";
					if($mods->showtitle){ echo "<h3>$mods->title</h3>"; }
					$newmod->loadthisblock($mods->id,$mods->data);
					echo "</div>";
				}
			}
		}
	}
	function checkBlocks($position)
	{
		//checks and returns whether a block is enabled in a particula position.
		if(hycus::getthisuserid())
			$where .= "(blockperms='0' OR blockperms='1') ";
		else
			$where .= "(blockperms='0' OR blockperms='2') ";

		$return = 0;
		$db = new hdatabase();
		$recs = $db->get_recs("#__blocks", "id,title,showtitle,data", "position = '$position' AND enabled = '1' AND ($where)", "ordering");
		if($recs){
			foreach($recs AS $rec){
					$return = 1;
			}
		}
		return $return;
	}
	function loadMessage()
	{
		$alertmessage = hycus::getcleanvar('hycusmessage', "cookie");
		//loads a hycus alert message :)
		if(!empty($alertmessage))
		{
			echo '<div id="alert">' . $alertmessage . '</div>';
			setcookie("hycusmessage", "", time()-60);
		}
		?>
		<script type="text/javascript">
		$(function () {
			var $alert = $('#alert');
			if($alert.length)
			{
				var alerttimer = window.setTimeout(function () {
					$alert.trigger('click');
				}, 3000);
				$alert.animate({height: $alert.css('line-height') || '50px'}, 200)
				.click(function () {
					window.clearTimeout(alerttimer);
					$alert.animate({height: '0'}, 200);
				});
			}
		});
		</script>
		<?php
	}
	function loadsnippet($name)
	{
		//loads the snippet when called.
		$db = new hdatabase();
		$snippets = $db->get_rec("#__snippets", "code", "title='$name' AND enabled = '1'");
		if($snippets)
		return str_replace('\"', '"', $snippets->code);
	}
	function hycusloadview($module){
		//loads the view part of the hycus modules.
		$view_path = "modules/".$module."/".$module.".tpl.php";
		if($view_path)
		include_once $view_path;
	}
	function saveconfigfile($hycus_db_type, $hycus_db_host, $hycus_db_user, $hycus_db_pwd, $hycus_db_name, $hycus_db_tbprefix)
	{
		//creates and writes on the configuration file.
		//creates database if not present
		if($hycus_db_type == "MySQL"){
			$dblink = mysql_connect($hycus_db_host, $hycus_db_user, $hycus_db_pwd);
			mysql_query("CREATE DATABASE IF NOT EXISTS `$hycus_db_name`  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci",$dblink);
		}
		elseif($hycus_db_type == "PostgreSQL"){
			$dblink = pg_connect("host={$hycus_db_host} dbname={$hycus_db_name} user={$hycus_db_user} password={$hycus_db_pwd}");
		}


		if($dblink){

			$tobewritten = '<?php
			defined( "HYCUSPAGEPROTECT" ) or die( "You don\'t have permission to view this page." );

			$dbhost = "'.$hycus_db_host.'";
			$dbuser = "'.$hycus_db_user.'";
			$dbpass = "'.$hycus_db_pwd.'";
			$dbname = "'.$hycus_db_name.'";
			$dbtype = "'.$hycus_db_type.'";
			$dbprefix = "'.$hycus_db_tbprefix.'";
			?>';

			$ourFileName = "../sites/default/config.php";
			$ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
			fwrite($ourFileHandle, $tobewritten);

			fclose($ourFileHandle);
			chmod($ourFileName, 0444);
			return "success";

		}
		else{
			return "error";
		}
	}
	function loadmoduleconfig($module){
		//loads the conifugation data of the module in the $module variable
		$db = new hdatabase();
		$data = $db->get_rec("#__modules", "data", "module='$module'");
		if($data)
		return $data->data;
	}

}