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

$orderid = getpot('a:');
