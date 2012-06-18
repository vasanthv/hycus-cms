CREATE TABLE IF NOT EXISTS `#__auth` (
  `auth_method` varchar(50) NOT NULL,
  `enabled` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*End of query*/

INSERT INTO `#__auth` (`auth_method`, `enabled`) VALUES
('auth_gmail', 'disabled');

/*End of query*/

CREATE TABLE IF NOT EXISTS `#__avatar` (
  `user_id` int(11) NOT NULL,
  `avatar` varchar(100) NOT NULL,
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*End of query*/

CREATE TABLE IF NOT EXISTS `#__blocks` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(1000) NOT NULL,
  `position` varchar(20) NOT NULL,
  `block_name` varchar(100) NOT NULL,
  `enabled` int(11) NOT NULL,
  `showtitle` int(11) NOT NULL,
  `data` varchar(5000) NOT NULL,
  `ordering` int(11) NOT NULL,
  `menuids` varchar(100) NOT NULL,
  `blockperms` int(11) NOT NULL,
  `blockclass` varchar(25) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

/*End of query*/

INSERT INTO `#__blocks` (`id`, `title`, `position`, `block_name`, `enabled`, `showtitle`, `data`, `ordering`, `menuids`, `blockperms`, `blockclass`) VALUES
(1, 'Mainmenu', 'topmenu', 'menu', 1, 0, 'menuid=1;menutype=horizontaldropdown;', 6, 'all', 0, 'hycustopmenu'),
(2, 'Slideshow', 'slideshow', 'slideshow', 1, 0, 'navigationtype=arrows;slideshowwidth=200;slideshowheight=150;', 1, '1', 0, 'hycusslideshow'),
(3, 'Top text', 'topnews', 'custom', 1, 0, 'contentid=7;', 5, '1', 0, 'hycustoptext'),
(4, 'search box', 'search', 'search', 1, 0, 'searchtext=search;searchbuttontext=Go;', 0, 'all', 0, 'searchbox'),
(5, 'Latest Blogs', 'right', 'latest', 1, 1, 'catids=3;count=5;blogblocktype=latest;showdate=1;', 4, '1', 0, 'hycuslatestblogs'),
(6, 'Login', 'right', 'login', 1, 1, 'showavatar=1;showedit=1;', 2, 'all', 0, 'hycusblock'),
(7, 'Custom Text', 'right', 'custom', 1, 1, 'contentid=6;', 3, '1', 0, 'hycusblock'),
(8, 'Breadcrumb', 'breadcrumb', 'breadcrumb', 1, 0, '', 0, 'all', 0, 'hycusbreadcrumb');

/*End of query*/

CREATE TABLE IF NOT EXISTS `#__categories` (
  `id` int(11) NOT NULL auto_increment,
  `parentid` int(11) NOT NULL,
  `title` varchar(1000) NOT NULL,
  `showtitle` varchar(3) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `enable_comments` int(11) NOT NULL,
  `user_cat` int(11) NOT NULL,
  `enablerss` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

/*End of query*/

INSERT INTO `#__categories` (`id`, `parentid`, `title`, `showtitle`, `description`, `enable_comments`, `user_cat`, `enablerss`) VALUES
(1, 0, 'Pages', 'yes', 'This category holds the content displayed on front page.', 0, 0, 0),
(2, 0, 'Blocks', 'yes', 'This holds the contents of the blocks.', 0, 0, 0),
(3, 0, 'First Category', 'yes', 'Sample First Category', 1, 1, 1);

/*End of query*/

CREATE TABLE IF NOT EXISTS `#__comments` (
  `id` int(11) NOT NULL auto_increment,
  `item_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `module` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `comment` varchar(10000) NOT NULL,
  `approved` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

/*End of query*/

INSERT INTO `#__comments` (`id`, `item_id`, `uid`, `module`, `time`, `comment`, `approved`) VALUES
(1, 4, 1, 'content', 1272099551, 'This is a sample comment.', 1);

/*End of query*/

CREATE TABLE IF NOT EXISTS `#__config` (
  `identifier` int(11) NOT NULL,
  `sitename` varchar(100) NOT NULL,
  `metakeywords` varchar(1000) NOT NULL,
  `metadesc` varchar(1000) NOT NULL,
  `siteurl` varchar(500) NOT NULL,
  `adminemail` varchar(25) NOT NULL,
  `timezone` varchar(4) NOT NULL,
  `timedisplayformat` int(11) NOT NULL,
  `template` varchar(100) NOT NULL,
  `language` varchar(5) NOT NULL,
  `sessionlimit` int(11) NOT NULL,
  `paginationlimit` int(11) NOT NULL,
  `disperror` int(11) NOT NULL,
  `enablesef` int(11) NOT NULL,
  `seftype` int(11) NOT NULL,
  `sefsuffix` varchar(10) NOT NULL,
  `showmodname` int(11) NOT NULL,
  `showmenuid` int(11) NOT NULL,
  `ajaxadmin` int(11) NOT NULL,
  UNIQUE KEY `identifier` (`identifier`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*End of query*/

CREATE TABLE IF NOT EXISTS `#__contact` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(20) NOT NULL,
  `introtext` varchar(1000) NOT NULL,
  `sendto` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

/*End of query*/

INSERT INTO `#__contact` (`id`, `title`, `introtext`, `sendto`) VALUES
(1, 'Contact us', 'Introduction text goes here. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut ', 'example@contact.com');

/*End of query*/

CREATE TABLE IF NOT EXISTS `#__contents` (
  `id` int(11) NOT NULL auto_increment,
  `catid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `title` varchar(10000) NOT NULL,
  `showtitle` varchar(3) NOT NULL,
  `data` mediumtext NOT NULL,
  `enable_comments` int(11) NOT NULL,
  `hits` int(11) NOT NULL,
  `added_on` int(11) NOT NULL,
  `lastupdated_on` int(11) NOT NULL,
  `enabled` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `metakeywords` varchar(1000) NOT NULL,
  `metadescription` varchar(1000) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

/*End of query*/

INSERT INTO `#__contents` (`id`, `catid`, `uid`, `title`, `showtitle`, `data`, `enable_comments`, `hits`, `added_on`, `lastupdated_on`, `enabled`, `ordering`, `metakeywords`, `metadescription`) VALUES
(1, 1, 1, 'Welcome to Hycus CMS', 'yes', '<p>\r\n			Hycus CMS is a free, open source PHP based content management system. Hycus cms is an easy to use cms with challenging features in it. The <strong>basic feature list</strong> of the cms:</p>\r\n		<ol style="margin-left:40px;">\r\n			<li>\r\n				Light weight CMS.</li>\r\n			<li>\r\n				Fully featured CK editor in admin side.</li>\r\n			<li>\r\n				Removable admin side.</li>\r\n			<li>\r\n				Fully ajax based admin side. (can also be disabled)</li>\r\n			<li>\r\n				Easy to manage your site''s content, menus, blocks.</li>\r\n			<li>\r\n				Site''s configuration at you finger tips.</li>\r\n			<li>\r\n				Creates RSS feed for your contents.</li>\r\n			<li>\r\n				User management for front end.</li>\r\n			<li>\r\n				Search with extendable plugins.</li>\r\n			<li>\r\n				Easy designing.</li>\r\n			<li>\r\n				Html/CSS templates to Hycus templates is easy.</li>\r\n			<li>\r\n				Contact page.</li>\r\n			<li>\r\n				Search Engine Friendly URL''s (SEF).</li>\r\n			<li>\r\n				Maintain multiple Websites with same single set of files.</li>\r\n			<li>\r\n				Add contents in other Indian langiages (available only from admin side)</li>\r\n		</ol>\r\n		<div>\r\n			<strong>Requirements</strong>:</div>\r\n		<ol style="margin-left:40px;">\r\n			<li>\r\n				PHP 5.0 or above</li>\r\n			<li>\r\n				MySQL 5.0.51 or above</li>\r\n			<li>\r\n				Javascript enabled browser</li>\r\n		</ol>\r\n		<div>\r\n			<strong>Browser compatibility</strong> (test in the following browsers):</div>\r\n		<ol style="margin-left:40px;">\r\n			<li>\r\n				Firefox 3</li>\r\n			<li>\r\n				Google Chrome</li>\r\n			<li>\r\n				Apple Safari</li>\r\n			<li>\r\n				Microsoft IE7 and IE8</li>\r\n			<li>\r\n				Opera 10</li>\r\n		</ol>', 1, 377, 1278089195, 1280659099, 1, 0, 'Welcome to Hycus CMS, hycus cms intro page', 'Hycus cms introduction page'),
(2, 1, 1, 'About Hycus', 'yes', '<p>\n			We are a small team of web developers working together in Tamilnadu, India. We are basically joomla CMS based web developers, and always wanted to develop a simpler CMS that makes a website, portal more powerful and fast. We started the Hycus-CMS project in the middle of April 2010 and was finished upto the first release by end of July 2010. We are continuously working on Hycus-CMS to make it more suitable to develop any website on it. We do take PHP based website development of web application development projects for our financial needs.</p>\n		<div>\n			<strong>We are social:</strong></div>\n		<table>\n			<tbody>\n				<tr>\n					<td>\n						Follow our twitter updates at <a href="http://twitter.com/teamhycus" target="_blank"><img src="images/twitter-button.png" style="width:175px;height:81px;border:mediumnone;" /></a></td>\n					<td>\n						Subscribe to our YouTube channel at <a href="http://www.youtube.com/user/teamhycus" target="_blank"><img src="images/youtube_button.jpg" style="width:175px;height:81px;border:mediumnone;" /></a></td>\n				</tr>\n			</tbody>\n		</table>', 0, 57, 1280047822, 1280224702, 1, 3, '', ''),
(3, 3, 1, 'Hycus License', 'yes', '<p>\n			This Web site is powered by <a href="http://www.hycus.com/">hycus</a>!. Hycus-CMS is released under GNU/GPL. The hycus cms and default templates on which it runs are Copyright 2010 <a href="http://www.hycus.com">Hycus.com</a>. All data entered into this Web site and templates added after installation, are copyrighted by their respective copyright owners.</p>\n		<hr id="readmore" />\n		<p>\n			If you want to distribute, copy, or modify Hycus-Cms, you are welcome to do so under the terms of the GNU General Public License. If you are unfamiliar with this license, you might want to read ''<a href="http://www.gnu.org/licenses/old-licenses/gpl-2.0.html#SEC4">How To Apply These Terms To Your Program</a>'' and the ''<a href="http://www.gnu.org/licenses/old-licenses/gpl-2.0-faq.html">GNU General Public License FAQ</a>''.</p>', 0, 6, 1274416988, 1280256669, 1, 7, '', ''),
(4, 3, 1, 'Sample Blog', 'yes', '<p>\n			This is a sample blog entry with comments.xyx</p>', 1, 9, 1274204848, 1280218238, 1, 8, '', ''),
(5, 3, 1, 'Hycus CMS', 'yes', '<p>\n			As you all know Hycus is free, open source, PHP based content management system (we are repeatedly saying it). Hycus gives you the ease of maintaining your own website. Are you new to the web publising? Are you looking for a software that makes your website development work easy? You are in the right place to find the solution. Try Hycus CMS.</p>\n		<hr id="readmore" />\n		<p>\n			You need not wanna have experience in HTML or CSS maintaining your website once it is up and running in your server.</p>\n		<p>\n			We are in development of extra modules and blocks that can make your work more easy. Check it out soon.</p>', 0, 8, 1278395309, 1280256538, 1, 6, '', ''),
(6, 2, 1, 'Custom Text', 'yes', '<p class="testimonial">\n			<span>&ldquo; </span> Nunc fringilla porttitor ipsum. Nulla rutrum sapien sed leo. Fusce sit amet nulla ac velit commodo mattis. Ut tristique neque nec lorem. <span> &rdquo;</span></p>', 0, 0, 1279526365, 1280048490, 1, 4, '', ''),
(7, 2, 1, 'Top block content', 'yes', '<p style="text-align:justify;">\n			Sample text. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam.</p>', 1, 13, 1271910227, 1279728332, 1, 5, 'sample, content, 2', 'this is a sample content description..'),
(8, 3, 1, 'Other languages', 'yes', '<p>You can now publish contents in eight indian languages too.</p><p>Hindi, gujarathi, punjabi, telugu, tamil, malayalam, kannada, bengali..</p>\n', 1, 0, 1280835164, 1280835200, 1, 7, '', '');

/*End of query*/

CREATE TABLE IF NOT EXISTS `#__menuitems` (
  `id` int(11) NOT NULL auto_increment,
  `menuid` int(11) NOT NULL,
  `parentid` int(11) NOT NULL,
  `itemtitle` varchar(250) NOT NULL,
  `itemlink` varchar(2000) NOT NULL,
  `pagetitle` varchar(100) NOT NULL,
  `showtitle` int(11) NOT NULL,
  `hovertext` varchar(1000) NOT NULL,
  `defaultmenu` int(11) NOT NULL,
  `menuperms` int(11) NOT NULL,
  `target` varchar(10) NOT NULL,
  `enabled` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

/*End of query*/

INSERT INTO `#__menuitems` (`id`, `menuid`, `parentid`, `itemtitle`, `itemlink`, `pagetitle`, `showtitle`, `hovertext`, `defaultmenu`, `menuperms`, `target`, `enabled`, `ordering`) VALUES
(1, 1, 0, 'Home', '?module=content&task=page&id=1', 'Welcome to Hycus CMS', 0, '', 1, 0, '0', 1, 1),
(2, 1, 0, 'About Hycus', 'http://www.hycus.com/About-us/about-us.html', 'About hycus team', 0, '', 0, 0, 'blank', 1, 2),
(3, 1, 0, 'Category', '?module=content&task=catview&id=3', 'Category', 0, '', 0, 0, '0', 1, 3),
(4, 1, 0, 'Contact', '?module=contact&task=contact&id=1', 'Contact us', 0, '', 0, 0, '0', 1, 4),
(5, 1, 0, 'Add Blog', '?module=content&task=addblog', '', 0, '', 0, 1, '0', 1, 5);

/*End of query*/

CREATE TABLE IF NOT EXISTS `#__menus` (
  `id` int(11) NOT NULL auto_increment,
  `menuname` varchar(500) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

/*End of query*/

INSERT INTO `#__menus` (`id`, `menuname`) VALUES
(1, 'mainmenu');

/*End of query*/

CREATE TABLE IF NOT EXISTS `#__modules` (
  `id` int(11) NOT NULL auto_increment,
  `module` varchar(50) NOT NULL,
  `data` varchar(1000) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

/*End of query*/

INSERT INTO `#__modules` (`id`, `module`, `data`) VALUES
(2, 'user', 'defaultusertype=3;useractivation=1;'),
(3, 'contact', '');

/*End of query*/

CREATE TABLE IF NOT EXISTS `#__sefurls` (
  `orgurl` varchar(500) NOT NULL,
  `sefurl` varchar(500) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*End of query*/

CREATE TABLE IF NOT EXISTS `#__session` (
  `time` int(11) NOT NULL,
  `sessionid` varchar(255) NOT NULL,
  `userid` int(11) NOT NULL,
  `guest` tinyint(4) NOT NULL,
  `sessionip` varchar(18) NOT NULL,
  PRIMARY KEY  (`sessionid`),
  UNIQUE KEY `sessionid` (`sessionid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*End of query*/

CREATE TABLE IF NOT EXISTS `#__snippets` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(100) NOT NULL,
  `code` varchar(5000) NOT NULL,
  `enabled` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

/*End of query*/

INSERT INTO `#__snippets` (`id`, `title`, `code`, `enabled`, `ordering`) VALUES
(1, 'google_analytics', '<script type=\\"text/javascript\\">\nvar gaJsHost = ((\\"https:\\" == document.location.protocol) ? \\"https://ssl.\\" : \\"http://www.\\");\ndocument.write(unescape(\\"%3Cscript src=''\\" + gaJsHost + \\"google-analytics.com/ga.js'' type=''text/javascript''%3E%3C/script%3E\\"));\n</script>\n<script type=\\"text/javascript\\">\ntry {\nvar pageTracker = _gat._getTracker(\\"/*Enter your unique id here*/\\");\npageTracker._trackPageview();\n} catch(err) {}</script>', 0, 0),
(3, 'tweetmeme', '<script type=\\"text/javascript\\" src=\\"http://tweetmeme.com/i/scripts/button.js\\"></script>', 0, 0),
(4, 'addthis', '<!-- You can add your own share button code here. You can get your button at http://www.addthis.com/ -->\n\n<!-- AddThis Button BEGIN -->\n<a class=\\"addthis_button\\" href=\\"http://www.addthis.com/bookmark.php?v=250&username=hycus\\"><img src=\\"http://s7.addthis.com/static/btn/v2/lg-share-en.gif\\" width=\\"125\\" height=\\"16\\" alt=\\"Bookmark and Share\\" style=\\"border:0\\"/></a><script type=\\"text/javascript\\" src=\\"http://s7.addthis.com/js/250/addthis_widget.js#username=hycus\\"></script>\n<!-- AddThis Button END -->\n', 0, 0);

/*End of query*/

CREATE TABLE IF NOT EXISTS `#__templates` (
  `templatename` varchar(50) NOT NULL,
  `data` varchar(1000) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*End of query*/

INSERT INTO `#__templates` (`templatename`, `data`) VALUES
('hycus_template', 'themecolor=006699;');

/*End of query*/

CREATE TABLE IF NOT EXISTS `#__users` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `typeid` int(11) NOT NULL default '1',
  `approved` tinyint(4) NOT NULL default '0',
  `registeredon` int(11) NOT NULL,
  `lastvisiton` int(11) NOT NULL,
  `lastvistfrom` varchar(200) NOT NULL,
  `auth_token` varchar(200) NOT NULL,
  `block` tinyint(4) NOT NULL default '0',
  `password` varchar(255) NOT NULL,
  `default` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*End of query*/

CREATE TABLE IF NOT EXISTS `#__usertypes` (
  `id` int(11) NOT NULL auto_increment,
  `usertype` varchar(50) NOT NULL,
  `adminaccess` int(11) NOT NULL,
  `contentae` int(11) NOT NULL,
  `moduleaccess` varchar(250) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

/*End of query*/

INSERT INTO `#__usertypes` (`id`, `usertype`, `adminaccess`, `contentae`, `moduleaccess`) VALUES
(1, 'administrator', 1, 1, 'blocks:1;categories:1;comment:1;config:1;contact:1;content:1;media:1;menus:1;snippets:1;statistics:1;templates:1;user:1;usertypes:1'),
(2, 'editor', 0, 1, ''),
(3, 'registered', 0, 0, '');
