/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 100212
Source Host           : localhost:3306
Source Database       : test

Target Server Type    : MYSQL
Target Server Version : 100212
File Encoding         : 65001

Date: 2018-08-15 21:38:01
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for post
-- ----------------------------
DROP TABLE IF EXISTS `post`;
CREATE TABLE `post` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `biz` varchar(255) NOT NULL COMMENT '文章对应的公众号biz',
  `field_id` int(11) unsigned NOT NULL COMMENT '微信定义的一个id，每条文章唯一',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '文章标题',
  `title_encode` text NOT NULL COMMENT '文章编码，防止文章出现emoji',
  `digest` varchar(500) NOT NULL DEFAULT '' COMMENT '文章摘要',
  `content_url` varchar(500) NOT NULL COMMENT '文章地址',
  `source_url` varchar(500) NOT NULL COMMENT '阅读原文地址',
  `cover` varchar(500) NOT NULL COMMENT '封面图片',
  `is_multi` tinyint(1) unsigned NOT NULL COMMENT '是否多图文',
  `is_top` tinyint(1) unsigned NOT NULL COMMENT '是否头条',
  `datetime` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '文章时间戳',
  `readNum` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '文章阅读量',
  `likeNum` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '文章点赞量',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信文章表';

-- ----------------------------
-- Table structure for tmplist
-- ----------------------------
DROP TABLE IF EXISTS `tmplist`;
CREATE TABLE `tmplist` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `content_url` varchar(255) NOT NULL DEFAULT '' COMMENT '文章地址',
  `load` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '读取中标记',
  PRIMARY KEY (`id`),
  UNIQUE KEY `content_url` (`content_url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='采集队列表';

-- ----------------------------
-- Table structure for weixin
-- ----------------------------
DROP TABLE IF EXISTS `weixin`;
CREATE TABLE `weixin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `biz` varchar(255) NOT NULL DEFAULT '' COMMENT '公众号唯一标识biz',
  `collect` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '记录采集时间的时间戳',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='采集队列表';
