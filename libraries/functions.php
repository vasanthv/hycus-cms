<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

class hycus{

	function getroot(){
		//returns the root path of this website.
		global $siteurl;
		return $siteurl;
	}
	function getbase(){
		//returns the actual path of the website.
		return dirname(__FILE__);
	}
	function getcurrenturl(){
		//returns the complete current url.
		//note: window hash value will not be returned.
		global $siteurl;
		$return = $siteurl;
		$currentsefurl =  str_ireplace(str_ireplace("http://".$_SERVER['HTTP_HOST'], "", hycus::getroot()), "", $_SERVER['REQUEST_URI']);

		return $return.$currentsefurl;
	}
	function isvalidemail($email) {
		//returns whether an email id is valid one or not.
	    return preg_match('|^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]{2,})+$|i', $email);
	}
	function getcleanvar($name,$type=null)
	{
		//returned cleaned value of POST or Get or SESSION or COOKIE variables
		switch($type){
			case "session":
			$string=$_SESSION[$name];
			break;
			case "cookie":
			$string=$_COOKIE[$name];
			break;
			case "post":
			$string=$_POST[$name];
			break;
			case "get":
			$string=$_GET[$name];
			break;
			default:
			$string=$_REQUEST[$name];
			break;
		}

		return hycus::cleanstring($string);
	}
	function setpagetitle($title){
		//Title set by this method will not be scrolled by search engine.
		?>
		<script type="text/javascript">
		$(document).ready(function() {
			document.title = '<?php echo $title; ?>';
		});
		</script>

		<?php
	}
	function cleanstring($string)
	{
		// returns a cleaned string
		$tags = "";$attr="";$tag_method="";$attr_method="";$xss_auto="";
		$myFilter = new InputFilter($tags, $attr, $tag_method, $attr_method, $xss_auto);
		$result = $myFilter->process($string);
		return $result;
	}
	function getcleaneditorvar($name)
	{
		//returns the cleaned editor variable
		$string=$_REQUEST[$name];
		$tags = array("b", "i", "u", "a", "font", "p", "img", "div", "span", "table", "tr", "th", "tbody", "tfoot", "td", "strike", "strong", "br", "sub", "sup", "blockquote", "ol", "ul", "li", "hr", "h1", "h2", "h3");$attr=array("href", "src", "style", "colspan", "rowspan", "align", "size", "width");$tag_method="";$attr_method="";$xss_auto="";
		$myFilter = new InputFilter($tags, $attr, $tag_method, $attr_method, $xss_auto);
		$result = $myFilter->process($string);
		return $result;
	}
	function getadmineditorvar($name)
	{
		//returns the cleaned admin editor variable
		//Note: this allows almost all the tags and attributes. you can add your attributes to it.
		$string=$_REQUEST[$name];
		$tags = array("a", "abbr", "address", "area", "article", "aside", "audio", "b", "base", "bdo", "blockquote", "br", "button", "canvas", "caption", "cite", "code", "col", "colgroup", "command", "datagrid", "datalist", "dd", "del", "details", "dfn", "dialog", "div", "dl", "dt", "em", "embed", "fieldset", "figure", "footer", "form", "h1", "h2", "h3", "h4", "h5", "h6", "header", "hgroup", "hr", "i", "iframe", "img", "input", "ins", "kbd", "keygen", "label", "legend", "li", "link", "map", "mark", "menu", "meta", "meter", "noscript", "object", "ol", "optgroup", "option", "output", "p", "param", "pre", "progress", "g", "s", "samp", "script", "section", "select", "small", "source", "span", "strong", "style", "sub", "table", "tbody", "td", "textarea", "tfoot", "th", "thead", "tr", "ul", "var", "video");
		$attr = array("abbr", "accept-charset", "accept", "accesskey", "action", "align", "alink", "alt", "archive", "axis", "background", "bgcolor", "border", "cellpadding", "cellspacing", "char", "charoff", "charset", "checked", "cite", "class", "classid", "clear", "code", "codebase", "codetype", "color", "cols", "colspan", "compact", "content", "coords", "data", "datetime", "declare", "defer", "dir", "disabled", "enctype", "face", "for", "frame", "frameborder", "headers", "height", "href", "hreflang", "hspace", "id", "ismap", "label", "lang", "language", "link", "longdesc", "marginheight", "marginwidth", "maxlength", "media", "method", "multiple", "name", "nohref", "noresize", "noshade", "nowrap", "object", "onblur", "onchange", "onclick", "ondblclick", "onfocus", "onkeydown", "onkeypress", "onkeyup", "onload", "onmousedown", "onmousemove", "onmouseout", "onmouseover", "onmouseup", "onreset", "onselect", "onsubmit", "onunload", "profile", "prompt", "readonly", "rel", "rev", "rows", "rowspan", "rules", "scheme", "scope", "scrolling", "selected", "shape", "size", "span", "src", "standby", "start", "style", "summary", "tabindex", "target", "text", "title", "type", "usemap", "valign", "value", "valuetype", "version", "vlink", "vspace", "width");
		$tag_method="";
		$attr_method="";
		$xss_auto="";
		$myFilter = new InputFilter($tags, $attr, $tag_method, $attr_method, $xss_auto);
		$result = $myFilter->process($string);
		return $result;
	}
	function captcha($name, $class){
		//returns a captcha image and textbox.
		unset($_SESSION['captcha']);
		echo "<img src='".hycus::getroot()."libraries/captcha.php' id='captcha' /><br/>";
		echo "<a href='#' onclick='document.getElementById(\"captcha\").src=\"".hycus::getroot()."libraries/captcha.php?\"+Math.random();document.getElementById(\"captcha-form\").focus();' id='change-image'>Not readable? Change text.</a><br/>";
		echo "<input type='text' name='".$name."' id='captcha-form' style='width:100px' class='".$class."'/>";

	}
	function redirect($url, $msg=null)
	{
		/* Set the cookie to show the alert in the redirected page*/
		setcookie("hycusmessage", "$msg", time()+100);

		/*Redirect the page the $url parameter.
		 * If the headers are already sent, we use Javascript redirection.
		 * Else we use PHP redirection
		 */
		if (headers_sent()) {
			//Javascript redirection script
			echo "<script>document.location.href='$url';</script>\n";
		} else {
			//PHP redirection script
			header( 'HTTP/1.1 301 Moved Permanently' );
			header( 'Location: ' . $url );
		}
	}
	function gethycusdata($data,$query)
	{
		//returns the value from a datastring which is used to store configurations in hycus
		$pieces = explode(";", $data);
		foreach($pieces AS $piece)
		{
			$fielddata = explode("=", $piece);
			if($fielddata[0] == $query)
			{
				return $fielddata[1];
			}
		}
		return 0;
	}
	function showtime($disptime=null, $format=null){
		//Displays date and time based on the configuration or the $format variable
		global $timezone, $timedisplayformat;

		if(!$format)
		$format = $timedisplayformat;

		$GetTime = new hycusTime();
		$time=$GetTime->ConvertTime(time(),"0",$timezone);
		if($disptime){
			$itemtime=$GetTime->ConvertTime($disptime,"0",$timezone);
		}
		else {
			$itemtime=$time;
		}

		switch($format){
			case 1:
				return date("l dS \of F Y h:i:s A", $itemtime);
				break;
			case 2:
				return date("M d, Y - H:i:s", $itemtime);
				break;
			case 3:
				return date("Y-m-d H:i:s", $itemtime);
				break;
			case 4:
			default;
				$timespan = $time - $itemtime;
				$days = floor($timespan/86400);
				$htTimeFormat = "";
				if($days != 0) {
					if($days == 1)
					{$htTimeFormat = "yesterday"; return $htTimeFormat; }
					else { $htTimeFormat = $days." "."days ago"; return $htTimeFormat; }
				}
				$hours = floor($timespan/3600);
				if($hours != 0) {
					if($hours == 1) { $htTimeFormat = "$hours "."hour ago"; return $htTimeFormat; }
					else { $htTimeFormat = "$hours "."hours ago"; return $htTimeFormat; }
				}
				$minutes = floor($timespan/60);
				$secs = $timespan - ($minutes * 60);
				if($minutes != 0 && $secs != 0) {
					if($minutes != 1 && $secs != 1) { $htTimeFormat = "$minutes "."mins and"." $secs "."secs ago"; return $htTimeFormat; }
					else if($minutes == 1 && $secs != 1) { $htTimeFormat = "$minutes "."min and"." $secs "."secs ago"; return $htTimeFormat; }
					else if($minutes == 1 && $secs == 1) { $htTimeFormat = "$minutes "."min and"." $sec "."secs ago"; return $htTimeFormat; }
				}
				else if($minutes != 0 && $secs == 0) { $htTimeFormat = "$minutes "."minutes ago"; return $htTimeFormat; }
				else if($minutes == 0 && $secs != 0) { $htTimeFormat = "$secs "."secs ago"; return $htTimeFormat; }
				if($htTimeFormat)
				{}
				else{
					$htTimeFormat = "0 secs ago";
				}
				return $htTimeFormat;
				break;
		}

	}

	function getthisuserid(){
		//returns the user id of the current logged in person
		return hycusSession::getthisuserid();
	}
	function getusertype($id=null){
		//returns the usertype based on the $id variable or the current logged in user.
		$db = new hdatabase();
		if($id){
			$current_usertype = $db->get_rec("#__users", "typeid", "id = '".$id."'");
		}
		else{
			$current_usertype = $db->get_rec("#__users", "typeid", "id = '".hycus::getthisuserid()."'");
		}
		return $current_usertype->typeid;
	}
	function getusername($id=null){
		//returns the username based on the $id variable or the current logged in user.
		$db = new hdatabase();
		if($id){
			$current_username = $db->get_rec("#__users", "username", "id = '".$id."'");
		}
		else{
			$current_username = $db->get_rec("#__users", "username", "id = '".hycus::getthisuserid()."'");
		}
		return $current_username->username;
	}
	function getuserfullname($id=null){
		//returns the fullname based on the $id variable or the current logged in user.
		$db = new hdatabase();
		if($id){
			$current_fullname = $db->get_rec("#__users", "name", "id = '".$id."'");
		}
		else{
			$current_fullname = $db->get_rec("#__users", "name", "id = '".hycus::getthisuserid()."'");
		}

		return $current_fullname->name;
	}
	function getuseremail($id=null){
		//returns the email based on the $id variable or the current logged in user.
		$db = new hdatabase();
		if($id){
			$current_user_email = $db->get_rec("#__users", "email", "id = '".$id."'");
		}
		else{
			$current_user_email = $db->get_rec("#__users", "email", "id = '".hycus::getthisuserid()."'");
		}

		return $current_user_email->email;
	}
	function getavatar($id=null){
		//returns the avatar image based on the $id variable or the current logged in user.
		$db = new hdatabase();
		if($id){
			$avatar = $db->get_rec("#__avatar", "avatar", "user_id = '".$id."'");
		}
		else{
			$avatar = $db->get_rec("#__avatar", "avatar", "user_id = '".hycus::getthisuserid()."'");
		}
		if($avatar->avatar)
			return "<img src='".hycus::getroot()."images/avatar/".$avatar->avatar."' alt='".hycus::getusername($id)."' />";
		else
			return "<img src='".hycus::getroot()."images/avatar/no-image.jpg' alt='".hycus::getusername($id)."' />";
	}
	function getavatarthumb($id=null){
		//returns the avatar thumb image based on the $id variable or the current logged in user.
		$db = new hdatabase();
		if($id){
			$avatar = $db->get_rec("#__avatar", "avatar", "user_id = '".$id."'");
		}
		else{
			$avatar = $db->get_rec("#__avatar", "avatar", "user_id = '".hycus::getthisuserid()."'");
		}

		if($avatar->avatar)
			return "<img src='".hycus::getroot()."images/avatar/thumbs/".$avatar->avatar."' alt='".hycus::getusername($id)."' />";
		else
			return "<img src='".hycus::getroot()."images/avatar/thumbs/no-image.jpg' alt='".hycus::getusername($id)."' />";
	}
	function getcontentperms($id=null){
		//returns the content adding permission for a user based on the $id variable or the current logged in user.
		$db = new hdatabase();
		if($id){
			$adminaccess = $db->get_rec("#__usertypes", "contentae", "id='".hycus::getusertype($id)."'");
		}
		else{
			$adminaccess = $db->get_rec("#__usertypes", "contentae", "id='".hycus::getusertype(hycus::getthisuserid())."'");
		}
		return $adminaccess->contentae;
	}

	function iconimage($imagename, $width=null, $height=null ){
		//returns the icon image from the folder images/icons

		$rhtml = "<img src='".hycus::getroot()."images/icons/$imagename' alt='$imagename'";
		if($height){$rhtml .= " height='$height' ";}else{$rhtml .= " height='16' ";}
		if($width){$rhtml .= "width='$width'";} else{$rhtml .= " width='16' "; }
		$rhtml .= " style='border:none;' />";
		return $rhtml;
	}

	function htooltip($id, $text, $tooltip ){
		//gives a tooltip icon and text.
		$html = "<a href='#' id='$id'>";
		if($text){$html.= $text;}else{$html.= "?";}
		$html.="</a>";
		$html.= '<script type="text/javascript">
			$("#'.$id.'").simpletip({ fixed: true, position: "right", offset: [0, -25], content: "<span>'.$tooltip.'</span>" });
		</script>';
		return $html;
	}

	/*Ajax Link Functions Starts*/
	function adminlink($uniqueid, $link, $destinationid=null, $linktitle=null, $anchortitle=null, $confirm=null, $ajaxreq=null)
	{
		global $ajaxadmin;
		if($ajaxadmin || $ajaxreq){
			//ajax links which is used in the admin side. displays the result html in the destination id.
			echo "<a href='#$link' id='$uniqueid' link='$link"."&".hycus::createhash()."=1"."' destinationid='$destinationid' title='$anchortitle'>$linktitle</a>";
			?>
			<script type="text/javascript">
				$("a#<?php echo $uniqueid; ?>").click(function () {
					<?php if($confirm){?> if(confirm("<?php echo $confirm; ?>")) { <?php } ?>
					$("div#ajax-loader").show();
					var adminlink = $('a#<?php echo $uniqueid; ?>').attr("link");
					var destination = "#<?php echo $destinationid; ?>";
					$.ajax({
						url: adminlink,
						success: function(html){
							$(destination).empty();
							$(destination).prepend(html);
							$(destination).fadeIn("slow");
							$("div#ajax-loader").hide();
						}
					});
					<?php if($confirm){?> }	<?php } ?>

				});
			</script>
			<?php
		}else{
			echo "<a href='$link"."&".hycus::createhash()."=1"."' id='$uniqueid'>$linktitle</a> "; ?>
			<?php if($confirm){?>
			<script type="text/javascript">
			$('a#<?php echo $uniqueid; ?>').click(function(event)
			{
				if(confirm("<?php echo $confirm; ?>")) {
					return true;
				}
				else{ return false; }
			});
			</script>
			<?php } ?>
			<?php
		}
	}
	/*Ajax Link Functions Ends*/

	/*Ajax Redirect Starts*/
	function ajax_redirect($link, $destinationid, $msg=null)
	{
		global $ajaxadmin;
		if($ajaxadmin){
			//loads the contents of the $link in $destinationid. $msg will be alerted if present.
			?>
			<script type="text/javascript">
					$("div#ajax-loader").show();
					var adminlink = "<?php echo $link; ?>";
					var destination = "#<?php echo $destinationid; ?>";
					$.ajax({
						url: adminlink,
						success: function(html){
							$(destination).empty();
							$(destination).prepend(html);
							$(destination).fadeIn("slow");
							$("div#ajax-loader").hide();
							<?php if($msg){ ?>
							alert("<?php echo $msg; ?>");
							<?php } ?>
						}
					});

			</script>
			<?php
		}else{
			hycus::redirect($link, $msg);
		}
	}
	/*Ajax Redirect Ends*/

	/*Jquery Date picker Starts*/
	function datepicker($elementid, $format=null)
	{
		if(!$format)
		{
			//assigning default format for datepicker
			$format="dd-mm-yy";
		}
		//Converts a text box into datepicker
		?>
		<link rel="stylesheet" type="text/css" href="<?php echo hycus::getroot(); ?>assets/datepicker/style.css">
		<script type="text/javascript">
			$(function() {
				$("#<?php echo $elementid; ?>").datepicker();
				$('#<?php echo $elementid; ?>').datepicker('option', {dateFormat: '<?php echo $format; ?>'});
			});
		</script>
		<?php
		//how to use
		/*hycus::datepicker("datepicker");
		* <input id="datepicker"/> */

	}
	/*Jquery Date picker Ends*/

	/*Jquery Form Validator Starts*/
	function formvalidator($formid)
	{
		//adds a validation to the form with $formid as id
		?>
		<script type="text/javascript">
			$(document).ready(function(){
				$("#<?php echo $formid; ?>").validate();
			});
		</script>
		<?php
	}
	/*Jquery Form Validator Ends*/

	/*WYSIWYG Editor Starts*/
	function wysiwyg($elementid)
	{
		//shows the textarea element(with $elementid as id) as texteditor
		?>
		<script type="text/javascript" src="<?php echo hycus::getroot(); ?>assets/ckeditor/ckeditor.js"></script>
			<script type="text/javascript">
			//<![CDATA[

				CKEDITOR.replace( '<?php echo $elementid;?>',
					{
						toolbar :
						        [
						        	['Source'],
						            ['Bold','Italic','Underline','Strike','-','Subscript','Superscript', '-', 'NumberedList', 'BulletedList', '-', 'Link','Unlink', '-', 'TextColor'],
						            ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
						            ['Image','Table','HorizontalRule','Smiley']

						        ]

 					});

			//]]>
			</script>
		<?php
	}
	/*WYSIWYG Editor Ends*/

	function admin_form($formid, $destinationid)
	{
		global $ajaxadmin;
		if($ajaxadmin){
			//adds a ajax form submission script to the form
			?>
			<script>
				// prepare the form when the DOM is ready
				$(document).ready(function(){
					$("div#ajax-loader").show();
					$("#<?php echo $formid; ?>").validate({
			            submitHandler: function(form) {
			                jQuery(form).ajaxSubmit({
			                    target: "#<?php echo $destinationid; ?>"
			                });
			            }
			        });
				    $("div#ajax-loader").hide();
				});
			</script>
			<?php
		}else{
			hycus::formvalidator($formid);
		}
	}

	function searchdisp ($str,$maxChar,$searchTerms)
	{
		// string manipulation for the search display.
		$len = count($searchTerms);
		$chunk = '';
		for ($i = 0; $i < $len; $i++)
		{
			if (preg_match("/$searchTems[$i]/",$str))
			{
				$pos = strpos ($str,$searchTerms[$i]);
				if (($pos - ($maxChar/2)) < 0)
				{
					$startPos = 0;
				}
				else
				{
					$startPos = ($pos - ($maxChar/2));
					$chunk .= '...';
				}

				$chunk .= trim(substr($str,$startPos,$maxChar));

				if (($pos + ($maxChar/2)) < strlen($str))
				{
					$chunk .= '...';
				}
				break;
			}
		}
		if ($chunk == '')
		{
			$chunk = substr($str,0,$maxChar).'...';
		}
		$chunk = strip_tags ($chunk);
		$chunk = str_ireplace ($searchTerms,"<b>$searchTerms</b>",$chunk);
		$this->chunk = $chunk;
		return $chunk;
	}
	/**/
	function checkformhash(){
		if(hycus::getcleanvar(md5(hycus::getthisuserid().$_SESSION['hycus_session']))){	return true; }
		else { return false; }
	}
	function addformhash(){
		?><input type="hidden" name="<?php echo hycus::createhash(); ?>" value="1"/><?php
	}
	function createhash(){
		return md5(hycus::getthisuserid().$_SESSION['hycus_session']);
	}
}
?>
