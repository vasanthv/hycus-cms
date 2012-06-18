<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

 defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

 class hycusBlock_login {
	function loadthisblock($id,$data) {
		$showavatar = hycus::gethycusdata($data,"showavatar");
		$showedit = hycus::gethycusdata($data,"showedit");
		$showblog = hycus::gethycusdata($data,"showblog");
		$loggeduser =  hycus::getthisuserid();

		echo "<div>";

			if($loggeduser){
				/*Setting the after logout redirect URL starts*/
				if($_SESSION["afterlogout"]){
					unset($_SESSION["afterlogout"]);
				}
				$_SESSION["afterlogout"] = hycus::getcurrenturl();
				/*Setting the after logout redirect URL ends*/

				?>
				<form action="<?php echo huri::makeuri("module=user&task=logout&menuid=".hycus::getcleanvar("menuid")); ?>" method="post" name="logoutForm" id="logoutForm">
					<table align="center" style="width:auto;margin-left:auto;margin-right:auto;">
						<tr><td align="center" <?php if($showedit && $showblog){ echo "colspan ='2'"; } ?>><?php echo welcome." ".hycus::getusername().","; ?></td></tr>
						<?php if($showavatar){ ?>
							<tr><td align="center" <?php if($showedit && $showblog){ echo "colspan ='2'"; } ?>><a href="<?php echo huri::makeuri("?module=user&task=profile&menuid=".hycus::getcleanvar("menuid")); ?>"><?php echo hycus::getavatarthumb(); ?></a></td></tr>
						<?php } ?>
						<?php if($showedit || $showblog){ ?>
							<tr>
								<?php if($showedit){ ?>
								<td align="center"><a href="<?php echo huri::makeuri("?module=user&task=editprofile&menuid=".hycus::getcleanvar("menuid")); ?>" ><?php echo edit_profile ?></a>
								<?php if($showedit && $showblog){ echo " | "; } ?>
								</td>
								<?php } ?>
								<?php if($showblog){ ?>
								<td align="center"><a href="<?php echo huri::makeuri("?module=content&task=addblog&menuid=".hycus::getcleanvar("menuid")); ?>" ><?php echo add_blog ?></a></td>
								<?php } ?>
							</tr>
						<?php } ?>
						<tr>
							<td align="center" <?php if($showedit && $showblog){ echo "colspan ='2'"; } ?>>
								<style>
									.logoutsubmit{color:red;border:none;background:none;cursor:pointer;}
									.logoutsubmit:hover {text-decoration:underline;}
								</style>
								<input type="hidden" name="redirecturl" value="<?php echo hycus::getcurrenturl(); ?>">
								<input class="logoutsubmit" type="submit" value="<?php echo logout; ?>">
							</td>
						</tr>
					</table>
		      	</form>
				<?php
			}
			else{
				?>
				<?php
					/*Setting the after login redirect URL starts*/
					if($_SESSION["afterlogin"]){
						unset($_SESSION["afterlogin"]);
					}
					$_SESSION["afterlogin"] = hycus::getcurrenturl();
					/*Setting the after login redirect URL ends*/
				?>

				<form action="<?php echo huri::makeuri("?module=user&task=hlogin&menuid=".hycus::getcleanvar("menuid")); ?>" method="post" name="loginForm" id="loginform">
					<div class="hycusforms">
				        <label><?php echo username ?></label>
						<div><input name="usr_email" type="text" class="required" size="20" /></div>
						<label><?php echo password ?></label>
						<div><input name="pwd" type="password" class="required password" size="20" /></div>
						<div><input name="remember" type="checkbox" id="remember" value="1" />&nbsp;<?php echo rememberme ?></div>
						<div>
							<div>
								<input class="button" type="submit" value="<?php echo login ?>" />
							</div>
							<div>
								<a href="<?php echo huri::makeuri("?module=user&task=register&menuid=".hycus::getcleanvar("menuid")); ?>"><?php echo register ?></a> |
								<a href="<?php echo huri::makeuri("?module=user&task=forgot&menuid=".hycus::getcleanvar("menuid")); ?>"><?php echo forgotpass ?></a>
							</div>
						</div>
			        </div>
		      	</form>
				<?php
			}

		echo "</div>";

	}
 }

?>
