<?php

class Dadacallback{
    public function __construct()
    {
    }
    //
    public function index(){
        echo 34;
    }

    public function doDada($weid=2,$orderid=85413,$storeid=170){
        global $_W, $_GPC;
        //var_dump($weid,$orderid,$storeid);exit();
        include "../addons/weisrc_dish/DadaOpenapi.php";
        $res=$this->pdo_get($this->table_setting,array('weid'=>$weid));
        $order=$this->pdo_get('weisrc_dish_order',array('id'=>$orderid));
        $store=$this->pdo_get('weisrc_dish_stores',array('weid'=>$weid,'id'=>$order['storeid']));

        //*********************配置项*************************
        $config = array();
        $config['app_key'] = $res['dada_key'];
        $config['app_secret'] = $res['dada_secret'];
        //商戶id
        $config['source_id'] =73753;
//        $config['source_id'] = $store['source_id'];
        $config['url'] = 'http://newopen.imdada.cn/api/cityCode/list';
        $obj = new DadaOpenapi($config);
        // $name="贵阳市发多少发士大夫十大";//$store['address'];
        // $name=substr($name,strpos($name,"省")+3);
        // var_dump($name);exit();
        $data=array();
//请求接口
        $reqStatus = $obj->makeRequest($data);
//       print_r($obj->getCode());die;
        if (!$reqStatus) {
            //接口请求正常，判断接口返回的结果，自定义业务操作
            if ($obj->getCode() == 0) {
                $arr=$obj->getResult();
                //var_dump($arr);die;
                // foreach($arr as $v){
                //   if($name==$v['cityName']){
                //     $cityCode=$v['cityCode'];
                //   }
                // }
                //先转换一下坐标
                $transpoint = $this->baiduMapTogaodeMap($order['lng'], $order['lat']);
//发单请求数据,只是样例数据，根据自己的需求进行更改。
                $data2 = array(
//              'shop_no'=>  $store['shop_no'],//门店编号
                    'shop_no'=> '11047059',//门店编号
                    'origin_id'=> $order['ordersn'],//订单id
                    'city_code'=> "贵阳市",//城市
                    //'tips'=> 0,//小费
                    'info'=> $order['note'],//备注
                    // 'cargo_type'=> 1,
                    // 'cargo_weight'=> 10,
                    'cargo_price'=> $order['totalprice'],
                    // 'cargo_num'=> 2,
                    'is_prepay'=> 0,
                    'expected_fetch_time'=>time(),
                    //'expected_finish_time'=> 0,
                    // 'invoice_title'=> '发票抬头',
                    'receiver_name'=> $order['username'],
                    'receiver_address'=> $order['address'],
                    'receiver_phone'=> $order['tel'],
                    // 'receiver_tel'=> '18599999999',
                    'receiver_lat'        => $transpoint['lat'],
                    'receiver_lng'        => $transpoint['lng'],
                    //          'callback'=>'http://newopen.imdada.cn/inner/api/order/status/notify'
                );
                $config['url'] = 'http://newopen.imdada.cn/api/order/addOrder';
                p($config);
                $data2['callback'] = $_W['siteroot'] . 'payment/dadacallback.php';
                $obj2 = new DadaOpenapi($config);

                $reqStatus2 = $obj2->makeRequest($data2);
                p($obj2);
                p($reqStatus2);
                die;
                if (!$reqStatus2) {
                    //接口请求正常，判断接口返回的结果，自定义业务操作
                    //print_r($obj2->getCode());echo '达达';die;
                    if ($obj2->getCode() == 0) {
                        //echo '下单成功';
                        //print_r($obj2->getResult());
                    }
                }

            }else{
                //echo  '失败';
            }
            //echo sprintf('code:%s,msg:%s', $obj->getCode(),$obj->getMsg());
        }else{
            //请求异常或者失败
            //echo 'except';
        }
    }


}
$obj = new Dadacallback();
$obj->doDada();

