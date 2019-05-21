<?php
    $host ="127.0.0.1";
    $user = "Jsdgogcuncom";
    $password = "NiNWHh58b8ZC3LdM";
    $databases="Jsdgogcuncom";
    $port = "3306";
    class CheckOrderCacnel{
        private static $obj;
        public function __construct($host,$user,$password,$databases,$port)
        {
           self::$obj =  mysqli_connect($host,$user,$password,$databases,$port);
        }

        //检查现在的所有订单,超过1小时未支付，并且不是已经取消的订单，
        public function index()
        {
            $time = time();
            $time1 = $time-60*10;
            $sql = "select id,ispay,status,storeid,totalnum,weid,totalprice,from_user,ordersn from ims_weisrc_dish_order where  ispay=0 and  status=0  and dateline < $time1 ";
            $query = self::$obj->query($sql);
            while($row = $query->fetch_array()){
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