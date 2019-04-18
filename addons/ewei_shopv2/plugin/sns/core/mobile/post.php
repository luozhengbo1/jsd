<?php
/*
 * 人人商城
 *
 * 青岛易联互动网络科技有限公司
 * http://www.we7shop.cn
 * TEL: 4000097827/18661772381/15865546761
 */
if (!defined('IN_IA')) {
    exit('Access Denied');
}
require EWEI_SHOPV2_PLUGIN . 'sns/core/page_mobile.php';

class Post_EweiShopV2Page extends SnsMobilePage
{

    function main()
    {
        global $_W, $_GPC;
        $id = intval($_GPC['id']);
        if (empty($id)) {
            $this->message('参数错误');
        }
        $post = $this->model->getPost($id);
        if (empty($post)) {
            $this->message('未找到话题!');
        }
        $post['avatar'] = tomedia($post['avatar']);
        $post['avatar']  =$this->model->getAvatar($post['avatar']); 

        //会员
        $m = $this->model->getMember($_W['openid']);

        $board = $this->model->getBoard($post['bid']);
        if (empty($board)) {
            $this->message('未找到版块!');
        }

        //是否版主
        $isManager = $this->model->isManager($board['id']);
        $isSuperManager = $this->model->isSuperManager();


        //是否能浏览
        if(!$isSuperManager && !$isManager) {
            $check = $this->model->check($m, $board);
            if (is_error($check)) {
                show_message($check['message'], '', 'error');
            }
        }


        $post['content'] = m('ui')->lazy($post['content']);
        $post['content'] =$this->model->replaceContent($post['content']);
        $post['content'] = htmlspecialchars_decode($post['content']);

        $images = iunserializer($post['images']);


        //查看次数
        pdo_update('ewei_shop_sns_post',array('views'=>$post['views']+1),array('id'=>$post['id']));

        //赞数
        $goodcount = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_sns_like') . ' where pid=:pid limit 1', array(':pid' => $post['id']));

        //评论数
        $replycount = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_sns_post') . ' where pid=:pid and deleted=0 and checked=1 limit 1', array(':pid' => $post['id']));

        //是否赞过
        $isgood = pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_sns_like')." where uniacid=:uniacid and pid=:pid and openid=:openid  limit 1",
            array(':uniacid'=>$_W['uniacid'],':pid'=>$post['id'],':openid'=>$_W['openid']));

        //设置 $set
        $set  =$this->getSet();
        //楼主
        $member = $this->model->getMember($post['openid']);

        //等级
        $level =array(
            'levelname' =>empty($set['levelname'])?'社区粉丝':$set['levelname'],
            'color' =>empty($set['levelcolor'])?'#333':$set['levelcolor'],
            'bg' =>empty($set['levelbg'])?'#eee':$set['levelbg'],
        );

        if(!empty($member['sns_level'])){
            $level = pdo_fetch('select * from '.tablename('ewei_shop_sns_level').' where id=:id  limit 1',array(':id'=>$member['sns_level']));
        }
        //投诉类别
        $catelist = pdo_fetchall("SELECT id,name FROM " . tablename('ewei_shop_sns_complaincate') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY displayorder asc");

        $shareImg =tomedia($board['share_icon']);
        if(!empty($images)){
            $shareImg = tomedia($images[0]);
        }

        $url = str_replace('./index.php?','', mobileUrl('sns/post',array('id'=>$post['id'])));
        $loginurl = mobileUrl('account/login',array('backurl'=>urlencode(base64_encode($url))));



        //分享
        $_W['shopshare'] = array(
            'title' => !empty($post['title']) ? $post['title'] : $board['title'],
            'imgUrl' =>$shareImg,
            'link' => mobileUrl('sns/post', array('id'=>$post['id']), true),
            'desc' => $board['title']
        );

        //是否能评论
        $canpost = true;
        if(!$isManager && !$isSuperManager){
            //非版主判断
            $check = $this->model->check($m, $board,true);
            $canpost = !is_error($check);
        }

        include $this->template();
    }
    /*
     * 检测是否为话题/评论
     * */
    function checkPost(){
        global $_W, $_GPC;
        $postid = intval($_GPC['postid']);
        $post = pdo_fetch("select pid,nickname from ".tablename('ewei_shop_sns_post')." where id = ".$postid." ");
        if(empty($post)){
            show_json(0, '该话题或评论不存在！');
        }

        show_json(1,array('post'=>$post));
    }

    //发帖
    function submit()
    {
        global $_W, $_GPC;
        if(!$this->islogin){
            show_json(0, '未登录');
        }

        $bid = intval($_GPC['bid']);
        if (empty($bid)) {
            show_json(0, '参数错误');
        }
        $board = $this->model->getBoard($bid);
        if (empty($board)) {
            show_json(0, '未找到版块!');
        }

        $member = m('member')->getMember($_W['openid']);

        $issupermanager = $this->model->isSuperManager();
        $ismanager =$this->model->isManager($board['id']);

        if(!$issupermanager && !$ismanager) {

            $check = $this->model->check($member, $board, true);
            if (is_error($check)) {
                show_json(0, $check['message']);
            }
        }
        $title = trim($_GPC['title']);
        $len = istrlen($title);
        if( $len<3){
            show_json(0, '标题最少3个汉字或字符哦~');
        }
        if( $len>25){
            show_json(0, '标题最多25个汉字或字符哦~');
        }
        $content = trim($_GPC['content']);
        $len = istrlen($content);
        if( $len<3){
            show_json(0, '内容最少3个汉字或字符哦~');
        }
        if( $len>1000){
            show_json(0, '内容最多1000个汉字或字符哦~');
        }
        $checked = 0;
        if($ismanager){
            $checked = $board['needcheckmanager'] ? 0 : 1;
        } else{
            $checked = $board['needcheck'] ? 0 : 1;
        }


        if($issupermanager){
            $checked=1;
        }

        //图片上传限制
        $imagesData = $this->getSet();
        if(is_array($_GPC['images'])) {
            $imgcount = count($_GPC['images']);
            if($imgcount > $imagesData['imagesnum'] && $imagesData['imagesnum'] > 0 ){
                show_json(0, '话题图片最多上传'.$imagesData['imagesnum'].'张！');
            }
            if($imgcount > 5 && $imagesData['imagesnum'] == 0 ){
                show_json(0, '话题图片最多上传5张！');
            }
        }


        $time = time();
        $data = array(
            'uniacid' => $_W['uniacid'],
            'bid' => $bid,
            'openid' => $_W['openid'],
            'createtime' => $time,
            'avatar' => $member['avatar'],
            'nickname' => $member['nickname'],
            'replytime' => $time,
            'title' => trim($_GPC['title']),
            'content' => trim($_GPC['content']),
            'images' => is_array($_GPC['images']) ? iserializer($_GPC['images']) : serialize(array()),
            'checked' => $checked
        );
        pdo_insert('ewei_shop_sns_post', $data);
        //积分
        if($checked) {
            $this->model->setCredit($_W['openid'], $bid, SNS_CREDIT_POST);

            //检测升级
            $this->model->upgradeLevel($_W['openid']);
        }

        //任务中心@社区发帖任务
        $task = p('task');
        if ($task){
            $task->checkTaskProgress(1,'post');
        }
        show_json(1,array('checked'=>$checked));
    }
    //投诉
    function complain(){
        global $_W, $_GPC;
        if(!$this->islogin){
            show_json(0, '未登录');
        }
        $uniacid = $_W['uniacid'];
        $id = intval($_GPC['id']);
        $openid = $_W['openid'];
        $posts = pdo_fetch("SELECT id,pid,openid FROM ".tablename('ewei_shop_sns_post')." WHERE uniacid = ".$uniacid." AND id = ".$id." AND deleted = 0 ");
        if(empty($posts)){
            show_json(0, '您要投诉的话题或评论不存在！');
        }
        $type = intval($_GPC['type']);
        if(empty($type)){
            show_json(0, '请选择投诉类别！');
        }
        $content = trim($_GPC['content']);
        $len = istrlen($content);
        if( $len<3){
            show_json(0, '内容最少3个汉字或字符哦~');
        }
        if( $len>500){
            show_json(0, '内容最多500个汉字或字符哦~');
        }
        $data = array(
            'uniacid' => $uniacid,
            'type' => $type,
            'postsid' => $id,
            'defendant' => $posts['openid'],            //被投诉人openid
            'complainant' => $openid,                    //投诉人openid
            'complaint_type' => $type,                  //投诉类型
            'complaint_text' => $content,               //投诉内容
            'createtime' => time(),
            'images' => is_array($_GPC['images']) ? iserializer($_GPC['images']) : serialize(array())    //上传图片
        );
        pdo_insert('ewei_shop_sns_complain', $data);
        $insert_id = pdo_insertid();
        if(empty($insert_id)){
            show_json(0,'提交投诉失败，请重试！');
        }

        show_json(1);
    }

    //回复
    function reply()
    {
        global $_W, $_GPC;
        if(!$this->islogin){
            show_json(0, '未登录');
        }

        $bid = intval($_GPC['bid']);
        $pid = intval($_GPC['pid']);
        $rpid = intval($_GPC['rpid']);
        if (empty($bid)) {
            show_json(0, '参数错误');
        }
        $board = $this->model->getBoard($bid);
        if (empty($board)) {
            show_json(0, '未找到版块!');
        }
        $post = $this->model->getPost($pid);
        if (empty($post)) {
            show_json(0, '未找到话题!');
        }

        $member = $this->model->getMember($_W['openid']);

        $ismanager =$this->model->isManager($board['id']);
        $issupermanager = $this->model->isSuperManager();

        //检测发帖权限
        if(!$issupermanager && !$ismanager) {

            $check = $this->model->check($member, $board, true);
            if (is_error($check)) {
                show_json(0, $check['message']);
            }

        }
        $content = trim($_GPC['content']);
        $len = istrlen($content);
        if( $len<3){
            show_json(0, '内容最少3个汉字或字符哦~');
        }
        if( $len>500){
            show_json(0, '内容最多500个汉字或字符哦~');
        }

        $checked = 0;
        if($ismanager){
            $checked = $board['needcheckreplymanager'] ? 0 : 1;
        } else{
            $checked = $board['needcheckreply'] ? 0 : 1;
        }


        if($issupermanager){
            $checked=1;
        }

        $time = time();
        $data = array(
            'uniacid' => $_W['uniacid'],
            'bid' => $bid,
            'pid' => $pid,
            'rpid' => $rpid,
            'openid' => $_W['openid'],
            'avatar' => $member['avatar'],
            'nickname' => $member['nickname'],
            'createtime' => $time,
            'replytime' => $time,
            'content' => trim($_GPC['content']),
            'images' => is_array($_GPC['images']) ? iserializer($_GPC['images']) : serialize(array()),
            'checked' => $checked
        );

        pdo_insert('ewei_shop_sns_post', $data);
        //最后回复时间
        pdo_update('ewei_shop_sns_post', array('replytime' => $time), array('id' => $pid, 'uniacid' => $_W['uniacid']));

        //积分
        if($checked) {
            $this->model->setCredit($_W['openid'], $bid, SNS_CREDIT_REPLY);

             $content =  $this->model->replaceContent($data['content']);
             $content = mb_substr($content,0,15)."...";

            //评论消息
            $this->model->sendReplyMessage($post['openid'],
                array(
                    'nickname'=>$member['nickname'],
                    'id'=>$post['id'],
                    'boardtitle'=>$board['title'],
                    'posttitle'=>$post['title'],
                    'content'=>$content,
                    'createtime'=>$data['createtime']
                )
            );
        }
        show_json(1,array('checked'=>$checked));
    }


    function getlist()
    {
        global $_W, $_GPC;

        $openid = $_W['openid'];
        $member = m('member')->getMember($openid);
        $shop = m('common')->getSysset('shop');
        $uniacid = $_W['uniacid'];
        $bid = intval($_GPC['bid']);
        $pid = intval($_GPC['pid']);

        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $condition = ' and `uniacid` = :uniacid and bid=:bid and pid=:pid and `deleted`=0';
        $params = array(':uniacid' => $_W['uniacid'],':pid'=>$pid,':bid'=>$bid);
        $isSuperManager = $this->model->isSuperManager();
        $isManager = $this->model->isManager($bid);
        if (!$isManager && !$isSuperManager) {
            $condition .= " and `checked`=1";
        }

        $sql = "select id,bid,rpid,title,createtime,content,images ,openid, nickname,avatar,checked from " . tablename('ewei_shop_sns_post')
            . "  where 1 {$condition} ORDER BY createtime desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
        $list = pdo_fetchall($sql, $params);
        $total = pdo_fetchcolumn("select count(*) from " . tablename('ewei_shop_sns_post') . " where 1 {$condition}", $params);
        foreach ($list as $key=> &$row) {


            $row['avatar'] = tomedia($row['avatar']);
            $row['avatar'] = $this->model->getAvatar($row['avatar']);
            $row['createtime'] = date('Y-m-d H:i',$row['createtime']);


            $row['goodcount'] = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_sns_like') . ' where pid=:pid limit 1', array(':pid' => $row['id']));
            $row['postcount'] = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_sns_post') . ' where pid=:pid limit 1', array(':pid' => $row['id']));

            $images = array();
            $rowimages = iunserializer($row['images']);
            if (is_array($rowimages) && !empty($rowimages)) {
                foreach ($rowimages as $img) {
                    if (count($images) <= 2) {
                        $images[] = tomedia($img);
                    }
                }
            }
            $row['images'] = $images;
            $row['imagewidth'] = '32%';
            $row['imagecount'] = count($rowimages);
            $row['content'] = $this->model->replaceContent($row['content']);
            //$row['createtime'] = $this->model->timeBefore($row['createtime']);

            //上级
            $row['parent'] =false;
            if(!empty($row['rpid'])){
                $parentPost = $this->model->getPost($row['rpid']);
                $row['parent'] = array(
                    'nickname'=>$parentPost['nickname'],
                    'content' =>$this->model->replaceContent($parentPost['content'])
                );
            }
            //是否赞过
            $isgood = pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_sns_like')." where uniacid=:uniacid and pid=:pid and openid=:openid limit 1",
                array(':uniacid'=>$_W['uniacid'],':pid'=>$row['id'],':openid'=>$_W['openid']));
            $row['isgood']  = $isgood;

            //赞数
            $row['goodcount'] = pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_sns_like')." where uniacid=:uniacid and pid=:pid  limit 1",
                array(':uniacid'=>$_W['uniacid'],':pid'=>$row['id']));

            //回复人
            //楼主
            $member = $this->model->getMember($row['openid']);

            //设置 $set
            $set  =$this->getSet();
            //等级
            $level =array(
                'levelname' =>empty($set['levelname'])?'社区粉丝':$set['levelname'],
                'color' =>empty($set['levelcolor'])?'#333':$set['levelcolor'],
                'bg' =>empty($set['levelbg'])?'#eee':$set['levelbg'],
            );

            if(!empty($member['sns_level'])){
                $level = pdo_fetch('select * from '.tablename('ewei_shop_sns_level').' where id=:id  limit 1',array(':id'=>$member['sns_level']));
            }

            $row['member'] = array(
                'id'=>$member['id']
            );
            $row['level'] = $level;

            //楼层
            $row['floor'] = ($pindex-1) * $psize +  $key + 2;

            //楼主
            $row['isAuthor'] = $row['openid'] == $_W['openid'];
            //版主
            $row['isManager'] = $this->model->isManager($row['bid'],$row['openid']);


        }
        unset($row);
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }

    //顶
    function like()
    {

        global $_W, $_GPC;
        if(!$this->islogin){
            show_json(0, '未登录');
        }
        $bid = intval($_GPC['bid']);
        $pid = intval($_GPC['pid']);
        if (empty($bid)) {
            show_json(0, '参数错误');
        }
        $board = $this->model->getBoard($bid);
        if (empty($board)) {
            show_json(0, '未找到版块!');
        }
        $post = $this->model->getPost($pid);
        if (empty($post)) {
            show_json(0, '未找到话题!');
        }
        $isgood = 1;
        $like = pdo_fetch('select id from ' . tablename('ewei_shop_sns_like') . " where pid=:pid and openid=:openid limit 1", array(':pid' => $pid, ':openid' => $_W['openid']));
        if (!empty($like)) {
            $isgood = 0;
             pdo_delete('ewei_shop_sns_like', array('id' => $like['id']));
        } else {

                $like = array(
                    'uniacid' => $_W['uniacid'],
                    'pid' => $pid,
                    'openid' => $_W['openid']
                );
                pdo_insert('ewei_shop_sns_like', $like);
        }
        //最后回复时间
        $time = time();
        pdo_update('ewei_shop_sns_post', array('replytime' => $time), array('id' => $pid, 'uniacid' => $_W['uniacid']));
        $goodcount = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_sns_like') . ' where pid=:pid limit 1', array(':pid' => $pid));
        show_json(1, array('isgood'=>$isgood, 'good' => $goodcount));
    }

    //删除
    function delete()
    {

        global $_W, $_GPC;
        if(!$this->islogin){
            show_json(0, '未登录');
        }
        $bid = intval($_GPC['bid']);
        $pid = intval($_GPC['pid']);
        if (empty($bid)) {
            show_json(0, '参数错误');
        }
        $board = $this->model->getBoard($bid);
        if (empty($board)) {
            show_json(0, '未找到版块!');
        }
        $post = $this->model->getPost($pid);
        if (empty($post)) {
            show_json(0, '未找到话题!');
        }

        $isManager = $this->model->isManager($bid);
        $isSuperManager = $this->model->isSuperManager();
        if(!$isManager && !$isSuperManager){
            show_json(0,'无权删除');
        }
        pdo_update('ewei_shop_sns_post', array('deleted' => 1,'deletedtime'=>time()), array('id' => $pid));

        //积分
        if($post['pid']){
            //删除的回复
            $this->model->setCredit($post['openid'], $bid, SNS_CREDIT_DELETE_REPLY);

        } else{
            //删除的主题
            $this->model->setCredit($post['openid'], $bid, SNS_CREDIT_DELETE_POST);
        }
        show_json(1);
    }

    //审核
    function check()
    {

        global $_W, $_GPC;
        if(!$this->islogin){
            show_json(0, '未登录');
        }
        $bid = intval($_GPC['bid']);
        $pid = intval($_GPC['pid']);
        if (empty($bid)) {
            show_json(0, '参数错误');
        }
        $board = $this->model->getBoard($bid);
        if (empty($board)) {
            show_json(0, '未找到版块!');
        }
        $post = $this->model->getPost($pid);
        if (empty($post)) {
            show_json(0, '未找到话题!');
        }
        $isManager = $this->model->isManager($bid);
        $isSuperManager = $this->model->isSuperManager();
        if(!$isManager && !$isSuperManager){
            show_json(0,'无权审核');
        }
        if(!$post['checked']) {
            pdo_update('ewei_shop_sns_post', array('checked' => 1, 'checktime' => time()), array('id' => $pid));
            //积分
            if ($post['pid']) {
                //回复
                $this->model->setCredit($post['openid'], $bid, SNS_CREDIT_REPLY);

            } else {
                //主题
                $this->model->setCredit($post['openid'], $bid, SNS_CREDIT_POST);
            }
        }
        show_json(1);
    }

    //版块精华
    function best()
    {

        global $_W, $_GPC;
        if(!$this->islogin){
            show_json(0, '未登录');
        }
        $bid = intval($_GPC['bid']);
        $pid = intval($_GPC['pid']);
        if (empty($bid)) {
            show_json(0, '参数错误');
        }
        $board = $this->model->getBoard($bid);
        if (empty($board)) {
            show_json(0, '未找到版块!');
        }
        $post = $this->model->getPost($pid);
        if (empty($post)) {
            show_json(0, '未找到话题!');
        }
        $isManager = $this->model->isManager($bid);
        $isSuperManager = $this->model->isSuperManager();
        if(!$isManager && !$isSuperManager){
            show_json(0,'无权设置精华');
        }

        $isbest = 1;
        if($post['isboardbest']){
            $isbest = 0;
            pdo_update('ewei_shop_sns_post', array('isboardbest' => 0), array('id' => $pid));
            //积分
            $this->model->setCredit($post['openid'], $bid, SNS_CREDIT_BEST_BOARD_CANCEL);

        } else{
            pdo_update('ewei_shop_sns_post', array('isboardbest' => 1), array('id' => $pid));
            //积分
            $this->model->setCredit($post['openid'], $bid, SNS_CREDIT_BEST_BOARD);
        }

        show_json(1,array('isbest'=>$isbest));
    }

    //版块置顶
    function top()
    {

        global $_W, $_GPC;
        if(!$this->islogin){
            show_json(0, '未登录');
        }
        $bid = intval($_GPC['bid']);
        $pid = intval($_GPC['pid']);
        if (empty($bid)) {
            show_json(0, '参数错误');
        }
        $board = $this->model->getBoard($bid);
        if (empty($board)) {
            show_json(0, '未找到版块!');
        }
        $post = $this->model->getPost($pid);
        if (empty($post)) {
            show_json(0, '未找到话题!');
        }
        $isManager = $this->model->isManager($bid);
        $isSuperManager = $this->model->isSuperManager();
        if(!$isManager && !$isSuperManager){
            show_json(0,'无权设置置顶');
        }

        $istop = 1;
        if($post['isboardtop']){
            $istop = 0;
            pdo_update('ewei_shop_sns_post', array('isboardtop' => 0), array('id' => $pid));

            //积分
            $this->model->setCredit($post['openid'], $bid, SNS_CREDIT_TOP_BOARD_CANCEL);

        } else{
            pdo_update('ewei_shop_sns_post', array('isboardtop' => 1), array('id' => $pid));

            //积分
            $this->model->setCredit($post['openid'], $bid, SNS_CREDIT_TOP_BOARD);
        }

        show_json(1,array('istop'=>$istop));
    }

    //全站精华
    function allbest()
    {

        global $_W, $_GPC;
        if(!$this->islogin){
            show_json(0, '未登录');
        }
        $bid = intval($_GPC['bid']);
        $pid = intval($_GPC['pid']);
        if (empty($bid)) {
            show_json(0, '参数错误');
        }
        $board = $this->model->getBoard($bid);
        if (empty($board)) {
            show_json(0, '未找到版块!');
        }
        $post = $this->model->getPost($pid);
        if (empty($post)) {
            show_json(0, '未找到话题!');
        }
        $isManager = $this->model->isSuperManager();
        if(!$isManager){
            show_json(0,'无权设置全站精华');
        }

        $isbest = 1;
        if($post['isbest']){
            $isbest = 0;
            pdo_update('ewei_shop_sns_post', array('isbest' => 0), array('id' => $pid));
            //积分
            $this->model->setCredit($post['openid'], $bid, SNS_CREDIT_BEST_CANCEL);

        } else{

            pdo_update('ewei_shop_sns_post', array('isbest' => 1), array('id' => $pid));
            //积分
            $this->model->setCredit($post['openid'], $bid, SNS_CREDIT_BEST);

        }

        show_json(1,array('isbest'=>$isbest));
    }

    //全站置顶
    function alltop()
    {

        global $_W, $_GPC;
        if(!$this->islogin){
            show_json(0, '未登录');
        }
        $bid = intval($_GPC['bid']);
        $pid = intval($_GPC['pid']);
        if (empty($bid)) {
            show_json(0, '参数错误');
        }
        $board = $this->model->getBoard($bid);
        if (empty($board)) {
            show_json(0, '未找到版块!');
        }
        $post = $this->model->getPost($pid);
        if (empty($post)) {
            show_json(0, '未找到话题!');
        }
        $isManager = $this->model->isSuperManager();
        if(!$isManager){
            show_json(0,'无权设置全站置顶');
        }

        $istop = 1;
        if($post['istop']){
            $istop = 0;
            pdo_update('ewei_shop_sns_post', array('istop' => 0), array('id' => $pid));
            //积分
            $this->model->setCredit($post['openid'], $bid, SNS_CREDIT_TOP_CANCEL);
        } else{
            pdo_update('ewei_shop_sns_post', array('istop' => 1), array('id' => $pid));
            //积分
            $this->model->setCredit($post['openid'], $bid, SNS_CREDIT_TOP);
        }

        show_json(1,array('istop'=>$istop));
    }



}
