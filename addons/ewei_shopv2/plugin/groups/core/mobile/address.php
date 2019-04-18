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

class Address_EweiShopV2Page extends PluginMobileLoginPage {
	function main() {
		global $_W, $_GPC, $_S;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = ' and openid=:openid and deleted=0 and  `uniacid` = :uniacid  ';
		$params = array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']);
		$sql = 'SELECT COUNT(*) FROM ' . tablename('ewei_shop_member_address') . " where 1 $condition";
		$total = pdo_fetchcolumn($sql, $params);
		$sql = 'SELECT * FROM ' . tablename('ewei_shop_member_address') . ' where 1 ' . $condition . ' ORDER BY `id` DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
		$list = pdo_fetchall($sql, $params);
		include $this->template();
	}

	//地址编辑，已弃用，走商城的地址编辑
	function post() {
		global $_W, $_GPC;
		$id = intval($_GPC['id']);
		$address = pdo_fetch('select * from ' . tablename('ewei_shop_member_address') . ' where id=:id and openid=:openid and uniacid=:uniacid limit 1 ', array(':id' => $id, ':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']));
		$shareaddress_config = false;
		//新地址库
        $area_set = m('util')->get_area_config_set();
        $new_area = intval($area_set['new_area']);
        $address_street = intval($area_set['address_street']);

		if ($_W['shopset']['trade']['shareaddress'] && is_weixin()) {
            /*$account = WeAccount::create();
           if (method_exists($account, "getShareAddressConfig")) {
               $shareaddress_config = $account->getShareAddressConfig();
           }*/
		}
        $show_data = 1;
        if((!empty($new_area) && empty($address['datavalue'])) || (empty($new_area) && !empty($address['datavalue']))) {
            $show_data = 0;
        }
		include $this->template();
	}

	function setdefault() {

		global $_W, $_GPC;
		$id = intval($_GPC['id']);
		$data = pdo_fetch('select id from ' . tablename('ewei_shop_member_address') . ' where id=:id and deleted=0 and uniacid=:uniacid limit 1', array(
			':uniacid' => $_W['uniacid'],
			':id' => $id
		));
		if (empty($data)) {
			show_json(0, '地址未找到');
		}
		pdo_update('ewei_shop_member_address', array('isdefault' => 0), array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid']));
		pdo_update('ewei_shop_member_address', array('isdefault' => 1), array('id' => $id, 'uniacid' => $_W['uniacid'], 'openid' => $_W['openid']));
		show_json(1);
	}

	function submit() {
		global $_W, $_GPC;

		$id = intval($_GPC['id']);
		$data = $_GPC['addressdata'];
		$areas = explode(' ', $data['areas']);
		$data['province'] = $areas[0];
		$data['city'] = $areas[1];
		$data['area'] = $areas[2];
		unset($data['areas']);
		$data['openid'] = $_W['openid'];
		$data['uniacid'] = $_W['uniacid'];
		if (empty($id)) {
			$addresscount = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('ewei_shop_member_address') . ' where openid=:openid and deleted=0 and `uniacid` = :uniacid ', array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']));
			if ($addresscount <= 0) {
				$data['isdefault'] = 1;
			}
			pdo_insert('ewei_shop_member_address', $data);
			$id = pdo_insertid();
		} else {
			pdo_update('ewei_shop_member_address', $data, array('id' => $id, 'uniacid' => $_W['uniacid'], 'openid' => $_W['openid']));
		}
		show_json(1, array('addressid' => $id));
	}

	function delete() {
		global $_W, $_GPC;

		$id = intval($_GPC['id']);
		$data = pdo_fetch('select id,isdefault from ' . tablename('ewei_shop_member_address') . ' where  id=:id and openid=:openid and deleted=0 and uniacid=:uniacid  limit 1', array(
			':uniacid' => $_W['uniacid'],
			':openid' => $_W['openid'],
			':id' => $id
		));
		if (empty($data)) {
			show_json(0, '地址未找到');
		}
		pdo_update('ewei_shop_member_address', array('deleted' => 1), array('id' => $id));

		//如果删除默认地址
		if ($data['isdefault'] == 1) {
			//将最近添加的地址设置成默认的
			pdo_update('ewei_shop_member_address', array('isdefault' => 0), array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid'], 'id' => $id));
			$data2 = pdo_fetch('select id from ' . tablename('ewei_shop_member_address') . ' where openid=:openid and deleted=0 and uniacid=:uniacid order by id desc limit 1', array(
				':uniacid' => $_W['uniacid'],
				':openid' => $_W['openid']
			));
			if (!empty($data2)) {
				pdo_update('ewei_shop_member_address', array('isdefault' => 1), array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid'], 'id' => $data2['id']));
				show_json(1, array('defaultid' => $data2['id']));
			}
		}
		show_json(1);
	}

	function selector() {
		global $_W, $_GPC;
		$condition = ' and openid=:openid and deleted=0 and  `uniacid` = :uniacid  ';
		$params = array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']);
		$sql = 'SELECT * FROM ' . tablename('ewei_shop_member_address') . ' where 1 ' . $condition . ' ORDER BY isdefault desc, id DESC ';
		$list = pdo_fetchall($sql, $params);
		include $this->template();
		exit;
	}

}
