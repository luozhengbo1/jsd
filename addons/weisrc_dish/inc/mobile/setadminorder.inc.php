<?php
global $_W, $_GPC;
$weid = $this->_weid;
$from_user = $this->_fromuser;
$setting = $this->getSetting();
$id = intval($_GPC['orderid']);
$status = trim($_GPC['status']);
$totalprice = floatval($_GPC['totalprice']);
$remark = trim($_GPC['remark']);

$orderstatus = array(
    'cancel' => -1,
    'confirm' => 1,
    'finish' => 3,
    'pay' => 2,
    'updateprice' => 4,
    'print' => 5
);

if (empty($orderstatus[$status])) {
    message('对不起，您没有该功能的操作权限!!');
}

if (empty($from_user)) {
    message('会话已过期，请重新发送关键字!');
}

$is_permission = false;
$tousers = explode(',', $setting['tpluser']);
if (in_array($from_user, $tousers)) {
    $is_permission = true;
}
if ($is_permission == false) {
    $accounts = pdo_fetchall("SELECT storeid FROM " . tablename($this->table_account) . " WHERE weid = :weid AND from_user=:from_user AND
 status=2 AND is_admin_order=1 ORDER BY id DESC ", array(':weid' => $this->_weid, ':from_user' => $from_user));
    if ($accounts) {
        $arr = array();
        foreach ($accounts as $key => $val) {
            $arr[] = $val['storeid'];
        }
        $storeids = implode(',', $arr);
        $is_permission = true;
    }
}

if ($is_permission == false) {
    message('对不起，您没有该功能的操作权限!');
}

$order = $this->getOrderById($id);
//$totalprice = floatval($order['totalprice']);
$store = $this->getStoreById($order['storeid']);
if (empty($order)) {
    message('订单不存在');
}

//处理打印
if ($orderstatus[$status] == 5) {
    $position_type = intval($_GPC['position_type']);
    $this->feieSendFreeMessage($id, $position_type);
    $this->feiyinSendFreeMessage($id, $position_type);
    $this->_365SendFreeMessage($id, $position_type);
    $this->_yilianyunSendFreeMessage($id, $position_type);
    message('操作成功！！', $this->createMobileUrl('adminorderdetail', array('orderid' => $id), true), 'success');
}

$user = $this->getFansByOpenid($from_user);
$touser = empty($user['nickname'])?$user['from_user']:$user['nickname'];

$paylog = pdo_fetch("SELECT * FROM " . tablename('core_paylog') . " WHERE tid=:tid AND uniacid=:uniacid AND status=0 AND module='weisrc_dish'
ORDER BY plid
DESC LIMIT 1", array(':tid' => $order['id'], ':uniacid' => $this->_weid));

$update_data = array(
    'totalprice' => $totalprice,
    'remark' => $remark
);

if ($orderstatus[$status] == 2) { //支付
    $update_data['ispay'] = 1;
    $update_data['paytime'] = TIMESTAMP;
    pdo_update($this->table_order, $update_data, array('id' => $order['id']));
    $this->addOrderLog($id, $touser, 2, 1, 2);

} else if ($orderstatus[$status] == 4) { //改价

    pdo_update($this->table_order, $update_data, array('id' => $order['id']));
    $this->addOrderLog($id, $touser, 2, 1, 7, $order['totalprice'], $totalprice);

} else if ($orderstatus[$status] == -1) { //取消

    if ($order['ispay'] == 1) {
        $update_data['ispay'] = 2;//待退款
    }
    $cacle_order = pdo_fetch("select id,status from ".tablename('weisrc_dish_order')." where id=:id ",array(':id'=>$id) );
    if($cacle_order['status']==-1 ){
        message('操作失败！该订单已经取消', $this->createMobileUrl('adminorderdetail', array('orderid' => $id), true), 'error');
        die;
    }
    //將商品庫存加回來
    $sql = "select a.total,a.goodsid,b.isoptions,a.optionid,b.counts,b.today_counts,b.sales,a.dateline from
            ".tablename('weisrc_dish_order_goods')."as a left join
            " .tablename('weisrc_dish_goods')." as  b on  b.id=a.goodsid  where a.orderid=:orderid ";
    $goodsList = pdo_fetchall($sql,array(':orderid'=>$id));
    if(!empty($goodsList) && is_array($goodsList)){
        $today_start = strtotime(date('Y-m-d 00:00:00'));
        $today_end = strtotime(date('Y-m-d 23:59:59'));
        foreach ($goodsList as $k=>$v){
            //判斷订单是否当天订单
            if(  $v['dateline']>=$today_start && $v['dateline']<=$today_end   ){
                //减去销量
                $todaySales = $v['today_counts']-$v['total'];
                $todaySales = $todaySales<=0?0:$todaySales;
                $sales = (($v['sales'] -$v['total'])<=0)?0:($v['sales'] -$v['total']);
                $update=['today_counts' =>$todaySales,'sales'=>$sales];
                pdo_update("weisrc_dish_goods",$update,array('id'=>$v['goodsid']));
            }
        }
    }
    if ($order['ispay'] == 1 || $order['ispay'] == 2 || $order['ispay'] == 4) { //已支付和待退款的可以退款
        $refund_price = $order['totalprice'];
        $store = $this->getStoreById($order['storeid']);
        if ($order['paytype'] == 2) { //微信支付
            //关单时判断是否退过款
            if($order['refund_price']>0){
                $result = $this->refund2($id, $refund_price,$order['origin_totalprice']);
            }else{
                $result = $this->refund2($id, $refund_price);
            }
            if ($result == 1) {
                //开始分摊金额  is_return 表示商品未退的进行分摊。
                // $refund_price=0.09;
                //  $order['totalprice']= 0.5;
                $ordergoodsList = pdo_fetchall("select *,total*price as moneyrate from ".tablename('weisrc_dish_order_goods')." where is_return=0  and  orderid=:orderid order by moneyrate desc ",array(':orderid'=>$id) );
                $totalRealPrice = 0;
                $totalPrice_total = array_sum(array_column($ordergoodsList,'moneyrate'));
                foreach ($ordergoodsList as $k=>$v){
                    //  $ordergoodsList[$k]['real_price']=  floor($v['price']*$v['total']/$order['totalprice'] * $refund_price *100)/100;
                    $ordergoodsList[$k]['real_tmp_price']=  number_format($v['price']*$v['total']/$totalPrice_total * $refund_price,2) ;
                    $totalRealPrice+= $ordergoodsList[$k]['real_tmp_price'];
//            p($ordergoodsList[$k]['real_price']);
                }
                $errorMoney = ($refund_price*100 - $totalRealPrice*100)/100 ;
                $ordergoodsList[0]['real_tmp_price'] =($ordergoodsList[0]['real_tmp_price']*100+ $errorMoney*100)/100;
                //  p($errorMoney);
                foreach ($ordergoodsList as $k=>$v){
                    $updateRealMoney =['real_price' =>$v['real_price'] + $v['real_tmp_price'],'single_real_price'=>($v['real_price'] + $v['real_tmp_price'])/$v['total'] ];
//                    $updateRealMoney=['real_price' =>$v['real_price'] + $v['real_tmp_price'] ];
                    pdo_update($this->table_order_goods,$updateRealMoney,array('id'=>$v['id']));
                }
                //  p($ordergoodsList); die;
                //分摊结束
                $order["refund_price1"] = $refund_price;
                $order["ispay"] = 3;//为了初始化订单退款推送状态
                $this->sendOrderNotice($order, $store, $setting);
                //
            }
        } else if ($order['paytype'] == 1) {
            $this->setFansCoin($order['from_user'], $refund_price, "码上点餐单号{$order['ordersn']}退款");
            pdo_update($this->table_order, array('ispay' => 3, 'refund_price' => $refund_price), array('id' => $id));
            $this->sendOrderNotice($order, $store, $setting);
            message('操作成功！', $url, 'success');
        } else {
            pdo_update($this->table_order, array('ispay' => 3, 'refund_price' => $refund_price), array('id' => $id));
            $this->sendOrderNotice($order, $store, $setting);
            message('操作成功！', $url, 'success');
        }
    }

    $update_data['status'] = $orderstatus[$status];
    pdo_update($this->table_order, $update_data, array('id' => $order['id']));
    $this->cancelfengniao($order, $store, $setting);
    $this->addOrderLog($id, $touser, 2, 1, 5);

} elseif ($orderstatus[$status] == 1) { //确认
    $order['status'] = $orderstatus[$status];
    $this->sendOrderNotice($order, $store, $setting);
    $update_data['confirmtime'] = TIMESTAMP;
    $update_data['status'] = $orderstatus[$status];
    pdo_update($this->table_order, $update_data, array('id' => $order['id']));
    pdo_update($this->table_service_log, array('status' => 1), array('orderid' => $id));
//    $this->doDada($weid,$id,$order['storeid']);
    //如果是配置了达达进行调用
    $storesInfo = pdo_fetch("select id,is_dada,shop_no,source_id from ".tablename('weisrc_dish_stores')." where id=:id limit 1",array(":id"=>$order['storeid']));
    if($storesInfo['is_dada']==1 &&  !empty($storesInfo['shop_no']) && !empty($storesInfo['source_id'])  ){
        //新增達達配送狀態
        $this->doDada($weid,$id,$order['storeid']);
//        if($dadares=='success'){
//            $msg ="订单已推送给达达";
        pdo_update($this->table_order, array('delivery_status' => 1), array('id' => $id, 'weid' => $weid));
//        }else{
//            $msg ="订单推达达失败，联系管理员,请先自己配送";
//            pdo_update($this->table_order, array('delivery_status' => 3), array('id' => $id, 'weid' => $weid));
//        }
        //商家自配
    }else{
        pdo_update($this->table_order, array('delivery_status' => 3), array('id' => $id, 'weid' => $weid));
    }
    $this->addOrderLog($id, $touser, 2, 1, 3);
//    p($order);die;

} else if ($orderstatus[$status] == 3) { //完成

    $update_data['finishtime'] = TIMESTAMP;
    $update_data['status'] = $orderstatus[$status];
    pdo_update($this->table_order, $update_data, array('id' => $order['id']));
    $this->addOrderLog($id, $touser, 2, 1, 4);

    $this->updateFansData($order['from_user']);
    $this->updateFansFirstStore($order['from_user'], $order['storeid']);
    //修改为已配送
    pdo_update($this->table_order, array('delivery_status' => 2), array('id' => $id, 'weid' => $weid));
    if ($order['isfinish'] == 0) {
        //计算积分
        $this->setOrderCredit($order['id']);
        pdo_update($this->table_order, array('isfinish' => 1), array('id' => $id));
        pdo_update($this->table_service_log, array('status' => 1), array('orderid' => $id));
        $fans = pdo_fetch("SELECT * FROM " . tablename($this->table_fans) . " WHERE weid = :weid  AND from_user=:from_user ORDER BY `id` DESC limit 1", array(':weid' => $weid, ':from_user' => $order['from_user']));
        pdo_update($this->table_fans, array('paytime' => TIMESTAMP), array('id' => $fans['id']));
        if ($order['dining_mode'] == 1) {
            pdo_update($this->table_tables, array('status' => 0), array('id' => $order['tables']));
        }
        $this->set_commission($order['id']);
        //奖励配送员
        $delivery_money = floatval($order['delivery_money']);//配送佣金
        $delivery_id = intval($order['delivery_id']);//配送员
        if ($delivery_money > 0) {
            $data = array(
                'weid' => $_W['uniacid'],
                'storeid' => $order['storeid'],
                'orderid' => $order['id'],
                'delivery_id' => $delivery_id,
                'price' => $delivery_money,
                'dateline' => TIMESTAMP,
                'status' => 0
            );
            pdo_insert("weisrc_dish_delivery_record", $data);
        }
    }
}
if (!empty($paylog) && $orderstatus[$status] != -1) {
    pdo_update('core_paylog', array('fee' => $totalprice), array('plid' => $paylog['plid']));
}
if ($this->_accountlevel == 4) {
    $order = $this->getOrderById($id);
    $this->sendOrderNotice($order, $store, $setting);
}
message('操作成功！！', $this->createMobileUrl('adminorderdetail', array('orderid' => $id), true), 'success');