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

class Index_EweiShopV2Page extends SystemPage {

	function main() {
	
		global $_W,$_GPC;
		if ($_W['ispost']) {
			if (!empty($_GPC['displayorder'])) {
				foreach ($_GPC['displayorder'] as $id => $displayorder) {
					pdo_update('ewei_shop_plugin', array('status' => $_GPC['status'][$id],
						'displayorder' => $displayorder,
						'name' => $_GPC['name'][$id],
						'thumb' => $_GPC['thumb'][$id],
						'desc' => $_GPC['desc'][$id]
						), array('id' => $id));
				}
				//缓存
				m('plugin')->refreshCache(1);

				show_json(1);
			}
		}
		$condition = " and iscom=0 and deprecated=0";
		if (!empty($_GPC['keyword'])) {
			$condition.=" and identity like :keyword or name like :keyword";
			$params[':keyword'] = "%{$_GPC['keyword']}";
		}

		$list = pdo_fetchall('select * from ' . tablename('ewei_shop_plugin') . " where 1 {$condition} order by displayorder asc", $params);
		$total = count($list);
		include $this->template();
		exit;
	}

    public function apps()
    {
        global $_W,$_GPC;
		$domain = trim( preg_replace( "/http(s)?:\/\//", "", rtrim($_W['siteroot'],"/") )  );
		$setting = setting_load('site');
        $id = isset($setting['site']['key']) ? $setting['site']['key'] : (isset($setting['key']) ? $setting['key'] : '0');
		$authcode = get_authcode();
		$auth =base64_encode(authcode($domain."|".$id.'|'.$authcode,'ENCODE', "ewei_shopv2_apps"));
		header("location:https://u.we7shop.com/apps?auth={$auth}");
//        include $this->template();
	}

}
