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

class Detail_EweiShopV2Page extends MobilePage {

    function main() {
        global $_W, $_GPC;

        $openid = $_W['openid'];
        $id = intval($_GPC['id']);
        $coupon = pdo_fetch('select * from ' . tablename('ewei_shop_coupon') . ' where id=:id and uniacid=:uniacid  limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
        if (empty($coupon)) {
            header('location: ' . mobileUrl('sale/coupon'));
            exit;
        }
        $coupon = com('coupon')->setCoupon($coupon, time());


        $title2='';
        $title3='';
        if($coupon['coupontype']=='0')
        {
            if($coupon['enough']>0)
            {
                $title2 ='满'.((float)$coupon['enough']).'元可用';
            }else
            {
                $title2 ='无金额门槛';
            }
        }
        elseif($coupon['coupontype']=='1')
        {
            if($coupon['enough']>0)
            {
                $title2 ='充值满'.((float)$coupon['enough']).'元可用';
            }else
            {
                $title2 ='无金额门槛';
            }
        }
        if($coupon['coupontype']=='2')
        {
            if($coupon['enough']>0)
            {
                $title2 ='满'.((float)$coupon['enough']).'元可用';
            }else
            {
                $title2 ='无金额门槛';
            }
        }

        if($coupon['backtype']==0)
        {
            if($coupon['enough']=='0')
            {
                $coupon['color']='orange';
            }
            else
            {
                $coupon['color']='blue';
            }
            $title3='<span class="subtitle nopadding">￥</span>'.((float)$coupon['deduct']);
        }
        if($coupon['backtype']==1)
        {
            $coupon['color']='red ';
            $title3=((float)$coupon['discount']).'折 ';
        }
        if($coupon['backtype']==2)
        {
            if($coupon['coupontype']=='0')
            {
                $coupon['color']='red ';
            }
            else
            {
                $coupon['color']='pink ';
            }

            if (!empty($coupon['backmoney']) && $coupon['backmoney'] > 0) {
                $backmoneytext =  $coupon['backmoney'].'元余额 ';
            }
            if (!empty($coupon['backcredit']) && $coupon['backcredit'] > 0) {
                $backcredittext =  $coupon['backcredit'].'积分 ';
            }
            if (!empty($coupon['backredpack']) && $coupon['backredpack'] > 0) {
                $backredpacktext =  $coupon['backredpack'].'元红包';
            }
        }
        $coupon['title2']= $title2;
        $coupon['title3']= $title3;

        $goods = array();
        $category = array();
        if($coupon['limitgoodtype']!=0)
        {
            if(!empty($coupon['limitgoodids']))
            {
                $where =  'and id in('.$coupon['limitgoodids'].')';
            }
            $goods = pdo_fetchall('select `title` from ' . tablename('ewei_shop_goods') . ' where uniacid=:uniacid '.$where, array(':uniacid' => $_W['uniacid']), 'id');
        }
        if($coupon['limitgoodcatetype']!=0)
        {
            if(!empty($coupon['limitgoodcateids']))
            {
                $where =  'and id in('.$coupon['limitgoodcateids'].')';
            }
            $category = pdo_fetchall('select `name`  from ' . tablename('ewei_shop_category') . ' where uniacid=:uniacid   '.$where, array(':uniacid' => $_W['uniacid'],), 'id');
        }

        //分类限制
        $limitmemberlevels =explode(",", $coupon['limitmemberlevels']);
        $limitagentlevels =explode(",", $coupon['limitagentlevels']);
        $limitpartnerlevels=explode(",", $coupon['limitpartnerlevels']);
        $limitaagentlevels=explode(",", $coupon['limitaagentlevels']);

        //分销商限制
        $hascommission = false;
        $plugin_com = p('commission');
        if ($plugin_com) {
            $plugin_com_set = $plugin_com->getSet();
            $leveltitle2  = $plugin_com_set['texts']['agent'];
            $hascommission = !empty($plugin_com_set['level']);
            if (in_array("0",$limitagentlevels)){
                $commissionname = empty($plugin_com_set['levelname'])?'普通等级':$plugin_com_set['levelname'];
            }
        }

        //股东限制
        $hasglobonus = false;
        $plugin_globonus = p('globonus');
        if ($plugin_globonus) {
            $plugin_globonus_set = $plugin_globonus->getSet();
            $leveltitle3  = $plugin_globonus_set['texts']['partner'];
            $hasglobonus = !empty($plugin_globonus_set['open']);
            if (in_array("0",$limitpartnerlevels)){
                $globonuname = empty($plugin_globonus_set['levelname'])?'普通等级':$plugin_globonus_set['levelname'];
            }
        }

        //区域代理限制
        $hasabonus = false;
        $abonu='';
        $plugin_abonus = p('abonus');
        if ($plugin_abonus) {
            $plugin_abonus_set = $plugin_abonus->getSet();
            $leveltitle4  = $plugin_abonus_set['texts']['aagent'];
            $hasabonus = !empty($plugin_abonus_set['open']);

            if (in_array("0",$limitaagentlevels)){
                $abonuname = empty($plugin_abonus_set['levelname'])?'普通等级':$plugin_abonus_set['levelname'];
            }
        }

        $pass = false;
        if($coupon['islimitlevel'] ==1) {
            $openid = trim($_W['openid']);
            $member = m('member')->getMember($openid);

            if(!empty($coupon['limitmemberlevels'])||$coupon['limitmemberlevels']=='0')
            {
                //会员等级
                $shop = $_W['shopset']['shop'];
                if (in_array("0",$limitmemberlevels)){
                    $meblvname =empty($shop['levelname'])?'普通等级':$shop['levelname'];
                }
                //会员等级
                $level1 = pdo_fetchall('select * from ' . tablename('ewei_shop_member_level') . ' where uniacid=:uniacid and  id in ('.$coupon['limitmemberlevels'].') ', array(':uniacid' => $_W['uniacid']));

                if (in_array($member['level'],$limitmemberlevels)){
                    $pass = true;
                }
            };

            if((!empty($coupon['limitagentlevels'])||$coupon['limitagentlevels']=='0')&&$hascommission) {
                //分销商等级
                $level2 = pdo_fetchall('select * from ' . tablename('ewei_shop_commission_level') . ' where uniacid=:uniacid and id  in ('.$coupon['limitagentlevels'].') ', array(':uniacid' => $_W['uniacid']));

                if($member['isagent']=='1'&&$member['status']=='1')
                {
                    if (in_array($member['agentlevel'],$limitagentlevels)){
                        $pass = true;
                    }
                }
            }

            if((!empty($coupon['limitpartnerlevels'])||$coupon['limitpartnerlevels']=='0')&&$hasglobonus) {
                //股东等级
                $level3 = pdo_fetchall('select * from ' . tablename('ewei_shop_globonus_level') . ' where uniacid=:uniacid and  id in('.$coupon['limitpartnerlevels'].') ', array(':uniacid' => $_W['uniacid']));

                if($member['ispartner']=='1'&&$member['partnerstatus']=='1')
                {
                    if (in_array($member['partnerlevel'],$limitpartnerlevels)){
                        $pass = true;
                    }
                }
            }
            if((!empty($coupon['limitaagentlevels'])||$coupon['limitaagentlevels']=='0')&&$hasabonus) {
                //区域代理
                $level4 = pdo_fetchall('select * from ' . tablename('ewei_shop_abonus_level') . ' where uniacid=:uniacid and  id in ('.$coupon['limitaagentlevels'].') ', array(':uniacid' => $_W['uniacid']));

                if($member['isaagent']=='1'&&$member['aagentstatus']=='1')
                {
                    if (in_array($member['aagentlevel'],$limitaagentlevels)){
                        $pass = true;
                    }
                }
            }
        }else
        {
            $pass = true;
        }

        $set = m('common')->getPluginset('coupon');

        if(is_h5app()){
            $sec = m('common')->getSec();
            $sec =iunserializer($sec['sec']);
            $shopset = m('common')->getSysset();

            $payinfo = array(
                'wechat' => !empty($sec['app_wechat']['merchname']) && !empty($shopset['pay']['app_wechat']) && !empty($sec['app_wechat']['appid']) && !empty($sec['app_wechat']['appsecret']) && !empty($sec['app_wechat']['merchid']) && !empty($sec['app_wechat']['apikey']) ? true : false,
                'alipay' => false,
                'mcname' => $sec['app_wechat']['merchname'],
                'logno' => null,
                'money' => null,
                'attach' => $_W['uniacid'] . ":4",
                'type' => 4
            );
        }

        list(,$payment) = m('common')->public_build();
        if (!empty($payment['is_new'])){
            if ($payment['type'] == 2 || $payment['type'] == 3){
                if (!empty($payment['sub_appsecret'])){
                    m('member')->wxuser($payment['sub_appid'],$payment['sub_appsecret']);
                }
            }
        }

        include $this->template();
    }

    public function pay($a=array(), $b=array()){
        global $_W, $_GPC;

        $openid = $_W['openid'];
        $id = intval($_GPC['id']);
        $coupon = pdo_fetch('select * from ' . tablename('ewei_shop_coupon') . ' where id=:id and uniacid=:uniacid  limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
        $coupon = com('coupon')->setCoupon($coupon, time());
        //无法从领券中心领取
        if (empty($coupon['gettype'])) {
            show_json(-1, '无法'.$coupon['gettypestr']);
        }

        if ($coupon['total'] != -1) {
            if ($coupon['total'] <= 0) {
                show_json(-1, '优惠券数量不足'); //数量不足
            }
        }
        if (!$coupon['canget']) {
            show_json(-1, "您已超出{$coupon['gettypestr']}次数限制"); //已经领取完
        }
        if ($coupon['credit'] > 0) {
            $credit = m('member')->getCredit($openid,'credit1');
            if(intval($coupon['credit'])>$credit){
                show_json(-1, "您的积分不足，无法{$coupon['gettypestr']}!"); //已经领取完
            }
        }
        $needpay = false;
        if ($coupon['money'] > 0) {
            //删除以前无效的记录
            pdo_delete('ewei_shop_coupon_log', array('couponid' => $id, 'openid' => $openid, 'status' => 0,'paystatus'=>0));
            $needpay = true;
            //找出上次支付但未参加的记录（例如断电，断网等特殊情况)
            $lastlog = pdo_fetch('select * from ' . tablename('ewei_shop_coupon_log') . ' where couponid=:couponid and openid=:openid  and status=0 and paystatus=1 and uniacid=:uniacid limit 1', array(':couponid' => $id, ':openid' => $openid, ':uniacid' => $_W['uniacid']));
            if (!empty($lastlog)) {
                show_json(1, array('logid' => $lastlog['id']));
            }
        }else{
            //删除以前无效的记录
            pdo_delete('ewei_shop_coupon_log', array('couponid' => $id, 'openid' => $openid, 'status' => 0));
        }

        $logno = m('common')->createNO('coupon_log', 'logno', 'CC');

        //生成日志
        $log = array(
            'uniacid' => $_W['uniacid'],
            'merchid' => $coupon['merchid'],
            'openid' => $openid,
            'logno' => $logno,
            'couponid' => $id,
            'status' => 0,
            'paystatus' => $coupon['money'] > 0  ? 0 : -1,
            'creditstatus' => $coupon['credit'] > 0 ? 0 : -1,
            'createtime' => time(),
            'getfrom'=>1
        );
        pdo_insert('ewei_shop_coupon_log', $log);
        $logid = pdo_insertid();
        if ($needpay) {
            $useweixin = true;
            //需额外支付
            //usecredit2判断是否优先使用余额
            if (!empty($coupon['usecredit2'])) {
                $money = m('member')->getCredit($openid,'credit2');
                //优先使用余额支付
                if ($money >= $coupon['money']) {
                    //如果足够
                    $useweixin = false;
                }
            }

            //支付方式
            pdo_update('ewei_shop_coupon_log', array('paytype' => $useweixin ? 1 : 0), array('id' => $logid));

            $set = m('common')->getSysset();
            $sec = m('common')->getSec();
            $sec =iunserializer($sec['sec']);

            if($useweixin && is_h5app()){
                if(empty($sec['app_wechat']['merchname']) || empty($set['pay']['app_wechat']) || empty($sec['app_wechat']['appid']) || empty($sec['app_wechat']['appsecret']) || empty($sec['app_wechat']['merchid']) || empty($sec['app_wechat']['apikey']) || empty($coupon['money'])){
                    $useweixin = false;
                }
            }

            if ($useweixin) {

                if(is_h5app()){
                    show_json(1, array(
                        'needpay'=>true,
                        'logid' => $logid,
                        'logno' => $logno,
                        'money' => $coupon['money']
                    ));
                }

                //微信支付
                $set['pay']['weixin'] = !empty($set['pay']['weixin_sub']) ? 1 : $set['pay']['weixin'];
                $set['pay']['weixin_jie'] = !empty($set['pay']['weixin_jie_sub']) ? 1 : $set['pay']['weixin_jie'];
                //微信支付
                if (!is_weixin()) {
                    show_json(-1, '非微信环境!');
                }
                //微信环境
                if (empty($set['pay']['weixin'])&&empty($set['pay']['weixin_jie'])) {
                    show_json(0, '未开启微信支付!');
                }
                $wechat = array('success' => false);
                $jie = intval($_GPC['jie']);
                //如果开启微信支付
                $params = array();
                $params['tid'] = $log['logno'];
                $params['user'] = $openid;
                $params['fee'] = $coupon['money'];
                $params['title'] = $set['shop']['name'] . '优惠券领取单号:' . $log['logno'];

                if (isset($set['pay']) && $set['pay']['weixin'] == 1 && $jie!==1) {
                    load()->model('payment');
                    $setting = uni_setting($_W['uniacid'], array('payment'));
                    $options = array();
                    if (is_array($setting['payment'])) {
                        $options = $setting['payment']['wechat'];
                        $options['appid'] = $_W['account']['key'];
                        $options['secret'] = $_W['account']['secret'];
                    }
                    $wechat = m('common')->wechat_build($params, $options, 4);
                    if (!is_error($wechat)) {
                        $wechat['success'] = true;
                        if (!empty($wechat['code_url'])){
                            $wechat['weixin_jie'] = true;
                        }else{
                            $wechat['weixin'] = true;
                        }
                    }
                }

                if ((isset($set['pay']) && $set['pay']['weixin_jie'] == 1&& !$wechat['success']) || $jie===1) {
                    $params['tid'] = $params['tid'].'_borrow';
                    $options = array();
                    $options['appid'] = $sec['appid'];
                    $options['mchid'] = $sec['mchid'];
                    $options['apikey'] = $sec['apikey'];
                    if (!empty($set['pay']['weixin_jie_sub']) && !empty($sec['sub_secret_jie_sub'])){
                        $wxuser = m('member')->wxuser($sec['sub_appid_jie_sub'],$sec['sub_secret_jie_sub']);
                        $params['openid'] = $wxuser['openid'];
                    }elseif(!empty($sec['secret'])){
                        $wxuser = m('member')->wxuser($sec['appid'],$sec['secret']);
                        $params['openid'] = $wxuser['openid'];
                    }

                    $wechat = m('common')->wechat_native_build($params, $options, 4);
                    if (!is_error($wechat)) {
                        $wechat['success'] = true;
                        if (!empty($params['openid'])){
                            $wechat['weixin'] = true;
                        }else{
                            $wechat['weixin_jie'] = true;
                        }
                    }
                }
                $wechat['jie'] = $jie;
                if (!$wechat['success']) {
                    show_json(0, '微信支付参数错误!');
                }

                show_json(1, array(
                    'logid' => $logid,
                    'wechat' => $wechat
                ));
            }
        }
        show_json(1, array('logid' => $logid));
    }
    public function payresult($a=array()){
        global $_W, $_GPC;

        $logid = intval($_GPC['logid']);
        $log = pdo_fetch('select id,logno,status,paystatus,couponid from ' . tablename('ewei_shop_coupon_log') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $logid, ':uniacid' => $_W['uniacid']));
        if(empty($log)){
            show_json(-1, "订单未找到");
        }
        $coupon = com('coupon')->getCoupon($log['couponid']);

        if(!empty($coupon['usecredit2']) || $coupon['money']<=0){
            $result = com('coupon')->payResult($log['logno']);
            if(is_error($result)){
                show_json($result['errno'],$result['message']);
            }
        }else{
            if (empty($log['paystatus'])){
                show_json(0, '支付未成功!');
            }
        }
        show_json(1,array('url'=>$result['url'],'dataid'=>$result['dataid'],'coupontype'=>$result['coupontype']));
    }
    function recommand(){
        $goods = m('goods')->getList(array(
            'pagesize'=>4,
            'isrecommand'=>true,
            'random'=>true
        ));
        show_json(1,array('list'=>$goods));
    }
}
