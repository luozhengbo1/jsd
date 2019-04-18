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

require EWEI_SHOPV2_PLUGIN.'dividend/core/dividend_page_web.php';
class Index_EweiShopV2Page extends DividendWebPage  {
    protected $statusCacheKey = "initDividendMemberCount";

    function main() {
        global $_W;
        if (cv('dividend.agent')) {
            header('location: ' . webUrl('dividend/agent'));
            exit;
        } else if (cv('dividend.apply.view1')) {
            header('location: ' . webUrl('dividend/apply', array('status' => 1)));
            exit;
        } else if (cv('dividend.apply.view2')) {
            header('location: ' . webUrl('dividend/apply', array('status' => 2)));
            exit;
        } else if (cv('dividend.apply.view3')) {
            header('location: ' . webUrl('dividend/apply', array('status' => 3)));
            exit;
        } else if (cv('dividend.apply.view_1')) {
            header('location: ' . webUrl('dividend/apply', array('status' => -1)));
            exit;
        } else if (cv('dividend.increase')) {
            header('location: ' . webUrl('dividend/increase'));
            exit;
        } else if (cv('dividend.notice')) {
            header('location: ' . webUrl('dividend/notice'));
            exit;
        } else if (cv('dividend.cover')) {
            header('location: ' . webUrl('dividend/cover'));
            exit;
        }  else if (cv('dividend.set')) {
            header('location: ' . webUrl('dividend/set'));
            exit;
        }
    }

    function init(){
        global $_W, $_GPC;

        $pindex = max(1, intval($_GPC['page']));
        $psize = 100;

        $open_redis = function_exists('redis') && !is_error(redis());

        $redisStatus = "N";
        if( $open_redis ) {
            $redis = redis();
            $redisStatus = "Y";
        }

        $count = pdo_fetchcolumn("SELECT count(*) FROM " . tablename('ewei_shop_member') . " WHERE uniacid=:uniacid AND agentid>0",  array(':uniacid' => $_W['uniacid']));
        if($count>$psize){
            $page_count=ceil($count/$psize);
        }else{
            $page_count=1;
        }

        if ($_W['isajax']) {
            // 这时候设置当前数据处理状态为处理中
            $redis->set($this->getStatusCacheKey(), 202);
            $list = pdo_fetchall("SELECT id,agentid FROM " . tablename('ewei_shop_member') . " WHERE uniacid=:uniacid AND agentid>0  ORDER BY id asc limit " . ($pindex - 1) * $psize . ',' . $psize,  array(':uniacid' => $_W['uniacid']));
            if(!empty($list)){
                foreach($list as $member) {
                    if($member['id']!=$member['agentid']){
                        $mem=p('commission') -> saveRelation($member['id'],$member['agentid']);
                        if(is_array($mem)){
                            // 如果发生错误,设置错误码
                            $errData = array(
                                'errno' => -1,
                                'errmsg'    => "初始化错误！". "<br/>请修改<a style='color: #259fdc;' target='_blank' href='".webUrl('member/list/detail', array('id' => $mem['id']))."'>会员(".$mem['nickname'].")</a>的上级分销商!",
                            );
                            $redis->set($this->getStatusCacheKey(), json_encode($errData, JSON_UNESCAPED_UNICODE));
                            die;
                        }
                    }
                }
            }

            if($pindex==$page_count){
                $data = m('common')->getPluginset('dividend');
                $data['init'] = 1;
                m('common')->updatePluginset(array('dividend'=>$data));
            }
            //$open_redis = function_exists('redis') && !is_error(redis());

            // 修改状态为处理完成
            $redis->set($this->getStatusCacheKey() , 200);
        }
        //$open_redis = !$open_redis;
        include $this->template();
    }

    // 获取缓存键
    private function getStatusCacheKey()
    {
        global $_GPC;
        return $this->statusCacheKey . $_GPC['page'];
    }

    // 获取当前请求处理状态
    function getHandleStatus()
    {
        global $_W, $_GPC;
        $open_redis = function_exists('redis') && !is_error(redis());

        $redis = redis();
        $status = $redis->get($this->getStatusCacheKey());

        if(!is_numeric($status)) {
            $ret= json_decode($status, true);
            show_json($ret['errno'], $ret['errmsg']);
        }

        // 提取缓存里面的状态
        preg_match("#\d+#", $this->getStatusCacheKey(), $pindex);
        show_json($status, array(
            'pindex' => implode($pindex),
            'cacheKey'  => $this->getStatusCacheKey()
        ));
    }


    function notice() {

        global $_W, $_GPC;

        $data = m('common')->getPluginset('dividend', false);
        $data = $data['tm'];
        //分销商提现通知商家高级
        $salers1 = array();
        if (isset($data['openid1'])) {
            if (!empty($data['openid1'])) {
                $openids1 = array();
                $strsopenids = explode(",", $data['openid1']);
                foreach ($strsopenids as $openid) {
                    $openids1[] = "'" . $openid . "'";
                }
                @$salers1 = pdo_fetchall("select id,nickname,avatar,openid from " . tablename('ewei_shop_member') . ' where openid in (' . implode(",", $openids1) . ") and uniacid={$_W['uniacid']}");
            }
        }

        //分销商提现通知商家低级
        $salers2 = array();
        if (isset($data['openid2'])) {
            if (!empty($data['openid2'])) {
                $openids2 = array();
                $strsopenids2 = explode(",", $data['openid2']);
                foreach ($strsopenids2 as $openid2) {
                    $openids2[] = "'" . $openid2 . "'";
                }
                @$salers2 = pdo_fetchall("select id,nickname,avatar,openid from " . tablename('ewei_shop_member') . ' where openid in (' . implode(",", $openids2) . ") and uniacid={$_W['uniacid']}");
            }
        }
        if ($_W['ispost']) {
            //分红通知商家
            $data = is_array($_GPC['data']) ? $_GPC['data'] : array();

            if (is_array($_GPC['openids1'])) {
                $data['openid1'] = implode(",", $_GPC['openids1']);
            }else{
                $data['openid1'] = '';
            }
            $data['openid'] =  $data['openid1'];

            m('common')->updatePluginset(array('dividend'=>array('tm'=>$data)));
            plog('dividend.notice.edit', '修改通知设置');
            show_json(1);
        }

        $data = m('common')->getPluginset('dividend');
        $template_lists= pdo_fetchall('SELECT id,title,typecode FROM ' . tablename('ewei_shop_member_message_template') . ' WHERE uniacid=:uniacid ', array(':uniacid' => $_W['uniacid']));

        $templatetype_list = pdo_fetchall('SELECT * FROM  ' . tablename('ewei_shop_member_message_template_type'));

        $template_group=array();

        foreach($templatetype_list as $type)
        {
            $templates=array();

            foreach($template_lists as $template)
            {
                if($template['typecode']==$type['typecode'])
                {
                    $templates[]=$template;
                }
            }
            $template_group[$type['typecode']]=$templates;

        }

        $template_list = $template_group;


        //        $template_sms = com('sms')->sms_temp();
        //        $opensms = com('sms');
        include $this->template();
    }

    function set() {
        global $_W, $_GPC;
        if ($_W['ispost']) {
            $data = is_array($_GPC['data']) ? $_GPC['data'] : array();

            $data['open'] = intval($data['open']);   //int   团队分红开关 0关闭 1开启
            $data['ratio'] = round(floatval(trim($data['ratio'], '%')),2);   //float   分红比例(%)
            $data['cashcredit'] = intval($data['cashcredit']);
            $data['cashweixin'] = intval($data['cashweixin']);
            $data['cashother'] = intval($data['cashother']);
            $data['cashalipay'] = intval($data['cashalipay']);
            $data['cashcard'] = intval($data['cashcard']);
            $data['texts'] = is_array($_GPC['texts']) ? $_GPC['texts'] : array();
            if(empty($data['ratio'])){
                $data['ratio'] = '0';
            }
            if($data['withdraw'] <1 || empty($data['withdraw'])){
                show_json(0,'请填写大于1的数字');
            }
            if($data['ratio'] < 0 || $data['ratio'] > 100){
                show_json(0,'请填写0-100之间的数值,只保留两位小数');
            }
            $data['condition'] = intval($data['condition']); // int

            switch ($data['condition'])
            {
                case 0:
                    $data['check'] = intval($data['check']);
                    break;
                case 1:
                    $data['downline'] = intval($data['downline']);
                    if(empty($data['downline'])){
                        show_json(0,'请填写下线人数');
                    }
                    $become = '下线人数达到'.$data['downline'];
                    break;
                case 2:
                    $data['commissiondownline'] = intval($data['commissiondownline']);
                    if(empty($data['commissiondownline'])){
                        show_json(0,'请填写下线分销商数');
                    }
                    $become = '下线分销商数达到'.$data['commissiondownline'];
                    break;
                case 3:
                    $data['total_commission'] = floatval($data['total_commission']);
                    if(empty($data['total_commission'])){
                        show_json(0,'请填写佣金总额');
                    }
                    $become = '分销佣金总额达到'.$data['total_commission'];
                    break;
                case 4:
                    $data['cash_commission'] = floatval($data['cash_commission']);
                    if(empty($data['cash_commission'])){
                        show_json(0,'请填写提现佣金总额');
                    }
                    $become = '提现佣金总额达到'.$data['cash_commission'];
                    break;
            }

            if(!empty($data['withdrawcharge'])) {
                $data['withdrawcharge'] = trim($data['withdrawcharge']);
                $data['withdrawcharge'] = floatval(trim($data['withdrawcharge'], '%'));
            }

            $data['withdrawbegin'] = floatval(trim($data['withdrawbegin']));
            $data['withdrawend'] = floatval(trim($data['withdrawend']));
            $data['open_protocol'] = floatval(trim($data['open_protocols']));
            $data['applycontent'] = m('common')->html_images($data['applycontent']);
            $data['register_bottom_content'] = m('common')->html_images($data['register_bottom_content']);
             m('common')->updatePluginset(array('dividend'=>$data));
            //模板缓存
             m('cache')->set('template_' . $this->pluginname, $data['style']);

            plog('dividend.set.edit', '修改基本设置<br>成为队长条件 -- '.$become);
            show_json(1,array('url'=>webUrl('dividend/set', array('tab'=>str_replace("#tab_","",$_GPC['tab'])))));
        }

        $data = m('common')->getPluginset('dividend');
        $data['open_protocols'] = $data['open_protocol'];

        include $this->template();
    }

    //重新初始化
    function refresh(){
        $data = m('common')->getPluginset('dividend');
        $data['init'] =0;
        m('common')->updatePluginset(array('dividend'=>$data));
        pdo_delete('ewei_shop_commission_relation');//清空关系树
        header('location: ' . webUrl('dividend/index'));
    }

}
