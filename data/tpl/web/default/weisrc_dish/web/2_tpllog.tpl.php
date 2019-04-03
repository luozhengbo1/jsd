<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/header', TEMPLATE_INCLUDEPATH)) : (include template('public/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/comhead', TEMPLATE_INCLUDEPATH)) : (include template('public/comhead', TEMPLATE_INCLUDEPATH));?>
<?php  if(!empty($storeid)) { ?>
<?php  echo $this -> set_tabbar($action, $storeid);?>
<?php  } else { ?>
<ul class="nav nav-tabs">
    <li><a href="<?php  echo $this->createWebUrl('allorder', array('op' => 'display'))?>">订单管理</a></li>
    <li class="active"><a href="#">模版消息日志</a></li>
</ul>
<?php  } ?>
<?php  if($operation == 'display') { ?>
<div class="main">
    <form action="" method="post" class="form-horizontal form" >
        <div class="panel panel-default">
            <div class="table-responsive panel-body">
                <table class="table table-hover">
                    <thead class="navbar-inner">
                    <tr>
                        <th style="width:8%;">编号</th>
                        <th style="width:14%;">通知类型</th>
                        <th style="width:15%;">订单号</th>
                        <th style="width:23%;">接收用户</th>
                        <th style="width:15%;">时间</th>
                        <th style="width:15%;">状态</th>
                        <th style="width:10%;">操作</th>
                    </tr>
                    </thead>
                    <tbody id="level-list">
                    <?php  if(is_array($list)) { foreach($list as $item) { ?>
                    <tr>
                        <td style="white-space:normal;"><?php  echo $item['id'];?></td>
                        <td><?php  echo $item['content'];?></td>
                        <td><?php  echo $item['ordersn'];?><br/>
                            <a href="<?php  echo $this->createWebUrl('allorder', array('op' => 'detail', 'id' => $item['orderid'], 'storeid' => $item['storeid']))?>">查看订单</a>
                        </td>
                        <td>
                            <?php  if(empty($fans[$item['from_user']]['nickname'])) { ?>
                            <?php  echo $item['from_user'];?>
                            <?php  } else { ?>
                            <?php  echo $fans[$item['from_user']]['nickname'];?>
                            <?php  } ?>
                        </td>
                        <td>
                            <?php  echo date("Y-m-d", $item['dateline'])?><br/>
                            <?php  echo date("H:i:s", $item['dateline'])?>
                        </td>
                        <td>
                            <?php  echo $item['result'];?>
                        </td>
                        <td>

                        </td>
                    </tr>
                    <?php  } } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
    <?php  echo $pager;?>
</div>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/footer', TEMPLATE_INCLUDEPATH)) : (include template('public/footer', TEMPLATE_INCLUDEPATH));?>
