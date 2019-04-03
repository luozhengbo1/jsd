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
    <li class="active"><a href="#">商品销售明细</a></li>
    <li><a href="<?php  echo $this->createWebUrl('saletj', array('op' => 'display'))?>">销售统计</a></li>  
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
        <div class="panel-heading">筛选</div>
        <div class="panel-body">
            <form action="./index.php" method="get" class="form-horizontal" role="form">
                <input type="hidden" name="c" value="site" />
                <input type="hidden" name="a" value="entry" />
                <input type="hidden" name="m" value="weisrc_dish" />
                <input type="hidden" name="do" value="goodssalemx" />
                <input type="hidden" name="op" value="display" />
                <input type="hidden" name="storeid" value="<?php  echo $storeid;?>" />
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:90px;">订单号</label>
                    <div class="col-sm-2 col-lg-2">
                        <input class="form-control" name="ordersn" id="" type="text" value="<?php  echo $_GPC['ordersn'];?>">
                    </div>
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:90px;">订单状态</label>
                    <div class="col-sm-8 col-lg-2 col-lg-2xs-12">
                        <select name="status" class="form-control">
                            <option value="">不限</option>
                            <option value="3" <?php  if($_GPC['status'] == 3) { ?> selected="selected" <?php  } ?>>已完成</option>
                            <option value="1" <?php  if($_GPC['status'] == 1) { ?> selected="selected" <?php  } ?>>已确认</option>
                            <option value="0" <?php  if($_GPC['status'] == 0 && isset($_GPC['status']) && $_GPC['status'] != '') { ?> selected="selected" <?php  } ?>>待处理</option>
                            <option value="-1" <?php  if($_GPC['status'] == -1) { ?> selected="selected" <?php  } ?>>已取消</option>
                        </select>
                    </div>
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width: 90px;">支付方式</label>
                    <div class="col-sm-7 col-lg-2 col-xs-12">
                        <select name="paytype" class="form-control">
                            <option value="">不限</option>
                            <option value="0" <?php  if($_GPC['paytype'] == 0 && isset($_GPC['paytype']) && $_GPC['paytype'] != '') { ?> selected="selected" <?php  } ?>>未确认</option>
                            <option value="1" <?php  if($_GPC['paytype'] == 1) { ?> selected="selected" <?php  } ?>>余额支付</option>
                            <option value="2" <?php  if($_GPC['paytype'] == 2) { ?> selected="selected" <?php  } ?>>微信支付</option>
                            <option value="3" <?php  if($_GPC['paytype'] == 3) { ?> selected="selected" <?php  } ?>>现金付款</option>
                            <option value="2" <?php  if($_GPC['paytype'] == 4) { ?> selected="selected" <?php  } ?>>支付宝</option>
                        </select>
                    </div>
                </div>                
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:90px;">桌台号</label>
                    <div class="col-sm-2 col-lg-2">
                        <input class="form-control" name="tableid" id="tableid" type="text" value="<?php  echo $_GPC['tableid'];?>">
                    </div>
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width: 90px;">订单类型</label>
                    <div class="col-sm-7 col-lg-2 col-xs-12">
                        <select name="dining_mode" class="form-control">
                            <option value="">不限</option>
                            <option value="1" <?php  if($_GPC['dining_mode'] == 1) { ?> selected="selected" <?php  } ?>>堂点</option>
                            <option value="2" <?php  if($_GPC['dining_mode'] == 2) { ?> selected="selected" <?php  } ?>>外卖</option>
                            <option value="3" <?php  if($_GPC['dining_mode'] == 3) { ?> selected="selected" <?php  } ?>>预定</option>
                            <option value="4" <?php  if($_GPC['dining_mode'] == 4) { ?> selected="selected" <?php  } ?>>快餐</option>
                        </select>
                    </div>
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width: 90px;">下单时间</label>
                    <div class="col-sm-7 col-lg-3 col-xs-12">
                        <?php  echo tpl_form_field_daterange('time', array('starttime'=>date('Y-m-d', $starttime),'endtime'=>date('Y-m-d', $endtime)));?>
                    </div>
                </div>
                <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width: 90px;">商品</label>
                    <div class="col-sm-7 col-lg-2">
                         <select class="form-control" id="selgoodsid" name="selgoodsid" autocomplete="off">
                            <option value=""<?php  if($selgoodsid==0) { ?> selected<?php  } ?>>不限</option>
                            <?php  if(is_array($goodslist)) { foreach($goodslist as $item) { ?>
                            <option value="<?php  echo $item['id'];?>"<?php  if($selgoodsid==$item['id']) { ?> selected<?php  } ?>><?php  echo $item['title'];?></option>
                            <?php  } } ?>
                        </select>
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
        <form action="" method="post" class="form-horizontal form" >
            <input type="hidden" name="storeid" value="<?php  echo $storeid;?>" />
            <div class="table-responsive panel-body">
                <table class="table table-hover">
                    <thead class="navbar-inner">
                    <tr>
                        <th style="width:14%;">订单号</th>
                        <th style="width:5%;">商品金额</th>
                        <th style="width:10%;">商品</th>
                        <th style="width:4%;">数量</th>
                        <th style="width:4%;">类型</th>
                        <th style="width:5%;">桌号</th>
                        <th style="width:8%;">状态</th>
                        <th style="width:8%;">支付状态</th>
                        <th style="width:12%;">下单时间</th>
                        
                    </tr>
                    </thead>
                    <tbody>
                    <?php  if(is_array($list)) { foreach($list as $item) { ?>
                    <tr>
                        <td style="vnd.ms-excel.numberformat:@;"><?php  echo $item['ordersn'];?><br/>
                            <font color="#5cb85c"><?php  echo $storelist[$item['storeid']]['title'];?></font></td>
                        <td>￥<?php  echo $item['price'];?></td>						
                        <td>
                            <?php  echo $item['title'];?>                            
                        </td>
                         <td>
                            <?php  echo $item['total'];?>                            
                        </td>
                        <td>
                            <?php  if($item['dining_mode']==1) { ?><span class="btn btn-info btn-sm" title="堂点" style="background-color: #9585bf;border-color: #9585bf;"><i class="fa fa-cutlery"></i></span><?php  } ?>
                            <?php  if($item['dining_mode']==2) { ?><span class="btn btn-info btn-sm" title="外卖"  style="background-color: #4f99c6;border-color: #4f99c6;"><i class="fa fa-truck"></i></span><?php  } ?>
                            <?php  if($item['dining_mode']==3) { ?><span class="btn btn-info btn-sm" title="预定" style="background-color: #fee188;border-color: #fee188;"><i class="fa fa-calendar"></i></span><?php  } ?>
                            <?php  if($item['dining_mode']==4) { ?><span class="btn btn-info btn-sm" title="快餐" style="background-color: #be386a;border-color: #be386a;"><i class="fa fa-delicious"></i></span><?php  } ?>
                        </td>
                        <td>
                            <?php  echo $item['tabletitle'];?>                            
                        </td>
                        <td>
                            <?php  if($item['status'] == 0) { ?><span class="label label-info">待处理</span><?php  } ?>
                            <?php  if($item['status'] == 1) { ?><span class="label label-warning">已确认</span><?php  } ?>
                            <?php  if($item['status'] == 2) { ?><span class="label label-success">已并台</span><?php  } ?>
                            <?php  if($item['status'] == 3) { ?><span class="label label-success">已完成</span><?php  } ?>
                            <?php  if($item['status'] == -1) { ?><span class="label label-danger">已取消</span><?php  } ?>
                        </td>
                        <td>
                            <?php  if($item['paytype'] == 0) { ?>未确认<?php  } ?>
                            <?php  if($item['paytype'] == 1) { ?>余额支付<?php  } ?>
                            <?php  if($item['paytype'] == 2) { ?>微信支付<?php  } ?>
                            <?php  if($item['paytype'] == 3) { ?>现金付款<?php  } ?>
                            <?php  if($item['paytype'] == 4) { ?>支付宝付款<?php  } ?>
                            <br/>
                            <?php  if($item['ispay'] == 0) { ?><span class="label label-default">未支付</span><?php  } ?>
                            <?php  if($item['ispay'] == 1) { ?><span class="label label-success">已支付</span><?php  } ?>
                        </td>
                        <td>
                            <?php  echo date("Y-m-d", $item['dateline'])?><br/>
                            <?php  echo date("H:i:s", $item['dateline'])?>
                        </td>                        
                    </tr>
                    <?php  } } ?>
                    </tbody>
                </table>
                <?php  echo $pager;?>
            </div>
        </form>
    </div>
    </form>
</div>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/footer', TEMPLATE_INCLUDEPATH)) : (include template('public/footer', TEMPLATE_INCLUDEPATH));?>