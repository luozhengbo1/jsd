<?php
global $_GPC, $_W;
$weid = $this->_weid;
$GLOBALS['frames'] = $this->getMainMenu();

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
    if (!empty($_GPC['displayorder'])) {
        foreach ($_GPC['displayorder'] as $id => $displayorder) {
            pdo_update($this->table_money, array('displayorder' => $displayorder), array('id' => $id));
        }
        foreach ($_GPC['url'] as $id => $url) {
            pdo_update($this->table_money, array('url' => $url), array('id' => $id));
        }
        message('门店类型排序更新成功！', $this->createWebUrl('type', array('op' => 'display')), 'success');
    }

    $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_money) . " WHERE weid = :weid", array(':weid' => $weid));
  
} elseif ($operation == 'post') {
    load()->func('tpl');
    $parentid = intval($_GPC['parentid']);
    $id = intval($_GPC['id']);
    if (!empty($id)) {
        $type = pdo_fetch("SELECT * FROM " . tablename($this->table_money) . " WHERE id = '$id'");
    } else {
        $type = array(
            'displayorder' => 0,
        );
    }

    if (checksubmit('submit')) {

        $data = array(
            'weid' => $weid,
            'limit'=>$_GPC['limit'],
            'nickname' => $_GPC['nickname'],
            'limit_jifen'=>$_GPC['limit_jifen'],
            'limit_discount'=>$_GPC['limit_discount'],
            'limit_coupon'=>$_GPC['limit_coupon'],
            'sort'=>$_GPC['sort'],
            'money_limit'=>$_GPC['money_limit'],
            'minus'=>$_GPC['minus'],
        );

        if (!empty($id)) {
            

            pdo_update($this->table_money, $data, array('id' => $id));
        } else {
            pdo_insert($this->table_money, $data);
        }
        message('会员更新成功 ', $this->createWebUrl('money', array('op' => 'display')), 'success');
    }
} elseif ($operation == 'delete') {
    $id = intval($_GPC['id']);
    $type = pdo_fetch("SELECT id FROM " . tablename($this->table_money) . " WHERE id = '$id'");
    if (empty($type)) {
        message('抱歉，数据不存在或是已经被删除！', $this->createWebUrl('type', array('op' => 'display')), 'error');
    }
    pdo_delete($this->table_money, array('id' => $id, 'weid' => $weid));
    message('数据删除成功！', $this->createWebUrl('money', array('op' => 'display')), 'success');
}
include $this->template('web/money');