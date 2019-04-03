<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/header', TEMPLATE_INCLUDEPATH)) : (include template('public/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/comhead', TEMPLATE_INCLUDEPATH)) : (include template('public/comhead', TEMPLATE_INCLUDEPATH));?>

<?php  if($operation == 'display') { ?>
<div class="main">

    <input type="hidden" name="storeid" value="<?php  echo $storeid;?>" />
    <form action="" method="post" class="form-horizontal form">
        <div class="panel panel-default">
            <div class="panel-heading"><a class="btn btn-primary" href="javascript:location.reload()"><i class="fa fa-refresh"></i> 刷新</a>
                <a class="btn btn-danger" href="<?php  echo $this->createWebUrl('servicelog', array('op' => 'checkall', 'storeid' => $storeid))?>" onclick="return confirm('此操作不可恢复，确认操作吗？');return false;">确认所有未读通知</a></div>
            <div class="table-responsive panel-body">
                <table class="table table-hover">
                    <thead class="navbar-inner">
                    <tr>
                        <th style="width:10%;">ID</th>
                        <th style="width:25%;">信息内容</th>
                        <th style="width:20%;">创建时间</th>
                        <th style="width:10%;">状态</th>
                        <th style="width:10%; text-align:right;">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php  if(is_array($list)) { foreach($list as $item) { ?>
                    <tr>
                        <td><?php  echo $item['id'];?></td>
                        <td>
                            <?php  echo $item['content'];?>
                            <?php  if($item['orderid']) { ?>
                            <a href="<?php  echo $this->createWebUrl('order', array('op' => 'detail', 'id' => $item['orderid'], 'storeid' => $storeid))?>" title="查看订单">(查看订单)</a>
                            <?php  } ?>
                        </td>
                        <!--<td>-->
                            <!--<a href="<?php  echo $this->createWebUrl('order', array('op' => 'detail', 'id' => $item['orderid'], 'storeid' => $storeid))?>" title="查看订单"><?php  echo $item['ordersn'];?></a>-->
                        <!--</td>-->
                        <td>
                            <?php  echo date('Y-m-d H:i', $item['dateline'])?>
                        </td>
                        <td>
                            <?php  if($item['status'] == 1) { ?>
                            <span class="label label-success">已读</span>
                            <?php  } else if($item['status'] == 0) { ?>
                            <span class="label label-danger">未读</span>
                            <?php  } ?>
                        </td>
                        <td style="text-align:right;">
                            <a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('servicelog', array('op' => 'check', 'id' => $item['id'], 'storeid' => $storeid))?>" >已读</a>

                            <a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('servicelog', array('op' => 'delete', 'id' => $item['id'], 'storeid' => $storeid))?>" title="删除" onclick="return confirm('此操作不可恢复，确认删除？');return false;">删</a>
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