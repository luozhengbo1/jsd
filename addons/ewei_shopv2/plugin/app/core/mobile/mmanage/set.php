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

require EWEI_SHOPV2_PLUGIN . 'app/core/page_auth_mobile.php';

class Set_EweiShopV2Page extends AppMobileAuthPage {

    public function main() {
        global $_W, $_GPC;

        $_W['uid'] = 2;

        load()->model('user');

        // C38zXf00YbntVT

        $account = user_single(array('username' => trim($_GPC['_username'])));
        if(empty($account)){
            app_error(AppError::$UserLoginFail, '未查询到此用户');
        }

        $editinfo = true;
        // 判断超级管理员
        $founders = explode(',', $_W['config']['setting']['founder']);
        if(in_array($account['uid'], $founders)){
            $editinfo = false;
        }
        // 判断操作员
        if($editinfo){
            $account_user = pdo_fetch('SELECT * FROM '. tablename('uni_account_users'). ' WHERE uid=:uid', array(
                ':uid'=>$account['uid']
            ));
            if($account_user['role'] != 'operator'){
                $editinfo = false;
            }
        }

        $roleuser = pdo_fetch("SELECT id, uid, username, status, openid, realname, mobile, openid_wa, member_nick FROM". tablename("ewei_shop_perm_user"). "WHERE uid=:uid AND uniacid=:uniacid", array(":uid"=>$account['uid'], ":uniacid"=>$_W['uniacid']));
        if(!empty($roleuser)){
            if(!empty($roleuser['openid_wa']) && empty($roleuser['member_nick'])){
                $roleuser['member_nick'] = '昵称未获取';
            }
        }

        if($_W['ispost']){
            $realname = trim($_GPC['realname']);
            $mobile = trim($_GPC['mobile']);
            $password = trim($_GPC['password']);
            $password2 = trim($_GPC['password2']);
            if(empty($realname)){
                app_error(AppError::$ParamsError, "请输入真实姓名");
            }
            if(empty($realname)){
                app_error(AppError::$ParamsError, "请输入手机号");
            }
            if(!empty($password) || !empty($password2)){
                if(empty($password)){
                    app_error(AppError::$ParamsError, "请输入密码");
                }
                if(empty($password2)){
                    app_error(AppError::$ParamsError, "请重复输入密码");
                }
                if($password!=$password2){
                    app_error(AppError::$ParamsError, "两次输入的密码不一致");
                }
                $changepass = true;
            }

            if($changepass){
                // 更新微擎表
                $changepassresult = user_update(array('uid' => $roleuser['uid'], 'password' => $password, 'salt' => $account['salt']));
                $data['password'] = $account['password'];
            }

            $data = array(
                'realname' => $realname,
                'mobile' => $mobile
            );
            // 更新商城表
            pdo_update('ewei_shop_perm_user', $data, array('id' => $roleuser['id'], 'uniacid' => $_W['uniacid']));

            app_json(array(
                'changepass'=>intval($changepassresult)
            ));
        }

        app_json(array(
            'user'=>$roleuser,
            'account'=>array(
                'username'=>$account['username'],
                'uid'=>$account['uid']
            ),
            'editinfo'=>$editinfo
        ));
    }

    /**
     * 绑定微信号
     */
    public function bindwx() {
        global $_W, $_GPC;

        $username = trim($_GPC['_username']);
        $userinfo = $_GPC['userinfo'];
        $confirm = intval($_GPC['confirm']);
        if(empty($confirm)){
            $code = trim($_GPC['code']);
            if(empty($code)){
                app_error(AppError::$ParamsError);
            }
            $openid = $this->getOpenid($code);
        }else{
            $openid = trim($_GPC['openid']);
        }

        load()->model('user');

        $roleuser = pdo_fetch("SELECT id, uid, username, status, openid, realname, mobile, openid_wa, member_nick FROM". tablename("ewei_shop_perm_user"). "WHERE openid_wa=:openid AND uniacid=:uniacid", array(":openid"=>$openid, ":uniacid"=>$_W['uniacid']));
        $account = user_single(array('username' => $username));
        if(empty($account)){
            app_error(AppError::$UserLoginFail);
        }

        // 绑定过
        if(!empty($roleuser) && empty($confirm)){
            if($account['uid'] == $roleuser['uid'].'0'){
                app_error(AppError::$BindError, '操作账号已绑定当前微信');
            }else{
                $member_wa = iunserializer($roleuser['member_wa']);
                app_json(array(
                    'error'=>AppError::$BindConfirm,
                    'message'=>'操作账号已绑定'.$member_wa['nickname'].' 确定要取消之前绑定？',
                    'openid'=>$openid
                ));
            }
        }

        // 执行绑定
        $data = array(
            'openid_wa'=>$openid,
            'member_nick'=>$userinfo['nickName']
        );
        pdo_update('ewei_shop_perm_user', $data, array(
            'uid'=>$account['uid'],
            'uniacid'=>$_W['uniacid'],
        ));

        if(!empty($roleuser)){
            pdo_update('ewei_shop_perm_user', array('openid_wa'=>'', 'member_nick'=>''), array('id'=>$roleuser['id']));
        }

        app_json();
    }

    /**
     * 获取用户openid
     * @param $code
     * @return mixed
     */
    protected function getOpenid($code) {
        $set = p('app')->getGlobal();
        if(empty($set['mmanage'])){
            $set['mmanage'] = array();
        }

        load()->func('communication');
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid={$set['mmanage']['appid']}&secret={$set['mmanage']['secret']}&js_code={$code}&grant_type=authorization_code";
        $resp = ihttp_request($url);
        if($resp['code'] != 200){
            app_error(AppError::$UserLoginFail , '与微信连接失败，请稍后重试');
        }
        $arr = @json_decode($resp['content'],true);
        if(!empty($arr['errcode']) || !isset($arr['openid'])){
            app_error(AppError::$UserLoginFail , $arr['errmsg']);
        }

        return $arr['openid'];
    }

}

