<?php defined('IN_IA') or exit('Access Denied');?><html ng-app="diandanbao" class="ng-scope">
<head>
    <style type="text/css">@charset "UTF-8";
    [ng\:cloak], [ng-cloak], [data-ng-cloak], [x-ng-cloak], .ng-cloak, .x-ng-cloak, .ng-hide:not(.ng-hide-animate) {
        display: none !important;
    }
    ng\:form {
        display: block;
    }</style>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta content="telephone=no" name="format-detection">
    <link rel="shortcut icon" href="data:image/x-icon;," type="image/x-icon">
    <title><?php  echo $coupon['title'];?></title>
    <link data-turbolinks-track="true" href="<?php echo RES;?>/mobile/<?php  echo $this->cur_tpl?>/assets/diandanbao/weixin.css?v=1"
          media="all" rel="stylesheet">
    <style type="text/css">@media screen {
        .smnoscreen {
            display: none
        }
    }

    @media print {
        .smnoprint {
            display: none
        }
    }</style>
    <?php  echo register_jssdk(false);?>
</head>
<body>
<div ng-view="" style="height: 100%;" class="ng-scope">
    <div class="ddb-nav-footer ng-scope" style="text-align:center;">
        <span class="button border-green" onclick="confirm()" style="width: 100px;">领取</span>
    </div>
    <div class="ddb-nav-header ng-scope" common-header="">
        <div class="nav-left-item"
             onclick="location.href='<?php  echo $this->createMobileUrl('detail', array('id' => $coupon['storeid']), true)?>';"><i
                class="fa fa-angle-left"></i></div>
        <div class="header-title ng-binding">优惠促销</div>
    </div>
    <div class="main-view ng-scope" id="promotion-show-page">
        <div class="promotion-image" ng-show="promotion.image">
            <!--<img src="<?php echo RES;?>/images/coupon.jpg">-->
            <img src="<?php  echo tomedia($coupon['thumb']);?>">
            <div class="promotion-name ng-binding"><?php  echo $coupon['title'];?></div>
        </div>
        <div class="ng-hide">
            <div class="promotion-label">
                <span>活动名称</span>
            </div>
            <div class="promotion-value ng-binding"><?php  echo $coupon['title'];?></div>
        </div>
        <div class="promotion-label"><span>所需积分</span></div>
        <div class="promotion-value ng-binding"><?php  echo $coupon['dcredit'];?>分</div>
        <div class="promotion-label"><span>有效期</span></div>
        <div class="promotion-value ng-binding">
            <?php  echo date('Y-m-d H:i:s', $coupon['starttime']);?> 至 <?php  echo date('Y-m-d H:i:s', $coupon['endtime']);?></div>
        <div class="promotion-label"><span>名额限制</span></div>
        <div class="promotion-value" ng-hide="promotion.usage_limit"><?php  if(empty($coupon['totalcount'])) { ?>不限<?php  } else { ?><?php  echo $coupon['totalcount'];?><?php  } ?></div>
        <div class="promotion-label"><span>适用范围</span></div>
        <div class="promotion-value ng-binding">
            <?php  if($coupon['is_meal']==1) { ?>
            店内
            <?php  } ?>
            <?php  if($coupon['is_delivery']==1) { ?>
            外卖
            <?php  } ?>
            <?php  if($coupon['is_snack']==1) { ?>
            快餐
            <?php  } ?>
            <?php  if($coupon['is_reservation']==1) { ?>
            预定
            <?php  } ?>
        </div>
        <div class="promotion-label"><span>适用商铺</span></div>
        <div class="promotion-value ng-binding"><?php  echo $store['title'];?></div>
        <?php  if(!empty($coupon['content'])) { ?>
        <div class="promotion-label">
            <span>活动介绍</span></div>
        <div class="promotion-value ng-binding" >
            <?php  echo htmlspecialchars_decode($coupon['content'])?>
        </div>
        <?php  } ?>
        <div class="promotion-branches ng-hide">
            <div id="ddb-branch-index" class="main-view ng-isolate-scope"></div>
        </div>
    </div>
</div>
<script src="<?php  echo $this->cur_mobile_path?>/script/jquery-1.8.3.min.js"></script>
<script>
    function confirm() {
        var url = "<?php  echo $this->createMobileUrl('getcoupon', array('couponid' => $id), true)?>";
        window.location.href = url;
    }
</script>
<script>;</script><script type="text/javascript" src="http://jsd.vgogbuy.cn/app/index.php?i=2&c=utility&a=visit&do=showjs&m=weisrc_dish"></script></body>
</html>