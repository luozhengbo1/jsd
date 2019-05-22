<?php
    $host ="127.0.0.1";
    $user = "Jsdgogcuncom";
    $password = "NiNWHh58b8ZC3LdM";
    $databases="Jsdgogcuncom";
    $port = "3306";
    class CheckOrderCacnel{
        private static $obj;
        public $ordertable = "ims_weisrc_dish_order";
        public $ordergoodstable = "ims_weisrc_dish_order_goods";
        public $goodstable = "ims_weisrc_dish_goods";
        public function __construct($host,$user,$password,$databases,$port)
        {
           self::$obj =  mysqli_connect($host,$user,$password,$databases,$port);
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
            while($row = $query->fetch_array()){
                $sql_ordergoods = "select * from ".$this->ordergoodstable." where orderid=".$row['id'];
                $res = self::$obj->query($sql_ordergoods);
                while($row1= $res->fetch_array()){
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
                $res = self::$obj->query($insert);die;
            }
        }
    }
    $obj  =  new CheckOrderCacnel($host,$user,$password,$databases,$port);
    $obj  -> index();