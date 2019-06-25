<?php
global $_W, $_GPC;
$weid = $this->_weid;
$from_user = $this->_fromuser;
$star = intval($_GPC['star']);
$orderid = intval($_GPC['orderid']);
$content = trim($_GPC['content']);

if ($orderid == 0) {
    $this->showMsg('订单不存在');
}

$order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE weid = :weid  AND from_user=:from_user AND id=:id ORDER BY `id`
DESC limit 1", array(':weid' => $weid, ':from_user' => $from_user, ':id' => $orderid));
if (empty($order)) {
    $this->showMsg('订单不存在');
}
if ($order['isfeedback'] == 1) {
    $this->showMsg('订单已经评论过');
}

$data = array(
    'weid' => $weid,
    'orderid' => $orderid,
    'from_user' => $from_user,
    'storeid' => $order['storeid'],
    'star' => $star,
    'content' => $content,
    'status' => 1,
    'dateline' => TIMESTAMP
);
pdo_insert($this->table_feedback, $data);
pdo_update($this->table_order, array('isfeedback' => 1), array('id' => $orderid));
//查询判断是否设置积分
$jifen = pdo_fetch("SELECT * FROM".tablename('weisrc_dish_money')."where weid =:weid and from_user=:from_user order by id desc LIMIT 1",array(':weid'=>$weid,':from_user'=>$order['from_user']));
if(!empty($jifen)){
    //查询会员积分
    $user = mc_fetch($from_user);//不能实时获取最新数据
    //根据id查询最新数据
    $member_jifen = pdo_fetch("SELECT * FROM".tablename('mc_members')." where uid =:uid",array(':uid'=>$user['uid']));
    $credit1 = $member_jifen['credit1'];//积分
    pdo_update("mc_members",array('credit1'=>$credit1+$jifen['limit_coupon']),array('uid'=>$user['uid']));//下单增加积分
}
$this->showMsg('评论成功!', 1);