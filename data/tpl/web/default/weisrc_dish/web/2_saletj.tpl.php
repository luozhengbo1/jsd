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
    <li class="active"><a href="#">销售统计</a></li>
    <li><a href="<?php  echo $this->createWebUrl('goodssaleph', array('op' => 'display'))?>">商品销售排行</a></li>
    <li><a href="<?php  echo $this->createWebUrl('memberpayph', array('op' => 'display'))?>">会员消费排行</a></li>
    <li><a href="<?php  echo $this->createWebUrl('memberaddqs', array('op' => 'display'))?>">会员增长趋势</a></li>   
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
        <div class="panel-heading">筛选(不选择月份表示年统计，不选择日，则表示月统计)</div>
        <div class="panel-body">
            <form action="./index.php" method="get" class="form-horizontal" role="form">
                <input type="hidden" name="c" value="site" />
                <input type="hidden" name="a" value="entry" />
                <input type="hidden" name="m" value="weisrc_dish" />
                <input type="hidden" name="do" value="saletj" />
                <input type="hidden" name="op" value="display" />
                <input type="hidden" name="storeid" value="<?php  echo $storeid;?>" />
                <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:90px;">日期</label>
                <div class="col-sm-8 col-lg-2 col-xs-12">
                        <select name="year" class="form-control">
                             <?php  if(is_array($years)) { foreach($years as $y) { ?>
                            <option value="<?php  echo $y['data'];?>"  <?php  if($y['selected']) { ?>selected="selected"<?php  } ?>><?php  echo $y['data'];?>年</option>
                            <?php  } } ?>
                        </select>
                    </div>
                    
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:90px;">月份</label>
                <div class="col-sm-8 col-lg-2 col-xs-12">
                        <select name="month" class="form-control">
                           <option value=''>未选月份</option>
                            <?php  if(is_array($months)) { foreach($months as $m) { ?>
                            <option value="<?php  echo $m['data'];?>"  <?php  if($m['selected']) { ?>selected="selected"<?php  } ?>><?php  echo $m['data'];?>月</option>
                            <?php  } } ?>
                        </select>
                    </div>
                    
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:90px;">日</label>
                <div class="col-sm-8 col-lg-2 col-xs-12">
                        <select name="day" class="form-control">
                           option value=''>未选日期</option>
                        </select>
                    </div>
                </div>                             
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">类型</label>
                    <div class="col-sm-3 col-lg-2">
                        <label class="radio-inline"><input type="radio" name="type" value="0" <?php  if($_GPC['type'] == 0) { ?>checked=""<?php  } ?>>交易额</label>
                        <label class="radio-inline"><input type="radio" name="type" value="1" <?php  if($_GPC['type'] == 1) { ?>checked=""<?php  } ?>>交易量</label>
                    </div>
                    <div class="col-sm-3 col-lg-3" style="width: 18%;">
                        <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
                        <button class="btn btn-success" name="out_put" value="output"><i class="fa fa-file"></i> 导出</button>
                    </div>
                </div>
            </form>            
        </div>
    </div>
 <div class="panel panel-default">
    <div class='panel-heading'>
        数据统计 
        <?php  if(empty($type)) { ?>交易额<?php  } else { ?>交易量<?php  } ?>总数：<span style="color:red; "><?php  echo $totalcount;?></span>，
        最高<?php  if(empty($type)) { ?>交易额<?php  } else { ?>交易量<?php  } ?>：<span style="color:red; "><?php  echo $maxcount;?></span> <?php  if(!empty($maxcount_date)) { ?><span>(<?php  echo $maxcount_date;?></span>)<?php  } ?>
       
    </div>
    <div class="panel-body">    
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style='width:100px;'>
                        <?php  if(empty($_GPC['month'])) { ?>月份<?php  } else { ?>日期<?php  } ?>
                    </th>
                    <th style='width:200px;'><?php  if(empty($type)) { ?>交易额<?php  } else { ?>交易量<?php  } ?></th>
                    <th>所占比例</th>
                </tr>
            </thead>            
            <tbody>
            <?php  if(is_array($list)) { foreach($list as $item) { ?>
            <tr>
            <td><?php  echo $item['data'];?></td>
            <td><?php  echo $item['count'];?></td>
            <td>
                <div class="progress" style='max-width:500px;' >
                    <div style="width: <?php  echo $item['percent'];?>%;" class="progress-bar progress-bar-info" ><span style="color:#000"><?php echo empty($item['percent'])?'':$item['percent'].'%'?></span></div>
                </div>
            </td>
            <tr>
            <?php  } } ?>               
            </tbody>
        </table>   
    </div>
</div>    
    </form>
</div>
<?php  } ?>
<script language='javascript'>
    function get_days(){
          
        var year = $('select[name=year]').val();
        var month =$('select[name=month]').val();
        var day  = $('select[name=day]');
       day.get(0).options.length = 0 ;
        if(month==''){
       day.append("<option value=''>未选日期</option");
            return;
        }
       
        day.get(0).options.length = 0 ;
        day.append("<option value=''>计算天数...</option").attr('disabled',true);
        $.post("<?php  echo $this->createWebUrl('saletj',array('op'=>'days'))?>",{year:year,month:month},function(days){
             day.get(0).options.length = 0 ;
             day.removeAttr('disabled');
             days =parseInt(days);
             day.append("<option value=''>未选日期</option");
             for(var i=1;i<=days;i++){
                 day.append("<option value='" + i +"'>" + i + "日</option");
             }
          
             <?php  if(!empty($day)) { ?>
                day.val( <?php  echo $day;?>);
             <?php  } ?>
        })
        
    }
    $('select[name=month]').change(function(){
           get_days();
    })
    
    get_days();
 </script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/footer', TEMPLATE_INCLUDEPATH)) : (include template('public/footer', TEMPLATE_INCLUDEPATH));?>