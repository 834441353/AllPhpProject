SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_shop_cats`;
CREATE TABLE `wst_shop_cats` (
  `catId` int(11) NOT NULL AUTO_INCREMENT,
  `shopId` int(11) NOT NULL,
  `parentId` int(11) NOT NULL,
  `isShow` tinyint(4) NOT NULL DEFAULT '1',
  `catName` varchar(100) NOT NULL,
  `catSort` int(11) NOT NULL DEFAULT '0',
  `dataFlag` tinyint(4) NOT NULL DEFAULT '1',
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`catId`),
  KEY `parentId` (`isShow`,`dataFlag`) USING BTREE,
  KEY `shopId` (`shopId`,`dataFlag`)
) ENGINE=InnoDB AUTO_INCREMENT=156 DEFAULT CHARSET=utf8;

INSERT INTO `wst_shop_cats` VALUES ('1', '1', '0', '1', '手机电器', '5', '1', '0000-00-00 00:00:00'),
('2', '1', '0', '1', '食品饮料', '1', '1', '0000-00-00 00:00:00'),
('3', '1', '0', '1', '生鲜品类', '0', '1', '0000-00-00 00:00:00'),
('4', '1', '1', '1', '手机', '0', '1', '0000-00-00 00:00:00'),
('5', '1', '1', '1', '畅玩', '0', '1', '0000-00-00 00:00:00'),
('6', '1', '1', '1', '华为', '0', '1', '0000-00-00 00:00:00'),
('7', '1', '1', '1', 'Mate/P系列', '0', '1', '0000-00-00 00:00:00'),
('8', '1', '1', '1', '华为', '0', '1', '0000-00-00 00:00:00'),
('9', '1', '1', '1', 'G系列', '0', '1', '0000-00-00 00:00:00'),
('10', '1', '1', '1', '畅享系列', '0', '1', '0000-00-00 00:00:00'),
('11', '1', '1', '1', '运营商合约', '0', '-1', '0000-00-00 00:00:00'),
('12', '2', '0', '1', '进口水果', '0', '1', '0000-00-00 00:00:00'),
('13', '2', '12', '1', '苹果类', '0', '1', '0000-00-00 00:00:00'),
('14', '2', '12', '1', '梨子类', '0', '1', '0000-00-00 00:00:00'),
('15', '2', '12', '1', '柑橘类', '0', '1', '0000-00-00 00:00:00'),
('16', '2', '12', '1', '橙柚类', '0', '1', '0000-00-00 00:00:00'),
('17', '2', '12', '1', '葡提类', '0', '1', '0000-00-00 00:00:00'),
('18', '2', '12', '1', '桃李类', '0', '1', '0000-00-00 00:00:00'),
('19', '2', '12', '1', '浆果类', '0', '1', '0000-00-00 00:00:00'),
('20', '2', '0', '1', '国产水果', '0', '1', '0000-00-00 00:00:00'),
('21', '2', '0', '1', '水果礼盒', '0', '1', '0000-00-00 00:00:00'),
('22', '2', '20', '1', '苹果类', '0', '1', '0000-00-00 00:00:00'),
('23', '2', '20', '1', '梨子类', '0', '1', '0000-00-00 00:00:00'),
('24', '2', '20', '1', '柑橘类', '0', '1', '0000-00-00 00:00:00'),
('25', '2', '20', '1', '橙柚类', '0', '1', '0000-00-00 00:00:00'),
('26', '2', '20', '1', '葡提类', '0', '1', '0000-00-00 00:00:00'),
('27', '2', '20', '1', '桃李类', '0', '1', '0000-00-00 00:00:00'),
('28', '2', '20', '1', '浆果类', '0', '1', '0000-00-00 00:00:00'),
('29', '2', '21', '1', '三合一礼盒', '0', '1', '0000-00-00 00:00:00'),
('30', '2', '21', '1', '四合一礼盒', '0', '1', '0000-00-00 00:00:00'),
('31', '2', '21', '1', '其它礼盒', '0', '1', '0000-00-00 00:00:00'),
('32', '3', '0', '1', '有机蔬菜', '0', '1', '0000-00-00 00:00:00'),
('33', '3', '0', '1', '根茎•瓜类', '0', '1', '0000-00-00 00:00:00'),
('34', '3', '0', '1', '茄果•花菜•豆', '0', '1', '0000-00-00 00:00:00'),
('35', '3', '0', '1', '加工蔬菜', '0', '1', '0000-00-00 00:00:00'),
('36', '3', '0', '1', '礼盒/券', '0', '1', '0000-00-00 00:00:00'),
('37', '3', '32', '1', '茄子/豆角/辣椒', '0', '1', '0000-00-00 00:00:00'),
('38', '3', '32', '1', '番茄类', '0', '1', '0000-00-00 00:00:00'),
('39', '3', '32', '1', '叶菜/生菜', '0', '1', '0000-00-00 00:00:00'),
('40', '3', '32', '1', '土豆/山药', '0', '1', '0000-00-00 00:00:00'),
('41', '3', '32', '1', '胡萝卜/洋葱', '0', '1', '0000-00-00 00:00:00'),
('42', '4', '0', '1', '卷纸', '0', '1', '0000-00-00 00:00:00'),
('43', '4', '42', '1', '有芯', '0', '1', '0000-00-00 00:00:00'),
('44', '4', '42', '1', '无芯', '0', '1', '0000-00-00 00:00:00'),
('45', '4', '0', '1', '纸抽', '0', '1', '0000-00-00 00:00:00'),
('46', '4', '0', '1', '湿巾', '0', '1', '0000-00-00 00:00:00'),
('47', '4', '45', '1', '软抽', '0', '1', '0000-00-00 00:00:00'),
('48', '4', '45', '1', '盒抽', '0', '1', '0000-00-00 00:00:00'),
('49', '4', '46', '1', '成人湿巾', '0', '1', '0000-00-00 00:00:00'),
('50', '4', '46', '1', '儿童湿巾', '0', '1', '0000-00-00 00:00:00'),
('51', '4', '46', '1', '婴儿湿巾', '0', '1', '0000-00-00 00:00:00'),
('52', '6', '0', '1', '白酒', '0', '1', '0000-00-00 00:00:00'),
('53', '6', '0', '1', '红酒', '0', '1', '0000-00-00 00:00:00'),
('54', '6', '0', '1', '洋酒', '0', '1', '0000-00-00 00:00:00'),
('55', '6', '0', '1', '啤酒', '0', '1', '0000-00-00 00:00:00'),
('56', '6', '0', '1', '黄酒', '0', '1', '0000-00-00 00:00:00'),
('57', '6', '0', '1', '保健酒', '0', '1', '0000-00-00 00:00:00'),
('58', '6', '0', '1', '预调酒', '0', '1', '0000-00-00 00:00:00'),
('59', '6', '0', '1', '配制酒', '0', '1', '0000-00-00 00:00:00'),
('60', '6', '55', '1', '黄啤酒', '0', '1', '0000-00-00 00:00:00'),
('61', '6', '55', '1', '白啤酒', '0', '1', '0000-00-00 00:00:00'),
('62', '6', '55', '1', '黑啤酒', '0', '1', '0000-00-00 00:00:00'),
('63', '7', '0', '1', '食用油', '0', '1', '0000-00-00 00:00:00'),
('64', '7', '63', '1', '橄榄油', '0', '1', '0000-00-00 00:00:00'),
('65', '7', '63', '1', '葵花籽油', '0', '1', '0000-00-00 00:00:00'),
('66', '7', '63', '1', '大豆油', '0', '1', '0000-00-00 00:00:00'),
('67', '7', '63', '1', '玉米油', '0', '1', '0000-00-00 00:00:00'),
('68', '7', '63', '1', '花生油', '0', '1', '0000-00-00 00:00:00'),
('69', '7', '63', '1', '调和油', '0', '1', '0000-00-00 00:00:00'),
('70', '7', '0', '1', '大米面粉', '0', '1', '0000-00-00 00:00:00'),
('71', '7', '0', '1', '调味品', '0', '1', '0000-00-00 00:00:00'),
('72', '7', '0', '1', '方便速食', '0', '1', '0000-00-00 00:00:00'),
('73', '7', '0', '1', '菌菇干货', '0', '1', '0000-00-00 00:00:00'),
('74', '8', '0', '1', '坚果/炒货', '0', '1', '0000-00-00 00:00:00'),
('75', '8', '0', '1', '果干/蜜饯', '0', '1', '0000-00-00 00:00:00'),
('76', '8', '0', '1', '糕点/点心', '0', '1', '0000-00-00 00:00:00'),
('77', '8', '0', '1', '饼干/膨化', '0', '1', '0000-00-00 00:00:00'),
('78', '8', '0', '1', '素食/卤味', '0', '1', '0000-00-00 00:00:00'),
('79', '8', '0', '1', '海味/河鲜', '0', '1', '0000-00-00 00:00:00'),
('80', '8', '74', '1', '碧根果', '0', '1', '0000-00-00 00:00:00'),
('81', '8', '74', '1', '开口松子', '0', '1', '0000-00-00 00:00:00'),
('82', '8', '74', '1', '夏威夷果', '0', '1', '0000-00-00 00:00:00'),
('83', '8', '74', '1', '开心果', '0', '1', '0000-00-00 00:00:00'),
('84', '8', '74', '1', '纸皮核桃', '0', '1', '0000-00-00 00:00:00'),
('85', '8', '74', '1', '手剥巴旦木', '0', '1', '0000-00-00 00:00:00'),
('86', '8', '74', '1', '瓜子', '0', '1', '0000-00-00 00:00:00'),
('87', '8', '75', '1', '芒果干', '0', '1', '0000-00-00 00:00:00'),
('88', '8', '75', '1', '冻干榴莲干', '0', '1', '0000-00-00 00:00:00'),
('89', '8', '75', '1', '草莓干', '0', '1', '0000-00-00 00:00:00'),
('90', '8', '75', '1', '猕猴桃干', '0', '1', '0000-00-00 00:00:00'),
('91', '8', '75', '1', '木瓜干', '0', '1', '0000-00-00 00:00:00'),
('92', '9', '0', '1', '彩妆', '0', '1', '0000-00-00 00:00:00'),
('93', '9', '0', '1', '护肤', '0', '1', '0000-00-00 00:00:00'),
('94', '9', '0', '1', '唇膏', '0', '1', '0000-00-00 00:00:00'),
('95', '9', '0', '1', '香水', '0', '1', '0000-00-00 00:00:00'),
('96', '9', '92', '1', '面部', '0', '1', '0000-00-00 00:00:00'),
('97', '9', '92', '1', '脸部', '0', '1', '0000-00-00 00:00:00'),
('98', '9', '92', '1', '唇部', '0', '1', '0000-00-00 00:00:00'),
('99', '9', '92', '1', '卸妆', '0', '1', '0000-00-00 00:00:00'),
('100', '9', '95', '1', '女士香水', '0', '1', '0000-00-00 00:00:00'),
('101', '9', '95', '1', '男士香水', '0', '1', '0000-00-00 00:00:00'),
('102', '9', '95', '1', '香水礼盒', '0', '1', '0000-00-00 00:00:00'),
('103', '10', '0', '1', '安神保健', '0', '1', '0000-00-00 00:00:00'),
('104', '10', '0', '1', '营养补充剂', '0', '1', '0000-00-00 00:00:00'),
('105', '10', '0', '1', '芦荟玛卡', '0', '1', '0000-00-00 00:00:00'),
('106', '10', '0', '1', '维生素', '0', '1', '0000-00-00 00:00:00'),
('107', '10', '0', '1', '安神补脑', '0', '1', '0000-00-00 00:00:00'),
('108', '10', '103', '1', '阿胶', '0', '1', '0000-00-00 00:00:00'),
('109', '10', '103', '1', '三楂', '0', '1', '0000-00-00 00:00:00'),
('110', '10', '103', '1', '蜜枣', '0', '1', '0000-00-00 00:00:00'),
('111', '11', '0', '1', '手机专区', '0', '1', '0000-00-00 00:00:00'),
('112', '11', '0', '1', '平板电脑', '0', '1', '0000-00-00 00:00:00'),
('113', '11', '0', '1', '穿戴设备', '0', '1', '0000-00-00 00:00:00'),
('114', '11', '0', '1', '精品配件', '0', '1', '0000-00-00 00:00:00'),
('115', '11', '111', '1', '荣耀手机', '0', '1', '0000-00-00 00:00:00'),
('116', '1', '0', '1', '个人护理', '2', '1', '0000-00-00 00:00:00'),
('117', '1', '0', '1', '清洁用品', '3', '1', '0000-00-00 00:00:00'),
('118', '1', '0', '1', '母婴用品', '4', '1', '0000-00-00 00:00:00'),
('119', '1', '0', '1', '中外名酒', '7', '1', '0000-00-00 00:00:00'),
('120', '1', '2', '1', '坚果零食', '0', '1', '0000-00-00 00:00:00'),
('121', '1', '2', '1', '糖果饼干', '0', '1', '0000-00-00 00:00:00'),
('122', '1', '2', '1', '水饮茶冲', '0', '1', '0000-00-00 00:00:00'),
('123', '1', '3', '1', '新鲜水果', '1', '1', '0000-00-00 00:00:00'),
('124', '1', '3', '1', '禽类蛋品', '2', '1', '0000-00-00 00:00:00'),
('125', '1', '3', '1', '饮品甜品', '3', '1', '0000-00-00 00:00:00'),
('126', '1', '3', '1', '新鲜蔬菜', '4', '1', '0000-00-00 00:00:00'),
('127', '1', '3', '1', '海鲜水产', '5', '1', '0000-00-00 00:00:00'),
('128', '1', '3', '1', '猪牛羊肉', '6', '1', '0000-00-00 00:00:00'),
('129', '1', '3', '1', '冷冻速食', '7', '1', '0000-00-00 00:00:00'),
('130', '1', '3', '1', '冷冻速食', '8', '-1', '0000-00-00 00:00:00'),
('131', '1', '3', '1', '冷冻速食', '9', '-1', '0000-00-00 00:00:00'),
('132', '1', '3', '1', '冷冻速食', '10', '-1', '0000-00-00 00:00:00'),
('133', '1', '3', '1', '冷冻速食', '11', '-1', '0000-00-00 00:00:00'),
('134', '1', '116', '1', '洗发护发', '0', '1', '0000-00-00 00:00:00'),
('135', '1', '116', '1', '卫生护理', '0', '1', '0000-00-00 00:00:00'),
('136', '1', '116', '1', '身体护理', '0', '1', '0000-00-00 00:00:00'),
('137', '1', '116', '1', '口腔护理', '0', '1', '0000-00-00 00:00:00'),
('138', '1', '116', '1', '洗护沐浴', '0', '1', '0000-00-00 00:00:00'),
('139', '1', '117', '1', '纸品湿巾', '0', '1', '0000-00-00 00:00:00'),
('140', '1', '117', '1', '衣物清洁', '1', '1', '0000-00-00 00:00:00'),
('141', '1', '117', '1', '清洁工具', '2', '1', '0000-00-00 00:00:00'),
('142', '1', '117', '1', '家庭清洁', '3', '1', '0000-00-00 00:00:00'),
('143', '1', '117', '1', '一次性用品', '4', '1', '0000-00-00 00:00:00'),
('144', '1', '117', '1', '驱虫用品', '5', '1', '0000-00-00 00:00:00'),
('145', '1', '117', '1', '皮具护理', '6', '1', '0000-00-00 00:00:00'),
('146', '1', '118', '1', '营养辅食', '0', '1', '0000-00-00 00:00:00'),
('147', '1', '118', '1', '尿裤湿巾', '0', '1', '0000-00-00 00:00:00'),
('148', '1', '118', '1', '喂养用品', '0', '1', '0000-00-00 00:00:00'),
('149', '1', '118', '1', '洗护用品', '0', '1', '0000-00-00 00:00:00'),
('150', '1', '118', '1', '寝居服饰', '0', '1', '0000-00-00 00:00:00'),
('151', '1', '118', '1', '妈妈专区', '0', '1', '0000-00-00 00:00:00'),
('152', '1', '118', '1', '童车童床', '0', '1', '0000-00-00 00:00:00'),
('153', '1', '118', '1', '儿童乐器', '0', '1', '0000-00-00 00:00:00'),
('154', '1', '118', '1', '儿童玩具', '0', '1', '0000-00-00 00:00:00'),
('155', '1', '2', '1', '牛奶饮品', '0', '1', '0000-00-00 00:00:00');
