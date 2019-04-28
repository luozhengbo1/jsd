<?php
global $_W, $_GPC;
$weid = $this->_weid;
$from_user = $this->_fromuser;
$setting = $this->getSetting();

$method = 'adminstore'; //method
$host = $this->getOAuthHost();
$authurl = $host . 'app/' . $this->createMobileUrl($method, array('typeid' => $typeid, 'areaid' => $areaid), true) . '&authkey=1';
$url = $host . 'app/' . $this->createMobileUrl($method, array('typeid' => $typeid, 'areaid' => $areaid), true);
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

$fans = $this->getFansByOpenid($from_user);
if (empty($fans)) {
    $this->addFans($nickname, $headimgurl);
} else {
    $this->updateFans($nickname, $headimgurl, $fans['id']);
}
$fans = $this->getFansByOpenid($from_user);
if ($fans['status'] == 0) {
    die('系统调试中！');
}

if (empty($from_user)) {
    message('会话已过期，请重新发送关键字!');
}

$is_permission = false;
$is_all = false;
$tousers = explode(',', $setting['tpluser']);
if (in_array($from_user, $tousers)) {
    $is_all = true;
    $is_permission = true;
}
$storeid = 0;
if ($is_permission == false) {
    $accounts = pdo_fetchall("SELECT storeid FROM " . tablename($this->table_account) . " WHERE weid = :weid AND from_user=:from_user AND
 status=2 AND is_admin_order=1 ORDER BY id DESC ", array(':weid' => $this->_weid, ':from_user' => $from_user));
    if ($accounts) {
        $arr = array();
        foreach ($accounts as $key => $val) {
            $arr[] = $val['storeid'];
            $storeid = $val['storeid'];
        }
        $storeids = implode(',', $arr);
        $is_permission = true;
    }
}
if ($is_permission == false) {
    message('对不起，您没有该功能的操作权限!');
}

$op = $_GPC['op'];
if ($op == 'setstatus') {
    $storeid = intval($_GPC['storeid']);
    $store = $this->getStoreById($storeid);
    pdo_update($this->table_stores, array('is_show' => 1 - intval($store['is_show'])), array('id' => $storeid));
    $jump_url = $this->createMobileUrl('adminstore', array(), true);
    header("location:$jump_url");
}

$pindex = max(1, intval($_GPC['page']));
$psize = $this->more_store_psize;
if ($is_all == true) {
    $strwhere = " where weid = :weid AND deleted=0 ";
} else {
    $strwhere = " where weid = :weid AND deleted=0 AND id in ('" . $storeids . "') ";
}

$limit = " LIMIT "  . ($pindex - 1) * $psize . ',' . $psize;
$this->resetHour();

$restlist = pdo_fetchall("SELECT * FROM " . tablename($this->table_stores) . " {$strwhere
} ORDER BY is_rest DESC,displayorder DESC, id DESC LIMIT 100", array(':weid' => $weid));
$setting = $this->getSetting();
$is_contain_delivery = intval($setting['is_contain_delivery']);
foreach($restlist as $key => $value) {
//    $totalprice = pdo_fetchcolumn("SELECT sum(totalprice) FROM " . tablename($this->table_order) . " WHERE weid = :weid AND storeid=:storeid AND ispay=1 AND ismerge=0 AND
//status=3 AND (paytype=1 OR paytype=2 OR paytype=4)", array(':weid' => $weid, ':storeid' => $value['id']));
//    $totalprice = floatval($totalprice);
//    //已申请
//    $totalprice1 = pdo_fetchcolumn("SELECT sum(price) FROM " . tablename($this->table_businesslog) . " WHERE weid = :weid AND storeid=:storeid AND status=1", array(':weid' => $weid, ':storeid' => $value['id']));
//    $totalprice1 = floatval($totalprice1);
//    //未申请
//    $totalprice2 = pdo_fetchcolumn("SELECT sum(price) FROM " . tablename($this->table_businesslog) . " WHERE weid = :weid AND storeid=:storeid AND status=0", array(':weid' => $weid, ':storeid' => $value['id']));
//    $totalprice2 = floatval($totalprice2);
//    $totalprice = $totalprice - $totalprice1 - $totalprice2;
    $storeid = intval($value['id']);
    $store = $this->getStoreById($storeid);
    $order_totalprice = $this->getStoreOrderTotalPrice($storeid, $is_contain_delivery);
    //已申请
    $totalprice1 = $this->getStoreOutTotalPrice($storeid);
    //未申请
    $totalprice2 = $this->getStoreGetTotalPrice($storeid);
    $totalprice = $order_totalprice - $totalprice1 - $totalprice2;
    $restlist[$key]['totalprice'] = $totalprice;
}
//p($restlist);die;

setcookie('global_sid_' . $weid,'',time()-1);
include $this->template($this->cur_tpl . '/adminstore');