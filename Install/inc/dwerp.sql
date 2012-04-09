CREATE TABLE IF NOT EXISTS `customer`
(
   `customer_id`          int(8) not null auto_increment,
   `customer_name`        varchar(64),
   primary key (`customer_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `item`
(
   `item_id`              smallint(7) not null  auto_increment,
   `po_id`                varchar(8),
   `product_id`           smallint(5),
   `ps_id`                smallint(2),
   `unit_id`              smallint(3),
   `width`                double,
   `color`                varchar(20),
   `qty`                  double,
   `date_delivery`        date,
   `specail_require`      varchar(100),
   primary key (`item_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `operate_record`
(
   `opr_id`               int(20) not null  auto_increment,
   `item_id`              smallint(7),
   `user_id`              varchar(64),
   `ps_id`                smallint(2),
   `opr_date`             date,
   primary key (`opr_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `po`
(
   `po_id`                varchar(8) not null,
   `user_id`              varchar(64),
   `customer_id`          integer(8),
   `date_place`           date,
   primary key (`po_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `product_state`
(
   `ps_id`                smallint(2) not null  auto_increment,
   `ps_name`              varchar(50),
   primary key (`ps_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `product_state` (`ps_id`, `ps_name`) VALUES
(1, '无坯'),
(2, '等坯'),
(3, '在库'),
(4, '染厂排队'),
(5, '染厂坯定'),
(6, '染厂待进缸'),
(7, '染厂待染色'),
(8, '染厂待成品定型'),
(9, '染厂待整理'),
(10, '部分成品在染厂'),
(11, '成品在染厂'),
(12, '成品在库');

CREATE TABLE IF NOT EXISTS `product_type`
(
   `product_id`           smallint(5) not null  auto_increment,
   `product_type`         varchar(50),
   `weight`               varchar(50),
   `pattern`              varchar(50),
   primary key (`product_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `role`
(
   `role_id`              tinyint(1) not null auto_increment,
   `role_name`            varchar(20),
   primary key (`role_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `role` (`role_id`, `role_name`) VALUES
(1, '管理员'),
(2, '老板'),
(3, '业务员'),
(4, '工人');

CREATE TABLE IF NOT EXISTS `unit`
(
   `unit_id`              smallint(3) not null auto_increment,
   `unit_name`            varchar(10),
   primary key (`unit_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `user`
(
   `user_id`              varchar(64) not null,
   `role_id`              tinyint(1),
   `user_pwd`             char(32),
   `user_name`            varchar(50),
   `status`               tinyint(1) DEFAULT '0',
   primary key (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `node` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `remark` varchar(255) DEFAULT NULL,
  `sort` smallint(6) unsigned DEFAULT NULL,
  `pid` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) unsigned NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `group_id` tinyint(3) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `level` (`level`),
  KEY `pid` (`pid`),
  KEY `status` (`status`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=34 ;


INSERT INTO `node` (`id`, `name`, `title`, `status`, `remark`, `sort`, `pid`, `level`, `type`, `group_id`) VALUES
(1, 'Admin', '后台管理', 1, NULL, NULL, 0, 1, 0, 0),
(2, 'Node', '节点管理', 1, NULL, NULL, 1, 2, 0, 1),
(3, 'User', '用户管理', 1, '', NULL, 1, 2, 0, 1),
(4, 'Role', '群组管理', 1, '', NULL, 1, 2, 0, 1),
(5, 'Public', '公共模块', 1, '', NULL, 1, 2, 0, 0),
(6, 'Index', '默认模块', 1, '', NULL, 1, 2, 0, 0),
(7, 'index', '后台首页', 1, '', NULL, 6, 3, 0, 0),
(8, 'index', '列表', 1, '', NULL, 2, 3, 0, 0),
(9, 'add', '添加', 1, '', NULL, 2, 3, 0, 0),
(10, 'foreverdelete', '删除', 1, '', NULL, 2, 3, 0, 0),
(11, 'edit', '修改', 1, '', NULL, 2, 3, 0, 0),
(12, 'insert', '写入', 1, '', NULL, 2, 3, 0, 0),
(13, 'forbid', '禁用', 1, '', NULL, 2, 3, 0, 0),
(14, 'update', '更新', 1, '', NULL, 2, 3, 0, 0),
(15, 'resume', '恢复', 1, '', NULL, 2, 3, 0, 0),
(16, 'Category', '分类管理', 1, '', NULL, 1, 2, 0, 2),
(17, 'Article', '文章管理', 1, '', NULL, 1, 2, 0, 2),
(18, 'index', '列表', 1, '', NULL, 16, 3, 0, 0),
(19, 'add', '新增', 1, '', NULL, 16, 3, 0, 0),
(20, 'foreverdelete', '删除', 1, '', NULL, 16, 3, 0, 0),
(21, 'edit', '编辑', 1, '', NULL, 16, 3, 0, 0),
(22, 'insert', '写入', 1, '', NULL, 16, 3, 0, 0),
(23, 'forbid', '禁用', 1, '', NULL, 16, 3, 0, 0),
(24, 'update', '更新', 1, '', NULL, 16, 3, 0, 0),
(25, 'resume', '恢复', 1, '', NULL, 16, 3, 0, 0),
(26, 'Music', '音乐管理', 1, '', 0, 1, 2, 0, 2),
(27, 'Video', '视频管理', 1, '', 0, 1, 2, 0, 2),
(28, 'Photo', '图片管理', 1, '', 0, 1, 2, 0, 2),
(29, 'Link', '链接管理', 1, '', 0, 1, 2, 0, 2),
(30, 'Diary', '日记管理', 1, '', 0, 1, 2, 0, 2),
(31, 'Message', '留言评论', 1, '', 0, 1, 2, 0, 2),
(32, 'System', '系统功能', 1, '', 0, 1, 2, 0, 1),
(33, 'Router', '路由列表', 1, '', 0, 1, 2, 0, 2),
(34, 'File', '文件管理', 1, '', 0, 1, 2, 0, 1);

alter table ITEM add constraint FK_Reference_11 foreign key (PRODUCT_ID)
      references PRODUCT_TYPE (PRODUCT_ID) on delete restrict on update restrict;

alter table ITEM add constraint FK_Reference_14 foreign key (PO_ID)
      references PO (PO_ID) on delete restrict on update restrict;

alter table ITEM add constraint FK_Reference_15 foreign key (PS_ID)
      references PRODUCT_STATE (PS_ID) on delete restrict on update restrict;

alter table ITEM add constraint FK_Reference_16 foreign key (UNIT_ID)
      references UNIT (UNIT_ID) on delete restrict on update restrict;

alter table OPERATE_RECORD add constraint FK_Reference_17 foreign key (ITEM_ID)
      references ITEM (ITEM_ID) on delete restrict on update restrict;

alter table OPERATE_RECORD add constraint FK_Reference_18 foreign key (USER_ID)
      references USER (USER_ID) on delete restrict on update restrict;

alter table OPERATE_RECORD add constraint FK_Reference_19 foreign key (PS_ID)
      references PRODUCT_STATE (PS_ID) on delete restrict on update restrict;

alter table PO add constraint FK_Reference_10 foreign key (CUSTOMER_ID)
      references CUSTOMER (CUSTOMER_ID) on delete restrict on update restrict;

alter table PO add constraint FK_Reference_13 foreign key (USER_ID)
      references USER (USER_ID) on delete restrict on update restrict;

alter table USER add constraint FK_Reference_12 foreign key (ROLE_ID)
      references ROLE (ROLE_ID) on delete restrict on update restrict;