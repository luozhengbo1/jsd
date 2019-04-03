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
require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';

class Store_EweiShopV2Page extends AppMobilePage
{

    /**
     * 门店选择
     */
    function selector()
    {
        global $_W, $_GPC;

        $ids = trim($_GPC['ids']);
        $type = intval($_GPC['type']);
        $merchid = intval($_GPC['merchid']);
        $lng = ($_GPC['lng']);
        $lat = ($_GPC['lat']);
        $condition = '';

        if(!empty($ids)){
            $condition =  " and id in({$ids})";
        }
        // type=1 自提  type=2 核销
        if($type==1){
            $condition .= " and type in(1,3) ";
        }
        elseif ($type==2){
            $condition .= " and type in(2,3) ";
        }

        if ($merchid > 0) {
            $list = pdo_fetchall('select * from ' . tablename('ewei_shop_merch_store') . ' where  uniacid=:uniacid and merchid=:merchid and status=1 '. $condition .' order by displayorder desc,id desc', array(':uniacid' => $_W['uniacid'], ':merchid' => $merchid));
        } else {
            $list = pdo_fetchall('select * from ' . tablename('ewei_shop_store') . ' where  uniacid=:uniacid and status=1 '. $condition .' order by displayorder desc,id desc', array(':uniacid' => $_W['uniacid']));
        }
        foreach($list as $key=>$value){
            $list[$key]['dast'] = m('util')->GetDistance($value['lat'],$value['lng'],$lat,$lng,2).'km';
        }
        $score = [];
        foreach ($list as $key => $value) {
            $score[$key] = $value['dast'];
        }
        array_multisort($score, SORT_ASC, SORT_NUMERIC ,$list);
        $list = set_medias($list,'logo');
        app_json( array('list'=>$list));

    }

    /**
     * 门店地图
     */
    function map(){

        global $_W, $_GPC;
        $id =intval($_GPC['id']);
        $merchid =intval($_GPC['merchid']);
        if ($merchid > 0) {
            $store = pdo_fetch('select * from ' . tablename('ewei_shop_merch_store') . ' where id=:id and uniacid=:uniacid and merchid=:merchid', array(':id'=>$id, ':uniacid' => $_W['uniacid'], ':merchid' => $merchid));
        } else {
            $store = pdo_fetch('select * from ' . tablename('ewei_shop_store') . ' where id=:id and uniacid=:uniacid', array(':id'=>$id, ':uniacid' => $_W['uniacid']));
        }

        $store['logo'] = empty($store['logo'])?$_W['shopset']['shop']['logo']:$store['logo'];
        $store['logo'] =tomedia($store['logo']);

        $gcj02 = $this->Convert_BD09_To_GCJ02($store['lat'],$store['lng']);
        $store['lat'] =$gcj02['lat'];
        $store['lng'] =$gcj02['lng'];
        app_json( array('store'=>$store));
    }

    function Convert_BD09_To_GCJ02($lat,$lng){
        $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
        $x = $lng - 0.0065;
        $y = $lat - 0.006;
        $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
        $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
        $lng = $z * cos($theta);
        $lat = $z * sin($theta);
        return array('lat'=>$lat,'lng'=>$lng);
    }

}

