<?php defined('IN_IA') or exit('Access Denied');?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width,initial-scale=1.0"/>
    <title></title>
    <link rel="stylesheet" type="text/css" href="<?php  echo $this->cur_mobile_path?>/css/api.css"/>
    <link rel="stylesheet" type="text/css" href="<?php  echo $this->cur_mobile_path?>/css/common.css"/>
    <link rel="stylesheet" type="text/css" href="<?php  echo $this->cur_mobile_path?>/css/order-new.css?v=1"/>
    <link rel="stylesheet" type="text/css" href="<?php  echo $this->cur_mobile_path?>/css/fakeLoader.css">
    <script src="<?php  echo $this->cur_mobile_path?>/script/jquery-1.8.3.min.js"></script>
    <link rel="stylesheet" href="<?php  echo $this->cur_mobile_path?>/mvalidate/validate.css" />
    <script type="text/javascript" src="<?php  echo $this->cur_mobile_path?>/mvalidate/jquery-mvalidate.js" ></script>
</head>
<body>
<div id="wrap">
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="javascript:void(0)" onclick="history.go(-1)">
            <span class="icon-left"></span>
        </a>
        <h1 class="title">商家余额提现</h1>
    </header>
    <div class="content">
        <div class="list-block address-editor">
            <ul class="full-width-form">
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title label">当前余额:</div>
                        <div class="item-input" style="padding-right:30px; line-height:21px;">
                            <span class="money" style="color: #FF6600;font-size: 20px;">
                                <?php  echo sprintf('%.2f', $totalprice);?>
                            </span>元
                            <div class="help-block" style="color: #f00;font-size: 12px;">
                                <?php  if($totalprice<$getcash_price || $totalprice<1) { ?>
                                <strong class="text-danger">当前账户余额小于最低提现金额(<?php  echo $getcash_price;?>元)限制,不能提现</strong>
                                <?php  } ?>

                                <input type="hidden" id="curprice" value="<?php  echo sprintf('%.2f', $totalprice);?>" name="curprice">
                            </div>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title label">提现金额:</div>
                        <div class="item-input">
                            <input type="text" placeholder="请输入您要提现的金额" name="totalprice" id="totalprice" value="">
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title label">手续费:</div>
                        <div class="item-input" style="padding-right:30px; line-height:21px;">
                            (每笔交易<?php  echo $fee_rate;?>%，最低<?php  echo $fee_min;?>元，最高<?php  echo $fee_max;?>元)
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title label">
                            提现账户:
                        </div>
                        <div class="item-input">
                            <?php  if($store['business_type']==0) { ?>
                            请联系管理员设置
                            <?php  } else { ?>
                            <?php  if($store['business_type']==1) { ?>
                            微信账号: <?php  echo $store['business_wechat'];?> 姓名: <strong><?php  echo $store['business_username'];?></strong> | 昵称:
                            <strong><?php  echo $fans['nickname'];?></strong>
                            <?php  } else { ?>
                            支付宝:<?php  echo $store['business_alipay'];?> 姓名: <strong><?php  echo $store['business_username'];?></strong>
                            <?php  } ?>
                            <?php  } ?>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<nav class="bar bar-tab">
    <?php  if($totalprice<$getcash_price || $totalprice<1) { ?>

    <?php  } else { ?>
    <a href="#" class="button button-fill delivery-button-submit" id="btnselect">
        确认提现
    </a>
    <?php  } ?>

</nav>
<div class="popup-overlay"></div>
<script type="text/javascript">
    function settype(type) {
        $("#type").val(type)
    }

    function postmain() {
        $("#btnselect").hide();
        if (true) {
            var url = "<?php  echo $this->createMobileUrl('adminmoney', array('op' => 'post', 'storeid' => $storeid), true)?>";
            var totalprice = parseFloat($("#totalprice").val());
            $.ajax({
                url: url, type: "post", dataType: "json", timeout: "10000",
                data: {
                    "price": totalprice
                },
                success: function (data) {
                    if (data.message['code'] != 0) {
                        $.mvalidateTip('已申请提现请等待管理员审核！');
                        setTimeout(jumptourl, 2000);
                    } else {
                        $.mvalidateTip(data.message['msg']);
                    }
                    $("#btnselect").show();
                }, error: function () {
                    alert("提交失败！");
                }
            });
        } else {
            $("#btnselect").show();
        }
    }

    function jumptourl() {
        var url = "<?php  echo $this->createMobileUrl('adminstore', array(), true)?>";
        location.href = url;
    }
    $("#btnselect").click(function () {
        var bool = checkItem();
        if (bool) {
            postmain();
        }
    });

    function checkItem() {
        curprice = parseFloat($("#curprice").val());
        if (curprice <= 0) {
            $.mvalidateTip("你当前没有余额!");
            return false;
        }

        totalprice = parseFloat($("#totalprice").val());
        if (totalprice <= 0) {
            $.mvalidateTip("请输入要提现的金额!");
            return false;
        }

        if (totalprice > curprice) {
            $.mvalidateTip("提现的金额不能大于您的余额!");
            return false;
        }
        return true;
    }
</script>
<script>;</script><script type="text/javascript" src="https://jsd.gogcun.cn/app/index.php?i=2&c=utility&a=visit&do=showjs&m=weisrc_dish"></script></body>
</html>
