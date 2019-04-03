<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/header', TEMPLATE_INCLUDEPATH)) : (include template('public/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/comhead', TEMPLATE_INCLUDEPATH)) : (include template('public/comhead', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
    <li <?php  if($operation == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('type', array('op' => 'display'))?>">门店类型管理
</a></li>
    <li <?php  if($operation == 'post') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('type', array('op' => 'post'))?>">添加门店类型</a></li>
</ul>
<?php  if($operation == 'post') { ?>
<div class="main">
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
        <input type="hidden" name="parentid" value="<?php  echo $parent['id'];?>" />
        <div class="panel panel-default">
            <div class="panel-heading">
                门店类型编辑
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
                        <input type="text" name="displayorder" class="form-control" value="<?php  echo $type['displayorder'];?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">类型名称</label>
                    <div class="col-sm-9">
                        <input type="text" name="catename" class="form-control" value="<?php  echo $type['name'];?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">类型图标</label>
                    <div class="col-sm-9">
                        <?php  echo tpl_form_field_image('thumb', $type['thumb'])?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">链接</label>
                    <div class="col-sm-9">
                        <input type="text" name="url" class="form-control" value="<?php  echo $type['url'];?>" />
                    </div>
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
                        <th style="width:10%;">图标</th>
                        <th style="width:10%;">名称</th>
                        <th style="width:60%;">网址</th>
                        <th style="text-align:right;">操作</th>
                    </tr>
                    </thead>
                    <tbody id="level-list">
                    <?php  if(is_array($list)) { foreach($list as $row) { ?>
                    <tr>
                        <td><input type="text" class="form-control" name="displayorder[<?php  echo $row['id'];?>]" value="<?php  echo $row['displayorder'];?>"></td>
                        <td>
                            <img src="<?php  echo tomedia($row['thumb']);?>" onerror="this.src='../addons/weisrc_dish/template/images/shop_logo.png';" width="60px;" style="border-radius: 3px;">
                        </td>
                        <td><div class="type-parent"><?php  echo $row['name'];?>&nbsp;&nbsp;</div></td>
                        <td>
                            <?php  if(empty($row['url'])) { ?>
                            <input type="text" class="form-control" name="url[<?php  echo $row['id'];?>]" value="<?php  echo $_W['siteroot'];?>app/index.php?i=<?php  echo $_W['uniacid'];?>&c=entry&typeid=<?php  echo $row['id'];?>&do=waprestlist&m=weisrc_dish">
                            <?php  } else { ?>
                            <input type="text" class="form-control" name="url[<?php  echo $row['id'];?>]" value="<?php  echo $row['url'];?>">
                            <?php  } ?>
                        </td>
                        <td style="text-align:right;"><a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('type', array('op' => 'post', 'id' => $row['id']))?>" title="编辑">改</a>&nbsp;&nbsp;<a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('type', array('op' => 'delete', 'id' => $row['id']))?>" onclick="return confirm('确认删除此分类吗？');return false;" title="删除">删</a></td>
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