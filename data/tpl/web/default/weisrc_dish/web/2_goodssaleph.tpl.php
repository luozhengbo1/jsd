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
    <li class="active"><a href="#">商品销售排行</a></li>
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
        <div class="panel-heading">查询商品销售量和销售额，默认排序为销售额从高到低</div>
        <div class="panel-body">
            <form action="./index.php" method="get" class="form-horizontal" role="form">
                <input type="hidden" name="c" value="site" />
                <input type="hidden" name="a" value="entry" />
                <input type="hidden" name="m" value="weisrc_dish" />
                <input type="hidden" name="do" value="goodssaleph" />
                <input type="hidden" name="op" value="display" />
                <input type="hidden" name="storeid" value="<?php  echo $storeid;?>" />
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:90px;">商品名称</label>
                    <div class="col-sm-2 col-lg-2">
                        <input class="form-control" name="title" type="text" value="<?php  echo $_GPC['title'];?>">
                    </div>
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width: 90px;">下单时间</label>
                    <div class="col-sm-7 col-lg-3 col-xs-12">
                        <?php  echo tpl_form_field_daterange('time', array('starttime'=>date('Y-m-d', $starttime),'endtime'=>date('Y-m-d', $endtime)));?>
                    </div>
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width: 90px;">所属门店</label>
                    <div class="col-sm-7 col-lg-2 col-xs-12">
                        <select name="storeid" class="form-control">
                            <option value="">全部门店</option>
                            <?php  if(is_array($storelist)) { foreach($storelist as $row) { ?>
                            <option value="<?php  echo $row['id'];?>"<?php  if($storeid==$row['id']) { ?> selected<?php  } ?>><?php  echo $row['title'];?></option>
                            <?php  } } ?>
                        </select>
                    </div>
                </div>                
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">排序方式</label>
                    <div class="col-sm-2 col-lg-2">
                        <label class="radio-inline"><input type="radio" name="orderby" value="0" <?php  if($_GPC['orderby'] == 0) { ?>checked=""<?php  } ?>>销售额</label>
                        <label class="radio-inline"><input type="radio" name="orderby" value="1" <?php  if($_GPC['orderby'] == 1) { ?>checked=""<?php  } ?>>销售量</label>
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
    <div class="panel-heading">总数: <span style='color:red'><?php  echo $total;?></span></div>
    <div class="panel-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style='width:80px;'>排行</th>
                    <th>商品名称</th>
                    <th>销售量</th>
                    <th>销售额</th>
                </tr>
            </thead>
            <tbody>
                <?php  if(is_array($list)) { foreach($list as $key => $row) { ?>
                <tr>
                    <td><?php  if(($pindex -1)* $psize + $key + 1<=3) { ?>
                             <labe class='label label-danger' style='padding:8px;'>&nbsp;<?php  echo ($pindex -1)* $psize + $key + 1?>&nbsp;</labe>
                            <?php  } else { ?>
                             <labe class='label label-default'  style='padding:8px;'>&nbsp;<?php  echo ($pindex -1)* $psize + $key + 1?>&nbsp;</labe>
                           <?php  } ?>
                        </td>
                    <td>
                        <img src="<?php  echo tomedia($row['thumb'])?>" style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                        <?php  echo $row['title'];?></td>
                    <td><?php  echo $row['sl'];?></td>
                    <td><?php  echo $row['price'];?></td>
                </tr>
                <?php  } } ?>
        </table>
        <?php  echo $pager;?>
    </div>      
</div>
   
    </form>
</div>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/footer', TEMPLATE_INCLUDEPATH)) : (include template('public/footer', TEMPLATE_INCLUDEPATH));?>