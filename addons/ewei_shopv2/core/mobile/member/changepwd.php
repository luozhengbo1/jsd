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

class Changepwd_EweiShopV2Page extends MobileLoginPage{

    protected $member;

    function __construct()
    {
        global $_W, $_GPC;
        parent::__construct();
        $this->member = m('member')->getMember($_W['openid']);
    }

    function main()
    {
        global $_W, $_GPC;

        $member = $this->member;

        $wapset = m('common')->getSysset('wap');

        if(is_weixin() || empty($_GPC['__ewei_shopv2_member_session_'.$_W['uniacid']])){
            header('location: '.mobileUrl());
        }

        if ($_W['ispost']) {
            $mobile = trim($_GPC['mobile']);
            $verifycode = trim($_GPC['verifycode']);
            $pwd = trim($_GPC['pwd']);
            @session_start();
            $key = '__ewei_shopv2_member_verifycodesession_' . $_W['uniacid'] . '_' . $mobile;
            if( !isset($_SESSION[$key]) ||  $_SESSION[$key]!==$verifycode || !isset($_SESSION['verifycodesendtime']) || $_SESSION['verifycodesendtime']+600<time()){
                show_json(0, '验证码错误或已过期!');
            }
            $member = pdo_fetch('select id,openid,mobile,pwd,salt,credit1,credit2, createtime from ' . tablename('ewei_shop_member') . ' where mobile=:mobile and uniacid=:uniacid and mobileverify=1 limit 1', array(':mobile' => $mobile, ':uniacid' => $_W['uniacid']));

            //加密盐
            $salt = empty($member) ? '' : $member['salt'];
            if (empty($salt)) {
                //生成识别码
                $salt = random(16);
                while (1) {
                    $count = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where salt=:salt limit 1', array(':salt' => $salt));
                    if ($count <= 0) {
                        break;
                    }
                    $salt = random(16);
                }
            }

            //用户手机号信息
            pdo_update('ewei_shop_member', array('mobile' => $mobile,'pwd'=>md5($pwd.$salt),'salt'=>$salt,'mobileverify'=>1),array('id'=>$this->member['id'],'uniacid'=>$_W['uniacid']));

            unset($_SESSION[$key]);

            show_json(1);
        }

        $sendtime = $_SESSION['verifycodesendtime'];
        if(empty($sendtime) || $sendtime+60<time()){
            $endtime = 0;
        }else{
            $endtime = 60 - (time() - $sendtime);
        }

        include $this->template();
    }
}
