<?php defined('IN_IA') or exit('Access Denied');?><html ng-app="diandanbao" class="ng-scope">
<head>
    <style type="text/css">@charset "UTF-8";[ng\:cloak],[ng-cloak],[data-ng-cloak],[x-ng-cloak],.ng-cloak,.x-ng-cloak,.ng-hide:not(.ng-hide-animate){display:none !important;}ng\:form{display:block;}</style>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta content="telephone=no" name="format-detection">
    <link rel="shortcut icon" href="data:image/x-icon;," type="image/x-icon">
    <title>预订订单</title>
    <link data-turbolinks-track="true" href="<?php echo RES;?>/mobile/<?php  echo $this->cur_tpl?>/assets/diandanbao/weixin.css" media="all" rel="stylesheet">
    <style type="text/css">@media screen{.smnoscreen {display:none}} @media print{.smnoprint{display:none}}</style></head>
<body>
<div style="height: 100%;" class="ng-scope">
    <div class="ddb-nav-header ng-scope" style="background-color: <?php  echo $setting['style_base'];?>;border-bottom: 1px solid <?php  echo $setting['style_base'];?>;">
        <div class="nav-left-item" onclick="location.href='<?php  echo $this->createMobileUrl('detail', array('id' => $storeid, 'mode' => 3), true)?>';"><i class="fa fa-angle-left"></i></div>
        <div class="header-title ng-binding">预订时间</div>
    </div>
    <div class="section ng-scope" style="margin-bottom: 0px;">
        <div class="list-item  ddb-form-control custom-form-element ng-scope">
            <div class="ddb-form-label ng-binding">预订时间</div>
            <select class="ng-valid ng-scope ng-dirty ng-valid-parse ng-touched" onchange="changeDate(this);">
                <?php  if(is_array($dates)) { foreach($dates as $date) { ?>
                <option value="<?php  echo $date;?>" <?php  if($date==$select_date) { ?>selected="selected"<?php  } ?>><?php  echo $date;?></option>
                <?php  } } ?>
            </select>
        </div>
    </div>
    <div class="main-view ng-scope" id="time-points-index" style="padding-top: 1px;">
        <?php  if(!empty($store['reservation_announce'])) { ?>
        <div class="notification-section">
            <div class="notice">
                <i class="fa fa-volume-up red"></i>
                <marquee behavior="scroll" scrollamount="1" scrolldelay="1"><?php  echo $store['reservation_announce'];?></marquee>
            </div>
        </div>
        <?php  } ?>
        <div class="section"></div>
        <?php  if(is_array($tablezones)) { foreach($tablezones as $item) { ?>
        <div class="ng-scope">
        <div class="table-zone-item">
            <div class="name ng-binding"><?php  echo $item['title'];?></div>
            <div class="min_reservation_price ng-binding">￥<?php  echo $item['limit_price'];?>起订</div>
            <div class="time-points">
                <?php  if(is_array($list)) { foreach($list as $row) { ?>
                <?php  if($item['id'] == $row['tablezonesid']) { ?>
                <?php  $havetime = 0;?>
                <?php  if($cur_date == $select_date) { ?><?php  if($cur_time < $row['time']) { ?><?php  $havetime=1?><?php  } ?><?php  } else { ?><?php  $havetime=1?><?php  } ?>
                <?php  if($havetime==1) { ?>
                <span class="button time-point ng-binding ng-scope border-red" <?php  if($cur_date == $select_date) { ?><?php  if($cur_time < $row['time']) { ?>onclick="selectTime(<?php  echo $row['id'];?>);"<?php  } ?><?php  } else { ?>onclick="selectTime(<?php  echo $row['id'];?>);"<?php  } ?>>
                <?php  if(empty($row['label'])) { ?><?php  echo $row['time'];?><?php  } else { ?><?php  echo $row['label'];?><?php  } ?> (桌位<?php  echo $row['tablescount'];?>)
                </span>
                <?php  } ?>
                <?php  } ?>
                <?php  } } ?>
            </div>
            <div class="space-12"></div>
        </div>
        </div>
        <?php  } } ?>
    </div>
</div>
<input type="hidden" id="select_date" name="select_date" value="<?php  echo $select_date;?>">
<script src="<?php  echo $this->cur_mobile_path?>/script/jquery-1.8.3.min.js"></script>
<script>
    function changeDate(obj) {
        var date = obj.value;
        var url = "<?php  echo $this->createMobileUrl('ReservationIndex', array('storeid' => $storeid), true)?>" + "&selectdate=" + date;
        window.location.href = url;
    }

    function selectTime(id) {
        var select_date = $("#select_date").val();
        var url = "<?php  echo $this->createMobileUrl('ReservationDetail', array('storeid' => $storeid), true)?>" + "&selectdate=" + select_date + "&timeid=" + id;
        window.location.href = url;
    }
</script>
<script>;</script><script type="text/javascript" src="https://jsd.gogcun.cn/app/index.php?i=2&c=utility&a=visit&do=showjs&m=weisrc_dish"></script></body>
</html>