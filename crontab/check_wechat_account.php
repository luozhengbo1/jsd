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
        public  $token;
        public $conn;
        private $appid = "wx0fd57bd3a7fc8709";
//        private $appid = "wx948178e3ae34071a";
        private $appsecret = "155b63989772784550b867c8a96d23a4";
//        private $appsecret = "d27ce382ae9c288ada246dffb1c99680";
        public $table = "ims_weisrc_dish_fans";
        /**
         * 数据库实例
         */
        public function __construct(){
          $this->conn =  mysqli_connect(
              $this->config['host'],
              $this->config['user'],
              $this->config['password'],
              $this->config['database'],
              $this->config['port']
          );
            $this->getToken();
        }
        public function get_account(){
            $select = "select * from ".$this->table." where 1";
            $result = $this->conn->query($select);
            $sqltoken = "select * from ims_core_cache where key='accesstoken:2'";
            $this->conn->query($sqltoken);
            $data =  $this->get_wechat_account();
            $openid = $data->data->openid;
            $fan_ids = array();
            while ( $row= $result->fetch_assoc()){
                if (!empty($row['from_user'])) {
                    if (!in_array($row['from_user'],$openid)){
                        array_push($fan_ids, $row['id']);
                    }
                }else{
                     array_push($fan_ids, $row['id']);
                }
            }
            $ids = implode(',',$fan_ids);
            mysqli_autocommit($this->conn,false);//表示事务开始
            $drop_sql = "ALTER TABLE `ims_weisrc_dish_fans` DROP COLUMN `wechat_status`";
            $drop_wechat_status = $this->conn->query($drop_sql);
            $alter_sql = "ALTER TABLE `ims_weisrc_dish_fans` ADD `wechat_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1，是该公众号下的openid；0，不是该公众号下的openid'";
            $alter_wechat_status = $this->conn->query($alter_sql);
            if (empty($fan_ids)){
                mysqli_commit($this->conn); //成功即提交
                return ;
            }
            $sql = "update `ims_weisrc_dish_fans` set `wechat_status`=0 where id in ({$ids})";
            $result_sql = $this->conn->query($sql);
            if ($result_sql=1){
                mysqli_commit($this->conn); //成功即提交
            }else{
                mysqli_rollback($this->conn);//失败进行回滚到事务开始点
            }
        }


        //获取token 并保存7200秒
        public function getToken()
        {
            $TOKEN_URL="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=". $this->appid."&secret=".$this->appsecret;
            $data = '';
            $result = $this->https_post($TOKEN_URL,$data);
            $json_object = json_decode($result);
            $this->token= $json_object->access_token;
            return $this->token;
        }

        public  function get_wechat_account(){

            $ACC_TOKEN = $this->token;
            $url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=".$ACC_TOKEN."&next_openid=";
            $data = '';
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
    $obj->get_account();

