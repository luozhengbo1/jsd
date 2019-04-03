<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/header', TEMPLATE_INCLUDEPATH)) : (include template('public/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/comhead', TEMPLATE_INCLUDEPATH)) : (include template('public/comhead', TEMPLATE_INCLUDEPATH));?>
<?php  if($operation == 'display') { ?>
<div class="main">
    <form action="" method="post" class="form-horizontal form">
        <div class="panel panel-default">
            <div class="panel-heading">
                评论管理
            </div>
            <div class="table-responsive panel-body">
                <table class="table table-hover">
                    <thead class="navbar-inner">
                    <tr>
                        <th style="width:8%;">(ID)</th>
                        <th style="width:10%;">用户</th>
                        <th style="width:25%;">评论详情</th>
                        <th style="width:25%;">评论时间</th>
                        <th style="width:10%;">状态</th>
                        <th style="width:15%;"></th>
                    </tr>
                    </thead>
                    <tbody id="level-list">
                    <?php  if(is_array($list)) { foreach($list as $item) { ?>
                    <tr>
                        <td>

                            <?php  echo $item['id'];?>
                        </td>
                        <td>
                            <img src="<?php  echo tomedia($item['headimgurl']);?>" width="50" style="border-radius: 3px;"/>
                            <br/>
                            <?php  echo $item['nickname'];?>
                        </td>
                        <td>
                            评分:<?php  echo $item['star'];?>星<br/>
                            内容:<?php  echo $item['content'];?><br/>
                            回复:<?php  echo $item['replycontent'];?>
                        </td>
                        <td style="white-space:normal;"><?php  echo date('Y-m-d H:i:s', $item['dateline'])?></td>
                        <td><?php  if($item['status'] == 0) { ?><span class="label label-default">未显示</span><?php  } else { ?><span
                                class="label label-success">显示</span><?php  } ?></td>
                        <td>
                            <a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('feedback', array('id' => $item['id'], 'op' => 'post', 'storeid' => $storeid))?>" title="编辑">回复</a>
                            <a class="btn btn-default btn-sm" onclick="return confirm('确认删除吗？');return false;"
                               href="<?php  echo $this->createWebUrl('feedback', array('op' => 'delete', 'id' => $item['id'], 'storeid' => $storeid))?>">删除</i></a>
                            <a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('order', array('op' => 'detail', 'id' => $item['orderid'], 'storeid' => $storeid))?>">订单查看</a>
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
<?php  } else if($operation == 'post') { ?>
<div class="main">
    <div class="panel panel-default">
        <div class="panel-body">
            <a class="btn btn-warning" href="<?php  echo $this->createWebUrl('allfans', array('op' => 'display', 'storeid' => $storeid))?>">返回会员管理
            </a>
        </div>
    </div>
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" id="invitative">
        <div class="panel panel-default">
            <div class="panel-heading">
                用户信息
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">留言内容</label>
                    <div class="col-sm-9">
                        <p class="form-control-static">
                            <textarea style="height:200px;" class="form-control richtext" name="content" cols="70"><?php  echo $item['content'];?></textarea>
                        </p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">回复内容</label>
                    <div class="col-sm-9">
                        <textarea style="height:200px;" class="form-control richtext" name="replycontent" cols="70"><?php  echo $item['replycontent'];?></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label" style="color:#f00;">状态</label>
                    <div class="col-sm-9">
                        <label class="radio-inline">
                            <input type="radio" name="status" value="1" <?php  if($item['status']==1 || empty($item)) { ?>checked<?php  } ?>>正常
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="status" value="0" <?php  if(isset($item['status']) &&empty($item['status'])) { ?>checked<?php  } ?>>关闭</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group col-sm-12">
            <input type="submit" name="submit" value="保存设置" class="btn btn-primary col-lg-3"/>
            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
        </div>
    </form>
</div>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/footer', TEMPLATE_INCLUDEPATH)) : (include template('public/footer', TEMPLATE_INCLUDEPATH));?>