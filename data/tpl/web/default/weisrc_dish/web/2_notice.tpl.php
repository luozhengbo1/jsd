<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/header', TEMPLATE_INCLUDEPATH)) : (include template('public/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/comhead', TEMPLATE_INCLUDEPATH)) : (include template('public/comhead', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
    <li <?php  if($operation == 'display') { ?>class="active"<?php  } ?>><a
        href="<?php  echo $this->createWebUrl('notice', array('op' => 'display'))?>">通知管理
</a></li>
    <li <?php  if($operation == 'post') { ?>class="active"<?php  } ?>><a
        href="<?php  echo $this->createWebUrl('notice', array('op' => 'post'))?>">发布通知</a></li>
</ul>
<?php  if($operation == 'post') { ?>
<div class="main">
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
        <input type="hidden" name="parentid" value="<?php  echo $parent['id'];?>" />
        <div class="panel panel-default">
            <div class="panel-heading">
                通知编辑
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
                    <div class="col-sm-9">
                        <input type="text" name="displayorder" class="form-control" value="<?php  echo $type['displayorder'];?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">通知名称</label>
                    <div class="col-sm-9">
                        <input type="text" name="title" class="form-control" value="<?php  echo $type['title'];?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">通知链接</label>
                    <div class="col-sm-9">
                        <input type="text" name="url" class="form-control" value="<?php  echo $type['url'];?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">通知内容</label>
                    <div class="col-sm-9">
                        <textarea style="height:200px;" class="form-control richtext" name="content" cols="70" id="reply-add-text"><?php  echo $type['content'];?></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">状态</label>
                    <div class="col-sm-9">
                        <label for="status1" class="radio-inline"><input type="radio" name="status" value="1" id="status1" <?php  if($type['status']==1) { ?>checked<?php  } ?> /> 开启</label>
                        <label for="status2" class="radio-inline"><input type="radio" name="status" value="0" id="status2"  <?php  if($type['status']==0 || empty($type)) { ?>checked<?php  } ?> /> 关闭</label>
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
<script src="https://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
<script type="text/javascript">
    require(['util', 'clockpicker'], function(u, $){
        u.editor($('.richtext')[0]);
    });
</script>
<?php  } else if($operation == 'display') { ?>
<div class="main">
    <div class="panel panel-default">
        <form action="" method="post" class="form-horizontal form" >
            <input type="hidden" name="storeid" value="<?php  echo $storeid;?>" />
            <div class="table-responsive panel-body">
                <table class="table table-hover">
                    <thead class="navbar-inner">
                    <tr>
                        <th style="width:8%;">显示顺序</th>
                        <th style="width:60%;">名称</th>
                        <th style="width:20%;">状态</th>
                        <th style="text-align:right;">操作</th>
                    </tr>
                    </thead>
                    <tbody id="level-list">
                    <?php  if(is_array($list)) { foreach($list as $row) { ?>
                    <tr>
                        <td><input type="text" class="form-control" name="displayorder[<?php  echo $row['id'];?>]" value="<?php  echo $row['displayorder'];?>"></td>
                        <td><div class="type-parent"><?php  echo $row['title'];?>&nbsp;&nbsp;</div></td>
                        <td><?php  if($row['status']==1) { ?><span class="label label-success">启用</span><?php  } else { ?><span class="label label-danger">关闭</span><?php  } ?></td>
                        <td style="text-align:right;">
                            <a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('notice', array('op' => 'post', 'id' => $row['id']))?>" title="编辑">改</a>&nbsp;&nbsp;
                            <a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('notice', array('op' => 'delete', 'id' => $row['id']))?>" onclick="return confirm('确认删除此分类吗？');return false;" title="删除">删</a>
                        </td>
                    </tr>
                    <?php  } } ?>
                    <tr>
                        <td colspan="4">
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