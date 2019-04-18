<?php

/*
 * 人人商城
 *
 * 青岛易联互动网络科技有限公司
 * http://www.we7shop.cn
 * TEL: 4000097827/18661772381/15865546761
 */
if (!defined('IN_IA')) {
    exit('Access Denied');
}

class Bank_EweiShopV2Page extends WebPage {

    function main() {

        global $_W, $_GPC;

        $condition = " and uniacid=:uniacid";
        $params = array(':uniacid' => $_W['uniacid']);

        $list = pdo_fetchall("SELECT * FROM " . tablename('ewei_shop_commission_bank') . " WHERE 1 {$condition}  ORDER BY displayorder DESC", $params);
        $total = pdo_fetchcolumn("SELECT count(1) FROM " . tablename('ewei_shop_commission_bank') . " WHERE 1 {$condition}", $params);


        include $this->template();
    }

    function add() {
        $this->post();
    }

    function edit() {
        $this->post();
    }

    protected function post() {

        global $_W, $_GPC;
        $id = intval($_GPC['id']);

        if ($_W['ispost']) {

            $_GPC['bankname'] = trim($_GPC['bankname']);
            $_GPC['status'] = intval($_GPC['status']);

            if (empty($_GPC['bankname'])) {
                show_json(0,"请输入银行名称");
            }

            $data = array();
            $data['uniacid'] = $_W['uniacid'];
            $data['bankname'] = $_GPC['bankname'];
            $data['status'] = $_GPC['status'];

            if (!empty($id)) {
                pdo_update('ewei_shop_commission_bank', $data, array('id' => $id));
            } else {
                pdo_insert('ewei_shop_commission_bank', $data);
                $id = pdo_insertid();
            }
            show_json(1);
        }
        //修改
        $item = pdo_fetch("SELECT * FROM " . tablename('ewei_shop_commission_bank') . " WHERE id = '$id' and uniacid = '{$_W['uniacid']}'");

        include $this->template();
    }

    function delete() {

        global $_W, $_GPC;

        $id = intval($_GPC['id']);
        if (empty($id)) {
            $id = is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0;
        }
        $items = pdo_fetchall("SELECT id FROM " . tablename('ewei_shop_commission_bank') . " WHERE id in( $id ) AND uniacid=" . $_W['uniacid']);
        foreach ($items as $item) {
            pdo_delete('ewei_shop_commission_bank', array('id' => $item['id']));
        }
        show_json(1, array('url' => referer()));
    }

    function status() {

        global $_W, $_GPC;
        $id = intval($_GPC['id']);
        if (empty($id)) {
            $id = is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0;
        }
        $items = pdo_fetchall("SELECT id FROM " . tablename('ewei_shop_commission_bank') . " WHERE id in( $id ) AND uniacid=" . $_W['uniacid']);

        foreach ($items as $item) {
            pdo_update('ewei_shop_commission_bank', array('status' => intval($_GPC['status'])), array('id' => $item['id']));
        }
        show_json(1, array('url' => referer()));
    }

    function displayorder() {

        global $_W, $_GPC;
        $id = intval($_GPC['id']);

        $displayorder = intval($_GPC['value']);
        $item = pdo_fetchall("SELECT id FROM " . tablename('ewei_shop_commission_bank') . " WHERE id in( $id ) AND uniacid=" . $_W['uniacid']);

        if (!empty($item)) {
            pdo_update('ewei_shop_commission_bank', array('displayorder' => $displayorder), array('id' => $id));
        }
        show_json(1);
    }

}
