<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/header', TEMPLATE_INCLUDEPATH)) : (include template('public/header', TEMPLATE_INCLUDEPATH));?>
<?php  if(!empty($storeid)) { ?>
<?php  echo $this -> set_tabbar($action, $storeid);?>
<?php  } else { ?>
<ul class="nav nav-tabs">
    <li><a href="<?php  echo $this->createWebUrl('allfans', array('op' => 'display'))?>">会员管理</a></li>
    <li><a href="<?php  echo $this->createWebUrl('allfans', array('op' => 'record'))?>">会员交易统计</a></li>
    <li class="active"><a href="<?php  echo $this->createWebUrl('commission', array())?>">推广佣金管理</a></li>
    <li><a href="<?php  echo $this->createWebUrl('cashlog', array('logtype' => 1))?>">佣金提现管理</a></li>
</ul>
<?php  } ?>
<?php  if($operation == 'display') { ?>
<div class="alert alert-info">
    <i class="fa fa-exclamation-circle"></i> 当没有开启自动结算时，可以手动操作结算，删除操作不可恢复，请谨慎操作。
</div>
<div class="main">
    <div class="panel panel-default" style="margin-top: 15px;">
        <div class="panel-heading">筛选</div>
        <div class="table-responsive panel-body">
            <form action="./index.php" method="get" class="form-horizontal" role="form">
                <input type="hidden" name="c" value="site" />
                <input type="hidden" name="a" value="entry" />
                <input type="hidden" name="m" value="weisrc_dish" />
                <input type="hidden" name="do" value="commission" />
                <input type="hidden" name="op" value="display" />
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-1 control-label">状态</label>
                    <div class="col-sm-9 col-xs-9 col-md-9">
                        <div class="btn-group">
                            <a href="<?php  echo $this->createWebUrl('commission', array('op' => 'display'))?>" class="btn <?php  if(!isset($_GPC['status'])) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>" data-original-title="" title="">不限</a>
                            <a href="<?php  echo $this->createWebUrl('commission', array('op' => 'display', 'status' => 0))?>" class="btn <?php  if(isset($_GPC['status'])&&$_GPC['status']==0) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>" data-original-title="" title="">未结算</a>
                            <a href="<?php  echo $this->createWebUrl('commission', array('op' => 'display', 'status' => 1))?>" class="btn <?php  if(isset($_GPC['status'])&&$_GPC['status']==1) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>" data-original-title="" title="">已结算</a>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-1 control-label">单号</label>
                    <div class="col-sm-9 col-xs-9 col-md-9">
                        <input type="text" class="form-control" placeholder="请输入单号" name="keyword">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-1 control-label"></label>
                    <div class="col-sm-9 col-xs-9 col-md-9">
                        <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <form action="" method="post" class="form-horizontal form" >
        <div class="panel panel-default">
            <div class="table-responsive panel-body">
                <table class="table table-hover">
                    <thead class="navbar-inner">
                    <tr>
                        <th style="width: 200px;">订单号</th>
                        <th>买家</th>
                        <th>奖励用户</th>
                        <th>佣金级别</th>
                        <th>奖励佣金</th>
                        <th>状态</th>
                        <th>时间</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="level-list">
                    <?php  if(is_array($list)) { foreach($list as $item) { ?>
                    <tr>
                        <td>
                            <a href="<?php  echo $this->createWebUrl('allorder', array('op' => 'detail', 'id' => $item['orderid']))?>">
                            <?php  echo $item['ordersn'];?>
                            </a>
                        </td>
                        <td style="white-space:normal;">
                            <a href="<?php  echo $this->createWebUrl('allfans', array('id' => $item['fansid'], 'op' => 'post'))?>">
                            <img src="<?php  echo tomedia($item['headimgurl']);?>" style="width:30px;height:30px;padding:1px;border:1px solid #ccc"/>
                            </br><?php  echo $item['nickname'];?>
                            </a>
                        </td>
                        <td>
                            <?php  if($item['agentid']<>0) { ?>
                            <a href="<?php  echo $this->createWebUrl('allfans', array('id' => $item['agentid'], 'op' => 'post'))?>">
                            <img src="<?php  echo tomedia($item['agentheadimgurl']);?>" style="width:30px;height:30px;padding:1px;border:1px solid #ccc"/>
                            </br><?php  echo $item['agentnickname'];?>
                            </a>
                            <?php  } else { ?>
                            <span class="label label-primary">平台</span>
                            <?php  } ?>
                        </td>
                        <td>
                            <?php  if($item['level']==1) { ?>
                            <span class="label label-info">一级</span>
                            <?php  } else if($item['level']==2) { ?>
                            <span class="label label-warning">二级</span>
                            <?php  } ?>
                        </td>
                        <td><?php  echo $item['price'];?></td>
                        <td>
                            <?php  if($item['status'] == 0) { ?>
                            <span class="label label-danger">未结算</span>
                            <?php  } else { ?>
                            <span class="label label-success">已结算</span>
                            <?php  } ?>
                        </td>
                        <td style="white-space:normal;">
                            <?php  echo date('Y-m-d <br/> H:i:s', $item['dateline'])?>
                        </td>
                        <td>
                            <?php  if($item['status'] == 0) { ?>
                            <a class="btn btn-success btn-sm" onclick="return confirm('您确定操作吗？');return false;"  href="<?php  echo $this->createWebUrl('commission', array('id' => $item['id'], 'op' => 'setstatus'))?>"><i class="fa fa-cog"></i>结算</a>
                            <a class="btn btn-danger btn-sm" onclick="return confirm('此操作将不能回复,您确定操作吗？');return false;"  href="<?php  echo $this->createWebUrl('commission', array('id' => $item['id'], 'op' => 'delete'))?>"><i class="fa fa-pencil"></i>删除</a>
                            <?php  } ?>
                        </td>
                    </tr>
                    <?php  } } ?>
                    </tbody>
                </table>
            </div>
            <div class="panel-footer">
                总共 <strong><?php  echo $total;?></strong> 条
            </div>
        </div>
    </form>
    <?php  echo $pager;?>
</div>
<?php  } else if($operation == 'post') { ?>
<div class="main">
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" id="invitative">
        <div class="panel panel-default">
            <div class="panel-heading">
                用户信息
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">微信ID</label>
                    <div class="col-sm-9">
                        <p class="form-control-static">
                            <?php  echo $item['from_user'];?>
                        </p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">累计下单数量</label>
                    <div class="col-sm-9">
                        <p class="form-control-static">
                            <?php  echo $order_count;?>
                        </p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">取消订单数量</label>
                    <div class="col-sm-9">
                        <p class="form-control-static">
                            <?php  echo $cancel_count;?>
                        </p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">累计消费金额</label>
                    <div class="col-sm-9">
                        <p class="form-control-static">
                            <?php  if(empty($pay_price)) { ?>0<?php  } else { ?><?php  echo $pay_price;?><?php  } ?>
                        </p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">昵称</label>
                    <div class="col-sm-9">
                        <input type="text" name="nickname" value="<?php  echo $item['nickname'];?>" id="nickname" class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">头像</label>
                    <div class="col-sm-9">
                        <?php  if(empty($item['headimgurl'])) { ?>
                        <?php  echo tpl_form_field_image('headimgurl', '../addons/weisrc_dish/template/images/default-headimg.jpg')?>
                        <?php  } else { ?>
                        <?php  echo tpl_form_field_image('headimgurl', $item['headimgurl'])?>
                        <?php  } ?>
                        <div class="help-block">大图片建议尺寸：80像素 * 80像素</div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">用户名</label>
                    <div class="col-sm-9">
                        <input type="text" name="username" value="<?php  echo $item['username'];?>" id="username" class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">手机</label>
                    <div class="col-sm-9">
                        <input type="text" name="mobile" value="<?php  echo $item['mobile'];?>" id="mobile" class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">地址</label>
                    <div class="col-sm-9">
                        <input type="text" name="address" value="<?php  echo $item['address'];?>" id="address" class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">坐标</label>
                    <div class="col-sm-9">
                        <?php  echo tpl_form_field_coordinate('baidumap', $item)?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">最后访问时间</label>
                    <div class="col-sm-9">
                        <p class="form-control-static">
                            <?php  echo date('Y-m-d H:i:s', $item['dateline'])?>
                        </p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label" style="color:#f00;">禁止下单</label>
                    <div class="col-sm-9">
                        <label class="radio-inline">
                            <input type="radio" name="status" value="1" <?php  if($item['status']==1 || empty($item)) { ?>checked<?php  } ?>>正常
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="status" value="0" <?php  if(isset($item['status']) && empty($item['status'])) { ?>checked<?php  } ?>>禁止</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group col-sm-12">
            <input type="submit" name="submit" value="保存设置" class="btn btn-primary col-lg-1"/>
            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
        </div>
    </form>
</div>
<script type="text/javascript">
    function check() {
        if($.trim($('#username').val()) == '') {
            message('没有输入姓名.', '', 'error');
            return false;
        }s
        return true;
    }
</script>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/footer', TEMPLATE_INCLUDEPATH)) : (include template('public/footer', TEMPLATE_INCLUDEPATH));?>
