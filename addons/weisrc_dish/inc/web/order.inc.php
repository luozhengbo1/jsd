<?php
global $_W, $_GPC;
$weid = $this->_weid;
$setting = $this->getSetting();
load()->func('tpl');
$action = 'order';
$title = $this->actions_titles[$action];
$storeid = intval($_GPC['storeid']);
$GLOBALS['frames'] = $this->getNaveMenu($storeid,$action);
$returnid = $this->checkPermission($storeid);
$cur_store = $this->getStoreById($storeid);

if (empty($cur_store)) {
    message('门店不存在!');
}

if (!$this->exists()) {
    $_GPC['idArr'] = '';
}

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'fengniaolist') {
    $pindex = max(1, intval($_GPC['page']));
    $psize = 10;

    $commoncondition = " weid = '{$_W['uniacid']}' ";
    if ($storeid != 0) {
        $commoncondition .= " AND storeid={$storeid} ";
    }
    $list = pdo_fetchall("SELECT * FROM " . tablename("weisrc_dish_fengniao") . " WHERE $commoncondition ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);

    $total = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename("weisrc_dish_fengniao") . " WHERE $commoncondition");
    $pager = pagination($total, $pindex, $psize);
} elseif ($operation == 'display') {
    $commoncondition = " weid = '{$_W['uniacid']}' ";
    if ($storeid != 0) {
        $commoncondition .= " AND storeid={$storeid} ";
    }
    $ispay = intval($_GPC['ispay']);
    if (isset($_GPC['ispay']) && $_GPC['ispay'] != '') {
        $commoncondition .= " AND ispay={$ispay} ";
    }

    if (!empty($_GPC['time'])) {
        $starttime = strtotime($_GPC['time']['start']);
        $endtime = strtotime($_GPC['time']['end']);
        $commoncondition .= " AND dateline >= :starttime AND dateline <= :endtime ";
        $paras[':starttime'] = $starttime;
        $paras[':endtime'] = $endtime;
    }

    if (empty($starttime) || empty($endtime)) {
        $starttime = strtotime('-1 month');
        $endtime = time();
    }

    $pindex = max(1, intval($_GPC['page']));
    $psize = 10;

    if (!empty($_GPC['ordersn'])) {
        $commoncondition .= " AND ordersn LIKE '%{$_GPC['ordersn']}%' ";
    }
    if (!empty($_GPC['dining_mode'])) {
        $commoncondition .= " AND dining_mode = '" . intval($_GPC['dining_mode']) . "' ";
    }

    $tablesid = $_GPC['tableid'];
    $table = pdo_fetch("SELECT * FROM " . tablename($this->table_tables) . " where weid = :weid AND title=:title LIMIT 1", array(':weid' => $weid, ':title' => $tablesid));
    if (!empty($table)) {
        $commoncondition .= " AND tables = '" . $table['id'] . "' ";
    }

    if (isset($_GPC['status']) && $_GPC['status'] != '') {
        $commoncondition .= " AND status = '" . intval($_GPC['status']) . "'";
    }

    if (isset($_GPC['paytype']) && $_GPC['paytype'] != '') {
        $commoncondition .= " AND paytype = '" . intval($_GPC['paytype']) . "'";
    }


    if ($_GPC['out_put'] == 'output') {
        $commoncondition .= " AND ismerge = 0 ";
        $this->out_order($commoncondition, $paras);
    } else if($_GPC['out_put'] == 'out_goods') {
        $commoncondition .= " AND ismerge = 0 ";
        $this->out_goods($commoncondition, $paras);
    }

    $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_order) . " WHERE $commoncondition ORDER BY id desc, dateline DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $paras);

    $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_order) . " WHERE $commoncondition", $paras);
    $pager = pagination($total, $pindex, $psize);
    $order_count = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename($this->table_order) . " WHERE weid=:weid AND status=0 AND storeid=:storeid LIMIT 1", array(':weid' => $this->_weid, ':storeid' => $storeid));

    if (!empty($list)) {
        foreach ($list as $key => $value) {
            $userids[$row['from_user']] = $row['from_user'];
            if (!empty($value["storeid"])){
                $storedata = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . " where id=:id LIMIT 1", array(':id' => $value["storeid"]));
                $list[$key]['store_type'] = $storedata['store_type'];
            }
            if ($value['dining_mode'] == 1 || $value['dining_mode'] == 3) {
                $tablesid = intval($value['tables']);
                $table = pdo_fetch("SELECT * FROM " . tablename($this->table_tables) . " where id=:id LIMIT 1", array(':id' => $tablesid));
                if (!empty($table)) {
                    $table_title = $table['title'];
                    $list[$key]['table'] = $table_title;
                }
            }
        }
    }

    $order_price = pdo_fetchcolumn("SELECT sum(totalprice) FROM " . tablename($this->table_order) . " WHERE $commoncondition ", $paras);
    $order_price = sprintf('%.2f', $order_price);

    //打印数量
    $print_order_count = pdo_fetchall("SELECT orderid,COUNT(1) as count FROM " . tablename($this->table_print_order) . "  GROUP BY orderid,weid having weid = :weid", array(':weid' => $_W['uniacid']), 'orderid');

    //门店列表
    $storelist = pdo_fetchall("SELECT * FROM " . tablename($this->table_stores) . " WHERE weid = :weid", array(':weid' => $_W['uniacid']), 'id');
} elseif ($operation == 'detail') {
    //流程 第一步确认付款 第二步确认订单 第三步，完成订单
    $id = intval($_GPC['id']);
    $this->feiyinSendFreeMessage($id);
    $order = $this->getOrderById($id);
    $fans = $this->getFansByOpenid($order['from_user']);

    if ($order['dining_mode'] == 1) {
        $tablelist = $this->getAllTableByStoreid($order['storeid']);
    }

    $orderlog = pdo_fetchall("SELECT * FROM " . tablename($this->table_order_log) . " WHERE orderid=:orderid ORDER BY id desc, dateline DESC", array(':orderid' => $id));

    if (!empty($_GPC['confirmtable'])) {
        if ($order['status'] == -1 || $order['ispay'] == 3) {
            message('取消和已退款订单不允许操作！', referer(), 'success');
        }
        //如果订单未支付 只恢复库存，  如果订单已支付需要退款 并恢复库存

        $tableid = intval($_GPC['tableid']);
        $table = $this->getTableById($tableid);
        if (!empty($table)) {
            $tablezones = $this-> getTablezonesById($table['tablezonesid']);
            $tablezonesid = $tablezones['id'];
        }
        pdo_update($this->table_order, array('tables' => $tableid, 'tablezonesid' => $tablezonesid), array('id' => $id));
        message('操作成功！', referer(), 'success');
    }
    if (!empty($_GPC['confirmcounts'])) { //改人数
        if ($order['status'] == -1 || $order['ispay'] == 3) {
            message('取消和已退款订单不允许操作！', referer(), 'success');
        }
        if ($order['paytype'] == 1 || $order['paytype'] == 2 || $order['paytype'] == 4 || $order['status'] == 3) {
            message('在线支付和已完成的单子不允许修改用餐人数！');
        }

        $store = $this->getStoreById($order['storeid']);
        $counts = intval($_GPC['counts']);//人数
        $teavalue = floatval($cur_store['tea_money']) * $counts; //茶位费
        $discount_money = floatval($order['discount_money']);//抵扣金额
        $service_money = floatval($order['service_money']);//服务费
        $goodsprice = floatval($order['goodsprice']);//商品金额

        $totalprice = $goodsprice + $service_money + $teavalue;
        $totalprice = $totalprice - $discount_money;

        pdo_update($this->table_order, array('counts' => $counts, 'tea_money' => $teavalue, 'totalprice' =>
            $totalprice), array
        ('id' => $id));
        message('操作成功！', referer(), 'success');
    }
    //改价
    if (!empty($_GPC['confirmprice'])) {
        if ($order['status'] == -1 || $order['ispay'] == 3) {
            message('取消和已退款订单不允许操作！', referer(), 'success');
        }
        if ($setting['is_operator_pwd'] == 1) {
            $operator_pwd = trim($_GPC['operator_pwd']);
            if ($setting['operator_pwd'] != $operator_pwd) {
                message('操作密码错误，请重新输入!');
            }
        }
        pdo_update($this->table_order, array('totalprice' => $_GPC['updateprice']), array('id' => $id));
        $paylog = pdo_fetch("SELECT * FROM " . tablename('core_paylog') . " WHERE tid=:tid AND uniacid=:uniacid AND status=0 AND module='weisrc_dish'
ORDER BY plid
DESC LIMIT 1", array(':tid' => $id, ':uniacid' => $this->_weid));
        if (!empty($paylog)) {
            pdo_update('core_paylog', array('fee' => $_GPC['updateprice']), array('plid' => $paylog['plid']));
        }
        $this->addOrderLog($id, $_W['user']['username'], 2, 2, 7, $order['totalprice'], $_GPC['updateprice']);
        message('改价成功！', referer(), 'success');
    }
    if (checksubmit('discount_submit')) {
        $rebate = round(floatval($_GPC['discount_rebate']), 2);
        if (empty($rebate)) {
            message("你输入的折扣率有误", referer(), 'error');
        }
    }
	//加菜查询begin
    if ($_POST['selectDish']) {
        $dishCondition = '';
        if ($_POST['addDishName']) {
            $dishCondition = "AND pcate=" . intval($_POST['addDishName']);
        }
        $allGoods = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . " WHERE storeid=:storeid AND weid=:weid  AND deleted=0 " . $dishCondition, array(":storeid" => $storeid, ":weid" => $this->_weid));
        foreach($allGoods as $key => $value) {
            $allGoods[$key]['thumb'] = tomedia($value['thumb']);
        }
        exit(json_encode($allGoods));
    }
    if ($_POST['addDish'] && !empty($_POST['dish'])) {
        $dish = $_POST['dish'];
        $dishInfo = pdo_fetchall("SELECT goodsid,price,total FROM " . tablename($this->table_order_goods) . " WHERE weid=:weid AND storeid=:storeid AND orderid=:orderid", array(":weid" => $weid, ":storeid" => $storeid, ":orderid" => $id));
        foreach ($dishInfo as $v) {
            $dishid[] = $v['goodsid'];
        }
        foreach ($dish as $k => $v) {
            if ($v['status'] != "己选择" || empty($v['num'])) {
                unset($dish[$k]);
            } else {
                if (!empty($dish)) {
                    if (in_array($k, $dishid)) {
                        $dishParm = array("total" => "total+{$v['num']}", "dateline" => time);
                        $dishCon = array(":weid" => $weid,":storeid"=>$storeid,":orderid"=>$id,":goodsid"=>$k);
                        $sql="UPDATE ".tablename($this->table_order_goods)." SET total=total+{$v['num']},dateline=".time()." WHERE weid=:weid AND storeid=:storeid AND orderid=:orderid AND goodsid=:goodsid";
                        pdo_query($sql,$dishCon);
                    } else {
                        $parm = array("weid" => $weid, "storeid" => $storeid, "orderid" => $id, "goodsid" => $k, "price" => $v['price'], "total" => $v['num'], 'dateline' => time());
                        pdo_insert($this->table_order_goods, $parm);
                    }

                    $add_goods = pdo_fetch("SELECT * FROM " . tablename($this->table_goods) . " WHERE weid=:weid AND id=:id ORDER by id DESC LIMIT 1", array(':weid' => $this->_weid, ':id' => $k));
                    $touser = $_W['user']['username'] . '&nbsp;加菜：' . $add_goods['title'] . "*" . $v['num'] . ",";
                    $this->addOrderLog($id, $touser, 2, 2, 1);
                }
            }
        }
        $newOrder=pdo_fetchall("SELECT price,total FROM ".tablename($this->table_order_goods)." WHERE orderid=:id",array(":id"=>$id));
        foreach($newOrder as $v){
            $dishTotal['num']+=$v['total'];
            $dishTotal['price']+=(number_format(floatval($v['price']),2) * $v['total']);
        }

        $newtotalprice = 0;
        $newtotalprice = $dishTotal['price'] + floatval($order['tea_money']) + floatval($order['service_money']) + floatval($order['dispatchprice']) + floatval($order['packvalue']);
        pdo_update($this->table_order, array("totalnum" => $dishTotal['num'], "totalprice" => $newtotalprice, "goodsprice" => $dishTotal['price']), array("id" => $id));

        message('操作成功！', referer(), 'success');
    }

    if (checksubmit('confrimpay')) {
        if ($order['status'] == -1 || $order['ispay'] == 3) {
            message('取消和已退款订单不允许操作！', referer(), 'success');
        }
        pdo_update($this->table_order, array('ispay' => 1), array('id' => $id));
        $this->addOrderLog($id, $_W['user']['username'], 2, 2, 2);
        message('操作成功！', referer(), 'success');
    }

    if (checksubmit('confrimsign')) {
        pdo_update($this->table_order, array('reply' => $_GPC['reply']), array('id' => $id));
        message('操作成功！', referer(), 'success');
    }

    $store = $this->getStoreById($order['storeid']);

    if (!empty($_GPC['finish'])) {
        //isfinish
        if ($order['isfinish'] == 0) {
            if ($order['status'] == -1 || $order['ispay'] == 3) {
                message('取消和已退款订单不允许操作！', referer(), 'success');
            }
            //计算积分
            $this->setOrderCredit($order['id']);
            pdo_update($this->table_order, array('isfinish' => 1), array('id' => $id));
            pdo_update($this->table_service_log, array('status' => 1), array('orderid' => $id));
            pdo_update($this->table_fans, array('paytime' => TIMESTAMP), array('id' => $fans['id']));
            if ($order['dining_mode'] == 1) {
                pdo_update($this->table_tables, array('status' => 0), array('id' => $order['tables']));
            }
            $this->set_commission($id);

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

        pdo_update($this->table_order, array('status' => 3, 'finishtime' => TIMESTAMP), array('id' => $id, 'weid' => $weid));
        //查询订单平台支付费用
        $dingdan = pdo_fetch("SELECT * FROM".tablename($this->table_order)."where id ='{$id}' and weid = '{$weid}' ");
        $pt_yf = $dingdan['pt_yf'];//平台承担运费
        $dprice = $dingdan['dprice'];//平台优惠券
        $zhekou = $dingdan['zhekou'];//会员折扣
        $jifen_dk = $dingdan['jifen_dk'];//积分抵扣
        //var_dump($pt_yf);
        //var_dump($dprice);
        //查询平台运营费
        $yun_ying = pdo_fetch("SELECT * FROM " . tablename($this->table_cost) . "WHERE weid = '$weid'");
        //var_dump($yun_ying['total_price']);exit();
        if($yun_ying['total_price']<1000){//运费小于1000发邮件
            $title = "您平台运营费小于1000元，请您及时充值";
            $this->doEmail($title);
            //echo "1";
        }
        if($pt_yf>0){//增加记录，减少运营费
            if($pt_yf>$yun_ying['total_price']){//运营费不足抵扣（发邮件）
                $title = "您平台运营费小于平台平台承担运费，不足抵扣，请您及时充值";
                $this->doEmail($title);
                //echo "2";
            }else{
               $yun = array(
                    'weid'=>$weid,
                    'orderid'=>$dingdan['ordersn'],
                    'title'=>'平台承担运费',
                    'money'=>$pt_yf,
                    'time'=>time(),
                );
                pdo_insert("weisrc_dish_cost_jilu",$yun);
                pdo_update($this->table_cost,array('total_price'=>$yun_ying['total_price']-$pt_yf),array('weid'=>$weid)); 
                //echo "3";
            }
            
        }
        if($dprice>0){//增加记录，减少运营费
            if($dprice > $yun_ying['total_price']){//运营费不足抵扣（发邮件）
                $title = "您平台运营费小于平台平台平台优惠券抵扣费用，不足抵扣，请您及时充值";
                $this->doEmail($title);
                //echo "4";
            }else{
                $yun = array(
                    'weid'=>$weid,
                    'orderid'=>$dingdan['ordersn'],
                    'title'=>'优惠券折扣费用',
                    'money'=>$dprice,
                    'time'=>time(),
                );
                pdo_insert("weisrc_dish_cost_jilu",$yun);
                pdo_update($this->table_cost,array('total_price'=>$yun_ying['total_price']-$dprice),array('weid'=>$weid)); 
                //echo "5";exit();
            }
        }
        if($zhekou>0){//增加记录，减少运营费
            if($zhekou>$yun_ying['total_price']){//运营费不足抵扣（发邮件）
                $title = "您平台运营费小于会员抵扣费用，不足抵扣，请您及时充值";
                $this->doEmail($title);
            }else{
                $yun = array(
                    'weid'=>$weid,
                    'orderid'=>$dingdan['ordersn'],
                    'title'=>'会员折扣费用',
                    'money'=>$zhekou,
                    'time'=>time(),
                );
                pdo_insert("weisrc_dish_cost_jilu",$yun);
                pdo_update($this->table_cost,array('total_price'=>$yun_ying['total_price']-$zhekou),array('weid'=>$weid)); 
            }
        }
        if($jifen_dk>0){//增加记录，减少运营费
            if($jifen_dk>$yun_ying['total_price']){//运营费不足抵扣（发邮件）
                $title = "您平台运营费小于积分抵扣费用，不足抵扣，请您及时充值";
                $this->doEmail($title);
            }else{
                $yun = array(
                    'weid'=>$weid,
                    'orderid'=>$dingdan['ordersn'],
                    'title'=>'积分折扣费用',
                    'money'=>$jifen_dk,
                    'time'=>time(),
                );
                pdo_insert("weisrc_dish_cost_jilu",$yun);
                pdo_update($this->table_cost,array('total_price'=>$yun_ying['total_price']-$jifen_dk),array('weid'=>$weid)); 
            }
        }



        $this->addOrderLog($id, $_W['user']['username'], 2, 2, 4);
        $this->updateFansData($order['from_user']);
        $this->updateFansFirstStore($order['from_user'], $order['storeid']);
        $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id=:id AND weid=:weid LIMIT 1", array(':id' => $id, ':weid' => $this->_weid));
        $this->sendOrderNotice($order, $cur_store, $setting);
        message('订单操作成功！', referer(), 'success');
    }

    if (!empty($_GPC['confirm'])) {
        if ($order['status'] == -1 || $order['ispay'] == 3) {
            message('取消和已退款订单不允许操作！', referer(), 'success');
        }
        pdo_update($this->table_order, array('status' => 1, 'confirmtime' => TIMESTAMP), array('id' => $id, 'weid' => $weid));
        pdo_update($this->table_service_log, array('status' => 1), array('orderid' => $id));
        $this->addOrderLog($id, $_W['user']['username'], 2, 2, 3);
        $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id=:id AND weid=:weid LIMIT 1", array(':id' => $id, ':weid' => $this->_weid));
        $this->doDada($weid,$id,$storeid);
        $this->sendOrderNotice($order, $store, $setting);
        message('确认订单操作成功！', referer(), 'success');
    }
    if (!empty($_GPC['cancel'])) {
        $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id=:id AND weid=:weid LIMIT 1", array(':id' => $id, ':weid' => $this->_weid));
        //將商品庫存加回來
        if($order['status']!=-1){
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
        }
        if ($order['ispay'] == 1 || $order['ispay'] == 2 || $order['ispay'] == 4) { //已支付和待退款的可以退款
            $refund_price = $order['totalprice'];
            $store = $this->getStoreById($order['storeid']);
            if ($order['paytype'] == 2) { //微信支付
                if ($cur_store['is_jxkj_unipay'] == 1) { //万融收银
                    $result = $this->refund4($id, $storeid);
                } else if ($cur_store['is_jueqi_ymf'] == 1) { //崛起支付
                    $result = $this->refund3($id, $storeid);
                } else {
                    //关单时判断是否退过款
                    if($order['refund_price']>0){
                        $result = $this->refund2($id, $refund_price,$order['origin_totalprice']);
                    }else{
                        $result = $this->refund2($id, $refund_price);
                    }
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
//                        $updateRealMoney=['real_price' =>$v['real_price'] + $v['real_tmp_price'] ];
                        $updateRealMoney=['real_price' =>$v['real_price'] + $v['real_tmp_price'],'single_real_price'=>($v['real_price'] + $v['real_tmp_price'])/$v['total'] ];
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
        pdo_update($this->table_order, array('status' => -1), array('id' => $id, 'weid' => $weid));
        $this->addOrderLog($id, $_W['user']['username'], 2, 2, 5);
        $this->sendOrderNotice($order, $store, $setting);
        $this->cancelfengniao($order, $store, $setting);
        message('订单关闭操作成功！', referer(), 'success');
    }
    if (!empty($_GPC['open'])) {
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
                    //加上销量
                    $todaySales = $v['today_counts']+ $v['total'];
                    $todaySales = $todaySales<=0?0:$todaySales;
                    $sales = (($v['sales'] + $v['total'])<=0)?0:($v['sales'] + $v['total']);
                    $update=['today_counts' =>$todaySales,'sales'=>$sales];
                    pdo_update("weisrc_dish_goods",$update,array('id'=>$v['goodsid']));
                }
            }
        }
        pdo_update($this->table_order, array('status' => 0), array('id' => $id, 'weid' => $weid));
        $this->addOrderLog($id, $_W['user']['username'], 2, 2, 8);
        message('开启订单操作成功！', referer(), 'success');
    }

    $item = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id = :id", array(':id' => $id));
    $goods = pdo_fetchall("SELECT a.goodsid,a.price, a.real_price,a.is_return, b.credit, a.total,b.thumb,b.title,b.id ,b.pcate,a.optionname FROM " . tablename($this->table_order_goods) . " a INNER JOIN " . tablename($this->table_goods) . " b ON a.goodsid=b.id WHERE a.orderid = :id and a.is_return = 0", array(':id' => $id));
    $discount = pdo_fetchall("SELECT * FROM " . tablename($this->table_category) . " WHERE weid=:weid and storeid=:storeid", array(":weid" => $weid,":storeid"=>$storeid));
    if ($item['dining_mode'] == 1 || $item['dining_mode'] == 3) {
        $tablesid = intval($item['tables']);
        $table = pdo_fetch("SELECT * FROM " . tablename($this->table_tables) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $tablesid));
        if (!empty($table)) {
            $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $table['tablezonesid']));
            $table_title = $tablezones['title'] . '-' . $table['title'];
        }
    }

    if ($item['couponid'] != 0) {
        $coupon = pdo_fetch("SELECT a.* FROM " . tablename($this->table_coupon) . "
        a INNER JOIN " . tablename($this->table_sncode) . " b ON a.id=b.couponid
 WHERE a.weid = :weid AND b.id=:couponid ORDER BY b.id
 DESC LIMIT 1", array(':weid' => $weid, ':couponid' => $item['couponid']));
        if (!empty($coupon)) {
            if ($coupon['type'] == 2) {
                $coupon_info = "抵用金额" . $order['discount_money'];
            } else {
                $coupon_info = $coupon['title'];
            }
        }
    }
} else if ($operation == 'print') {
    $id = $_GPC['id'];//订单id
    $flag = false;

    $prints = pdo_fetchall("SELECT * FROM " . tablename($this->table_print_setting) . " WHERE weid = :weid AND storeid=:storeid", array(':weid' => $_W['uniacid'], ':storeid' => $storeid));

    if (empty($prints)) {
        message('请先添加打印机或者开启打印机！');
    }

    foreach ($prints as $key => $value) {
        if ($value['print_status'] == 1 && $value['type'] == 'hongxin') {
            $data = array(
                'weid' => $_W['uniacid'],
                'orderid' => $id,
                'print_usr' => $value['print_usr'],
                'print_status' => -1,
                'dateline' => TIMESTAMP
            );
            pdo_insert('weisrc_dish_print_order', $data);
        }
    }
    $this->feieSendFreeMessage($id);
    $this->feiyinSendFreeMessage($id);
    $this->_365SendFreeMessage($id);
    $this->_yilianyunSendFreeMessage($id);
    message('操作成功！', $this->createWebUrl('order', array('op' => 'display', 'storeid' => $storeid)), 'success');
} elseif ($operation == 'printall') {
    $position_type = intval($_GPC['position_type']);
    $rowcount = 0;
    $notrowcount = 0;
    foreach ($_GPC['idArr'] as $k => $id) {
        $id = intval($id);
        if (!empty($id)) {
            $order = $this->getOrderById($id);
            $prints = pdo_fetchall("SELECT * FROM " . tablename($this->table_print_setting) . " WHERE weid = :weid AND storeid=:storeid", array(':weid' => $weid, ':storeid' => $order['storeid']));

            if (empty($prints)) {
                $notrowcount++;
                continue;
            }

            foreach ($prints as $key => $value) {
                if ($value['print_status'] == 1 && $value['type'] == 'hongxin') {
                    $data = array(
                        'weid' => $weid,
                        'orderid' => $id,
                        'print_usr' => $value['print_usr'],
                        'print_status' => -1,
                        'dateline' => TIMESTAMP
                    );
                    pdo_insert($this->table_print_order, $data);
                }
            }
            $this->feieSendFreeMessage($id, $position_type);
            $this->feiyinSendFreeMessage($id, $position_type);
            $this->_365SendFreeMessage($id , $position_type);
            $this->_yilianyunSendFreeMessage($id, $position_type);
            $rowcount++;
        }
    }
    $this->message("操作成功！", '', 0);
} elseif ($operation == 'payall') {
    $rowcount = 0;
    $notrowcount = 0;
    $paytype = intval($_GPC['paytype']);
    foreach ($_GPC['idArr3'] as $k => $id) {
        $id = intval($id);
        if (!empty($id)) {
            $order = $this->getOrderById($id);
            if ($order) {
                if ($order['status'] == -1 || $order['ispay'] == 3) {
                    continue;
                }
                if ($order['paytype'] != 0) {
                    continue;
                }
                if ($paytype == 3 || $paytype == 10 || $paytype == 11) {
                    $paytype = $paytype;
                } else {
                    $paytype = 0;
                }
                pdo_update($this->table_order, array('ispay' => 1, 'paytime' => TIMESTAMP, 'paytype' => $paytype), array('id' => $id,
                    'weid' => $weid));
                $this->addOrderLog($id, $_W['user']['username'], 2, 2, 2);
                $rowcount++;
            }
        }
    }
    $this->message("操作成功,共操作{$rowcount}条数据!", '', 0);
} elseif ($operation == 'confirmall') {

    $rowcount = 0;
    $notrowcount = 0;

    foreach ($_GPC['idArr4'] as $k => $id) {
        $id = intval($id);
        if (!empty($id)) {
            $order = $this->getOrderById($id);
            if ($order) {
//                 if ($order['status'] == -1 || $order['ispay'] == 3) {
//                     continue;
//                 }
                 if ($order['status'] == -1 ) {
                     continue;
                 }
//                 //修改訂單為確認
                pdo_update($this->table_order, array('status' => 1, 'confirmtime' => TIMESTAMP), array('id' => $id, 'weid' => $weid));
//                //將對應訂單提醒修改為，已確認
                pdo_update($this->table_service_log, array('status' => 1), array('orderid' => $id));
//                //如果是配置了达达进行调用
                $storesInfo = pdo_fetch("select id,is_dada,shop_no,source_id from ".tablename('weisrc_dish_stores')." where id=:id limit 1",array(":id"=>$storeid));
                if($storesInfo['is_dada']==1 &&  !empty($storesInfo['shop_no']) && !empty($storesInfo['source_id'])  ){
                    //新增達達配送狀態
                    $dadares =$this->doDada($weid,$id,$storeid);
                    if($dadares=='success'){
                        $msg ="订单已推送给达达";
                        pdo_update($this->table_order, array('delivery_status' => 1), array('id' => $id, 'weid' => $weid));
                    }else{
                        $msg ="订单推达达失败，联系管理员,请先自己配送";
                        pdo_update($this->table_order, array('delivery_status' => 3), array('id' => $id, 'weid' => $weid));

                    }
                //商家自配
                }else{
                    //
                    pdo_update($this->table_order, array('delivery_status' => 3), array('id' => $id, 'weid' => $weid));
                }
                $this->addOrderLog($id, $_W['user']['username'], 2, 2, 3);
                $rowcount++;
            }
        }
    }

    $this->message("操作成功,共操作{$rowcount}条数据!{$msg}", '', 0);

} elseif ($operation == 'cancelall') {
    $rowcount = 0;
    $notrowcount = 0;
    foreach ($_GPC['idArr6'] as $k => $id) {
        $id = intval($id);
        if (!empty($id)) {
            $order = $this->getOrderById($id);
            if ($order) {
                //將商品庫存加回來
                if($order['status']!=-1){
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
                }

                pdo_update($this->table_order, array('status' => -1), array('id' => $id, 'weid' => $weid));
                $this->addOrderLog($id, $_W['user']['username'], 2, 2, 5);
                $order = $this->getOrderById($id);
                $this->sendOrderNotice($order, $store, $setting);
                $this->cancelfengniao($order, $store, $setting);
                $rowcount++;
            }
        }
    }
    $this->message("操作成功,共操作{$rowcount}条数据!", '', 0);
} elseif ($operation == 'finishall') {
    $rowcount = 0;
    $notrowcount = 0;
    foreach ($_GPC['idArr5'] as $k => $id) {
        $id = intval($id);
        if (!empty($id)) {
            $order = $this->getOrderById($id);
            if ($order) {
                if ($order['isfinish'] == 0) {
                    if ($order['status'] == -1 || $order['ispay'] == 3) {
                        continue;
                    }
                    //计算积分
                    $this->setOrderCredit($order['id']);
                    pdo_update($this->table_order, array('isfinish' => 1), array('id' => $id));
                    //点击完成就完成
                    pdo_update($this->table_order, array('delivery_status' => 2), array('id' => $id, 'weid' => $weid));
                    pdo_update($this->table_service_log, array('status' => 1), array('orderid' => $id));
                    pdo_update($this->table_fans, array('paytime' => TIMESTAMP), array('id' => $fans['id']));
                    if ($order['dining_mode'] == 1) {
                        pdo_update($this->table_tables, array('status' => 0), array('id' => $order['tables']));
                    }
                    $this->set_commission($id);

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
                pdo_update($this->table_order, array('status' => 3, 'finishtime' => TIMESTAMP), array('id' => $id, 'weid' => $weid));
                $this->addOrderLog($id, $_W['user']['username'], 2, 2, 4);
                $this->updateFansData($order['from_user']);
                $this->updateFansFirstStore($order['from_user'], $order['storeid']);
                $order = $this->getOrderById($id);
                $store = $this->getStoreById($order['storeid']);
                $this->sendOrderNotice($order, $store, $setting);
                $rowcount++;
            }
        }
    }
    $this->message("操作成功,共操作{$rowcount}条数据!", '', 0);
} elseif ($operation == 'noticeall') {
    $rowcount = 0;
    $notrowcount = 0;
    foreach ($_GPC['idArr7'] as $k => $id) {
        $id = intval($id);
        if (!empty($id)) {
            $order = $this->getOrderById($id);
            $store = $this->getStoreById($order['storeid']);
            if ($order) {
                $this->sendOrderNotice($order, $store, $setting);
                $rowcount++;
            }
        }
    }
    $this->message("操作成功,共操作{$rowcount}条数据!", '', 0);
} elseif ($operation == 'refund') {
    if ($setting['is_operator_pwd'] == 1) {
        $operator_pwd = trim($_GPC['operator_pwd']);
        if (empty($operator_pwd)) {
            message('请输入操作密码!');
        }
        if ($setting['operator_pwd'] != $operator_pwd) {
            message('操作密码错误，请重新输入!');
        }
    }

    $url = $this->createWebUrl('order', array('op' => 'display', 'storeid' => $storeid));
    $id = $_GPC['id'];
    $order = $this->getOrderById($id);
    $store = $this->getStoreById($order['storeid']);
    if (empty($order)) {
        message('订单不存在！', '', 'error');
    }
    // if (!$this->exists()) {
    //     message('退款失败!!！', $url, 'error');
    // }
    //开始分摊金额  is_return 表示商品未退的进行分摊。
//         $refund_price=3.75;
//      $order['totalprice']= 0.5;
//    $refund_price = floatval($_GPC['refund_price']);
//    $ordergoodsList = pdo_fetchall("select *,total*price as moneyrate from ".tablename('weisrc_dish_order_goods')." where is_return=0  and  orderid=:orderid order by moneyrate desc ",array(':orderid'=>$id) );
//    $totalRealPrice = 0;
//    $totalPrice_total = array_sum(array_column($ordergoodsList,'moneyrate'));
//    foreach ($ordergoodsList as $k=>$v){
//        //  $ordergoodsList[$k]['real_price']=  floor($v['price']*$v['total']/$order['totalprice'] * $refund_price *100)/100;
//        $ordergoodsList[$k]['real_tmp_price']=  number_format($v['price']*$v['total']/$totalPrice_total * $refund_price,2) ;
//        $totalRealPrice+= $ordergoodsList[$k]['real_tmp_price'];
//        p($ordergoodsList[$k]['real_tmp_price']);
//    }
//    $errorMoney = ($refund_price*100 - $totalRealPrice*100)/100 ;
//    $ordergoodsList[0]['real_tmp_price'] =($ordergoodsList[0]['real_tmp_price']*100+ $errorMoney*100)/100;
////    p($errorMoney);
//    foreach ($ordergoodsList as $k=>$v){
//      $updateRealMoney=['real_price' =>$v['real_price'] + $v['real_tmp_price'],'single_real_price'=>($v['real_price'] + $v['real_tmp_price'])/$v['total'] ];
//        p($updateRealMoney);
//        //pdo_update($this->table_order_goods,$updateRealMoney,array('id'=>$v['id']));
//    }
//    die;
    //分摊结束
    //不是取消的订单做个退款判断
    if($order['status']!=-1) {
        $refund_price = floatval($_GPC['refund_price']);
        $after_total = ($order['totalprice'] * 100 - $refund_price * 100) / 100;
        if($store['store_type']==1){
            $bjRes = bccomp($after_total,$store['sendingprice'],2);
            if($store['store_type']==1 && $store['is_delivery_distance']==1  &&  $store['sendingprice'] && $bjRes==-1   ){
                message('退款后订单低于配送价格不支持退款！', $url, 'error');
                exit;
            }
        }elseif ($store['store_type']==3){
            if(  $store['sendingprice'] && $after_total < $store['sendingprice']){
                message('退款后订单低于配送价格不支持退款！', $url, 'error');
                exit;
            }
        }
    }

    $this->addOrderLog($id, $_W['user']['username'], 2, 2, 6);
    if ($order['ispay'] == 1 || $order['ispay'] == 2 || $order['ispay'] == 4) { //已支付和待退款的可以退款
        $refund_price = floatval($_GPC['refund_price']);

        if(bccomp($order['totalprice'],$order['origin_totalprice'],2)!=0 ){
          $origin_totalprice = $order['origin_totalprice'];
        }else{
            $origin_totalprice =$order['totalprice'] ;
        }
        //退款金额的比较
        $refundBjRes = bccomp($refund_price+$order['refund_price'],$origin_totalprice,2);
        $refundBjRes1 = bccomp($refund_price,$origin_totalprice,2);
        if ($refundBjRes1==1 || $refundBjRes==1  ) {
            message('退款金额不能大于订单金额1！', $url, 'error');
        }
        if ($order['paytype'] == 2) { //微信支付
            if ($cur_store['is_jxkj_unipay'] == 1) { //万融收银
                $result = $this->refund4($id, $storeid);
            } else if ($cur_store['is_jueqi_ymf'] == 1) { //崛起支付
                $result = $this->refund3($id, $storeid);
            } else {
                $result = $this->refund2($id, $refund_price,$order['origin_totalprice']);
            }
            if ($result == 1) {
                //开始分摊金额  is_return 表示商品未退的进行分摊。
//         $refund_price=3.75;
                //  $order['totalprice']= 0.5;
                $ordergoodsList = pdo_fetchall("select *,total*price as moneyrate from ".tablename('weisrc_dish_order_goods')." where is_return=0  and  orderid=:orderid order by moneyrate desc ",array(':orderid'=>$id) );
                $totalRealPrice = 0;
                $totalPrice_total = array_sum(array_column($ordergoodsList,'moneyrate'));
                foreach ($ordergoodsList as $k=>$v){
                    //  $ordergoodsList[$k]['real_price']=  floor($v['price']*$v['total']/$order['totalprice'] * $refund_price *100)/100;
                    $ordergoodsList[$k]['real_tmp_price']=  number_format($v['price']*$v['total']/$totalPrice_total * $refund_price,2) ;
                    $totalRealPrice+= $ordergoodsList[$k]['real_tmp_price'];
                }
//    p($totalRealPrice);
                $errorMoney = ($refund_price*100 - $totalRealPrice*100)/100 ;
                $ordergoodsList[0]['real_tmp_price'] =($ordergoodsList[0]['real_tmp_price']*100+ $errorMoney*100)/100;
//    p($errorMoney);
                foreach ($ordergoodsList as $k=>$v){
                    $updateRealMoney=['real_price' =>$v['real_price'] + $v['real_tmp_price'],'single_real_price'=>($v['real_price'] + $v['real_tmp_price'])/$v['total'] ];
                    pdo_update($this->table_order_goods,$updateRealMoney,array('id'=>$v['id']));
                }
//    p($updateRealMoney);
//    die;
                //分摊结束
                //将订单总价减少 更新订单金额
                $order_totalprice = ($order['totalprice'] - $refund_price)>=0?$order['totalprice'] - $refund_price:0;
                pdo_update($this->table_order, array('totalprice' => $order_totalprice), array('weid' =>
                    $this->_weid, 'id' => $id));
                $order["refund_price1"] = $refund_price;
                $order["ispay"] = 3;//为了初始化订单退款推送状态
                $this->sendOrderNotice($order, $store, $setting);
                message('退款成功！', $url, 'success');
            } else {
                message( $result, $url, 'error');
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
    } else {
        message('操作失败！', '', 'error');
    }
} elseif ($operation == 'mergeall') {
    $rowcount = 0;
    $notrowcount = 0;
    //1.判断店内订单，未支付的订单;
    //2.以最初的单子为主，生成新订单，合并金额
    //3.先判断商品是否存在，存在的话增加数量

    //订单金额
    $totalprice = 0;
    $totalnum = 0;
    $goodsprice = 0;
    $teavalue = 0;
    $service_money = 0;
    $discount_money = 0;
    $counts = 0;
    $sid = 0;
    foreach ($_GPC['idArr8'] as $k => $id) {
        $id = intval($id);
        if (!empty($id)) {
            $order = $this->getOrderById($id);
            if ($order) {
                if ($sid != 0 && $sid != $order['storeid']) {
                    $this->message("不同门店的订单不允许合并!", '', 0);
                }
                if ($order['dining_mode'] != 1) {
                    $this->message("您设置的订单编号{$order['id']}不是店内订单", '', 0);
                }
                if ($order['ismerge'] == 1) {
                    $this->message("您设置的订单编号{$order['id']}是并单，不能合并", '', 0);
                }
                $totalprice = $totalprice + floatval($order['totalprice']);
                $totalnum = $totalnum + intval($order['totalnum']);
                $counts = $counts + intval($order['counts']);
                $goodsprice = $goodsprice + floatval($order['goodsprice']);
                $teavalue = $teavalue + floatval($order['tea_money']);
                $service_money = $service_money + floatval($order['service_money']);
                $discount_money = $discount_money + floatval($order['discount_money']);
                $sid = $order['storeid'];
            }
        }
    }

    $data = array(
        'weid' => $weid,
        'from_user' => $order['from_user'],
        'storeid' => $order['storeid'],
        'couponid' => $order['couponid'],
        'discount_money' => $discount_money,
        'ordersn' => date('Ymd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99)),
        'totalnum' => $totalnum, //产品数量
        'totalprice' => $totalprice, //总价
        'goodsprice' => $goodsprice,
        'tea_money' => $teavalue,
        'service_money' => $service_money,
        'dispatchprice' => 0,
        'packvalue' => 0,
        'paytype' => 3, //付款类型
        'username' => $order['username'],
        'tel' => $order['tel'],
        'meal_time' => '',
        'counts' => $counts,
        'seat_type' => '',
        'tables' => $order['tables'],
        'tablezonesid' => $order['tablezonesid'],
        'carports' => 0,
        'dining_mode' => 1, //订单类型
        'remark' => $order['remark'], //备注
        'address' => $order['address'], //地址
        'status' => 0, //状态
        'rechargeid' => 0,
        'lat' => 0,
        'lng' => 0,
        'isvip' => $order['isvip'],
        'isfinish' => 1,
        'ismerge' => 1,
        'is_append' => 0,
        'dateline' => TIMESTAMP
    );

    //保存订单
    pdo_insert($this->table_order, $data);
    $neworderid = pdo_insertid();

    foreach ($_GPC['idArr'] as $k => $id) {
        $id = intval($id);
        if (!empty($id)) {
            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_order_goods) . " WHERE orderid = :orderid", array(':orderid' => $id));
            foreach ($list as $key => $val) {
                $goodsid = intval($val['goodsid']);
                $order_goods = pdo_fetch("SELECT * FROM " . tablename($this->table_order_goods) . " WHERE orderid = :orderid AND goodsid=:goodsid", array(':orderid' => $neworderid, ':goodsid' => $goodsid));
                if ($order_goods) {
                    $goodsprice = floatval($order_goods['price']);
                    $goodstotal = intval($order_goods['total']);
                    $goodstotal = intval($val['total']) + $goodstotal;

                    pdo_update($this->table_order_goods, array(
                        'weid' => $_W['uniacid'],
                        'storeid' => $val['storeid'],
                        'goodsid' => $val['goodsid'],
                        'orderid' => $neworderid,
                        'price' => $goodsprice,
                        'total' => $goodstotal,
                        'dateline' => TIMESTAMP,
                    ), array('id' => $order_goods['id']));
                } else {
                    $goodsprice = floatval($val['price']);
                    $goodstotal = intval($val['total']);

                    pdo_insert($this->table_order_goods, array(
                        'weid' => $_W['uniacid'],
                        'storeid' => $val['storeid'],
                        'goodsid' => $val['goodsid'],
                        'orderid' => $neworderid,
                        'price' => $goodsprice,
                        'total' => $goodstotal,
                        'dateline' => TIMESTAMP,
                    ));
                }
            }
        }
    }
    $this->message("操作成功!", '', 0);
}elseif($operation == 'sendfengniao') {
    $id = intval($_GPC['id']);
    $order = $this->getOrderById($id);
    $this->sendfengniao($order, $cur_store, $setting);
    message("操作成功");
}elseif($operation == 'getcarrier') {
    $id = intval($_GPC['id']);
    $order = $this->getOrderById($id);
    $this->getcarrier($order, $cur_store, $setting);
}elseif($operation == 'complaint') {
    $id = intval($_GPC['id']);
    $order = $this->getOrderById($id);
    $this->complaintfengniao($order, $cur_store, $setting);
}elseif($operation == "logistics"){
    //添加修改物流号
    $id = intval($_GPC['order_id']);
    $logistics_number = $_GPC['shipment_number'];
    $order = $this->getOrderById($id);
    if (empty($order)) {
        message('订单不存在！', '', 'error');
    }
    pdo_update($this->table_order, array('logistics_number' => $logistics_number), array('id' => $id));

    $url = $this->createWebUrl('order', array('op' => 'display', 'storeid' => $storeid));
    //邮寄店修改物流单号推送信息
    if (!empty($logistics_number)){
        $order['logistics_number'] = $logistics_number;
        $this->sendOrderNotice($order, $cur_store, $setting);
    }
    message('录入成功！', $url, 'success');
}

include $this->template('web/order');