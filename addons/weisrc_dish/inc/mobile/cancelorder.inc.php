<?php
global $_W, $_GPC;
$weid = $this->_weid;
$from_user = $this->_fromuser;
$id = intval($_GPC['id']);
if (empty($from_user)) {
    $this->showMsg('请重新发送关键字进入系统!');
}
if ($id == 0) { //未选队列
    $this->showMsg('请先选择订单!');
} else { //已选队列
    //將商品庫存加回來
    $sql = "select a.total,a.goodsid,b.isoptions,a.optionid,b.counts,b.today_counts from
            ".tablename('weisrc_dish_order_goods')."as a left join
            " .tablename('weisrc_dish_goods')." as  b on  b.id=a.goodsid  where a.orderid=:orderid and b.counts<>-1";
    $goodsList = pdo_fetchall($sql,array(':orderid'=>$id));

    if(!empty($goodsList) && is_array($goodsList)){
        foreach ($goodsList as $k=>$v){
            //判斷商品是否啓用規格
            if($v['isoptions']!=1){
                $update=['today_counts' =>$v['today_counts']-$v['total']];
                pdo_update("weisrc_dish_goods",$update,$where['id']=$v['goodsid']);
            }
//            else{
                //啓用規格的商品 邏輯設計有bug 沒庫存
//            }

        }
    }
    $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id=:id AND from_user=:from_user AND status=0 ORDER BY id DESC LIMIT 1", array(':id' => $id, ':from_user' => $from_user));
    if (empty($order)) {
        $this->showMsg('订单不存在！');
    }
    $store = $this->getStoreById($order['storeid']);

    $coin = floatval($order['totalprice']);
    if ($order['paytype'] == 1 && $order['status'] != -1) {
        if ($order['ispay'] == 1) {
//            $coin = floatval($order['totalprice']);
//            $this->setFansCoin($order['from_user'], $coin, "码上点餐单号{$order['ordersn']}退款");
        }
    }
    if ($order['ispay'] == 1) {
        pdo_update($this->table_order, array('ispay' => 2), array('id' => $id));
        //將生成對應的語音提示信息
        if($order){
            $yytsres =  pdo_fetch('select id ,orderid  from '.tablename('weisrc_dish_service_log').' where orderid=:orderid  and ts_type=2 limit 1',array(':orderid'=>$order['orderid']));
            if(!$yytsres){
                pdo_insert("weisrc_dish_service_log",
                    array(
                        'orderid' => $order['id'],
                        'storeid' =>$order['storeid'] ,
                        'weid' => $order['weid'] ,
                        'from_user' => $order['from_user'],
                        'content' => '您有待退款的的订单，请尽快处理',
                        'dateline' => TIMESTAMP,
                        'status' => 0,
                        'ts_type'=>2,
                    )
                );
            }

        }
    }

    pdo_update($this->table_order, array('status' => -1), array('id' => $id));
    $this->feiyinSendFreeMessage($id);
    $this->_365SendFreeMessage($id);
    $this->feieSendFreeMessage($id);
    $this->_yilianyunSendFreeMessage($id);
    $setting = pdo_fetch("select * from " . tablename($this->table_setting) . " where weid =:weid LIMIT 1", array(':weid' => $weid));
//    file_put_contents(IA_ROOT . "/addons/weisrc_dish/canclefengniao.log", '1' . PHP_EOL, FILE_APPEND);
    $this->cancelfengniao($order, $store, $setting);
    if (!empty($setting)) {
        //平台提醒
        if ($setting['is_notice'] == 1) {
            if (!empty($setting['tpluser'])) {
                $tousers = explode(',', $setting['tpluser']);
                foreach ($tousers as $key => $value) {
                    $this->sendAdminOrderNotice($id, $value, $setting);
                }
            }
        }

        $storeid = intval($order['storeid']);
        //门店提醒
        $accounts = pdo_fetchall("SELECT * FROM " . tablename($this->table_account) . " WHERE weid = :weid AND storeid=:storeid AND status=2 ORDER BY id DESC ", array(':weid' => $weid, ':storeid' => $storeid));
        foreach ($accounts as $key => $value) {
            if (!empty($value['from_user'])) {
                $this->sendAdminOrderNotice($id, $value['from_user'], $setting);
            }
        }
    }

    $this->showMsg('取消订单成功!', 1);
}
$this->showMsg('操作成功!!!', 1);