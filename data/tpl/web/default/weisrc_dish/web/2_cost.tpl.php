<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/header', TEMPLATE_INCLUDEPATH)) : (include template('public/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/comhead', TEMPLATE_INCLUDEPATH)) : (include template('public/comhead', TEMPLATE_INCLUDEPATH));?>
<style type="text/css">
    .money_off{
        height:20%;
        width:20%;
        display: inline-block;
    }
</style>
<ul class="nav nav-tabs">
    <li <?php  if($operation == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('cost', array('op' => 'display'))?>">平台运营费充值
</a></li>
<li <?php  if($operation == 'post') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('cost', array('op' => 'post'))?>">平台运营费使用记录</a></li>
  <li <?php  if($operation == 'email') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('cost', array('op' => 'email'))?>">邮件提醒</a></li>
</ul>
<?php  if($operation == 'display') { ?>
<div class="main">
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
        <input type="hidden" name="parentid" value="<?php  echo $parent['id'];?>" />
        <div class="panel panel-default">
            <div class="panel-heading">
                平台运营费编辑
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">运营费充值</label>
                    <div class="col-sm-9">
                        <input type="text" name="total_price" class="form-control" value="<?php  echo $item['total_price'];?>" />
                    </div>
                </div>
        <div class="form-group col-sm-12">
            <input type="submit" name="submit" value="保存设置" class="btn btn-primary col-lg-3" />
            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
        </div>
    </form>
</div>
<?php  } else if($operation == 'post') { ?>
<div class="main">
    <div class="panel panel-default">
        <form action="" method="post" class="form-horizontal form" >
            <input type="hidden" name="storeid" value="<?php  echo $storeid;?>" />
            <div class="table-responsive panel-body">
                <table class="table table-hover">
                    <thead class="navbar-inner">
                    <tr>
                        <th style="width:100px;">ID</th>
                        <th style="width:15%;">订单编号</th>
                        <th style="width:15%;">运营费用途</th>
                        <th style="width:10%;">运营费金额</th>
                        <th style="width:10%;">时间</th>
                        <!-- <th style="text-align:right;">操作</th> -->
                    </tr>
                    </thead>
                    <tbody id="level-list">
                    <?php  if(is_array($list)) { foreach($list as $row) { ?>
                    <tr>
                        <td><div class="type-parent"><?php  echo $row['id'];?></div></td>
                        <td><div class="type-parent"><?php  echo $row['orderid'];?></div></td>
                        <td><span class="type-parent"><?php  echo $row['title'];?></span></td>
                        <td><span class="type-parent"><?php  echo $row['money'];?>元</span></td>
                        <td><span class="type-parent"><?php  echo date("Y-m-d H:i:s",$row['time'])?></span></td>
                        <!-- <td style="text-align:right;"><a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('cost', array('op' => 'post', 'id' => $row['id']))?>" title="编辑">改</a>&nbsp;&nbsp;<a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('cost', array('op' => 'delete', 'id' => $row['id']))?>" onclick="return confirm('确认删除此分类吗？');return false;" title="删除">删</a></td> -->
                    </tr>
                    <?php  } } ?>
                    <!-- <tr>
                        <td colspan="5">
                            <input name="submit" type="submit" class="btn btn-primary" value="批量排序">
                            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
                        </td>
                    </tr> -->
                    </tbody>
                </table>
            </div>
        </form>
    </div>
    <?php  echo $pager;?>
</div>
<?php  } else if($operation == 'email') { ?>
<div class="main">
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
        <input type="hidden" name="parentid" value="<?php  echo $parent['id'];?>" />
        <div class="panel panel-default">
            <div class="panel-heading">
                平台运营费邮件提醒
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">邮箱服务器地址</label>
                    <div class="col-sm-9">
                        <input type="text" name="mailhost" class="form-control" value="<?php  echo $item['mailhost'];?>" placeholder="请输入域名邮箱的服务器地址" autocomplete="off" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">远程服务器端口号</label>
                    <div class="col-sm-9">
                        <input type="text" name="mailport" class="form-control" value="<?php  echo $item['mailport'];?>" placeholder="以前默认25,现在新的不可用,可选465或587" autocomplete="off" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">发件人邮箱</label>
                    <div class="col-sm-9">
                        <input type="text" name="mailsend" class="form-control" value="<?php  echo $item['mailsend'];?>" placeholder="请输入发件人邮箱" autocomplete="off" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">发件人姓名(昵称)</label>
                    <div class="col-sm-9">
                        <input type="text" name="mailformname" class="form-control" value="<?php  echo $item['mailformname'];?>" placeholder="显示在收件人邮件的发件人邮箱地址前的发件人姓名" autocomplete="off" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">smtp登录账号</label>
                    <div class="col-sm-9">
                        <input type="text" name="mailusername" class="form-control" value="<?php  echo $item['mailusername'];?>" placeholder="smtp登录的账号" autocomplete="off" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">smtp登录的密码</label>
                    <div class="col-sm-9">
                        <input type="text" name="mailpassword" class="form-control" value="<?php  echo $item['mailpassword'];?>" placeholder="smtp登录的密码" autocomplete="off" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">接收邮件的邮箱</label>
                    <div class="col-sm-9">
                        <input type="text" name="mailhostname" class="form-control" value="<?php  echo $item['mailhostname'];?>" placeholder="接收邮件的邮箱(多个邮箱用英文(,)逗号隔开)" autocomplete="off" />
                    </div>
                </div>
                
        <div class="form-group col-sm-12">
            <input type="submit" name="submit" value="保存设置" class="btn btn-primary col-lg-3" />
            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
        </div>
    </form>
</div>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/footer', TEMPLATE_INCLUDEPATH)) : (include template('public/footer', TEMPLATE_INCLUDEPATH));?>
