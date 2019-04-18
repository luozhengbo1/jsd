<?php
global $_W, $_GPC;
$weid = $this->_weid;
$from_user = $this->_fromuser;
$setting = $this->getSetting();
$type = isset($_GPC['type']) ? intval($_GPC['type']) : 1;

if ($type == 1) {
    $strwhere = " AND a.status=0 AND " . TIMESTAMP . "<b.endtime ";
} elseif ($type == 2) {
    $strwhere = " AND a.status=1 ";
} elseif ($type == 3) {
    $strwhere = " AND a.status=0 AND " . TIMESTAMP . ">b.endtime ";
}
$couponlist = pdo_fetchall("SELECT a.id as ids,a.couponid,a.weid,a.from_user,a.status,b.* FROM".tablename('weisrc_dish_sncode')."as a left join ".tablename('weisrc_dish_coupons')." as b on a.couponid = b.id where a.weid = :weid and a.from_user =:from_user {$strwhere} ",array(':weid'=>$weid,':from_user'=>$from_user));
//$couponlist = pdo_fetchall("SELECT a.*,b.sncode FROM " . tablename($this->table_coupon) . " a INNER JOIN " .tablename($this->table_sncode) ." b ON a.id = b.couponid
// WHERE a.weid = :weid AND b.from_user=:from_user {$strwhere} ORDER BY b.id DESC
//LIMIT 30", array(':weid' => $weid, ':from_user' => $from_user));

$storelist = pdo_fetchall("SELECT id,title FROM " . tablename($this->table_stores) . " WHERE weid=:weid ORDER BY id DESC ", array(':weid' => $weid), 'id');

include $this->template($this->cur_tpl . '/mycoupon');