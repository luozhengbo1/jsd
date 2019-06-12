<?php
global $_W, $_GPC;
$weid = $this->_weid;
$from_user = $this->_fromuser;
$couponid = intval($_GPC['couponid']);
$storeid = intval($_GPC['storeid']);
$rtype = intval($_GPC['rtype']);
$setting = $this->getSetting();
$zhekou = $_GPC['zhekou'];
$psnum = $_GPC['psnum'];
//var_dump($from_user);exit();
//p($_GPC['ordertype']);die;
$mode = intval($_GPC['ordertype']) == 0 ? 1 : intval($_GPC['ordertype']);
$is_auto_address = intval($setting['is_auto_address']);
if ($mode == 3) {
    $is_auto_address = 1;
}

$useraddress = pdo_fetch("SELECT * FROM " . tablename($this->table_useraddress) . " WHERE weid=:weid AND from_user=:from_user AND isdefault=1 LIMIT 1", array(':weid' => $weid, ':from_user' => $from_user));

if ($is_auto_address == 0) {
    $lat = trim($useraddress['lat']);
    $lng = trim($useraddress['lng']);
} else {
    $lat = trim($_GPC['lat']);
    $lng = trim($_GPC['lng']);
}
//百度转高德计算距离
//$postionlatlng = $this->baiduMapTogaodeMap($lng,$lat);
//$lat=$postionlatlng['lat'];
//$lng=$postionlatlng['lng'];
$is_handle_goods = 1; //是否处理商品
if ($mode == 5 || $mode == 6) {
    $is_handle_goods = 0;
}
if (empty($from_user)) {
    $this->showTip('请重新发送关键字进入系统!');
}
$isvip = $this->get_sys_card($from_user);

if ($mode != 6) {
    if (empty($storeid)) {
        $this->showTip('请先选择门店!');
    }
    $store = $this->getStoreById($storeid);
}

//外卖
if ($mode == 2) {
    if (empty($lat) || empty($lng)) {
        $this->showTip('请重新选择配送地址!');
    }
    //距离
    $delivery_radius = floatval($store['delivery_radius']);
	//sjg 2018-09-04
	$distance = $this->getDistanceByGaodeForRiding($lat, $lng, $store['lat'], $store['lng']);
	if($distance == 0)
	{
		$distance = $this->getDistance($lat, $lng, $store['lat'], $store['lng']);
	}
    $distance = floatval($distance);
    if ($store['not_in_delivery_radius'] == 0 && $delivery_radius > 0) { //只能在距离范围内
        if ($distance > $delivery_radius) {
            $this->showTip('超出配送范围，不允许下单。',1);
        }
    }
}
if ($mode != 6) {
    //购物车为空
    $cart = pdo_fetchall("SELECT * FROM " . tablename($this->table_cart) . " WHERE weid = :weid AND from_user = :from_user AND storeid=:storeid", array(':weid' => $weid, ':from_user' => $from_user, ':storeid' => $storeid));
}
if ($is_handle_goods == 1) {
    if ($rtype != 1) {
        if (empty($cart)) {
            $this->showTip('请先添加商品!');
        }
        $goodsid = implode(',', array_column($cart, 'goodsid'));
        $goods = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . " WHERE id IN ($goodsid)", array(':weid' => $weid, ':from_user' => $from_user, ':storeid' => $storeid));
        if (empty($goods)) {
            $this->showTip('商品已删除!');
        }
        foreach ($goods as $k => $v){
            if ($v["deleted"] == 1){
                $this->showTip($v["title"].'已删除!');
            }
        }
    }
}
if ($is_auto_address == 0) { //多收餐地址
    $guest_name = $useraddress['realname']; //用户名
    $tel = $useraddress['mobile']; //电话
    $address = $useraddress['address'] . ' ' . $useraddress['doorplate']; //地址
} else {
    $guest_name = trim($_GPC['username']); //用户名
    $tel = trim($_GPC['tel']); //电话
    $address = trim($_GPC['address']);
}

$sex = trim($_GPC['sex']); //性别
$meal_time = trim($_GPC['meal_time']); //订餐时间
$counts = intval($_GPC['counts']); //预订人数
$seat_type = intval($_GPC['seat_type']); //就餐形式
$carports = intval($_GPC['carports']); //预订车位
$remark = trim($_GPC['remark']); //备注
$dispatcharea = trim($_GPC['dispatcharea']); //地址
$tables = intval($_GPC['tables']); //桌号
$tablezonesid = intval($_GPC['tablezonesid']); //桌台
$append = intval($_GPC['append']); //是否加单

if ($mode != 4 && $mode != 1 && $is_handle_goods == 1) {//非堂点非收银
    if (empty($guest_name)) {
        $this->showTip('请选择您的联系方式!');
    }
    if (empty($tel)) {
        $this->showTip('请选择您的联系方式.');
    }
}

//堂点
if ($mode == 1) {
    if ($append == 0 && $counts <= 0) {
        $this->showTip('请输入用餐人数!');
    }
    if ($tables == 0) {
        $this->showTip('请先扫描桌台!');
    }

    if ($store['is_locktables'] == 1) {
        $haveorder = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE weid=:weid AND storeid=:storeid AND tables=:tables AND
status<>-1 AND status<>3 LIMIT 1", array(":weid" => $weid, ":storeid" => $storeid, ":tables" => $tables));
        if ($haveorder) {
            if ($haveorder['from_user'] != $from_user) {
                $this->showTip('餐台已经有人在使用中！');
            }
        }
    }
} else if ($mode == 2) {//外卖
    if (empty($address)) {
        $this->showTip('请选择您的联系方式!！');
    }
} else if ($mode == 3) {
    if ($tables == 0) {
        $this->showTip('请先选择桌台!');
    }
}
$user = $this->getFansByOpenid($from_user);
$fansdata = array('weid' => $weid,
    'from_user' => $from_user,
    'username' => $guest_name,
    'address' => $address,
    'mobile' => $tel
);
if (empty($guest_name)) {
    unset($fansdata['username']);
}
if (empty($tel)) {
    unset($fansdata['mobile']);
}
if (empty($address)) {
    unset($fansdata['address']);
}
if ($mode == 2) { //外卖
    $fansdata['lat'] = $lat;
    $fansdata['lng'] = $lng;
}
if (empty($user)) {
    pdo_insert($this->table_fans, $fansdata);
} else {
    pdo_update($this->table_fans, $fansdata, array('id' => $user['id']));
}
//2.购物车 //a.添加订单、订单产品
$totalnum = 0;
$totalprice = 0;
$goodsprice = 0;
$dispatchprice = 0;
$freeprice = 0;
$packvalue = 0;
$teavalue = 0;
$service_money = 0;

if ($rtype != 1) {
    foreach ($cart as $value) {
        $total = intval($value['total']);
        $totalnum = $totalnum + intval($value['total']);
        $goodsprice = $goodsprice + ($total * floatval($value['price']));
        if ($mode == 2) { //打包费
            $packvalue = $packvalue + ($total * floatval($value['packvalue']));
        }
    }
}
if ($mode == 2) { //外卖

    $dispatchprice = $store['dispatchprice'];
    if ($is_auto_address == 0 && $useraddress) { //多收餐地址 算距离
        //sjg 2018-09-04
		$distance = $this->getDistanceByGaodeForRiding($useraddress['lat'], $useraddress['lng'], $store['lat'], $store['lng']);
		if($distance == 0)
		{
			$distance = $this->getDistance($useraddress['lat'], $useraddress['lng'], $store['lat'], $store['lng']);
		}
        $distance = floatval($distance);
    }
    $dispatchareas_pt =  pdo_fetch("SELECT * FROM " . tablename('weisrc_dish_distance_pt') . " WHERE weid=:weid and begindistance<'{$distance}' and enddistance>'{$distance}' ORDER BY id ASC", array(':weid' => $weid));
    if(empty($dispatchareas_pt)){
        $dispatchareas_pt = pdo_fetch("SELECT * FROM".tablename('weisrc_dish_distance_pt'). "where weid =:weid and enddistance < '{$distance}' order by enddistance desc ",array(':weid'=>$weid));
    }
   
    $psf = $dispatchareas_pt['dispatchprice'];


    if ($store['is_delivery_distance'] == 1 && $store['store_type']==1 ) { //按距离收费 店铺为外卖店
		//sjg 2018-09-04
		/*
		$distance = $this->getDistanceByGaodeForRiding($useraddress['lat'], $useraddress['lng'], $store['lat'], $store['lng']);
		if($distance == 0)
		{
			$distance = $this->getDistance($useraddress['lat'], $useraddress['lng'], $store['lat'], $store['lng']);
		}
		*/
        $distanceprice = $this->getdistanceprice($storeid, $distance);
        $pi_yf = floatval($distanceprice['dispatchprice']);
        $dispatchprice = floatval($distanceprice['dispatchprice'])-$psf;
//        p(floatval($distanceprice['dispatchprice']));
//        p($distance);
//        p($psf);
//        p($dispatchprice);die;
        $order_ps_type=1;
    }else{
//        $dispatchprice=0;
//        $pi_yf=0;
        //平台运费
        //用户配送费 $dispatchprice
        $psf=0;
        $order_ps_type=2;
    }
    if ($store['is_delivery_time'] == 1) { //特殊时段加价
        $tprice = $this->getPriceByTime($storeid);
        $dispatchprice = $dispatchprice + $tprice;
    }
    $freeprice = floatval($store['freeprice']);
    if ($freeprice > 0.00) {
        if ($goodsprice >= $freeprice) {
            $dispatchprice = 0;
        }
    }
}

if ($mode == 1) { //店内
    if ($store['is_tea_money'] == 1) {
        $teavalue = $counts * floatval($store['tea_money']);
    }
}

$isnewuser = $this->isNewUser($storeid);
$dlimitprice = 0;
$newlimitprice = '';
$oldlimitprice = '';
$newlimitpricevalue = '';
$oldlimitpricevalue = '';
if ($isnewuser == 1) { //新用户
    if ($store['is_newlimitprice'] == 1) { //新顾客满减
        $coupon_obj1 = $this->getNewLimitPrice($storeid, $goodsprice, $mode);
        if ($coupon_obj1) {
            $dlimitprice = floatval($coupon_obj1['dmoney']);
            $newlimitprice = $coupon_obj1['title'];
            $newlimitpricevalue = $dlimitprice;
        }
    }
} else { //老用户
    if ($store['is_oldlimitprice'] == 1) { //老顾客满减
        $coupon_obj2 = $this->getOldLimitPrice($storeid, $goodsprice, $mode);
        if ($coupon_obj2) {
            $dlimitprice = floatval($coupon_obj2['dmoney']);
            $oldlimitprice = $coupon_obj2['title'];
            $oldlimitpricevalue = $dlimitprice;
        }
    }
}
if($psnum == 2){ // 邮寄
    $psf = 0;
    $dispatchprice = 0;
}
$totalprice = $goodsprice + $dispatchprice + $packvalue + $teavalue - $dlimitprice;

if ($mode == 1) { //店内
    $table = pdo_fetch("SELECT * FROM " . tablename($this->table_tables) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $tables));
    $tablezonesid = $table['tablezonesid'];
    $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " WHERE id = :id", array(':id' => $tablezonesid));
    $service_rate = floatval($tablezones['service_rate']);
    if ($service_rate > 0) {
        $service_money = $totalprice * $service_rate / 100;
    }
    $totalprice = $totalprice + $service_money;
}

if ($mode == 3) { //预定
    if ($rtype == 1) {
        $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " WHERE id = :id", array(':id' => $tablezonesid));
        if (floatval($tablezones['reservation_price']) <= 0) {
            $totalprice = 0.01;
        } else {
            $totalprice = floatval($tablezones['reservation_price']);
        }
    }
}

if ($mode == 2) { //外卖
    $sendingprice = floatval($store['sendingprice']);
    if ($sendingprice > 0.00) {
        if ($goodsprice < $store['sendingprice']) {
            $this->showTip('您的购买金额达不到起送价格!');
        }
    }
}
$time = time();
$coupon = pdo_fetch("SELECT a.id as ids,a.couponid,a.weid,a.from_user,a.status,b.* FROM".tablename('weisrc_dish_sncode')."as a left join ".tablename('weisrc_dish_coupons')." as b on a.couponid = b.id where a.id = {$couponid} and a.weid = :weid and a.from_user =:from_user and b.starttime <{$time} and b.endtime >{$time} and a.status = 0",array(':weid'=>$weid,':from_user'=>$from_user));
$dmoney = $coupon['dmoney'];//优惠金额
// var_dump($dmoney);exit();
if(!empty($dmoney)){
    $totalprice = $totalprice - $dmoney;
}

if(!empty($zhekou)){
    $totalprice = $totalprice - $zhekou;
}

//var_dump($totalprice);exit();

//查询会员积分
$jifen = pdo_fetch("SELECT * FROM".tablename('weisrc_dish_money')."where weid =:weid order by id desc LIMIT 1",array(':weid'=>$weid));
if(!empty($jifen)){
    //查询会员积分
    $user = mc_fetch($from_user);//不能实时获取最新数据
    //根据id查询最新数据
    $member_jifen = pdo_fetch("SELECT * FROM".tablename('mc_members')." where uid =:uid",array(':uid'=>$user['uid']));
    $credit1 = $member_jifen['credit1'];//积分
    if($credit1>=$jifen['money_limit']){
        $jifen_sl = $credit1/$jifen['money_limit'];
        $jifen_dk = round($jifen_sl*$jifen['minus'],2);
        $totalprice = $totalprice-$jifen_dk;
        //var_dump($totalprice);exit();
    }
}
// $coupon = pdo_fetch("SELECT a.*,b.sncode FROM " . tablename($this->table_coupon) . "
//         a INNER JOIN " . tablename($this->table_sncode) . " b ON a.id= b.couponid
//  WHERE a.weid = :weid AND b.from_user=:from_user AND b.status=0 AND :time<a.endtime AND b.id=:couponid ORDER BY b.id
//  DESC LIMIT 1", array(':weid' => $weid, ':from_user' => $from_user, ':time' => TIMESTAMP, ':couponid' => $couponid));
// var_dump(expression)
$discount_money = 0;
// if ($couponid <> 0 && empty($coupon)) {
//     $this->showTip('优惠券不存在!');
// } else {
    
//     
    
// }
if (!$this->getmodules()) {
    $totalprice = $totalprice;
    $goodsprice = $goodsprice;
}

if ($mode == 5 || $mode == 6) { //收银
    $totalprice = floatval($_GPC['total']);
}
if ($mode == 6) {
    //充值赠送
    $recharge = pdo_fetch("SELECT * FROM " . tablename($this->table_recharge) . " WHERE weid = :weid AND :nowtime<endtime AND :nowtime>starttime AND :recharge_value>=recharge_value ORDER BY `recharge_value` DESC,`id` DESC LIMIT 1", array(':weid' => $weid, ':nowtime' => TIMESTAMP, ':recharge_value' => $totalprice));
    $rechargeid = intval($recharge['id']);
}

//加菜

if ($append == 2) {
    $orderid = intval($_GPC['order_id']);
    $dishInfo = pdo_fetchall("SELECT goodsid,price,total FROM " . tablename($this->table_order_goods) . " WHERE weid=:weid AND storeid=:storeid AND orderid=:orderid", array(":weid" => $weid, ":storeid" => $storeid, ":orderid" => $orderid));
    foreach ($dishInfo as $v) {
        $dishid[] = $v['goodsid'];
    }
    if ($rtype != 1) {
        foreach ($cart as $k => $v) {
            if (empty($v['total'])) {
                continue;
            }

            if (in_array($v['goodsid'], $dishid)) {
                $dishCon = array(":weid" => $weid, ":storeid" => $storeid, ":orderid" => $orderid, ":goodsid" => $v['goodsid']);
                $sql = "UPDATE " . tablename($this->table_order_goods) . " SET total=total+{$v['total']},dateline=" . time() . " WHERE weid=:weid AND storeid=:storeid AND orderid=:orderid AND goodsid=:goodsid";
                pdo_query($sql, $dishCon);
            } else {
                $parm = array("weid" => $weid, "storeid" => $storeid, "orderid" => $orderid, "goodsid" => $v['goodsid'], "price" => $v['price'], "total" => $v['total'], 'dateline' => time(), 'optionid' => $row['optionid'], 'optionname' => $row['optionname']);
                pdo_insert($this->table_order_goods, $parm);
            }
            $goodsName = pdo_fetch("SELECT title FROM " . tablename($this->table_goods) . " WHERE id=:id", array(":id" => $v['goodsid']));
            $appendMes .= $goodsName['title'] . "*" . $v['total'] . ",";
            pdo_query("UPDATE " . tablename($this->table_goods) . " SET today_counts=today_counts+:counts,sales=sales+:counts,lasttime=:time WHERE id=:id", array(':id' => $v['goodsid'], ':counts' => $v['total'], ':time' => TIMESTAMP));
        }
    }

    $orderParm = array(':totalnum' => $totalnum, ':totalprice' => $totalprice, ':goodsprice' => $goodsprice, ':service_money' => $service_money, ':tea_money' => $teavalue, ':id' => $orderid);
    pdo_query("UPDATE " . tablename($this->table_order) . " SET totalnum=totalnum+:totalnum ,totalprice=totalprice+:totalprice,goodsprice=goodsprice+:goodsprice,service_money=service_money+:service_money,tea_money=tea_money+:tea_money,append_dish=1 where id=:id ", $orderParm);
    if ($couponid > 0) {
        pdo_update($this->table_sncode, array('status' => 1), array('id' => $couponid));
    }
    $this->msg_status_success = 2;
} else {
    $data = array(
        'weid' => $weid,
        'from_user' => $from_user,
        'storeid' => $storeid,
        'couponid' => $couponid,
        'discount_money' => $discount_money,
        'ordersn' => date('Ymd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99)),
        'totalnum' => $totalnum, //产品数量
        'totalprice' => $totalprice, //总价
        'goodsprice' => $goodsprice,
        'tea_money' => $teavalue,
        'service_money' => $service_money,
        'dispatchprice' => $dispatchprice,
        'packvalue' => $packvalue,
        'paytype' => 0, //付款类型
        'newlimitprice' => $newlimitprice,
        'oldlimitprice' => $oldlimitprice,
        'newlimitpricevalue' => $newlimitpricevalue,
        'oldlimitpricevalue' => $oldlimitpricevalue,
        'one_order_getprice' => floatval($setting['one_order_getprice']),
        'username' => $guest_name,
        'tel' => $tel,
        'meal_time' => $meal_time,
        'counts' => $counts,
        'seat_type' => $seat_type,
        'tables' => $tables,
        'tablezonesid' => $tablezonesid,
        'carports' => $carports,
        'dining_mode' => $mode, //订单类型
        'remark' => $remark, //备注
        'address' => $dispatcharea . $address, //地址
        'status' => 0, //状态
        'rechargeid' => $rechargeid,
        'lat' => $lat,
        'lng' => $lng,
        'isvip' => $isvip,
        'is_append' => $append,
        'dateline' => TIMESTAMP,
        'sj_yf'=>$pi_yf,
        'pt_yf'=>$psf,
        'dprice'=>$dmoney,
        'zhekou'=>$zhekou,
        'jifen_dk'=>$jifen_dk,
        'ps_jvli'=>$distance,
    );

    if ($mode == 1) { //店内
        unset($data['username']);
        unset($data['tel']);
        unset($data['address']);
    }

    if ($mode == 4) { //快餐
        $quicknum = $this->getQuickNum($storeid);
        $data['quicknum'] = $quicknum;
    }
    $data['order_ps_type'] =$order_ps_type;
    //保存订单
//    p($data);die;
    pdo_insert($this->table_order, $data);
    $orderid = pdo_insertid();
    //$this->doEmail($title);
    //$this->doDada($weid,$orderid,$storeid);
    //修改优惠券状态
    //var_dump($couponid);
    if($couponid != 0){
        pdo_update("weisrc_dish_sncode",array('status'=>1),array('id'=>$couponid));
    }

    //清空积分
    //查询会员积分
    $user = mc_fetch($from_user);//不能实时获取最新数据
    //根据id查询最新数据
    $member_jifen = pdo_fetch("SELECT * FROM".tablename('mc_members')." where uid =:uid",array(':uid'=>$user['uid']));
    pdo_update("mc_members",array('credit1'=>0),array('uid'=>$user['uid']));//下单增加积分

    //查询判断是否设置积分
    $jifen = pdo_fetch("SELECT * FROM".tablename('weisrc_dish_money')."where weid =:weid order by id desc LIMIT 1",array(':weid'=>$weid));
    if(!empty($jifen)){
        //判断是否满足条件
        if($totalprice>$jifen['limit']){
            //查询会员积分
            $user = mc_fetch($from_user);//不能实时获取最新数据
            //根据id查询最新数据
            $member_jifen = pdo_fetch("SELECT * FROM".tablename('mc_members')." where uid =:uid",array(':uid'=>$user['uid']));
            $credit1 = $member_jifen['credit1'];//积分
            pdo_update("mc_members",array('credit1'=>$credit1+$jifen['limit_jifen']),array('uid'=>$user['uid']));//下单增加积分
        }
    }

    
    
    if ($orderid > 0) {
        if ($couponid > 0) {
            pdo_update($this->table_sncode, array('status' => 1,'usetime'=>time()), array('id' => $couponid));
        }
    }
    //保存新订单商品
    if ($rtype != 1) {
        if ($is_handle_goods == 1) {
            foreach ($cart as $row) {
                if (empty($row) || empty($row['total']) || $rtype == 1) {
                    continue;
                }
                pdo_query("UPDATE " . tablename($this->table_goods) . " SET today_counts=today_counts+:counts,sales=sales+:counts,lasttime=:time WHERE id=:id", array(':id' => $row['goodsid'], ':counts' => $row['total'], ':time' => TIMESTAMP));
                pdo_insert($this->table_order_goods, array(
                    'weid' => $_W['uniacid'],
                    'storeid' => $row['storeid'],
                    'goodsid' => $row['goodsid'],
                    'optionid' => $row['optionid'],
                    'optionname' => $row['optionname'],
                    'orderid' => $orderid,
                    'price' => $row['price'],
                    'total' => $row['total'],
                    'dateline' => TIMESTAMP,
                ));
            }
        }
    }
}

if ($is_handle_goods == 1) {
    if ($rtype != 1) {
        pdo_delete($this->table_cart, array('weid' => $weid, 'from_user' => $from_user, 'storeid' => $storeid));
    }
}

$touser = empty($user['nickname']) ? $user['from_user'] : $user['nickname'];
if ($this->msg_status_success == 2) {
    $touser .= '&nbsp;加菜：' . $appendMes;
}

$this->addOrderLog($orderid, $touser, 1, 1, 1);
//pdo_insert($this->table_service_log,
//    array(
//        'orderid' => $orderid,
//        'storeid' => $storeid,
//        'weid' => $weid,
//        'from_user' => $from_user,
//        'content' => '您有未处理的订单，请尽快处理123123',
//        'dateline' => TIMESTAMP,
//        'status' => 0)
//);
//if ($mode == 5) {
//    $params['tid'] = $orderid;
//    $params['user'] = $_W['fans']['from_user'];
//    $params['fee'] = $data['totalprice'];
//    $params['ordersn'] = $data['ordersn'];
//    $params['virtual'] = true;
//    $params['module'] = 'weisrc_dish';
//    $params['title'] = '餐饮' . $data['ordersn'];
//    $log = pdo_get('core_paylog', array('uniacid' => $_W['uniacid'], 'module' => $params['module'], 'tid' => $params['tid']));
//    if (empty($log)) {
//        $log = array(
//            'uniacid' => $_W['uniacid'],
//            'acid' => $_W['acid'],
//            'openid' => $_W['member']['uid'],
//            'module' => $this->module['name'], //模块名称，请保证$this可用
//            'tid' => $params['tid'],
//            'fee' => $params['fee'],
//            'card_fee' => $params['fee'],
//            'status' => '0',
//            'is_usecard' => '0',
//        );
//        pdo_insert('core_paylog', $log);
//    }
//
//    $result['orderid'] = $orderid;
//    $result['code'] = $this->msg_status_success;
//    $result['params'] = base64_encode(json_encode($params));
//    $result['msg'] = '操作成功';
//    message($result, '', 'ajax');
//
//} else {
    $result['orderid'] = $orderid;
    $result['code'] = $this->msg_status_success;
    $result['msg'] = '操作成功';
    message($result, '', 'ajax');
//}



