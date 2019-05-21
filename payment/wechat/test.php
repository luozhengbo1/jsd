<?php
class Test{
        private $config=array(
            'host'=>'localhost',
            'user'=>'Jsdgogcuncom',
            'password'=>'NiNWHh58b8ZC3LdM',
            'database'=>'Jsdgogcuncom_test',
            'port'=>'3306',
        );
        private $conn;
        public $table = "ims_weisrc_dish_sendmsg";
        public $filecache;
        /**
         * 测试推送
         */
        public function __construct(){
          $this->conn =  mysqli_connect(
              $this->config['host'],
              $this->config['user'],
              $this->config['password'],
              $this->config['database'],$this->config['port']);
        }
        public function sendMsg(){
            $select = "select * from ".$this->table." where status=0 ";
            $result = $this->conn->query($select);
//            echo "<pre>";
//            echo 4;
            $updatesql = '';
            while ( $row= $result->fetch_assoc()){
                if (!empty($row['openid'])) {
                    // 立即返回
                    ignore_user_abort(true);
                    ob_start();
                    header('Connection: close');
                    header('Content-Length: ' . ob_get_length());
                    ob_end_flush();
                    ob_flush();
                    flush();
                    $data =  $this->reply_customer($row['openid'], $row['id']."时间:".date('Y-m-d H:i:s')."\n".$row['content']);
                    file_put_contents('/www/wwwroot/ts.log',  print_r($data,true),8);
                    if($data->errcode==0 && $data->errmsg=="ok"){
                        $updatesql .= " update ".$this->table." set status = 1 where id =". $row['id']."; "  ;
                    }

                }
            }
            $this->conn->multi_query($updatesql);

        }

        public  function reply_customer($touser,$content){

            //更换成自己的APPID和APPSECRET
            $APPID="wx0fd57bd3a7fc8709";
            $APPSECRET="155b63989772784550b867c8a96d23a4";

            $TOKEN_URL="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$APPID."&secret=".$APPSECRET;

            $json=file_get_contents($TOKEN_URL);
            $result=json_decode($json);

            $ACC_TOKEN=$result->access_token;

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

