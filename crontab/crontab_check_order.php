<?php
    define('IN_IA', true);
    define('IA_ROOT', str_replace("\\",'/', dirname(dirname(__FILE__))));
    $configfile = IA_ROOT . "/data/config.php";
    if(!file_exists($configfile)) {
        header('Content-Type: text/html; charset=utf-8');
        exit('配置文件不存在或是不可读，请检查“data/config”文件或是重新安装！');
    }
    require($configfile);
    $con =  $config['db']['master'];
    $host =$con['host'];
    $user = $con['username'];
    $password = $con['password'];
    $databases= $con['database'];
    $port =$con['port'] ;
    class CheckOrderCacnel{
        private static $obj;
        public $ordertable = "ims_weisrc_dish_order";
        public $ordergoodstable = "ims_weisrc_dish_order_goods";
        public $goodstable = "ims_weisrc_dish_goods";
        public function __construct($host,$user,$password,$databases,$port)
        {
           self::$obj =  mysqli_connect($host,$user,$password,$databases,$port);
            if (!self::$obj ) {
                printf("Can't connect to MySQL Server. Errorcode: %s ", mysqli_connect_error());
                exit;
            }
        }

        //检查现在的所有订单,超过1小时未支付，并且不是已经取消的订单，
        public function index()
        {
            $time = time();
            $time1 = $time-60*10;
            $sql = "select id,ispay,status,storeid,totalnum,weid,totalprice,from_user,ordersn from {$this->ordertable} where  ispay=0 and  status=0  and dateline < $time1 ";
            $query = self::$obj->query($sql);
            $today_start = strtotime(date('Y-m-d 00:00:00'));
            $today_end = strtotime(date('Y-m-d 23:59:59'));
            while($row = $query->fetch_assoc()){
                $sql_ordergoods = "select * from ".$this->ordergoodstable." where orderid=".$row['id'];
                $res = self::$obj->query($sql_ordergoods);
                while($row1= $res->fetch_assoc()){
                    //如果时间在今天之内。
                    $sql_updategoods = " ";
                    if( $row1['dateline']>=$today_start &&   $row1['dateline']<=$today_end ){
                        //将销量减少
                        $sql_updategoods .= " update ".$this->goodstable." set sales=sales-{$row1['total']} , today_counts=today_counts-{$row1['total']}  where id={$row1['goodsis']} ; ";
                    }
                    self::$obj->multi_query($sql_updategoods);
                }
               $update = "update ims_weisrc_dish_order set status=-1 where id=".$row['id'];
                self::$obj->query($update);
                $insert = "insert into  ims_weisrc_dish_order_log(
                    weid,storeid,orderid,content,fromtype,status,dateline
                    ) value(". $row['weid'].",".$row['storeid'].", ".$row['id'].",'系统自动检测取消订单','1',0,".$time."  )";
                //更新操作狀態
                $res = self::$obj->query($insert);
            }
        }
    }
    $obj  =  new CheckOrderCacnel($host,$user,$password,$databases,$port);
    $obj  -> index();