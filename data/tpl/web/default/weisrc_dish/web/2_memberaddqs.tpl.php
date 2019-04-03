<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/header', TEMPLATE_INCLUDEPATH)) : (include template('public/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/comhead', TEMPLATE_INCLUDEPATH)) : (include template('public/comhead', TEMPLATE_INCLUDEPATH));?>
<style>
    /*top1.html*/
    .topleft1{background-color:#f8f8f8; height:58px; border:1px solid #ebebeb;margin-bottom: 10px;}
    .topright1 li{display:inline-block; line-height:60px; font-size:16px; color:#666; width:210px; padding-left:10px;}
    .topright1 li a{font-size:16px;}
    .xian{border-left:1px solid #DCDCDC; line-height:45px; display:block; padding-left:10px;}
    .topright1 li img{margin-left:5px; width:28px; vertical-align:middle; margin-top:-2px;}
</style>
<?php  if(!empty($storeid)) { ?>
<?php  echo $this -> set_tabbar($action, $storeid);?>
<?php  } else { ?>
<ul class="nav nav-tabs">
    <li><a href="<?php  echo $this->createWebUrl('allorder', array('op' => 'display'))?>">订单管理</a></li>
    <li><a href="<?php  echo $this->createWebUrl('goodssalemx', array('op' => 'display'))?>">商品销售明细</a></li>
    <li><a href="<?php  echo $this->createWebUrl('saletj', array('op' => 'display'))?>">销售统计</a></li>  
    <li><a href="<?php  echo $this->createWebUrl('goodssaleph', array('op' => 'display'))?>">商品销售排行</a></li>
    <li><a href="<?php  echo $this->createWebUrl('memberpayph', array('op' => 'display'))?>">会员消费排行</a></li>
    <li class="active"><a href="#">会员增长趋势</a></li>   
    <li><a href="<?php  echo $this->createWebUrl('tpllog', array('op' => 'display'))?>">模版消息日志</a></li>
</ul>
<?php  } ?>
<?php  include $this->template('web/_common');?>
<?php  if($operation == 'display') { ?>
<!--<script language="JavaScript">-->
    <!--function myrefresh(){-->
        <!--window.location.reload();-->
    <!--}-->
    <!--setTimeout('myrefresh()',2000); //指定1秒刷新一次-->
<!--</script>-->
<style>
    .page-nav {
        margin: 0;
        width: 100%;
        min-width: 800px;
    }

    .page-nav > li > a {
        display: block;
    }

    .page-nav-tabs {
        background: #EEE;
    }

    .page-nav-tabs > li {
        line-height: 40px;
        float: left;
        list-style: none;
        display: block;
        text-align: -webkit-match-parent;
    }

    .page-nav-tabs > li > a {
        font-size: 14px;
        color: #666;
        height: 40px;
        line-height: 40px;
        padding: 0 10px;
        margin: 0;
        border: 1px solid transparent;
        border-bottom-width: 0px;
        -webkit-border-radius: 0;
        -moz-border-radius: 0;
        border-radius: 0;
    }

    .page-nav-tabs > li > a, .page-nav-tabs > li > a:focus {
        border-radius: 0 !important;
        background-color: #f9f9f9;
        color: #999;
        margin-right: -1px;
        position: relative;
        z-index: 11;
        border-color: #c5d0dc;
        text-decoration: none;
    }

    .page-nav-tabs >li >a:hover {
        background-color: #FFF;
    }

    .page-nav-tabs > li.active > a, .page-nav-tabs > li.active > a:hover, .page-nav-tabs > li.active > a:focus {
        color: #576373;
        border-color: #c5d0dc;
        border-top: 2px solid #4c8fbd;
        border-bottom-color: transparent;
        background-color: #FFF;
        z-index: 12;
        margin-top: -1px;
        box-shadow: 0 -2px 3px 0 rgba(0, 0, 0, 0.15);
    }
</style>
<div class="main">   
    <div class="panel panel-default">
        <div class="panel-heading">筛选(不选择月份表示年统计)</div>
        <div class="panel-body">
            <form action="./index.php" method="get" class="form-horizontal" role="form" onsubmit='return checkform()'>
                <input type="hidden" name="c" value="site" />
                <input type="hidden" name="a" value="entry" />
                <input type="hidden" name="m" value="weisrc_dish" />
                <input type="hidden" name="do" value="memberaddqs" />
                <input type="hidden" name="op" value="display" />
                <input type="hidden" name="storeid" value="<?php  echo $storeid;?>" />
                <input type="hidden" name="search" value="1" />
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:90px;">最近</label>
                    <div class="col-sm-2">
                        <select class="form-control" id="days" name="days" autocomplete="off">
                        <option value="7"  <?php  if($days==7) { ?>selected<?php  } ?>>7天</option>
                        <option value="14"  <?php  if($days==14) { ?>selected<?php  } ?>>14天</option>
                        <option value="30"  <?php  if($days==30) { ?>selected<?php  } ?>>30天</option>
                        <option value=""  <?php  if($days=='') { ?>selected<?php  } ?>>按日期</option>                           
                        </select>
                    </div>
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:90px;">日期</label>
                    <div class="col-sm-8 col-lg-2 col-xs-12">
                        <select id='year' name="year" class="form-control">
                            <option value=''>未选年份</option>
                            <?php  if(is_array($years)) { foreach($years as $y) { ?>
                            <option value="<?php  echo $y['data'];?>"  <?php  if($y['selected']) { ?>selected="selected"<?php  } ?>><?php  echo $y['data'];?>年</option>
                            <?php  } } ?>
                        </select>
                    </div>
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:90px;">月份</label>
                    <div class="col-sm-8 col-lg-2 col-xs-12">
                        <select id='month' name="month" class="form-control">
                            <option value=''>未选月份</option>
                            <?php  if(is_array($months)) { foreach($months as $m) { ?>
                            <option value="<?php  echo $m['data'];?>"  <?php  if($m['selected']) { ?>selected="selected"<?php  } ?>><?php  echo $m['data'];?>月</option>
                            <?php  } } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:90px;"></label>
                    <div class="col-sm-2">
                        <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="panel panel-default">
    <div class="panel-heading">趋势图示例</div>
    <div class="panel-body">
        <div id="container" style="min-width: 300px; height: 400px; margin: 0 auto"></div>  
    </div>
</div>
<script type="text/javascript" src="<?php  echo $_W['siteroot'];?>web/resource/components/ueditor/third-party/highcharts/highcharts.js"></script>
<script type="text/javascript">
   
   function checkform(){   
 
       if($('#days').val()==''){    
           if($('#year').val()==''){    
               alert('请选择年份!');
               return false;
           }
       }
       return true;
   }
 
      $('#days').change(function(){
            if($(this).val()!=''){ 
                $('#year').val('');
                $('#month').val('').attr('disabled',true);;
            }
          
        })
       $('#year').change(function(){
            if($(this).val()==''){ 
                $('#month').val('').attr('disabled',true);
            }
            else{
                $('#days').val('');
                $('#month').removeAttr('disabled');
            }
        })
        
    $(function () {
   
        
        
        $('#container').highcharts({
        chart: {
            type: 'line'
        },
        title: {
             text: '<?php  echo $charttitle;?>',
        },
        subtitle: {
            text: ''
        },
        colors: [
'#0061a5',
'#ff0000'
],
        xAxis: {
            categories: [    
                 <?php  if(is_array($datas)) { foreach($datas as $key => $row) { ?>           
                   <?php  if($key>0) { ?>,<?php  } ?>"<?php  echo $row['date'];?>"
                   <?php  } } ?>]
        },
        yAxis: {
            title: {
                text: '人数'
            },allowDecimals:false
        },
        tooltip: {
            enabled: false,
            formatter: function() {
                return '<b>'+ this.series.name +'</b><br>'+this.x +': '+ this.y +'°C';
            }
        },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: true
                },
                enableMouseTracking: false
            }
        },
        series: [
            {
               name: '会员',
               data: [
                   <?php  if(is_array($datas)) { foreach($datas as $key => $row) { ?>                   
                   <?php  if($key>0) { ?>,<?php  } ?><?php  echo $row['mcount'];?>                   
                   <?php  } } ?>
               ]
            } ]
    });
    
});
</script>
    
   
   
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/footer', TEMPLATE_INCLUDEPATH)) : (include template('public/footer', TEMPLATE_INCLUDEPATH));?>