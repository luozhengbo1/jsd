<?php
global $_W, $_GPC;
$weid = $this->_weid;
$from_user = $this->_fromuser;
$setting = $this->getSetting();
$storeid = intval($_GPC['storeid']);

$method = 'admingoods'; //method
$host = $this->getOAuthHost();
$authurl = $host . 'app/' . $this->createMobileUrl($method, array('storeid' => $storeid), true) . '&authkey=1';
$url = $host . 'app/' . $this->createMobileUrl($method, array('storeid' => $storeid), true);
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

$store = $this->getStoreById($storeid);
if (empty($store)) {
    message('门店不存在!');
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

$op = $_GPC['op'];
if ($op == 'setstatus') {
    $id = intval($_GPC['id']);
    $goods = pdo_fetch("SELECT * FROM " . tablename($this->table_goods) . " WHERE id=:id", array(":id" => $id));
    pdo_update($this->table_goods, array('status' => 1 - intval($goods['status'])), array('id' => $id));
    $jump_url = $this->createMobileUrl('admingoods', array('storeid' => $storeid), true);
    header("location:$jump_url");
}

$strwhere = " where weid=:weid AND deleted=0 AND storeid=:storeid ";
$restlist = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . " {$strwhere
} ORDER BY displayorder DESC, id DESC ", array(':weid' => $weid, ':storeid' => $storeid));

include $this->template($this->cur_tpl . '/admingoods');