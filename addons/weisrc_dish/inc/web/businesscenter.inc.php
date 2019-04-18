<?php
global $_GPC, $_W;
$weid = $this->_weid;
$action = 'businesscenter';
$title = $this->actions_titles[$action];

$storeid = intval($_GPC['storeid']);
$setting = $this->getSetting();
$is_contain_delivery = intval($setting['is_contain_delivery']);
//最低提现金额
$getcash_price = intval($setting['getcash_price']);
$fee_rate = floatval($setting['fee_rate']);
$fee_min = intval($setting['fee_min']);
$fee_max = intval($setting['fee_max']);

$this->checkStore($storeid);
$returnid = $this->checkPermission($storeid);
$store = $this->getStoreById($storeid);

if ($store['is_default_rate'] == 2) {
    $getcash_price = intval($store['getcash_price']);
    $fee_rate = floatval($store['fee_rate']);
    $fee_min = intval($store['fee_min']);
    $fee_max = intval($store['fee_max']);
}

$GLOBALS['frames'] = $this->getNaveMenu($storeid, $action);
if (!empty($store['business_openid'])) {
    $fans = $this->getFansByOpenid($store['business_openid']);
}

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
    $pindex = max(1, intval($_GPC['page']));
    $psize = 10;

    $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_businesslog) . " WHERE weid = :weid AND storeid=:storeid ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ",{$psize}", array(':weid' => $weid, ':storeid' => $storeid));

    if (!empty($list)) {
        $total = pdo_fetchcolumn("SELECT count(1) FROM " . tablename($this->table_businesslog) . " WHERE weid = :weid AND storeid=:storeid  ", array(':weid' => $weid, ':storeid' => $storeid));
        $pager = pagination($total, $pindex, $psize);
    }

    $order_totalprice = $this->getStoreOrderTotalPrice($storeid, $is_contain_delivery);
    //已申请
    $totalprice1 = $this->getStoreOutTotalPrice($storeid);
    //未申请
    $totalprice2 = $this->getStoreGetTotalPrice($storeid);
    $totalprice = $order_totalprice - $totalprice1 - $totalprice2;
    $totalprice = sprintf('%.2f', $totalprice);

} else if ($operation == 'post') {
    //店鋪id 開啓了距離配送費
    $order_totalprice = $this->getStoreOrderTotalPrice($storeid, $is_contain_delivery);
    //已申请
    $totalprice1 = $this->getStoreOutTotalPrice($storeid);
    //未申请
    $totalprice2 = $this->getStoreGetTotalPrice($storeid);

    $totalprice = $order_totalprice - $totalprice1 - $totalprice2;
    $totalprice = sprintf('%.2f', $totalprice);

    if (checksubmit('submit')) {
        if ($store['business_type'] == 0) {
            message('请先选择提现的账号', $this->createWebUrl('businesssetting', array('storeid' => $storeid)), 'error');
        }
        $price = floatval($_GPC['price']);
        if ($totalprice <= 0) {
            message('已没有足够的余额可提现！');
        }
        if ($getcash_price != 0) {
            if ($price < $getcash_price) {
                message('最低的提现金额为' . $getcash_price);
            }
        } else {
            if ($price < 1) {
                message('最低的提现金额为1元！');
            }
        }
        if ($price > $totalprice) {
            message('可提现余额只有' . $totalprice);
        }
        if ($price > 20000) {
            message('每次提现不能大于20000！');
        }
        $charges = 0;
        if ($fee_rate > 0) {
            $charges = $price * $fee_rate / 100;
        }
        if ($fee_min > 0) {
            if ($charges < $fee_min) {
                $charges = $fee_min;
            }
        }
        if ($fee_max > 0) {
            if ($charges > $fee_max) {
                $charges = $fee_max;
            }
        }

        $successprice = $price - $charges;
        $data = array(
            'weid' => $weid,
            'storeid' => $storeid,
            'uid' => $_W['user']['uid'],
            'price' => $price,
            'dining_mode' => 0,
            'business_type' => intval($store['business_type']),
            'charges' => $charges,
            'successprice' => $successprice,
            'haveprice' => $totalprice1,
            'totalprice' => $order_totalprice,
            'status' => 0,
            'dateline' => TIMESTAMP
        );

        pdo_insert($this->table_businesslog, $data);

        $this->sendApplyNotice($setting, $store, $price);
        message('操作成功', $this->createWebUrl('businesscenter', array('storeid' => $storeid)), 'success');
    }
}

include $this->template('web/businesscenter');