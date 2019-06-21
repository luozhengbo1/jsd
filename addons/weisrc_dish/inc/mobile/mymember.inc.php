<?php
global $_W, $_GPC;

$weid = $this->_weid;
$from_user = $this->_fromuser;
//var_dump($from_user);
$cur_nave = 'my';
$setting = $this->getSetting();
$config = $this->module['config']['weisrc_dish'];
// $id = intval($_GPC['orderid']);
// $status = trim($_GPC['status']);
// $totalprice = floatval($_GPC['totalprice']);
// $remark = trim($_GPC['remark']);
  
// $orderstatus = array('cancel' => -1, 'confirm' => 1, 'finish' => 3, 'pay' => 2, 'updateprice' => 4, 'print' => 5);

$member = pdo_fetch("SELECT * FROM " . tablename($this->table_member) . " WHERE weid=:weid LIMIT 1", array(':weid' => $weid));
if(empty($member)){
    
}else{
    $arr = 1;
    $limit = $member['limit'];
    $limit_discount = $member['limit_discount'];
    //查询判断是否充值过会员卡
    $time = time();//当前时间戳
//    p($time);exit;
    $huiyuan_ka = pdo_fetch("SELECT * FROM".tablename('weisrc_dish_member_ka')." WHERE weid ='{$weid}' and user ='{$from_user}' and endtime>'{$time}'  ");
    if(empty($huiyuan_ka)){
        $arrs = 1;
        //var_dump($arrs);
    }else{
        $arrs = 2;
        $huiyuan_ka['endtime'] = date("Y-m-d H:i:s",$huiyuan_ka['endtime']);
        //var_dump($arrs);
    }
}

//var_dump($_GPC['op']);
$orders = rand();


if($_GPC['op'] == 'pay'){

	//var_dump('expression');
	$order = array(

            'tid' => $orders,

            'user' => $from_user, //用户OPENID

            'fee' => floatval($limit), //金额

            'title' => '码上点餐订单',

        );
        //生成支付参数，返回给小程序端

        $pay_params = $this->pay($order);
        //var_dump($pay_params);exit();
        if (is_error($pay_params)) {
            return $this->result(1, '支付失败，请重试');
        }else{
        	//$ka = pdo_insert('weisrc_dish_member_ka',$order);
        }
}



// if ($orderstatus[$status] == 2) { //支付
//     $update_data['ispay'] = 1;
//     $update_data['paytime'] = TIMESTAMP;
//     pdo_update($this->table_order, $update_data, array('id' => $order['id']));
//     $this->addOrderLog($id, $touser, 2, 1, 2);

// } 

include $this->template($this->cur_tpl . '/mymember');