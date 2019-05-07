<?php

include_once './framework/class/account.class.php';
include_once './framework/class/weixin.account.class.php';
class WechatTs {
    public function __construct()
    {
    }

    /**
     *
     */
    public function index()
    {
        $this->sendText();
    }
    private function sendText($openid, $content)
    {
        $send['touser'] = trim($openid);
        $send['msgtype'] = 'text';
        $send['text'] = array('content' => urlencode($content));
        $acc = \WeAccount::create();
        $data = $acc->sendCustomNotice($send);
        return $data;
    }

}
