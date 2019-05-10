<?php
header("Content-Type: text/html;charset=utf-8");
//参考文档 http://newopen.imdada.cn/#/development/file/cityList?_k=qbcp8l
define("BASE_DIR", dirname(__FILE__) . "/");
require_once BASE_DIR . 'api/cityCodeApi.php';
require_once BASE_DIR . 'client/dadaRequestClient.php';
require_once BASE_DIR . 'client/dadaResponse.php';
require_once BASE_DIR . 'config/config.php';
//*********************1.配置项*************************
$config = new Config(16846, true);
//*********************2.实例化一个model*************************
// city_code 业务参数为""
//'{
//    "app_key": "dada123456789",
//    "body": "",
//    "format": "json",
//    "signature": "aaaaaaaaaaaaaaaaaaaaaaaa",
//    "source_id": "73753",
//    "timestamp": "1511749248",
//    "v": "1.0",
//    "app_secret": "1234567890abcdefghijklmn"
// } ';
$cityCodeModel['app_key'] = $config->app_key;
$cityCodeModel['app_secret'] = $config->app_secret;
$cityCodeModel['v'] = "1.0";
$cityCodeModel['source_id'] = $config->source_id;
$cityCodeModel['timestamp'] = time();
$cityCodeModel['format'] = "json";
$cityCodeModel['body'] = "贵阳";

$cityCodeModel['signature'] = "";

//*********************3.实例化一个api*************************
$cityCodeApi = new CityCodeApi(json_encode($cityCodeModel));

//***********************4.实例化客户端请求************************
$dada_client = new DadaRequestClient($config, $cityCodeApi);
$resp = $dada_client->makeRequest();
echo json_encode($resp);
