-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2008 年 06 月 06 日 17:44
-- 服务器版本: 5.0.16
-- PHP 版本: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- 数据库: `frame_smallphp`
--

-- --------------------------------------------------------

--
-- 表的结构 `articles`
--

DROP TABLE IF EXISTS `articles`;
CREATE TABLE IF NOT EXISTS `articles` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0 COMMENT='文章表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `articles_content`
--

DROP TABLE IF EXISTS `articles_content`;
CREATE TABLE IF NOT EXISTS `articles_content` (
  `article_id` int(11) unsigned NOT NULL,
  `content` longtext NOT NULL,
  UNIQUE KEY `article_id` (`article_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0 COMMENT='文章内容';

-- --------------------------------------------------------

--
-- 表的结构 `article_categories`
--

DROP TABLE IF EXISTS `article_categories`;
CREATE TABLE IF NOT EXISTS `article_categories` (
  `id` int(11) NOT NULL auto_increment,
  `pid` int(11) default '0',
  `title` varchar(255) NOT NULL,
  `ranking` smallint(6) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL auto_increment,
  `rid` int(1) NOT NULL,
  `content` text NOT NULL,
  `username` varchar(50) NOT NULL,
  `timer` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='评论表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `db_error`
--

DROP TABLE IF EXISTS `db_error`;
CREATE TABLE IF NOT EXISTS `db_error` (
  `id` int(11) NOT NULL auto_increment,
  `page_name` varchar(255) default '',
  `url` varchar(255) default '',
  `error_str` mediumtext,
  `timer` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0 COMMENT='数据库错误记录' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `soft_categories`
--

DROP TABLE IF EXISTS `soft_categories`;
CREATE TABLE IF NOT EXISTS `soft_categories` (
  `id` int(11) NOT NULL auto_increment,
  `pid` int(11) default '0',
  `title` varchar(255) NOT NULL,
  `ranking` smallint(6) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `tags`
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(20) unsigned NOT NULL auto_increment,
  `title` varchar(100) character set utf8 collate utf8_unicode_ci NOT NULL COMMENT 'tag标签',
  `r_id` int(11) unsigned NOT NULL,
  `sum_tags` int(11) unsigned NOT NULL default '1' COMMENT '被使用次数',
  `visit` int(11) unsigned NOT NULL default '1' COMMENT '标签访问次数',
  `timer` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `title` (`title`),
  KEY `article_id` (`r_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `username` varchar(100) collate utf8_unicode_ci NOT NULL,
  `password` varchar(100) collate utf8_unicode_ci NOT NULL,
  `grade` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

INSERT INTO `users` (`id`, `username`, `password`, `grade`) VALUES
(1, 'owen', '43996fb100428b0d99e233c3261f7187', 9);
