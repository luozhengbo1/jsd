<?php
/**
 *  重新发单api
 */
define("BASE_DIR",dirname(__FILE__)."/");
require_once BASE_DIR."api/baseApi.php";
require_once BASE_DIR."config/urlConfig.php";

class ReOrderApi extends BaseApi{

    public function __construct($url, $params)
    {
        parent::__construct(UrlConfig::ORDER_REPEAT_URL, $params);
    }
}
