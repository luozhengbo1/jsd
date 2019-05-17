<?php
global $_W, $_GPC;
$weid = $this->_weid;
$from_user = $this->_fromuser;
$setting = $this->getSetting();
$status = 0;
$type = isset($_GPC['type']) ? intval($_GPC['type']) : 1;

if (!empty($_GPC['status'])) {
    $status = intval($_GPC['status']);
}

$do = 'adminorder';
$method = 'adminorder'; //method
$host = $this->getOAuthHost();
$authurl = $host . 'app/' . $this->createMobileUrl($method, array(), true) . '&authkey=1';
$url = $host . 'app/' . $this->createMobileUrl($method, array(), true);
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
if (empty($from_user)) {
    message('会话已过期，请重新发送关键字!');
}

$is_permission = false;
$tousers = explode(',', $setting['tpluser']);
if (in_array($from_user, $tousers)) {
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
    $url_order = '../../app/' . $this->createMobileUrl('waprestlist');//更改URL跳转到店面
    message('对不起，您没有该功能的操作权限!', $url_order, 'error');
}

$storelist = pdo_fetchall("SELECT * FROM " . tablename($this->table_stores) . " WHERE weid=:weid ORDER BY id DESC ", array(':weid' => $weid), 'id');
if (empty($storeids)) {
    //已确认
    $order_list = pdo_fetchall("SELECT * FROM " . tablename($this->table_order) . " WHERE weid = '{$weid}' ORDER BY id DESC LIMIT 200");
    //数量
//    $order_total = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename($this->table_order) . " WHERE weid = '{$weid}' ORDER BY id DESC");
} else {
    //已确认
    $order_list = pdo_fetchall("SELECT * FROM " . tablename($this->table_order) . " WHERE weid = '{$weid}' AND storeid in ('" . $storeids . "') ORDER BY id DESC LIMIT 200");
    //数量
    $order_total = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename($this->table_order) . " WHERE weid={$weid} AND storeid in ('" . $storeids . "') ORDER BY id
DESC");
}

foreach ($order_list as $key => $value) {
    $storeid = intval($value['storeid']);
    $store = $this->getStoreById($storeid);
    $order_list[$key]['storename'] = $store['title'];

    if ($value['dining_mode'] == 1 || $value['dining_mode'] == 3) {
        $tablesid = intval($value['tables']);
        $table = pdo_fetch("SELECT * FROM " . tablename($this->table_tables) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $tablesid));
        if (!empty($table)) {
            $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $table['tablezonesid']));
            if (empty($tablezones)) {
                exit('餐桌类型不存在！');
            }
            $table_title = $tablezones['title'] . '-' . $table['title'];
            $order_list[$key]['table_title'] = $table_title;
        }
    }

}
foreach ($order_list as $k => $v){
    if ($type == 1) {
        if(!(($v['ispay'] == 1 && $v['status']==0)||($v['ispay'] == 2))){
            unset($order_list[$k]);
        }
    } elseif ($type == 2) {
        if(!($v['ispay'] == 1 && $v['status']==1)){
            unset($order_list[$k]);
        }
    } elseif ($type == 3) {
        if(!($v['ispay'] == 1 && $v['status']==3)){
            unset($order_list[$k]);
        }
    } elseif ($type == 4) {
        if(!($v['status'] == -1 && ($v['ispay']==0 || $v['ispay']==2 || $v['ispay']==3))){
            unset($order_list[$k]);
        }
    }
}
$setIsSpeak = pdo_fetch("select id,is_speaker,yy_ts_time from  ".tablename('weisrc_dish_setting')." limit 1");
include $this->template($this->cur_tpl . '/admin_order');