<?php
global $_W, $_GPC;
$weid = $this->_weid;
$from_user = $this->_fromuser;
$id = intval($_GPC['goodsid']);
$savewineid = intval($_GPC['savewineid']);
$total = intval($_GPC['total']);

if (empty($from_user)) {
    $this->showMsg('请先登录');
}

$order = pdo_fetch("SELECT * FROM " . tablename($this->table_savewine_log) . " WHERE id ={$savewineid} AND from_user='{$from_user}' ORDER BY
id DESC LIMIT 1");

if (empty($order)) {
    $this->showMsg('没有存酒记录');
}

if ($id == 0) {
    //全部取出
    //未过期的

    pdo_query("UPDATE ".tablename('weisrc_dish_savewine_goods')." SET status=:status,takeouttime=:takeouttime WHERE savewineid =:savewineid AND takeouttime=0 AND (savetime=0 OR (savetime>0 AND savetime>:savetime))",
        array(
            ':status' => -1,
            ':savewineid' => $savewineid,
            ':takeouttime' => TIMESTAMP,
            ':savetime' => TIMESTAMP,
        )
    );

    pdo_query("UPDATE ".tablename('weisrc_dish_savewine_log')." SET status=:status,takeouttime=:takeouttime WHERE id
    =:id AND status=1",
        array(

            ':id' => $savewineid,
            ':takeouttime' => TIMESTAMP,
            ':status' => -1,

        )
    );

    $data = array(
        'weid' => $weid,
        'storeid' => $order['storeid'],
        'savewineid' => $savewineid,
        'goodsid' => 0,
        'total' => 0,
        'dateline' => TIMESTAMP
    );
    pdo_insert("weisrc_dish_savewine_record", $data);
} else {
    $goods = pdo_fetch("SELECT * FROM " . tablename("weisrc_dish_savewine_goods") . " WHERE id ={$id} LIMIT 1");
    if ($goods) {
        $data = array(
            'weid' => $weid,
            'storeid' => $order['storeid'],
            'savewineid' => $savewineid,
            'goodsid' => $goods['goodsid'],
            'total' => $total,
            'dateline' => TIMESTAMP
        );
        pdo_insert("weisrc_dish_savewine_record", $data);
    }
    if ($total >= $goods['total']) {
        pdo_update("weisrc_dish_savewine_goods", array('status' => -1, 'takeouttime' => TIMESTAMP), array('id' => $id));
    } else {
        pdo_update("weisrc_dish_savewine_goods", array('total' => intval($goods['total']) - $total, 'takeouttime' => TIMESTAMP), array('id' => $id));
    }
}
$this->showMsg('操作成功', 1);







