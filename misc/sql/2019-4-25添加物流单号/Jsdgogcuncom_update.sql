 alter table `ims_weisrc_dish_order` add   `logistics_number` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '物流号';
 CREATE TABLE `ims_weisrc_dish_sendmsg` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL COMMENT '内容',
  `openid` varchar(255) NOT NULL COMMENT '发给谁',
  `status` tinyint(1) DEFAULT NULL COMMENT '0未发送 1已发送',
  `type` tinyint(1) DEFAULT NULL COMMENT '类型1顾客下单付款成功的通知2商家确认订单3商家安排派送4商家处理退款6邮寄点',
  `sendtime` datetime DEFAULT NULL,
  `createtime` datetime default null
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


	alter table `ims_weisrc_dish_order_goods` add `real_price` decimal(10,2) default 0 COMMENT '实际分摊金额';
	alter table `ims_weisrc_dish_order_goods` add `single_real_price` decimal(10,2) default 0 COMMENT '单个实际分摊金额';
	alter table `ims_weisrc_dish_order_goods` add `is_return` tinyint(1) default 0 COMMENT '0 未退，1已退';
	alter table `ims_weisrc_dish_goods` add `basic_counts` int(11) default 0 COMMENT '基础库存';
	alter table `ims_weisrc_dish_goods` add `add_counts` int(11) default 0 COMMENT '新增库存';

	alter table `ims_weisrc_dish_order` add `origin_totalprice` decimal(10,2) default 0 COMMENT '订单原价';
	alter table `ims_weisrc_dish_sendmsg` add `createtime` datetime  COMMENT '创建时间';
	ALTER TABLE `ims_weisrc_dish_coupons` ADD `storeids` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '店铺id';
	-- 将历史数据中没有原价的进行更新
update ims_weisrc_dish_order set origin_totalprice=`totalprice` where id in (select * from (  select  id from ims_weisrc_dish_order where  paytime < 1560177000 ) as a)




ALTER TABLE `ims_weisrc_dish_cart` ADD `status` tinyint(1) default 1 COMMENT '1 商品在，0商品不在,2商品已下架';
-- 删除from_user 为空的数据
delete from ims_weisrc_dish_fans where id in (select * from ( select id  from ims_weisrc_dish_fans where from_user="") as  a )
alter table `ims_weisrc_dish_fans` modify from_user varchar(80)  not null ;

--增加积分记录增加还是减少
alter table `ims_mc_credits_record` add  `type` tinyint(1) default 1  COMMENT '1加0减少' ;


---0702后  已更
CREATE TABLE `ims_weisrc_dish_dada_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '达达订单表监控',
  `order_info` text COLLATE utf8mb4_unicode_ci COMMENT '订单数据',
  `createtime` datetime DEFAULT NULL COMMENT '创建时间',
  `updatetime` datetime DEFAULT NULL COMMENT '更新时间',
  `status` tinyint(5) DEFAULT '0' COMMENT '订单状态012--完成 ',
  `dada_order_info` text COLLATE utf8mb4_unicode_ci COMMENT '达达订单数据',
  `res` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '达达返回的数据',
  `ordersn` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '第三方订单',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

alter table `ims_weisrc_dish_dada_order` add  `order_status` tinyint(5)   COMMENT '达达传送的订单状态' ;

alter table `ims_weisrc_dish_fans` add  `stop_storeids` varchar(255)  COMMENT '0表示所有。' ;
alter table `ims_weisrc_dish_fans` add  `store_status` tinyint(1) default 1 COMMENT '1开启0禁止。' ;


