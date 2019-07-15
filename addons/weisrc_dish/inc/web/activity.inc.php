<?php

global $_W, $_GPC;
$weid = $this->_weid;
$setting = $this->getSetting();
load()->func('tpl');
$action = 'activity';
$title = $this->actions_titles[$action];
$storeid = intval($_GPC['storeid']);
$returnid = $this->checkPermission($storeid);
$GLOBALS['frames'] = $this->getMainMenu();

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
    $condition = " a.weid=:weid";
    if (!empty($_GPC['keyword'])) {
        $types = trim($_GPC['types']);
        $condition .= " AND {$types} LIKE '%{$_GPC['keyword']}%'";
    }
    $condition .= " AND a.deleted = 0";
    $pindex = max(1, intval($_GPC['page']));
    $psize = 8;

    $start = ($pindex - 1) * $psize;
    $limit = "";
    $limit .= " LIMIT {$start},{$psize}";
    $sql = "select a.id,a.activityprice,a.counts,a.startdate,a.enddate,b.title,b.productprice from
            ".tablename($this->table_goods_activity)."as a left join
            " .tablename('weisrc_dish_goods')." as  b on  b.id=a.goodsid  where  {$condition} ORDER BY updatetime DESC,id DESC " . $limit;
    $list = pdo_fetchall($sql,  array(':weid' => $weid));

    //$total = pdo_fetchcolumn("SELECT count(1) FROM " . tablename($this->table_goods_activity) . " WHERE {$condition} ", array(':weid' => $weid));

    $pager = pagination($total, $pindex, $psize);
}elseif ($operation == 'post'){
    $id = intval($_GPC['id']);
    $sql = "select a.id,a.activityprice,a.counts,a.startdate,a.enddate,b.title,a.goodsid,b.productprice from
            ".tablename($this->table_goods_activity)."as a left join
            " .tablename('weisrc_dish_goods')." as  b on  b.id=a.goodsid  where  a.id = {$id}";
    $goods = pdo_fetch($sql);
    $productprice = $goods['productprice'];
    $activityprice = $goods['activityprice'];
    $counts = $goods['counts'];
    $goodslist = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . " WHERE deleted=0 AND status=1 ORDER BY
    displayorder DESC,id DESC");
    if (checksubmit('submit')) {
        $data = array(
            'goodsid' => 12,
            'weid' => intval($_W['uniacid']),
            'storeid' => 0,
            'counts' => intval($_W['counts']),
            'activityprice' => floatval($_GPC['activityprice']),
            'startdate' => strtotime($_GPC['datelimit']['start']),
            'enddate' => strtotime($_GPC['datelimit']['end']),
        );
        if (!empty($id)) {
            pdo_update($this->table_goods_activity, $data, array('id' => $id, 'weid' => $_W['uniacid']));
        } else {
            pdo_insert($this->table_goods_activity, $data);

        }
        message('操作成功!', $this->createWebUrl('activity', array('op' => 'display')));
    }
}elseif ($operation == 'delete'){
    $id = intval($_GPC['id']);
    $item = pdo_fetch("SELECT id FROM " . tablename($this->table_goods_activity) . " WHERE id = :id AND weid=:weid", array(':id' => $id, ':weid' => $weid));
    if (empty($item)) {
        message('抱歉，不存在或是已经被删除！', $this->createWebUrl('activity', array('op' => 'display', 'storeid' => $storeid)), 'error');
    }
    pdo_query("UPDATE " . tablename($this->table_goods_activity) . " SET deleted = 1 WHERE id=:id", array( ':id' => $id));
    message('删除成功！', $this->createWebUrl('activity', array('op' => 'display')), 'success');
}
include $this->template('web/activity');