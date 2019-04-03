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
    <title>全部评论</title>
    <link data-turbolinks-track="true" href="<?php echo RES;?>/mobile/<?php  echo $this->cur_tpl?>/assets/diandanbao/weixin.css" media="all" rel="stylesheet">
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
</head>
<body>

<!-- ngView:  -->
<div ng-view="" style="height: 100%;" class="ng-scope">
    <div class="ddb-nav-header ng-scope"  style="background-color: <?php  echo $setting['style_base'];?>;border-bottom: 1px solid <?php  echo $setting['style_base'];?>;">
        <div class="nav-left-item"  onclick="location.href='<?php  echo $this->createMobileUrl('detail', array('id' => $storeid), true)?>';"><i class="fa fa-angle-left"></i></div>
        <div class="header-title ng-binding"><?php  echo $store['title'];?></div>
    </div>
    <div id="ddb-comments-index" class="main-view ng-scope">
        <div class="section comments-section">
            <div class="list-item ng-hide" ng-show="comments.length == 0">该门店目前还没有评论</div>
            <div ng-comments="comments" class="ng-isolate-scope">
                <?php  if(is_array($feedbacklist)) { foreach($feedbacklist as $item) { ?>
                <div class="comment-item ng-scope">
                    <div class="comment-info">
                        <div class="nickname ng-binding"><?php  echo $item['nickname'];?></div>
                        <div class="comment-level red">
                            <i class="fa <?php  if($item['star']>=1) { ?>fa-star<?php  } else { ?>fa-star-o<?php  } ?> ng-scope"></i>
                            <i class="fa <?php  if($item['star']>=2) { ?>fa-star<?php  } else { ?>fa-star-o<?php  } ?> ng-scope"></i>
                            <i class="fa <?php  if($item['star']>=3) { ?>fa-star<?php  } else { ?>fa-star-o<?php  } ?> ng-scope"></i>
                            <i class="fa <?php  if($item['star']>=4) { ?>fa-star<?php  } else { ?>fa-star-o<?php  } ?> ng-scope"></i>
                            <i class="fa <?php  if($item['star']>=5) { ?>fa-star<?php  } else { ?>fa-star-o<?php  } ?> ng-scope"></i>
                        </div>
                    </div>
                    <div class="time ng-binding"><?php  echo date("Y-m-d", $item['dateline'])?></div>
                    <div class="content ng-binding"><?php  echo $item['content'];?></div>
                </div>
                <?php  if($item['replycontent']) { ?>
                <div class="comment-item ng-scope">
                    <div class="comment-info">
                        <div class="nickname ng-binding">管理员：</div>
                    </div>
                    <div class="content ng-binding"><?php  echo $item['replycontent'];?></div>
                </div>
                <?php  } ?>
                <?php  } } ?>
            </div>
        </div>
    </div>
</div>
<script>;</script><script type="text/javascript" src="https://jsd.gogcun.cn/app/index.php?i=2&c=utility&a=visit&do=showjs&m=weisrc_dish"></script></body>
</html>