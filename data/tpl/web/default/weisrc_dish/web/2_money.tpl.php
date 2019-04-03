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
    <li <?php  if($operation == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('money', array('op' => 'display'))?>">会员积分管理
</a></li>
    <li <?php  if($operation == 'post') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('money', array('op' => 'post'))?>">会员积分充值</a></li>
    
      
</ul>
<?php  if($operation == 'post') { ?>
<div class="main">
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
        <input type="hidden" name="parentid" value="<?php  echo $parent['id'];?>" />
        <div class="panel panel-default">
            <div class="panel-heading">
                会员积分编辑
            </div>
            <div class="panel-body">
                <?php  if(!empty($parentid)) { ?>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">上级分类</label>
                    <div class="col-sm-9">
                        <?php  echo $parent['name'];?>
                    </div>
                </div>
                <?php  } ?>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
                    <div class="col-sm-9">
                        <input type="text" name="sort" class="form-control" value="<?php  echo $type['sort'];?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">昵称</label>
                    <div class="col-sm-9">
                        <input type="text" name="nickname" class="form-control" value="<?php  echo $type['nickname'];?>" />
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">下单金额送积分：</label>
                    <div class="col-sm-9">
                        满<input type="text" name="limit" class="form-control money_off" value="<?php  echo $type['limit'];?>" />元送
                        <input type="text" name="limit_jifen" class="form-control money_off" value="<?php  echo $type['limit_jifen'];?>" />积分
                    </div>
                </div>
                 <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">购买商品送积分</label>
                    <div class="col-sm-9">
                        <input type="text" name="limit_discount" class="form-control" value="<?php  echo $type['limit_discount'];?>" />
                    </div>
                </div>
                 <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">评论积分</label>
                    <div class="col-sm-9">
                        <input type="text" name="limit_coupon" class="form-control" value="<?php  echo $type['limit_coupon'];?>" />
                    </div>
                </div>
                 <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">积分抵现</label>
                    <div class="col-sm-9">
                        <input type="text" name="money_limit" class="form-control money_off" value="<?php  echo $type['money_limit'];?>" />积分抵
                        <input type="text" name="minus" class="form-control money_off" value="<?php  echo $type['minus'];?>" />元
                    </div>
                </div>
        </div>
        <div class="form-group col-sm-12">
            <input type="submit" name="submit" value="保存设置" class="btn btn-primary col-lg-3" />
            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
        </div>
    </form>
</div>
<?php  } else if($operation == 'display') { ?>
<div class="main">
    <div class="panel panel-default">
        <form action="" method="post" class="form-horizontal form" >
            <input type="hidden" name="storeid" value="<?php  echo $storeid;?>" />
            <div class="table-responsive panel-body">
                <table class="table table-hover">
                    <thead class="navbar-inner">
                    <tr>
                        <th style="width:100px;">显示顺序</th>
                        <th style="width:10%;">头像</th>
                        <th style="width:10%;">昵称</th>
                        <th style="width:10%;">下单积分（满元/积分）</th>
                        <th style="width:10%;">购买积分</th>
                        <th style="width:10%;">评论积分</th>
                        <th style="width:10%;">积分抵现（积分/元）</th>
                        <th style="text-align:right;">操作</th>
                    </tr>
                    </thead>
                    <tbody id="level-list">
                    <?php  if(is_array($list)) { foreach($list as $row) { ?>
                    <tr>
                        <td><input type="text" class="form-control" name="displayorder[<?php  echo $row['id'];?>]" value="<?php  echo $row['sort'];?>"></td>
                        <td>
                            <img src="<?php  echo tomedia($row['headimgurl']);?>">
                        </td>
                        <td><div class="type-parent"><?php  echo $row['nickname'];?>&nbsp;&nbsp;</div></td>
                        <td><span class="type-parent"><?php  echo $row['limit'];?>元/<?php  echo $row['limit_jifen'];?>积分</span></td>
                        <td><span class="type-parent"><?php  echo $row['limit_discount'];?>积分</span></td>
                        <td><span class="type-parent"><?php  echo $row['limit_coupon'];?>积分</span></td>
                       <td><span class="type-parent"><?php  echo $row['money_limit'];?>积分/<?php  echo $row['minus'];?>元</span></td>
                        <td style="text-align:right;"><a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('money', array('op' => 'post', 'id' => $row['id']))?>" title="编辑">改</a>&nbsp;&nbsp;<a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('money', array('op' => 'delete', 'id' => $row['id']))?>" onclick="return confirm('确认删除此分类吗？');return false;" title="删除">删</a></td>
                    </tr>
                    <?php  } } ?>
                    <tr>
                        <td colspan="5">
                            <input name="submit" type="submit" class="btn btn-primary" value="批量排序">
                            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
    <?php  echo $pager;?>
</div>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/footer', TEMPLATE_INCLUDEPATH)) : (include template('public/footer', TEMPLATE_INCLUDEPATH));?>