/*
Navicat MySQL Data Transfer

Source Server         : 192.168.1.199
Source Server Version : 50173
Source Host           : 192.168.1.199:3306
Source Database       : db_customer_service

Target Server Type    : MYSQL
Target Server Version : 50173
File Encoding         : 65001

Date: 2016-05-04 10:52:04
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tb_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `tb_auth_group`;
CREATE TABLE `tb_auth_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '组id',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '组名',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态 1：启用  0:禁用',
  `rules` varchar(255) NOT NULL DEFAULT '' COMMENT '用户组拥有的规则id， 多个规则","隔开',
  `sub_group` varchar(100) NOT NULL DEFAULT '' COMMENT '管理组id，多个以'''',''''隔开',
  `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '说明',
  `add_time` int(10) unsigned NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='用户组表';

-- ----------------------------
-- Records of tb_auth_group
-- ----------------------------
INSERT INTO `tb_auth_group` VALUES ('1', '超级管理员', '1', '1,1,1,1,1,1,1,1,1,1,1,9,9,9,11,12,13,50,51,10,10,10,14,15,29,29,29,30,31,55,56,57,2,3,4,4,4,4,16,19,20,21,22,17,23,24,25,18,26,27,28,5,5,5,5,32,35,36,33,37,38,39,40,34,7,7,7,7,44,45,46,6,41,42,43', '2,3,4,5,6', '', '1459394340');
INSERT INTO `tb_auth_group` VALUES ('2', '系统管理员', '1', '1,1,9,9,11,12,13,50,51,10,14,15,29,30,31,2,3,4,4,4,16,19,20,21,22,5,32,35,36,33,37,38,39,40,34,7,7,7,7,44,45,46,55,56,57,1', '2,3,4,5,6', '', '1460008056');
INSERT INTO `tb_auth_group` VALUES ('3', '在线客服', '1', '1,9,11,12,13,50,51,1,2,3,32,35,36,5,7,44,45,46', '3', '', '1460008360');
INSERT INTO `tb_auth_group` VALUES ('4', '普通客服', '1', '2,3,7,44,45,46', '4', '', '1460009080');
INSERT INTO `tb_auth_group` VALUES ('5', '客户', '1', '2,3,6,41,42,43,8,47,48,49', '5', '', '1460105604');
INSERT INTO `tb_auth_group` VALUES ('6', 'newTree', '1', '1,9,11,12,13,50,51,10,14,15,29,30,31,2,3,4,4,4,4,16,16,16,19,20,17,5,5,5,5,5,5,5,5,5,32,32,32,32,32,32,32,32,35,33,33,33,33,33,39,40,34,6,6,6,6,6,6,41,41,41,41,41,41,42,43,7,44,45,46,8,8,8,8,47,48,49,55,56,57,1', '6', '', '1460444686');

-- ----------------------------
-- Table structure for tb_auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `tb_auth_group_access`;
CREATE TABLE `tb_auth_group_access` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `group_id` int(10) unsigned NOT NULL COMMENT '组id',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户-组对应表';

-- ----------------------------
-- Records of tb_auth_group_access
-- ----------------------------
INSERT INTO `tb_auth_group_access` VALUES ('1', '1');
INSERT INTO `tb_auth_group_access` VALUES ('3', '1');
INSERT INTO `tb_auth_group_access` VALUES ('6', '4');
INSERT INTO `tb_auth_group_access` VALUES ('7', '2');
INSERT INTO `tb_auth_group_access` VALUES ('9', '2');
INSERT INTO `tb_auth_group_access` VALUES ('10', '6');
INSERT INTO `tb_auth_group_access` VALUES ('17', '0');
INSERT INTO `tb_auth_group_access` VALUES ('22', '2');
INSERT INTO `tb_auth_group_access` VALUES ('23', '2');
INSERT INTO `tb_auth_group_access` VALUES ('26', '5');
INSERT INTO `tb_auth_group_access` VALUES ('27', '1');
INSERT INTO `tb_auth_group_access` VALUES ('28', '1');
INSERT INTO `tb_auth_group_access` VALUES ('29', '2');
INSERT INTO `tb_auth_group_access` VALUES ('30', '4');
INSERT INTO `tb_auth_group_access` VALUES ('31', '2');
INSERT INTO `tb_auth_group_access` VALUES ('32', '2');
INSERT INTO `tb_auth_group_access` VALUES ('33', '4');
INSERT INTO `tb_auth_group_access` VALUES ('34', '4');
INSERT INTO `tb_auth_group_access` VALUES ('35', '2');
INSERT INTO `tb_auth_group_access` VALUES ('36', '5');
INSERT INTO `tb_auth_group_access` VALUES ('37', '5');
INSERT INTO `tb_auth_group_access` VALUES ('38', '6');

-- ----------------------------
-- Table structure for tb_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `tb_auth_rule`;
CREATE TABLE `tb_auth_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则id',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '规则名  ‘控制器名/方法名’',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '中文描述',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '类型',
  `auth_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '认证类型，1：菜单   2：权限   3：菜单+权限',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态 1：启用 ， 0：禁用',
  `css` varchar(20) NOT NULL DEFAULT '' COMMENT '样式',
  `icon` varchar(30) NOT NULL DEFAULT '' COMMENT 'icon图标',
  `condition` varchar(100) NOT NULL DEFAULT '' COMMENT '额外条件',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父规则id',
  `level` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '几级菜单标识',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `add_time` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8 COMMENT='规则表';

-- ----------------------------
-- Records of tb_auth_rule
-- ----------------------------
INSERT INTO `tb_auth_rule` VALUES ('1', 'fireware/firewareList', '固件', '1', '1', '1', '', 'fa-th-large', '', '0', '1', '0', '1459992564');
INSERT INTO `tb_auth_rule` VALUES ('2', 'question/customer', '客服', '1', '1', '1', '', 'fa-smile-o', '', '0', '1', '0', '1459992587');
INSERT INTO `tb_auth_rule` VALUES ('3', 'user/personal', '个人中心', '1', '1', '1', '', 'fa-user', '', '0', '1', '0', '1459992605');
INSERT INTO `tb_auth_rule` VALUES ('4', 'user/admin', '用户管理', '1', '1', '1', '', 'fa-suitcase', '', '0', '1', '0', '1459992634');
INSERT INTO `tb_auth_rule` VALUES ('5', 'question/Qmanage', '问题管理', '1', '1', '1', '', 'fa-file-text', '', '0', '1', '0', '1459992657');
INSERT INTO `tb_auth_rule` VALUES ('6', 'user/myFireware', '我的固件', '1', '1', '1', '', 'fa-cube', '', '0', '1', '0', '1459992680');
INSERT INTO `tb_auth_rule` VALUES ('7', 'question/myQlist', '我的问题单', '1', '1', '1', '', 'fa-list-ul', '', '0', '1', '0', '1459992715');
INSERT INTO `tb_auth_rule` VALUES ('8', 'question/myQlist2', '我的提问', '1', '1', '1', '', 'fa-list-ul', '', '0', '1', '0', '1460019598');
INSERT INTO `tb_auth_rule` VALUES ('9', 'FirmwarePublish', '固件列表', '1', '1', '1', '', '', '', '1', '2', '12', '1460365672');
INSERT INTO `tb_auth_rule` VALUES ('10', 'Platform', '平台列表', '1', '1', '1', '', '', '', '1', '2', '9', '1460365687');
INSERT INTO `tb_auth_rule` VALUES ('11', 'FirmwarePublish/publish', '发布', '1', '3', '1', 'publishBtn', 'fa-paper-plane', '', '9', '3', '3', '1460365703');
INSERT INTO `tb_auth_rule` VALUES ('12', 'FirmwarePublish/delete', '删除', '1', '3', '1', 'delBtn', 'fa-trash-o', '', '9', '3', '2', '1460365720');
INSERT INTO `tb_auth_rule` VALUES ('13', 'FirmwarePublish/getFirmwarePublish', '版本描述', '1', '3', '1', 'descBtn', 'fa-tags', '', '9', '3', '0', '1460365738');
INSERT INTO `tb_auth_rule` VALUES ('14', 'Platform/add', '新增', '1', '3', '1', 'addPlatformBtn', 'fa-plus-square', '', '10', '3', '2', '1460365751');
INSERT INTO `tb_auth_rule` VALUES ('15', 'Platform/delete', '删除', '1', '3', '1', 'delPlatformBtn', 'fa-minus-square', '', '10', '3', '0', '1460365768');
INSERT INTO `tb_auth_rule` VALUES ('16', 'User', '用户管理', '1', '1', '1', '', '', '', '4', '2', '10', '1460427990');
INSERT INTO `tb_auth_rule` VALUES ('17', 'Group', '用户组管理', '1', '1', '1', '', '', '', '4', '2', '9', '1460428012');
INSERT INTO `tb_auth_rule` VALUES ('18', 'Auth', '权限管理', '1', '1', '1', '', '', '', '4', '2', '8', '1460428028');
INSERT INTO `tb_auth_rule` VALUES ('19', 'User/addUser', '创建用户', '1', '3', '1', 'userBtn', 'fa-user-plus', '', '16', '3', '4', '1460428106');
INSERT INTO `tb_auth_rule` VALUES ('20', 'User/modifyPasswd', '重置密码', '1', '3', '1', 'pwdBtn', 'fa-unlock', '', '16', '3', '3', '1460428106');
INSERT INTO `tb_auth_rule` VALUES ('21', 'User/modifyUserInfo', '修改用户', '1', '3', '1', 'editBtn', 'fa-pencil-square-o', '', '16', '3', '2', '1460428106');
INSERT INTO `tb_auth_rule` VALUES ('22', 'User/deleteUser', '删除', '1', '3', '1', 'delBtn', 'fa-user-times', '', '16', '3', '1', '1460428106');
INSERT INTO `tb_auth_rule` VALUES ('23', 'Group/addAuthGroup', '新增', '1', '3', '1', 'addUGBtn', 'fa-plus-square', '', '17', '3', '3', '1460428106');
INSERT INTO `tb_auth_rule` VALUES ('24', 'Group/editAuthGroup', '修改', '1', '3', '1', 'editUGBtn', 'fa-pencil-square-o', '', '17', '3', '2', '1460428106');
INSERT INTO `tb_auth_rule` VALUES ('25', 'Group/delAuthGroup', '删除', '1', '3', '1', 'delUGBtn', 'fa-minus-square', '', '17', '3', '1', '1460428106');
INSERT INTO `tb_auth_rule` VALUES ('26', 'Group/addAuthRule', '新增', '1', '3', '1', 'addRuleBtn', 'fa-plus-square', '', '18', '3', '3', '1460428106');
INSERT INTO `tb_auth_rule` VALUES ('27', 'Group/editAuthRule', '修改', '1', '3', '1', 'editRuleBtn', 'fa-pencil-square-o', '', '18', '3', '2', '1460428106');
INSERT INTO `tb_auth_rule` VALUES ('28', 'Group/delAuthRule', '删除', '1', '3', '1', 'delRuleBtn', 'fa-minus-square', '', '18', '3', '1', '1460428106');
INSERT INTO `tb_auth_rule` VALUES ('29', 'VendorId', 'vendorID列表', '1', '1', '1', '', '', '', '1', '2', '8', '1460445102');
INSERT INTO `tb_auth_rule` VALUES ('30', 'Vendorid/add', '新增', '1', '3', '1', 'addVendorIDBtn', 'fa-plus-square', '', '29', '3', '2', '1460445119');
INSERT INTO `tb_auth_rule` VALUES ('31', 'Vendorid/delete', '删除', '1', '3', '1', 'delVendorIDBtn', 'fa-minus-square', '', '29', '3', '0', '1460445136');
INSERT INTO `tb_auth_rule` VALUES ('32', 'Question/getComQuestionList', '常见问题', '1', '1', '1', '', '', '', '5', '2', '3', '1460512206');
INSERT INTO `tb_auth_rule` VALUES ('33', 'Question/getQuestionList', '问题单管理', '1', '1', '1', '', '', '', '5', '2', '2', '1460512227');
INSERT INTO `tb_auth_rule` VALUES ('34', 'Question/getCategory', '分类管理', '1', '1', '1', '', '', '', '5', '2', '1', '1460512258');
INSERT INTO `tb_auth_rule` VALUES ('35', 'Question/addCommonQuestion', '发布', '1', '3', '1', 'addBtn', 'fa-paper-plane', '', '32', '3', '2', '1460512334');
INSERT INTO `tb_auth_rule` VALUES ('36', 'Question/deleteComQuestion', '删除', '1', '3', '1', 'delBtn', 'fa-trash-o', '', '32', '3', '1', '1460512376');
INSERT INTO `tb_auth_rule` VALUES ('37', 'Question/assignQuestion', '指派', '1', '3', '1', 'assignBtn', 'fa-external-link', '', '33', '3', '4', '1460512459');
INSERT INTO `tb_auth_rule` VALUES ('38', 'checkAll', '全选', '1', '3', '1', 'delBtn', 'fa-check-square-o', '', '33', '3', '3', '1460512523');
INSERT INTO `tb_auth_rule` VALUES ('39', 'myAssignBtn', '查看指派', '1', '3', '1', 'myAssignBtn', 'fa-eye', '', '33', '3', '2', '1460512553');
INSERT INTO `tb_auth_rule` VALUES ('40', 'allQuesBtn', '查看全部', '1', '3', '1', 'allQuesBtn', 'fa-bars', '', '33', '3', '1', '1460512576');
INSERT INTO `tb_auth_rule` VALUES ('41', 'myFireware', '我的固件', '1', '1', '1', '', '', '', '6', '2', '0', '1460529848');
INSERT INTO `tb_auth_rule` VALUES ('42', 'FirmwarePublish/getFirmwarePublish', '版本描述', '1', '1', '1', 'descBtn', 'fa-tags', '', '41', '2', '0', '1460530112');
INSERT INTO `tb_auth_rule` VALUES ('43', 'FirmwarePublish/firmComment', '评论', '1', '3', '1', 'sayBtn', 'fa-commenting', '', '41', '3', '0', '1460530849');
INSERT INTO `tb_auth_rule` VALUES ('44', 'unsolve', '未解决', '1', '1', '1', '', '', '', '7', '2', '3', '1460603348');
INSERT INTO `tb_auth_rule` VALUES ('45', 'solve', '已解决', '1', '1', '1', '', '', '', '7', '2', '2', '1460603385');
INSERT INTO `tb_auth_rule` VALUES ('46', 'allQues', '全部问题单', '1', '1', '1', '', '', '', '7', '2', '1', '1460603417');
INSERT INTO `tb_auth_rule` VALUES ('47', 'unsolve', '未解决', '1', '1', '1', '', '', '', '8', '2', '3', '1460603469');
INSERT INTO `tb_auth_rule` VALUES ('48', 'solve', '已解决', '1', '1', '1', '', '', '', '8', '2', '2', '1460603485');
INSERT INTO `tb_auth_rule` VALUES ('49', 'allQues', '全部问题单', '1', '1', '1', '', '', '', '8', '2', '1', '1460603502');
INSERT INTO `tb_auth_rule` VALUES ('50', 'FirmwarePublish/firmComment', '评论', '1', '3', '1', 'sayBtn', 'fa-commenting', '', '9', '3', '0', '1460682945');
INSERT INTO `tb_auth_rule` VALUES ('51', 'FirmwarePublish/delCommet', '删除固件评论', '1', '2', '1', '', '', '', '9', '3', '0', '1460970390');
INSERT INTO `tb_auth_rule` VALUES ('55', 'Brands', '品牌列表', '1', '1', '1', '', '', '', '1', '2', '0', '1461651148');
INSERT INTO `tb_auth_rule` VALUES ('56', 'Brands/addBrand', '新增', '1', '3', '1', 'addBrandBtn', 'fa-plus-square', '', '55', '3', '4', '1461658351');
INSERT INTO `tb_auth_rule` VALUES ('57', 'Brands/delBrand', '删除', '1', '3', '1', 'delBrandBtn', 'fa-minus-square', '', '55', '3', '3', '1461658393');

-- ----------------------------
-- Table structure for tb_brands
-- ----------------------------
DROP TABLE IF EXISTS `tb_brands`;
CREATE TABLE `tb_brands` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '品牌id',
  `brand_name` varchar(64) NOT NULL DEFAULT '' COMMENT '品牌名',
  `customer` varchar(32) NOT NULL DEFAULT '' COMMENT '品牌对应客户名',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `admin_user` varchar(32) NOT NULL DEFAULT '' COMMENT '添加人用户id',
  `add_time` int(10) unsigned NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `user` (`customer`) USING BTREE,
  KEY `brand_name` (`brand_name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='品牌表';

-- ----------------------------
-- Records of tb_brands
-- ----------------------------
INSERT INTO `tb_brands` VALUES ('4', '213', 'customer', '21', 'duxiaoyao', '1461754150');
INSERT INTO `tb_brands` VALUES ('8', 'dfdf', 'ztokay', 'dsfdsf', 'duxiaoyao', '1461754517');
INSERT INTO `tb_brands` VALUES ('9', 'ads', 'ztokay', 'asd', 'duxiaoyao', '1461807786');
INSERT INTO `tb_brands` VALUES ('10', '213', 'ztokay', '213', 'duxiaoyao', '1461807906');
INSERT INTO `tb_brands` VALUES ('11', 'r4ew', 'customer', 'wre', 'duxiaoyao', '1461807957');
INSERT INTO `tb_brands` VALUES ('16', 'JAV', 'customer', '', 'service', '1461822355');

-- ----------------------------
-- Table structure for tb_chat_record
-- ----------------------------
DROP TABLE IF EXISTS `tb_chat_record`;
CREATE TABLE `tb_chat_record` (
  `id` int(32) unsigned NOT NULL AUTO_INCREMENT,
  `sender` varchar(32) NOT NULL COMMENT '发送者用户名',
  `receiver` varchar(32) NOT NULL COMMENT '接收者用户名',
  `send_time` int(32) unsigned NOT NULL COMMENT '发送时间，时间戳',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='聊天记录表';

-- ----------------------------
-- Records of tb_chat_record
-- ----------------------------

-- ----------------------------
-- Table structure for tb_common_question
-- ----------------------------
DROP TABLE IF EXISTS `tb_common_question`;
CREATE TABLE `tb_common_question` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '问题单id',
  `cate_id_1` int(10) unsigned NOT NULL COMMENT '问题分类1',
  `cate_id_2` int(10) unsigned NOT NULL COMMENT '问题分类2',
  `content` text NOT NULL COMMENT '问题内容',
  `reply` text NOT NULL COMMENT '回复内容',
  `ask_attach` varchar(500) NOT NULL DEFAULT '' COMMENT '提问附件',
  `reply_attach` varchar(500) NOT NULL DEFAULT '' COMMENT '回答附件，',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0:已关闭   1:显示中',
  `typical` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1:典型问题   0::普通',
  `admin_id` int(10) unsigned NOT NULL COMMENT '后台管理员id',
  `add_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8 COMMENT='常见问题单表';

-- ----------------------------
-- Records of tb_common_question
-- ----------------------------
INSERT INTO `tb_common_question` VALUES ('73', '115', '116', '下载桌面布局失败？', '检查sn和mac', '', '', '1', '1', '9', '1459393701');
INSERT INTO `tb_common_question` VALUES ('74', '61', '105', '434', '3434', '', '', '1', '1', '7', '1459405325');
INSERT INTO `tb_common_question` VALUES ('77', '61', '105', '111111111111111111111', '1111111111111111111111111', '', '', '1', '1', '7', '1459405558');
INSERT INTO `tb_common_question` VALUES ('79', '61', '105', '22', '44', '', '', '1', '1', '7', '1459406111');
INSERT INTO `tb_common_question` VALUES ('83', '115', '116', 'test', 'test', '', '', '1', '1', '22', '1459998125');
INSERT INTO `tb_common_question` VALUES ('84', '115', '116', 'aaaaa', 'aaaa', '', '', '1', '1', '22', '1459998135');
INSERT INTO `tb_common_question` VALUES ('85', '115', '116', 'bbb', 'bbb', '', '', '1', '1', '22', '1459998147');
INSERT INTO `tb_common_question` VALUES ('86', '115', '116', 'ccc', 'ccc', '', '', '1', '1', '22', '1459998162');
INSERT INTO `tb_common_question` VALUES ('91', '123', '126', '固件下载失败？', 'tfdsfvts', 's:24:\"C:\\fakepath\\app_icon.png\";', 's:28:\"C:\\fakepath\\登录窗口.jpg\";', '1', '1', '9', '1460083743');
INSERT INTO `tb_common_question` VALUES ('92', '115', '117', 'gdybjhkl', 'ytftyfhi', '', '', '1', '0', '9', '1460084168');
INSERT INTO `tb_common_question` VALUES ('93', '115', '116', '这是桌面-》桌面布局的典型问题', '这是回复内容', 's:22:\"新建文本文档.txt\";', 's:16:\"需求分析.txt\";', '1', '1', '3', '1460085164');
INSERT INTO `tb_common_question` VALUES ('94', '146', '147', 'ThinkPad E520总是蓝屏，肿么解决。。', '砸了不就行了。', 's:16:\"需求分析.txt\";', 's:22:\"56d3e7ffb7957_1024.jpg\";', '1', '1', '3', '1460086452');
INSERT INTO `tb_common_question` VALUES ('95', '146', '148', '电脑-》台式电脑问题描述', '电脑-》台式电脑问题 回复内容', 'a:2:{i:0;s:49:\"Upload/Question/20160408/L/56d3e7ffb7957_1024.jpg\";i:1;s:32:\"Upload/Question/20160408/p/a.txt\";}', '', '1', '1', '3', '1460093989');
INSERT INTO `tb_common_question` VALUES ('96', '146', '147', '测试发布常见问题附件', '这是回复', 'a:2:{i:0;s:49:\"Upload/Question/20160408/r/56d3e7ffb7957_1024.jpg\";i:1;s:32:\"Upload/Question/20160408/r/a.txt\";}', 'a:2:{i:0;s:32:\"Upload/Question/20160408/i/b.txt\";i:1;s:32:\"Upload/Question/20160408/F/d.txt\";}', '1', '1', '3', '1460094870');
INSERT INTO `tb_common_question` VALUES ('97', '115', '116', '桌面布局错误', '检查匹配mac和sn', 'a:1:{i:0;s:43:\"Upload/Question/20160411/x/登录窗口.jpg\";}', 'a:1:{i:0;s:43:\"Upload/Question/20160411/J/忘记密码.png\";}', '1', '1', '9', '1460337207');
INSERT INTO `tb_common_question` VALUES ('98', '115', '116', 'ccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccc', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '', '', '1', '0', '9', '1460530307');
INSERT INTO `tb_common_question` VALUES ('99', '115', '116', '桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局', '回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复', '', '', '1', '1', '3', '1460605038');
INSERT INTO `tb_common_question` VALUES ('100', '115', '116', '桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面', '回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复', '', '', '1', '1', '3', '1460605111');
INSERT INTO `tb_common_question` VALUES ('101', '115', '116', '桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局桌面布局', '回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复回复', '', '', '1', '1', '3', '1460605224');
INSERT INTO `tb_common_question` VALUES ('102', '151', '152', 'xadsfsdfsgg测试测试asdfsfgdsgfreggjuhfgwga测试测试测试测试sfsetgfgtherfrth测试测试sfregfdhd测试sdgfethsdg测试测试dsfdedvgbfdhb测试zfegdfh测试sfgerhgfh测试vdsgdhfhje测试sfdgh测试sfdgdfh测试测试sfetgr测试sfergfdh测试', 'aaa aaaaaaaaaa 回复aaaasdwaf回复回复fcsdfdg回复sfgvfdh回复gsdgfdhasfer回复回复回复sfwafdhsar回复回复sdsghwafhaw回复回复fasfdghh回复aewghse回复edrfsdgfh回复aresefrdh回复回复fsdesag回复wadsf', '', '', '1', '1', '3', '1460704233');
INSERT INTO `tb_common_question` VALUES ('103', '151', '152', 'xadsfsdfsgg测试测试asdfsfgdsgfreggjuhfgwga测试测试测试测试sfsetgfgtherfrth测试测试sfregfdhd测试sdgfethsdg测试测试dsfdedvgbfdhb测试zfegdfh测试sfgerhgfh测试vdsgdhfhje测试sfdgh测试sfdgdfh测试测试sfetgr测试sfergfdh测试dsafdghdsh测试saafsg测试fdsg测试xadsfsdfsgg测试测试asdfsfgdsgfreggjuhfgwga测试测试测试测试sfsetgfgtherfrth测试测试sfregfdhd测试sdgfethsdg测试测试dsfdedvgbfdhb测试zfegdfh测试sfgerhgfh测试vdsgdhfhje测试sfdgh测试sfdgdfh测试测试sfetgr测试sfergfdh测试', 'aaa aaaaaaaaaa 回复aaaasdwaf回复回复fcsdfdg回复sfgvfdh回复gsdgfdhasfer回复回复回复sfwafdhsar回复回复sdsghwafhaw回复回复fasfdghh回复aewghse回复edrfsdgfh回复aresefrdh回复回复fsdesag回复wadsfaaa aaaaaaaaaa 回复aaaasdwaf回复回复fcsdfdg回复sfgvfdh回复gsdgfdhasfer回复回复回复sfwafdhsar回复回复sdsghwafhaw回复回复fasfdghh回复aewghse回复edrfsdgfh回复aresefrdh回复回复fsdesag回复wadsf', '', '', '1', '1', '3', '1460704335');
INSERT INTO `tb_common_question` VALUES ('104', '151', '152', 'dswarewtswatryawwdsgwafdstdhehedaseyhfgdjefweshdhserjwrdeyw', 'ggfdhrehdjfsedrcfsdegfd', 'a:1:{i:0;s:56:\"Upload/Question/20160415/S/客服系统_20160304_1831.rp\";}', '', '1', '1', '26', '1460707242');
INSERT INTO `tb_common_question` VALUES ('105', '146', '148', '测试常见问题附件', '测试常见问题附件测试常见问题附件测试常见问题附件', 'a:2:{i:0;s:32:\"Upload/Question/20160415/j/c.txt\";i:1;s:32:\"Upload/Question/20160415/U/d.txt\";}', 'a:1:{i:0;s:49:\"Upload/Question/20160415/H/56d3e7ffb7957_1024.jpg\";}', '1', '1', '3', '1460707322');
INSERT INTO `tb_common_question` VALUES ('106', '115', '150', 'desktop update desktop update desktop update desktop update desktop update desktop update desktop update desktop update desktop update desktop update desktop update desktop update desktop update desktop update desktop update desktop update desktop update', 'update update update update update update update', 'a:1:{i:0;s:43:\"Upload/Question/20160415/O39Dtt/appinfo.xml\";}', 'a:1:{i:0;s:46:\"Upload/Question/20160415/aNKWIC/moduleinfo.xml\";}', '1', '1', '26', '1460713021');
INSERT INTO `tb_common_question` VALUES ('107', '146', '148', 'hello', '发发发发', '', '', '1', '1', '3', '1461830573');
INSERT INTO `tb_common_question` VALUES ('108', '146', '148', 'fasdfdsaf', 'vbrggrgrg', '', '', '1', '1', '3', '1461830815');
INSERT INTO `tb_common_question` VALUES ('109', '146', '147', '测试发布常见问题文件上传优化描述', '测试发布常见问题文件上传优化描述回复', 'a:2:{i:0;s:45:\"Upload/Question/20160428/hxwP9X/baidu_web.png\";i:1;s:40:\"Upload/Question/20160428/BxvDoh/test.txt\";}', 'a:2:{i:0;s:40:\"Upload/Question/20160428/RxOMsn/test.txt\";i:1;s:42:\"Upload/Question/20160428/7GhQ9G/测试.txt\";}', '1', '0', '3', '1461840167');
INSERT INTO `tb_common_question` VALUES ('110', '115', '150', 'abcdefghijklmn', 'opqrstuvwxyz', '', '', '1', '1', '3', '1461908378');
INSERT INTO `tb_common_question` VALUES ('111', '115', '150', '1234567890', '9876543210', '', '', '1', '0', '3', '1461908432');
INSERT INTO `tb_common_question` VALUES ('112', '123', '125', 'qwerty', 'poiuytr', '', '', '1', '0', '3', '1461908466');

-- ----------------------------
-- Table structure for tb_common_question_category
-- ----------------------------
DROP TABLE IF EXISTS `tb_common_question_category`;
CREATE TABLE `tb_common_question_category` (
  `cate_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `cate_name` varchar(32) NOT NULL DEFAULT '' COMMENT '分类名',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父分类id,0:表示根分类',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '排序',
  `if_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1：显示   0：不显示',
  `remark` varchar(50) NOT NULL DEFAULT '' COMMENT '备注',
  `add_time` int(10) unsigned NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`cate_id`),
  KEY `cate_name` (`cate_name`) USING BTREE,
  KEY `parent_id` (`parent_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=159 DEFAULT CHARSET=utf8 COMMENT='常见问题分类表';

-- ----------------------------
-- Records of tb_common_question_category
-- ----------------------------
INSERT INTO `tb_common_question_category` VALUES ('12', '分类1', '63', '0', '1', '', '1458729287');
INSERT INTO `tb_common_question_category` VALUES ('13', '分类2', '63', '0', '1', '', '1458729287');
INSERT INTO `tb_common_question_category` VALUES ('14', '分类3', '63', '0', '1', '', '1458729287');
INSERT INTO `tb_common_question_category` VALUES ('61', '常见问题一级分类1', '0', '255', '1', '常见问题一级分类1', '1458810704');
INSERT INTO `tb_common_question_category` VALUES ('63', '1级常用分类', '0', '255', '1', '*****', '1458811887');
INSERT INTO `tb_common_question_category` VALUES ('91', '常见问题分类一级分类', '0', '255', '1', '常见问题分类', '1459146410');
INSERT INTO `tb_common_question_category` VALUES ('102', '双', '99', '255', '1', '', '1459152251');
INSERT INTO `tb_common_question_category` VALUES ('103', '月月月月', '99', '255', '1', '', '1459152262');
INSERT INTO `tb_common_question_category` VALUES ('105', '2121', '61', '255', '1', '', '1459152462');
INSERT INTO `tb_common_question_category` VALUES ('107', '33', '91', '255', '1', '', '1459152485');
INSERT INTO `tb_common_question_category` VALUES ('109', '双枪', '91', '255', '1', '', '1459152660');
INSERT INTO `tb_common_question_category` VALUES ('110', '仍需', '91', '255', '1', '', '1459152664');
INSERT INTO `tb_common_question_category` VALUES ('111', '人', '91', '255', '1', '', '1459152668');
INSERT INTO `tb_common_question_category` VALUES ('112', 'aaaaaaaaaaaaaaaaaaaaa', '91', '255', '1', '', '1459153391');
INSERT INTO `tb_common_question_category` VALUES ('115', '桌面', '0', '255', '1', '', '1459244617');
INSERT INTO `tb_common_question_category` VALUES ('123', '固件', '0', '255', '1', '', '1459491605');
INSERT INTO `tb_common_question_category` VALUES ('124', '固件升级', '123', '255', '1', '', '1459491616');
INSERT INTO `tb_common_question_category` VALUES ('125', '固件版本', '123', '255', '1', '', '1459491632');
INSERT INTO `tb_common_question_category` VALUES ('126', '固件下载', '123', '255', '1', '', '1459491639');
INSERT INTO `tb_common_question_category` VALUES ('131', 'a', '115', '255', '1', '', '1460011090');
INSERT INTO `tb_common_question_category` VALUES ('132', 'b', '115', '255', '1', '', '1460011094');
INSERT INTO `tb_common_question_category` VALUES ('133', 'c', '115', '255', '1', '', '1460011097');
INSERT INTO `tb_common_question_category` VALUES ('134', 'd', '115', '255', '1', '', '1460011100');
INSERT INTO `tb_common_question_category` VALUES ('135', 'e', '115', '255', '1', '', '1460011102');
INSERT INTO `tb_common_question_category` VALUES ('136', 'f', '115', '255', '1', '', '1460011106');
INSERT INTO `tb_common_question_category` VALUES ('146', '电脑', '0', '255', '1', '', '1460086256');
INSERT INTO `tb_common_question_category` VALUES ('147', '笔记本电脑', '146', '255', '1', '', '1460086264');
INSERT INTO `tb_common_question_category` VALUES ('148', '台式电脑', '146', '255', '1', '', '1460086270');
INSERT INTO `tb_common_question_category` VALUES ('150', '桌面更新', '115', '255', '1', '', '1460623794');
INSERT INTO `tb_common_question_category` VALUES ('151', '开机', '0', '255', '1', '', '1460624478');
INSERT INTO `tb_common_question_category` VALUES ('152', '开不了机', '151', '255', '1', '', '1460624503');
INSERT INTO `tb_common_question_category` VALUES ('153', '1564', '115', '255', '1', '', '1460628525');
INSERT INTO `tb_common_question_category` VALUES ('154', '1634535', '115', '255', '1', '', '1460628538');
INSERT INTO `tb_common_question_category` VALUES ('155', '桌面布局', '115', '255', '1', '', '1460628555');
INSERT INTO `tb_common_question_category` VALUES ('158', '654985', '115', '255', '1', '', '1460689498');

-- ----------------------------
-- Table structure for tb_firmware_comment
-- ----------------------------
DROP TABLE IF EXISTS `tb_firmware_comment`;
CREATE TABLE `tb_firmware_comment` (
  `id` int(32) unsigned NOT NULL AUTO_INCREMENT,
  `publish_id` int(32) unsigned NOT NULL COMMENT '固件发布id，对应 tb_firmware_publish.id',
  `content` text NOT NULL COMMENT '评论内容',
  `time` int(32) unsigned NOT NULL COMMENT '评论时间',
  `user` varchar(32) NOT NULL COMMENT '评论者用户名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='固件评论表';

-- ----------------------------
-- Records of tb_firmware_comment
-- ----------------------------
INSERT INTO `tb_firmware_comment` VALUES ('3', '102', 'hjgjgjhgk', '1460103512', 'customer');
INSERT INTO `tb_firmware_comment` VALUES ('6', '102', 'grtsrtr;jh7yedckgtrs\n', '1460538830', 'customer');
INSERT INTO `tb_firmware_comment` VALUES ('7', '106', '我是第一个来评论的', '1460685265', 'admin');
INSERT INTO `tb_firmware_comment` VALUES ('8', '106', '再评论一次', '1460685272', 'admin');
INSERT INTO `tb_firmware_comment` VALUES ('12', '106', 'comment\n', '1461035664', 'customer');
INSERT INTO `tb_firmware_comment` VALUES ('13', '106', 'comment31234', '1461035718', 'customer');
INSERT INTO `tb_firmware_comment` VALUES ('17', '102', 'gftedsrswea', '1461036183', 'customer');
INSERT INTO `tb_firmware_comment` VALUES ('20', '112', 'rswru', '1461830087', 'customer');
INSERT INTO `tb_firmware_comment` VALUES ('21', '112', '000', '1461830093', 'customer');

-- ----------------------------
-- Table structure for tb_firmware_publish
-- ----------------------------
DROP TABLE IF EXISTS `tb_firmware_publish`;
CREATE TABLE `tb_firmware_publish` (
  `id` int(32) unsigned NOT NULL AUTO_INCREMENT COMMENT '固件发布id',
  `vendor_id` varchar(8) NOT NULL,
  `platform` varchar(32) NOT NULL COMMENT '平台名',
  `firmware_ver` varchar(64) NOT NULL COMMENT '固件版本',
  `md5` varchar(32) NOT NULL COMMENT 'MD5',
  `customer` varchar(32) NOT NULL COMMENT '客户用户名',
  `brand_id` int(10) unsigned NOT NULL COMMENT '品牌id，tb_brands表id',
  `path` varchar(255) NOT NULL COMMENT '百度盘下载地址',
  `passwd` varchar(4) NOT NULL COMMENT '百度盘下载密码',
  `publisher` varchar(32) NOT NULL COMMENT '发布者用户名',
  `pub_time` int(32) unsigned NOT NULL COMMENT '发布时间，时间戳',
  `version_desc` text NOT NULL COMMENT '版本描述',
  `unique_md5_str` varchar(32) NOT NULL DEFAULT '' COMMENT '固件唯一性区分，customer+brand_id+vendor_id+platform+firmware_ver+md5+path的md5加密串',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_md5_str` (`unique_md5_str`) USING BTREE,
  KEY `brand_id` (`brand_id`) USING BTREE,
  KEY `customer` (`customer`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8 COMMENT='固件发布表';

-- ----------------------------
-- Records of tb_firmware_publish
-- ----------------------------
INSERT INTO `tb_firmware_publish` VALUES ('101', '1dw14325', 'caewcfdasfcewas', '124645', '3CD4DF466E93DC6696D7A9BA7E1F9E6D', 'customer', '0', 'http://www.qq.com', '1235', 'service', '1460025420', 'fdsgsdfh', '17d6139a97ac69851677060eb5d11166');
INSERT INTO `tb_firmware_publish` VALUES ('102', '3126', '2r3 cdr3wedec', '31234', '3CD4DF466E93DC6696D7A9BA7E1F9E6D', 'customer', '0', 'http://www.google.com', '2345', 'service', '1460077701', 'gsdgfh', '58c1265a9f23c0e3e53395b901690f0d');
INSERT INTO `tb_firmware_publish` VALUES ('106', '1235', 'qrer13215qwre', '2356', 'sr324dqw2325wfwqe3425wgsw42365w5', 'customer', '0', 'http://www.12306.cn', '1679', 'admin', '1460624042', 'gedtsrwewfw243rw2', 'a8fa12df8e1785504bef9bdb8c6af02a');
INSERT INTO `tb_firmware_publish` VALUES ('109', '0103', 'vxvbnxdf', 'dassad', 'sr324dqw2325wfwqe3425wgsw42365w5', 'ztokay', '11', 'http://www.12306.cn', '1234', 'duxiaoyao', '1461811870', 'sadfad', 'ae78173f8f3a1550b13cda921b71fc44');
INSERT INTO `tb_firmware_publish` VALUES ('110', '1dw14325', 'vxcbvxbcvn', 'asdasd', '3CD4DF466E93DC6696D7A9BA7E1F9E6D', 'testtttttttt', '8', 'http://www.google.com', '1234', 'duxiaoyao', '1461813342', 'tghfgdfh', '658a3fb72aeb24cca7ac4f7c3f2a3e29');
INSERT INTO `tb_firmware_publish` VALUES ('111', '3126', 'qrer13215qwre', 'rasfdsfgw', 'dsarfawrq42r141de14e214t12432522', 'customer', '16', 'http://www.baidu.com', '1233', 'service', '1461822513', 'adsafasgfd', '40997b07d23b8758774188c677188966');
INSERT INTO `tb_firmware_publish` VALUES ('112', '3126', 'qrer13215qwre', 'rasfdsfgw', 'ae12edw1414rdwq41e3245fw321ea241', 'customer', '16', 'http://www.sogou.com', '1235', 'service', '1461822981', 'sdasfdsgag', 'ce58986f148638784bf316918fe9f86b');

-- ----------------------------
-- Table structure for tb_firmware_publish_history
-- ----------------------------
DROP TABLE IF EXISTS `tb_firmware_publish_history`;
CREATE TABLE `tb_firmware_publish_history` (
  `id` int(32) unsigned NOT NULL COMMENT '固件发布id',
  `vendor_id` varchar(8) NOT NULL,
  `platform` varchar(32) NOT NULL COMMENT '平台名',
  `firmware_ver` varchar(64) NOT NULL COMMENT '固件版本',
  `md5` varchar(32) NOT NULL COMMENT 'MD5',
  `customer` varchar(32) NOT NULL COMMENT '客户用户名',
  `brand` varchar(100) NOT NULL DEFAULT '' COMMENT '品牌',
  `path` varchar(255) NOT NULL COMMENT '百度盘下载地址',
  `passwd` varchar(4) NOT NULL COMMENT '百度盘下载密码',
  `publisher` varchar(32) NOT NULL COMMENT '发布者用户名',
  `pub_time` int(32) unsigned NOT NULL COMMENT '发布时间，时间戳',
  `version_desc` text NOT NULL COMMENT '版本描述',
  `unique_md5_str` varchar(32) NOT NULL DEFAULT '' COMMENT '固件唯一性区分，customer+brand_id+vendor_id+platform+firmware_ver+md5+path的md5加密串'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='固件发布表';

-- ----------------------------
-- Records of tb_firmware_publish_history
-- ----------------------------
INSERT INTO `tb_firmware_publish_history` VALUES ('1', 'aaa', 'android', '1.2.3', '47bce5c74f589f4867dbd57e9ca9f808', 'tester', '', 'http://www.baidu.com', '1234', 'publisher', '1458110180', '这是版本描述1', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('2', 'aaa', 'ios', '1.2.3', '47bce5c74f589f4867dbd57e9ca9f808', 'tester', '', 'http://www.baidu.com', '1234', 'publisher', '1458110181', '这是版本描述2', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('54', 'time7168', 'imag', 'dfasf', '47bce5c74f589f4867dbd57e9ca9f808', 'dsfaaewfcd', '', 'http://www.baidu.com', 'fsdf', 'ecjon', '1458280905', 'sdfdsf', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('57', '1dw14325', 'VSOON', '12.34.56', 'e10adc3949ba59abbe56e057f20f883e', 'dsfaaewfcd', '', 'http://www.qq.com', '2345', 'service', '1458610778', 'ssfesg', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('58', 'fdasfds', 'imag', '1.2.3', '05b28d17a7b6e7024b6e5d8cc43a8bf7', 'aaa', '', 'http://www.baidu.com', '1122', 'service', '1458631891', 'zzz', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('59', 'fdasfds', 'imag', '2.3.4', '05b28d17a7b6e7024b6e5d8cc43a8bf7', 'aaa', '', 'http://www.baidu.com', '1324', 'service', '1458632104', 'wdef', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('75', 'fdasfds', 'imag', '12.3', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'aaa', '', 'http://www.qq.com', '4651', 'service', '1458728808', 'asfwt', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('77', 'fdasfds', 'new Platform', '1.2.4', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'aaa', '', 'http://www.qq.com', '5649', 'service', '1458730081', 'fsfasg', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('60', '1dw14325', 'VSOON', '23.45.67', '05b28d17a7b6e7024b6e5d8cc43a8bf7', 'fdasfds', '', 'http://www.google.com', '4321', 'service', '1458632350', 'dfgstewyg', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('82', '1122', 'new Platform', '1.321', '05b28d17a7b6e7024b6e5d8cc43a8bf7', 'dsfaaewfcd', '', 'http://www.baidu.com/fdasfds', 'feaw', 'ecjon', '1458784821', 'fvaewcfeaw', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('63', '3126', 'VSOON', '1.1', '05b28d17a7b6e7024b6e5d8cc43a8bf7', 'dsfaaewfcd', '', 'http://www.baidu.com/fadsfads', 'fdsa', 'ecjon', '1458715689', 'fadsfdsafdsafds', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('86', '1122', 'VSOON', '1.1', '05b28d17a7b6e7024b6e5d8cc43a8bf7', 'test1111111', '', 'http://www.baidu.com/521', 'fdsa', 'ecjon', '1458785869', 'fdsafds', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('61', '1dw14325', 'VSOON', '1.2.3', '05b28d17a7b6e7024b6e5d8cc43a8bf7', 'dsfaaewfcd', '', 'http://www.google.com', '2356', 'service', '1458632969', 'gsfw', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('85', '3126', 'VSOON', '1.1', '05b28d17a7b6e7024b6e5d8cc43a8bf7', 'test1111111', '', 'http://www.baidu.com/fdsaf', 'vasd', 'ecjon', '1458785181', 'dfavdsf', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('62', '1122', 'imag', '1.1', '05b28d17a7b6e7024b6e5d8cc43a8bf7', 'test1111111', '', 'http://www.baidu.com/fefefe', 'fdas', 'ecjon', '1458703928', 'fvaewfvaewfc', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('84', '1122', 'new Platform', '5.2.1', '05b28d17a7b6e7024b6e5d8cc43a8bf7', 'test1111111', '', 'http://www.baidu.com/fdasf', 'fdsf', 'ecjon', '1458785133', 'gdasgds', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('64', '1dw14325', 'newer', '1234', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'test1111111', '', 'http://www.google.com', '2222', 'service', '1458715882', 'czdsg', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('67', '1dw14325', 'platform', '1324', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'dsfaaewfcd', '', 'http://www.google.com', '3134', 'service', '1458716295', 'dasdsfgdgag', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('68', '1122', 'VSOON', '1324', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'test1111111', '', 'http://www.com', '4353', 'service', '1458716329', 'dadsfag', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('80', '1122', 'newer', '1.62', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'dsfaaewfcd', '', 'http://www.baidu.com/adfds', 'fdsa', 'ecjon', '1458783658', 'fdasfvaewf', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('81', '6006', 'VSOON', '1.111', '05b28d17a7b6e7024b6e5d8cc43a8bf7', 'aaa', '', 'http://www.baidu.com/fdsaf', 'vgea', 'ecjon', '1458784131', 'vgdasfvds', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('83', '1122', 'new Platform', '1.1', '05b28d17a7b6e7024b6e5d8cc43a8bf7', 'test1111111', '', 'http://www.baidu.com/fdadsf', 'fvda', 'ecjon', '1458785050', 'fdsaf', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('87', '1122', 'new Platform', '1.1', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'dsfaaewfcd', '', 'http://www.google.com/fdasfds', 'fasd', 'ecjon', '1458786733', 'fdasfds', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('88', '1235', 'VSOON', '4.5.6', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'test1111111', '', 'http://www.a.com', '1345', 'service', '1458786957', 'hfgfdgj', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('89', '3126', 'new Platform', '1.1', '05b28d17a7b6e7024b6e5d8cc43a8bf7', 'dsfaaewfcd', '', 'http://www.ab.com/fsdf', 'fdas', 'ecjon', '1458787155', 'fdasfdasfds', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('90', '1dw14325', '1dewsdc3', '7.8.9', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'aaa', '', 'http://www.abc.com', '1346', 'service', '1458787835', 'szres', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('91', '1235', '1dewsdc3', 'dadfg', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'dsfaaewfcd', '', 'http://www.a.com', '1324', 'service', '1458787908', 'fdsddg', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('92', '1dw14325', '2r3 cdr3wedec', 'dfghs', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'dsfaaewfcd', '', 'http://www.a.com', '1342', 'service', '1458787941', 'zxfvxgh', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('93', '1122', '324324', 'kjokinj', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'test1111111', '', 'http://www.a.cn', '1264', 'service', '1458788010', 'niniuniujn', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('94', '1dw14325', '2r3 cdr3wedec', '1eqw3', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'test1111111', '', 'http://www.a.c.com', '1645', 'service', '1458788108', 'zfsdg', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('95', '1dw14325', '1dewsdc3', 'sfsgws', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'dsfaaewfcd', '', 'http://www.a.net', 'a1b2', 'service', '1458788711', 'zcxvb', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('96', '0103', 'RK3128 2.1', '2.2.1-R-20160303.1915', 'BC7B36FE4D2924E49800D9B3DC4A325C', 'test1111111', '', 'http://www.baidu.com/s/HHHHHHHH', 'dfsj', 'ecjon', '1458797940', 'asgdsafdsa', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('97', '1dw14325', '2r3 cdr3wedec', 'arsfgdf', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'test1111111', '', 'http://www.baidu', '1562', 'service', '1458809612', 'cdsfsdgshd', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('98', '0103', '2r3 cdr3wedec', '6.1.2', 'e10adc3949ba59abbe56e057f20f883e', 'dsfaaewfcd', '', 'http://www.baidu.com', '5236', 'admin', '1458884426', '6.1.2', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('65', '3126', 'RK3128 2.1', '2233', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'fdasfds', '', 'http://www.google.com', '1234', 'service', '1458716091', 'czsfgh', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('66', '1235', 'new Platform', '11', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'dsfaaewfcd', '', 'http://www.google.com', '1234', 'service', '1458716241', 'safdag', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('69', '6006', 'newer', '3435', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'test1111111', '', 'http://www.cn', '2341', 'service', '1458716367', 'dsafdsagh', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('70', '3161', 'VSOON', '24235', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'fdasfdddddds', '', 'http://www.qq.com', '3e24', 'service', '1458716396', 'fsfsdag', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('71', '3126', 'VSOON', '1.9', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'test1111111', '', 'http://www.baidu.com', 'fdsf', 'ecjon', '1458716436', 'faewfeawfveawcve', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('72', '1235', 'imag', '23425', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'fdasfdddddds', '', 'http://www.qq.com', '1321', 'service', '1458716456', 'adsdsafsga', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('73', '3126', 'VSOON', '1.1.1', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'dsfaaewfcd', '', 'http://www.baidu.com/fadsfave', 'faew', 'ecjon', '1458716463', 'vfaewvfsedfvewf', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('74', '6006', 'imag', '5352', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'fdasfdddddds', '', 'http://www.test.com', '4252', 'service', '1458716563', 'fsfadad', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('76', 'fdasfds', 'newer', '1.23', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'aaa', '', 'http://www.baidu.com', '1649', 'service', '1458730026', 'czxfsdg', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('77', '1235', 'new Platform', '1.11', '05b28d17a7b6e7024b6e5d8cc43a8bf7', 'test1111111', '', 'http://www.baidu.com/dfasf', 'fsdf', 'ecjon', '1458782587', 'fdasfeawf', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('78', '1122', 'new Platform', '1.31', '05b28d17a7b6e7024b6e5d8cc43a8bf7', 'dsfaaewfcd', '', 'http://www.baidu.com/fsdafd', 'dfsa', 'ecjon', '1458782806', 'fdasfdsafdasf', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('79', '1235', 'VSOON', '1.32', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'test1111111', '', 'http://www.baidu.com', 'fasd', 'ecjon', '1458783466', 'gaewge', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('99', '0103', '123', '2.2.1-R-2015000001', '3CD4DF466E93DC6696D7A9BA7E1F9E6D', 'test1111111', '', 'http://pan.baidu.com/s/1o8mZGAA', '1234', 'yangxue', '1459056296', '333333', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('103', '1dw14325', 'vxcbvxbcvn', 'fsfdg', 'zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz', 'aaa', '', 'http://www.sogou.com', '1324', 'customer', '1460542704', 'fsdafdghha', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('104', '2202', 'fcacve', '4565', 'adwara34rt5624rqwrf35462tre63523', '客户', '', 'http://www.12.com', '1534', 'customer', '1460623126', 'dsdfdsghasg', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('105', '2202', 'vxvbnxdf', '2431', 'afdw34gtret235yert35136f23436eg3', '客户', '', 'http://www.123.com', '6921', 'admin', '1460623239', 'sdfaerfsdvfdyrrgr', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('107', '1dw14325', 'qrer13215qwre', 'myFirmware', 'e10adc3949ba59abbe56e057f20f883e', 'admin', '', 'http://www.baidu.com', '1234', 'admin', '1460685368', 'desc', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('100', '0103', 'fasdefcveawcdsa', '64134', '3CD4DF466E93DC6696D7A9BA7E1F9E6D', 'customer', 'test', 'http://www.baidu.com', '1234', 'service', '1460025158', 'sfdghgs', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('108', '23', 'vxcbvxbcvn', '213', 'sr324dqw2325wfwqe3425wgsw42365w5', 'customer', '', 'http://www.12306.cn', '3131', 'duxiaoyao', '1461811670', 'zsddvzasf', '');
INSERT INTO `tb_firmware_publish_history` VALUES ('113', '3126', 'qrer13215qwre', 'rasfdsfqw', 'ae12edw1414rdwq41e3245fw321ea241', 'customer', '', 'http://ww.sogou.com', '1367', 'admin', '1461827843', 'zcasffqGFGfs', '1758c47b6a1710055018385938b5fa09');
INSERT INTO `tb_firmware_publish_history` VALUES ('114', '3126', 'qrer13215qwre', 'rasfdsfqw', 'ae12edw1414rdwq41e3245fw321ea241', 'customer', '', 'http://www.sogou.com', '1367', 'admin', '1461827910', '1234567890', 'c60d916d8e619e4cc86b9ee7509baa9c');

-- ----------------------------
-- Table structure for tb_mail
-- ----------------------------
DROP TABLE IF EXISTS `tb_mail`;
CREATE TABLE `tb_mail` (
  `mail_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '邮件id',
  `mail_to` varchar(255) NOT NULL DEFAULT '' COMMENT '接收方邮箱地址',
  `subject` varchar(255) NOT NULL DEFAULT '' COMMENT '邮件标题',
  `body` text NOT NULL COMMENT '内容',
  `type` varchar(50) NOT NULL DEFAULT '' COMMENT '类型',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1:成功   0：失败',
  `mail_time` int(10) unsigned NOT NULL COMMENT '发送时间',
  PRIMARY KEY (`mail_id`),
  KEY `type` (`type`) USING BTREE,
  KEY `mail_to` (`mail_to`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tb_mail
-- ----------------------------
INSERT INTO `tb_mail` VALUES ('3', '1353178739@qq.com', 'VSOONTECH - 客户服务系统重置密码', '\r\n   			&lt;div style=&quot;padding: 30px&quot;&gt;\r\n				&lt;div style=&quot;margin: 6px 0 60px 0;&quot;&gt;\r\n					&lt;p&gt;我们收到您的重置密码请求，如果确认是您本人操作，请点击一下链接。&lt;/p&gt;\r\n					&lt;p&gt;\r\n						&lt;a href=&quot;http://www.kefu.com/Customer/Home/User/userResetPwd/key/B1A9BB1DB8F2B7220831E9CFDBA4879DzUzRoJmW1ZzT2MVZBFjRsVGO4MjN5MTO1QTM&quot; target=&quot;_blank&quot; style=&quot;text-decoration:none&quot;&gt;&lt;span style=&quot;background:#08CBFE;color:#fff;font-size:15px;font-weight:bold;padding:5px 10px;&quot;&gt;重置密码&lt;/span&gt;&lt;/a&gt;\r\n					&lt;/p&gt;\r\n					&lt;br/&gt;\r\n					&lt;p style=&quot;color:gray;font-size:12px&quot;&gt;如果不是您本人操作，请忽略此邮件。此为系统邮件，请勿回复&lt;/p&gt;\r\n					&lt;p style=&quot;color:gray;font-size:12px&quot;&gt;该验证邮件有效期为30分钟，超时请重新发送邮件。&lt;/p&gt;\r\n				&lt;/div&gt;\r\n			&lt;/div&gt;', 'sendFindPwdEmail', '1', '1459396391');
INSERT INTO `tb_mail` VALUES ('4', '1353178739@qq.com', 'VSOONTECH - 客户服务系统重置密码', '\r\n   			&lt;div style=&quot;padding: 30px&quot;&gt;\r\n				&lt;div style=&quot;margin: 6px 0 60px 0;&quot;&gt;\r\n					&lt;p&gt;我们收到您的重置密码请求，如果确认是您本人操作，请点击一下链接。&lt;/p&gt;\r\n					&lt;p&gt;\r\n						&lt;a href=&quot;http://www.kefu.com/Customer/Home/User/userResetPwd/key/E8A77BEB9F98BE54E4E53C3BC0D73C06zEERBJXTkJXTNZTaVJnMzZUOxYjMxQTO1QTM&quot; target=&quot;_blank&quot; style=&quot;text-decoration:none&quot;&gt;&lt;span style=&quot;background:#08CBFE;color:#fff;font-size:15px;font-weight:bold;padding:5px 10px;&quot;&gt;重置密码&lt;/span&gt;&lt;/a&gt;\r\n					&lt;/p&gt;\r\n					&lt;br/&gt;\r\n					&lt;p style=&quot;color:gray;font-size:12px&quot;&gt;如果不是您本人操作，请忽略此邮件。此为系统邮件，请勿回复&lt;/p&gt;\r\n					&lt;p style=&quot;color:gray;font-size:12px&quot;&gt;该验证邮件有效期为30分钟，超时请重新发送邮件。&lt;/p&gt;\r\n				&lt;/div&gt;\r\n			&lt;/div&gt;', 'sendFindPwdEmail', '1', '1459412626');
INSERT INTO `tb_mail` VALUES ('5', '1353178739@qq.com', 'VSOONTECH - 客户服务系统重置密码', '\r\n   			&lt;div style=&quot;padding: 30px&quot;&gt;\r\n				&lt;div style=&quot;margin: 6px 0 60px 0;&quot;&gt;\r\n					&lt;p&gt;我们收到您的重置密码请求，如果确认是您本人操作，请点击一下链接。&lt;/p&gt;\r\n					&lt;p&gt;\r\n						&lt;a href=&quot;http://192.168.1.199:180/Pages/customService/updatePwd.html?key=38C8CFB613DC6800C738E4C7C3928D33zgnbPF3RjNGVTN0cPp1R0IFOyATOyATM2QTM&quot; target=&quot;_blank&quot; style=&quot;text-decoration:none&quot;&gt;&lt;span style=&quot;background:#08CBFE;color:#fff;font-size:15px;font-weight:bold;padding:5px 10px;&quot;&gt;重置密码&lt;/span&gt;&lt;/a&gt;\r\n					&lt;/p&gt;\r\n					&lt;br/&gt;\r\n					&lt;p style=&quot;color:gray;font-size:12px&quot;&gt;如果不是您本人操作，请忽略此邮件。此为系统邮件，请勿回复&lt;/p&gt;\r\n					&lt;p style=&quot;color:gray;font-size:12px&quot;&gt;该验证邮件有效期为30分钟，超时请重新发送邮件。&lt;/p&gt;\r\n				&lt;/div&gt;\r\n			&lt;/div&gt;', 'sendFindPwdEmail', '1', '1461029038');
INSERT INTO `tb_mail` VALUES ('6', 'human@ipmacro.com', 'VSOONTECH - 客户服务系统重置密码', '\r\n   			&lt;div style=&quot;padding: 30px&quot;&gt;\r\n				&lt;div style=&quot;margin: 6px 0 60px 0;&quot;&gt;\r\n					&lt;p&gt;我们收到您的重置密码请求，如果确认是您本人操作，请点击一下链接。&lt;/p&gt;\r\n					&lt;p&gt;\r\n						&lt;a href=&quot;http://192.168.1.199:180/Pages/customService/updatePwd.html?key=F89AC05C1A04A3D8A29848DDC5C5A17E==gNycDbwxWQKF1M2IUOs5GdWpWM1ITMzATM2QTM&quot; target=&quot;_blank&quot; style=&quot;text-decoration:none&quot;&gt;&lt;span style=&quot;background:#08CBFE;color:#fff;font-size:15px;font-weight:bold;padding:5px 10px;&quot;&gt;重置密码&lt;/span&gt;&lt;/a&gt;\r\n					&lt;/p&gt;\r\n					&lt;br/&gt;\r\n					&lt;p style=&quot;color:gray;font-size:12px&quot;&gt;如果不是您本人操作，请忽略此邮件。此为系统邮件，请勿回复&lt;/p&gt;\r\n					&lt;p style=&quot;color:gray;font-size:12px&quot;&gt;该验证邮件有效期为30分钟，超时请重新发送邮件。&lt;/p&gt;\r\n				&lt;/div&gt;\r\n			&lt;/div&gt;', 'sendFindPwdEmail', '1', '1461031254');
INSERT INTO `tb_mail` VALUES ('7', 'human@ipmacro.com', 'VSOONTECH - 客户服务系统重置密码', '\r\n   			&lt;div style=&quot;padding: 30px&quot;&gt;\r\n				&lt;div style=&quot;margin: 6px 0 60px 0;&quot;&gt;\r\n					&lt;p&gt;我们收到您的重置密码请求，如果确认是您本人操作，请点击一下链接。&lt;/p&gt;\r\n					&lt;p&gt;\r\n						&lt;a href=&quot;http://192.168.1.199:180/Pages/customService/updatePwd.html?key=7305E6777EDBE8F0339C0FC38E30F718==gNyE0b0JHOhFHew4mMjlFZDBTN5YTMzATM2QTM&quot; target=&quot;_blank&quot; style=&quot;text-decoration:none&quot;&gt;&lt;span style=&quot;background:#08CBFE;color:#fff;font-size:15px;font-weight:bold;padding:5px 10px;&quot;&gt;重置密码&lt;/span&gt;&lt;/a&gt;\r\n					&lt;/p&gt;\r\n					&lt;br/&gt;\r\n					&lt;p style=&quot;color:gray;font-size:12px&quot;&gt;如果不是您本人操作，请忽略此邮件。此为系统邮件，请勿回复&lt;/p&gt;\r\n					&lt;p style=&quot;color:gray;font-size:12px&quot;&gt;该验证邮件有效期为30分钟，超时请重新发送邮件。&lt;/p&gt;\r\n				&lt;/div&gt;\r\n			&lt;/div&gt;', 'sendFindPwdEmail', '1', '1461031695');
INSERT INTO `tb_mail` VALUES ('8', 'zhangtao@ipmacro.com', 'VSOONTECH - 客户服务系统重置密码', '\r\n   			&lt;div style=&quot;padding: 30px&quot;&gt;\r\n				&lt;div style=&quot;margin: 6px 0 60px 0;&quot;&gt;\r\n					&lt;p&gt;我们收到您的重置密码请求，如果确认是您本人操作，请点击一下链接。&lt;/p&gt;\r\n					&lt;p&gt;\r\n						&lt;a href=&quot;http://192.168.1.199:180/Pages/customService/updatePwd.html?key=1E3017D3E4B1D868B1DFCE9F97330E02==gNyATMw80Q0JFUCR0SzQUQXNlNwkTMzATM2QTM&quot; target=&quot;_blank&quot; style=&quot;text-decoration:none&quot;&gt;&lt;span style=&quot;background:#08CBFE;color:#fff;font-size:15px;font-weight:bold;padding:5px 10px;&quot;&gt;重置密码&lt;/span&gt;&lt;/a&gt;\r\n					&lt;/p&gt;\r\n					&lt;br/&gt;\r\n					&lt;p style=&quot;color:gray;font-size:12px&quot;&gt;如果不是您本人操作，请忽略此邮件。此为系统邮件，请勿回复&lt;/p&gt;\r\n					&lt;p style=&quot;color:gray;font-size:12px&quot;&gt;该验证邮件有效期为30分钟，超时请重新发送邮件。&lt;/p&gt;\r\n				&lt;/div&gt;\r\n			&lt;/div&gt;', 'sendFindPwdEmail', '0', '1461031907');
INSERT INTO `tb_mail` VALUES ('9', '873695715@qq.com', 'VSOONTECH - 客户服务系统重置密码', '\r\n   			&lt;div style=&quot;padding: 30px&quot;&gt;\r\n				&lt;div style=&quot;margin: 6px 0 60px 0;&quot;&gt;\r\n					&lt;p&gt;我们收到您的重置密码请求，如果确认是您本人操作，请点击一下链接。&lt;/p&gt;\r\n					&lt;p&gt;\r\n						&lt;a href=&quot;http://192.168.1.199:180/Pages/customService/updatePwd.html?key=944D9C01210E87431CE7B0780116FF95==gNyMFVNJjbSxGU490TsVlQRBjM0AjMzATM2QTM&quot; target=&quot;_blank&quot; style=&quot;text-decoration:none&quot;&gt;&lt;span style=&quot;background:#08CBFE;color:#fff;font-size:15px;font-weight:bold;padding:5px 10px;&quot;&gt;重置密码&lt;/span&gt;&lt;/a&gt;\r\n					&lt;/p&gt;\r\n					&lt;br/&gt;\r\n					&lt;p style=&quot;color:gray;font-size:12px&quot;&gt;如果不是您本人操作，请忽略此邮件。此为系统邮件，请勿回复&lt;/p&gt;\r\n					&lt;p style=&quot;color:gray;font-size:12px&quot;&gt;该验证邮件有效期为30分钟，超时请重新发送邮件。&lt;/p&gt;\r\n				&lt;/div&gt;\r\n			&lt;/div&gt;', 'sendFindPwdEmail', '1', '1461032048');
INSERT INTO `tb_mail` VALUES ('10', 'human@ipmacro.com', 'VSOONTECH - 客户服务系统重置密码', '\r\n   			&lt;div style=&quot;padding: 30px&quot;&gt;\r\n				&lt;div style=&quot;margin: 6px 0 60px 0;&quot;&gt;\r\n					&lt;p&gt;我们收到您的重置密码请求，如果确认是您本人操作，请点击一下链接。&lt;/p&gt;\r\n					&lt;p&gt;\r\n						&lt;a href=&quot;http://192.168.1.199:180/Pages/customService/updatePwd.html?key=9B466F3C9B6DA1513F7A610586030246==gNycVRGxGbLVmc4hXUihXZEdkMyIzMzATM2QTM&quot; target=&quot;_blank&quot; style=&quot;text-decoration:none&quot;&gt;&lt;span style=&quot;background:#08CBFE;color:#fff;font-size:15px;font-weight:bold;padding:5px 10px;&quot;&gt;重置密码&lt;/span&gt;&lt;/a&gt;\r\n					&lt;/p&gt;\r\n					&lt;br/&gt;\r\n					&lt;p style=&quot;color:gray;font-size:12px&quot;&gt;如果不是您本人操作，请忽略此邮件。此为系统邮件，请勿回复&lt;/p&gt;\r\n					&lt;p style=&quot;color:gray;font-size:12px&quot;&gt;该验证邮件有效期为30分钟，超时请重新发送邮件。&lt;/p&gt;\r\n				&lt;/div&gt;\r\n			&lt;/div&gt;', 'sendFindPwdEmail', '1', '1461033225');
INSERT INTO `tb_mail` VALUES ('11', '1353178739@qq.com', 'VSOONTECH - 客户服务系统重置密码', '\r\n   			&lt;div style=&quot;padding: 30px&quot;&gt;\r\n				&lt;div style=&quot;margin: 6px 0 60px 0;&quot;&gt;\r\n					&lt;p&gt;我们收到您的重置密码请求，如果确认是您本人操作，请点击一下链接。&lt;/p&gt;\r\n					&lt;p&gt;\r\n						&lt;a href=&quot;http://192.168.1.199:180/Pages/customService/updatePwd.html?key=B82CA814B7550987DED1CAC9E548D584zYGU182RYF0cMlnT0ZHTyMFNzQzMzATM2QTM&quot; target=&quot;_blank&quot; style=&quot;text-decoration:none&quot;&gt;&lt;span style=&quot;background:#08CBFE;color:#fff;font-size:15px;font-weight:bold;padding:5px 10px;&quot;&gt;重置密码&lt;/span&gt;&lt;/a&gt;\r\n					&lt;/p&gt;\r\n					&lt;br/&gt;\r\n					&lt;p style=&quot;color:gray;font-size:12px&quot;&gt;如果不是您本人操作，请忽略此邮件。此为系统邮件，请勿回复&lt;/p&gt;\r\n					&lt;p style=&quot;color:gray;font-size:12px&quot;&gt;该验证邮件有效期为30分钟，超时请重新发送邮件。&lt;/p&gt;\r\n				&lt;/div&gt;\r\n			&lt;/div&gt;', 'sendFindPwdEmail', '1', '1461033435');
INSERT INTO `tb_mail` VALUES ('12', 'human@ipmacro.com', 'VSOONTECH - 客户服务系统重置密码', '\r\n   			&lt;div style=&quot;padding: 30px&quot;&gt;\r\n				&lt;div style=&quot;margin: 6px 0 60px 0;&quot;&gt;\r\n					&lt;p&gt;我们收到您的重置密码请求，如果确认是您本人操作，请点击一下链接。&lt;/p&gt;\r\n					&lt;p&gt;\r\n						&lt;a href=&quot;http://192.168.1.199:180/Pages/customService/updatePwd.html?key=79A81E6A6E5883A92568FB3670D7BF3C==gNyEnQIhFTyFjemFGdilnMIFGN3YzMzATM2QTM&quot; target=&quot;_blank&quot; style=&quot;text-decoration:none&quot;&gt;&lt;span style=&quot;background:#08CBFE;color:#fff;font-size:15px;font-weight:bold;padding:5px 10px;&quot;&gt;重置密码&lt;/span&gt;&lt;/a&gt;\r\n					&lt;/p&gt;\r\n					&lt;br/&gt;\r\n					&lt;p style=&quot;color:gray;font-size:12px&quot;&gt;如果不是您本人操作，请忽略此邮件。此为系统邮件，请勿回复&lt;/p&gt;\r\n					&lt;p style=&quot;color:gray;font-size:12px&quot;&gt;该验证邮件有效期为30分钟，超时请重新发送邮件。&lt;/p&gt;\r\n				&lt;/div&gt;\r\n			&lt;/div&gt;', 'sendFindPwdEmail', '1', '1461033675');
INSERT INTO `tb_mail` VALUES ('13', 'human@ipmacro.com', 'VSOONTECH - 客户服务系统重置密码', '\r\n   			&lt;div style=&quot;padding: 30px&quot;&gt;\r\n				&lt;div style=&quot;margin: 6px 0 60px 0;&quot;&gt;\r\n					&lt;p&gt;我们收到您的重置密码请求，如果确认是您本人操作，请点击一下链接。&lt;/p&gt;\r\n					&lt;p&gt;\r\n						&lt;a href=&quot;http://192.168.1.199:180/Pages/customService/updatePwd.html?key=45DB6AE923A19A41E2AE0AC0AB8DDD26==gNy0Ud0QUNQljVRdmVk1WcKR0N4cjN1ATM2QTM&quot; target=&quot;_blank&quot; style=&quot;text-decoration:none&quot;&gt;&lt;span style=&quot;background:#08CBFE;color:#fff;font-size:15px;font-weight:bold;padding:5px 10px;&quot;&gt;重置密码&lt;/span&gt;&lt;/a&gt;\r\n					&lt;/p&gt;\r\n					&lt;br/&gt;\r\n					&lt;p style=&quot;color:gray;font-size:12px&quot;&gt;如果不是您本人操作，请忽略此邮件。此为系统邮件，请勿回复&lt;/p&gt;\r\n					&lt;p style=&quot;color:gray;font-size:12px&quot;&gt;该验证邮件有效期为30分钟，超时请重新发送邮件。&lt;/p&gt;\r\n				&lt;/div&gt;\r\n			&lt;/div&gt;', 'sendFindPwdEmail', '1', '1461056790');

-- ----------------------------
-- Table structure for tb_platform
-- ----------------------------
DROP TABLE IF EXISTS `tb_platform`;
CREATE TABLE `tb_platform` (
  `id` int(32) unsigned NOT NULL AUTO_INCREMENT,
  `platform` varchar(32) NOT NULL COMMENT '平台名',
  `note` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=188 DEFAULT CHARSET=utf8 COMMENT='平台表';

-- ----------------------------
-- Records of tb_platform
-- ----------------------------
INSERT INTO `tb_platform` VALUES ('171', 'faewvcecf', 'fcaewfce');
INSERT INTO `tb_platform` VALUES ('172', 'cvaewce', 'fdasfcve');
INSERT INTO `tb_platform` VALUES ('173', 'vaerfeaw', 'cfaesfvewfvre');
INSERT INTO `tb_platform` VALUES ('174', 'fcvaewcvew', 'fvcsdaefcve');
INSERT INTO `tb_platform` VALUES ('175', 'caewc', 'fcaeswcfeaw');
INSERT INTO `tb_platform` VALUES ('176', 'faewvef', 'cvaewfceaw');
INSERT INTO `tb_platform` VALUES ('177', 'cavece', 'fvaedsfce');
INSERT INTO `tb_platform` VALUES ('178', 'fcacve', 'vfaewfvewfc');
INSERT INTO `tb_platform` VALUES ('179', 'sdfdsg', '');
INSERT INTO `tb_platform` VALUES ('180', 'zxfdsgfdh', '');
INSERT INTO `tb_platform` VALUES ('181', 'gdfghgdfzshf', '');
INSERT INTO `tb_platform` VALUES ('182', 'vxvbnxdf', '');
INSERT INTO `tb_platform` VALUES ('183', 'vxcbvxbcvn', '');
INSERT INTO `tb_platform` VALUES ('184', 'fadsvc', 'eavcaefcve');
INSERT INTO `tb_platform` VALUES ('186', 'qrer13215qwre', '');
INSERT INTO `tb_platform` VALUES ('187', '1234', '');

-- ----------------------------
-- Table structure for tb_question
-- ----------------------------
DROP TABLE IF EXISTS `tb_question`;
CREATE TABLE `tb_question` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '问题单id',
  `cate_id_1` int(10) unsigned NOT NULL COMMENT '问题分类1',
  `cate_id_2` int(10) unsigned NOT NULL COMMENT '问题分类1',
  `content` text NOT NULL COMMENT '问题内容',
  `reply` text NOT NULL COMMENT '回复内容',
  `asker_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '提问者客户id',
  `reply_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '回复者用户id，即被指派人id',
  `ask_time` int(10) unsigned NOT NULL COMMENT '发布问题单时间，时间戳',
  `ask_attach` varchar(500) NOT NULL DEFAULT '' COMMENT '提问附件',
  `reply_time` int(10) unsigned NOT NULL COMMENT '回复问题单时间，时间戳',
  `reply_attach` varchar(500) NOT NULL DEFAULT '' COMMENT '回答附件，',
  `assign_id` int(10) unsigned NOT NULL COMMENT '指派操作人id',
  `assign_time` int(10) unsigned NOT NULL COMMENT '指派时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0：待指派，1:已指派，待回复，2：已回复',
  PRIMARY KEY (`id`),
  KEY `asker_id` (`asker_id`) USING BTREE,
  KEY `reply_id` (`reply_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=195 DEFAULT CHARSET=utf8 COMMENT='问题单表';

-- ----------------------------
-- Records of tb_question
-- ----------------------------
INSERT INTO `tb_question` VALUES ('142', '113', '0', '附件测试2个', '附件回复3个', '7', '7', '1459508359', 'a:2:{i:0;s:32:\"Upload/Question/20160401/M/1.png\";i:1;s:32:\"Upload/Question/20160401/f/2.png\";}', '1459508434', 'a:3:{i:0;s:32:\"Upload/Question/20160401/F/3.png\";i:1;s:32:\"Upload/Question/20160401/r/4.png\";i:2;s:32:\"Upload/Question/20160401/J/5.png\";}', '0', '0', '2');
INSERT INTO `tb_question` VALUES ('143', '113', '0', '测试3个文件', '2', '7', '7', '1459508496', 'a:3:{i:0;s:33:\"Upload/Question/20160401/z/17.png\";i:1;s:33:\"Upload/Question/20160401/O/18.png\";i:2;s:33:\"Upload/Question/20160401/I/19.png\";}', '1459821909', 'a:1:{i:0;s:32:\"Upload/Question/20160405/h/2.png\";}', '0', '0', '2');
INSERT INTO `tb_question` VALUES ('144', '113', '0', '测试', '1', '7', '7', '1459513411', 'a:1:{i:0;s:32:\"Upload/Question/20160401/O/1.png\";}', '1459821894', 'a:1:{i:0;s:32:\"Upload/Question/20160405/6/1.png\";}', '0', '0', '2');
INSERT INTO `tb_question` VALUES ('145', '113', '0', '1', '', '7', '0', '1459822213', '', '0', '', '0', '0', '0');
INSERT INTO `tb_question` VALUES ('146', '112', '0', '播放视频慢', '已回复', '9', '22', '1459835519', '', '1459937703', '', '0', '0', '2');
INSERT INTO `tb_question` VALUES ('147', '112', '0', '新建问题-系统性能', '这是回复内容。。。。。。', '3', '3', '1459835659', 'a:1:{i:0;s:43:\"Upload/Question/20160405/l/需求分析.txt\";}', '1459835985', 'a:1:{i:0;s:44:\"Upload/Question/20160405/E/ThinkPHP3.2.2.chm\";}', '0', '0', '2');
INSERT INTO `tb_question` VALUES ('149', '113', '0', 'fasdfasdf', '', '22', '0', '1459907834', '', '0', '', '0', '0', '0');
INSERT INTO `tb_question` VALUES ('150', '112', '0', 'test', '', '22', '0', '1459907872', '', '0', '', '0', '0', '0');
INSERT INTO `tb_question` VALUES ('151', '113', '0', 'test', '', '22', '0', '1459907908', '', '0', '', '0', '0', '0');
INSERT INTO `tb_question` VALUES ('168', '113', '0', '测试', 'gvtsertfrytsed', '3', '3', '1460086736', 'a:1:{i:0;s:49:\"Upload/Question/20160408/4/56d3e7ffb7957_1024.jpg\";}', '1460625209', 'a:1:{i:0;s:38:\"Upload/Question/20160414/t/appinfo.xml\";}', '3', '1460110845', '2');
INSERT INTO `tb_question` VALUES ('180', '113', '0', '测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试', '', '35', '3', '1460614723', '', '0', '', '3', '1460709276', '1');
INSERT INTO `tb_question` VALUES ('182', '117', '0', '问题描述。。。', '', '3', '0', '1460711121', 'a:2:{i:0;s:37:\"Upload/Question/20160415/bTIECP/a.txt\";i:1;s:37:\"Upload/Question/20160415/msKDfQ/d.txt\";}', '0', '', '0', '0', '0');
INSERT INTO `tb_question` VALUES ('185', '0', '0', '测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试', '', '26', '0', '1460942461', '', '0', '', '0', '0', '0');
INSERT INTO `tb_question` VALUES ('186', '113', '0', '测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试', '', '26', '0', '1460950710', '', '0', '', '0', '0', '0');
INSERT INTO `tb_question` VALUES ('187', '117', '0', 'settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings settings setti', 'system settings', '26', '3', '1460957257', '', '1460959478', 'a:1:{i:0;s:50:\"Upload/Question/20160418/hv8zpY/moduleinfo_ttt.xml\";}', '3', '1460959394', '2');
INSERT INTO `tb_question` VALUES ('188', '113', '0', 'daf ', '', '26', '0', '1460960236', '', '0', '', '0', '0', '0');
INSERT INTO `tb_question` VALUES ('189', '108', '0', 'question question question question question question question question question question question question question question question question question question question question question question question question question question question question question question question question question question question question question question question question question question question question question question question question', '', '26', '3', '1460960307', 'a:1:{i:0;s:44:\"Upload/Question/20160418/EZh4MQ/channel.json\";}', '0', '', '3', '1460975040', '1');
INSERT INTO `tb_question` VALUES ('190', '113', '0', 'test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test', '', '3', '0', '1461034079', '', '0', '', '0', '0', '0');
INSERT INTO `tb_question` VALUES ('191', '117', '0', 'e6e6546', '', '23', '0', '1461321020', 'a:1:{i:0;s:40:\"Upload/Question/20160422/MT4dfj/yibo.bmp\";}', '0', '', '0', '0', '0');
INSERT INTO `tb_question` VALUES ('192', '109', '0', 'ghrtdyftydtyfuydtyhvydrtcgtsxhjnmgtydxchjrtzxkhjfrytsdxsretasgvuyedtr4dytfvytuesdyucythtdthysrtgcvyjfudtryesrtagdygihohuirff', '', '26', '0', '1461808998', 'a:1:{i:0;s:60:\"Upload/Question/20160428/WR77p8/Android前端发布系统.mm\";}', '0', '', '0', '0', '0');
INSERT INTO `tb_question` VALUES ('193', '113', '0', '测试', '', '3', '0', '1461839915', 'a:2:{i:0;s:40:\"Upload/Question/20160428/jws6bt/test.txt\";i:1;s:42:\"Upload/Question/20160428/36qCvp/测试.txt\";}', '0', '', '0', '0', '0');
INSERT INTO `tb_question` VALUES ('194', '114', '0', '测试优化上传', '', '3', '0', '1461839966', 'a:2:{i:0;s:40:\"Upload/Question/20160428/HZsAgn/test.txt\";i:1;s:42:\"Upload/Question/20160428/GRcLDd/测试.txt\";}', '0', '', '0', '0', '0');

-- ----------------------------
-- Table structure for tb_question_append
-- ----------------------------
DROP TABLE IF EXISTS `tb_question_append`;
CREATE TABLE `tb_question_append` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `q_id` int(10) unsigned NOT NULL COMMENT '关联问题单id  tb_question表id字段',
  `content` text NOT NULL COMMENT '追问内容',
  `append_time` int(10) unsigned NOT NULL COMMENT '追问问题单时间，时间戳',
  `append_attach` varchar(500) NOT NULL DEFAULT '' COMMENT '追问附件',
  `type` enum('1','2') NOT NULL DEFAULT '1' COMMENT '1:追加提问   2:追加回复',
  PRIMARY KEY (`id`),
  KEY `q_id` (`q_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8 COMMENT='问题单追加表';

-- ----------------------------
-- Records of tb_question_append
-- ----------------------------
INSERT INTO `tb_question_append` VALUES ('32', '142', '再来两个', '1459508564', '', '2');
INSERT INTO `tb_question_append` VALUES ('33', '143', '43', '1459508833', '', '1');
INSERT INTO `tb_question_append` VALUES ('34', '142', '追加回复中2个文件', '1459509050', '', '2');
INSERT INTO `tb_question_append` VALUES ('35', '142', '23', '1459509123', '', '2');
INSERT INTO `tb_question_append` VALUES ('36', '142', '加', '1459510077', 'a:3:{i:0;s:32:\"Upload/Question/20160401/X/2.png\";i:1;s:32:\"Upload/Question/20160401/j/9.png\";i:2;s:33:\"Upload/Question/20160401/7/16.png\";}', '2');
INSERT INTO `tb_question_append` VALUES ('37', '142', '4', '1459511725', 'a:4:{i:0;s:32:\"Upload/Question/20160401/9/1.png\";i:1;s:32:\"Upload/Question/20160401/N/2.png\";i:2;s:32:\"Upload/Question/20160401/D/3.png\";i:3;s:32:\"Upload/Question/20160401/7/4.png\";}', '2');
INSERT INTO `tb_question_append` VALUES ('38', '143', '仍需', '1459512914', 'a:3:{i:0;s:32:\"Upload/Question/20160401/A/1.png\";i:1;s:32:\"Upload/Question/20160401/x/8.png\";i:2;s:33:\"Upload/Question/20160401/Q/15.png\";}', '1');
INSERT INTO `tb_question_append` VALUES ('39', '143', '再来一次', '1459512956', 'a:1:{i:0;s:33:\"Upload/Question/20160401/N/15.png\";}', '1');
INSERT INTO `tb_question_append` VALUES ('40', '142', '34', '1459513241', '', '2');
INSERT INTO `tb_question_append` VALUES ('41', '142', '34', '1459513888', '', '2');
INSERT INTO `tb_question_append` VALUES ('42', '142', '34', '1459513908', '', '2');
INSERT INTO `tb_question_append` VALUES ('43', '142', '34', '1459513917', '', '2');
INSERT INTO `tb_question_append` VALUES ('44', '142', '32报', '1459820126', 'a:2:{i:0;s:33:\"Upload/Question/20160405/e/17.png\";i:1;s:33:\"Upload/Question/20160405/9/18.png\";}', '2');
INSERT INTO `tb_question_append` VALUES ('45', '144', '仍', '1459820195', 'a:1:{i:0;s:32:\"Upload/Question/20160405/y/8.png\";}', '1');
INSERT INTO `tb_question_append` VALUES ('46', '142', '脸', '1459820237', 'a:1:{i:0;s:33:\"Upload/Question/20160405/5/15.png\";}', '1');
INSERT INTO `tb_question_append` VALUES ('47', '142', '2', '1459820269', 'a:1:{i:0;s:32:\"Upload/Question/20160405/G/2.png\";}', '1');
INSERT INTO `tb_question_append` VALUES ('48', '142', '3', '1459820307', 'a:1:{i:0;s:32:\"Upload/Question/20160405/h/3.png\";}', '1');
INSERT INTO `tb_question_append` VALUES ('49', '142', '4', '1459820340', 'a:1:{i:0;s:32:\"Upload/Question/20160405/E/4.png\";}', '2');
INSERT INTO `tb_question_append` VALUES ('50', '142', '4', '1459820340', 'a:1:{i:0;s:32:\"Upload/Question/20160405/z/4.png\";}', '2');
INSERT INTO `tb_question_append` VALUES ('51', '142', '5', '1459820440', 'a:1:{i:0;s:32:\"Upload/Question/20160405/V/5.png\";}', '2');
INSERT INTO `tb_question_append` VALUES ('52', '142', '2', '1459821016', 'a:1:{i:0;s:32:\"Upload/Question/20160405/l/2.png\";}', '2');
INSERT INTO `tb_question_append` VALUES ('53', '142', '3', '1459821324', 'a:1:{i:0;s:32:\"Upload/Question/20160405/5/3.png\";}', '2');
INSERT INTO `tb_question_append` VALUES ('54', '142', '3', '1459821537', 'a:1:{i:0;s:32:\"Upload/Question/20160405/G/3.png\";}', '2');
INSERT INTO `tb_question_append` VALUES ('55', '147', '这是追加回复。。。。', '1459836064', 'a:2:{i:0;s:49:\"Upload/Question/20160405/E/新建文本文档.txt\";i:1;s:43:\"Upload/Question/20160405/L/需求分析.txt\";}', '2');
INSERT INTO `tb_question_append` VALUES ('56', '148', '追加内容', '1459907288', 'a:1:{i:0;s:35:\"Upload/Question/20160406/R/conf.zip\";}', '1');
INSERT INTO `tb_question_append` VALUES ('57', '148', 'sfdsfgdgdsh', '1459907588', 'a:1:{i:0;s:35:\"Upload/Question/20160406/P/kefu.sql\";}', '1');
INSERT INTO `tb_question_append` VALUES ('58', '148', 'fsdfdgfha', '1459907634', 'a:1:{i:0;s:56:\"Upload/Question/20160406/K/客服系统_20160405_1850.rp\";}', '1');
INSERT INTO `tb_question_append` VALUES ('59', '148', 'sdfdhgfj', '1459908124', 'a:3:{i:0;s:42:\"Upload/Question/20160406/y/客服系统.rp\";i:1;s:56:\"Upload/Question/20160406/B/客服系统_20160304_1831.rp\";i:2;s:56:\"Upload/Question/20160406/L/客服系统_20160307_1602.rp\";}', '1');
INSERT INTO `tb_question_append` VALUES ('60', '168', '这是追加回复', '1460682804', '', '2');
INSERT INTO `tb_question_append` VALUES ('61', '181', '测试测试测试测试测试测试测试测试测试测试测试测试', '1460710528', '', '1');
INSERT INTO `tb_question_append` VALUES ('62', '187', '设置设置', '1460959516', 'a:1:{i:0;s:43:\"Upload/Question/20160418/szPtyr/appinfo.xml\";}', '1');
INSERT INTO `tb_question_append` VALUES ('63', '187', 'fsdfdgfsdgfdh', '1461664363', '', '2');
INSERT INTO `tb_question_append` VALUES ('64', '147', 'sdaearfsfweatageeyhah', '1461806528', '', '2');
INSERT INTO `tb_question_append` VALUES ('65', '189', 'rasrerfawtgqaedsgfdhdf', '1461807194', 'a:1:{i:0;s:40:\"Upload/Question/20160428/VDSrEC/kefu.sql\";}', '1');

-- ----------------------------
-- Table structure for tb_question_category
-- ----------------------------
DROP TABLE IF EXISTS `tb_question_category`;
CREATE TABLE `tb_question_category` (
  `cate_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `cate_name` varchar(32) NOT NULL DEFAULT '' COMMENT '分类名',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父分类id,0:表示根分类',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '排序',
  `if_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1：显示   0：不显示',
  `remark` varchar(50) NOT NULL DEFAULT '' COMMENT '备注',
  `add_time` int(10) unsigned NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`cate_id`),
  KEY `cate_name` (`cate_name`) USING BTREE,
  KEY `parent_id` (`parent_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=118 DEFAULT CHARSET=utf8 COMMENT='问题分类表';

-- ----------------------------
-- Records of tb_question_category
-- ----------------------------
INSERT INTO `tb_question_category` VALUES ('99', '直播', '0', '255', '1', '', '1459394280');
INSERT INTO `tb_question_category` VALUES ('107', '开机画面', '0', '255', '1', '', '1459499231');
INSERT INTO `tb_question_category` VALUES ('108', '点播', '0', '255', '1', '', '1459499301');
INSERT INTO `tb_question_category` VALUES ('109', '产测', '0', '255', '1', '', '1459499307');
INSERT INTO `tb_question_category` VALUES ('111', '天气', '0', '255', '1', '', '1459499321');
INSERT INTO `tb_question_category` VALUES ('112', '应用管理', '0', '255', '1', '', '1459499336');
INSERT INTO `tb_question_category` VALUES ('113', '系统性能', '0', '255', '1', '', '1459499349');
INSERT INTO `tb_question_category` VALUES ('114', '设置', '0', '255', '1', '', '1460624459');
INSERT INTO `tb_question_category` VALUES ('117', '设置2', '0', '255', '1', '', '1460684808');

-- ----------------------------
-- Table structure for tb_question_comments
-- ----------------------------
DROP TABLE IF EXISTS `tb_question_comments`;
CREATE TABLE `tb_question_comments` (
  `c_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '问题单评论',
  `question_id` int(10) unsigned NOT NULL COMMENT '对应tb_question表id',
  `comment_id` int(10) unsigned NOT NULL COMMENT '评论用户id',
  `score` tinyint(1) unsigned NOT NULL COMMENT '评价，1-5分',
  `content` varchar(500) NOT NULL DEFAULT '' COMMENT '评论内容',
  `add_time` int(10) unsigned NOT NULL COMMENT '评论时间',
  PRIMARY KEY (`c_id`),
  KEY `question_id` (`question_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='问题单评论表';

-- ----------------------------
-- Records of tb_question_comments
-- ----------------------------
INSERT INTO `tb_question_comments` VALUES ('1', '85', '7', '2', 'rt34', '1459221330');
INSERT INTO `tb_question_comments` VALUES ('2', '82', '7', '2', '233443', '1459221347');
INSERT INTO `tb_question_comments` VALUES ('3', '94', '24', '2', '1', '1459393984');
INSERT INTO `tb_question_comments` VALUES ('4', '148', '26', '5', 'test', '1459921997');
INSERT INTO `tb_question_comments` VALUES ('5', '187', '26', '5', 'comment', '1461664397');

-- ----------------------------
-- Table structure for tb_upload_files
-- ----------------------------
DROP TABLE IF EXISTS `tb_upload_files`;
CREATE TABLE `tb_upload_files` (
  `file_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键，文件id',
  `belong` enum('2','1') NOT NULL DEFAULT '1' COMMENT '所属，1：常见问题单，2：客户问题单，其余待拓展',
  `path` varchar(100) NOT NULL DEFAULT '' COMMENT '文件相对路径',
  `type` varchar(60) NOT NULL DEFAULT '' COMMENT '文件类型',
  `size` int(10) unsigned NOT NULL COMMENT '文件大小字节',
  `add_time` int(10) unsigned NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`file_id`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8 COMMENT='文件上传记录表';

-- ----------------------------
-- Records of tb_upload_files
-- ----------------------------
INSERT INTO `tb_upload_files` VALUES ('1', '1', 'Upload/Question/20160401/Z/10.png', 'image/png', '5640', '1459502937');
INSERT INTO `tb_upload_files` VALUES ('2', '1', 'Upload/Question/20160401/b/17.png', 'image/png', '8255', '1459502937');
INSERT INTO `tb_upload_files` VALUES ('3', '1', 'Upload/Question/20160401/T/11.png', 'image/png', '6278', '1459503201');
INSERT INTO `tb_upload_files` VALUES ('4', '1', 'Upload/Question/20160401/2/18.png', 'image/png', '6693', '1459503201');
INSERT INTO `tb_upload_files` VALUES ('5', '1', 'Upload/Question/20160401/M/2.png', 'image/png', '4078', '1459506175');
INSERT INTO `tb_upload_files` VALUES ('6', '1', 'Upload/Question/20160401/m/9.png', 'image/png', '4034', '1459506175');
INSERT INTO `tb_upload_files` VALUES ('7', '1', 'Upload/Question/20160401/U/1.png', 'image/png', '2269', '1459506379');
INSERT INTO `tb_upload_files` VALUES ('8', '1', 'Upload/Question/20160401/J/8.png', 'image/png', '5933', '1459506379');
INSERT INTO `tb_upload_files` VALUES ('9', '1', 'Upload/Question/20160401/s/8.png', 'image/png', '5933', '1459506885');
INSERT INTO `tb_upload_files` VALUES ('10', '1', 'Upload/Question/20160401/c/15.png', 'image/png', '4796', '1459506885');
INSERT INTO `tb_upload_files` VALUES ('11', '1', 'Upload/Question/20160401/v/1.png', 'image/png', '2269', '1459506927');
INSERT INTO `tb_upload_files` VALUES ('12', '1', 'Upload/Question/20160401/O/16.png', 'image/png', '3128', '1459506927');
INSERT INTO `tb_upload_files` VALUES ('13', '1', 'Upload/Question/20160401/4/2.png', 'image/png', '4078', '1459506949');
INSERT INTO `tb_upload_files` VALUES ('14', '1', 'Upload/Question/20160401/j/12.png', 'image/png', '5734', '1459506949');
INSERT INTO `tb_upload_files` VALUES ('15', '1', 'Upload/Question/20160401/M/1.png', 'image/png', '2269', '1459508359');
INSERT INTO `tb_upload_files` VALUES ('16', '1', 'Upload/Question/20160401/f/2.png', 'image/png', '4078', '1459508359');
INSERT INTO `tb_upload_files` VALUES ('17', '1', 'Upload/Question/20160401/F/3.png', 'image/png', '3797', '1459508434');
INSERT INTO `tb_upload_files` VALUES ('18', '1', 'Upload/Question/20160401/r/4.png', 'image/png', '4207', '1459508434');
INSERT INTO `tb_upload_files` VALUES ('19', '1', 'Upload/Question/20160401/J/5.png', 'image/png', '2955', '1459508434');
INSERT INTO `tb_upload_files` VALUES ('20', '1', 'Upload/Question/20160401/z/17.png', 'image/png', '8255', '1459508496');
INSERT INTO `tb_upload_files` VALUES ('21', '1', 'Upload/Question/20160401/O/18.png', 'image/png', '6693', '1459508496');
INSERT INTO `tb_upload_files` VALUES ('22', '1', 'Upload/Question/20160401/I/19.png', 'image/png', '4983', '1459508496');
INSERT INTO `tb_upload_files` VALUES ('23', '1', 'Upload/Question/20160401/X/3.png', 'image/png', '3797', '1459509830');
INSERT INTO `tb_upload_files` VALUES ('24', '1', 'Upload/Question/20160401/b/10.png', 'image/png', '5640', '1459509830');
INSERT INTO `tb_upload_files` VALUES ('25', '1', 'Upload/Question/20160401/9/17.png', 'image/png', '8255', '1459509830');
INSERT INTO `tb_upload_files` VALUES ('26', '1', 'Upload/Question/20160401/X/2.png', 'image/png', '4078', '1459510077');
INSERT INTO `tb_upload_files` VALUES ('27', '1', 'Upload/Question/20160401/j/9.png', 'image/png', '4034', '1459510077');
INSERT INTO `tb_upload_files` VALUES ('28', '1', 'Upload/Question/20160401/7/16.png', 'image/png', '3128', '1459510077');
INSERT INTO `tb_upload_files` VALUES ('29', '1', 'Upload/Question/20160401/9/1.png', 'image/png', '2269', '1459511725');
INSERT INTO `tb_upload_files` VALUES ('30', '1', 'Upload/Question/20160401/N/2.png', 'image/png', '4078', '1459511725');
INSERT INTO `tb_upload_files` VALUES ('31', '1', 'Upload/Question/20160401/D/3.png', 'image/png', '3797', '1459511725');
INSERT INTO `tb_upload_files` VALUES ('32', '1', 'Upload/Question/20160401/7/4.png', 'image/png', '4207', '1459511725');
INSERT INTO `tb_upload_files` VALUES ('33', '1', 'Upload/Question/20160401/A/1.png', 'image/png', '2269', '1459512914');
INSERT INTO `tb_upload_files` VALUES ('34', '1', 'Upload/Question/20160401/x/8.png', 'image/png', '5933', '1459512914');
INSERT INTO `tb_upload_files` VALUES ('35', '1', 'Upload/Question/20160401/Q/15.png', 'image/png', '4796', '1459512914');
INSERT INTO `tb_upload_files` VALUES ('36', '1', 'Upload/Question/20160401/N/15.png', 'image/png', '4796', '1459512956');
INSERT INTO `tb_upload_files` VALUES ('37', '1', 'Upload/Question/20160401/K/17.png', 'image/png', '8255', '1459512987');
INSERT INTO `tb_upload_files` VALUES ('38', '1', 'Upload/Question/20160401/m/18.png', 'image/png', '6693', '1459512987');
INSERT INTO `tb_upload_files` VALUES ('39', '1', 'Upload/Question/20160401/x/17.png', 'image/png', '8255', '1459512991');
INSERT INTO `tb_upload_files` VALUES ('40', '1', 'Upload/Question/20160401/8/18.png', 'image/png', '6693', '1459512991');
INSERT INTO `tb_upload_files` VALUES ('41', '1', 'Upload/Question/20160401/3/17.png', 'image/png', '8255', '1459513064');
INSERT INTO `tb_upload_files` VALUES ('42', '1', 'Upload/Question/20160401/B/18.png', 'image/png', '6693', '1459513064');
INSERT INTO `tb_upload_files` VALUES ('43', '1', 'Upload/Question/20160401/R/17.png', 'image/png', '8255', '1459513120');
INSERT INTO `tb_upload_files` VALUES ('44', '1', 'Upload/Question/20160401/b/18.png', 'image/png', '6693', '1459513120');
INSERT INTO `tb_upload_files` VALUES ('45', '1', 'Upload/Question/20160401/s/9.png', 'image/png', '4034', '1459513261');
INSERT INTO `tb_upload_files` VALUES ('46', '1', 'Upload/Question/20160401/9/16.png', 'image/png', '3128', '1459513261');
INSERT INTO `tb_upload_files` VALUES ('47', '1', 'Upload/Question/20160401/h/9.png', 'image/png', '4034', '1459513283');
INSERT INTO `tb_upload_files` VALUES ('48', '1', 'Upload/Question/20160401/r/16.png', 'image/png', '3128', '1459513283');
INSERT INTO `tb_upload_files` VALUES ('49', '1', 'Upload/Question/20160401/O/1.png', 'image/png', '2269', '1459513411');
INSERT INTO `tb_upload_files` VALUES ('50', '1', 'Upload/Question/20160401/h/2.png', 'image/png', '4078', '1459513545');
INSERT INTO `tb_upload_files` VALUES ('51', '1', 'Upload/Question/20160401/v/16.png', 'image/png', '3128', '1459513625');
INSERT INTO `tb_upload_files` VALUES ('52', '1', 'Upload/Question/20160401/S/16.png', 'image/png', '3128', '1459513686');
INSERT INTO `tb_upload_files` VALUES ('53', '1', 'Upload/Question/20160401/6/16.png', 'image/png', '3128', '1459513718');
INSERT INTO `tb_upload_files` VALUES ('54', '1', 'Upload/Question/20160401/X/16.png', 'image/png', '3128', '1459513880');
INSERT INTO `tb_upload_files` VALUES ('55', '1', 'Upload/Question/20160405/j/2.png', 'image/png', '4078', '1459820097');
INSERT INTO `tb_upload_files` VALUES ('56', '1', 'Upload/Question/20160405/p/9.png', 'image/png', '4034', '1459820097');
INSERT INTO `tb_upload_files` VALUES ('57', '1', 'Upload/Question/20160405/L/16.png', 'image/png', '3128', '1459820097');
INSERT INTO `tb_upload_files` VALUES ('58', '1', 'Upload/Question/20160405/e/17.png', 'image/png', '8255', '1459820126');
INSERT INTO `tb_upload_files` VALUES ('59', '1', 'Upload/Question/20160405/9/18.png', 'image/png', '6693', '1459820126');
INSERT INTO `tb_upload_files` VALUES ('60', '1', 'Upload/Question/20160405/8/10.png', 'image/png', '5640', '1459820155');
INSERT INTO `tb_upload_files` VALUES ('61', '1', 'Upload/Question/20160405/B/10.png', 'image/png', '5640', '1459820174');
INSERT INTO `tb_upload_files` VALUES ('62', '1', 'Upload/Question/20160405/y/8.png', 'image/png', '5933', '1459820195');
INSERT INTO `tb_upload_files` VALUES ('63', '1', 'Upload/Question/20160405/5/15.png', 'image/png', '4796', '1459820209');
INSERT INTO `tb_upload_files` VALUES ('64', '1', 'Upload/Question/20160405/G/2.png', 'image/png', '4078', '1459820269');
INSERT INTO `tb_upload_files` VALUES ('65', '1', 'Upload/Question/20160405/h/3.png', 'image/png', '3797', '1459820307');
INSERT INTO `tb_upload_files` VALUES ('66', '1', 'Upload/Question/20160405/E/4.png', 'image/png', '4207', '1459820331');
INSERT INTO `tb_upload_files` VALUES ('67', '1', 'Upload/Question/20160405/z/4.png', 'image/png', '4207', '1459820340');
INSERT INTO `tb_upload_files` VALUES ('68', '1', 'Upload/Question/20160405/V/5.png', 'image/png', '2955', '1459820354');
INSERT INTO `tb_upload_files` VALUES ('69', '1', 'Upload/Question/20160405/X/1.png', 'image/png', '2269', '1459820985');
INSERT INTO `tb_upload_files` VALUES ('70', '1', 'Upload/Question/20160405/W/1.png', 'image/png', '2269', '1459820996');
INSERT INTO `tb_upload_files` VALUES ('71', '1', 'Upload/Question/20160405/l/2.png', 'image/png', '4078', '1459821016');
INSERT INTO `tb_upload_files` VALUES ('72', '1', 'Upload/Question/20160405/c/3.png', 'image/png', '3797', '1459821037');
INSERT INTO `tb_upload_files` VALUES ('73', '1', 'Upload/Question/20160405/n/1.png', 'image/png', '2269', '1459821129');
INSERT INTO `tb_upload_files` VALUES ('74', '1', 'Upload/Question/20160405/T/2.png', 'image/png', '4078', '1459821190');
INSERT INTO `tb_upload_files` VALUES ('75', '1', 'Upload/Question/20160405/5/3.png', 'image/png', '3797', '1459821324');
INSERT INTO `tb_upload_files` VALUES ('76', '1', 'Upload/Question/20160405/L/1.png', 'image/png', '2269', '1459821509');
INSERT INTO `tb_upload_files` VALUES ('77', '1', 'Upload/Question/20160405/G/3.png', 'image/png', '3797', '1459821537');
INSERT INTO `tb_upload_files` VALUES ('78', '1', 'Upload/Question/20160405/s/1.png', 'image/png', '2269', '1459821615');
INSERT INTO `tb_upload_files` VALUES ('79', '1', 'Upload/Question/20160405/6/1.png', 'image/png', '2269', '1459821894');
INSERT INTO `tb_upload_files` VALUES ('80', '1', 'Upload/Question/20160405/h/2.png', 'image/png', '4078', '1459821909');
INSERT INTO `tb_upload_files` VALUES ('81', '1', 'Upload/Question/20160405/l/需求分析.txt', 'text/plain', '1084', '1459835659');
INSERT INTO `tb_upload_files` VALUES ('82', '1', 'Upload/Question/20160405/E/ThinkPHP3.2.2.chm', 'application/octet-stream', '1200961', '1459835985');
INSERT INTO `tb_upload_files` VALUES ('83', '1', 'Upload/Question/20160405/E/新建文本文档.txt', 'text/plain', '87', '1459836064');
INSERT INTO `tb_upload_files` VALUES ('84', '1', 'Upload/Question/20160405/L/需求分析.txt', 'text/plain', '1084', '1459836064');
INSERT INTO `tb_upload_files` VALUES ('85', '1', 'Upload/Question/20160406/k/moduleinfo.xml', 'text/plain', '861', '1459906746');
INSERT INTO `tb_upload_files` VALUES ('86', '1', 'Upload/Question/20160406/R/conf.zip', 'application/zip', '98965', '1459907288');
INSERT INTO `tb_upload_files` VALUES ('87', '1', 'Upload/Question/20160406/P/kefu.sql', 'text/plain', '5727', '1459907588');
INSERT INTO `tb_upload_files` VALUES ('88', '1', 'Upload/Question/20160406/K/客服系统_20160405_1850.rp', 'application/zip', '583136', '1459907634');
INSERT INTO `tb_upload_files` VALUES ('89', '1', 'Upload/Question/20160406/y/客服系统.rp', 'application/zip', '614937', '1459908124');
INSERT INTO `tb_upload_files` VALUES ('90', '1', 'Upload/Question/20160406/B/客服系统_20160304_1831.rp', 'application/zip', '375066', '1459908124');
INSERT INTO `tb_upload_files` VALUES ('91', '1', 'Upload/Question/20160406/L/客服系统_20160307_1602.rp', 'application/zip', '442011', '1459908124');
INSERT INTO `tb_upload_files` VALUES ('92', '1', './Question/20160406/Y/需求分析.txt', 'text/plain', '1084', '1459940919');
INSERT INTO `tb_upload_files` VALUES ('93', '1', 'Upload/Question/20160408/4/56d3e7ffb7957_1024.jpg', 'image/jpeg', '625138', '1460086736');
INSERT INTO `tb_upload_files` VALUES ('94', '1', 'Upload/Question/20160408/L/56d3e7ffb7957_1024.jpg', 'image/jpeg', '625138', '1460093989');
INSERT INTO `tb_upload_files` VALUES ('95', '1', 'Upload/Question/20160408/p/a.txt', 'application/octet-stream', '1', '1460093989');
INSERT INTO `tb_upload_files` VALUES ('96', '1', 'Upload/Question/20160408/r/56d3e7ffb7957_1024.jpg', 'image/jpeg', '625138', '1460094870');
INSERT INTO `tb_upload_files` VALUES ('97', '1', 'Upload/Question/20160408/r/a.txt', 'application/octet-stream', '1', '1460094870');
INSERT INTO `tb_upload_files` VALUES ('98', '1', 'Upload/Question/20160408/i/b.txt', 'application/octet-stream', '1', '1460094870');
INSERT INTO `tb_upload_files` VALUES ('99', '1', 'Upload/Question/20160408/F/d.txt', 'application/octet-stream', '1', '1460094870');
INSERT INTO `tb_upload_files` VALUES ('100', '1', 'Upload/Question/20160411/x/登录窗口.jpg', 'image/jpeg', '414332', '1460337207');
INSERT INTO `tb_upload_files` VALUES ('101', '1', 'Upload/Question/20160411/J/忘记密码.png', 'image/png', '2036420', '1460337207');
INSERT INTO `tb_upload_files` VALUES ('102', '1', 'Upload/Question/20160413/w/客服系统_20160405_1850.rp', 'application/zip', '583141', '1460511627');
INSERT INTO `tb_upload_files` VALUES ('103', '1', 'Upload/Question/20160414/t/appinfo.xml', 'text/plain', '1053', '1460625209');
INSERT INTO `tb_upload_files` VALUES ('104', '1', 'Upload/Question/20160415/S/客服系统_20160304_1831.rp', 'application/zip', '375066', '1460707242');
INSERT INTO `tb_upload_files` VALUES ('105', '1', 'Upload/Question/20160415/j/c.txt', 'application/octet-stream', '1', '1460707322');
INSERT INTO `tb_upload_files` VALUES ('106', '1', 'Upload/Question/20160415/U/d.txt', 'application/octet-stream', '1', '1460707322');
INSERT INTO `tb_upload_files` VALUES ('107', '1', 'Upload/Question/20160415/H/56d3e7ffb7957_1024.jpg', 'image/jpeg', '625138', '1460707322');
INSERT INTO `tb_upload_files` VALUES ('108', '1', 'Upload/Question/20160415/A/appinfo.xml', 'text/plain', '1053', '1460710014');
INSERT INTO `tb_upload_files` VALUES ('109', '1', 'Upload/Question/20160415/bTIECP/a.txt', 'application/octet-stream', '1', '1460711121');
INSERT INTO `tb_upload_files` VALUES ('110', '1', 'Upload/Question/20160415/msKDfQ/d.txt', 'application/octet-stream', '1', '1460711121');
INSERT INTO `tb_upload_files` VALUES ('111', '1', 'Upload/Question/20160415/ShUyx4/appinfo.xml', 'text/plain', '1053', '1460712833');
INSERT INTO `tb_upload_files` VALUES ('112', '1', 'Upload/Question/20160415/O39Dtt/appinfo.xml', 'text/plain', '1053', '1460713021');
INSERT INTO `tb_upload_files` VALUES ('113', '1', 'Upload/Question/20160415/aNKWIC/moduleinfo.xml', 'text/plain', '861', '1460713021');
INSERT INTO `tb_upload_files` VALUES ('114', '1', 'Upload/Question/20160415/FVutaV/base-1.0.9.pom', 'application/xml', '895', '1460713316');
INSERT INTO `tb_upload_files` VALUES ('115', '1', 'Upload/Question/20160415/G17tQc/c.txt', 'application/octet-stream', '1', '1460715691');
INSERT INTO `tb_upload_files` VALUES ('116', '1', 'Upload/Question/20160415/T7NF6m/d.txt', 'application/octet-stream', '1', '1460715691');
INSERT INTO `tb_upload_files` VALUES ('117', '1', 'Upload/Question/20160418/hv8zpY/moduleinfo_ttt.xml', 'text/plain', '861', '1460959478');
INSERT INTO `tb_upload_files` VALUES ('118', '1', 'Upload/Question/20160418/szPtyr/appinfo.xml', 'text/plain', '1053', '1460959516');
INSERT INTO `tb_upload_files` VALUES ('119', '1', 'Upload/Question/20160418/EZh4MQ/channel.json', 'text/plain', '3762', '1460960306');
INSERT INTO `tb_upload_files` VALUES ('120', '1', 'Upload/Question/20160422/MT4dfj/yibo.bmp', 'image/x-ms-bmp', '922676', '1461321020');
INSERT INTO `tb_upload_files` VALUES ('121', '1', 'Upload/Question/20160428/VDSrEC/kefu.sql', 'text/plain', '5727', '1461807194');
INSERT INTO `tb_upload_files` VALUES ('122', '1', 'Upload/Question/20160428/WR77p8/Android前端发布系统.mm', 'application/x-freemind', '4974', '1461808998');
INSERT INTO `tb_upload_files` VALUES ('123', '1', 'Upload/Question/20160428/jws6bt/test.txt', 'text/plain', '8', '1461839915');
INSERT INTO `tb_upload_files` VALUES ('124', '1', 'Upload/Question/20160428/36qCvp/测试.txt', 'text/plain', '12', '1461839915');
INSERT INTO `tb_upload_files` VALUES ('125', '', 'Upload/Question/20160428/HZsAgn/test.txt', 'text/plain', '8', '1461839966');
INSERT INTO `tb_upload_files` VALUES ('126', '', 'Upload/Question/20160428/GRcLDd/测试.txt', 'text/plain', '12', '1461839966');
INSERT INTO `tb_upload_files` VALUES ('127', '', 'Upload/Question/20160428/hxwP9X/baidu_web.png', 'image/png', '3790', '1461840167');
INSERT INTO `tb_upload_files` VALUES ('128', '', 'Upload/Question/20160428/BxvDoh/test.txt', 'text/plain', '8', '1461840167');
INSERT INTO `tb_upload_files` VALUES ('129', '', 'Upload/Question/20160428/RxOMsn/test.txt', 'text/plain', '8', '1461840167');
INSERT INTO `tb_upload_files` VALUES ('130', '', 'Upload/Question/20160428/7GhQ9G/测试.txt', 'text/plain', '12', '1461840167');

-- ----------------------------
-- Table structure for tb_user
-- ----------------------------
DROP TABLE IF EXISTS `tb_user`;
CREATE TABLE `tb_user` (
  `id` int(32) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(32) NOT NULL COMMENT '用户名，Unique',
  `name` varchar(32) NOT NULL COMMENT '昵称，Unique',
  `passwd` varchar(32) NOT NULL COMMENT '登录密码',
  `permission` enum('root','admin','online','normal','customer') NOT NULL COMMENT '权限：超级管理员、客服管理员、在线客服、普通客服、客户',
  `email` varchar(64) NOT NULL COMMENT '用户邮箱',
  `note` varchar(255) NOT NULL COMMENT '备注',
  `modify_passwd` enum('false','true') NOT NULL COMMENT '是否修改初始密码',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user` (`user`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Records of tb_user
-- ----------------------------
INSERT INTO `tb_user` VALUES ('1', 'root', '管理员222', '25d55ad283aa400af464c76d713c07ad', 'root', '123@qq.com', '', 'true');
INSERT INTO `tb_user` VALUES ('3', 'admin', '客服管理员', '25d55ad283aa400af464c76d713c07ad', 'admin', '1353178739@qq.com', '', 'true');
INSERT INTO `tb_user` VALUES ('7', 'jatai', 'jatai', '25d55ad283aa400af464c76d713c07ad', 'root', '1@qq.com', 'jatai', 'true');
INSERT INTO `tb_user` VALUES ('9', 'service', '客服service', '25d55ad283aa400af464c76d713c07ad', 'admin', 'service@ipmacro.com', '客服', 'true');
INSERT INTO `tb_user` VALUES ('10', 'ecjon', 'gg客服管理员', '25d55ad283aa400af464c76d713c07ad', 'admin', '123456444@qq.com', '我是客服管理员', 'true');
INSERT INTO `tb_user` VALUES ('22', 'style_lee', 'style_lee', '25f9e794323b453885f5181f1b624d0b', 'admin', '123456798@163.com', '', 'true');
INSERT INTO `tb_user` VALUES ('23', 'yangxue', '阳雪', '25d55ad283aa400af464c76d713c07ad', 'admin', 'yangxue@ipmacro.com', '', 'true');
INSERT INTO `tb_user` VALUES ('26', 'customer', '客户customer', '25d55ad283aa400af464c76d713c07ad', 'customer', 'human@ipmacro.com', 'dsfdsggsahhhsdfdhgh', 'true');
INSERT INTO `tb_user` VALUES ('34', 'testtest', 'testtestttt', '1bbd886460827015e5d605ed44252251', '', 'test@test.test', '', 'true');
INSERT INTO `tb_user` VALUES ('35', 'style_lee_test', '测试专用', '25f9e794323b453885f5181f1b624d0b', 'admin', '123456789@163.com', '', 'true');
INSERT INTO `tb_user` VALUES ('36', 'ztokay', 'ztokay_nick', '25f9e794323b453885f5181f1b624d0b', 'customer', '135317873@qq.com', '', 'false');
INSERT INTO `tb_user` VALUES ('37', 'testtttttttt', 'testtttttttt', '25d55ad283aa400af464c76d713c07ad', 'customer', 'tet@test.test', '', 'false');
INSERT INTO `tb_user` VALUES ('38', 'duxiaoyao', 'xiaoyao', '25d55ad283aa400af464c76d713c07ad', 'admin', '1005133914@qq.com', '', 'true');

-- ----------------------------
-- Table structure for tb_vendorid
-- ----------------------------
DROP TABLE IF EXISTS `tb_vendorid`;
CREATE TABLE `tb_vendorid` (
  `id` int(32) unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` varchar(8) NOT NULL,
  `note` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8 COMMENT='vendorID表';

-- ----------------------------
-- Records of tb_vendorid
-- ----------------------------
INSERT INTO `tb_vendorid` VALUES ('7', 'time2822', '1458124920');
INSERT INTO `tb_vendorid` VALUES ('8', 'time267', '1458124920');
INSERT INTO `tb_vendorid` VALUES ('9', 'time1031', '1458124920');
INSERT INTO `tb_vendorid` VALUES ('10', 'time6853', '1458124920');
INSERT INTO `tb_vendorid` VALUES ('11', 'time5870', '1458124920');
INSERT INTO `tb_vendorid` VALUES ('12', 'time3012', '1458124921');
INSERT INTO `tb_vendorid` VALUES ('100', '商家标识83', '备注83');
INSERT INTO `tb_vendorid` VALUES ('101', '商家标识84', '备注84');
INSERT INTO `tb_vendorid` VALUES ('102', '商家标识85', '备注85');
INSERT INTO `tb_vendorid` VALUES ('103', '商家标识86', '备注86');
INSERT INTO `tb_vendorid` VALUES ('107', '商家标识90', '备注90');
INSERT INTO `tb_vendorid` VALUES ('108', '商家标识91', '备注91');
INSERT INTO `tb_vendorid` VALUES ('109', '商家标识92', '备注92');
INSERT INTO `tb_vendorid` VALUES ('110', '商家标识93', '备注93');
INSERT INTO `tb_vendorid` VALUES ('111', '商家标识94', '备注94');
INSERT INTO `tb_vendorid` VALUES ('112', '商家标识95', '备注95');
INSERT INTO `tb_vendorid` VALUES ('113', '商家标识96', '备注96');
INSERT INTO `tb_vendorid` VALUES ('114', '商家标识97', '备注97');
INSERT INTO `tb_vendorid` VALUES ('116', '商家标识99', '备注99');
INSERT INTO `tb_vendorid` VALUES ('117', 'fdasfds', 'fdasfds');
INSERT INTO `tb_vendorid` VALUES ('119', '1234', '');
INSERT INTO `tb_vendorid` VALUES ('120', '3161', '');
INSERT INTO `tb_vendorid` VALUES ('121', '2202', '');
INSERT INTO `tb_vendorid` VALUES ('122', '6006', '');
INSERT INTO `tb_vendorid` VALUES ('123', '3126', '');
INSERT INTO `tb_vendorid` VALUES ('125', '1235', '');
INSERT INTO `tb_vendorid` VALUES ('126', '1dw14325', '');
INSERT INTO `tb_vendorid` VALUES ('127', '0103', '');
INSERT INTO `tb_vendorid` VALUES ('129', '23', 'asw');
INSERT INTO `tb_vendorid` VALUES ('130', '1111', 'adasrf');
