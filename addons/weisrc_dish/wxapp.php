<?php

/**

 * 码上点餐

 *

 * 作者:迷失卍国度

 *

 * qq : 15595755

 */

defined('IN_IA') or exit('Access Denied');



class weisrc_dishModuleWxapp extends WeModuleWxapp

{

    //模块标识

    public $modulename = 'weisrc_dish';

    public $cur_tpl = 'style1';

    public $cur_mobile_path = '';

    public $cur_res = '';

    public $cur_version = 1;



    public $member_code = '';

    public $feyin_key = '';

    public $device_no = '';



    public $msg_status_success = 1;

    public $msg_status_bad = 0;

    public $_debug = '1'; //default:0

    public $_weixin = '1'; //default:1



    public $_appid = '';

    public $_appsecret = '';

    public $_accountlevel = '';

    public $_account = '';



    public $_weid = '';

    public $_fromuser = '';

    public $_nickname = '';

    public $_headimgurl = '';



    public $_auth2_openid = '';

    public $_auth2_nickname = '';

    public $_auth2_headimgurl = '';

    public $_auth2_key = 'bHYzNjAubmV0LmNu';

    public $_lat = '';

    public $_lng = '';

    public $table_area = 'weisrc_dish_area';

    public $table_blacklist = 'weisrc_dish_blacklist';

    public $table_cart = 'weisrc_dish_cart';

    public $table_category = 'weisrc_dish_category';

    public $table_email_setting = 'weisrc_dish_email_setting';

    public $table_goods = 'weisrc_dish_goods';

    public $table_intelligent = 'weisrc_dish_intelligent';

    public $table_nave = 'weisrc_dish_nave';

    public $table_order = 'weisrc_dish_order';

    public $table_order_goods = 'weisrc_dish_order_goods';

    public $table_print_order = 'weisrc_dish_print_order';

    public $table_print_setting = 'weisrc_dish_print_setting';

    public $table_reply = 'weisrc_dish_reply';

    public $table_setting = 'weisrc_dish_setting';

    public $table_sms_checkcode = 'weisrc_dish_sms_checkcode';

    public $table_sms_setting = 'weisrc_dish_sms_setting';

    public $table_store_setting = 'weisrc_dish_store_setting';

    public $table_mealtime = 'weisrc_dish_mealtime';

    public $table_stores = 'weisrc_dish_stores';

    public $table_coupon = 'weisrc_dish_coupon';

    public $table_sncode = 'weisrc_dish_sncode';

    public $table_collection = 'weisrc_dish_collection';

    public $table_type = 'weisrc_dish_type';

    public $table_ad = 'weisrc_dish_ad';

    public $table_template = "weisrc_dish_template";

    public $table_account = "weisrc_dish_account";

    public $table_queue_setting = "weisrc_dish_queue_setting";

    public $table_queue_order = "weisrc_dish_queue_order";

    public $table_tablezones = "weisrc_dish_tablezones";

    public $table_tables = "weisrc_dish_tables";

    public $table_tables_order = "weisrc_dish_tables_order";

    public $table_reservation = "weisrc_dish_reservation";

    public $table_fans = "weisrc_dish_fans";

    public $table_feedback = "weisrc_dish_feedback";

    public $table_businesslog = "weisrc_dish_businesslog";

    public $table_tpl_log = "weisrc_dish_tpl_log";

    public $table_savewine_log = "weisrc_dish_savewine_log";

    public $table_commission = "weisrc_dish_commission";

    public $table_service_log = "weisrc_dish_service_log";

    public $table_print_label = "weisrc_dish_print_label";

    public $table_order_log = "weisrc_dish_order_log";

    public $table_dispatcharea = "weisrc_dish_dispatcharea";

    public $table_deliveryarea = "weisrc_dish_deliveryarea";

    public $table_recharge = "weisrc_dish_recharge";

    public $table_useraddress = "weisrc_dish_useraddress";

    public $table_integral = "weisrc_dish_integral";

    public $serverip = '';



    public $global_sid = 0;

    public $logo = '';

    public $more_store_psize = 10;

    public $_isdebug = 0;



    function __construct()

    {

        global $_W, $_GPC;

        if ($this->_isdebug == 1) {

            $this->_fromuser = 'debug';

        } else {

            $this->_fromuser = $_W['openid'];

        }



        $host = $_SERVER['HTTP_HOST'];

        $this->_weid = $_W['uniacid'];

        $account = $_W['account'];

        $this->_auth2_openid = 'auth2_openid_' . $_W['uniacid'];

        $this->_auth2_nickname = 'auth2_nickname_' . $_W['uniacid'];

        $this->_auth2_headimgurl = 'auth2_headimgurl_' . $_W['uniacid'];



        $this->_lat = 'lat_' . $this->_weid;

        $this->_lng = 'lng_' . $this->_weid;



        $this->_appid = '';

        $this->_appsecret = '';

        $this->_accountlevel = $account['level']; //是否为高级号



        if (isset($_COOKIE[$this->_auth2_openid])) {

            $this->_fromuser = $_COOKIE[$this->_auth2_openid];

        }



        if (isset($_COOKIE['global_sid_' . $_W['uniacid']])) {

            $this->global_sid = $_COOKIE['global_sid_' . $_W['uniacid']];

        }



        if ($this->_accountlevel < 4) {

            $setting = uni_setting($this->_weid);

            $oauth = $setting['oauth'];

            if (!empty($oauth) && !empty($oauth['account'])) {

                $this->_account = account_fetch($oauth['account']);

                $this->_appid = $this->_account['key'];

                $this->_appsecret = $this->_account['secret'];

            }

        } else {

            $this->_appid = $_W['account']['key'];

            $this->_appsecret = $_W['account']['secret'];

        }



        $logo = pdo_fetch("SELECT site_logo FROM " . tablename($this->table_setting) . " WHERE weid = :weid", array

        (':weid' => $this->_weid));

        if (empty($logo['site_logo'])) {

            $this->logo = '../addons/weisrc_dish/template/images/logo.png';

        } else {

            $this->logo = tomedia($logo['site_logo']);

        }



        $template = pdo_fetch("SELECT * FROM " . tablename($this->table_template) . " WHERE weid = :weid", array(':weid' => $this->_weid));

        if (!empty($template)) {

            $this->cur_tpl = $template['template_name'];

        }

        $this->cur_res = RES . '/mobile/' . $this->cur_tpl;

        $this->cur_mobile_path = RES . '/mobile/' . $this->cur_tpl;

    }



    public function doPageIndex()

    {

        global $_W, $_GPC;

        $setting = $this->getSetting();



        $this->resetHour();



        $slide = $this->getSlidesByPos(2);

        $adlist = $this->getSlidesByPos(3);



        //门店类型

        $shoptypes = $this->getShoptypes();



        $typecount = count($shoptypes);



        $areaid = intval($_GPC['areaid']);

        $typeid = intval($_GPC['typeid']);

        $sortid = intval($_GPC['sortid']);

        if ($sortid == 0) {

            $sortid = 2;

        }

        $lat = trim($_GPC['lat']);

        $lng = trim($_GPC['lng']);



        $shoptotal = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename($this->table_stores) . " WHERE weid={$this->_weid} AND is_show=1 ORDER BY id DESC");



        $notice = pdo_fetch("SELECT * FROM " . tablename("weisrc_dish_notice") . " WHERE weid = :weid AND status=1 ORDER BY displayorder DESC,id DESC LIMIT 1", array(':weid' => $this->_weid));



        pdo_update($this->table_setting, array('visit' => intval($setting['visit']) + 1), array('id' => $setting['id']));



        header("Content-type: application/json; charset=utf-8");

        return $this->result(0, '', array('slides' => $slide, 'types' => $shoptypes, 'notice' =>

            $notice, 'visit' => $this->formatMoney($setting['visit']), 'shoptotal' => $shoptotal));

    }



    public function doPageGetMoreStore()

    {

        global $_GPC, $_W;

        $weid = $this->_weid;
        $lat = $_COOKIE[$this->_lat];
        $lng = $_COOKIE[$this->_lng];

        $areaid = intval($_GPC['areaid']);
        $typeid = intval($_GPC['typeid']);
        $sortid = intval($_GPC['sortid']);
        if ($sortid == 0) {
            $sortid =1;
        }

        $strwhere = " where weid = :weid and is_show=1 AND is_list=1 AND deleted=0 ";

        if ($areaid != 0) {
            $strwhere .= "  AND areaid={$areaid} ";

        }



        if ($typeid != 0) {

            $strwhere .= " AND typeid={$typeid} ";

        }



        $pindex = max(1, intval($_GPC['page']));

        $psize = $this->more_store_psize;

        $limit = " LIMIT " . ($pindex - 1) * $psize . ',' . $psize;



        if ($sortid == 1) {
            $timein = date('H:i');
            $strwhere .=" and ('{$timein}'>=begintime  and '{$timein}'<= endtime) ";
            $list = pdo_fetchall("SELECT *,(lat-:lat) * (lat-:lat) + (lng-:lng) * (lng-:lng) as dist FROM " . tablename($this->table_stores) . " {$strwhere

} ORDER BY dist, is_rest DESC,displayorder DESC, id DESC " . $limit, array(':weid' => $weid, ':lat' => $lat, ':lng' => $lng));

        } else if ($sortid == 2 && !empty($lat)) {

            $list = pdo_fetchall("SELECT *,(lat-:lat)*(lat-:lat) + (lng-:lng) * (lng-:lng) as dist FROM " . tablename($this->table_stores) . " {$strwhere

} ORDER BY dist, displayorder DESC,id DESC " . $limit, array(':weid' => $weid, ':lat' => $lat, ':lng' => $lng));

        } else {

            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_stores) . " {$strwhere} ORDER BY is_rest DESC,displayorder DESC, id DESC" . $limit, array(':weid' => $weid));

        }


        if (!empty($list)) {

            foreach ($list as $key => $value) {

//        $good_count = pdo_fetchcolumn("SELECT sum(sales) FROM " . tablename($this->table_goods) . " WHERE storeid=:id ", array(':id' => $value['id']));

//                $list[$key]['sales'] = intval($good_count);

                $newlimitprice = '';

                $oldlimitprice = '';



                $list[$key]['logo'] = tomedia($value['logo']);

                if ($value['is_newlimitprice'] == 1) {

                    $couponlist = pdo_fetchall("select * from " . tablename($this->table_coupon) . " WHERE storeid=:storeid AND :time<endtime AND type=3 ORDER BY gmoney desc,id DESC LIMIT 10", array(':storeid' => $value['id'], ':time' => TIMESTAMP));

                    foreach ($couponlist as $key2 => $value2) {

                        $newlimitprice .= $value2['title'] . ';';

                    }

                    $list[$key]['newlimitprice'] = $newlimitprice;

                }

                if ($value['is_oldlimitprice'] == 1) {

                    $couponlist = pdo_fetchall("select * from " . tablename($this->table_coupon) . " WHERE storeid=:storeid AND :time<endtime AND type=4 ORDER BY gmoney

desc,id DESC LIMIT 10", array(':storeid' => $value['id'], ':time' => TIMESTAMP));

                    foreach ($couponlist as $key3 => $value3) {

                        $oldlimitprice .= $value3['title'] . ';';

                    }

                    $list[$key]['oldlimitprice'] = $oldlimitprice;

                }

            }

        }



        header("Content-type: application/json; charset=utf-8");

        return $this->result(0, '', array('list' => $list));

    }



    public function resetHour()

    {

        global $_W, $_GPC;

        $weid = $this->_weid;

        pdo_update($this->table_stores, array('is_rest' => 0), array('weid' => $weid));



        $stores = pdo_fetchall("SELECT * FROM " . tablename($this->table_stores) . " WHERE weid = :weid ", array(':weid' => $weid));

        foreach($stores as $key => $value) {

            if (!empty($value['begintime']) && !empty($value['endtime'])) {

                $state = $this->check_hourtime($value['begintime'], $value['endtime']);

                if ($state >= 1) {

                    pdo_update($this->table_stores, array('is_rest' => 1), array('id' => $value['id']));

                }

            }

            if (!empty($value['begintime1']) && !empty($value['endtime1'])) {

                $state = $this->check_hourtime($value['begintime1'], $value['endtime1']);

                if ($state >= 1) {

                    pdo_update($this->table_stores, array('is_rest' => 1), array('id' => $value['id']));

                }

            }

            if (!empty($value['begintime2']) && !empty($value['endtime2'])) {

                $state = $this->check_hourtime($value['begintime2'], $value['endtime2']);

                if ($state >= 1) {

                    pdo_update($this->table_stores, array('is_rest' => 1), array('id' => $value['id']));

                }

            }

        }

    }



    public function check_hourtime($begintime, $endtime)

    {

        global $_W, $_GPC;

        $nowtime = intval(date("Hi"));

        $begintime = intval(str_replace(':', '', $begintime));

        $endtime = intval(str_replace(':', '', $endtime));



        if ($begintime < $endtime) { //开始时间小于结束时间

            if ($begintime <= $nowtime && $nowtime <= $endtime) { //开始时间小于现在时间

                return 1;//在营业时间

            }

        } else {

            if ($begintime <= $nowtime || $nowtime <= $endtime) {

                return 1;//在营业时间

            }

        }

        return 0;

    }



    public function doPagePay()

    {

        global $_GPC, $_W;
        var_dump('123');exit();
        $login_success = $this->checkLogin();

        if (is_error($login_success)) {

            return $this->result($login_success['errno'], $login_success['message']);

        }



        //获取订单号，保证在业务模块中唯一即可

        $orderid = intval($_GPC['orderid']);

        $myorder = $this->getOrderById($orderid);



        //构造支付参数

        $order = array(

            'tid' => $orderid,

            'user' => $_W['openid'], //用户OPENID

            'fee' => floatval($myorder['totalprice']), //金额

            'title' => '码上点餐订单',

        );



        //生成支付参数，返回给小程序端

        $pay_params = $this->pay($order);

        if (is_error($pay_params)) {

            return $this->result(1, '支付失败，请重试');

        }

        return $this->result(0, '', $pay_params);

    }



    public function doPageGetMoreOrder()

    {

        global $_GPC, $_W;

        $weid = $this->_weid;

        $from_user = $this->_fromuser;



        $login_success = $this->checkLogin();

        if (is_error($login_success)) {

            return $this->result($login_success['errno'], $login_success['message']);

        }



        $pindex = max(1, intval($_GPC['page']));

        $psize = $this->more_store_psize;

        $limit = " LIMIT " . ($pindex - 1) * $psize . ',' . $psize;

        $strwhere = " WHERE a.weid = '{$weid}' AND a.from_user='{$from_user}' ";

        $strwhere2 = " WHERE weid = '{$weid}' AND  from_user='{$from_user}' ";



        $order_list = pdo_fetchall("SELECT a.*,b.title AS storename,

b.tel AS tel,b.address AS address FROM " . tablename($this->table_order) . " AS a LEFT JOIN " . tablename($this->table_stores) . "

AS b ON a.storeid=b.id {$strwhere} ORDER BY a.id DESC " . $limit);

        $order_total = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename($this->table_order) . " {$strwhere2} ORDER BY id DESC");



        foreach ($order_list as $key => $value) {

            $order_list[$key]['goods'] = pdo_fetchall("SELECT a.*,b.title FROM " . tablename($this->table_order_goods) . " as a left join  " . tablename($this->table_goods) . " as b on a.goodsid=b.id WHERE a.weid = '{$weid}' and a.orderid={$value['id']}");



            if ($value['dining_mode'] == 1 || $value['dining_mode'] == 3) {

                $tablesid = intval($value['tables']);

                $table = pdo_fetch("SELECT * FROM " . tablename($this->table_tables) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $tablesid));

                if (!empty($table)) {

                    $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $table['tablezonesid']));

                    if (empty($tablezones)) {

                        exit('餐桌类型不存在！');

                    }

                    $table_title = $tablezones['title'] . '-' . $table['title'];

                    $order_list[$key]['table_title'] = $table_title;

                }

            }

        }

        header("Content-type: application/json; charset=utf-8");

        return $this->result(0, '', array('list' => $order_list, 'totalcount' => $order_total));

    }



    public function doPageOrderDetail()

    {

        global $_W, $_GPC;

        $weid = $this->_weid;

        $from_user = $this->_fromuser;

        $login_success = $this->checkLogin();

        if (is_error($login_success)) {

            return $this->result($login_success['errno'], $login_success['message']);

        }



        $id = intval($_GPC['id']);

        $setting = $this->getSetting();

        $strwhere = " WHERE a.id={$id} AND a.from_user='{$from_user}' ";



        $order = pdo_fetch("SELECT a.* FROM " . tablename($this->table_order) . " AS a LEFT JOIN " . tablename($this->table_stores) . " AS b ON a.storeid=b.id {$strwhere} ORDER BY a.id DESC LIMIT 1");



        if (empty($order)) {

            return $this->result(1, '订单不存在!');

        }



        $op = $_GPC['op'];

        if ($op == 'acceptorder') { //收货

            if ($order['from_user'] != $from_user) {

                return $this->result(1, '您没有该订单的操作权限!');

            }

            pdo_update($this->table_order, array('delivery_status' => 2, 'delivery_finish_time' => TIMESTAMP), array('id' => $id, 'from_user' => $from_user));

            if ($_W['isajax']) {

                $this->showMsg('收货成功!', 1);

            } else {

                message('收货成功!', $this->createMobileUrl('feedback', array('orderid' => $id)), 'success');

            }

        } else {

            $store = $this->getStoreById($order['storeid']);

            if ($order['dining_mode'] == 1 || $order['dining_mode'] == 3) {

                $tablesid = intval($order['tables']);

                $table = pdo_fetch("SELECT * FROM " . tablename($this->table_tables) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $tablesid));

                if (!empty($table)) {

                    $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $table['tablezonesid']));

                    $table_title = $tablezones['title'] . '-' . $table['title'];

                    $order['table_title'] = $table_title;

                }

            }



            if ($order['dining_mode'] == 3) {

                $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $order['tablezonesid']));

            }

            $order['goods'] = pdo_fetchall("SELECT a.*,b.title FROM " . tablename($this->table_order_goods) . " as a left join  " . tablename($this->table_goods) . " as b on a.goodsid=b.id WHERE a.weid = '{$weid}' and a.orderid={$order['id']}");



            if ($order['couponid'] != 0) {

                $coupon = pdo_fetch("SELECT a.* FROM " . tablename($this->table_coupon) . "

        a INNER JOIN " . tablename($this->table_sncode) . " b ON a.id=b.couponid

 WHERE a.weid = :weid AND b.id=:snid ORDER BY b.id

 DESC LIMIT 1", array(':weid' => $weid, ':snid' => $order['couponid']));



                if (!empty($coupon)) {

                    if ($coupon['type'] == 2) {

                        $coupon_info = "代金券抵用金额" . $order['discount_money'];

                    } else {

                        $coupon_info = $coupon['title'];

                    }

                    $order['coupon_info'] = $coupon_info;

                }

            }



            if ($order['dining_mode'] == 2) {

                $deliveryuser = pdo_fetch("SELECT * FROM " . tablename($this->table_account) . " where id=:id LIMIT 1", array(':id' => $order['delivery_id']));

                $order['deliveryuser'] = $deliveryuser;

            }

        }



        header("Content-type: application/json; charset=utf-8");

        return $this->result(0, '', array('order' => $order, 'store' => $store));

    }



    public function doPageRestlist()

    {

        global $_W, $_GPC;



        $areaid = intval($_GPC['areaid']);

        $typeid = intval($_GPC['typeid']);

        $sortid = intval($_GPC['sortid']);



        //所属区域

        $area = pdo_fetchall("SELECT * FROM " . tablename($this->table_area) . " where weid = :weid ORDER BY displayorder DESC", array(':weid' => $this->_weid), 'id');

        $curarea = "全城";

        if (!empty($area[$areaid]['name'])) {

            $curarea = $area[$areaid]['name'];

        }

//门店类型

        $shoptype = $this->getShoptypes();

        $curtype = "门店类型";

        if (!empty($shoptype[$typeid]['name'])) {

            $curtype = $shoptype[$typeid]['name'];

        }

        $cursort = "综合排序";

        if ($sortid == 1) {

            $cursort = "正在营业";

        } else if ($sortid == 2) {

            $cursort = "距离优先";

        }



        header("Content-type: application/json; charset=utf-8");

        return $this->result(0, '', array('types' => $shoptype, 'areas' => $area, 'curarea' => $curarea, 'curtype' =>

            $curtype, 'cursort' => $cursort));

    }



    public function doPageUpdateDishNum()

    {

        global $_W, $_GPC;

        $weid = $this->_weid;

        $from_user = $this->_fromuser;



        $storeid = intval($_GPC['storeid']); //门店id

        $dishid = intval($_GPC['id']); //商品id

        $optionid = $_GPC['optionid']; //规格id

        $total = intval($_GPC['num']); //更新数量

        $optype = trim($_GPC['optype']);



        $login_success = $this->checkLogin();

        if (is_error($login_success)) {

            return $this->result($login_success['errno'], $login_success['message']);

        }



        $store = $this->getStoreById($storeid);

        if ($store['is_rest'] != 1) {

            return $this->result(1, '商家休息中,暂不接单');

        }



        $goods = $this->getGoodsById($dishid);

        if (empty($goods)) {

            return $this->result(1, '没有相关商品');

        }



        $nowtime = mktime(0, 0, 0);

        if ($goods['lasttime'] <= $nowtime) {

            pdo_query("UPDATE " . tablename($this->table_goods) . " SET today_counts=0,lasttime=:time WHERE id=:id", array(':id' => $dishid, ':time' => TIMESTAMP));

        }

        if (empty($optionid)) {

            $cart = pdo_fetch("SELECT * FROM " . tablename($this->table_cart) . " WHERE goodsid=:goodsid AND weid=:weid AND storeid=:storeid AND

from_user=:from_user", array(':goodsid' => $dishid, ':weid' => $weid, ':storeid' => $storeid, ':from_user' => $from_user));

        } else {

            //查询购物车有没该商品

            $cart = pdo_fetch("SELECT * FROM " . tablename($this->table_cart) . " WHERE goodsid=:goodsid AND weid=:weid AND storeid=:storeid AND

from_user=:from_user AND optionid=:optionid ", array(':goodsid' => $dishid, ':weid' => $weid, ':storeid' => $storeid, ':from_user' => $from_user, ':optionid' => $optionid));

        }



        if ($goods['counts'] == 0) {

            return $this->result(1, '该商品已售完!');

        }

        if ($goods['counts'] > 0) {

            $count = $goods['counts'] - $goods['today_counts'];

            if ($count <= 0) {

                return $this->result(1, '该商品已售完!!');

            }

            if (!empty($cart)) {

                if ($cart['total'] < $total) {

                    if ($total > $count) {

                        return $this->result(1, '该商品已没库存!!');

                    }

                }

            } else {

                if ($total > $count) {

                    return $this->result(1, '该商品已没库存!!');

                }

            }

        }



        $iscard = $this->get_sys_card($from_user);

        $price = floatval($goods['marketprice']);

        if ($iscard == 1 && !empty($goods['memberprice'])) {

            $price = floatval($goods['memberprice']);

        }



        $optionid = trim($_GPC['optionid']);

        $optionids = explode('_', $optionid);

        $optionprice = 0;

        $optionname = '';



        if (count($optionids) > 0) {

            $options = pdo_fetchall("SELECT * FROM " . tablename("weisrc_dish_goods_option") . "  WHERE id IN ('" . implode("','", $optionids) . "')");

            $is_first = 0;

            foreach ($options as $key => $val) {

                $optionprice = $optionprice + $val['price'];

                if ($is_first == 0) {

                    $optionname .= $val['title'];

                } else {

                    $optionname .= '+' . $val['title'];

                }

                $is_first++;

            }



        }



        $price = $price + floatval($optionprice);



        if (empty($cart)) {

            //不存在的话增加商品点击量

            pdo_query("UPDATE " . tablename($this->table_goods) . " SET subcount=subcount+1 WHERE id=:id", array(':id' => $dishid));



            $addtotal = 1;

            if ($optype == 'add') {

                $addtotal = $total;

            }



            //添加进购物车

            $data = array(

                'weid' => $weid,

                'storeid' => $goods['storeid'],

                'goodsid' => $goods['id'],

                'optionid' => $optionid,

                'optionname' => $optionname,

                'goodstype' => $goods['pcate'],

                'price' => $price,

                'packvalue' => $goods['packvalue'],

                'from_user' => $from_user,

                'total' => $addtotal

            );

            pdo_insert($this->table_cart, $data);

        } else {

            if ($optype == 'add') {

                $total = intval($cart['total']) + $total;

            }

            //更新商品在购物车中的数量

            pdo_query("UPDATE " . tablename($this->table_cart) . " SET total=:total WHERE id=:id", array(':id' => $cart['id'], ':total' => $total));

        }



        $totalcount = 0;

        $totalprice = 0;

        $goodscount = 0;



        $cart = pdo_fetchall("SELECT * FROM " . tablename($this->table_cart) . " WHERE  storeid=:storeid AND from_user=:from_user AND weid=:weid", array(':storeid' => $storeid, ':from_user' => $from_user, ':weid' => $weid));



        $rgoods = array();

        foreach ($cart as $key => $value) {



            $goods_t = pdo_fetch("SELECT * FROM " . tablename($this->table_goods) . " WHERE id = :id LIMIT 1 ", array(':id' => $value['goodsid']));

            if (!$this->getmodules()) {

                $value['price'] = floatval($value['price']) + 6;

            }

            $cart[$key]['goodstitle'] = $goods_t['title'];

            $totalcount = $totalcount + $value['total'];

            $totalprice = $totalprice + $value['total'] * $value['price'];



            if ($value['goodsid'] == $dishid) {

                $goodscount = $goodscount + intval($value['total']);

            }



            if ($value['total'] > 0) {

                $optionname = '';

                if (!empty($value['optionname'])) {

                    $optionname = '[' . $value['optionname'] . ']';

                }



                $rgoods[] = array(

                    'goodsid' => $value['goodsid'],

                    'optionid' => $value['optionid'],

                    'goodstitle' => $goods_t['title'] . $optionname,

                    'goodsprice' => $value['price'],

                    'total' => $value['total']

                );

            }

        }



        header("Content-type: application/json; charset=utf-8");

        return $this->result(0, '',

            array(

                'totalprice' => $totalprice,

                'totalcount' => $totalcount,

                'goodscount' => $goodscount,

                'goods' => $rgoods

            ));

    }



    public function payResult($params)

    {

        global $_W, $_GPC;

        $weid = $this->_weid;

        $orderid = $params['tid'];

        $fee = intval($params['fee']);

        $paytype = array('credit' => '1', 'wechat' => '2', 'alipay' => '4', 'baifubao' => '5', 'delivery' => '3');



        // 卡券代金券备注

        if (!empty($params['is_usecard'])) {

            $cardType = array('1' => '微信卡券', '2' => '系统代金券');

            $result_price = ($params['fee'] - $params['card_fee']);

            $data['paydetail'] = '使用' . $cardType[$params['card_type']] . '支付了' . $result_price;

            $data['paydetail'] .= '元，实际支付了' . $params['card_fee'] . '元。';

            $data['totalprice'] = $params['card_fee'];

        }



//        $data['paytype'] = $paytype[$params['type']];

        $data['paytype'] = 2;



//        if ($params['type'] == 'alipay') {

//            if (!empty($params['transaction_id'])) {

//                $data['transid'] = $params['transaction_id'];

//            }

//        }

//        if ($params['type'] == 'wechat') {

//            if (!empty($params['tag']['transaction_id'])) {

//                $data['transid'] = $params['tag']['transaction_id'];

//            }

//        }

        if (!empty($params['tag']['transaction_id'])) {

            $data['transid'] = $params['tag']['transaction_id'];

        }





        if (($params['paysys'] == 'bm_payms') || ($params['paysys'] == 'jxkj_unipay')) {

            $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE ordersn = :ordersn", array(':ordersn' => $params['tid']));

            $orderid = $order['id'];

        } else {

            $order = $this->getOrderById($orderid);

        }



        if (empty($order)) {

            message('订单不存在258!!!');

        }



        if ($order['ispay'] == 0) {

            $storeid = $order['storeid'];

            $store = $this->getStoreById($storeid);



            //本订单产品

            $goods = pdo_fetchall("SELECT a.*,b.title,b.unitname FROM " . tablename($this->table_order_goods) . " as a left join  " . tablename($this->table_goods) . " as b on a.goodsid=b.id WHERE a.orderid=:orderid ", array(':orderid' => $orderid));

            $goods_str = '';

            $goods_tplstr = '';

            $flag = false;

            foreach ($goods as $key => $value) {

                if (!$flag) {

                    $goods_str .= "{$value['title']} 价格：{$value['price']} 数量：{$value['total']}{$value['unitname']}";

                    $goods_tplstr .= "{$value['title']} {$value['total']}{$value['unitname']}";

                    $flag = true;

                } else {

                    $goods_str .= "<br/>{$value['title']} 价格：{$value['price']} 数量：{$value['total']}{$value['unitname']}";

                    $goods_tplstr .= ",{$value['title']} {$value['total']}{$value['unitname']}";

                }

            }



            if ($order['dining_mode'] == 1) { //店内

                if ($data['paytype'] == 3) { //现金

                    pdo_update($this->table_tables, array('status' => 2), array('id' => $order['tables']));

                } else {

                    pdo_update($this->table_tables, array('status' => 3), array('id' => $order['tables']));

                }

            }

            $setting = $this->getSettingByWeid($order['weid']);



            //后台通知，修改状态

            if ($params['result'] == 'success' && $params['from'] == 'notify') {

//                if ($data['paytype'] == 1 || $data['paytype'] == 2 || $data['paytype'] == 4) { //在线，余额支付

//                    $data['ispay'] = 1;

//                    $data['paytime'] = TIMESTAMP;

//                    if ($store['is_auto_confirm'] == 1 && $order['status'] == 0) {

//                        $data['status'] = 1;

//                    }

//                    pdo_update($this->table_order, $data, array('id' => $orderid));

//

//                    $user = $this->getFansByOpenid($order['from_user']);

//                    $touser = empty($user['nickname']) ? $user['from_user'] : $user['nickname'];

//                    $this->addOrderLog($orderid, $touser, 1, 1, 2);

//                }

                $data['ispay'] = 1;

                $data['paytime'] = TIMESTAMP;

                if ($store['is_auto_confirm'] == 1 && $order['status'] == 0) {

                    $data['status'] = 1;

                }

                pdo_update($this->table_order, $data, array('id' => $orderid));



                $user = $this->getFansByOpenid($order['from_user']);

                $touser = empty($user['nickname']) ? $user['from_user'] : $user['nickname'];

                $this->addOrderLog($orderid, $touser, 1, 1, 2);





                file_put_contents(IA_ROOT . "/addons/weisrc_dish/wxparams.log", var_export($data, true) . PHP_EOL, FILE_APPEND);

                file_put_contents(IA_ROOT . "/addons/weisrc_dish/params.log", var_export($params, true) . PHP_EOL, FILE_APPEND);

                if ($params['paysys'] != 'payu' && $params['paysys'] != 'bm_payms' && $params['paysys'] != 'jxkj_unipay') {

                    if ($params['type'] == 'alipay') {

                        if (empty($params['transaction_id'])) {

                            return false;

                        }

                    }

                    if ($params['type'] == 'wechat') {

                        if (empty($params['tag']['transaction_id'])) {

                            return false;

                        }

                    }

                }





                if ($order['dining_mode'] == 6) { //充值

                    $status = $this->setFansCoin($order['from_user'], $order['totalprice'], "订单编号{$orderid}充值");

                    $this->addRechargePrice($orderid);

                    pdo_update($this->table_order, array('status' => 3), array('id' => $orderid));

                }

//                if ($order['istpl'] == 0) {

                if ($params['type'] == 'credit') {

                    pdo_update($this->table_order, array('istpl' => 1), array('id' => $orderid));

                }



                if ($order['dining_mode'] != 6) {

//                    $this->feiyinSendFreeMessage($orderid); //飞印

//                    $this->_365SendFreeMessage($orderid); //365打印机

//                    $this->feieSendFreeMessage($orderid); //飞鹅

//                    $this->_yilianyunSendFreeMessage($orderid); //易联云



                    $order = $this->getOrderById($orderid);

                    //用户

                    $this->sendOrderNotice($order, $store, $setting);

                    //管理

                    if (!empty($setting)) {

                        //平台提醒

                        if ($setting['is_notice'] == 1) {

                            if (!empty($setting['tpluser'])) {

                                $tousers = explode(',', $setting['tpluser']);

                                foreach ($tousers as $key => $value) {

                                    $this->sendAdminOrderNotice($orderid, $value, $setting);

                                }

                            }

                            if (!empty($setting['email']) && !empty($setting['email_user']) && !empty($setting['email_pwd'])) {

//                                $this->sendAdminOrderEmail($setting['email'], $order, $store, $goods_str);

                            }

                            if (!empty($setting['sms_mobile']) && !empty($setting['sms_username']) && !empty($setting['sms_pwd'])) {

                                $this->sendAdminOrderSms($setting['sms_mobile'], $order);

                            }

                        }



                        //门店提醒

                        $accounts = pdo_fetchall("SELECT * FROM " . tablename($this->table_account) . " WHERE weid = :weid AND storeid=:storeid AND status=2 AND is_notice_order=1 ORDER BY id

DESC ", array(':weid' => $this->_weid, ':storeid' => $storeid));

                        foreach ($accounts as $key => $value) {

                            if (!empty($value['from_user'])) {

                                $this->sendAdminOrderNotice($orderid, $value['from_user'], $setting);

                            }

                            if (!empty($value['email']) && !empty($setting['email_user']) && !empty($setting['email_pwd'])) {

//                                $this->sendAdminOrderEmail($value['email'], $order, $store, $goods_str);

                            }

                            if (!empty($value['mobile']) && !empty($setting['sms_username']) && !empty($setting['sms_pwd'])) {

//                                $smsStatus = $this->sendAdminOrderSms($value['mobile'], $order);

                            }

                        }

                    }



                    if ($order['dining_mode'] == 2 && $setting['delivery_mode'] != 1) { //外卖订单,通知配送

                        $strwhere = '';

                        if ($setting['delivery_mode'] == 2) { //所有配送员

                            $deliverys = pdo_fetchall("SELECT * FROM " . tablename($this->table_account) . " WHERE weid = :weid AND role=4 AND status=2 ORDER BY id DESC ", array(':weid' => $this->_weid));

                            foreach ($deliverys as $key => $value) {

                                $this->sendDeliveryOrderNotice($orderid, $value['from_user'], $setting);

                            }

                        } else if ($setting['delivery_mode'] == 3) { //区域配送员

                            $area = $this->getNearDeliveryArea($order['lat'], $order['lng']);

                            $areaid = intval($area['id']);

                            if ($areaid != 0) {

                                $strwhere = " AND areaid={$areaid} ";

                                $deliverys = pdo_fetchall("SELECT * FROM " . tablename($this->table_account) . " WHERE weid = :weid AND role=4 AND status=2 {$strwhere} ORDER BY id DESC ", array(':weid' => $this->_weid));

                                foreach ($deliverys as $key => $value) {

                                    $this->sendDeliveryOrderNotice($orderid, $value['from_user'], $setting);

                                }

                            }

                        }

                    }

                }

//                $this->sendfengniao($order, $store, $setting);

                pdo_update($this->table_order, array('istpl' => 1), array('id' => $orderid));

//                }

            }



            //前台通知

            if ($params['from'] == 'return') {

                if ($order['istpl'] == 0 && $params['type'] == 'delivery') {

                    $data['istpl'] = 1;//

                    if ($data['paytype'] == 3) { //现金

                        if ($store['is_order_autoconfirm'] == 1 && $order['status'] == 0) {

                            $data['status'] = 1;

                        }

                    }



                    pdo_update($this->table_order, $data, array('id' => $orderid));

                    $this->feiyinSendFreeMessage($orderid);

                    $this->_365SendFreeMessage($orderid);

                    $this->feieSendFreeMessage($orderid);

                    $this->_yilianyunSendFreeMessage($orderid);

                    $this->sendfengniao($order, $store, $setting);



                    $order = $this->getOrderById($orderid);

                    //用户

                    $this->sendOrderNotice($order, $store, $setting);

                    //管理

                    if (!empty($setting)) {

                        //平台提醒

                        if ($setting['is_notice'] == 1) {

                            if (!empty($setting['tpluser'])) {

                                $tousers = explode(',', $setting['tpluser']);

                                foreach ($tousers as $key => $value) {

                                    $this->sendAdminOrderNotice($orderid, $value, $setting);

                                }

                            }

                            if (!empty($setting['email'])) {

//                                $this->sendAdminOrderEmail($setting['email'], $order, $store, $goods_str);

                            }

                            if (!empty($setting['sms_mobile'])) {

                                $smsStatus = $this->sendAdminOrderSms($setting['sms_mobile'], $order);

                            }

                        }

                        //门店提醒

                        $accounts = pdo_fetchall("SELECT * FROM " . tablename($this->table_account) . " WHERE weid = :weid AND storeid=:storeid AND status=2 AND is_notice_order=1 ORDER BY id DESC ", array(':weid' => $this->_weid, ':storeid' => $storeid));

                        foreach ($accounts as $key => $value) {

                            if (!empty($value['from_user'])) {

                                $this->sendAdminOrderNotice($orderid, $value['from_user'], $setting);

                            }

                            if (!empty($value['email'])) {

//                                $this->sendAdminOrderEmail($value['email'], $order, $store, $goods_str);

                            }

                            if (!empty($value['mobile'])) {

                                $smsStatus = $this->sendAdminOrderSms($value['mobile'], $order);

                            }

                        }

                    }



                    if ($order['dining_mode'] == 2 && $setting['delivery_mode'] != 1) { //外卖

                        $strwhere = '';

                        if ($setting['delivery_mode'] == 2) { //所有配送员

                            $deliverys = pdo_fetchall("SELECT * FROM " . tablename($this->table_account) . " WHERE weid = :weid AND role=4 AND status=2 ORDER BY id DESC ", array(':weid' => $this->_weid));

                            foreach ($deliverys as $key => $value) {

                                $this->sendDeliveryOrderNotice($orderid, $value['from_user'], $setting);

                            }

                        } else if ($setting['delivery_mode'] == 3) { //区域配送员

                            $fans = $this->getFansByOpenid($order['from_user']);

                            $area = $this->getNearDeliveryArea($order['lat'], $order['lng']);

                            $areaid = intval($area['id']);

                            if ($areaid != 0) {

                                $strwhere = " WHERE weid =:weid AND areaid=:areaid AND role=4 AND status=2 ";

                                $deliverys = pdo_fetchall("SELECT * FROM " . tablename($this->table_account) . " {$strwhere} ORDER BY id DESC ", array(':weid' => $this->_weid, ':areaid' => $areaid));

                                foreach ($deliverys as $key => $value) {

                                    $this->sendDeliveryOrderNotice($orderid, $value['from_user'], $setting);

                                }

                            }

                        }

                    }

                }

            }

        }



        $tip_msg = '支付成功123';

        if ($params['type'] == 'delivery') {

            $tip_msg = '下单成功';

        }



        $setting = uni_setting($_W['uniacid'], array('creditbehaviors'));

        $credit = $setting['creditbehaviors']['currency'];



        $url = '../../app/' . $this->createMobileUrl('orderdetail', array('orderid' => $orderid));

        if ($order['dining_mode'] == 6) {

            $tip_msg = '充值成功';

            $url = '../../app/' . $this->createMobileUrl('usercenter', array());

        }



        if ($params['type'] == $credit) {



            if ($params['type'] == 'baifubao') {

                return $this->result(0, $tip_msg);

            } else {

                return $this->result(0, $tip_msg);

            }

        } else {



            if ($params['paysys'] == 'payu' || $params['paysys'] == 'bm_payms' || $params['paysys'] == 'jxkj_unipay') {

//                Header("Location: {$url}");

            } else {

                return $this->result(0, $tip_msg);

            }

        }

        return $this->result(0, '操作成功');

    }



    public function doPageCreatNewOrder()

    {

        global $_W, $_GPC;

        $login_success = $this->checkLogin();

        if (is_error($login_success)) {

            return $this->result($login_success['errno'], $login_success['message']);

        }



        $cart = htmlspecialchars_decode($_GPC['goods']);

//        header("Content-type: application/json; charset=utf-8");

//        return $this->result(0, '操作成功', array('goods' => $cart));



        $cart = json_decode($cart, true);



//        $cart = json_decode($_GPC['goods'], true);

//        $cart = urldecode($_GPC['goods']), true);

//        header("Content-type: application/json; charset=utf-8");

//        return $this->result(0, '操作成功', array('goods' => $cart));

        file_put_contents(IA_ROOT . "/addons/weisrc_dish/goods.log", date('Y-m-d H:i', TIMESTAMP) .var_export($_GPC, true) . PHP_EOL, FILE_APPEND);



        $weid = $this->_weid;

        $from_user = $this->_fromuser;

        $couponid = intval($_GPC['couponid']);

        $storeid = intval($_GPC['storeid']);

        //1店内,2外卖

        $mode = intval($_GPC['mode']) == 0 ? 1 : intval($_GPC['mode']);

        $is_reservation_goods = intval($_GPC['reservationtype']);

        $setting = $this->getSetting();



        $is_auto_address = intval($setting['is_auto_address']);

        if ($mode == 3) {

            $is_auto_address = 1;

        }



        file_put_contents(IA_ROOT . "/addons/weisrc_dish/goods.log", 'mode:' .var_export($_GPC['mode'], true) . PHP_EOL,

            FILE_APPEND);



        $useraddress = pdo_fetch("SELECT * FROM " . tablename($this->table_useraddress) . " WHERE weid=:weid AND from_user=:from_user AND isdefault=1 LIMIT 1", array(':weid' => $weid, ':from_user' => $from_user));



        if ($is_auto_address == 0) {

            if ($useraddress) {

                return $this->result(1, '请先添加联系方式！');

            }

            $lat = trim($useraddress['lat']);

            $lng = trim($useraddress['lng']);

        } else {

            $lat = trim($_GPC['lat']);

            $lng = trim($_GPC['lng']);

        }



        $is_handle_goods = 1; //是否处理商品

        if ($mode == 5 || $mode == 6) {

            $is_handle_goods = 0;

        }

        $isvip = $this->get_sys_card($from_user);



        if ($mode != 6) {

            if (!empty($storeid)) {

                $store = $this->getStoreById($storeid);

                if (empty($store)) {

                    return $this->result(1, '请先选择门店');

                }

            }

        }



        //外卖

        if ($mode == 2) {

            if (empty($lat) || empty($lng)) {

                return $this->result(1, '请重新选择配送地址');

            }

            //距离

            $delivery_radius = floatval($store['delivery_radius']);

            $distance = $this->getDistance($lat, $lng, $store['lat'], $store['lng']);

            $distance = floatval($distance);

            if ($store['not_in_delivery_radius'] == 0 && $delivery_radius > 0) { //只能在距离范围内

                if ($distance > $delivery_radius) {

                    return $this->result(1, '超出配送范围，不允许下单。');

                }

            }

        }

        if ($mode != 6) {

            //购物车为空

//            $cart = pdo_fetchall("SELECT * FROM " . tablename($this->table_cart) . " WHERE weid = :weid AND from_user = :from_user AND storeid=:storeid", array(':weid' => $weid, ':from_user' => $from_user, ':storeid' => $storeid));

        }



        if ($is_handle_goods == 1 && $is_reservation_goods != 1) {

//            if (empty($cart)) {

//                return $this->result(1, '请先添加商品');

//            }

        }

        if ($is_auto_address == 0) { //多收餐地址

            $username = $useraddress['realname']; //用户名

            $tel = $useraddress['mobile']; //电话

            $address = $useraddress['address'] . ' ' . $useraddress['doorplate']; //地址

        } else {

            $username = trim($_GPC['username']); //用户名

            $tel = trim($_GPC['tel']); //电话

            $address = trim($_GPC['address']);

        }



        $sex = trim($_GPC['sex']); //性别

        $meal_time = trim($_GPC['meal_time']); //订餐时间

        $counts = intval($_GPC['counts']); //预订人数

        $seat_type = intval($_GPC['seat_type']); //就餐形式

        $carports = intval($_GPC['carports']); //预订车位

        $remark = trim($_GPC['remark']); //备注

        $dispatcharea = trim($_GPC['dispatcharea']); //地址

        $tables = intval($_GPC['tables']); //桌号

        $tablezonesid = intval($_GPC['tablezonesid']); //桌台

        $append = intval($_GPC['append']); //是否加单



        if ($mode != 4 && $mode != 1 && $is_handle_goods == 1) {//非堂点非收银

            if (empty($username)) {

                return $this->result(1, '请选择您的联系方式!');

            }

            if (empty($tel)) {

                return $this->result(1, '请选择您的联系方式!!');

            }

        }



        //堂点

        if ($mode == 1) {

            if ($append == 0 && $counts <= 0) {

                return $this->result(1, '请输入用餐人数');

            }

            if ($tables == 0) {

                return $this->result(1, '请先扫描桌台');

            }



            if ($store['is_locktables'] == 1) {

                $haveorder = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE weid=:weid AND storeid=:storeid AND tables=:tables AND

status<>-1 AND status<>3 LIMIT 1", array(":weid" => $weid, ":storeid" => $storeid, ":tables" => $tables));

                if ($haveorder) {

                    if ($haveorder['from_user'] != $from_user) {

                        return $this->result(1, '餐台已经有人在使用中');

                    }

                }

            }

        } else if ($mode == 2) {//外卖

            if (empty($address)) {

                return $this->result(1, '请选择您的联系方式');

            }

        } else if ($mode == 3) {

            if ($tables == 0) {

                return $this->result(1, '请先选择桌台');

            }

        }

        $user = $this->getFansByOpenid($from_user);

        $fansdata = array('weid' => $weid,

            'from_user' => $from_user,

            'username' => $username,

            'address' => $address,

            'mobile' => $tel

        );

        if (empty($username)) {

            unset($fansdata['username']);

        }

        if (empty($tel)) {

            unset($fansdata['mobile']);

        }

        if (empty($address)) {

            unset($fansdata['address']);

        }

        if ($mode == 2) { //外卖

            $fansdata['lat'] = $lat;

            $fansdata['lng'] = $lng;

        }

        if (empty($user)) {

            pdo_insert($this->table_fans, $fansdata);

        } else {

            pdo_update($this->table_fans, $fansdata, array('id' => $user['id']));

        }

//2.购物车 //a.添加订单、订单产品

        $totalnum = 0;

        $totalprice = 0;

        $goodsprice = 0;

        $dispatchprice = 0;

        $freeprice = 0;

        $packvalue = 0;

        $teavalue = 0;

        $service_money = 0;



        if ($is_reservation_goods != 1) {

            foreach ($cart as $key => $value) {

                //商品数量

                $total = intval($value['num']);

                //总数量

                $totalnum = $totalnum + intval($value['num']);

                //商品总价格

                $goodsprice = $goodsprice + ($total * floatval($value['price']));

                if ($mode == 2) { //打包费

                    $packvalue = $packvalue + ($total * floatval($value['packing_fee']));

                }

            }

        }



        file_put_contents(IA_ROOT . "/addons/weisrc_dish/goods.log", '商品价格:' .var_export($goodsprice, true) . PHP_EOL,

            FILE_APPEND);



        if ($mode == 2) { //外卖

            $dispatchprice = $store['dispatchprice'];

            if ($store['is_delivery_distance'] == 1 && $is_auto_address == 0) { //按距离收费

                $distance = $this->getDistance($useraddress['lat'], $useraddress['lng'], $store['lat'], $store['lng']);

                $distanceprice = $this->getdistanceprice($storeid, $distance);

                $dispatchprice = floatval($distanceprice['dispatchprice']);

            }

            if ($store['is_delivery_time'] == 1) { //特殊时段加价

                $tprice = $this->getPriceByTime($storeid);

                $dispatchprice = $dispatchprice + $tprice;

            }

            $freeprice = floatval($store['freeprice']);

            if ($freeprice > 0.00) {

                if ($goodsprice >= $freeprice) {

                    $dispatchprice = 0;

                }

            }

        }

        if ($mode == 1) { //店内

            if ($store['is_tea_money'] == 1) {

                $teavalue = $counts * floatval($store['tea_money']);

            }

        }



        $isnewuser = $this->isNewUser($storeid);

        $dlimitprice = 0;

        $newlimitprice = '';

        $oldlimitprice = '';

        $newlimitpricevalue = '';

        $oldlimitpricevalue = '';

        if ($isnewuser == 1) { //新用户

            if ($store['is_newlimitprice'] == 1) { //新顾客满减

                $coupon_obj1 = $this->getNewLimitPrice($storeid, $goodsprice, $mode);

                if ($coupon_obj1) {

                    $dlimitprice = floatval($coupon_obj1['dmoney']);

                    $newlimitprice = $coupon_obj1['title'];

                    $newlimitpricevalue = $dlimitprice;

                }

            }

        } else { //老用户

            if ($store['is_oldlimitprice'] == 1) { //老顾客满减

                $coupon_obj2 = $this->getOldLimitPrice($storeid, $goodsprice, $mode);

                if ($coupon_obj2) {

                    $dlimitprice = floatval($coupon_obj2['dmoney']);

                    $oldlimitprice = $coupon_obj2['title'];

                    $oldlimitpricevalue = $dlimitprice;

                }

            }

        }



        $totalprice = $goodsprice + $dispatchprice + $packvalue + $teavalue - $dlimitprice;

        if ($mode == 1) { //店内

            $table = pdo_fetch("SELECT * FROM " . tablename($this->table_tables) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $tables));

            $tablezonesid = $table['tablezonesid'];

            $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " WHERE id = :id", array(':id' => $tablezonesid));

            $service_rate = floatval($tablezones['service_rate']);

            if ($service_rate > 0) {

                $service_money = $totalprice * $service_rate / 100;

            }

            $totalprice = $totalprice + $service_money;

        }



        if ($mode == 3) { //预定

            if ($is_reservation_goods == 1) {

                $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " WHERE id = :id", array(':id' => $tablezonesid));

                if (floatval($tablezones['reservation_price']) <= 0) {

                    $totalprice = 0.01;

                } else {

                    $totalprice = floatval($tablezones['reservation_price']);

                }

            }

        }



        if ($mode == 2) { //外卖

            $sendingprice = floatval($store['sendingprice']);

            if ($sendingprice > 0.00) {

                if ($goodsprice < $store['sendingprice']) {

                    return $this->result(1, '您的购买金额达不到起送价格!');

                }

            }

        }



        $coupon = pdo_fetch("SELECT a.*,b.sncode FROM " . tablename($this->table_coupon) . "

        a INNER JOIN " . tablename($this->table_sncode) . " b ON a.id= b.couponid

 WHERE a.weid = :weid AND b.from_user=:from_user AND b.status=0 AND :time<a.endtime AND b.id=:couponid ORDER BY b.id

 DESC LIMIT 1", array(':weid' => $weid, ':from_user' => $from_user, ':time' => TIMESTAMP, ':couponid' => $couponid));



        $discount_money = 0;

        if ($couponid <> 0 && empty($coupon)) {

            return $this->result(1, '优惠券不存在!');

        } else {

            if ($coupon['type'] == 2) {

                $discount_money = floatval($coupon['dmoney']);

                $totalprice = $totalprice - $coupon['dmoney'];

            }

        }

        if (!$this->getmodules()) {

            $totalprice = $totalprice + 8;

            $goodsprice = $goodsprice - 2;

        }



        if ($mode == 5 || $mode == 6) { //收银

            $totalprice = floatval($_GPC['total']);

        }

        if ($mode == 6) {

            //充值赠送

            $recharge = pdo_fetch("SELECT * FROM " . tablename($this->table_recharge) . " WHERE weid = :weid AND :nowtime<endtime AND :nowtime>starttime AND :recharge_value>=recharge_value ORDER BY `recharge_value` DESC,`id` DESC LIMIT 1", array(':weid' => $weid, ':nowtime' => TIMESTAMP, ':recharge_value' => $totalprice));

            $rechargeid = intval($recharge['id']);

        }



        //加菜

        if ($append == 2) {

            $orderid = intval($_GPC['order_id']);

            $dishInfo = pdo_fetchall("SELECT goodsid,price,total FROM " . tablename($this->table_order_goods) . " WHERE weid=:weid AND storeid=:storeid AND orderid=:orderid", array(":weid" => $weid, ":storeid" => $storeid, ":orderid" => $orderid));

            foreach ($dishInfo as $v) {

                $dishid[] = $v['goodsid'];

            }

            if ($is_reservation_goods != 1) {

                foreach ($cart as $k => $v) {

                    if (empty($v['total'])) {

                        continue;

                    }

                    if (in_array($v['goodsid'], $dishid)) {

                        $dishCon = array(":weid" => $weid, ":storeid" => $storeid, ":orderid" => $orderid, ":goodsid" => $v['goodsid']);

                        $sql = "UPDATE " . tablename($this->table_order_goods) . " SET total=total+{$v['total']},dateline=" . time() . " WHERE weid=:weid AND storeid=:storeid AND orderid=:orderid AND goodsid=:goodsid";

                        pdo_query($sql, $dishCon);

                    } else {

                        $parm = array("weid" => $weid, "storeid" => $storeid, "orderid" => $orderid, "goodsid" => $v['goodsid'], "price" => $v['price'], "total" => $v['total'], 'dateline' => time(), 'optionid' => $v['optionid'], 'optionname' => $row['optionname']);

                        pdo_insert($this->table_order_goods, $parm);

                    }

                    $goodsName = pdo_fetch("SELECT title FROM " . tablename($this->table_goods) . " WHERE id=:id", array(":id" => $v['goodsid']));

                    $appendMes .= $goodsName['title'] . "*" . $v['total'] . ",";

                    pdo_query("UPDATE " . tablename($this->table_goods) . " SET today_counts=today_counts+:counts,sales=sales+:counts,lasttime=:time WHERE id=:id", array(':id' => $v['goodsid'], ':counts' => $v['total'], ':time' => TIMESTAMP));

                }

            }



            $orderParm = array(':totalnum' => $totalnum, ':totalprice' => $totalprice, ':goodsprice' => $goodsprice, ':service_money' => $service_money, ':tea_money' => $teavalue, ':id' => $orderid);

            pdo_query("UPDATE " . tablename($this->table_order) . " SET totalnum=totalnum+:totalnum ,totalprice=totalprice+:totalprice,goodsprice=goodsprice+:goodsprice,service_money=service_money+:service_money,tea_money=tea_money+:tea_money,append_dish=1 where id=:id ", $orderParm);

            if ($couponid > 0) {

                pdo_update($this->table_sncode, array('status' => 1), array('id' => $couponid));

            }

            $this->msg_status_success = 2;

        } else {

            $data = array(

                'weid' => $weid,

                'from_user' => $from_user,

                'storeid' => $storeid,

                'couponid' => $couponid,

                'discount_money' => $discount_money,

                'ordersn' => date('Ymd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99)),

                'totalnum' => $totalnum, //产品数量

                'totalprice' => $totalprice, //总价

                'goodsprice' => $goodsprice,

                'tea_money' => $teavalue,

                'service_money' => $service_money,

                'dispatchprice' => $dispatchprice,

                'packvalue' => $packvalue,

                'paytype' => 0, //付款类型

                'newlimitprice' => $newlimitprice,

                'oldlimitprice' => $oldlimitprice,

                'newlimitpricevalue' => $newlimitpricevalue,

                'oldlimitpricevalue' => $oldlimitpricevalue,

                'one_order_getprice' => floatval($setting['one_order_getprice']),

                'username' => $username,

                'tel' => $tel,

                'meal_time' => $meal_time,

                'counts' => $counts,

                'seat_type' => $seat_type,

                'tables' => $tables,

                'tablezonesid' => $tablezonesid,

                'carports' => $carports,

                'dining_mode' => $mode, //订单类型

                'remark' => $remark, //备注

                'address' => $dispatcharea . $address, //地址

                'status' => 0, //状态

                'rechargeid' => $rechargeid,

                'lat' => $lat,

                'lng' => $lng,

                'isvip' => $isvip,

                'is_append' => $append,

                'dateline' => TIMESTAMP

            );



            if ($mode == 1) { //店内

                unset($data['username']);

                unset($data['tel']);

                unset($data['address']);

            }



            if ($mode == 4) { //快餐

                $quicknum = $this->getQuickNum($storeid);

                $data['quicknum'] = $quicknum;

            }



            //保存订单

            pdo_insert($this->table_order, $data);

            $orderid = pdo_insertid();

            if ($orderid > 0) {

                if ($couponid > 0) {

                    pdo_update($this->table_sncode, array('status' => 1), array('id' => $couponid));

                }

            }

            //保存新订单商品

            if ($is_reservation_goods != 1 && $is_handle_goods == 1) {

                foreach ($cart as $key => $row) {

                    file_put_contents(IA_ROOT . "/addons/weisrc_dish/goods.log", '有商品:' . PHP_EOL, FILE_APPEND);

                    $total = intval($row['num']);

                    $price = floatval($row['price']);

                    $goods_id = intval($row['goods_id']);



                    if ( empty($total)) {

                        continue;

                    }



                    $sum_name = array();

                    $sum_id = array();

                    foreach ($row['sub'] as $sum_key => $sum_value) {

                        $sum_name [] = $sum_value['sub_name'];

                        $sum_id [] = $sum_value['sub_id'];

                    }

                    $sum_ids = implode("_", $sum_id);

                    $sum_names = implode("+", $sum_name);



                    pdo_query("UPDATE " . tablename($this->table_goods) . " SET today_counts=today_counts+:counts,sales=sales+:counts,lasttime=:time WHERE id=:id",

                        array(

                            ':id' => $goods_id,

                            ':counts' => $total,

                            ':time' => TIMESTAMP

                        )

                    );



                    pdo_insert($this->table_order_goods, array(

                        'weid' => $_W['uniacid'],

                        'storeid' => $storeid,

                        'goodsid' => $goods_id,

                        'optionid' => $sum_ids,

                        'optionname' => $sum_names,

                        'orderid' => $orderid,

                        'price' => $price,

                        'total' => $total,

                        'dateline' => TIMESTAMP,

                    ));

                }



            }

        }



        $touser = empty($user['nickname']) ? $user['from_user'] : $user['nickname'];

        if ($this->msg_status_success == 2) {

            $touser .= '&nbsp;加菜：' . $appendMes;

        }



        $this->addOrderLog($orderid, $touser, 1, 1, 1);

        pdo_insert($this->table_service_log,

            array(

                'orderid' => $orderid,

                'storeid' => $storeid,

                'weid' => $weid,

                'from_user' => $from_user,

                'content' => '您有未处理的订单，请尽快处理',

                'dateline' => TIMESTAMP,

                'status' => 0)

        );



        header("Content-type: application/json; charset=utf-8");

        return $this->result(0, '操作成功', array('orderid' => $orderid));

    }



    public function doPageMenu()

    {

        global $_W, $_GPC;

        $login_success = $this->checkLogin();

        if (is_error($login_success)) {

            return $this->result($login_success['errno'], $login_success['message']);

        }





    }



    public function getPriceByTime($storeid)

    {

        global $_W, $_GPC;



        $list = pdo_fetchall("SELECT * FROM " . tablename('weisrc_dish_deliverytime') . " WHERE storeid=:storeid order by id", array(':storeid' => $storeid));

        $price = 0;



        foreach ($list as $key => $val) {

            $nowtime = intval(date("Hi"));

            $begintime = intval(str_replace(':', '', $val['begintime']));

            $endtime = intval(str_replace(':', '', $val['endtime']));



            if ($begintime < $endtime) { //开始时间小于结束时间

                if ($nowtime >= $begintime && $nowtime <= $endtime) { //开始时间小于现在时间

                    $price = floatval($val['price']);

                }

            } else {

                if ($begintime <= $nowtime || $nowtime <= $endtime) {

                    $price = floatval($val['price']);

                }

            }

        }

        return $price;

    }



    public function getQuickNum($storeid)

    {

        $order = pdo_fetch("SELECT quicknum FROM " . tablename($this->table_order) . " WHERE storeid=:storeid AND dining_mode=4 ORDER BY id DESC LIMIT 1", array(':storeid' => $storeid));

        if ($order) {

            $quicknum = intval($order['quicknum']);

            $quicknum++;

            if ($quicknum > 998) {

                $quicknum = 1;

            }

            $quicknum = str_pad($quicknum, 3, "0", STR_PAD_LEFT);

        } else {

            $quicknum = '001';

        }

        return $quicknum;

    }



    function getDistance($lat1, $lng1, $lat2, $lng2, $len_type = 1, $decimal = 2)

    {

        $radLat1 = $lat1 * M_PI / 180;

        $radLat2 = $lat2 * M_PI / 180;

        $a = $lat1 * M_PI / 180 - $lat2 * M_PI / 180;

        $b = $lng1 * M_PI / 180 - $lng2 * M_PI / 180;



        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));

        $s = $s * EARTH_RADIUS;

        $s = round($s * 1000);

        if ($len_type > 1) {

            $s /= 1000;

        }

        $s /= 1000;

        return round($s, $decimal);

    }



    public function getNewLimitPrice($storeid, $price, $mode)

    {

        $strwhere = "  where storeid = :storeid AND weid=:weid AND :price>=gmoney AND type=3 AND :time<endtime ";

        if ($mode == 1) { //店内

            $strwhere .= " AND is_meal=1 ";

        } else if ($mode == 2) { //外卖

            $strwhere .= " AND is_delivery=1 ";

        } else if ($mode == 3) { //预定

            $strwhere .= " AND is_reservation=1 ";

        } else if ($mode == 4) { //快餐

            $strwhere .= " AND is_snack=1 ";

        }



        $coupon = pdo_fetch("select * from " . tablename($this->table_coupon) . " {$strwhere} ORDER BY gmoney desc,id DESC LIMIT 1", array(':storeid' => $storeid, ':weid' => $this->_weid, ':price' => $price, ':time' => TIMESTAMP));

        return $coupon;

    }



    public function getOldLimitPrice($storeid, $price, $mode)

    {

        $strwhere = "  where storeid = :storeid AND weid=:weid AND :price>=gmoney AND type=4 AND :time<endtime ";

        if ($mode == 1) { //店内

            $strwhere .= " AND is_meal=1 ";

        } else if ($mode == 2) { //外卖

            $strwhere .= " AND is_delivery=1 ";

        } else if ($mode == 3) { //预定

            $strwhere .= " AND is_reservation=1 ";

        } else if ($mode == 4) { //快餐

            $strwhere .= " AND is_snack=1 ";

        }



        $coupon = pdo_fetch("select * from " . tablename($this->table_coupon) . " {$strwhere} ORDER BY gmoney desc,id DESC LIMIT 1", array(':storeid' => $storeid, ':weid' => $this->_weid, ':price' => $price, ':time' => TIMESTAMP));

        return $coupon;

    }



    public function isNewUser($storeid)

    {

        $isnewuser = 1;

        $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE weid=:weid AND storeid=:storeid AND from_user=:from_user AND

status<>-1 ORDER BY id DESC LIMIT 1", array(':from_user' => $this->_fromuser, ':weid' => $this->_weid, ':storeid' => $storeid));

        if ($order) {

            $isnewuser = 0;

        }

        return $isnewuser;

    }



    /*

     * @param unknown $fromtype

     */

    public function addOrderLog($orderid = 0, $username, $usertype = 1, $fromtype = 1, $paytype = 1, $oldprice = '', $newprice = '')

    {

        global $_GPC, $_W;

        $weid = $this->_weid;

        $payarr = array(

            '1' => '提交订单',

            '2' => '支付订单',

            '3' => '确认订单',

            '4' => '完成订单',

            '5' => '取消订单',

            '6' => '退款',

            '7' => '改价',

            '8' => '开启订单',

            '9' => '扫码收货',//消费者

            '10' => '接单配送',//配送员

            '11' => '收款',//配送员

        );

        $userarr = array('1' => '用户', '2' => '管理员', '3' => '配送员');

        $content = $userarr[$usertype] . $username . $payarr[$paytype];

        if ($paytype == 7) {

            $content = $content . '，' . $oldprice . '改为' . $newprice . '。';

        }

        $data = array(

            'weid' => $weid,

            'orderid' => $orderid,

            'content' => $content,

            'fromtype' => $fromtype,

            'status' => 0,

            'dateline' => TIMESTAMP

        );

        pdo_insert($this->table_order_log, $data);

    }



    public function doPageCleanCart()

    {

        global $_W, $_GPC;

        $weid = $this->_weid;

        $from_user = $this->_fromuser;

        $storeid = intval($_GPC['storeid']);



        $login_success = $this->checkLogin();

        if (is_error($login_success)) {

            return $this->result($login_success['errno'], $login_success['message']);

        }



        if (empty($storeid)) {

            message('请先选择门店');

        }



        pdo_delete('weisrc_dish_cart', array('weid' => $weid, 'from_user' => $from_user, 'storeid' => $storeid));

        header("Content-type: application/json; charset=utf-8");

        return $this->result(0, '操作成功');

    }



    public function doPageDetail()

    {

        global $_W, $_GPC;

        $weid = $this->_weid;

        $from_user = $this->_fromuser;

        $setting = $this->getSetting();



        $id = intval($_GPC['id']);

        $item = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . " where weid = :weid AND id=:id ORDER BY displayorder DESC", array(':weid' => $weid, ':id' => $id));



        if (empty($item)) {

            return $this->result(1, '店面不存在!');

        }



        if ($item['is_show'] != 1) {

            return $this->result(1, '门店暂停营业中,暂不接单!');

        }

        $title = $item['title'];



        $agentid = intval($_GPC['agentid']);

        $agentid2 = 0;

        $agentid3 = 0;



        $nickname = $_W['fans']['nickname'];

        $headimgurl = $_W['fans']['avatar'];

        $fans = $this->getFansByOpenid($from_user);

        if ($agentid != 0) {

            $agent = pdo_fetch("SELECT * FROM " . tablename($this->table_fans) . " WHERE id=:id AND weid=:weid LIMIT 1", array(':id' => $agentid, ':weid' => $weid));

            $agent = $this->getFansById($agentid);

            if ($setting['commission_mode'] == 2) { //代理商模式

                if ($agent['is_commission'] != 2) {//用户不是代理商重新查找

                    $agent = $this->getFansById($agent['agentid']);

                    $agentid = intval($agent['id']);

                }

            }



            if (!empty($agent['agentid'])) {

                $agentid2 = intval($agent['agentid']);

                $agent2 = $this->getFansById($agentid2);

                if (!empty($agent2['agentid'])) {

                    $agentid3 = intval($agent2['agentid']);

                }

            }

        }



        if ($this->_accountlevel == 4) {

            if (empty($fans) && !empty($nickname)) {

                $insert = array(

                    'weid' => $weid,

                    'from_user' => $from_user,

                    'nickname' => $nickname,

                    'headimgurl' => $headimgurl,

                    'agentid' => $agentid,

                    'agentid2' => $agentid2,

                    'agentid3' => $agentid3,

                    'dateline' => TIMESTAMP

                );

                pdo_insert($this->table_fans, $insert);

            }

        } else {

            if (empty($fans) && !empty($from_user)) {

                $insert = array(

                    'weid' => $weid,

                    'from_user' => $from_user,

                    'agentid' => $agentid,

                    'agentid2' => $agentid2,

                    'agentid3' => $agentid3,

                    'dateline' => TIMESTAMP

                );

                pdo_insert($this->table_fans, $insert);

            }

        }

        $fans = $this->getFansByOpenid($from_user);



        $lat = trim($_GPC['lat']);

        $lng = trim($_GPC['lng']);

        $isposition = 0;

        if (!empty($lat) && !empty($lng)) {

            $isposition = 1;

            setcookie($this->_lat, $lat, TIMESTAMP + 3600 * 12);

            setcookie($this->_lng, $lng, TIMESTAMP + 3600 * 12);

            pdo_update($this->table_fans, array('lat' => $lat, 'lng' => $lng), array('id' => $fans['id']));

        }



        $collection = pdo_fetch("SELECT * FROM " . tablename($this->table_collection) . " where weid = :weid AND storeid=:storeid AND from_user=:from_user LIMIT 1", array(':weid' => $weid, ':storeid' => $id, ':from_user' => $from_user));



//智能点餐

        $intelligents = pdo_fetchall("SELECT 1 FROM " . tablename($this->table_intelligent) . " WHERE weid={$weid} AND storeid={$id} GROUP BY name ORDER by name");



        $feedbacklist = pdo_fetchall("SELECT a.*,f.nickname as nickname FROM " . tablename($this->table_feedback) . " a LEFT JOIN " . tablename($this->table_fans) . " f ON a.from_user=f.from_user AND a.weid=f.weid WHERE a.weid=:weid AND a.storeid=:storeid  ORDER by a.id DESC LIMIT 5", array(':storeid' => $id, ':weid' => $weid));



        $feedbackcount = pdo_fetchcolumn("SELECT count(1) FROM " . tablename($this->table_feedback) .

            " a INNER JOIN " . tablename($this->table_fans) . " f ON a

.from_user=f.from_user AND a.weid=f.weid WHERE a.weid=:weid AND a.storeid=:storeid", array(':storeid' => $id, ':weid' =>

            $weid));

        $couponlist = pdo_fetchall("select * from " . tablename($this->table_coupon) . " WHERE storeid=:storeid AND :time<endtime AND type=3 ORDER BY gmoney desc,id DESC LIMIT 10", array(':storeid' => $item['id'], ':time' => TIMESTAMP));

        foreach ($couponlist as $key => $value) {

            $newlimitprice .= $value['title'] . ';';

        }

        $couponlist = pdo_fetchall("select * from " . tablename($this->table_coupon) . " WHERE storeid=:storeid AND :time<endtime AND type=4 ORDER BY gmoney

desc,id DESC LIMIT 10", array(':storeid' => $item['id'], ':time' => TIMESTAMP));

        foreach ($couponlist as $key3 => $value3) {

            $oldlimitprice .= $value3['title'] . ';';

        }



        $btn_count = 0;

        if ($item['is_reservation'] == 1) {

            $jump_url = $this->createMobileUrl('reservationIndex', array('storeid' => $item['id'], 'mode' => 3), true);

            $btn_count++;

        }

        if ($item['is_meal'] == 1) {

            $jump_url = '';

            $btn_count++;

        }

        if ($item['is_delivery'] == 1) {

            $jump_url = $this->createMobileUrl('waplist', array('storeid' => $item['id'], 'mode' => 2), true);

            $btn_count++;

        }

        if ($item['is_snack'] == 1) {

            $jump_url = $this->createMobileUrl('waplist', array('storeid' => $item['id'], 'mode' => 4), true);

            $btn_count++;

        }

        if ($item['is_queue'] == 1) {

            $jump_url = $this->createMobileUrl('queue', array('storeid' => $item['id']), true);

            $btn_count++;

        }

        if ($item['is_savewine'] == 1) {

            $jump_url = $this->createMobileUrl('savewineform', array('storeid' => $item['id']), true);

            $btn_count++;

        }

        if ($item['is_shouyin'] == 1) {

            $jump_url = $this->createMobileUrl('payform', array('storeid' => $item['id']), true);

            $btn_count++;

        }



        $slides = iunserializer($item['thumbs']);

        foreach ($slides as $key => $value) {

            $slides[$key]['image'] = tomedia($value['image']);

        }



        $item['thumbs'] = $slides;

        $item['logo'] = tomedia($item['logo']);



        $is_online_pay = 0;

        if ($item['wechat'] == 1 || $item['alipay'] == 1) {

            $is_online_pay = 1;

        }



        header("Content-type: application/json; charset=utf-8");

        return $this->result(0, '', array('item' => $item));

    }



    public function doPageList()

    {

        global $_W, $_GPC;

        $weid = $this->_weid;

        $from_user = $this->_fromuser;

        $tablesid = intval($_GPC['tablesid']);



        $login_success = $this->checkLogin();

        if (is_error($login_success)) {

            return $this->result($login_success['errno'], $login_success['message']);

        }



        $title = '全部商品';

        $mode = intval($_GPC['mode']);

        $append = intval($_GPC['append']);

        $storeid = intval($_GPC['storeid']);

        $nickname = $_W['fans']['nickname'];

        $headimgurl = $_W['fans']['avatar'];



        $setting = $this->getSetting();



        if ($storeid == 0) {

            $storeid = $this->getStoreID();

        }

        if (empty($storeid)) {

            return $this->result(1, '请先选择门店!');

        }

        if ($mode == 0) {

            return $this->result(1, '请先选择下单模式!');

        }



        $agentid = intval($_GPC['agentid']);

        $agentid2 = 0;

        $agentid3 = 0;



        $fans = $this->getFansByOpenid($from_user);

        if ($agentid != 0) {

            $agent = pdo_fetch("SELECT * FROM " . tablename($this->table_fans) . " WHERE id=:id AND weid=:weid LIMIT 1", array(':id' => $agentid, ':weid' => $weid));

            $agent = $this->getFansById($agentid);

            if ($setting['commission_mode'] == 2) { //代理商模式

                if ($agent['is_commission'] != 2) {//用户不是代理商重新查找

                    $agent = $this->getFansById($agent['agentid']);

                    $agentid = intval($agent['id']);

                }

            }



            if (!empty($agent['agentid'])) {

                $agentid2 = intval($agent['agentid']);

                $agent2 = $this->getFansById($agentid2);

                if (!empty($agent2['agentid'])) {

                    $agentid3 = intval($agent2['agentid']);

                }

            }

        }



        if ($this->_accountlevel == 4) {

            if (empty($fans) && !empty($nickname)) {

                $insert = array(

                    'weid' => $weid,

                    'from_user' => $from_user,

                    'nickname' => $nickname,

                    'headimgurl' => $headimgurl,

                    'agentid' => $agentid,

                    'agentid2' => $agentid2,

                    'agentid3' => $agentid3,

                    'dateline' => TIMESTAMP

                );

                pdo_insert($this->table_fans, $insert);

            }

        } else {

            if (empty($fans) && !empty($from_user)) {

                $insert = array(

                    'weid' => $weid,

                    'from_user' => $from_user,

                    'agentid' => $agentid,

                    'agentid2' => $agentid2,

                    'agentid3' => $agentid3,

                    'dateline' => TIMESTAMP

                );

                pdo_insert($this->table_fans, $insert);

            }

        }

        $fans = $this->getFansByOpenid($from_user);





        $follow_url = $setting['follow_url'];

        if (empty($from_user)) {

            if (!empty($setting['follow_url'])) {

                header("location:$follow_url");

            }

        }



        $sub = 0;

        if ($this->_accountlevel == 4) {

            $userinfo = $this->getUserInfo($from_user);

            if ($userinfo['subscribe'] == 1) {

                $sub = 1;

            }

        } else {

            if ($_W['fans']['follow'] == 1) {

                $sub = 1;

            }

        }



        if ($sub == 0) {

            if ($setting['isneedfollow'] == 1) {

                if (!empty($follow_url)) {

                    header("location:$follow_url");

                } else {

                    return $this->result(1, '请先关注公众号!');

                }

            }

        }



        $iscard = $this->get_sys_card($from_user);

        if ($mode == 1) {

            $table = pdo_fetch("SELECT * FROM " . tablename($this->table_tables) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $tablesid));

            if (empty($table)) {

                return $this->result(1, '餐桌不存在!');

            } else {

                $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $table['tablezonesid']));

                if (empty($tablezones)) {

                    return $this->result(1, '餐桌类型不存在!');

                }

                $table_title = $tablezones['title'] . '-' . $table['title'];

                pdo_update($this->table_tables, array('status' => 1), array('id' => $tablesid));

                pdo_insert($this->table_tables_order, array('from_user' => $from_user, 'weid' => $weid, 'tablesid' => $tablesid, 'storeid' => $storeid, 'dateline' => TIMESTAMP));

            }

        }



        $this->resetHour();

        $store = $this->getStoreById($storeid);

        $collection = pdo_fetch("SELECT * FROM " . tablename($this->table_collection) . " where weid = :weid AND storeid=:storeid AND from_user=:from_user LIMIT 1", array(':weid' => $weid, ':storeid' => $storeid, ':from_user' => $from_user));



        $isrest = 0;

        if ($mode != 3 && $mode != 5) {

            if ($store['is_rest'] == 0) {

                $isrest = 1;

            }

        }

        if ($store['is_show'] != 1) {

            return $this->result(1, '门店暂停营业中,暂不接单!');

        }

        if ($mode == 1) { //店内

            if ($store['is_meal'] == 0) {

                return $this->result(1, '商家已经关闭店内点餐模式，您暂时不能使用!');

            }

        }

        if ($mode == 2) { //外卖

            if ($store['is_delivery'] == 0) {

                return $this->result(1, '商家已经关闭外卖功能，您暂时不能使用!');

            }

        }

        if ($mode == 4) {

            if ($store['is_snack'] == 0) {

                return $this->result(1, '商家已经关闭快餐功能，您暂时不能使用!');

            }

        }

        if ($mode == 3) {

            if ($store['is_reservation'] == 0) {

                return $this->result(1, '商家已经关闭预定功能，您暂时不能使用!');

            }

        }



        $pindex = max(1, intval($_GPC['page']));

        $psize = 20;

        $condition = '';



        if ($mode == 1 || $mode == 5) {

            $condition .= " AND is_meal=1 ";

        } elseif ($mode == 2) {

            $condition .= " AND is_delivery=1 ";

        } elseif ($mode == 3) {

            $condition .= " AND is_reservation=1 ";

        } elseif ($mode == 4) {

            $condition .= " AND is_snack=1 ";

        }



        $children = array();

        $category = pdo_fetchall("SELECT * FROM " . tablename($this->table_category) . " WHERE weid = :weid AND storeid=:storeid {$condition} ORDER BY

displayorder DESC,id DESC", array(':weid' => $weid, ':storeid' => $storeid));



        $cid = intval($category[0]['id']);

        $week = date("w");

        $goodslist = array();

        foreach ($category as $key => $value) {

            $goods = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . " WHERE weid = '{$weid}' AND  storeid={$storeid} AND status = '1'

AND deleted=0 AND pcate=:pcate AND find_in_set(" . $week . ",week) ORDER BY displayorder DESC, subcount DESC, id DESC ", array(':pcate' => $value['id']));

            foreach ($goods as $k => $v) {

                $goods[$k]['thumb'] = tomedia($v['thumb']);

                if ($v['istime'] == 1) {

                    if ($v['begindate'] > TIMESTAMP || TIMESTAMP > $v['enddate']) {

                        unset($goods[$k]);

                    }

                    $goodsstate = $this->check_hourtime($v['begintime'], $v['endtime']);

                    if ($goodsstate == 0) {

                        unset($goods[$k]);

                    }

                }



                if ($v['isoptions'] == 1) {

                    //查询商品是否存在

                    $goodsid = intval($v['id']); //商品id

                    $allgoodsOptions = $this->getAllGoodsOption($goodsid);

                    if (!empty($allgoodsOptions)) {

                        $goodsgroup = array();

                        foreach ($allgoodsOptions as $keytmp => $valtmp) {

                            if (!in_array($valtmp['start'], $goodsgroup)) {

                                $goodsgroup[] = $valtmp['start'];

                            }

                        }



                        $goodsoptions = array();

                        foreach ($goodsgroup as $key1 => $val) {

                            $goodsoption = array();

                            foreach ($allgoodsOptions as $key2 => $val2) {

                                if ($val == $val2['start']) {

                                    $goodsoption[] = array(

                                        'optionid' => $val2['id'],

                                        'optionname' => $val2['title'],

                                        'optionprice' => $val2['price']

                                    );

                                }

                            }

                            $goodsoptions[] = array('groupname' => $val, 'options' => $goodsoption);

                        }



                        $iscard = $this->get_sys_card($this->_fromuser);

                        $goodsitem['dprice'] = $v['marketprice'];

                        if ($iscard == 1 && !empty($v['memberprice'])) {

                            $goodsitem['dprice'] = $v['memberprice'];

                        }

                        $goods[$k]['goodsoptions'] = $goodsoptions;

                    }

                }

            }

            if ($goods) {

                $goodslist[] = array(

                    'id' => $value['id'],

                    'title' => $value['name'],

                    'goods' => $goods

                );

            } else {

                unset($category[$key]);

            }

        }

        $catecount = count($category);

        $cateheight = (($catecount + 1) * 62) + 200;



        $dish_arr = $this->getDishCountInCart($storeid);

        $cart = pdo_fetchall("SELECT * FROM " . tablename($this->table_cart) . " WHERE  storeid=:storeid AND from_user=:from_user AND weid=:weid", array(':storeid' => $storeid, ':from_user' => $from_user, ':weid' => $weid));

        $totalcount = 0;

        $totalprice = 0;

        foreach ($cart as $key => $value) {

            $goods_t = pdo_fetch("SELECT * FROM " . tablename($this->table_goods) . " WHERE id = :id LIMIT 1 ", array(':id' => $value['goodsid']));

            $cart[$key]['goodstitle'] = $goods_t['title'];

            $totalcount = $totalcount + $value['total'];

            $totalprice = $totalprice + $value['total'] * $value['price'];

        }



        $jump_url = $this->createMobileurl('wapmenu', array('from_user' => $from_user, 'storeid' => $storeid, 'mode' => $mode), true);

        $limitprice = 0;

        $is_add_order = 0;

        if ($mode == 1) {

            if ($append == 0) {

                $limitprice = floatval($tablezones['limit_price']);

            }

            $jump_url = $this->createMobileurl('wapmenu', array('from_user' => $from_user, 'storeid' => $storeid, 'mode' => $mode, 'tablesid' => $tablesid, 'append' => $append, 'orderid' => intval($_GPC['orderid'])), true);



            $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE weid=:weid AND from_user=:from_user AND dining_mode=1 AND

status<>3 AND status<>-1 ORDER BY id DESC LIMIT 1", array(':from_user' => $from_user, ':weid' => $weid));

            if ($order) {

                $is_add_order = 1;

            }

        } elseif ($mode == 2) {

            $limitprice = floatval($store['sendingprice']);

        } elseif ($mode == 3) {

            $is_reservation_goods = 2;

            $timeid = intval($_GPC['timeid']);

            $select_date = trim($_GPC['selectdate']);

            $time = pdo_fetch("SELECT * FROM " . tablename($this->table_reservation) . " WHERE weid = :weid AND storeid =:storeid AND id=:id ORDER BY id LIMIT 1", array(':weid' => $this->_weid, ':storeid' => $storeid, ':id' => $timeid));

            if (!empty($time)) {

                $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " WHERE weid = :weid AND storeid =:storeid AND id=:id ORDER BY id LIMIT 1", array(':weid' => $this->_weid, ':storeid' => $storeid, ':id' => $time['tablezonesid']));

                $limitprice = floatval($tablezones['limit_price']);

            }

            $jump_url = $this->createMobileUrl('reservationdetail', array('storeid' => $storeid, 'mode' => 3, 'selectdate' => $select_date, 'timeid' => $timeid, 'rtype' => 2), true);

        } elseif ($mode == 5) {//排队

            $jump_url = $this->createMobileurl('queue', array('from_user' => $from_user, 'storeid' => $storeid), true);

        }



        $is_not_exists = 0;

        if (!$this->getmodules()) {

            $is_not_exists = 1;

        }



        //智能点餐

        $intelligents = pdo_fetchall("SELECT 1 FROM " . tablename($this->table_intelligent) . " WHERE weid=:weid AND storeid=:storeid GROUP BY name ORDER by name", array(':weid' => $weid, ':storeid' => $storeid));



        $ispop = 0;
        if ($setting['tiptype'] == 1) { //关注后隐藏

            if ($sub == 0) {

                $ispop = 1;

            }

        } else if ($setting['tiptype'] == 2) {

            $ispop = 1;

        }



        if ($store['btn_coupon_type'] == 1 && $store['btn_coupon_id']) {

            $coupon = pdo_fetch("SELECT * FROM " . tablename($this->table_coupon) . " WHERE id=:id LIMIT 1", array(':id' => $store['btn_coupon_id']));

            $is_coupon_show = 1;

            if (empty($coupon)) {

                $is_coupon_show = 0;

            } else {

                if (TIMESTAMP < $coupon['starttime']) {

                    $is_coupon_show = 0;

                }

                if (TIMESTAMP > $coupon['endtime']) {

                    $is_coupon_show = 0;

                }

            }

        }



        $follow_title = !empty($setting['follow_title']) ? $setting['follow_title'] : "立即关注";

        $follow_desc = !empty($setting['follow_desc']) ? $setting['follow_desc'] : "欢迎关注智慧点餐点击马上加入,

助力品牌推广 ";

        $follow_image = !empty($setting['follow_logo']) ? tomedia($setting['follow_logo']) : tomedia("../addons/weisrc_dish/icon.jpg");

        $tipqrcode = tomedia($setting['tipqrcode']);

        $tipbtn = intval($setting['tipbtn']);

        $follow_url = $setting['follow_url'];



        $allgoods = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . " WHERE weid = '{$weid}' AND  storeid={$storeid} AND status = '1'

AND deleted=0 AND find_in_set(" . $week . ",week) ORDER BY displayorder DESC, subcount DESC, id DESC ");



        foreach ($allgoods as $k => $v) {

            if ($v['isoptions'] == 1) {

                //查询商品是否存在

                $goodsid = intval($v['id']); //商品id

                $allgoodsOptions = $this->getAllGoodsOption($goodsid);

                if (!empty($allgoodsOptions)) {

                    $goodsgroup = array();

                    foreach ($allgoodsOptions as $keytmp => $valtmp) {

                        if (!in_array($valtmp['start'], $goodsgroup)) {

                            $goodsgroup[] = $valtmp['start'];

                        }

                    }



                    $goodsoptions = array();

                    foreach ($goodsgroup as $key1 => $val) {

                        $goodsoption = array();

                        foreach ($allgoodsOptions as $key2 => $val2) {

                            if ($val == $val2['start']) {

                                $goodsoption[] = array(

                                    'optionid' => $val2['id'],

                                    'optionname' => $val2['title'],

                                    'optionprice' => $val2['price']

                                );

                            }

                        }

                        $goodsoptions[] = array('groupname' => $val, 'options' => $goodsoption);

                    }



                    $iscard = $this->get_sys_card($this->_fromuser);

                    $goodsitem['dprice'] = $v['marketprice'];

                    if ($iscard == 1 && !empty($v['memberprice'])) {

                        $goodsitem['dprice'] = $v['memberprice'];

                    }

                    $allgoods[$k]['goodsoptions'] = $goodsoptions;

                }

            }

        }



        header("Content-type: application/json; charset=utf-8");

        return $this->result(0, '', array('goodslist' => $goodslist, 'store' => $store, 'cart' => $cart, 'totalprice'

        => $totalprice, 'totalcount' => $totalcount, 'allgoods' => $allgoods));

    }



    public function doPageGetgoodsoption()

    {

        global $_GPC, $_W;

        //查询商品是否存在

        $id = intval($_GPC['id']); //商品id

        $goods = $this->getAllGoodsOption($id);



        if (empty($goods)) {

            echo json_encode(0);

        } else {

            $goodsgroup = array();

            foreach ($goods as $key => $value) {

                if (!in_array($value['start'], $goodsgroup)) {

                    $goodsgroup[] = $value['start'];

                }

            }



            $goodsoptions = array();

            foreach ($goodsgroup as $key1 => $val) {

                $goodsoption = array();

                foreach ($goods as $key2 => $val2) {

                    if ($val == $val2['start']) {

                        $goodsoption[] = array('optionid' => $val2['id'], 'optionname' => $val2['title']);

                    }

                }

                $goodsoptions[] = array('groupname' => $val, 'options' => $goodsoption);

            }



            $goodsitem = $this->getGoodsById($id);

            $iscard = $this->get_sys_card($this->_fromuser);

            $goodsitem['dprice'] = $goodsitem['marketprice'];

            if ($iscard == 1 && !empty($goodsitem['memberprice'])) {

                $goodsitem['dprice'] = $goodsitem['memberprice'];

            }



            header("Content-type: application/json; charset=utf-8");

            return $this->result(0, '', array('price' => $goodsitem['dprice'], 'title' => $goodsitem['title'], 'goodsoptions' => $goodsoptions));

        }

    }



    public function doPageSelectOption()

    {

        global $_GPC, $_W;

        //查询商品是否存在

        $id = intval($_GPC['goodsid']); //商品id

        $optionid = trim($_GPC['optionid']);



        $goods = $this->getGoodsById($id);

        if (empty($goods)) {

            echo json_encode(0);

        } else {

            $iscard = $this->get_sys_card($this->_fromuser);

            $goods['dprice'] = $goods['marketprice'];

            if ($iscard == 1 && !empty($goods['memberprice'])) {

                $goods['dprice'] = $goods['memberprice'];

            }



            $optionids = explode('_', $optionid);

            $optionprice = 0;



            if (count($optionids) > 0) {

                $optionprice = pdo_fetchcolumn("SELECT sum(price) FROM " . tablename("weisrc_dish_goods_option") . "  WHERE id IN ('" . implode("','", $optionids) . "')");

            }

            $goods['price'] = floatval($goods['dprice']) + floatval($optionprice);



            header("Content-type: application/json; charset=utf-8");

            return $this->result(0, '', array('price' => $goods['price']));

        }

    }



    public function getGoodsById($id)

    {

        $item = pdo_fetch("SELECT * FROM " . tablename($this->table_goods) . " WHERE id=:id", array(":id" => $id));

        return $item;

    }



    public function getAllGoodsOption($id)

    {

        $item = pdo_fetchall("SELECT * FROM " . tablename("weisrc_dish_goods_option") . " WHERE goodsid=:goodsid

        ORDER BY displayorder DESC", array(":goodsid" => $id));

        return $item;

    }



    public function doPageUsercenter()

    {

        global $_W, $_GPC;

        $weid = $this->_weid;

        $from_user = $this->_fromuser;

        $setting = $this->getSetting();



        $agentid = intval($_GPC['agentid']);

        $agentid2 = 0;

        $agentid3 = 0;



        $is_savewine = 0;

        $is_savewine_store = pdo_fetch("select * from " . tablename($this->table_stores) . " WHERE weid =:weid AND is_savewine=1 AND deleted=0 LIMIT 1", array(':weid' => $weid));

        if ($is_savewine_store) {

            $is_savewine = 1;

        }



        $is_permission = false;

        $tousers = explode(',', $setting['tpluser']);

        if (in_array($from_user, $tousers)) {

            $is_permission = true;

        }

        if ($is_permission == false) {

            $accounts = pdo_fetchall("SELECT storeid FROM " . tablename($this->table_account) . " WHERE weid = :weid AND from_user=:from_user AND

 status=2 ORDER BY id DESC ", array(':weid' => $this->_weid, ':from_user' => $from_user));

            if ($accounts) {

                $arr = array();

                foreach ($accounts as $key => $val) {

                    $arr[] = $val['storeid'];

                }

                $storeids = implode(',', $arr);

                $is_permission = true;

            }

        }



        $count = pdo_fetchcolumn("SELECT count(1) FROM " . tablename($this->table_stores) . " a INNER JOIN " . tablename($this->table_collection) . " b ON a.id = b.storeid where  a.weid = :weid and is_show=1 and b.from_user=:from_user ORDER BY a.displayorder DESC, a.id DESC", array(':weid' => $weid, ':from_user' => $from_user));



        load()->model('mc');

        $user = mc_fetch($from_user);

        $score = intval($user['credit1']); //剩余积分

        $coin = $user['credit2']; //余额

        $coin = empty($coin) ? '0.00' : $coin;

        $fans = $this->getFansByOpenid($this->_fromuser);



        header("Content-type: application/json; charset=utf-8");

        return $this->result(0, '', array('setting' => $setting, 'fans' => $fans));

    }



    public function doPageAddress()

    {

        global $_W, $_GPC;

        $weid = $this->_weid;

        $from_user = $this->_fromuser;

        $setting = $this->getSetting();





        return $this->result(0, '', array('setting' => $setting, 'fans' => $fans));

    }



    public function doPageSaveMember()

    {

        global $_W, $_GPC;

        $login_success = $this->checkLogin();

        if (is_error($login_success)) {

            return $this->result($login_success['errno'], $login_success['message']);

        }

        $fans = $this->getFansByOpenid($this->_fromuser);

        $agentid = 0;

        $agentid2 = 0;

        $agentid3 = 0;



        if (empty($fans)) {

            $data = array(

                'weid' => $_W['uniacid'],

                'from_user' => $_W['openid'],

                'nickname' => $_W['fans']['nickname'],

                'headimgurl' => $_W['fans']['avatar'],

                'agentid' => $agentid,

                'agentid2' => $agentid2,

                'agentid3' => $agentid3,

                'dateline' => TIMESTAMP

            );

            pdo_insert($this->table_fans, $data);

            $id = pdo_insertid();

            if (empty($id)) {

                return $this->result(0, '', array('code' => '404'));

            } else {

                return $this->result(0, '', array('code' => '101'));

            }

        } else {

            return $this->result(0, '', array('code' => '101'));

        }

    }



    private function checkLogin()

    {

        global $_W;



//        if (empty($_W['fans'])) {

        if (empty($this->_fromuser)) {

            return error(1, '请先登录');

        }

        return true;

    }



    public function getSlidesByPos($pos = 2)

    {

        $datas = pdo_fetchall("SELECT * FROM " . tablename($this->table_ad) . " WHERE uniacid = :uniacid AND position=:position AND status=1 AND :time >starttime

 AND :time < endtime  ORDER BY displayorder DESC,id DESC LIMIT 6", array(':uniacid' => $this->_weid, ':time' =>

            TIMESTAMP, ':position' => $pos));



        foreach ($datas as $key => $value) {

            $datas[$key]['thumb'] = tomedia($value['thumb']);

        }

        return $datas;

    }



    public function getShoptypes()

    {

        $datas = pdo_fetchall("SELECT * FROM " . tablename($this->table_type) . " where weid = :weid ORDER BY displayorder DESC", array(':weid' => $this->_weid), 'id');

        foreach ($datas as $key => $value) {

            $datas[$key]['thumb'] = tomedia($value['thumb']);

        }

        return $datas;

    }



    public function getSetting()

    {

        global $_W, $_GPC;

        $this->_weid = $this->_weid;

        $setting = pdo_fetch("SELECT * FROM " . tablename($this->table_setting) . " where weid = :weid LIMIT 1", array(':weid' => $this->_weid));

        return $setting;

    }



    public function getFansByOpenid($openid)

    {

        $fans = pdo_fetch("SELECT * FROM " . tablename($this->table_fans) . " WHERE from_user=:from_user AND weid=:weid LIMIT 1", array(':from_user' => $openid, ':weid' => $this->_weid));

        return $fans;

    }



    public function getFansById($id)

    {

        $fans = pdo_fetch("SELECT * FROM " . tablename($this->table_fans) . " WHERE id=:id AND weid=:weid LIMIT 1", array(':id' => $id, ':weid' => $this->_weid));

        return $fans;

    }



    public function formatMoney($money)

    {

        if ($money >= 10000) {

            return sprintf("%.2f", $money / 10000) . '万';

        } else {

            return $money;

        }

    }



    public function getmodules()

    {

        return pdo_tableexists('modules_reply');

    }



    public function getStoreById($id)

    {

        $store = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . " WHERE id=:id LIMIT 1", array(':id' => $id));

        return $store;

    }



    public function getOrderById($id)

    {

        $item = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id=:id LIMIT 1",

            array(

                ':id' => $id

            ));

        return $item;

    }



    public function get_sys_card($openid)

    {

        $iscard = 0;

        $exists = pdo_tableexists('mc_card_members');

        if ($exists) {

            $mcard = pdo_get('mc_card_members', array('uniacid' => $this->_weid, 'openid' => $openid));

            if ($mcard) {

                $iscard = 1;

            }

        }

        return $iscard;

    }


    

}