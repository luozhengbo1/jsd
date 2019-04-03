<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/header', TEMPLATE_INCLUDEPATH)) : (include template('public/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/comhead', TEMPLATE_INCLUDEPATH)) : (include template('public/comhead', TEMPLATE_INCLUDEPATH));?>
<style>
    .label-success,.btn-success{
        background-color: #1ab394;
        color: #FFFFFF;
    }
    .label-info,.btn-info{
        background-color: #1c84c6;
        color: #FFFFFF;
    }
    .label-danger,.btn-danger{
        background-color: #ed5565;
        color: #FFFFFF;
    }
</style>

<ul class="nav nav-tabs">
    <?php  if($logtype == 1) { ?>
    <li><a href="<?php  echo $this->createWebUrl('allfans', array('op' => 'display'))?>">会员管理</a></li>
    <li><a href="<?php  echo $this->createWebUrl('allfans', array('op' => 'record'))?>">会员交易统计</a></li>
    <li><a href="<?php  echo $this->createWebUrl('commission', array())?>">推广佣金管理</a></li>
    <?php  if($_W['role'] == 'manager' || $_W['isfounder']) { ?>
    <li <?php  if($operation == 'display' || $operation == 'post') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('cashlog', array('op' => 'display', 'logtype' => 1))?>">佣金提现管理</a></li>
    <?php  } ?>
    <?php  } else { ?>
    <li><a href="<?php  echo $this->createWebUrl('delivery', array('op' => 'display'))?>">配送员管理</a></li>
    <li><a href="<?php  echo $this->createWebUrl('delivery', array('op' => 'post'))?>">添加配送员</a></li>
    <li><a href="<?php  echo $this->createWebUrl('delivery', array('op' => 'setting'))?>">配送详情设置</a></li>
    <li><a href="<?php  echo $this->createWebUrl('deliveryarea', array('op' => 'display'))?>">管理配送点</a></li>
    <li><a href="<?php  echo $this->createWebUrl('deliveryarea', array('op' => 'post'))?>">添加配送点</a></li>
    <?php  if($_W['role'] == 'manager' || $_W['isfounder']) { ?>
    <li class="active"><a href="<?php  echo $this->createWebUrl('cashlog', array('op' => 'display', 'logtype' => 2))?>">配送提现管理</a></li>
    <?php  } ?>
    <?php  } ?>
</ul>
<?php  if($operation == 'display') { ?>
<div class="main">
    <div class="alert alert-info">
        <h4>
            <i class="fa fa-info-circle"></i>
            说明:佣金提现申请审核后微信账号会自动打款!
        </h4>
        <p style="color: red">&nbsp;&nbsp;&nbsp;&nbsp;结算数据涉及钱款操作，请认真审核，谨慎操作！</p>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">筛选</div>
        <div class="panel-body">
            <form action="./index.php" method="get" class="form-horizontal" role="form">
                <input type="hidden" name="c" value="site" />
                <input type="hidden" name="a" value="entry" />
                <input type="hidden" name="m" value="weisrc_dish" />
                <input type="hidden" name="do" value="cashlog" />
                <input type="hidden" name="op" value="display" />
                <input type="hidden" name="status" value="<?php  echo $_GPC['status'];?>" />
                <input type="hidden" name="type" value="<?php  echo $_GPC['type'];?>" />
                <input type="hidden" name="logtype" value="<?php  echo $_GPC['logtype'];?>" />
                <div class="form-group" style="margin-left: 40px">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">提现状态</label>
                    <div class="col-sm-8 col-lg-10 col-xs-12">
                        <div class="btn-group">
                            <a href="<?php  echo $this->createWebUrl('cashlog', array('op' => 'display', 'type' => $_GPC['type']))?>" class="btn <?php  if($_GPC['status'] == '') { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>" style="width: 80px">不限</a>
                            <a href="<?php  echo $this->createWebUrl('cashlog', array('op' => 'display', 'status' => 0, 'type' => $_GPC['type']))?>"
                               class="btn <?php  if($_GPC['status'] == 0 && isset($_GPC['status']) && $_GPC['status'] != '') { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>">待处理</a>
                            <a href="<?php  echo $this->createWebUrl('cashlog', array('op' => 'display', 'status' => 1, 'type' => $_GPC['type']))?>" class="btn <?php  if($_GPC['status'] == 1) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>">已提现</a>
                            <a href="<?php  echo $this->createWebUrl('cashlog', array('op' => 'display', 'status' => -1, 'type' => $_GPC['type']))?>" class="btn <?php  if($_GPC['status'] == -1) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>">提现失败</a>
                        </div>
                    </div>
                </div>
                <div class="form-group" style="margin-left: 40px">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">提现类型</label>
                    <div class="col-sm-8 col-lg-10 col-xs-12">
                        <div class="btn-group">
                            <a href="<?php  echo $this->createWebUrl('cashlog', array('op' => 'display', 'status' => $_GPC['status']))?>" class="btn <?php  if($_GPC['type'] == '') { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>" style="width: 80px">不限</a>
                            <a href="<?php  echo $this->createWebUrl('cashlog', array('op' => 'display', 'type' => 1, 'status' => $_GPC['status']))?>" class="btn <?php  if($_GPC['type'] == 1) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>">微信</a>
                            <a href="<?php  echo $this->createWebUrl('cashlog', array('op' => 'display', 'type' => 2, 'status' => $_GPC['status']))?>" class="btn <?php  if($_GPC['type'] == 2) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>">余额</a>
                        </div>
                    </div>
                </div>
                <div class="form-group" style="margin-left: 40px">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" >下单时间</label>
                    <div class="col-sm-7 col-lg-3 col-xs-12">
                        <div class="input-group-btn">
                            <?php  echo tpl_form_field_daterange('time', array('starttime'=>date('Y-m-d', $starttime),'endtime'=>date('Y-m-d', $endtime)));?>
                            <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
    <form action="" method="post" class="form-horizontal form">
        <div class="panel panel-default">
            <div class="table-responsive panel-body">
                <table class="table table-hover">
                    <thead class="navbar-inner">
                    <tr>
                        <th style="width:20%;">(ID)申请时间</th>
                        <th style="width:10%;">提现金额</th>
                        <th style="width:10%;">手续费</th>
                        <th style="width:10%;">到帐金额</th>
                        <th style="width:15%;">用户信息</th>
                        <th style="width:12%;">提现类型</th>
                        <th style="width:13%;">状态</th>
                        <th style="width:10%;">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php  if(is_array($list)) { foreach($list as $item) { ?>
                    <tr>
                        <td>
                            (<?php  echo $item['id'];?>)<?php  echo date("Y-m-d H:i:s", $item['dateline'])?>
                            <?php  if($item['type']== 1 && $item['status'] == 1) { ?>
                            <br/>
                            <span class="label label-success">商户单号:<?php  echo $item['trade_no'];?></span>
                            <br/>
                            <span class="label label-success">微信单号:<?php  echo $item['payment_no'];?></span>
                            <?php  } ?>
                        </td>
                        <td>
                            <?php  echo $item['price'];?>元
                        </td>
                        <td>
                            <?php  echo $item['charges'];?>元
                        </td>
                        <td>
                            <?php  echo $item['successprice'];?>元
                        </td>
                        <td>
                            <img src="<?php  echo tomedia($item['headimgurl']);?>" style="width:30px;height:30px;padding1px;border:1px solid #ccc"/>
                            </br>昵称:<?php  echo $item['nickname'];?>
                            </br>姓名:<?php  echo $item['username'];?>
                        </td>
                        <td>
                            <?php  if($item['type'] == 1) { ?>
                            微信支付
                            <?php  } else if($item['type'] == 2) { ?>
                            会员余额
                            <?php  } else if($item['type'] == 3) { ?>
                            现金提现
                            <?php  } ?>
                        </td>
                        <td>
                            <?php  if($item['status'] == 1) { ?>
                            <span class="label label-success">已提现</span>
                            <?php  } else if($item['status'] == -1) { ?>
                            <span class="label label-danger">提现失败</span>
                            <?php  } else { ?>
                            <span class="label label-danger">待审核</span>
                            <?php  } ?>
                            <?php  if(!empty($item['remark'])) { ?>
                            <br/>
                            <?php  echo $item['remark'];?>
                            <?php  } ?>
                        </td>
                        <td>
                            <?php  if($item['status'] == 0) { ?>
                            <a class="btn btn-success btn-sm" href="<?php  echo $this->createWebUrl('cashlog', array('id' => $item['id'], 'op' => 'setstatus', 'logtype' => $logtype))?>" title="审核" onclick="return confirm('确认操作吗？');return false;"><i
                                    class="fa fa-cog"></i> 审核</a>
                            <?php  } ?>
                        </td>
                    </tr>
                    <?php  } } ?>
                    </tbody>
                </table>
                <?php  echo $pager;?>
            </div>
        </div>
    </form>
</div>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/footer', TEMPLATE_INCLUDEPATH)) : (include template('public/footer', TEMPLATE_INCLUDEPATH));?>
