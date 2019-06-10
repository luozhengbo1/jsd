<?php


//define('IN_IA', true);
//define('IA_ROOT', str_replace("\\",'/', dirname(dirname(__FILE__))));
//$configfile = IA_ROOT . "/data/config.php";
//if(!file_exists($configfile)) {
//    header('Content-Type: text/html; charset=utf-8');
//    exit('配置文件不存在或是不可读，请检查“data/config”文件或是重新安装！');
//}
//require($configfile);
//$con =  $config['db']['master'];
//$host =$con['host'];
//$user = $con['username'];
//$password = $con['password'];
//$databases= $con['database'];
//$port =$con['port'] ;
//$querystring = getpot('a:');
//$pdo = new PDO("mysql:host=" . $host . ";dbname=" . $databases, $user, $password, array(PDO::ATTR_PERSISTENT => true));
////
//    swoole_after_timer(10000,function ($querystring){
//        global $querystring, $pdo;
//        $time = time();
//        $time1 = $time-60*1;
//        $orderid= $querystring['a'];
//        $pdo->beginTransaction;//开启事务
//        $sql = "select id,ispay,status,storeid,totalnum,weid,totalprice,from_user,ordersn from ims_weisrc_dish_order where  ispay=0 and  status=0  and dateline < $time1 ";
//        $pdo->
//    });
