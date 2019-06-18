<?php
global $_GPC, $_W;
$weid = $this->_weid;
$GLOBALS['frames'] = $this->getMainMenu();

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
    if (!empty($_GPC['displayorder'])) {
        foreach ($_GPC['displayorder'] as $id => $displayorder) {
            pdo_update($this->table_member, array('displayorder' => $displayorder), array('id' => $id));
        }
        foreach ($_GPC['url'] as $id => $url) {
            pdo_update($this->table_member, array('url' => $url), array('id' => $id));
        }
        message('会员更新成功！', $this->createWebUrl('member', array('op' => 'display')), 'success');
    }

    $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_member) . " WHERE weid = :weid", array(':weid' => $weid));

} elseif ($operation == 'post') {
    load()->func('tpl');
    $parentid = intval($_GPC['parentid']);
    $id = intval($_GPC['id']);
    if (!empty($id)) {
        $type = pdo_fetch("SELECT * FROM " . tablename($this->table_member) . " WHERE id = '$id'");
    } else {
        $type = array(
            'displayorder' => 0,
        );
    }

    if (checksubmit('submit')) {
        //var_dump($_GPC['datelimit']);exit();
        $data = array(
            'weid' => $weid,
            'limit'=>$_GPC['limit'],
            'nickname' => $_GPC['nickname'],
            'limit_jifen'=>$_GPC['limit_jifen'],
            'limit_discount'=>$_GPC['limit_discount'],
            'limit_coupon'=>$_GPC['limit_coupon'],
            'sort'=>$_GPC['sort'],
            'starttime' => $_GPC['datelimit']['start'],
            'endtime' => $_GPC['datelimit']['end'],
        );
        if ($data["nickname"] == ''){
            message('请输入昵称！', $this->createWebUrl('member', array('op' => 'display')), 'error');
        }
        if (!is_numeric($data["limit"])|| $data["limit"] == ''){
            message('请输入正确充值额度！', $this->createWebUrl('member', array('op' => 'display')), 'error');
        }
        if (!is_numeric($data["limit_discount"]) || is_numeric($data["limit_discount"])  == ''){
            message('请输入正确订单折扣！', $this->createWebUrl('member', array('op' => 'display')), 'error');
        }
        if (!empty($id)) {
            pdo_update($this->table_member, $data, array('id' => $id));
        } else {
            pdo_insert($this->table_member, $data);
        }
        message('会员更新成功 ', $this->createWebUrl('member', array('op' => 'display')), 'success');
    }
} elseif ($operation == 'delete') {
    $id = intval($_GPC['id']);
    $type = pdo_fetch("SELECT id FROM " . tablename($this->table_member) . " WHERE id = '$id'");
    if (empty($type)) {
        message('抱歉，数据不存在或是已经被删除！', $this->createWebUrl('member', array('op' => 'display')), 'error');
    }
    pdo_delete($this->table_member, array('id' => $id, 'weid' => $weid));
    message('数据删除成功！', $this->createWebUrl('member', array('op' => 'display')), 'success');
}elseif($operation == 'seting'){
     load()->func('tpl');
    $parentid = intval($_GPC['parentid']);
    $id = intval($_GPC['id']);
    if (!empty($id)) {
        $type = pdo_fetch("SELECT * FROM " . tablename($this->table_members) . " WHERE id = '$id'");
    } else {
        $type = array(
            'displayorder' => 0,
        );
    }

    if (checksubmit('submit')) {

        $data = array(
            'weid' => $weid,
            'limit'=>$_GPC['limit'],
            'sill'=>$_GPC['sill'],
            'sill_limit'=>$_GPC['sill_limit'],
            'money_off'=>$_GPC['money_off'],
            'money_limit'=>$_GPC['money_limit'], 
            'minus'=>$_GPC['minus'],
            'nickname' => $_GPC['nickname'],
            'sort'=>$_GPC['sort'],  
             
        );

        if (!empty($id)) {
            
            
            pdo_update($this->table_members, $data, array('id' => $id));
        } else {
            pdo_insert($this->table_members, $data);
        }
        message('会员更新成功 ', $this->createWebUrl('member', array('op' => 'show')), 'success');
    }
}elseif($operation == 'show'){
    if (!empty($_GPC['displayorder'])) {
        foreach ($_GPC['displayorder'] as $id => $displayorder) {
            pdo_update($this->table_members, array('displayorder' => $displayorder), array('id' => $id));
        }
        foreach ($_GPC['url'] as $id => $url) {
            pdo_update($this->table_members, array('url' => $url), array('id' => $id));
        }
        message('会员更新成功！', $this->createWebUrl('member', array('op' => 'display')), 'success');
    }

    $lists = pdo_fetchall("SELECT * FROM " . tablename($this->table_members) . " WHERE weid = :weid", array(':weid' => $weid));
}elseif ($operation == 'deletes') {
    $id = intval($_GPC['id']);
    $type = pdo_fetch("SELECT id FROM " . tablename($this->table_members) . " WHERE id = '$id'");
    if (empty($type)) {
        message('抱歉，数据不存在或是已经被删除！', $this->createWebUrl('member', array('op' => 'display')), 'error');
    }
    pdo_delete($this->table_members, array('id' => $id, 'weid' => $weid));
    message('数据删除成功！', $this->createWebUrl('member', array('op' => 'show')), 'success');
}
include $this->template('web/member');