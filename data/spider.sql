/*
Navicat MySQL Data Transfer

Source Server         : 本地
Source Server Version : 50552
Source Host           : localhost:3306
Source Database       : test

Target Server Type    : MYSQL
Target Server Version : 50552
File Encoding         : 65001

Date: 2018-04-27 16:36:06
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for article
-- ----------------------------
DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `digest` varchar(255) DEFAULT NULL,
  `article_img` varchar(255) DEFAULT NULL,
  `source_url` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `weixin_article_id` int(11) unsigned NOT NULL,
  `datetime` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='文章表';

-- ----------------------------
-- Table structure for image
-- ----------------------------
DROP TABLE IF EXISTS `image`;
CREATE TABLE `image` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `href` varchar(255) NOT NULL COMMENT '图片地址',
  `md5file` char(32) NOT NULL COMMENT 'redis key name ',
  `local_path` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='图片资源库';

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `weixin_name` varchar(255) NOT NULL,
  `biz` varchar(255) NOT NULL COMMENT '公众号标识',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('1', '亚太灯饰传媒', 'http://img01.sogoucdn.com/app/a/100520090/oIWsFtz9nG3cmaajXDmb7tAnnYG8', '亚太灯饰传媒', 'MzAxMjAwMDY3OA==', '0');
INSERT INTO `users` VALUES ('2', '最灯饰', 'http://img01.sogoucdn.com/app/a/100520090/oIWsFt4FwvHnHbAGVb7uLBAOOvrE', '最灯饰', 'MzIyNjA1NDM0MA==', '0');
INSERT INTO `users` VALUES ('3', '欧普照明', 'http://img01.sogoucdn.com/app/a/100520090/oIWsFt9W5GIDiKCS5DxhxCT-VuiM', '欧普照明', 'MzIzNjQ4NjQ4OQ==', '0');
INSERT INTO `users` VALUES ('4', '欧普商业照明', '', '欧普商业照明', 'MzAxNDUxNzA5MQ==', '0');
INSERT INTO `users` VALUES ('5', '雷士照明', 'http://wx.qlogo.cn/mmhead/Q3auHgzwzM6IKLFQicdLhnuvTzFe8YXSVBpb54LI1SRwwhVOSFhtxibQ/0', 'NVC雷士照明', 'MjM5MzI1MTQyMA==', '0');
INSERT INTO `users` VALUES ('6', '古镇灯饰报', 'http://wx.qlogo.cn/mmhead/Q3auHgzwzM7ZWJfSjKBSNp9Am5VuvdxyWXJ0NVBicWYicPbMhHRiaPXTA/0', '古镇灯饰报', 'MjM5OTk3MDYwMw==', '0');
INSERT INTO `users` VALUES ('7', '古镇灯博会', 'http://wx.qlogo.cn/mmhead/Q3auHgzwzM6SJHIHRiaQPutaZ2N8UERjXVPBKKSS6voMicpwGuF9Stgw/0', '古镇灯博会', 'MjM5NDE2NjAxNg==', '0');
INSERT INTO `users` VALUES ('8', '华艺灯饰', 'http://wx.qlogo.cn/mmhead/Q3auHgzwzM4Fe65wFcucsMBic7flBGahe8bbyvUPOaMM3x3zmGdpCOA/0', '华艺灯饰照明', 'MjM5ODYwNjg2NA==', '0');
INSERT INTO `users` VALUES ('9', '灯网', 'http://wx.qlogo.cn/mmhead/Q3auHgzwzM5k5hB4LWLqYpiaNUoq1wBSUqD5jS123zvgn3p9b25fJ9g/0', 'dengcom灯网官微', 'MzA4MjgxNDc1Ng==', '0');
INSERT INTO `users` VALUES ('10', '美的照明', 'http://wx.qlogo.cn/mmhead/Q3auHgzwzM7M0wFHH4OLr12bPNZLBsd9XGaRkUbqWVS090CpNLowew/0', 'Midea美的照明', 'MzIwNjE5MTc1Mw==', '0');
INSERT INTO `users` VALUES ('11', '土巴兔广州', 'http://wx.qlogo.cn/mmhead/Q3auHgzwzM4oawDSb2I1iaKrGRJAG9d2u2FTRAIolgcEFEtaeAWuFdw/0', '土巴兔广州', 'MzU4MDAxNTY5NQ==', '0');
INSERT INTO `users` VALUES ('12', '阿拉丁照明', 'http://wx.qlogo.cn/mmhead/Q3auHgzwzM7NpBuzgXHvicR2H9NiavLhlXN3Nyk0HNOice72njgnmcBmw/0', '阿拉丁照明商城', 'MzA3MjgyMTQ2Nw==', '0');
INSERT INTO `users` VALUES ('13', '狂飙照明网', 'http://wx.qlogo.cn/mmhead/Q3auHgzwzM7nsAksO3z9NpJTQejicpXM3Zc9bVFmNSPtJpuTHibRlZRA/0', '狂飙照明网', 'MjM5MTE2Njg5NQ==', '0');
INSERT INTO `users` VALUES ('14', '照明业界资讯网', 'http://wx.qlogo.cn/mmhead/Q3auHgzwzM7bIZpt8HwLMq3TwMIwUqPzhibz6QKOBvsLOf2sIgFElpg/0', '照明业界资讯', 'MjM5NzM0ODEyNA==', '0');
INSERT INTO `users` VALUES ('15', '奥朵灯饰', 'http://img01.sogoucdn.com/app/a/100520090/oIWsFt_ZIXX2K3LS1muqiqs2iJkU', '奥朵灯饰', 'MzI0NzQ3MTM4OQ==', '0');
INSERT INTO `users` VALUES ('16', '灯饰照明共享联盟', 'http://wx.qlogo.cn/mmhead/Q3auHgzwzM4ZcwntDmbbCmXaGqTjTEDu61Ir0icmYK78Q5T2xPPJTTg/0', '灯饰照明共享联盟', 'MzA3MTYwMzAxOQ==', '0');
INSERT INTO `users` VALUES ('17', '灯饰微杂志', 'http://wx.qlogo.cn/mmhead/Q3auHgzwzM7l8eAP3AV2hYJJaAp48CjLMTanPg2Ngxop8SyTsvsnlA/0', '灯饰微杂志', 'MzIyMTA4OTQ1Nw==', '0');
INSERT INTO `users` VALUES ('18', '国际家居', 'http://wx.qlogo.cn/mmhead/Q3auHgzwzM5q8yYKicRNSicfDYT3NEy5Zr1euWDTDjlWf8Na7HMUZIvw/0', '国际家居', 'MzA3NjM4Mjg1NA==', '0');
INSERT INTO `users` VALUES ('19', '创意家居', 'http://wx.qlogo.cn/mmhead/Q3auHgzwzM6fGXP4pkXkfm9hxrHRv4GPntqoFWza49uUBibafbMe0kw/0', '创意家居生活', 'MzA3NzA1MjYxNQ==', '0');
INSERT INTO `users` VALUES ('20', '一条', 'http://wx.qlogo.cn/mmhead/Q3auHgzwzM5NanCaicLRdhAd4s6P3Ejq4QmjJlKZrjRxlN6d5AzHrZQ/0', '一条', 'MjM5MDI5OTkyOA==', '0');
INSERT INTO `users` VALUES ('21', '家居配饰师', 'http://wx.qlogo.cn/mmhead/Q3auHgzwzM4xOXcYmKNxBXlib1nbANmTrH4CDgh3Xs2GBjSHU0Jt9LQ/0', '家居配饰师', 'MzAxMjA3MDc0Nw==', '0');
INSERT INTO `users` VALUES ('22', 'LEDinside', 'http://wx.qlogo.cn/mmhead/Q3auHgzwzM7qEgiaUI80Qdf5I6RAGQd0IctSiaGAibNkvqeOFjM4BCpJw/0', 'LEDinside', 'MjM5NDgwNTAyMA==', '0');
INSERT INTO `users` VALUES ('23', '云知光照明微课堂', 'http://wx.qlogo.cn/mmhead/Q3auHgzwzM71ibrXG7hUKD5GHNpJzaN7OUfLK4ic1u3ht6mkfbQ6nDSg/0', '云知光照明微课堂', 'MzA3ODE2NTEwMA==', '0');
INSERT INTO `users` VALUES ('24', '行家说Talk', 'http://wx.qlogo.cn/mmhead/Q3auHgzwzM5YQ2lhKYL4YbJKK4ZCLzNUe4avic4CfzErcvxw1pMicNYg/0', '行家说Talk', 'MzI0MjQxODEzNQ==', '0');
INSERT INTO `users` VALUES ('25', 'OFweek半导体照明', 'http://wx.qlogo.cn/mmhead/Q3auHgzwzM5gofdrh2cSBkTcIXdgYlLFr90mXnsKQfpbP2YhndGmBA/0', 'OFweek半导体照明', 'MzA4MjQyMzIyNg==', '0');
INSERT INTO `users` VALUES ('26', '泛家居圈', 'http://wx.qlogo.cn/mmhead/Q3auHgzwzM612vBqzmTD26otydkbQs9ibKpQ4k2ZHak4WcNlibba7BOQ/0', '泛家居圈', 'MzA5NzUxMTQwNg==', '0');
INSERT INTO `users` VALUES ('27', '极有家', 'http://wx.qlogo.cn/mmhead/Q3auHgzwzM7hrV4P1yYyIw0YxsuR5ZmaYicFxnfdbXUYToHlFSjO8BA/0', '极有家', 'MzA3NzQzMzg3Mw==', '0');
INSERT INTO `users` VALUES ('28', '中国家博会CIFF', 'http://wx.qlogo.cn/mmhead/Q3auHgzwzM4yHWCWDrm9zRRrR4kichoS5oXCmYEBeMJFJ28UTicmA2Gw/0', '中国家博会CIFF', 'MzA3NTk0ODkzNg==', '0');
INSERT INTO `users` VALUES ('29', '奥普爱生活', 'http://wx.qlogo.cn/mmhead/Q3auHgzwzM7Q8OXchaQO7z6wzWqLZdVOibgO4zSE4vFlCxJZWz5Wbibw/0', '奥普爱生活', 'MzA3NDM3NzQwNw==', '0');
INSERT INTO `users` VALUES ('30', '创意家居生活', 'http://wx.qlogo.cn/mmhead/Q3auHgzwzM6fGXP4pkXkfm9hxrHRv4GPntqoFWza49uUBibafbMe0kw/0', '创意家居生活', 'MzA3NzA1MjYxNQ==', '0');
INSERT INTO `users` VALUES ('31', '涟源dengcom灯网', 'http://wx.qlogo.cn/mmhead/Q3auHgzwzM48e6xictRjdMtjOZIIQqvZahu3pGUqEhMWNpSvfYBUYsg/0', '涟源dengcom灯网', 'MzUyMTM0OTIwMw==', '0');
INSERT INTO `users` VALUES ('32', 'dengcom灯网官微', 'http://wx.qlogo.cn/mmhead/Q3auHgzwzM5k5hB4LWLqYpiaNUoq1wBSUqD5jS123zvgn3p9b25fJ9g/0', 'dengcom灯网官微', 'MzA4MjgxNDc1Ng==', '0');
INSERT INTO `users` VALUES ('33', '广东灯灯网科技有限公司', 'http://wx.qlogo.cn/mmhead/Q3auHgzwzM42icvD9PwZdszT0zSiaNt48g4ib7tsl8hxbabqPMFzQEYng/0', '广东灯灯网科技有限公司', 'MjM5MzU3MzA1NQ==', '0');
