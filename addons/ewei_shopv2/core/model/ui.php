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

class Ui_EweiShopV2Model
{

    function lazy($html = '')
    {
        global $_W;
        $html = preg_replace_callback("/<img.*?src=[\\\'| \\\"](.*?(?:[\.gif|\.jpg|\.png|\.jpeg]?))[\\\'|\\\"].*?[\/]?>/", function ($matches) use ($_W) {
            $images = $matches[0];
            if (strexists($images,'http://') || strexists($images,'https://')) {
                return preg_replace("/src=/", "data-lazy=", $images);
            }
            //判断是不是本地
            if(file_exists(IA_ROOT . '/' . $_W['config']['upload']['attachdir'] . '/' . $matches[1]))
            {
                $image = $matches[1];
                $images = str_replace($image,tomedia($image),$images);
            }

            $attachurl = str_replace(array('https://','http://'),'',$_W['attachurl_local']);
            if (strexists($images, $attachurl)){
                $image = $matches[1];
                $image = str_replace(array('https://','http://'),'',$image);
                $image = str_replace($attachurl,'',$image);
                $images = str_replace(array('https://','http://'),'',$images);
                $images = str_replace($attachurl,'',$images);
                $images = str_replace($image,tomedia($image),$images);
            }else{
                $image = $matches[1];
                $images = str_replace($image,tomedia($image),$images);
            }
            return preg_replace("/src=/", "data-lazy=", $images);
        }, $html);
        return $html;
    }
}
