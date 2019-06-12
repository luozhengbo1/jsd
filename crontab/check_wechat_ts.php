<?php
class Test{
        private $config=array(
            'host'=>'127.0.0.1',
            'user'=>'Jsdgogcuncom',
            'password'=>'NiNWHh58b8ZC3LdM',
            'database'=>'Jsdgogcuncom_test',//线下
//            'user'=>'jsdgogcun',
//            'password'=>'7XTkmbfrfe',
//            'database'=>'jsdgogcun',//线上
            'port'=>'3306',
        );
        private  $token;
        private $conn;
        private $appid = "wx0fd57bd3a7fc8709";
//        private $appid = "wx948178e3ae34071a";
        private $appsecret = "155b63989772784550b867c8a96d23a4";
//        private $appsecret = "d27ce382ae9c288ada246dffb1c99680";
        public $table = "ims_weisrc_dish_sendmsg";
        /**
         * 测试推送
         */
        public function __construct(){
          $this->conn =  mysqli_connect(
              $this->config['host'],
              $this->config['user'],
              $this->config['password'],
              $this->config['database'],$this->config['port']);
              $this->setToken();
        }
        public function sendMsg(){
            $select = "select * from ".$this->table." where status=0 ";
            $result = $this->conn->query($select);
//            $row['openid']="oW-VD01zhPdr764rS0AO8yFAAX9E";
            $updatesql = '';
            while ( $row= $result->fetch_assoc()){
                if (!empty($row['openid'])) {
                    $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->token.'&openid='.$row['openid'];
                    $result1 =file_get_contents($url);
                    $result1 = json_decode($result1);
                    if(isset($result1->subscribe) && $result1->subscribe==1  ){ //关注的用户才发送消息
                        $data =  $this->reply_customer($row['openid'],$row['content']);
                        if($data->errcode==0 && $data->errmsg=="ok"){
                            $updatesql .= " update ".$this->table." set status = 1,sendtime='".date('Y-m-d H:i:s')."' where id =". $row['id']."; "  ;
                        }
                    }else{
                        $updatesql .=" update ".$this->table." set status = 2,sendtime='".date('Y-m-d H:i:s')."' where id =". $row['id']."; ";
                    }
                }
            }
            $this->conn->multi_query($updatesql);
        }


        //获取token 并保存7200秒
        public function setToken()
        {
            $sql = "select * from ims_wechat_token limit 1 ";
            $res = $this->conn->query($sql);
            if(!$res->num_rows){
                $TOKEN_URL="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=". $this->appid."&secret=".$this->appsecret;
                $json=file_get_contents($TOKEN_URL);
                $result=json_decode($json);
                $this->token=$result->access_token;
                $insert = "insert into ims_wechat_token(`token`,`createtime`) VALUE('{$this->token}','".date('Y-m-d H:i:s')."')";
                mysqli_query($this->conn,$insert);
            }else {
                if($row= $res->fetch_assoc() ){
                    if(strtotime($row['createtime'])+7200 > time() &&  $row['token'] ){ //未过期
                        $this->token = $row['token'];
                    }else{
                        $TOKEN_URL="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=". $this->appid."&secret=".$this->appsecret;
                        $json=file_get_contents($TOKEN_URL);
                        $result=json_decode($json);
                        $this->token=$result->access_token;
                        $update = "update  ims_wechat_token set `token`='{$result->access_token}',`createtime` ='".date('Y-m-d H:i:s')."'";
                        mysqli_query($this->conn,$update);
                    }
                }
            }

        }
        public  function reply_customer($touser,$content){

            $ACC_TOKEN = $this->token;
            $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$ACC_TOKEN;
            $data = '{
                "touser":"'.$touser.'",
                "msgtype":"text",
                "text":
                {
                     "content":"'.$content.'"
                },
                "msgid": "'.time().'"
                
            }';
            $result = $this->https_post($url,$data);
            $final = json_decode($result);
            return $final;
        }

        public function https_post($url,$data)
        {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($curl);
            if (curl_errno($curl)) {
                return 'Errno'.curl_error($curl);
            }
            curl_close($curl);
            return $result;
        }
    }
    $obj = new Test();
    $obj->sendMsg();

