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

class Nav_EweiShopV2Page extends WebPage {

	function main() {

		global $_W, $_GPC;

		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = " and uniacid=:uniacid and iswxapp=0 ";
		$params = array(':uniacid' => $_W['uniacid']);
		if ($_GPC['status'] != '') {
			$condition.=' and status=' . intval($_GPC['status']);
		}
		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition.=' and navname  like :keyword';
			$params[':keyword'] = "%{$_GPC['keyword']}%";
		}

		$list = pdo_fetchall("SELECT * FROM " . tablename('ewei_shop_nav') . " WHERE 1 {$condition}  ORDER BY displayorder DESC limit " . ($pindex - 1) * $psize . ',' . $psize, $params);
		$total = pdo_fetchcolumn("SELECT count(*) FROM " . tablename('ewei_shop_nav') . " WHERE 1 {$condition}", $params);
		$pager = pagination2($total, $pindex, $psize);
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
			$data = array(
				'uniacid' => $_W['uniacid'],
				'navname' => trim($_GPC['navname']),
				'url' => trim($_GPC['url']),
				'status' => intval($_GPC['status']),
				'displayorder' => intval($_GPC['displayorder']),
				'icon' => save_media($_GPC['icon'])
			);
			if (!empty($id)) {
				pdo_update('ewei_shop_nav', $data, array('id' => $id));
				plog('shop.nav.edit', "修改首页导航 ID: {$id}");
			} else {
				pdo_insert('ewei_shop_nav', $data);
				$id = pdo_insertid();
				plog('shop.nav.add', "添加首页导航 ID: {$id}");
			}
			// 更新静态首页
			m("common")->createStaticFile(mobileUrl('getpage',null, true), true);
			
			show_json(1, array("url" => webUrl('shop/nav')));
		}

		$item = pdo_fetch("select * from " . tablename('ewei_shop_nav') . " where id=:id and uniacid=:uniacid limit 1", array(":id" => $id, ":uniacid" => $_W['uniacid']));
		include $this->template();
	}

	function delete() {

		global $_W, $_GPC;
		$id = intval($_GPC['id']);
		if (empty($id)) {
			$id = is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0;
		}
		$items = pdo_fetchall("SELECT id,navname FROM " . tablename('ewei_shop_nav') . " WHERE id in( $id ) AND uniacid=" . $_W['uniacid'] );
		foreach ($items as $item) {
			pdo_delete('ewei_shop_nav', array('id' => $item['id']));
			plog('shop.nav.delete', "删除首页导航 ID: {$item['id']} 标题: {$item['navname']} ");
		}
		show_json(1, array('url' => referer()));
	}

	function displayorder() {

		global $_W, $_GPC;
		$id = intval($_GPC['id']);
		$displayorder = intval($_GPC['value']);
		$item = pdo_fetchall("SELECT id,navname FROM " . tablename('ewei_shop_nav') . " WHERE id in( $id ) AND uniacid=" . $_W['uniacid']);
		if (!empty($item)) {
			pdo_update('ewei_shop_nav', array('displayorder' => $displayorder), array('id' => $id));
			plog('shop.nav.edit', "修改首页导航排序 ID: {$item['id']} 标题: {$item['navname']} 排序: {$displayorder} ");
		}
		show_json(1);
	}

	function status() {

		global $_W, $_GPC;
		$id = intval($_GPC['id']);
		if (empty($id)) {
			$id = is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0;
		}
		$items = pdo_fetchall("SELECT id,navname FROM " . tablename('ewei_shop_nav') . " WHERE id in( $id ) AND uniacid=" . $_W['uniacid'] );

		foreach ($items as $item) {
			pdo_update('ewei_shop_nav', array('status' => intval($_GPC['status'])), array('id' => $item['id']));
			plog('shop.nav.edit', "修改首页导航状态<br/>ID: {$item['id']}<br/>标题: {$item['navname']}<br/>状态: " . $_GPC['status'] == 1 ? '显示' : '隐藏');
		}
		show_json(1, array('url' => referer()));
	}

}
