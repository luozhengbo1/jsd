<?php
global $_GPC, $_W;
$weid = $this->_weid;
$GLOBALS['frames'] = $this->getMainMenu();

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'post') {
    $pindex = max(1, intval($_GPC['page']));
    $psize = 10;
    $where = "WHERE weid = {$weid}";
    //$list = pdo_fetchall("SELECT * FROM " . tablename("weisrc_dish_cost_jilu") . " WHERE weid = :weid", array(':weid' => $weid));
    $list = pdo_fetchall("SELECT * FROM " . tablename("weisrc_dish_cost_jilu") . " {$where} order by id desc LIMIT " . ($pindex - 1) * $psize . ",{$psize}");
    //var_dump($list);
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("weisrc_dish_cost_jilu") . " $where");
    $pager = pagination($total, $pindex, $psize);

    
}
if ($operation == 'display') {
    load()->func('tpl');
    $parentid = intval($_GPC['parentid']);
    $item = pdo_fetch("SELECT * FROM " . tablename($this->table_cost) . "WHERE weid = '$weid'");
    if (checksubmit('submit')) {
        $data = array(
            'weid' => $weid,
            'total_price'=>$_GPC['total_price'],
        );
        if (!is_numeric($data["total_price"])|| $data["total_price"] == ''){
            message('请输入正确运营费充值！', $this->createWebUrl('cost', array('op' => 'display')), 'error');
        }
        if (!empty($item)) {  
          $b =   pdo_update($this->table_cost, $data, array('id' => $item['id']));
        } else {
          $a =  pdo_insert($this->table_cost, $data);
          
        }
        message('运营费充值成功 ', $this->createWebUrl('cost', array('op' => 'display')), 'success');
    }
 } 
if ($operation == 'email') {
    $item = pdo_fetch("SELECT * FROM " . tablename('weisrc_dish_cost_email') . " WHERE weid = :weid", array(':weid' => $weid));
            // var_dump($item);
    if (checksubmit('submit')) {
        $data = array(
            'weid' => $weid,
            'mailhost'=> $_GPC['mailhost'],
            'mailport'=> $_GPC['mailport'],
            'mailhostname'=>$_GPC['mailhostname'],
            'mailformname'=>$_GPC['mailformname'],
            'mailusername'=>$_GPC['mailusername'],
            'mailpassword'=>$_GPC['mailpassword'],
            'mailsend'  =>$_GPC['mailsend']
        );
        if(!$data['mailhost']){
            message('请输入域名邮箱的服务器地址');
        }
        if(!$data['mailport']){
            message('请输入远程服务器端口号');
        }
        if(!$data['mailhostname']){
            message('请输入接收邮件的邮箱');
        }
        if(!$data['mailformname']){
            message('请输入发件人姓名(昵称)');
        }
        if(!$data['mailusername']){
            message('请输入smtp登录账号');
        }
        if(!$data['mailpassword']){
            message('请输入smtp登录的密码');
        }
        if(!$data['mailsend']){
            message('请输入发件人邮箱');
        }
        if (empty($item)) {
            pdo_insert('weisrc_dish_cost_email', $data);
        } else {
            pdo_update('weisrc_dish_cost_email', $data , array('weid' => $weid));
        } 
        message('邮箱配置更新成功!', $this -> createWebUrl('cost', array('op' => 'email')), 'success');
        
    } 
}
include $this->template('web/cost');