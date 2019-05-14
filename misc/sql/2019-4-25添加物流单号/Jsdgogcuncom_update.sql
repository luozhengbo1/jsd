 alter table `ims_weisrc_dish_order` add   `logistics_number` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '物流号';
 CREATE TABLE `ims_weisrc_dish_sendmsg` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL COMMENT '内容',
  `openid` varchar(255) NOT NULL COMMENT '发给谁',
  `status` tinyint(1) DEFAULT NULL COMMENT '0未发送 1已发送',
  `type` tinyint(1) DEFAULT NULL COMMENT '类型1顾客下单付款成功的通知2商家确认订单3商家安排派送4商家处理退款6邮寄点',
  `sendtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8;
alter table `ims_weisrc_dish_stores` add `store_type`  tinyint(1)  default 2 COMMENT '1为外卖店2堂食店3为邮寄店';
alter table `ims_weisrc_dish_stores` add `is_dada`  tinyint(1)  default 2 COMMENT ' 1 是 2 否';
alter table `ims_weisrc_dish_service_log` add `ts_times`  int(11)  default 0 COMMENT '提示次數';
alter table `ims_weisrc_dish_service_log` add `ts_type`  int(1)  default 0 COMMENT ' 提示類型 1 待處理 2 帶退款 3 支付';
alter table `ims_weisrc_dish_service_log` add `ts_times_pc`  int(11)  default 0 COMMENT 'pc端提示次数';
alter table `ims_weisrc_dish_setting` add `yy_ts_time`  int(11)  default 3 COMMENT '语音提示次数';
alter TABLE `ims_weisrc_dish_sncode`    ENGINE=InnoDB;
ALTER  TABLE  `ims_weisrc_dish_sncode`  ADD  INDEX index_weid(`weid`);

alter table  `ims_weisrc_dish_order` add `order_ps_type` tinyint(1) default 2 COMMENT '订单配送类型1表示是有补贴的订单，2表示没有补贴的订单';
	update  ims_weisrc_dish_order set order_ps_type=1;
	update  `ims_weisrc_dish_stores` set  is_dada=1;
	alter table `ims_weisrc_dish_coupons` add `goodsids` text  COMMENT '商品id';
	alter table `ims_weisrc_dish_service_log` add `yy_ts_time` int(11) default 3 COMMENT '实时语音提示次数';


