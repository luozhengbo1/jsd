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
} else {
    //已选队列
    $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id=:id AND from_user=:from_user  ORDER BY id DESC LIMIT 1", array(':id' => $id, ':from_user' => $from_user));
    if($order['ispay']==3){
        $this->showMsg('订单已退款不能撤销!');
    }
//    //將商品庫存加回來
    $sql = "select a.total,a.goodsid,b.isoptions,a.optionid,b.counts,b.today_counts,b.sales,a.dateline from
            ".tablename('weisrc_dish_order_goods')."as a left join
            " .tablename('weisrc_dish_goods')." as  b on  b.id=a.goodsid  where a.orderid=:orderid ";
    $goodsList = pdo_fetchall($sql,array(':orderid'=>$id));

    if(!empty($goodsList) && is_array($goodsList)){
        $today_start = strtotime(date('Y-m-d 00:00:00'));
        $today_end = strtotime(date('Y-m-d 23:59:59'));
        foreach ($goodsList as $k=>$v){
            //判斷商品是否啓用規格
            if(  $v['dateline']>=$today_start && $v['dateline']<=$today_end   ){
                //加上销量
                $todaySales = $v['today_counts']+$v['total'];
                $todaySales = $todaySales<=0?0:$todaySales;
                $sales = ($v['sales'] +$v['total']);
                $update=['today_counts' =>$todaySales,'sales'=>$sales];
                pdo_update("weisrc_dish_goods",$update,array('id'=>$v['goodsid']));
            }
        }
    }
    $fansickanme = pdo_getcolumn($this->table_fans,array('from_user'=>$order['from_user']),'nickname');
    pdo_update($this->table_order, array('status' => 0), array('id' => $id));
    $this->addOrderLog($id, $fansickanme, 1, 1, 12);
    $this->showMsg('撤销订单成功！');
}
$this->showMsg('操作成功!!!', 1);