<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

 defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

 class hycusBlock_search {
	function loadthisblock($id,$data) {
		$searchtext = hycus::gethycusdata($data,"searchtext");
		$searchbuttontext = hycus::gethycusdata($data,"searchbuttontext");
		$showbutton = hycus::gethycusdata($data,"showbutton");

		?>
		<div>
			<form action="<?php echo huri::makeuri("?module=search&menuid=".hycus::getcleanvar('menuid'));?>" method="post" name='searchbox'>

				<span><input type="text" name="q" value="<?php if(hycus::getcleanvar("q")){echo hycus::getcleanvar("q");} else{ echo "$searchtext";} ?>" <?php if(!hycus::getcleanvar("q")){ ?> onfocus="javascript:document.searchbox.q.value=''" onblur="javascript:document.searchbox.q.value='<?php echo "$searchtext"; ?>'" <?php } ?>/></span>
				<?php if($showbutton){ ?>
				<span>
					<input type="submit" value="<?php echo $searchbuttontext; ?>" />
				</span>
				<?php } ?>
			</form>
		</div><?php
	}
 }

?>