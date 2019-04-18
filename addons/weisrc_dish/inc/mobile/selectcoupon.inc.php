<?php
global $_W, $_GPC;
$weid = $this->_weid;
$from_user = $this->_fromuser;
$couponid = intval($_GPC['couponid']);
//var_dump('aaa'.$couponid);
$coupon = pdo_fetch("SELECT * FROM".tablename('weisrc_dish_sncode')."as a left join ".tablename('weisrc_dish_coupons')." as b on a.couponid = b.id where a.id = {$couponid} and a.weid = :weid and a.from_user =:from_user and a.status = 0",array(':weid'=>$weid,':from_user'=>$from_user));
//var_dump($coupon);
// //$mode = intval($_GPC['mode']);

// $strwhere = " WHERE a.weid = :weid AND b.from_user=:from_user AND b.status=0 AND :time<a.endtime AND b.id=:couponid ";


// $coupon = pdo_fetch("SELECT a.*,b.sncode,b.id AS couponid FROM " . tablename($this->table_coupon) . "
//         a INNER JOIN " . tablename($this->table_sncode) . " b ON a.id= b.couponid
//  {$strwhere} ORDER BY b.id DESC LIMIT 1", array(':weid' => $weid, ':from_user' => $from_user, ':time' => TIMESTAMP, ':couponid' => $couponid));

// if (empty($coupon)) {
//     $result['status'] = 0;
//     echo json_encode($result);
//     exit;
// }
// if ($coupon['type'] == 1) { //商品
//     $result['status'] = 0;
//     echo json_encode($result);
//     exit;
// }

$result['gmoney'] = $coupon['gmoney'];
$result['dprice'] = $coupon['dmoney'];
echo json_encode($result);
exit;