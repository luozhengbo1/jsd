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
require dirname(__DIR__). '/../defines.php';
 
class ComProcessor extends WeModuleProcessor {
    public $model;
    public $modulename;
    public $message;
    public function __construct($name = '') {
 
        $this->modulename = 'ewei_shopv2';
        $this->pluginname = $name;
      
        //自动加载插件model.php
        $this->loadModel();  
    }
      /**
     * 加载插件model
     */
    private function loadModel(){
        $modelfile = IA_ROOT.'/addons/'.$this->modulename."/core/com/".$this->pluginname.".php";
         if(is_file($modelfile)){
              $classname = ucfirst($this->pluginname)."_EweiShopV2ComModel";
              require $modelfile;
              $this->model = new $classname($this->pluginname);
         }
    }
    public function respond(){
        $this->message = $this->message;
    }

}