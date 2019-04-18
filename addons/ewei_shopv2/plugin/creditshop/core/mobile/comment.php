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

class Comment_EweiShopV2Page extends PluginMobileLoginPage {

    public function __construct()
    {
        parent::__construct();
        $trade = m('common')->getSysset('trade');
        if ( !empty($trade['closecomment']) )
        {
            $this->message('不允许评论!','','error');
        }
    }

    function main() {

        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $openid = $_W['openid'];
        $goodsid = intval($_GPC['goodsid']);
        $logid = intval($_GPC['logid']);
        //多商户
        $merch_plugin = p('merch');
        $merch_data = m('common')->getPluginset('merch');
        if ($merch_plugin && $merch_data['is_openmerch']) {
            $is_openmerch = 1;
        } else {
            $is_openmerch = 0;
        }
        $merchid = intval($_GPC['merchid']);
        $condition = " log.uniacid = ".$uniacid." ";
        if($merchid>0){
            $condition .= " and g.merchid = ".$merchid." ";
        }
        //评论
        $log = pdo_fetch("select log.id,log.status,log.goodsid,g.goodstype,g.type,log.iscomment,log.optionid,g.thumb,o.title as optiontitle,g.title
                ,g.credit,g.money
                from ".tablename('ewei_shop_creditshop_log')." as log
                left join ".tablename('ewei_shop_creditshop_goods')." as g on g.id = log.goodsid
                left join " . tablename('ewei_shop_creditshop_option') . " o on o.id=log.optionid
                where ".$condition." and log.goodsid = ".$goodsid." and log.id = ".$logid." ");
        $log = set_medias($log, 'thumb');
        if(($log['money']-intval($log['money']))==0){
            $log['money'] = intval($log['money']);
        }
        if (empty($log)) {
            header('location: '.mobileUrl('creditshop/log'));
            exit;
        }
        if($log['goodstype']==0){//商品
            if ($log['status'] != 3) {
                $this->message('订单未完成，不能评价!',mobileUrl('creditshop/log/detail',array('id'=>$logid)));
            }
        }elseif($log['goodstype']==1){//优惠券
            if ($log['status'] != 3) {
                $this->message('订单未完成，不能评价!',mobileUrl('creditshop/log/detail',array('id'=>$logid)));
            }
        }elseif($log['goodstype']==2){//余额
            if ($log['status'] != 3) {
                $this->message('订单未完成，不能评价!',mobileUrl('creditshop/log/detail',array('id'=>$logid)));
            }
        }elseif($log['goodstype']==3){//红包
            if ($log['status'] !=3) {
                $this->message('订单未完成，不能评价!',mobileUrl('creditshop/log/detail',array('id'=>$logid)));
            }
        }
        if ($log['iscomment'] >= 2) {
            $this->message('您已经评价过了!',mobileUrl('creditshop/log/detail',array('id'=>$logid)));
        }
        include $this->template();
    }

    function submit() {

        global $_W, $_GPC;
        $openid = $_W['openid'];
        $uniacid = $_W['uniacid'];
        $logid = intval($_GPC['logid']);
        $merchid = intval($_GPC['merchid']);
        $condition = " and uniacid=:uniacid ";
        if($merchid>0){
            $condition .= " and merchid = ".$merchid." ";
        }
        //评论
        $log = pdo_fetch("select id,status,iscomment,logno from " . tablename('ewei_shop_creditshop_log') . ' where id=:id '.$condition.' and openid=:openid limit 1'
            , array(':id' => $logid, ':uniacid' => $uniacid, ':openid' => $openid));
        if (empty($log)) {
            show_json(0, '兑换记录未找到');
        }

        $member = m('member')->getMember($openid);
        $comments = $_GPC['comments'];
        if (!is_array($comments)) {
            show_json(0, '数据出错，请重试!');
        }

        $trade = m('common')->getSysset('trade');
        if (empty($trade['commentchecked'])) {
            $checked = 0;
        } else {
            $checked = 1;
        }

        foreach ($comments as $c) {
            $old_c = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_creditshop_comment') . '
            where uniacid=:uniacid and logid=:logid and goodsid=:goodsid limit 1', array(':uniacid' => $_W['uniacid'], ':goodsid' => $c['goodsid'], ':logid' => $logid));
            if (empty($old_c)) {
                //第一次评论
                $comment = array(
                    'uniacid' => $uniacid,
                    'logid' => $logid,
                    'logno' => $log['logno'],
                    'goodsid' => $c['goodsid'],
                    'level' => $c['level'],
                    'content' => trim($c['content']),
                    'images' => is_array($c['images']) ? iserializer($c['images']) : iserializer(array()),
                    'openid' => $openid,
                    'nickname' => $member['nickname'],
                    'headimg' => $member['avatar'],
                    'time' => time(),
                    'checked' => $checked
                );
                pdo_insert('ewei_shop_creditshop_comment', $comment);
            } else {
                $comment = array(
                    'append_content' => trim($c['content']),
                    'append_images' => is_array($c['images']) ? iserializer($c['images']) : iserializer(array()),
                    'append_checked' => $checked,
                    'append_time' => time()
                );
                pdo_update('ewei_shop_creditshop_comment', $comment, array('uniacid' => $_W['uniacid'], 'goodsid' => $c['goodsid'], 'logid' => $logid));
            }
        }
        if ($log['iscomment'] <= 0) {
            $d['iscomment'] = 1;
        } else {
            $d['iscomment'] = 2;
        }
        pdo_update('ewei_shop_creditshop_log', $d, array('id' => $logid, 'uniacid' => $uniacid));
        show_json(1);
    }

}
