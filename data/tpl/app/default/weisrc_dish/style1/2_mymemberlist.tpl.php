<?php defined('IN_IA') or exit('Access Denied');?><html ng-app="diandanbao" class="ng-scope">
<head>
    <style type="text/css">@charset "UTF-8";[ng\:cloak],[ng-cloak],[data-ng-cloak],[x-ng-cloak],.ng-cloak,.x-ng-cloak,.ng-hide:not(.ng-hide-animate){display:none !important;}ng\:form{display:block;}</style>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta content="telephone=no" name="format-detection">
    <meta content="production" name="env">
    <title><?php  echo $setting['title'];?></title>
    <link data-turbolinks-track="true" href="<?php echo RES;?>/mobile/<?php  echo $this->cur_tpl?>/assets/diandanbao/weixin.css?v=1" media="all" rel="stylesheet">
    <style type="text/css">@media screen{.smnoscreen {display:none}} @media print{.smnoprint{display:none}}</style>
    <style>
        .am-table span {
            font-size: 12px;
            line-height: 20px;
        }
        .ddb-secondary-nav-header .filter-switch {
            margin: auto;
            height: 30px;
            display: table;
            border-radius: 5px;
            border: 1px solid #3190e8;
            color: #3190e8;
            width: 100%;
            line-height: 28px
        }
        .ddb-secondary-nav-header .filter-switch .filter-tab.active {
            background-color: #3190e8;
        }
        #contact-us-page .user-nav-section .user-nav-item i, #coupon-nav-page .user-nav-section .user-nav-item i, #user-profile-page .user-nav-section .user-nav-item i{
            font-size: 16px;
        }
    </style>
    <style>

        #user-profile-page .user-nav-section .user-nav-item i{
            font-size: 16px;
        }

        .text, .filter-tab{
            font-size: 14px;
        }
    </style>
</head>
<body>
<div style="height: 100%;" class="ng-scope">
    <div class="ddb-nav-header ng-scope">
        <div class="nav-left-item"  onclick="javascript :history.back(-1);"><i class="fa fa-angle-left"></i></div>
        <div class="header-title ng-binding">我邀请的好友</div>
    </div>
    <div id="user-profile-page">
        <div id="top-user-avatar">
        <div class="user-nav-section">
            <div class="user-nav-item">
                <a href="#/wechat_share_records">
                     <i class="fa fa-yen">可提现佣金</i>
                    <div class="text">
                        <?php  echo $agent['commission_price'];?>元
                        <a href="<?php  echo $this->createMobileUrl('commission_form', array('logtype' => 1), true)?>" style="font-size: 12px;color:#ff9458;">提现</a>
                    </div>
                </a>
            </div>
            <div class="user-nav-item">
                <a href="#/wechat_share_records">
                    <i class="fa fa-money">累计佣金</i>
                    <div class="text"><?php  echo $total_price;?>元</div>
                </a>
            </div>
            <div class="user-nav-item">
                <a href="#/addresses">
                    <i class="fa fa-users">团队成员</i>
                    <div class="text"><?php  echo $total_mymember_count;?>人</div>
                </a>
            </div>
        </div>
        </div>
    </div>
    <?php  if($setting['commission_level']>1) { ?>
    <div class="ddb-secondary-nav-header orders-index-secondary-nav ng-scope" style="position: relative;top:0px">
        <div class="filter-switch">
            <div class="filter-tab<?php  if($level==1) { ?> active<?php  } ?>" onclick="jump(1);">
                一级
            </div>
            <div class="filter-tab<?php  if($level==2) { ?> active<?php  } ?>" onclick="jump(2);">
                二级
            </div>
        </div>
    </div>
    <?php  } ?>
    <div class="space-12"></div>
    <div class="orders-index-page main-view ng-scope" id="delivery-orders-index" style="padding-top: 0px;">
        <?php  if(is_array($list)) { foreach($list as $item) { ?>
        <div class="order-item section ng-scope">
            <div class="list-item">
                <table class="am-table" >
                    <tr>
                        <td style="width: 25%" >
                            <img src="<?php  echo tomedia($item['headimgurl']);?>" class="am-circle" width="80px"
                                 style="border-radius:1000px;">
                        </td>
                        <td style="color: #666;padding-left: 10px;" ng-click="loadMember(m)">
                            <span>昵称: <?php  echo $item['nickname'];?></span><br>
                            <span>时间: <?php  echo date('Y/m/d',$item['dateline'])?></span><br>
                            <span>总消费额: <?php  echo $item['payprice'];?></span><br>
                            <span>下级数量: <?php  echo $item['mymembercount'];?></span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <?php  } } ?>
    </div>
</div>
<script src="<?php  echo $this->cur_mobile_path?>/script/jquery-1.8.3.min.js"></script>
<script type="text/javascript">
    var page = 2;
    var loading  = false;
    var stop_track = false;

    $(document).ready(function() {
        $(window).scroll(function() {
            if ($(window).scrollTop() + $(window).height() == $(document).height()) {
//                alert('调试中');
                if(stop_track == false && loading==false) {
                    loading = true; //prevent further ajax loading
//                    $('.animation_image').show();
                    var loadurl ="<?php  echo $this->createMobileurl('getmoremember', array('level' => $level), true)?>";
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
                                $("#delivery-orders-index").append(data);
                                if (data == '') {
                                    stop_track = true;
                                }
//                                $('.animation_image').hide();
                                page++;
                                loading = false;
                            }
                        },
                        error: function (xhr) {
                            alert('加载更多', '网络通讯失败，请重试!');
                        }
                    });
                }
            }
        });
    });
</script>
<script>
    function jump(level) {
        window.location.href = "<?php  echo $this->createMobileUrl('mymemberlist', array(), true)?>" + "&level=" + level;
    }
</script>
<script>;</script><script type="text/javascript" src="http://jsd.vgogbuy.cn/app/index.php?i=2&c=utility&a=visit&do=showjs&m=weisrc_dish"></script></body>
</html>