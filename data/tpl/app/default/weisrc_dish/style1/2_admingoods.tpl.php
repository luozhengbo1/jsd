<?php defined('IN_IA') or exit('Access Denied');?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width,initial-scale=1.0"/>
    <meta name="keywords" content="美食,团购,外卖,网上订餐,酒店,旅游,电影票,火车票,飞机票">
    <meta name="description" content="美食攻略,外卖网上订餐,酒店预订,旅游团购,飞机票火车票,电影票,ktv团购吃喝玩乐全都有!店铺信息查询,商家评分/评价一站式生活服务网站">
    <title><?php  echo $setting['title'];?></title>
    <link rel="stylesheet" href="<?php echo RES;?>/plugin/light7/light7.min.css">
    <link rel="stylesheet" type="text/css" href="<?php  echo $this->cur_mobile_path?>/css/iconfont.css"/>
    <link rel="stylesheet" type="text/css" href="<?php  echo $this->cur_mobile_path?>/css/api.css"/>
    <link rel="stylesheet" type="text/css" href="<?php  echo $this->cur_mobile_path?>/css/common.css"/>
    <link rel="stylesheet" href="<?php  echo $this->cur_mobile_path?>/css/shop.css?v=3">
    <link rel="stylesheet" type="text/css" href="<?php  echo $this->cur_mobile_path?>/css/fakeLoader.css">
    <script src="<?php  echo $this->cur_mobile_path?>/script/jquery-1.8.3.min.js"></script>
    <style>
        i {
            width: 12px;
            height: 12px;
            display: inline-block;
            line-height: 12px;
            font-size: 12px;
            margin-right: 2px;
            text-align: center;
            color: #fff;
            border-radius: 2px;
        }
        .icon-bg1{ background:#70bc46;}
        .icon-bg2{ background:#f07373;}
        .icon-bg3{ background:#f1884f;}
        .icon-bg4{ background:#f5a317;}
        .info {
            font-size: 10px;
            color: #a7a2a9;
        }
        .icon-delivery {
            padding: 5px 10px;
        border: 1px solid <?php  echo $setting['style_base'];?>;
        border-radius: 2px;
        background-color: #fff;
        color: <?php  echo $setting['style_base'];?>;
        font-size: 10px;
        line-height: 12px;
        }
        .footer-bar.bar-tab .tab-item.active .icon, .bar-tab .tab-item:active .icon {
        color: <?php  echo $setting['style_base'];?>;
        }
        .footer-bar.bar-tab .tab-item.active, .bar-tab .tab-item:active {
        color: <?php  echo $setting['style_base'];?>;
        }
    </style>
</head>
<body>
<div class="fakeloader"></div>
<?php  $url_search = $this->createMobileUrl('adminstore', array(), true);?>
<?php  $url_user = $this->createMobileUrl('usercenter', array(), true);?>
<div id="wrap" style="height:100%;overflow: scroll;overflow-y:scroll;-webkit-overflow-scrolling:touch;">
    <div id="header" style="background-color: <?php  echo $setting['style_base'];?>;border-bottom: 1px solid <?php  echo $setting['style_base'];?>;">
        <div class="nav-left-item"  onclick="javascript:window.location.href='<?php  echo $url_search;?>'"><i class="i-back"></i></div>
        <div class="flex-full"><?php  echo $store['title'];?>_商品管理</div>
        <div class="map" tapmode="topbar-active"  onclick="javascript:window.location.href='<?php  echo $url_user;?>'">
            <i class="i-user"></i>
        </div>
    </div>
    <div id="main" style="margin-top:44px;padding-bottom: 70px;">
        <div class="box item-list" id="index-data">
            <?php  if(is_array($restlist)) { foreach($restlist as $item) { ?>
            <section class="item" >
                <div class="left-wrap">
                    <img class="logo lazy" data-original="<?php  echo tomedia($item['thumb']);?>" src="<?php  echo tomedia($item['thumb']);?>" onerror="this.src='<?php  echo tomedia('./addons/weisrc_dish/icon.jpg');?>'">
                    <?php  if($item['status'] == 0) { ?>
                    <span class="status-tip" style="background-color: rgb(192, 192, 192);">
                    已下架
                    </span>
                    <?php  } else { ?>
                    <span class="status-tip" style="background-color: rgb(147, 192, 88);">
                    已上架
                    </span>
                    <?php  } ?>
                </div>
                <div class="right-wrap">
                    <section class="line">
                        <h3 class="shopname"><?php  echo $item['title'];?></h3>
                        <div class="support-wrap">

                        </div>
                    </section>
                    <section class="line">
                        <div class="rate-wrap">
                       	<span>
                            <?php  for($i=0;$i < $item['level']; $i++){ ?>
                            <i class="i-star i-star-gold"></i>
                            <?php  }?>
                        </span>
                        </div>
                        <div class="delivery-wrap">
                            <?php  $url = $this->createMobileUrl('admingoods', array('op' => 'setstatus','id'=>$item['id'], 'storeid' => $storeid), true);?>
                            <span class="icon-delivery" onclick="javascript:window.location.href='<?php  echo $url;?>'">
                                <?php  if($item['status'] == 1) { ?>
                                下架
                                <?php  } else { ?>
                                上架
                                <?php  } ?>
                            </span>
                        </div>
                    </section>
                    <section class="line">
                        <div class="moneylimit">
                            <span>价格：<?php  echo $item['marketprice'];?>元</span>
                            <span>销量：<?php  echo $item['sales'];?></span>
                        </div>
                    </section>
                </div>
            </section>
            <?php  } } ?>
        </div>
        <div class="popup-overlay"></div>
    </div>
</div>
<?php  echo register_jssdk(false);?>
<script>
    wx.ready(function () {
        sharedata = {
            title: '<?php  echo $share_title;?>',
            desc: '<?php  echo $share_desc;?>',
            link: '<?php  echo $share_url;?>',
            imgUrl: '<?php  echo $share_image;?>',
            success: function(){
                //alert('感谢分享');
            },
            cancel: function(){
                //alert('cancel');
            }
        };
        wx.onMenuShareAppMessage(sharedata);
        wx.onMenuShareTimeline(sharedata);
    });
</script>
<?php  include $this->template($this->cur_tpl.'/_nave');?>
<script type="text/javascript" src="<?php echo RES;?>/js/jquery.lazyload.min.js"></script>
<script>
    $(function () {
        $("img.lazy").lazyload({effect: "fadeIn"});
    });
</script>

<script type="text/javascript">
    var page = 2;
    var loading  = false;
    var stop_track = false;

    $(document).ready(function() {
        $('#wrap').scroll(function(){
                if(stop_track == false && loading==false) {
                    loading = true;
                    var loadurl ="<?php  echo $this->createMobileurl('getmorestore', array('areaid' => $areaid, 'typeid' => $typeid), true)?>";
                    $.ajax({
                        type: 'POST',
                        url: loadurl,
                        data: {
                            'page': page
                        },
                        dataType: 'json',
                        timeout: 3000,
                        context: $('body'),
                        success: function(data){
                            if (data == '0') {
                                stop_track = true;
                            } else {
                                $("#index-data").append(data);
                                if (data == '') {
                                    stop_track = true;
                                }
                                page++;
                                loading = false;
                            }
                        },
                        error: function (xhr) {
                            alert('网络通讯失败，请重试!');
                        }
                    });
                }
        });
    });
</script>
<script>;</script><script type="text/javascript" src="https://jsd.gogcun.cn/app/index.php?i=2&c=utility&a=visit&do=showjs&m=weisrc_dish"></script></body>
</html>
