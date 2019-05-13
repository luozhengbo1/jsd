<?php
//确认订单页面
global $_W, $_GPC;
$weid = $this->_weid;
$from_user = $this->_fromuser;
$title = '我的菜单';
$do = 'menu';
$storeid = intval($_GPC['storeid']);
$orderid = intval($_GPC['orderid']);
$mode = intval($_GPC['mode']);
$append = intval($_GPC['append']);
$psnum = $_GPC['psnum'];
if ($mode == 0) {
    message('请先选择下单模式', $this->createMobileUrl('detail', array('id' => $storeid)));
}

$user = pdo_fetch("SELECT * FROM " . tablename($this->table_fans) . " WHERE weid = :weid  AND from_user=:from_user ORDER BY `id` DESC limit 1", array(':weid' => $weid, ':from_user' => $from_user));

if ($user['status'] == 0) {
    message('你被禁止下单,不能进行相关操作...');
}

if (empty($storeid)) {
    message('请先选择门店', $this->createMobileUrl('waprestlist'));
}

$method = 'wapmenu'; //method
$host = $this->getOAuthHost();
$authurl = $host . 'app/' . $this->createMobileUrl($method, array('storeid' => $storeid, 'mode' => $mode), true) . '&authkey=1';
$url = $host . 'app/' . $this->createMobileUrl($method, array('storeid' => $storeid, 'mode' => $mode), true);
if (isset($_COOKIE[$this->_auth2_openid])) {
    $from_user = $_COOKIE[$this->_auth2_openid];
    $nickname = $_COOKIE[$this->_auth2_nickname];
    $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
} else {
    if (isset($_GPC['code'])) {
        $userinfo = $this->oauth2($authurl);
        if (!empty($userinfo)) {
            $from_user = $userinfo["openid"];
            $nickname = $userinfo["nickname"];
            $headimgurl = $userinfo["headimgurl"];
        } else {
            message('授权失败!');
        }
    } else {
        if (!empty($this->_appsecret)) {
            $this->getCode($url);
        }
    }
}
if (empty($from_user)) {
    message('会话已过期，请重新发送关键字!');
}

$this->resetHour();
$store = $this->getStoreById($storeid);
if ($store['is_rest'] != 1) {
    message('门店休息中!');
}

$iscard = $this->get_sys_card($from_user);

$mealtimes = pdo_fetchall("SELECT * FROM " . tablename($this->table_mealtime) . " WHERE weid=:weid AND storeid=:storeid ORDER BY id ASC", array(':weid' => $weid, ':storeid' => $storeid));

$dispatchareas = pdo_fetchall("SELECT * FROM " . tablename($this->table_dispatcharea) . " WHERE weid=:weid AND storeid=:storeid ORDER BY id ASC", array(':weid' => $weid, ':storeid' => $storeid));



$useraddress = pdo_fetch("SELECT * FROM " . tablename($this->table_useraddress) . " WHERE weid=:weid AND from_user=:from_user AND isdefault=1 LIMIT 1", array(':weid' => $weid, ':from_user' => $from_user));

$goods =  pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . " WHERE weid=:weid ORDER BY id ASC", array(':weid' => $weid));
foreach ($goods as $key => $value) {
     // echo $value;
        $send_way = $value['send_way'];
}
$psnum = $send_way;
// var_dump($send_way);
$select_mealdate = '';
if (!empty($store['delivery_within_days'])) {
    $delivery_within_days = intval($store['delivery_within_days']) + 1;
    for ($i = 0; $i < $delivery_within_days; $i++) {
        $date_title = '';
        if ($i == 0) {
            if ($store['delivery_isnot_today'] == 1) {
                continue;
            }
            $date_value = date("Y-m-d", TIMESTAMP); 
            $date_title = '今日';
        } elseif ($i == 1) {
            $date_value = date("Y-m-d", strtotime("+{$i} day"));
            $date_title = '明日';
        } else {
            $date_value = date("Y-m-d", strtotime("+{$i} day"));
            $date_title = date("Y-m-d", strtotime("+{$i} day"));
        }

        $select_mealdate .= "<option value='{$date_value}'>{$date_title}</option>";
    }
}
$select_mealtime = '';
$select_mealtime2 = '';
$cur_date = date("Y-m-d", TIMESTAMP);
foreach ($mealtimes as $key => $value) {
    $begintime = intval(strtotime(date('Y-m-d ') . $value['begintime']));
    $endtime = intval(strtotime(date('Y-m-d ') . $value['endtime']));
    if ($store['delivery_isnot_today'] == 1) {
        $select_mealtime .= '<option value="' . $value['begintime'] . '~' . $value['endtime'] . '">' . $value['begintime'] . '~' . $value['endtime'] . '</option>';
    } else {
        if ($store['is_delivery_nowtime'] == 1) {
            if (TIMESTAMP < $endtime) {//debug
                $select_mealtime .= '<option value="' . $value['begintime'] . '~' . $value['endtime'] . '">' . $value['begintime'] . '~' . $value['endtime'] . '</option>';
            }
        } else {
            if ($begintime > TIMESTAMP) {//debug
                $select_mealtime .= '<option value="' . $value['begintime'] . '~' . $value['endtime'] . '">' . $value['begintime'] . '~' . $value['endtime'] . '</option>';
            }
        }
    }

    $select_mealtime2 .= '<option value="' . $value['begintime'] . '~' . $value['endtime'] . '">' . $value['begintime'] . '~' . $value['endtime'] . '</option>';
}
if (empty($select_mealtime)) {
    $select_mealtime = '<option value="休息中">没在配送时间内</option>';
}

$flag = false;
$issms = intval($store['is_sms']);
$checkcode = pdo_fetch("SELECT * FROM " . tablename('weisrc_dish_sms_checkcode') . " WHERE weid = :weid  AND from_user=:from_user AND status=1 ORDER BY `id` DESC limit 1", array(':weid' => $weid, ':from_user' => $from_user));
if ($issms == 1 && empty($checkcode)) {
    $flag = true;
}

$setting = $this->getSetting();

$cart = pdo_fetchall("SELECT * FROM " . tablename($this->table_cart) . " a LEFT JOIN " . tablename('weisrc_dish_goods') . " b ON a.goodsid=b.id WHERE
 a.weid=:weid AND a.from_user=:from_user AND a.storeid=:storeid AND total<>0", array(':weid' => $weid, ':from_user' =>
    $from_user, ':storeid' => $storeid));
//打包费用
//p($cart);
$packvalue = 0;
foreach ($cart as $key => $value) {
    if ($value['status'] == 0) {
        message('商品' . $value['title'] . '已下架！');
    }
    if (($value["counts"]-$value["today_counts"]<=0 || $value["counts"]==0) && ($value["counts"] != -1)){
        $url = '../../app/' . $this->createMobileUrl('waplist', array('storeid' => $storeid, 'mode' => $mode));//更改URL跳转到我的订单
        message($value['title'] . '已没库存！请从购物车删除该商品后下单', $url, 'error');
    }
    if (($value["counts"] != -1) && ($value["counts"]-$value["today_counts"]-$value["total"]<0)){
        $url = '../../app/' . $this->createMobileUrl('waplist', array('storeid' => $storeid, 'mode' => $mode));//更改URL跳转到我的订单
        message($value['title'] . '库存不足！', $url, 'error');
    }
    $packvalue = $packvalue + $value['total'] * $value['packvalue'];
}

$cart2 = pdo_fetchall("SELECT * FROM " . tablename($this->table_cart) . " WHERE  storeid=:storeid AND from_user=:from_user AND weid=:weid AND
total>0 ", array(':storeid' => $storeid, ':from_user' => $from_user, ':weid' => $weid));
$totalcount = 0;
$totalprice = 0;
$goodsprice = 0;
foreach ($cart2 as $key => $value) {
    $totalcount = $totalcount + $value['total'];
    $totalprice = $totalprice + $value['total'] * $value['price'];
    $goodsprice = $goodsprice + $value['total'] * $value['price'];

}
// exit;
$jump_url = $this->createMobileurl('wapmenu', array('from_user' => $from_user, 'storeid' => $storeid), true);
$limitprice = 0;
if ($mode == 1) { //店内
    $tablesid = intval($_GPC['tablesid']);
    $table = pdo_fetch("SELECT * FROM " . tablename($this->table_tables) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $tablesid));
    if (empty($table)) {
        exit('餐桌不存在！');
    } else {
        //餐桌类型
        $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $table['tablezonesid']));
        if (empty($tablezones)) {
            exit('餐桌类型不存在！');
        }
        $table_title = $tablezones['title'] . '-' . $table['title'];
        if ($append == 0) {
            $limitprice = floatval($tablezones['limit_price']);
        }
        $service_rate = floatval($tablezones['service_rate']);
    }

    //茶位费
    $is_tea_money = intval($store['is_tea_money']);
    $teatip = empty($store['tea_tip']) ? "茶位费用" : $store['tea_tip'];
    $teavaule = 0;
    if ($is_tea_money == 1) {
        $default_user_count = intval($store['default_user_count']);
        $teavaule = floatval($store['tea_money']);
        $totalteavalue = $append != 0 ? 0 : $teavaule * $default_user_count;
    }
} elseif ($mode == 2) { //外卖
    $limitprice = floatval($store['sendingprice']);
    $jump_url = $this->createMobileurl('wapmenu', array('from_user' => $from_user, 'storeid' => $storeid, 'mode' => 2), true);
} elseif ($mode == 5) {//排队
    $jump_url = $this->createMobileurl('queue', array('from_user' => $from_user, 'storeid' => $storeid), true);
}

$strwhere = " WHERE a.weid = :weid AND b.from_user=:from_user AND a.storeid=:storeid AND b.status=0 AND goodsids<>'0' AND FIND_IN_SET(:goodsid, goodsids) AND :time<a.endtime AND (a.type=1 OR a.type=2) ";

$strwhere1 = " WHERE a.weid = :weid AND b.from_user=:from_user AND a.storeid=:storeid AND b.status=0 AND goodsids='0' AND a.gmoney>0 AND a.gmoney<={$totalprice} AND :time<a.endtime AND (a.type=1 OR a.type=2) ";

if ($mode == 1) { //店内
    $strwhere .= " AND a.is_meal=1 ";
    $strwhere1 .= " AND a.is_meal=1 ";
} else if ($mode == 2) { //外卖
    $strwhere .= " AND a.is_delivery=1 ";
    $strwhere1 .= " AND a.is_delivery=1 ";
} else if ($mode == 3) { //预定
    $strwhere .= " AND a.is_reservation=1 ";
    $strwhere1 .= " AND a.is_reservation=1 ";
} else if ($mode == 4) { //快餐
    $strwhere .= " AND a.is_snack=1 ";
    $strwhere1 .= " AND a.is_snack=1 ";
}
$param = array(':weid' => $weid, ':from_user' => $from_user, ':time' => TIMESTAMP, ':storeid' => $storeid);

//php获取当前时间搓
$time = time();

$couponlist = pdo_fetchall("SELECT a.id as ids,a.couponid,a.weid,a.from_user,a.status,b.* FROM".tablename('weisrc_dish_sncode')."as a left join ".tablename('weisrc_dish_coupons')." as b on a.couponid = b.id where a.weid = :weid and a.from_user =:from_user and b.starttime <{$time} and b.endtime >{$time} and a.status = 0",array(':weid'=>$weid,':from_user'=>$from_user));
foreach ($couponlist as $k => $v){
   if ($v["storeid"] != 0 && $v["storeid"] != $storeid){
           //删除不是当前店面的优惠劵
           unset($couponlist[$k]);
   }

//   if (isset($v["goodsids"])){
//        //删除不是当前商品的优惠劵
//        $goodsids = explode(",",$v["goodsids"]);
//        if (!in_array(0,$goodsids)){
//            $goodsids_cart = array_column($cart,"goodsid");
//            if (empty(array_intersect($goodsids,$goodsids_cart))){
//                unset($couponlist[$k]);
//            }
//        }
//    }
}
//查询会员卡
$huiyuan_ka = pdo_fetch("SELECT * FROM".tablename('weisrc_dish_member_ka')."as a left join ".tablename('weisrc_dish_member')." as b on a.weid = b.weid where a.weid ='{$weid}' and a.user='{$from_user}' and a.endtime>'{$time}' ");
if(!empty($huiyuan_ka)){//会员打折
    $zhekou = round(($totalprice*(10-$huiyuan_ka['limit_discount'])/10),2);
}else{
    $zhekou = 0;
}

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
        //$totalprice = $totalprice-$jifen_dk;
        //var_dump($totalprice);exit();
    }
}else{
    $jifen_dk = 0;
}


//var_dump($couponlist);
//var_dump($_GPC['selectcoupon']);
//$couponlist = pdo_fetchall("SELECT a.*,b.sncode,b.id AS couponid FROM " . tablename($this->table_coupon) . " a INNER JOIN" . tablename($this->table_sncode) . " b ON a.id= b.couponid {$strwhere1} ORDER BY b.id DESC LIMIT 30", $param);

//var_dump($couponid);exit();
// foreach ($cart2 as $key => $value) {
//     $param[':goodsid'] = $value['goodsid'];
//     $coupon = pdo_fetchall("SELECT a.*,b.sncode,b.id AS couponid FROM " . tablename($this->table_coupon) . " a INNER JOIN
// " . tablename($this->table_sncode) . " b ON a.id= b.couponid {$strwhere} ORDER BY b.id DESC LIMIT 30", $param);
//     if ($coupon) {
//         $couponlist = array_merge($couponlist, $coupon);
//     }
// }


$isnewuser = $this->isNewUser($storeid);
$dlimitprice = 0;
if ($isnewuser == 1) { //新用户
    if ($store['is_newlimitprice'] == 1) { //新顾客满减
        $coupon_obj1 = $this->getNewLimitPrice($storeid, $goodsprice, $mode);
        if ($coupon_obj1) {
            $dlimitprice = floatval($coupon_obj1['dmoney']);
            $totalprice = $goodsprice - $dlimitprice;
        }
    }
} else { //老用户
    if ($store['is_oldlimitprice'] == 1) { //老顾客满减
        $coupon_obj2 = $this->getOldLimitPrice($storeid, $goodsprice, $mode);
        if ($coupon_obj2) {
            $dlimitprice = floatval($coupon_obj2['dmoney']);
            $totalprice = $goodsprice - $dlimitprice;
        }
    }
}

$is_auto_address = intval($setting['is_auto_address']);

$over_radius = 0;
$delivery_radius = floatval($store['delivery_radius']);
if ($mode == 2) {
    //距离
    $addressLatLng =  pdo_fetch("SELECT * FROM " . tablename('weisrc_dish_useraddress') . " WHERE id = :id limit 1", array(':id' => $_GPC['addressid']));
    //計算兩經緯度之間骑行距離
    $distance = $this->getDistanceByGaodeForRiding($addressLatLng['lat'], $addressLatLng['lng'], $store['lat'], $store['lng']);
    if($distance==0){
        $distance = $this->getDistance($addressLatLng['lat'], $addressLatLng['lng'], $store['lat'], $store['lng']);
    }
    $distance = floatval($distance);
    if ($store['not_in_delivery_radius'] == 0) { //只能在距离范围内
        if ($distance > $delivery_radius) {
            $over_radius = 1;
        }

    }
}
//p($addressLatLng);
//p($store);
//var_dump($distance);die;
if ($is_auto_address == 0 && $useraddress) { //多收餐地址 算距离
//if ($useraddress) { //多收餐地址 算距离 計算兩經緯度之間骑行距離
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
//var_dump($psf);exit();
//delivery_radius
if ($store['is_delivery_distance'] == 1) { //按距离收费
    if($psnum == 2){
        $dispatchprice = 0;
    }else{
        if ($distance > $delivery_radius) {
            $over_radius = 1;
            
        }
        //阶梯距离价格
        $distanceprice = $this->getdistanceprice($storeid, $distance);
		//$distanceprice11 = $distanceprice;
		//sjg 2018-09-09
		if(empty($distanceprice) || $distanceprice['dispatchprice'] == 0) {
			$dispatchprice = $psf;
        }else{
//            var_dump(floatval($distanceprice['dispatchprice']));
//            var_dump($psf);die;
			$dispatchprice = floatval($distanceprice['dispatchprice'])-$psf;
			#新增如果距离配送费小于商家配送费的时候需要修改
//            $dispatchprice = ($dispatchprice<0)?0:$dispatchprice;
        }
    }
    if ($store['not_in_delivery_radius'] == 0) { //只能在距离范围内
        if ($distance > $delivery_radius) {
            $over_radius = 1;
            //exit('只能在距离范围内');
            //$address = pdo_fetchall("select * from " . tablename($this->table_useraddress) . " WHERE from_user=:from_user",array(":from_user"=>$from_user));
            // if(!empty($address)){
            //     message('地址超出配送范围，请从新选择地址', $this->createMobileUrl('useraddress', array('storeid' => $storeid, 'mode' => $mode, 'op' => 'display')));
            // }
            //var_dump($address);exit();
            //
        }
        //message('!');$this->createMobileUrl('useraddress', array('storeid' => $storeid, 'mode' => $mode, 'op' => 'display')
        //
    }

} else {
	if($psnum == 2){ //邮寄
		$dispatchprice = 0;
	}else{
		//配送费
		$dispatchprice = floatval($store['dispatchprice']);
	}
}

if ($store['is_delivery_time'] == 1) { //特殊时段加价
    $tprice = $this->getPriceByTime($storeid);
    $dispatchprice = $dispatchprice + $tprice;
}

if($psnum == 2){//邮递

    $dispatchprice = 0;
}
$goodsids = join(array_column($cart,'id'),',');
//echo "<pre>";
//print_r($storeid);
//print_r($couponlist);die;
$share_title = !empty($setting['share_title']) ? str_replace("#username#", $nickname, $setting['share_title']) : "您的朋友{$nickname}邀请您来吃饭";
$share_desc = !empty($setting['share_desc']) ? str_replace("#username#", $nickname, $setting['share_desc']) : "最新潮玩法，快来试试！";
$share_image = !empty($setting['share_image']) ? tomedia($setting['share_image']) : tomedia("../addons/weisrc_dish/icon.jpg");
$share_url = $host . 'app/' . $this->createMobileUrl('usercenter', array('agentid' => $fans['id']), true);

include $this->template($this->cur_tpl . '/menu');