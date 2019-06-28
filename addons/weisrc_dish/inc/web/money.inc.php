<?php
global $_GPC, $_W;
$weid = $this->_weid;
$GLOBALS['frames'] = $this->getMainMenu();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
    if (!empty($_GPC['displayorder'])) {
        foreach ($_GPC['displayorder'] as $id => $displayorder) {
            pdo_update($this->table_money, array('sort' => $displayorder), array('id' => $id));
        }
        message('排序更新成功！', $this->createWebUrl('money', array('op' => 'display')), 'success');
    }

    $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_money) . " WHERE weid = :weid  ORDER BY sort ASC", array(':weid' => $weid));
  
} elseif ($operation == 'post') {
    load()->func('tpl');
    $parentid = intval($_GPC['parentid']);
    $id = intval($_GPC['id']);
    if (!empty($id)) {
        $type = pdo_fetch("SELECT * FROM " . tablename($this->table_money) . " WHERE id = '$id'");
    } else {
        $type = array(
            'displayorder' => 0,
        );
    }

    if (checksubmit('submit')) {

        $data = array(
            'weid' => $weid,
            'limit'=>$_GPC['limit'],
            'nickname' => $_GPC['nickname'],
            //新增
            'from_user' => $_GPC['from_user'],
            'limit_jifen'=>$_GPC['limit_jifen'],
            'limit_discount'=>$_GPC['limit_discount'],
            'limit_coupon'=>$_GPC['limit_coupon'],
            'sort'=>$_GPC['sort'],
            'money_limit'=>$_GPC['money_limit'],
            'minus'=>$_GPC['minus'],
        );
        if(!$data['nickname'] || !$data['from_user'] ){
            message('昵称不能为空');
        }
        if( ! (($data['limit'] && $data['limit_jifen']) ||   $data['limit_discount']  || $data['limit_coupon'] ||  ( $data['money_limit']  && $data['minus'])  ) ){
            message('请合理设置积分');
        }
        if (!empty($id)) {
            pdo_update($this->table_money, $data, array('id' => $id));
        } else {
            $isIn = pdo_get($this->table_money,array('from_user'=>$data['from_user']),'id');
            if($isIn['id']){
                message('该会员已经存在，请修改！');
            }
            pdo_insert($this->table_money, $data);
        }
        message('会员更新成功 ', $this->createWebUrl('money', array('op' => 'display')), 'success');
    }
} elseif ($operation == 'delete') {
    $id = intval($_GPC['id']);
    $type = pdo_fetch("SELECT id FROM " . tablename($this->table_money) . " WHERE id = '$id'");
    if (empty($type)) {
        message('抱歉，数据不存在或是已经被删除！', $this->createWebUrl('type', array('op' => 'display')), 'error');
    }
    pdo_delete($this->table_money, array('id' => $id, 'weid' => $weid));
    message('数据删除成功！', $this->createWebUrl('money', array('op' => 'display')), 'success');
}
include $this->template('web/money');