<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

class hycusUserView{

	function login()
	{
		hycus::formvalidator("logform");
		?>
		<form action="<?php echo huri::makeuri("?module=user&task=hlogin&menuid=".hycus::getcleanvar("menuid")); ?>" method="post" name="logForm" id="logform">
			<div class="hycusforms">
		        <div><h4><strong><?php echo login_form; ?></strong></h4></div>
		        <label><?php echo usernamenemail; ?></label>
				<div><input name="usr_email" type="text" class="required textbox" size="25"></div>
				<label><?php echo password; ?></label>
				<div><input name="pwd" type="password" class="required password textbox" size="25"></div>
				<div><input name="remember" type="checkbox" id="remember" value="1">&nbsp;<?php echo rememberme; ?></div>
				<div>
					<div>
						<input class="button" type="submit" value="<?php echo login; ?>">
					</div>
					<div>
						<a href="<?php echo huri::makeuri("?module=user&task=register&menuid=".hycus::getcleanvar("menuid")); ?>"><?php echo register; ?></a> |
						<a href="<?php echo huri::makeuri("?module=user&task=forgot&menuid=".hycus::getcleanvar("menuid")); ?>"><?php echo forgotpass; ?></a>
					</div>
				</div>
	        </div>
      	</form>

		<?php
	}
	function register()
	{
		hycus::formvalidator("regform");
	?>
		<form action="<?php echo huri::makeuri("?module=user&task=hregister&menuid=".hycus::getcleanvar("menuid")); ?>" method="post" name="regForm" id="regform">
			<div class="hycusforms">
				<div><h4><strong><?php echo registration;?></strong></h4></div>

				<div><?php echo allfieldsreq; ?></div>

				<label><?php echo name; ?></label>
				<div><input name="full_name" type="text" id="full_name" class="required textbox" value="<?php echo $_SESSION['full_name'];unset($_SESSION['full_name']); ?>"></div>

				<label><?php echo username; ?></label>
				<div><input name="user_name" type="text" id="user_name" class="required username textbox" minlength="5" value="<?php echo $_SESSION['user_name'];unset($_SESSION['user_name']); ?>"></div>

				<label><?php echo email; ?></label>
				<div><input name="usr_email" type="text" id="usr_email" class="required email textbox"  value="<?php echo $_SESSION['usr_email'];unset($_SESSION['usr_email']); ?>"></div>

				<label><?php echo password; ?></label>
				<div><input name="pwd" type="password" class="required password textbox" minlength="5" id="pwd"></div>

				<label><?php echo repassword; ?></label>
				<div><input name="pwd2"  id="pwd2" class="required password textbox" type="password" minlength="5" equalto="#pwd"></div>

				<?php
					$data = hycusLoader::loadmoduleconfig("user");
					if(hycus::gethycusdata($data, "registrationcaptcha")) { ?>
					<div><?php hycus::captcha("regcaptcha", "required textbox"); ?></div>
				<?php } ?>

				<?php if(hycus::gethycusdata($data, "registrationtos")) { ?>
				<div><input name="regtos" id="tos" class="required" type="checkbox" />&nbsp;<?php echo iagree; ?></div>
				<?php } ?>

				<div>
					<input class="button" type="submit" value="<?php echo register;?>">
				</div>

			</div>
		</form>
	<?php
	}
	function forgot()
	{
		hycus::formvalidator("forgotpassform");
		?>
		<form action="<?php echo huri::makeuri("?module=user&task=forgotpass&menuid=".hycus::getcleanvar("menuid")); ?>" method="post" name="forgotpassForm" id="forgotpassform">
			<div class="hycusforms">
				<div><h4><strong><?php echo forgotpass; ?></strong></h4></div>

				<div><?php echo forgotpassintro; ?></div><br/>

				<label><?php echo usernameremail; ?></label>
				<div><input name="useremail" type="text" id="full_name" class="required textbox"></div>

				<div>
					<input class="button" type="submit" value="<?php echo submitreq; ?>">
				</div>
			</div>
		</form>
	<?php
	}
	function resetpass()
	{
		hycus::setpagetitle("Reset Password");

		$c = hycus::getcleanvar("c");
		$uid = hycus::getcleanvar("uid");
		$db=new hdatabase();
		$auid = $db->get_rec("#__users", "id", "auth_token = '".$c."'");
		if($uid==$auid->id){
		hycus::formvalidator("resetpassform");
		?>
		<form action="<?php echo huri::makeuri("?module=user&task=authresetpass&menuid=".hycus::getcleanvar("menuid")); ?>" method="post" name="resetpassForm" id="resetpassform">
			<div class="hycusforms">
				<div><h4><strong><?php echo resetpass; ?></strong></h4></div>

				<div><?php echo enternewpass; ?>.</div><br/>

				<label><?php echo newpass; ?></label>
				<div><input name="pwd" type="password" class="required password textbox" minlength="5" id="pwd"></div>

				<label><?php echo renewpass; ?></label>
				<div><input name="pwd2"  id="pwd2" class="required password textbox" type="password" minlength="5" equalto="#pwd"></div>

				<div>
					<input type="hidden" name="authtoken" value="<?php echo $c; ?>">
					<input class="button" type="submit" value="<?php echo resetpass; ?>">
				</div>
			</div>
		</form>
		<?php
		}
		else{ echo resetnotauthorized; }
	}
	function editprofile()
	{
		?>
		<h3><?php echo editprofile; ?></h3>
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
	<br/>
	<ul class="tabs">
	    <li><a href="#tab1"><?php echo general; ?></a></li>
	    <li><a href="#tab2"><?php echo password; ?></a></li>
	    <li><a href="#tab3"><?php echo avatar; ?></a></li>
	</ul>

	<div class="tab_container">
	    <div id="tab1" class="tab_content">
			<form action="<?php echo huri::makeuri("?module=user&task=update&menuid=".hycus::getcleanvar("menuid")); ?>" method="post" name="regForm" class="validateform">
				<div class="hycusforms">

					<label><?php echo name; ?></label>
					<div><input name="full_name" type="text" id="full_name" class="required textbox" value="<?php echo hycus::getuserfullname(hycus::getthisuserid()); ?>"></div>

					<label><?php echo username; ?></label>
					<div><input name="user_name" type="text" id="user_name" class="required username textbox" minlength="5" value="<?php echo hycus::getusername(hycus::getthisuserid()); ?>"></div>

					<label><?php echo email; ?></label>
					<div><input name="usr_email" type="text" id="usr_email" class="required email textbox" value="<?php echo hycus::getuseremail(hycus::getthisuserid()); ?>"></div>

					<div>
						<input name="curent_uid" type="hidden" value="<?php echo hycus::getthisuserid(); ?>">
						<input class="button" type="submit" value="<?php echo update; ?>">
					</div>
				</div>
			</form>
	    </div>
	    <div id="tab2" class="tab_content">
	    	<script>
	    	$(document).ready(function(){
				$(".passwordform").validate();
			});
	    	</script>
			<form action="<?php echo huri::makeuri("?module=user&task=changepass&menuid=".hycus::getcleanvar("menuid")); ?>" method="post" name="regForm" class="passwordform">
				<div class="hycusforms">

					<label><?php echo oldpass; ?></label>
					<div><input name="oldpwd" type="password" class="required password textbox" minlength="5"></div>

					<label><?php echo newpass; ?></label>
					<div><input name="pwd" type="password" class="required password textbox" minlength="5" id="pwd"></div>

					<label><?php echo renewpass; ?></label>
					<div><input name="pwd2"  id="pwd2" class="required password textbox" type="password" minlength="5" equalto="#pwd"></div>

					<div>
						<input name="curent_uid" type="hidden" value="<?php echo hycus::getthisuserid(); ?>">
						<input class="button" type="submit" value="<?php echo changepass; ?>">
					</div>
				</div>
			</form>
	    </div>
	    <div id="tab3" class="tab_content">
			<form method="post" action="<?php echo huri::makeuri("?module=user&task=uploadavatar&menuid=".hycus::getcleanvar("menuid")); ?>" enctype="multipart/form-data" name="form1">
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td><div align="right" class="titles"><?php echo picture; ?>: </div></td>
						<td width="350" align="left">
							<div align="left">
								<input size="25" name="file" type="file" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:10pt" class="box"/>
							</div>
						</td>
					</tr>
					<tr>
						<td></td>
						<td valign="top" height="35px" class="help"><?php echo imagesize; ?></span></td>
					</tr>
					<tr>
						<td></td>
						<td valign="top" height="35px"><input type="submit" id="mybut" value="<?php echo upload; ?>" name="Submit"/></td>
					</tr>
   				</table>
			</form>
	    </div>
	</div>
		<?php
	}

}

?>