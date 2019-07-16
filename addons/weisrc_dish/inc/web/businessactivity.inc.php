<?php

global $_W, $_GPC;
$weid = $this->_weid;
$setting = $this->getSetting();
load()->func('tpl');
$action = 'businessactivity';
$title = $this->actions_titles[$action];
$storeid = intval($_GPC['storeid']);
$this->checkStore($storeid);
$returnid = $this->checkPermission($storeid);
$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getNaveMenu($storeid,$action);

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
    if (!empty($storeid)){
        $condition .= " AND a.storeid = {$storeid}";
    }
    $condition .= " AND a.deleted = 0";
    $pindex = max(1, intval($_GPC['page']));
    $psize = 8;

    $goods_name_sql = "select distinct a.goodsid,b.title from
            ".tablename($this->table_goods_activity)."as a left join
            " .tablename('weisrc_dish_goods')." as  b on  b.id=a.goodsid  where  a.weid=:weid and a.storeid=:storeid ORDER BY updatetime DESC,a.id DESC ";
    $goods_name = pdo_fetchall($goods_name_sql,  array(':weid' => $weid, ':storeid' => $storeid));


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
        $marketprice = $goods['marketprice'];
        $activityprice = $goods['activityprice'];
        $counts = $goods['counts'];
    }
    $goodslist = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . " WHERE deleted=0 AND status=1 AND storeid={$storeid}");
    if (checksubmit('submit')) {
        $data = array(
            'goodsid' => intval($_GPC['goods']),
            'weid' => intval($_W['uniacid']),
            'storeid' => $storeid,
            'counts' => intval($_GPC['counts']),
            'activityprice' => floatval($_GPC['activityprice']),
            'startdate' => strtotime($_GPC['datelimit']['start']),
            'enddate' => strtotime($_GPC['datelimit']['end']),
        );
        if (empty($data['goodsid'])){
            message("请选择商品!");
        }
        if($data['counts']<=0 || empty($data['counts'])){
            message("请输入正确的限购数量!");
        }
        if($data['activityprice']<0 || empty($data['activityprice'])){
            message("限购价格格式不对!");
        }
        //折扣商品不能添加限购
        $pcate_sql = "select a.id,a.marketprice,b.rebate from
            ".tablename($this->table_goods)."as a left join
            " .tablename($this->table_category)." as  b on  b.id=a.pcate  where  a.id = {$data['goodsid']}";
        $pcate = pdo_fetch($pcate_sql);
        if ($pcate['rebate']<10 && $pcate){
            message('该商品属于折扣商品不能参加限购活动', '', 'error');
        }
        if ($data['activityprice'] > $pcate['marketprice']){
            message("限购价只能小于等于商品原价!");
        }
        //控制一个商品只能在一个时间段参加活动，不能重叠
        $sql_businessactivity = "select * from " . tablename($this->table_goods_activity) . "as c where c.deleted = 0 and c.goodsid = {$data['goodsid']} and c.weid=:weid and  not (c.`startdate`>{$data["enddate"]} or c.`enddate`<{$data["startdate"]})";
        $businessactivity  = pdo_fetchall($sql_businessactivity,  array(':weid' => $weid));
        if (!empty($id)) {
            if (count($businessactivity) > 1){
                message('该商品在该时间段内已有限购活动', '', 'error');
            }
            pdo_update($this->table_goods_activity, $data, array('id' => $id, 'weid' => $_W['uniacid']));
        } else {
            if (count($businessactivity) > 0){
                message('该商品在该时间段内已有限购活动', '', 'error');
            }
            pdo_insert($this->table_goods_activity, $data);
        }
        message('操作成功!', $this->createWebUrl('businessactivity', array('op' => 'display', 'storeid' => $storeid)));
    }
}elseif ($operation == 'delete'){
    $id = intval($_GPC['id']);
    $item = pdo_fetch("SELECT id FROM " . tablename($this->table_goods_activity) . " WHERE id = :id AND weid=:weid", array(':id' => $id, ':weid' => $weid));
    if (empty($item)) {
        message('抱歉，不存在或是已经被删除！', $this->createWebUrl('businessactivity', array('op' => 'display', 'storeid' => $storeid)), 'error');
    }
    pdo_query("UPDATE " . tablename($this->table_goods_activity) . " SET deleted = 1 WHERE id=:id", array( ':id' => $id));
    message('删除成功！', $this->createWebUrl('businessactivity', array('op' => 'display', 'storeid' => $storeid)), 'success');
}elseif($operation == "getgoodsprice"){
    global $_W, $_GPC;
    $goodsid = intval($_GPC["goodsid"]);
    $weid = $this->_weid;
    $where = "AND id  = {$goodsid} AND deleted=0 AND status=1";
    $goodslist = pdo_fetchall("SELECT title,id,marketprice FROM " . tablename($this->table_goods) . " WHERE weid=:weid {$where} ORDER BY displayorder DESC,id DESC", array(':weid' => $weid));
    $data["msg"] = "提示";
    $data["data"] = array(
        "marketprice" =>  $goodslist[0]["marketprice"]
    );
    $result = array('data' =>$data, 'status' => 1);
    echo json_encode($result);
    exit();
}
include $this->template('web/businessactivity');