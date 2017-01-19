/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : laravel

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-01-19 15:23:00
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `read_log`
-- ----------------------------
DROP TABLE IF EXISTS `read_log`;
CREATE TABLE `read_log` (
  `id` varchar(13) NOT NULL,
  `nid` varchar(13) NOT NULL COMMENT '小说ID',
  `cid` varchar(19) NOT NULL COMMENT '章节ID',
  `utime` int(11) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='阅读记录';

-- ----------------------------
-- Records of read_log
-- ----------------------------
INSERT INTO `read_log` VALUES ('588059dbe98e8', '587c43043ac79', '01409_587c43043ac79', '1484806619');
