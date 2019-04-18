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

class Index_EweiShopV2Page extends AppMobilePage
{
    public function main()
    {
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        //广告
        $advs = pdo_fetchall("select id,advname,link,thumb from " . tablename('ewei_shop_groups_adv') . ' where uniacid=:uniacid and enabled=1 order by displayorder desc', array(':uniacid' => $uniacid));
        $advs = set_medias($advs, 'thumb');
        //分类
        $category = pdo_fetchall("select id,name,thumb from " . tablename('ewei_shop_groups_category') . ' where uniacid=:uniacid and  enabled=1 order by displayorder desc', array(':uniacid' => $uniacid));
        $category = set_medias($category, 'thumb');
        //热门推荐
        $recgoods = pdo_fetchall("select id,title,thumb,thumb_url,price,groupnum,groupsprice,isindex,goodsnum,units,sales,description,is_ladder,more_spec from " . tablename('ewei_shop_groups_goods') . '
					where uniacid=:uniacid and isindex = 1 and status=1 and deleted=0 order by displayorder desc,id DESC limit 20', array(':uniacid' => $uniacid));
        $recgoods = set_medias($recgoods, 'thumb');
        //分享
//        $this->model->groupsShare();
        app_json(array(
            'advs' => $advs,
            'category' => $category,
            'recgoods' => $recgoods
        ));
    }

}