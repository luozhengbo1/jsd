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
//    echo "<pre>";
//    print_r($distancelist);die;
    if (checksubmit('submit')) {
        $distanceids = array(0);
//        echo "<pre>";
        if (is_array($_GPC['begindistance'])) {
            foreach ($_GPC['begindistance'] as $oid => $val) {
                $begindistance = floatval($_GPC['begindistance'][$oid]);
                $enddistance = floatval($_GPC['enddistance'][$oid]);
                $dispatchprice = floatval($_GPC['dispatchprices'][$oid]);
                $data = array(
                    'weid' => $weid,
                    'begindistance' => $begindistance,
                    'enddistance' => $enddistance,
                    'dispatchprice' => $dispatchprice,
                );
                pdo_update($this->table_distance_pt, $data, array('id' => $_GPC['id'][$oid]));
                $distanceids[] = $oid;
            }
        }

        if (is_array($_GPC['newbegindistance'])) {
            foreach ($_GPC['newbegindistance'] as $nid => $val) {
                $begindistance = floatval($_GPC['newbegindistance'][$nid]);
                $enddistance = floatval($_GPC['newenddistance'][$nid]);
                $dispatchprice = floatval($_GPC['newdispatchprices'][$nid]);
                if (empty($enddistance)) {
                    continue;
                }
                if ($enddistance <= $begindistance) {
                    continue;
                }
                $data = array(
                    'weid' => $weid,
                    'begindistance' => $begindistance,
                    'enddistance' => $enddistance,
                    'dispatchprice' => $dispatchprice,
                    'dateline' => TIMESTAMP
                );
                pdo_insert($this->table_distance_pt, $data);
                $did = pdo_insertid();
                $distanceids[] = $did;
            }
        }
//            echo "<pre>";
//            print_r($data);die;

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