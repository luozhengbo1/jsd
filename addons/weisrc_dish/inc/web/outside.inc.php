<?php
global $_GPC, $_W;
$weid = $this->_weid;
$GLOBALS['frames'] = $this->getMainMenu();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
    if (!empty($_GPC['displayorder'])) {
        foreach ($_GPC['displayorder'] as $id => $displayorder) {
            pdo_update($this->table_distance_pt, array('displayorder' => $displayorder), array('id' => $id));
        }
        foreach ($_GPC['url'] as $id => $url) {
            pdo_update($this->table_distance_pt, array('url' => $url), array('id' => $id));
        }
        message('会员更新成功！', $this->createWebUrl('member', array('op' => 'display')), 'success');
    }

    $distancelist = pdo_fetchall("SELECT * FROM " . tablename($this->table_distance_pt) . " WHERE weid = :weid ORDER BY id ASC", array(':weid' => $weid));
    load()->func('tpl');
    $parentid = intval($_GPC['parentid']);
    $id = intval($_GPC['id']);
    if (!empty($id)) {
        $type = pdo_fetch("SELECT * FROM " . tablename($this->table_distance_pt) . " WHERE id = '$id'");
    } else {
        $type = array(
            'displayorder' => 0,
        );
    }
    if (checksubmit('submit')) {
        $data = array(
            'weid' => $weid,
            'begindistance'=>$_GPC['begindistance'],
            'enddistance' => $_GPC['enddistance'],
            'dispatchprice'=>$_GPC['dispatchprice'],
        );

        $distancelist = pdo_insert($this->table_distance_pt, $data);
        message('设置成功 ', $this->createWebUrl('outside', array('op' => 'display')), 'success');
    }
} elseif ($operation == 'delete') {
    $id = intval($_GPC['id']);
    $distancelist = pdo_fetch("SELECT id FROM " . tablename($this->table_distance_pt) . " WHERE id ='".$id."'");
    if (empty($distancelist)) {
        message('抱歉，数据不存在或是已经被删除！', $this->createWebUrl('outside', array('op' => 'display')), 'error');
    }
    pdo_delete($this->table_distance_pt, array('id' => $id, 'weid' => $weid));
    message('数据删除成功！', $this->createWebUrl('outside', array('op' => 'display')), 'success');
    //新增此方法获取数据来判断
}elseif($operation == 'ajaxgetdata'){
    $ajaxdata = pdo_fetchall("SELECT id,begindistance,enddistance,dispatchprice FROM " . tablename($this->table_distance_pt) );
    exit(json_encode($ajaxdata));
}
include $this->template('web/outside');
?>