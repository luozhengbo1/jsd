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
class PluginPfMobilePage extends Page {

    public $model;
    public $set;
    public function __construct() {

        m('shop')->checkClose();
        $this->model = m('plugin')->loadModel($GLOBALS["_W"]['plugin']);
        $this->set = $this->model->getSet();
    }
		
	public function getSet(){
		return $this->set;
	}
   
}
