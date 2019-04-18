<?php
/*
 * 人人商城
 *
 * 青岛易联互动网络科技有限公司
 * http://www.we7shop.cn
 * TEL: 4000097827/18661772381/15865546761
 */
ini_set('display_errors', 'On');
error_reporting(E_ALL);
require '../../../../../framework/bootstrap.inc.php';
require '../../../../../addons/ewei_shopv2/defines.php';
require '../../../../../addons/ewei_shopv2/core/inc/functions.php';
global $_W, $_GPC;
ignore_user_abort(); //忽略关闭浏览器
set_time_limit(0); //永远执行
$p = com('coupon');
$sets = pdo_fetchall('select uniacid from ' . tablename('ewei_shop_sysset'));
foreach ($sets as $set) {

    $_W['uniacid'] = $set['uniacid'];
    if (empty($_W['uniacid'])) {
        continue;
    }
    $trade = m('common')->getSysset('trade', $_W['uniacid']);

    $days = intval($trade['refunddays']);
    $daytimes = 86400 * $days;


    $orders = pdo_fetchall("select id,couponid from " . tablename('ewei_shop_order') . " where  uniacid={$_W['uniacid']} and status=3 and isparent=0 and couponid<>0 and finishtime + {$daytimes} <=unix_timestamp() ");
    if (!empty($orders)) {
        if ($p) {
            foreach ($orders as $o) {
                //优惠券自动返利
                if (!empty($o['couponid'])) {
                    $p->backConsumeCoupon($o['id']); //自动关闭订单
                }
            }
        }
    }
}




