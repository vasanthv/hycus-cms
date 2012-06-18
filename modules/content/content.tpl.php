<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

class hycusContentView{

	function addblog($data){
		if(hycus::getcleanvar("task")=="editblog" && $data->uid != hycus::getthisuserid())
		{echo hycus::redirect(huri::makeuri("?module=content&task=addblog&menuid=".hycus::getcleanvar("menuid")));}

		if(hycus::getcontentperms()){
		hycus::formvalidator("blogForm");
		?>
		<form action="<?php echo huri::makeuri("?module=content&task=haddeditblog&menuid=".hycus::getcleanvar("menuid")); ?>" method="post" name="blogForm" id="blogForm">
			<div class="hycusforms">
				<div><h4><strong><?php if($data){ echo edit_blog." - ".$data->title; }else { echo add_blog; } ?></strong></h4></div>

				<label><?php echo title; ?></label>
				<div><input name="blog_title" type="text" id="blog_title" class="required textbox" value="<?php echo $data->title; ?>"/></div>

				<label><?php echo description; ?></label>
				<div><textarea name="blog_desc" id="blog_desc" class=""><?php echo $data->data; ?></textarea></div>
				<?php hycus::wysiwyg("blog_desc");?>

				<?php
					$db = new hdatabase();
					$cats = $db->get_recs("#__categories", "*", "user_cat='1' AND parentid='0'");
					echo "<select name='blog_cat' id='blog_cat' class='required'>";
					echo "<option value=''>".select_category."</option>";
					foreach($cats AS $cat)
					{
						echo "<option value='".$cat->id."' ";
						if($data->catid == $cat->id){ echo "selected=SELECTED"; }
						echo ">". $cat->title ."</option>";
						hycusContentView::getsubcats($data->catid, $cat->id, 1);
					}
					echo "</select>";
				?>

				<div>
					<input type="checkbox" name="enable_comments" <?php if(!$data->enable_comments){}else{ ?>checked="checked" <?php } ?> value="1"/>&nbsp;<?php echo enable_comments; ?>
				</div>

				<?php if(hycus::getcleanvar("task")=="editblog"){ ?><input name="editblog" type="hidden" value="<?php echo hycus::getcleanvar("id"); ?>"><?php } ?>
				<input name="menuid" type="hidden" value="<?php echo hycus::getcleanvar("menuid"); ?>">
				<div><input class="button" type="submit" value="<?php echo submit; ?>"></div>
			</div>
		</form>
		<?php
		}
		else
		{
			echo restrict_add_content;
		}
	}
	function blog($data){
		?>
		<div class="blogs">
			<?php if($data->showtitle == "yes"){ ?>
				<h4><?php echo $data->title; ?>&nbsp;<?php if($data->uid==hycus::getthisuserid()){ ?><a href="<?php echo huri::makeuri("?module=content&task=editblog&id=".$data->id."&menuid=".hycus::getcleanvar("menuid")); ?>" title="<?php echo $blog->title; ?>"><img src="<?php echo hycus::getroot();?>/images/icon_edit.gif" /></a><?php } ?></h4>
			<?php } ?>
			<div class="blog_details">
				<div><?php echo author.": ".hycus::getuserfullname($data->uid); ?></div>
				<div><?php echo last_updated.": ".hycus::showtime($data->lastupdated_on); ?></div>
			</div>
			<?php if(hycusLoader::loadsnippet("tweetmeme")){ ?>
				<div style="float:left;margin:5px;">
					<?php echo hycusLoader::loadsnippet("tweetmeme"); ?>
				</div>
			<?php } ?>
			<div class="blog_desc"><?php echo str_replace('<hr id="readmore" />', "", $data->data); ?></div>
			<?php if(hycusLoader::loadsnippet("addthis")){ echo "<div>".hycusLoader::loadsnippet("addthis")."</div>"; } ?>
			<div><?php if($data->enable_comments){ hycusLoader::loadModule("comment"); }?></div>
		</div>
		<?php
	}
	function page($data){
		?>
		<div class="page">
			<?php if($data->showtitle == "yes"){ ?>
				<h4><?php echo $data->title; ?></h4>
			<?php } ?>
			<div><?php echo str_replace('<hr id="readmore" />', "", $data->data); ?></div>
		</div>
		<?php
	}
	function catview($blogs){
		foreach($blogs AS $blog){
			?>
			<div class="blogs">
				<?php if($blog->showtitle == "yes"){ ?>
					<h4><a href="<?php echo huri::makeuri("?module=content&task=blog&id=".$blog->id."&menuid=".hycus::getcleanvar("menuid")); ?>" title="<?php echo $blog->title; ?>"><?php echo $blog->title; ?></a>&nbsp;<?php if($blog->uid==hycus::getthisuserid()){ ?><a href="<?php echo huri::makeuri("?module=content&task=editblog&id=".$blog->id."&menuid=".hycus::getcleanvar("menuid")); ?>" title="<?php echo $blog->title; ?>"><img src="<?php echo hycus::getroot();?>/images/icon_edit.gif" /></a><?php } ?></h4>
				<?php } ?>
				<div class="blog_details">
					<div><?php echo author.": ".hycus::getuserfullname($blog->uid); ?></div>
					<div><?php echo last_updated.": ".hycus::showtime($blog->lastupdated_on); ?></div>
				</div>
				<div class="blog_desc"><?php if(strchr($blog->data, '<hr id="readmore" />')){ echo substr($blog->data, 0, stripos($blog->data, '<hr id="readmore" />')); $showread="1";}else { echo $blog->data; $showread="0";}?></div>
				<?php if($showread) { ?>
					<a href="<?php echo huri::makeuri("?module=content&task=blog&id=".$blog->id."&menuid=".hycus::getcleanvar("menuid")); ?>" title="<?php echo $blog->title; ?>" class="more"><?php echo read_more; ?></a>
				<?php } ?>
				<?php if($showread && $blog->enable_comments) { echo "|"; } ?>
				<?php if($blog->enable_comments) { ?>
				<a href="<?php echo huri::makeuri("?module=content&task=blog&id=".$blog->id."&menuid=".hycus::getcleanvar("menuid")); ?>#comments" title="comments"><?php
				$db = new hdatabase();
				$comments = $db->get_recs("#__comments", "id", "module='content' AND item_id='$blog->id' AND approved='1'");
				echo count($comments)." ".comments;
				?></a>
				<?php } ?>
			</div>
			<?php
		}
	}
	function getsubcats($selectedid, $catid,$level){
		$db = new hdatabase();
		$cats = $db->get_recs("#__categories", "*", "parentid = '".$catid."' AND user_cat = '1'");
		if($cats)
		{
			foreach($cats AS $cat){
				echo "<option value='".$cat->id."'";
				if($selectedid == $cat->id){ echo "selected=SELECTED"; }
				echo ">";
				for($i=1; $i<=$level; $i++){
					echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				}
				echo "|_".$cat->title."</option>";
				hycusContentView::getsubcats($selectedid, $cat->id,$level+1);
			}
		}

	}
}
?>
