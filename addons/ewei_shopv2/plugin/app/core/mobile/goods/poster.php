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
require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';

class Poster_EweiShopV2Page extends AppMobilePage
{
    public function getimage() {
        global $_W, $_GPC;

        // 1. 判断商品id
        $id = intval($_GPC['id']);
        if(empty($id)){
            app_error(AppError::$ParamsError, '参数错误');
        }

        // 2. 判断商品信息
        $goods = pdo_fetch("select * from " . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
        if(empty($goods)){
            app_error(AppError::$GoodsNotFound, '商品未找到');
        }

        // 3. 判断 会员信息
        $member = $this->member;
        if(empty($member)){
            $member = array();
        }
        $imgurl = $this->createPoster($goods, $member);
        if(empty($imgurl)){
            app_error(AppError::$PosterCreateFail, '海报生成失败');
        }
        app_json(array('url'=>$imgurl));
    }

    private function createPoster($goods = array(), $member = array()) {
        global $_W;

        set_time_limit(0);
        @ini_set("memory_limit", "256M");

        $path = IA_ROOT . "/addons/ewei_shopv2/data/poster_wxapp/goods/" . $_W['uniacid'] . "/";
        if (!is_dir($path)) {
            load()->func('file');
            mkdirs($path);
        }

        $md5 = md5(json_encode(array(
            'siteroot'=>$_W['siteroot'],
            'openid' => $member['openid'],
            'goodstitle' => $goods['title'],
            'goodprice' => $goods['minprice'],
            'version' => 1
        )));
        $filename = $md5 . '.png';
        $filepath = $path. $filename;


        if(is_file($filepath)){
            return $this->getImgUrl($filename);
        }

        // 创建背景
        $target = imagecreatetruecolor(750, 1127);

        // 白色画布
        $white = imagecolorallocate($target, 255, 255, 255);
        imagefill($target, 0, 0, $white);

        // 商品图片
        if(!empty($goods['thumb'])){
            //处理图片没有https的问题
            if(stripos($goods['thumb'], '//') === false){
                $thumb = $this->createImage(tomedia($goods['thumb']));
            } else {
                $thumb = $this->createImage($goods['thumb']);
            }
            imagecopyresized($target, $thumb, 30, 124, 0, 0, 690, 690, imagesx($thumb), imagesy($thumb));
        }

        //字体
        $font = IA_ROOT . "/addons/ewei_shopv2/static/fonts/pingfang.ttf";
        if(!is_file($font)){
            $font = IA_ROOT . "/addons/ewei_shopv2/static/fonts/msyh.ttf";
        }

        //会员信息

        //$avatartarget = imagecreate(70, 70);
        //$avatarwhite = imagecolorallocatealpha($avatartarget,0,0,0,0);
        //将海报图片变为真彩色(有用户将原本生成的图像是灰色提了bug)
        $avatartarget = imagecreatetruecolor(70, 70);
        $avatarwhite = imagecolorallocate($avatartarget, 255, 255, 255);
        imagefill($avatartarget, 0, 0, $avatarwhite);
        //会员头像
        $memberthumb = tomedia($member['avatar']);
        $avatar = preg_replace('/\/0$/i', '/96', $memberthumb);
        $image = $this->mergeImage($avatartarget,array('type'=>'avatar','style'=>'circle'),$avatar);

        imagecopyresized($target, $image, 32, 30, 0, 0, 70, 70, 70, 70);

        //会员名称
        $name = $this -> memberName($member['nickname']);
        $nameColor = imagecolorallocate($target,82,134,207);
        imagettftext($target, 26, 0, 126, 80, $nameColor, $font, $name);
        $shareColor = imagecolorallocate($target,56,56,56);
        $textbox = imagettfbbox(26, 0, $font, $name);
        $textwidth = 136 + $textbox[4]-$textbox[6]; //获取字符串宽度
        imagettftext($target, 26, 0, $textwidth, 80, $shareColor, $font, '分享给你一个商品');



        // 商品价格

//        // 价格背景
//        $pricebgstr = file_get_contents(IA_ROOT . "/addons/ewei_shopv2/plugin/app/static/images/poster/goodsprice.png");
//        $pricebg = imagecreatefromstring($pricebgstr);
//        imagecopyresized($target, $pricebg, 32, 632, 0, 0, 686, 86, 686, 86);

        //文字颜色
        $pricecolor = imagecolorallocate($target, 248,88,77);

        // 价格文字
        imagettftext($target, 52, 0, 56, 1016, $pricecolor, $font, $goods['minprice']);
        imagettftext($target, 26, 0, 30, 1016, $pricecolor, $font, '￥');

        // 商品标题
        $titles = $this->getGoodsTitles($goods['title'], 28, $font, 690);
        $black = imagecolorallocate($target, 0, 0, 0);
        imagettftext($target, 28, 0, 30, 872, $black, $font, $titles[0]);
        imagettftext($target, 28, 0, 30, 922, $black, $font, $titles[1]);

        // 二维码
        $boxstr = file_get_contents(IA_ROOT . "/addons/ewei_shopv2/plugin/app/static/images/poster/goodsbox.png");
        $box = imagecreatefromstring($boxstr);
        imagecopyresampled($target, $box, 546, 934, 0, 0, 150, 150,176,176);

        // 获取二维码
        $qrcode = p('app')->getCodeUnlimit(array(
            'scene'=>'id='. $goods['id']. '&mid='. $member['id'],
            'page'=>'pages/goods/detail/index',
            //'page'=>'pages/work/work',
        ));
        if(!is_error($qrcode)){
            $qrcode = imagecreatefromstring($qrcode);
            imagecopyresized($target, $qrcode, 546, 934, 0, 0, 150, 150, imagesx($qrcode), imagesy($qrcode));
        }

        $gary2 = imagecolorallocate($target, 152,152,152);
        imagettftext($target, 24, 0, 30, 1070, $gary2, $font, '长按识别小程序码访问');

//        header("content-type:image/png");
//        imagepng($target);
//        die;

        imagepng($target, $filepath);
        imagedestroy($target);

        return $this->getImgUrl($filename);
    }

    /**
     * 获取图片路径
     * @param $filename
     * @return string
     */
    private function getImgUrl($filename) {
        global $_W;
        return $_W['siteroot'] . "addons/ewei_shopv2/data/poster_wxapp/goods/". $_W['uniacid']. "/". $filename. '?v=1.0';
    }

    /**
     * 创建图片
     * @param $imgurl
     * @return resource|string
     */
    private function createImage($imgurl) {
        if(empty($imgurl)){
            return '';
        }

        load()->func('communication');
        $resp = ihttp_request($imgurl);
        if ($resp['code'] == 200 && !empty($resp['content'])) {
            return imagecreatefromstring($resp['content']);
        }
        $i = 0;
        while ($i < 3) {
            $resp = ihttp_request($imgurl);
            if ($resp['code'] == 200 && !empty($resp['content'])) {
                return imagecreatefromstring($resp['content']);
            }
            $i++;
        }
        return "";
    }

    /**
     * 获取商品标题
     * @param $text
     * @param int $width
     */
    private function getGoodsTitles($text, $fontsize = 30, $font = '', $width = 100) {
        $titles = array('', '');
        $textLen = mb_strlen($text, 'UTF8');
        $textWidth = imagettfbbox($fontsize, 0, $font, $text);
        $textWidth = $textWidth[4] - $textWidth[6];
        if($textLen > 19 && $textWidth > $width){
            $titleLen1 = 19;
            for ($i=19; $i<=$textLen; $i++){
                $titleText1 = mb_substr($text, 0, $i, 'UTF8');
                $titleWidth1 = imagettfbbox($fontsize, 0, $font, $titleText1);
                if(($titleWidth1[4] - $titleWidth1[6]) > $width){
                    $titleLen1 = $i-1;
                    break;
                }
            }
            $titles[0] = mb_substr($text, 0, $titleLen1, 'UTF8');
            $titleLen2 = 19;
            for ($i=19; $i<=$textLen; $i++){
                $titleText2 = mb_substr($text, $titleLen1, $i, 'UTF8');
                $titleWidth2 = imagettfbbox($fontsize, 0, $font, $titleText2);
                if(($titleWidth2[4] - $titleWidth2[6]) > $width){
                    $titleLen2 = $i-1;
                    break;
                }
            }
            $titles[1] = mb_substr($text, $titleLen1, $titleLen2, 'UTF8');
            if($textLen > ($titleLen1 + $titleLen2)){
                $titles[1] = mb_substr($titles[1], 0, $titleLen2 - 1, 'UTF8');
                $titles[1] .= '...';
            }
        }else{
            $titles[0] = $text;
        }

        return $titles;
    }

    /**
     * 截取会员名称
     * @param $text
     */
    private function memberName($text) {
        $textLen = mb_strlen($text, 'UTF8');
        if($textLen >= 5){
            $text = mb_substr($text,0,5,'utf-8').'...';
        }
        return $text;
    }

    /**
     * 图片缩放
     * @param bool $image
     * @param int $zoom
     * @return resource
     */
    private function imageZoom($image = false, $zoom = 2) {
        $width = imagesx($image);
        $height = imagesy($image);
        $target = imagecreatetruecolor($width * $zoom, $height * $zoom);
        imagecopyresampled($target, $image, 0, 0, 0, 0, $width * $zoom, $height * $zoom, $width, $height);
        imagedestroy($image);
        return $target;
    }

    /**
     * 图片圆角
     * @param bool $target
     * @param bool $circle
     * @return resource
     */
    private function imageRadius($target = false, $circle = false) {
        $w   = imagesx($target);
        $h   = imagesy($target);
        $w   = min($w, $h);
        $h   = $w;
        $img = imagecreatetruecolor($w, $h);
        imagesavealpha($img, true);
        $bg = imagecolorallocatealpha($img, 255, 255, 255, 127);
        imagefill($img, 0, 0, $bg);

        $radius = $circle? $w/2 : 20;
        $r = $radius; //圆 角半径
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $rgbColor = imagecolorat($target, $x, $y);
                if (($x >= $radius && $x <= ($w - $radius)) || ($y >= $radius && $y <= ($h - $radius))) {
                    imagesetpixel($img, $x, $y, $rgbColor);
                } else {
                    $y_x = $r; //圆心X坐标
                    $y_y = $r; //圆心Y坐标
                    if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
                        imagesetpixel($img, $x, $y, $rgbColor);
                    }
                    //上右
                    $y_x = $w - $r; //圆心X坐标
                    $y_y = $r; //圆心Y坐标
                    if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
                        imagesetpixel($img, $x, $y, $rgbColor);
                    }
                    //下左
                    $y_x = $r; //圆心X坐标
                    $y_y = $h - $r; //圆心Y坐标
                    if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
                        imagesetpixel($img, $x, $y, $rgbColor);
                    }
                    //下右
                    $y_x = $w - $r; //圆心X坐标
                    $y_y = $h - $r; //圆心Y坐标
                    if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
                        imagesetpixel($img, $x, $y, $rgbColor);
                    }
                }
            }
        }
        return $img;
    }

    /**
     * 合并图片
     * @param $target
     * @param $data
     * @param $imgurl
     */
    private function mergeImage($target = false, $data = array(), $imgurl = '', $local = false) {
        if(empty($data) || empty($imgurl)){
            return $target;
        }

        if(!$local){
            $image = $this->createImage($imgurl);
        }else{
            $image = imagecreatefromstring($imgurl);
        }

        $sizes = $sizes_default = array(
            'width'=>imagesx($image),
            'height'=>imagesy($image),
        );
        $sizes = array('width'=>70, 'height'=>70);

        if($data['style'] == 'radius' || $data['style'] == 'circle'){
            $image = $this->imageZoom($image, 4);
            $image = $this->imageRadius($image, $data['style']=='circle');
            $sizes_default = array(
                'width'=>$sizes_default['width'] *4,
                'height'=>$sizes_default['height'] *4,
            );
        }

        imagecopyresampled($target, $image, intval($data['left']) *2, intval($data['top']) *2, 0, 0, $sizes['width'], $sizes['height'], $sizes_default['width'], $sizes_default['height']);

        imagedestroy($image);

        return $target;
    }
}