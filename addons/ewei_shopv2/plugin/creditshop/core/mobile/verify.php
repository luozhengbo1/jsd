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

class Verify_EweiShopV2Page extends PluginMobileLoginPage {

	function main() {
		global $_W, $_GPC;
		$logid = intval($_GPC['logid']);

		$verifycode = trim($_GPC['verifycode']);
		$query = array('logid' => $logid, 'verifycode' => $verifycode);
		$url = mobileUrl('creditshop/verify/detail', $query, true);
		$qrcode = m('qrcode')->createQrcode($url);


		include $this->template();
	}
	function detail(){
		global $_W, $_GPC;
		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		//多商户
		$merch_plugin = p('merch');
		$merch_data = m('common')->getPluginset('merch');
		if ($merch_plugin && $merch_data['is_openmerch']) {
			$is_openmerch = 1;
		} else {
			$is_openmerch = 0;
		}
		$logid = intval($_GPC['logid']);
		$data  = p('creditshop')->allow($logid);
		if(is_error($data)){
			$this->message($data['message'], webUrl('creditshop/log/detail', array('id' => $logid)), 'error');
		}
		extract($data);
		include $this->template('creditshop/verifydetail');
	}
	function qrcode() {
		global $_W, $_GPC;
		$logid = intval($_GPC['logid']);
		$verifycode = $_GPC['verifycode'];
		$query = array('id' => $logid, 'verifycode' => $verifycode);
		$url = mobileUrl('creditshop/verify/detail', $query, true);
		show_json(1, array('url' => m('qrcode')->createQrcode($url)));
	}

	function select() {
		global $_W, $_GPC;
		$orderid = intval($_GPC['id']);
		$verifycode = trim($_GPC['verifycode']);
		if (empty($verifycode) || empty($orderid)) {
			show_json(0);
		}
		$order = pdo_fetch("select id,verifyinfo from " . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid limit 1'
			, array(':id' => $orderid, ':uniacid' => $_W['uniacid']));
		if (empty($order)) {
			show_json(0);
		}
		$verifyinfo = iunserializer($order['verifyinfo']);
		foreach ($verifyinfo as &$v) {
			if ($v['verifycode'] == $verifycode) {
				if (!empty($v['select'])) {
					$v['select'] = 0;
				} else {
					$v['select'] = 1;
				}
			}
		}
		unset($v);
		pdo_update('ewei_shop_order', array('verifyinfo' => iserializer($verifyinfo)), array('id' => $orderid));
		show_json(1);
	}

	function check() {
		global $_W, $_GPC;
		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];

		$orderid = intval($_GPC['id']);
		$order = pdo_fetch("select id,status,isverify,verifytype,verifynum from " . tablename('ewei_shop_groups_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1'
			, array(':id' => $orderid, ':uniacid' => $uniacid, ':openid' => $openid));
		if (empty($order)) {
			show_json(0);
		}
		if (empty($order['isverify'])) {
			show_json(0);
		}
		if ($order['verifytype'] == 0) {
			show_json(0);
		}

		show_json(1);
	}

	function complete() {
		global $_W, $_GPC;
		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		$logid = intval($_GPC['id']);
		$times = intval($_GPC['times']);
		p('creditshop')->verify($logid,$times);
		show_json(1);
	}
	
	function success(){
		global $_W,$_GPC;
		$id =intval($_GPC['logid']);
		$times = intval($_GPC['times']);
		$this->message(array('title'=>'操作完成','message'=>'您可以退出浏览器了'),"javascript:WeixinJSBridge.call(\"closeWindow\");",'success');
	}
	

}
