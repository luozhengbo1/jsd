<?php

/*
 * 人人商城
 *
 * 青岛易联互动网络科技有限公司
 * http://www.we7shop.cn
 * TEL: 4000097827/18661772381/15865546761
 */
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Index_EweiShopV2Page extends WebPage {

	function main() {
 
		if(cv('statistics.sale.main')){
			header('location: '.webUrl('statistics/sale'));
		} 
		else if(cv('statistics.sale_analysis.main')){
			header('location: '.webUrl('statistics/sale_analysis'));
		}
		else if(cv('statistics.order.main')){
			header('location: '.webUrl('statistics/order'));
		}
		else if(cv('statistics.sale_analysis.main')){
			header('location: '.webUrl('statistics/sale_analysis'));
		}
		else if(cv('statistics.goods.main')){
			header('location: '.webUrl('statistics/goods'));
		}
		else if(cv('statistics.goods_rank.main')){
			header('location: '.webUrl('statistics/goods_rank'));
		}
		else if(cv('statistics.goods_trans.main')){
			header('location: '.webUrl('statistics/goods_trans'));
		}
		else if(cv('statistics.member_cost.main')){
			header('location: '.webUrl('statistics/member_cost'));
		}
		else if(cv('statistics.member_increase.main')){
			header('location: '.webUrl('statistics/member_increase'));
		}else{
			header('location: '.webUrl());
		}
	}
}