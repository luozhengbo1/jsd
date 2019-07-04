<?php

header("Content-Type: text/html;charset=utf-8");

//参考文档 http://newopen.imdada.cn/#/development/file/add?_k=ff7mls

//define("BASE_DIR", dirname(__FILE__) . "/");
//require_once BASE_DIR . 'api/addOrderApi.php';
//require_once BASE_DIR . 'client/dadaRequestClient.php';
//require_once BASE_DIR . 'client/dadaResponse.php';
//require_once BASE_DIR . 'config/config.php';
//require_once BASE_DIR . 'model/orderModel.php';
class Dadacallback{

    //重发订单
    public function reAddOrder(){
        $json ='{"shop_no":"113-550","origin_id":"20190704203769306282","city_code":"0851","info":null,"cargo_price":"31.50","is_prepay":0,"expected_fetch_time":1562221100,"receiver_name":"\u4f0d\u5148\u751f","receiver_address":"\u8d35\u5dde\u7701\u8d35\u9633\u5e02\u4e91\u5ca9\u533a\u91d1\u9e2d\u793e\u533a\u670d\u52a1\u4e2d\u5fc3\u91d1\u9f99\u661f\u5c9b\u56fd\u9645 23\u680b2\u5355\u5143604","receiver_phone":"17785108711","receiver_lat":"26.5916600000","receiver_lng":"106.6536950000","callback":"https:\/\/jsd.gogcun.cn\/payment\/dadacallback.php"}';
        echo "<pre>";
        print_r(json_decode($json,true));
    }

    public function index()
    {
        $input = file_get_contents('php://input');
        file_put_contents('/www/wwwroot/dada_order.log',$input."\n",8);
        $data = json_decode($input,true);
        if($data['order_status']==1000){
            //从新发单？
        }
        return 200;
       /*
        * (待接单＝1,
        * 待取货＝2,
        * 配送中＝3,
        * 已完成＝4,
        * 已取消＝5,
        * 已过期＝7,
        * 指派单=8,
        * 妥投异常之物品返回中=9,
        * 妥投异常之物品返回完成=10,
        * 骑士到店=100,
        * 创建达达运单失败=1000
        * 可参考文末的状态说明）
        1.已取消：包括配送员取消、商户取消、客服取消、系统取消（比如：骑士接单后一直未取货）， 此时订单的状态为5，可以通过“重新发单”来下发订单。

        2.已过期：订单30分钟未被骑士接单，系统会自动将订单过期终结。此时订单状态为7，可以通过“重新发单”来下发订单。

        3.妥投异常：配送员在收货地，无法正常送到用户手中（包括用户电话打不通、客户暂时不方便收件、客户拒收、货物有问题等等）

        4.状态1000：表示因为达达内部服务异常，导致下发订单失败。可以通过“新增订单”来下发订单。
       */
    }


}
$obj = new Dadacallback();
$obj->reAddOrder();
