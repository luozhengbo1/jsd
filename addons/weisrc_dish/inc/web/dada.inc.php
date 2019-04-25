<?php
global $_GPC, $_W;
$weid = $this->_weid;
$storeid = intval($_GPC['storeid']);
$action = 'dada';
//var_dump($weid,$storeid);exit();
$this->checkStore($storeid);
$title = $this->actions_titles[$action];
$returnid = $this->checkPermission($storeid);
//$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getNaveMenu($storeid,$action);
$info=pdo_get('weisrc_dish_stores',array('weid'=>$weid,'id'=>$storeid));
    if(checksubmit('submit')){
            $data['source_id']=$_GPC['source_id'];
            $data['shop_no']=$_GPC['shop_no'];
            $data['is_dada']=$_GPC['is_dada'];
            $res = pdo_update('weisrc_dish_stores', $data, array('id' => $storeid));
            if($res){
                message('编辑成功',$this->createWebUrl('dada',array('op' => 'display', 'storeid' => $storeid)),'success');
            }else{
                message('编辑失败','','error');
            }
           
        }
include $this->template('web/dada');