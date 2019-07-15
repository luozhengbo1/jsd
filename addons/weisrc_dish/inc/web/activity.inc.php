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
    $time =TIMESTAMP;
    if ($_GPC['status'] == 1){
        $condition .= " AND a.startdate >{$time}";
    }elseif ($_GPC['status'] == 2){
        $condition .= " AND (a.startdate <={$time} AND {$time} < a.enddate )";
    }elseif ($_GPC['status'] == 3){
        $condition .= " AND a.enddate < {$time}";
    }
    if ($_GPC['status']){
        $status = $_GPC['status'];
    }
    if ($_GPC['goods_name']){
        $select_goods = $_GPC['goods_name'];
    }
    if ($_GPC['goods_name']){
        $condition .= " AND a.goodsid = {$_GPC['goods_name']}";
    }
    $condition .= " AND a.deleted = 0";
    $pindex = max(1, intval($_GPC['page']));
    $psize = 8;

    $goods_name_sql = "select distinct a.goodsid,b.title from
            ".tablename($this->table_goods_activity)."as a left join
            " .tablename('weisrc_dish_goods')." as  b on  b.id=a.goodsid  where  a.weid=:weid ORDER BY updatetime DESC,a.id DESC ";
    $goods_name = pdo_fetchall($goods_name_sql,  array(':weid' => $weid));


    $start = ($pindex - 1) * $psize;
    $limit = "";
    $limit .= " LIMIT {$start},{$psize}";
    $sql = "select a.id,a.activityprice,a.counts,a.startdate,a.enddate,b.title,b.productprice,b.marketprice from
            ".tablename($this->table_goods_activity)."as a left join
            " .tablename('weisrc_dish_goods')." as  b on  b.id=a.goodsid  where  {$condition} ORDER BY updatetime DESC,id DESC " . $limit;
    $list = pdo_fetchall($sql,  array(':weid' => $weid));

    $total = pdo_fetchcolumn("SELECT count(1) FROM " . tablename($this->table_goods_activity) . "as a WHERE {$condition} ", array(':weid' => $weid));

    $pager = pagination($total, $pindex, $psize);
}elseif ($operation == 'post'){
    $id = intval($_GPC['id']);
    if ($id){
        $sql = "select a.id,a.activityprice,a.counts,a.startdate,a.enddate,b.title,a.goodsid,b.productprice,b.storeid,b.marketprice from
            ".tablename($this->table_goods_activity)."as a left join
            " .tablename('weisrc_dish_goods')." as  b on  b.id=a.goodsid  where  a.id = {$id}";
        $goods = pdo_fetch($sql);
        $goodsid = $goods["goodsid"];
        $storeid = $goods["storeid"];
        $marketprice = $goods['marketprice'];
        $activityprice = $goods['activityprice'];
        $counts = $goods['counts'];
        $goodslist = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . " WHERE deleted=0 AND status=1 AND storeid={$storeid}");
    }
    $where_store = "WHERE weid = {$weid} AND deleted=0";
    $storeslist = pdo_fetchall("SELECT * FROM " . tablename($this->table_stores) . " {$where_store} order by displayorder desc,id desc");
    if (checksubmit('submit')) {
        $data = array(
            'goodsid' => intval($_GPC['goods']),
            'weid' => intval($_W['uniacid']),
            'storeid' => 0,
            'counts' => intval($_W['counts']),
            'activityprice' => floatval($_GPC['activityprice']),
            'startdate' => strtotime($_GPC['datelimit']['start']),
            'enddate' => strtotime($_GPC['datelimit']['end']),
        );

        //控制一个商品只能在一个时间段参加活动，不能重叠
        $sql_activity = "select * from " . tablename($this->table_goods_activity) . "as c where c.deleted = 0 and c.weid=:weid and  not (c.`startdate`>{$data["enddate"]} or c.`enddate`<{$data["startdate"]})";
        $activity  = pdo_fetchall($sql_activity,  array(':weid' => $weid));
        if (count($activity) > 1){
            message('该商品在该时间段内已经设置', '', 'error');
        }
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
}elseif($operation == "getgoods"){
    global $_W, $_GPC;
    $storeid = $_GPC["storeid"];
    $weid = $this->_weid;
    if ($storeid !=''){
        $where = "AND storeid  = {$storeid} AND deleted=0 AND status=1";
        $goodslist = pdo_fetchall("SELECT title,id FROM " . tablename($this->table_goods) . " WHERE weid=:weid {$where} ORDER BY displayorder DESC,id DESC", array(':weid' => $weid), 'id');
    }
    $data["msg"] = "提示";
    $data["data"] = $goodslist;
    $result = array('data' =>$data, 'status' => 1);
    echo json_encode($result);
    exit();
}elseif($operation == "getgoodsprice"){
    global $_W, $_GPC;
    $goodsid = $_GPC["goodsid"];
    $weid = $this->_weid;
    $where = "AND id  = {$goodsid} AND deleted=0 AND status=1";
    $goodslist = pdo_fetchall("SELECT title,id,marketprice FROM " . tablename($this->table_goods) . " WHERE weid=:weid {$where} ORDER BY displayorder DESC,id DESC", array(':weid' => $weid), 'id');
    $data["msg"] = "提示";
    $data["data"] = array(
        "marketprice" =>  $goodslist[0]["marketprice"]
    );
    $result = array('data' =>$data, 'status' => 1);
    echo json_encode($result);
    exit();
}
include $this->template('web/activity');