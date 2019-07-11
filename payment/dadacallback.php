<?php

header("Content-Type: text/html;charset=utf-8");
require '../framework/bootstrap.inc.php';
require_once "../addons/weisrc_dish/templateMessage.php";
class Dadacallback{

    public $config=array(
        'host'=>'127.0.0.1',
        // 'user'=>'Jsdgogcuncom',
        // 'password'=>'NiNWHh58b8ZC3LdM',
        // 'database'=>'Jsdgogcuncom_test',//线下
        'user'=>'jsdgogcun',
        'password'=>'7XTkmbfrfe',
        'database'=>'jsdgogcun',//线上
        'port'=>'3306',
    );
    public  $token;
    public $conn;
    //  private $appid = "wx0fd57bd3a7fc8709";
    public $appid = "wx948178e3ae34071a";  //线上
    //    private $appsecret = "155b63989772784550b867c8a96d23a4";
    public $appsecret = "d27ce382ae9c288ada246dffb1c99680"; //线上
    public  $cache;
    public $site_url ;
    //模板id
    public $templateArr=[
        //发送给商户
        'sendAccept'=>'0mY8g6iE_pRsp9cAifuntjV2BGccUE-Uib8O7zGcbxY',
        //发给用户
        'sendUser'=>'d8ZH3rVHaibdAJ8ze5QlGRrDxINdFir9Vp8FMC7zjrg',
        //重发订单
        'reAddOrder'=>'IfGmGtgTp28bxuyzGcob8imirDjtrfzLpr7Sd4aN060',
    ];
    /**
     * 测试推送
     */
    public function __construct(){
        $this->conn =  mysqli_connect(
            $this->config['host'],
            $this->config['user'],
            $this->config['password'],
            $this->config['database'],
            $this->config['port']
        );
        $sqltoken = "select * from ims_core_cache where`key`='accesstoken:2'";
        $res = $this->conn->query($sqltoken);

        if($res->num_rows>0){
            $row = $res->fetch_assoc();
            $tokenArr = unserialize($row['value']);
            if(isset($tokenArr['token']) && $tokenArr['token'] &&  $tokenArr['expire'] > time()  ){// 未过期
                $this->token = $tokenArr['token'];
            }else{ //过期 更新
                $this->token =  $this->getToken();
                //将token 存入缓存
                $data = serialize(array('token'=>$this->token,'expire'=>time()+7200));
                $update = "update ims_core_cache set `value`= '{$data}' where `key` = 'accesstoken:2' ";
                $this->conn->query($update);
            }
        }
        $this->site_url = "https://jsd.gogcun.cn/";
    }

    //发条推送---重发订单
    public function reAddOrder($data,$msg)
    {
        global $_W;
        //线下
        //  $templateid ="06yGJoVfQiw_hKlAjWINaVNLft3-QaRAUvoGspY_ebg";
        $order = pdo_fetch("select * from ims_weisrc_dish_order WHERE ordersn =:ordersn LIMIT 1", array(':ordersn' => $data['order_id']));
        $goods = pdo_fetchall("SELECT a.*,b.title,b.unitname FROM ims_weisrc_dish_order_goods as a left join  ims_weisrc_dish_goods as b on a.goodsid=b.id WHERE  a.orderid=:orderid", array( ':orderid' => $order['id']));
        $keyword1="";   //商品详情
        if (!empty($goods)) {
            $keyword1 .= "\n－－－－－－－－－－－－－－－－";
            $keyword1 .= "\n商品名称   属性   数量   小计";
            foreach ($goods as $key => $value) {
                $optionstring = '';
                if ($value['optionname'] != ""){
                    $optionname = explode('+', $value['optionname']);
                    for ($i = 0; $i < 3; $i++){
                        $optionstring .= "(".$optionname[$i].")";
                    }
                }else{
                    $optionstring="无";
                }
                $keyword1 .= "\n{$value['title']}   {$optionstring}   {$value['total']}{$value['unitname']}    ".number_format($value['price']*$value['total'],2);
            }
        }
        $first = $msg;
        $keyword2="下单确认后由达达配送"; //配送时间 下单确认后由达达配送
        $keyword3=$order['origin_totalprice']; //实付金额
        $remark="如需重新配送，请点击该通知，进行重新配送！";
        $content = array(
            'first' => array(
                'value' => $first,
                'color' => '#000'
            ),
            'keyword1' => array(
                'value' => $keyword1,
                'color' => '#000'
            ),
            'keyword2' => array(
                'value' => $keyword2,
                'color' => '#000'
            ),
            'keyword3' => array(
                'value' => $keyword3,
                'color' => '#000'
            ),
            'remark' => array(
                'value' => '备注：'.$remark?$remark:'无',
                'color' => '#000'
            ),

        );
        $templateMessage = new templateMessage();
        $url =$this->site_url."/app/index.php?i=2&c=entry&orderid={$order['id']}&do=adminorderdetail&m=weisrc_dish&flag=1";
        $list = pdo_fetchall("SELECT id,from_user FROM ims_weisrc_dish_account  WHERE status=2 and  storeid=:storeid   ORDER BY id DESC ",array(":storeid"=>$order['storeid']));
        foreach ($list as $key => $value) {
            $res = $templateMessage->send_template_message($value['from_user'], $this->templateArr['reAddOrder'], $content, $this->token, $url);
        }
    }

    public function index()
    {
        $input = file_get_contents('php://input');
        file_put_contents('/www/wwwroot/dada_order.log',$input."\n",8);
        //$input='{"signature":"c22fa8c549511b0ec374a478de15fe63","client_id":"283714882373282","order_id":"20190711077977485107","order_status":5,"cancel_reason":"","cancel_from":0,"dm_id":6184889,"dm_name":"黄昇","dm_mobile":"17785922429","update_time":1562808563}';
        $data = json_decode($input,true);
        if($data['order_status']==1000 ||$data['order_status']==7 || $data['order_status']==5 ){ //异常和过期进行从新下单处理。
            switch ($data['order_status']){
                case 5:
                    if($data['cancel_from']==1){
                        $msg = "您好，你的订单配送已被:达达配送员取消，是否重新发送订单到达达。";
                    }else if($data['cancel_from']==2){
                        $msg = "您好，你的订单配送已被:商家主动取消，是否重新发送订单到达达。";
                    }else if($data['cancel_from']==3){
                        $msg = "您好，你的订单配送已被:系统或客服取消，是否重新发送订单到达达。";
                    }else{
                        $msg = "您好，你的订单配送已被达达取消，是否重新发送订单到达达。";
                    }
                    break;
                case 7:
                    $msg = "您好，你的订单30分钟未被骑士接单，系统会自动取消。请重新发单。";
                    break;
                case 1000:
                    $msg = "因为达达内部服务异常，导致下发订单失败。可以通过“重发订单到达达”来下发订单";
                    break;
            }
            $this->reAddOrder($data,$msg);
        }else if($data['order_status']==100){ //骑手到店取货
            $this->sendUserForDada($data);
        }else if($data['order_status']==2){
            $this->sendMerchantForDada($data);
        }else{
            if($data['order_status']==4 ){ //status 1 ,0  发出 。2 完成 order_status=4 完成
                pdo_update('weisrc_dish_dada_order',array('status'=>2,'order_status'=>4),array('ordersn'=>$data['order_id']));
            }
        }
        //更新实时状态
        pdo_update('weisrc_dish_dada_order',array('order_status'=>$data['order_status']),array('ordersn'=>$data['order_id']));
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

    /**
     * 骑手接单
     */
    public function sendMerchantForDada($data)
    {

        $order = pdo_fetch("select id,from_user from ims_weisrc_dish_order WHERE ordersn =:ordersn LIMIT 1", array(':ordersn' => $data['order_id']));
        $first = "你好，外卖配送员正准备上门取商品";
        $keyword1 = $data['dm_name'];
        $keyword2 = $data['dm_mobile'];
        $keyword3 = date('Y-m-d H:i:s',$data['update_time']);
        $remark="";   //商品详情
        $remark .="达达运单号：".$data['client_id'];
        $remark .="\n外卖订单号：".$data['order_id'];
        $content = array(
            'first' => array(
                'value' => $first,
                'color' => '#000'
            ),
            'keyword1' => array(
                'value' => $keyword1,
                'color' => '#000'
            ),
            'keyword2' => array(
                'value' => $keyword2,
                'color' => '#000'
            ),
            'keyword3' => array(
                'value' => $keyword3,
                'color' => '#000'
            ),
            'remark' => array(
                'value' =>$remark,
                'color' => '#000'
            ),
        );
        $order = pdo_fetch("select id,from_user from ims_weisrc_dish_order WHERE ordersn =:ordersn LIMIT 1", array(':ordersn' => $data['order_id']));
        $templateMessage = new templateMessage();
        $url = $this->site_url."/app/index.php?i=2&c=entry&orderid={$order['id']}&do=adminorderdetail&m=weisrc_dish";
        $res = $templateMessage->send_template_message($order['from_user'], $this->templateArr['sendAccept'], $content, $this->token, $url);
    }

    /**
     * 通知用户骑手配送中
     */
    public function sendUserForDada($data)
    {
        $first = "您的订单".$data['order_id']."已于".date('Y-m-d H:i:s',$data['update_time'])."送出"; //您的订单123456已于2017-12-22 09:40:21送出
        $keyword1 = $data['dm_name'];
        $keyword2 = $data['dm_mobile'];
        $content = array(
            'first' => array(
                'value' => $first,
                'color' => '#000'
            ),
            'keyword1' => array(
                'value' => $keyword1,
                'color' => '#000'
            ),
            'keyword2' => array(
                'value' => $keyword2,
                'color' => '#000'
            ),
            'remark' => array(
                'value' => '请耐心等待',
                'color' => '#000'
            ),
        );
        $order = pdo_fetch("select id,from_user from ims_weisrc_dish_order WHERE ordersn =:ordersn LIMIT 1", array(':ordersn' => $data['order_id']));
        $templateMessage = new templateMessage();
        $url = $this->site_url."/app/index.php?i=2&c=entry&orderid={$order['id']}&do=orderdetail&m=weisrc_dish";
        $res = $templateMessage->send_template_message($order['from_user'], $this->templateArr['sendUser'], $content, $this->token, $url);
    }

}
$obj = new Dadacallback();
$obj->index();
