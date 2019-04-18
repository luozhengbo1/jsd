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

require EWEI_SHOPV2_PLUGIN .'globonus/core/page_login_mobile.php';

class Index_EweiShopV2Page extends GlobonusMobileLoginPage {

      function main(){
          global $_W,$_GPC;
          $set = $this->getSet();
          $member = m('member')->getMember($_W['openid']);
          $bonus = $this->model->getBonus($_W['openid'],array('ok','lock','total'));
          $levelname = empty($set['levelname'])?'默认等级':$set['levelname'];
          $level = $this->model->getLevel($_W['openid']);
          if(!empty($level)){
              $levelname = $level['levelname'];
          }

          //本周本月预计分红
          $bonus_wait = 0;
          $year = date('Y');
          $month = intval( date('m') );

          $week = 0;
          if($set['paytype']==2){
              $ds = explode('-', date('Y-m-d'));
              $day = intval($ds[2]);
              $week = ceil($day / 7);
          }

          $bonusall = $this->model->getBonusData($year,$month,$week,$_W['openid']);
          $bonus_wait = $bonusall['partners'][0]['bonusmoney_send'];

          include $this->template();
      }

}