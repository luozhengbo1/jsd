<?php
/*
 * 人人商城
 *
 * 青岛易联互动网络科技有限公司
 * http://www.we7shop.cn
 * TEL: 4000097827/18661772381/15865546761
 */
define('IN_SYS', true);
require __DIR__. '/../framework/bootstrap.inc.php';
load()->web('common');
$uniacid  = $_W['uniacid'] = intval($_GPC['i']);


$_W['attachurl'] = $_W['attachurl_local'] = $_W['siteroot'] . $_W['config']['upload']['attachdir'] . '/';
if (!empty($_W['setting']['remote'][$_W['uniacid']]['type'])) {
    $_W['setting']['remote'] = $_W['setting']['remote'][$_W['uniacid']];
}
if (!empty($_W['setting']['remote']['type'])) {
    if ($_W['setting']['remote']['type'] == ATTACH_FTP) {
        $_W['attachurl'] = $_W['attachurl_remote'] = $_W['setting']['remote']['ftp']['url'] . '/';
    } elseif ($_W['setting']['remote']['type'] == ATTACH_OSS) {
        $_W['attachurl'] = $_W['attachurl_remote'] = $_W['setting']['remote']['alioss']['url'] . '/';
    } elseif ($_W['setting']['remote']['type'] == ATTACH_QINIU) {
        $_W['attachurl'] = $_W['attachurl_remote'] = $_W['setting']['remote']['qiniu']['url'] . '/';
    } elseif ($_W['setting']['remote']['type'] == ATTACH_COS) {
        $_W['attachurl'] = $_W['attachurl_remote'] = $_W['setting']['remote']['cos']['url'] . '/';
    }
}

//$uniacid = $_GPC['i'] = 4;

// check微擎绑定
if(!empty($_GPC['formwe7'])){
    $bind = pdo_fetch('SELECT * FROM '. tablename('ewei_shop_wxapp_bind'). ' WHERE wxapp=:wxapp LIMIT 1', array(':wxapp'=>$uniacid));
    if(!empty($bind) && !empty($bind['uniacid'])){
        $uniacid = $_GPC['i'] = $bind['uniacid'];
    }
}

if(empty($uniacid)){
    die('Access Denied.');
}
$site = WeUtility::createModuleSite('ewei_shopv2');
$_GPC['c']='site';
$_GPC['a']='entry';
$_GPC['m']='ewei_shopv2';
$_GPC['do']='mobile';
$_W['uniacid'] = (int)$_GPC['i'];
$_W['account'] = uni_fetch($_W['uniacid']);
$_W['acid'] = (int)$_W['account']['acid'];
if (!isset($_GPC['r'])){
    $_GPC['r']='app';
}else{
    $_GPC['r']='app.'.$_GPC['r'];
}
if(!is_error($site)) {
    $method = 'doMobileMobile';
    $site->uniacid = $uniacid ;
    $site->inMobile = true;
    if (method_exists($site, $method)) {
        $site->$method();
        exit;
    }
}

