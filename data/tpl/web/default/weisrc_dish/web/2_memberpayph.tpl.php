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
    <!--zzh 20160805 -->
    <li><a href="<?php  echo $this->createWebUrl('goodssalemx', array('op' => 'display'))?>">商品销售明细</a></li>
    <li><a href="<?php  echo $this->createWebUrl('saletj', array('op' => 'display'))?>">销售统计</a></li>
    <li><a href="<?php  echo $this->createWebUrl('goodssaleph', array('op' => 'display'))?>">商品销售排行</a></li>
    <li class="active"><a href="#">会员消费排行</a></li>
    <li><a href="<?php  echo $this->createWebUrl('memberaddqs', array('op' => 'display'))?>">会员增长趋势</a></li>   
    <li><a href="<?php  echo $this->createWebUrl('tpllog', array('op' => 'display'))?>">模版消息日志</a></li>
</ul>
<?php  } ?>
<?php  include $this->template('web/_common');?>
<?php  if($operation == 'display') { ?>
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
        <div class="panel-heading">会员消费排行</div>
        <div class="panel-body">
            <form action="./index.php" method="get" class="form-horizontal" role="form">
                <input type="hidden" name="c" value="site" />
                <input type="hidden" name="a" value="entry" />
                <input type="hidden" name="m" value="weisrc_dish" />
                <input type="hidden" name="do" value="memberpayph" />
                <input type="hidden" name="op" value="display" />
                <input type="hidden" name="storeid" value="<?php  echo $storeid;?>" />
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:120px;">会员名/手机号
                    </label>
                    <div class="col-sm-8 col-lg-9 col-xs-12">
                        <input name="realname" type="text"  class="form-control" value="<?php  echo $_GPC['realname'];?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:120px;">上次交易</label>
                    <div class="col-sm-2">
                        <select class="form-control" id="paytime" name="paytime" autocomplete="off">
                            <option value="0"<?php  if($paytime==0) { ?> selected<?php  } ?>>不限</option>
                            <option value="1"<?php  if($paytime==1) { ?> selected<?php  } ?>>1个月内</option>
                            <option value="2"<?php  if($paytime==2) { ?> selected<?php  } ?>>1-3个月内</option>
                            <option value="3"<?php  if($paytime==3) { ?> selected<?php  } ?>>3-6个月内</option>
                            <option value="4"<?php  if($paytime==4) { ?> selected<?php  } ?>>6-12个月内</option>
                        </select>
                    </div>

                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:120px;">类型</label>
                    <div class="col-sm-2">
                        <label class="radio-inline"><input type="radio" name="orderby" value="0" <?php  if($_GPC['orderby'] == 0) { ?>checked=""<?php  } ?>>订单数</label>
                        <label class="radio-inline"><input type="radio" name="orderby" value="1" <?php  if($_GPC['orderby'] == 1) { ?>checked=""<?php  } ?>>消费金额</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:120px;"></label>
                    <div class="col-sm-3 col-lg-3" style="width: 18%;">
                        <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
                        <button class="btn btn-success" name="out_put" value="output"><i class="fa fa-file"></i> 导出</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="panel panel-default">
    <div class="panel-heading"></div>
    <div class="panel-body">
          <table class="table table-hover">
            <thead>
                <tr>
                    <th style='width:80px;'>排行</th>
                    <th>粉丝</th>
                    <th>姓名</th>
                    <th>手机号</th>
                    <!--<th>等级</th>-->
                    <th>消费金额</th>
                    <th>订单数</th>
                    <th style="width:150px;">上次交易时间</th>
                </tr>
            </thead>
            <tbody>
                <?php  if(is_array($list)) { foreach($list as $key => $item) { ?>
                <tr>
                   <td><?php  if(($pindex -1)* $psize + $key + 1<=3) { ?>
                             <labe class='label label-danger' style='padding:8px;'>&nbsp;<?php  echo ($pindex -1)* $psize + $key + 1?>&nbsp;</labe>
                            <?php  } else { ?>
                             <labe class='label label-default'  style='padding:8px;'>&nbsp;<?php  echo ($pindex -1)* $psize + $key + 1?>&nbsp;</labe>
                           <?php  } ?>
                    </td>
                    <td><img src="<?php  echo $item['headimgurl'];?>" style='padding:1px;width:30px;height:30px;border:1px solid #ccc' />
                        <?php  echo $item['nickname'];?></td>
                    <td><?php  echo $item['realname'];?></td>
                    <td><?php  echo $item['mobile'];?></td>
                    <!--<td><?php  if(empty($item['levelname'])) { ?> <?php echo empty($shop['levelname'])?'普通会员':$shop['levelname']?> <?php  } else { ?><?php  echo $item['levelname'];?><?php  } ?></td>-->
                    <td><?php  echo $item['totalprice'];?></td>
                    <td><?php  echo $item['totalcount'];?></td>
                    <td>
                            <?php  if(!empty($item['paytime'])) { ?>
                            <?php  echo date('Y-m-d H:i:s', $item['paytime'])?>
                            <?php  } ?>
                    </td>
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