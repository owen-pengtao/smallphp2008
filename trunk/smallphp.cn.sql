-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2008 年 07 月 30 日 17:15
-- 服务器版本: 5.0.16
-- PHP 版本: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- 数据库: `frame_smallphp`
--

-- --------------------------------------------------------

--
-- 表的结构 `db_error`
--
-- 创建时间: 2008 年 07 月 30 日 16:50
-- 最后更新时间: 2008 年 07 月 30 日 17:06
--

DROP TABLE IF EXISTS `db_error`;
CREATE TABLE IF NOT EXISTS `db_error` (
  `id` int(11) NOT NULL auto_increment,
  `page_name` varchar(255) default '',
  `url` varchar(255) default '',
  `error_str` mediumtext,
  `timer` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0 COMMENT='数据库错误记录' AUTO_INCREMENT=0 ;


-- --------------------------------------------------------

--
-- 表的结构 `sp_articles`
--
-- 创建时间: 2008 年 06 月 06 日 17:22
-- 最后更新时间: 2008 年 06 月 06 日 17:52
--

DROP TABLE IF EXISTS `sp_articles`;
CREATE TABLE IF NOT EXISTS `sp_articles` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `cid` int(11) unsigned default '0',
  `cid_name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `visit` int(11) unsigned default '1',
  `sum_comments` smallint(5) unsigned NOT NULL default '0' COMMENT '评论总数',
  `sum_supports` smallint(5) unsigned NOT NULL default '0' COMMENT '支持总数',
  `author` varchar(100) NOT NULL,
  `copy_from` varchar(255) NOT NULL,
  `copy_url` varchar(255) NOT NULL,
  `count_word` int(11) unsigned NOT NULL default '0',
  `count_page` int(11) unsigned NOT NULL default '0',
  `tag` varchar(255) NOT NULL,
  `info` varchar(255) NOT NULL COMMENT '内容摘要',
  `is_pass` smallint(6) unsigned NOT NULL default '1',
  `timer` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `is_pass` (`is_pass`),
  KEY `tag` (`tag`,`is_pass`),
  KEY `cid` (`cid`,`is_pass`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0 COMMENT='文章表' AUTO_INCREMENT=2 ;

--
-- 导出表中的数据 `sp_articles`
--

INSERT INTO `sp_articles` (`id`, `cid`, `cid_name`, `title`, `visit`, `sum_comments`, `sum_supports`, `author`, `copy_from`, `copy_url`, `count_word`, `count_page`, `tag`, `info`, `is_pass`, `timer`) VALUES
(1, 8, '《鬼吹灯》之精绝古城', 'a', 1, 0, 0, 'owen', '', '', 0, 0, 'test', 'aa', 1, 1217409224);

-- --------------------------------------------------------

--
-- 表的结构 `sp_articles_content`
--
-- 创建时间: 2008 年 06 月 06 日 17:08
-- 最后更新时间: 2008 年 07 月 30 日 16:58
--

DROP TABLE IF EXISTS `sp_articles_content`;
CREATE TABLE IF NOT EXISTS `sp_articles_content` (
  `article_id` int(11) unsigned NOT NULL,
  `content` longtext NOT NULL,
  UNIQUE KEY `article_id` (`article_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0 COMMENT='文章内容';

--
-- 导出表中的数据 `sp_articles_content`
--

INSERT INTO `sp_articles_content` (`article_id`, `content`) VALUES
(1, '<p><strong>aa的</strong></p>');

-- --------------------------------------------------------

--
-- 表的结构 `sp_article_categories`
--
-- 创建时间: 2008 年 04 月 30 日 15:07
-- 最后更新时间: 2008 年 04 月 30 日 18:06
--

DROP TABLE IF EXISTS `sp_article_categories`;
CREATE TABLE IF NOT EXISTS `sp_article_categories` (
  `id` int(11) NOT NULL auto_increment,
  `pid` int(11) default '0',
  `title` varchar(255) NOT NULL,
  `ranking` smallint(6) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=220 ;

--
-- 导出表中的数据 `sp_article_categories`
--

INSERT INTO `sp_article_categories` (`id`, `pid`, `title`, `ranking`) VALUES
(1, 0, '测试分类', 0),
(82, 80, '易中天品三国', 0),
(4, 2, '相关资料', 0),
(2, 0, '鬼吹灯www.guichuideng.sh.cn', 0),
(3, 2, '相关评论', 0),
(5, 2, '《鬼吹灯》之昆仑神宫', 1),
(6, 2, '《鬼吹灯》之云南虫谷', 2),
(7, 2, '《鬼吹灯》之龙谷迷窟', 3),
(8, 2, '《鬼吹灯》之精绝古城', 4),
(9, 0, '鬼故事www.guigushi.org', 0),
(10, 9, '短篇鬼故事', 0),
(11, 9, '校园鬼故事', 0),
(12, 9, '长篇鬼故事', 0),
(13, 9, '医院怪谈', 0),
(14, 9, '灵异故事', 0),
(15, 9, '外国鬼故事', 0),
(16, 9, '墓地惊魂', 0),
(17, 9, '鬼神知识', 0),
(18, 2, '《鬼吹灯2》之黄皮子e&#39;坟', 5),
(19, 0, '诛仙8www.zhuxian8.org/', 0),
(20, 19, '诛仙8_第1-2章', 0),
(21, 19, '诛仙8_第3-4章', 0),
(22, 19, '诛仙8_第5-6章', 0),
(23, 19, '诛仙8_第7-8章', 0),
(24, 19, '诛仙8_第9-10章', 0),
(25, 0, '明晓溪www.mingxiaoxi.net.cn', 0),
(26, 25, '相关报道', 0),
(27, 25, '泡沫之夏1', 0),
(28, 25, '泡沫之夏2', 0),
(29, 25, '泡沫之夏3', 0),
(30, 25, '水晶般透明', 0),
(31, 25, '冬日最灿烂的阳光', 0),
(32, 25, '无往而不胜的童话', 0),
(33, 25, '烈火如歌', 0),
(34, 25, '烈火如歌2', 0),
(39, 38, '相关新闻', 0),
(38, 0, '于丹www.yudan.net.cn', 0),
(35, 25, '雨夜里的星星沙', 0),
(36, 25, '会有天使替我爱你', 0),
(37, 25, '小魔女的必杀技', 0),
(40, 38, '于丹论语心得', 0),
(41, 38, '于丹解读庄子', 0),
(42, 38, '论语庄子相关', 0),
(43, 38, '网友评论', 0),
(44, 38, '论语全书', 0),
(45, 38, '庄子全书', 0),
(46, 0, '杨二车娜姆www.yangerchenamu.com', 0),
(47, 46, '相关信息', 0),
(48, 46, '离开母亲湖', 0),
(49, 46, '中国红遇见挪威蓝', 0),
(50, 46, '七年之痒', 0),
(51, 46, '长得漂亮不如活得漂亮', 0),
(52, 46, '关于摩梭', 0),
(53, 54, '相关新闻', 0),
(54, 0, '张小娴www.zhangxiaoxian.net', 0),
(55, 54, '荷包里的单人床', 0),
(56, 54, '面包树出走了', 0),
(57, 54, '三个ACUP的女人', 0),
(58, 54, '面包树上的女人', 0),
(59, 54, '贴身感觉', 0),
(60, 54, '刻骨的爱人', 0),
(61, 54, '蝴蝶过期居留', 0),
(62, 54, '那年的梦想', 0),
(63, 54, '三月里的幸福饼', 0),
(64, 54, '悬浮在空中的吻', 0),
(65, 54, '永不永不说再见', 0),
(66, 54, '情人无泪', 0),
(67, 54, '离别曲', 0),
(68, 54, '流波上的舞', 0),
(69, 54, '永无止境的怀抱', 0),
(70, 54, '不如你送我一场春雨', 0),
(71, 54, '禁果之味', 0),
(72, 54, '魔法蛋糕店', 0),
(73, 54, '再见野鼬鼠', 0),
(74, 54, '幸福鱼面颊', 0),
(75, 54, '思念里的流浪狗', 0),
(76, 54, '卖海豚的女孩', 0),
(77, 54, '我们都是丑小鸭', 0),
(78, 54, '其他作品', 0),
(79, 54, '网友评论', 0),
(80, 0, '易中天www.yizhongtian.org.cn', 0),
(81, 80, '媒体关注', 0),
(83, 80, '品读汉代风云人物', 0),
(84, 80, '帝国的惆怅', 0),
(85, 80, '品人录', 0),
(86, 80, '读城记', 0),
(87, 80, '文章博客', 0),
(88, 80, '网友评论', 0),
(89, 0, '郭敬明www.guojm.com', 0),
(90, 89, '相关新闻', 0),
(91, 89, '梦里花落知多少', 0),
(92, 89, '幻城', 0),
(93, 89, '一梦三四年', 0),
(94, 89, '夏至未至', 0),
(95, 89, '爱与痛的边缘', 0),
(96, 89, '无极', 0),
(97, 89, '初恋', 0),
(98, 89, '岛1.柢步', 0),
(99, 89, '岛2.陆眼', 0),
(100, 89, '岛3.锦年', 0),
(101, 89, '岛4.普瑞尔', 0),
(102, 89, '岛5.埃泽尔', 0),
(103, 89, '岛6.泽塔', 0),
(104, 89, '新版幻城', 0),
(105, 89, '悲伤逆流成河', 0),
(106, 89, '相关文章', 0),
(107, 0, '唐家三少www.tangjiasanshao.com', 0),
(108, 107, '相关新闻', 0),
(109, 107, '冰火魔厨', 0),
(110, 107, '惟我独仙', 0),
(111, 107, '善良的死神', 0),
(112, 107, '狂神', 0),
(113, 107, '光之子', 0),
(114, 107, '空速星痕', 0),
(115, 107, '生肖守护神', 0),
(159, 157, '相关新闻陈安之成功学', 0),
(116, 107, '相关评论', 0),
(118, 117, '相关新闻', 0),
(117, 0, '木子美www.muzimei.net.cn', 0),
(119, 117, '遗情书', 0),
(120, 117, '其他作品', 0),
(121, 117, '木子美博客', 0),
(122, 0, '梦里花落知多少menglihualuozhiduoshao.net.cn', 0),
(123, 122, '第一部分', 0),
(124, 122, '第二部分', 0),
(125, 122, '第三部分', 0),
(126, 122, '第四部分', 0),
(127, 122, '第五部分', 0),
(128, 122, '主要演员', 0),
(129, 122, '动态追踪', 0),
(130, 0, '李小龙www.lixiaolong.org.cn', 0),
(131, 130, '相关新闻', 0),
(132, 130, '李小龙一生', 0),
(133, 130, '影视生涯', 0),
(134, 130, '故居亲人弟子', 0),
(135, 130, '图片视频', 0),
(136, 130, '生活纪实', 0),
(137, 130, '风采荣誉', 0),
(138, 130, '相关评论', 0),
(139, 0, '金庸www.jinyongbooks.com', 0),
(140, 139, '相关新闻', 0),
(141, 139, '飞狐外传', 0),
(142, 139, '雪山飞狐', 0),
(143, 139, '连城诀', 0),
(144, 139, '天龙八部', 0),
(145, 139, '射雕英雄传', 0),
(146, 139, '白马啸西风', 0),
(147, 139, '鹿鼎记', 0),
(148, 139, '笑傲江湖', 0),
(149, 139, '书剑恩仇录', 0),
(150, 139, '神雕侠侣', 0),
(151, 139, '侠客行', 0),
(152, 139, '倚天屠龙记', 0),
(153, 139, '碧血剑', 0),
(154, 139, '鸳鸯刀', 0),
(155, 139, '续鹿鼎记', 0),
(156, 139, '其他作品', 0),
(157, 0, '陈安之www.chenanzhi.net.cn', 0),
(158, 157, '相关新闻', 0),
(160, 157, '陈安之讲座', 0),
(161, 157, '陈安之演讲', 0),
(162, 157, '相关资料', 0),
(163, 157, '陈安之博客', 0),
(164, 157, '陈安之作品', 0),
(165, 0, '回到明朝当王爷huidaomingchaodangwangye.org.cn', 0),
(166, 165, '第1-2卷', 0),
(167, 165, '第3-4卷', 0),
(168, 165, '第5卷', 0),
(169, 165, '第6-7卷+最新章节', 0),
(170, 165, '外篇', 0),
(171, 0, '安妮宝贝www.vivian.org.cn', 0),
(172, 171, '相关新闻', 0),
(173, 171, '安妮宝贝小说集', 0),
(174, 171, '安妮宝贝随笔', 0),
(175, 171, '安妮宝贝诗歌', 0),
(176, 171, '二三事', 0),
(177, 171, '相关作品', 0),
(178, 171, '网友评论', 0),
(179, 0, '陈晓旭www.chenxiaoxu.net', 0),
(180, 179, '相关新闻', 0),
(181, 179, '历史报道', 0),
(182, 179, '梦里三年', 0),
(183, 179, '商海生涯', 0),
(184, 179, '遁入空门', 0),
(185, 179, '回忆怀念', 0),
(186, 179, '各方评论', 0),
(187, 0, '都市花盗www.dushihuadao.cn', 0),
(188, 187, '都市花盗_第1卷', 0),
(189, 187, '都市花盗_第2卷', 0),
(190, 187, '都市花盗_第3卷', 0),
(191, 187, '都市花盗_第4卷', 0),
(192, 187, '都市花盗_第5卷', 0),
(193, 187, '都市花盗_第6卷', 0),
(194, 0, '女人我最大', 0),
(195, 194, '女人我最大-34', 0),
(196, 46, '杨二车娜姆视频', 0),
(197, 80, '易中天-品三国-视频', 0),
(198, 80, '易中天-汉代风云人物-视频', 0),
(199, 80, '豆瓣-评论-品三国', 0),
(200, 194, '减肥', 0),
(201, 194, '女人我最大-151', 0),
(202, 194, '美发', 0),
(203, 194, '美容', 0),
(204, 194, '其他视频', 0),
(205, 0, '康熙来了', 0),
(206, 205, '康熙来了视频', 0),
(211, 3, 'a', 0),
(214, 211, 'c', 0),
(213, 211, 'c', 0),
(215, 211, 'c', 0),
(216, 211, 'c', 0),
(217, 211, 'c', 0),
(218, 211, 'd', 0),
(219, 0, '技术文章', 0);

-- --------------------------------------------------------

--
-- 表的结构 `sp_comments`
--
-- 创建时间: 2008 年 06 月 06 日 17:08
-- 最后更新时间: 2008 年 06 月 06 日 17:08
--

DROP TABLE IF EXISTS `sp_comments`;
CREATE TABLE IF NOT EXISTS `sp_comments` (
  `id` int(11) NOT NULL auto_increment,
  `rid` int(1) NOT NULL,
  `content` text NOT NULL,
  `username` varchar(50) NOT NULL,
  `timer` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='评论表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `sp_comments`
--


-- --------------------------------------------------------

--
-- 表的结构 `sp_db_error`
--
-- 创建时间: 2008 年 06 月 06 日 15:52
-- 最后更新时间: 2008 年 07 月 30 日 16:28
--

DROP TABLE IF EXISTS `sp_db_error`;
CREATE TABLE IF NOT EXISTS `sp_db_error` (
  `id` int(11) NOT NULL auto_increment,
  `page_name` varchar(255) default '',
  `url` varchar(255) default '',
  `error_str` mediumtext,
  `timer` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0 COMMENT='数据库错误记录' AUTO_INCREMENT=3 ;

--
-- 导出表中的数据 `sp_db_error`
--

INSERT INTO `sp_db_error` (`id`, `page_name`, `url`, `error_str`, `timer`) VALUES
(1, '/root/tag.php', 'http://www.smallphp.cn/root/tag.php', '查询失败:\nSELECT count(tag_id) as count_id FROM `ad_tags` <br/>\r\n1146<br/>\r\n', 1212738735),
(2, '/root/category.php', 'http://www.smallphp.cn/root/category.php?channel=article', '查询失败:\nSELECT id, pid, title, ranking FROM `article_categories` ORDER BY ranking DESC ,id<br/>\r\n1146<br/>\r\n', 1217406494);

-- --------------------------------------------------------

--
-- 表的结构 `sp_tags`
--
-- 创建时间: 2008 年 06 月 06 日 17:32
-- 最后更新时间: 2008 年 07 月 30 日 17:13
--

DROP TABLE IF EXISTS `sp_tags`;
CREATE TABLE IF NOT EXISTS `sp_tags` (
  `id` int(20) unsigned NOT NULL auto_increment,
  `title` varchar(100) character set utf8 collate utf8_unicode_ci NOT NULL COMMENT 'tag标签',
  `r_id` int(11) unsigned NOT NULL,
  `sum_tags` int(11) unsigned NOT NULL default '1' COMMENT '被使用次数',
  `visit` int(11) unsigned NOT NULL default '1' COMMENT '标签访问次数',
  `timer` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `title` (`title`),
  KEY `article_id` (`r_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- 导出表中的数据 `sp_tags`
--

INSERT INTO `sp_tags` (`id`, `title`, `r_id`, `sum_tags`, `visit`, `timer`) VALUES
(1, 'ddd', 1, 1, 1, 1212744820),
(2, '444', 0, 1, 1, 1212745025),
(3, '48765432', 0, 1, 1, 1213196579),
(4, 'fff', 1, 1, 1, 1217408335),
(5, 'test', 1, 1, 1, 1217409224);

-- --------------------------------------------------------

--
-- 表的结构 `sp_users`
--
-- 创建时间: 2008 年 05 月 22 日 16:12
-- 最后更新时间: 2008 年 06 月 06 日 16:49
--

DROP TABLE IF EXISTS `sp_users`;
CREATE TABLE IF NOT EXISTS `sp_users` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `username` varchar(100) collate utf8_unicode_ci NOT NULL,
  `password` varchar(100) collate utf8_unicode_ci NOT NULL,
  `grade` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- 导出表中的数据 `sp_users`
--

INSERT INTO `sp_users` (`id`, `username`, `password`, `grade`) VALUES
(1, 'owen', '43996fb100428b0d99e233c3261f7187', 9);
