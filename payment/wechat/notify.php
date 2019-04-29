<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
define('IN_MOBILE', true);
require '../../framework/bootstrap.inc.php';
$input = file_get_contents('php://input');
file_put_contents('/www/wwwroot/jsd.gogcun.com/test.log',$input."\n",8);
//$input="
//<xml><appid><![CDATA[wx0fd57bd3a7fc8709]]></appid>
//<attach><![CDATA[2]]></attach>
//<bank_type><![CDATA[ICBC_DEBIT]]></bank_type>
//<cash_fee><![CDATA[1]]></cash_fee>
//<fee_type><![CDATA[CNY]]></fee_type>
//<is_subscribe><![CDATA[Y]]></is_subscribe>
//<mch_id><![CDATA[1532311851]]></mch_id>
//<nonce_str><![CDATA[qKtlgfkJ]]></nonce_str>
//<openid><![CDATA[oW-VD01zhPdr764rS0AO8yFAAX9E]]></openid>
//<out_trade_no><![CDATA[2019042919351400001362636622]]></out_trade_no>
//<result_code><![CDATA[SUCCESS]]></result_code>
//<return_code><![CDATA[SUCCESS]]></return_code>
//<sign><![CDATA[FA97748EF2011ADAF3777625256887AF]]></sign>
//<time_end><![CDATA[20190429193532]]></time_end>
//<total_fee>1</total_fee>
//<trade_type><![CDATA[JSAPI]]></trade_type>
//<transaction_id><![CDATA[4200000332201904292260245055]]></transaction_id>
//</xml>
//";
$isxml = true;
if (!empty($input) && empty($_GET['out_trade_no'])) {
	$obj = isimplexml_load_string($input, 'SimpleXMLElement', LIBXML_NOCDATA);
	$data = json_decode(json_encode($obj), true);
	if (empty($data)) {
		$result = array(
			'return_code' => 'FAIL',
			'return_msg' => ''
		);
		echo array2xml($result);
		exit;
	}
	if ($data['result_code'] != 'SUCCESS' || $data['return_code'] != 'SUCCESS') {
		$result = array(
			'return_code' => 'FAIL',
			'return_msg' => empty($data['return_msg']) ? $data['err_code_des'] : $data['return_msg']
		);
		echo array2xml($result);
		exit;
	}
	$get = $data;
} else {
	$isxml = false;
	$get = $_GET;
}
load()->web('common');
load()->classs('coupon');
    $_W['uniacid'] = $_W['weid'] = intval($get['attach']);
    $_W['uniaccount'] = $_W['account'] = uni_fetch($_W['uniacid']);
    $_W['acid'] = $_W['uniaccount']['acid'];
    $setting = uni_setting($_W['uniacid'], array('payment'));

//訂單語音提示
$ordertsData = pdo_fetch('select id,ordersn,storeid,from_user from'.tablename("weisrc_dish_order")." where transid=:transid limit 1",array(':transid'=>$data['transaction_id']));

if($ordertsData){
    $yytsres =  pdo_fetch('select id ,orderid  from '.tablename('weisrc_dish_service_log').' where orderid=:orderid and ts_type=1 limit 1',array(':orderid'=>$ordertsData['id']));
    if(!$yytsres){
        pdo_insert("weisrc_dish_service_log",
            array(
                'orderid' => $ordertsData['id'],
                'storeid' =>$ordertsData['storeid'] ,
                'weid' => $_W['weid'] ,
                'from_user' => $data['openid'],
                'content' => "您有待確認的订单，请尽快处理",
                'dateline' => TIMESTAMP,
                'status' => 0,
                'ts_type'=>1,
            )
        );
    }
    $res_order_goods = pdo_get('weisrc_dish_order_goods',['orderid'=>$ordertsData['id']],'*');
    if(!$res_order_goods){
        $yytsres1 =  pdo_fetch('select id ,orderid  from '.tablename('weisrc_dish_service_log').' where orderid=:orderid and ts_type=3 limit 1',array(':orderid'=>$ordertsData['id']));
        if(!$yytsres1){
            pdo_insert("weisrc_dish_service_log",
                array(
                    'orderid' => $ordertsData['id'],
                    'storeid' =>$ordertsData['storeid'] ,
                    'weid' => $_W['weid'] ,
                    'from_user' => $data['openid'],
                    'content' => "您有一个付款单，请尽快处理",
                    'dateline' => TIMESTAMP,
                    'status' => 0,
                    'ts_type'=>3,
                )
            );
        }
    }
}
if ($get['trade_type'] == 'NATIVE') {
	$setting = setting_load('store_pay');
	$setting['payment']['wechat'] = $setting['store_pay']['wechat'];
}

if(is_array($setting['payment'])) {
	$wechat = $setting['payment']['wechat'];
	WeUtility::logging('pay', var_export($get, true));
	if(!empty($wechat)) {
        ksort($get);
		$string1 = '';
		foreach($get as $k => $v) {
			if($v != '' && $k != 'sign') {
				$string1 .= "{$k}={$v}&";
			}
		}

		if (intval($wechat['switch']) == 3) {
			$facilitator_setting = uni_setting($wechat['service'], array('payment'));
			$wechat['signkey'] = $facilitator_setting['payment']['wechat_facilitator']['signkey'];
		} else {
			$wechat['signkey'] = ($wechat['version'] == 1) ? $wechat['key'] : $wechat['signkey'];
		}
		$sign = strtoupper(md5($string1 . "key={$wechat['signkey']}"));
		if($sign == $get['sign']) {
            $sql = 'SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniontid`=:uniontid';
			$params = array();
			$params[':uniontid'] = $get['out_trade_no'];
			$log = pdo_fetch($sql, $params);
			if (intval($wechat['switch']) == PAYMENT_WECHAT_TYPE_SERVICE) {
				$get['openid'] = $log['openid'];
			}


            $log['status']=0;
			if(!empty($log) && $log['status'] == '0' && (($get['total_fee'] / 100) == $log['card_fee'])) {
//                echo $sign;
                $log['tag'] = iunserializer($log['tag']);
				$log['tag']['transaction_id'] = $get['transaction_id'];
				$log['uid'] = $log['tag']['uid'];
				$record = array();
				//表示
                $record['status'] = '1';
				$record['tag'] = iserializer($log['tag']);
				pdo_update('core_paylog', $record, array('plid' => $log['plid']));
				$mix_pay_credit_log = pdo_get('core_paylog',
                    array(
                        'module' => $log['module'],
                        'tid' => $log['tid'],
                        'uniacid' => $log['uniacid'],
                        'type' => 'credit')
                );
                if (!empty($mix_pay_credit_log)) {
                    pdo_update('core_paylog', array('status' => 1), array('plid' => $mix_pay_credit_log['plid']));
					$log['fee'] = $mix_pay_credit_log['fee'] + $log['fee'];
					$log['card_fee'] = $mix_pay_credit_log['fee'] + $log['card_fee'];
					$setting = uni_setting($_W['uniacid'], array('creditbehaviors'));
					$credtis = mc_credit_fetch($log['uid']);
					mc_credit_update($log['uid'], $setting['creditbehaviors']['currency'], -$mix_pay_credit_log['fee'], array($log['uid'], '消费' . $setting['creditbehaviors']['currency'] . ':' . $fee));
				}
				if ($log['is_usecard'] == 1 && !empty($log['encrypt_code'])) {
                    $coupon_info = pdo_get('coupon', array('id' => $log['card_id']), array('id'));
					$coupon_record = pdo_get('coupon_record', array('code' => $log['encrypt_code'], 'status' => '1'));
					load()->model('activity');
				 	$status = activity_coupon_use($coupon_info['id'], $coupon_record['id'], $log['module']);
				}

				if ($log['type'] == 'wxapp') {
                    $site = WeUtility::createModuleWxapp($log['module']);
				} else {

                    $site = WeUtility::createModuleSite($log['module']);
				}

                if(!is_error($site)) {
					$method = 'payResult';
					if (method_exists($site, $method)) {
                        $ret = array();
						$ret['weid'] = $log['weid'];
						$ret['uniacid'] = $log['uniacid'];
						$ret['acid'] = $log['acid'];
						$ret['result'] = 'success';
						$ret['type'] = $log['type'];
						$ret['from'] = 'notify';
						$ret['tid'] = $log['tid'];
						$ret['uniontid'] = $log['uniontid'];
						$ret['transaction_id'] = $log['transaction_id'];
						$ret['trade_type'] = $get['trade_type'];
						$ret['follow'] = $get['is_subscribe'] == 'Y' ? 1 : 0;
						$ret['user'] = empty($get['openid']) ? $log['openid'] : $get['openid'];
						$ret['fee'] = $log['fee'];
						$ret['tag'] = $log['tag'];
						$ret['is_usecard'] = $log['is_usecard'];
						$ret['card_type'] = $log['card_type'];
						$ret['card_fee'] = $log['card_fee'];
						$ret['card_id'] = $log['card_id'];
						if(!empty($get['time_end'])) {
                            $ret['paytime'] = strtotime($get['time_end']);
						}
                        if($isxml) {
						    //自动确认支付的订单进行dodada
                            $site->$method($ret);
							$result = array(
								'return_code' => 'SUCCESS',
								'return_msg' => 'OK'
							);
							echo array2xml($result);
							exit;
						} else {
							exit('success');
						}
					}
				}
			}
		}
	}
}
if($isxml) {
	$result = array(
		'return_code' => 'FAIL',
		'return_msg' => ''
	);
	echo array2xml($result);
	exit;
} else {
	exit('fail');
}
