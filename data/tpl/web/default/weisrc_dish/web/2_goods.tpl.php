<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/header', TEMPLATE_INCLUDEPATH)) : (include template('public/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/comhead', TEMPLATE_INCLUDEPATH)) : (include template('public/comhead', TEMPLATE_INCLUDEPATH));?>

<?php  if($operation == 'display') { ?>
<div class="main" style="margin-top:0px;">
    <ul class="nav nav-tabs" role="tablist">
        <li <?php  if($deleted == 0) { ?>class="active"<?php  } ?>>
        <a href="<?php  echo $this->createWebUrl('goods', array('op' => 'display', 'storeid' => $storeid))?>">商品管理</a>
        </li>
        <li <?php  if($deleted == 1) { ?>class="active"<?php  } ?>>
        <a href="<?php  echo $this->createWebUrl('goods', array('op' => 'display', 'storeid' => $storeid, 'deleted' => 1))?>">商品回收站</a></li>
        <li>
            <a href="<?php  echo $this->createWebUrl('category', array('op' => 'display', 'storeid' => $storeid))?>">商品分类</a>
        </li>
    </ul>
    <div class="panel panel-default" id="uploaddata" style="display: none;">
        <div class="panel-body">
            <form action="" method="post" class="navbar-form navbar-left" enctype="multipart/form-data">
                <input type="hidden" name="leadExcel" value="true">
                <input type="hidden" name="c" value="site" />
                <input type="hidden" name="a" value="entry" />
                <input type="hidden" name="m" value="weisrc_dish" />
                <input type="hidden" name="do" value="UploadExcel" />
                <input type="hidden" name="ac" value="goods" />
                <input type="hidden" name="storeid" value="<?php  echo $storeid;?>" />
                <span class="input-group">
                    <input name="viewfile" id="viewfile" type="text" value="" class="form-control" style="width:300px;" readonly>
			        <span class="input-group-btn">
                        <a class="btn btn-default ms_br">
                            <label for="unload" class="ms_mp" >选择...</label>
                        </a>
                        <input type="submit" style="border-radius: 0px;" class="btn btn-success" value="上传"
                               onclick="if(document.getElementById('viewfile').value==''){alert('请先选择上传文件!');return false;}">
                        <a class="btn btn-primary ms_mb" href="<?php  echo $_W['siteroot'];?>addons/weisrc_dish/example/example_goods4.xls">下载导入模板
                        </a>
                    </span>
                    <input type="file" name="inputExcel" id="unload" class="form-control" style="display: none;"
                           onchange="document.getElementById('viewfile').value=this.value;this.style.display='none';">
                </span>
            </form>
            <style>
                .ms_br {
                    border-radius: 0px;border-left-width: 0px;
                }
                .ms_mp {
                    margin: 0px;padding:0px;
                }
                .ms_mb {
                    border-top-left-radius:0px;border-bottom-left-radius:0px;
                }
            </style>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <a class="btn btn-success" href="<?php  echo $this->createWebUrl('goods', array('op' => 'post', 'storeid' => $storeid))?>">添加商品</a>
            <a class="btn btn-success" href="#" onclick="$('#uploaddata').slideToggle();">批量导入</a>
            <a class="btn btn-primary"
               href="<?php  echo $this->createWebUrl('goods', array('op' => 'opengoods', 'storeid' => $storeid))?>" >
                一键上架</a>
            <a class="btn btn-danger" href="<?php  echo $this->createWebUrl('goods', array('op' => 'closegoods', 'storeid' => $storeid))?>" >
                一键下架</a>
        </div>
        <div class="panel-body">
            <form action="" method="get" class="form-horizontal" role="form">
                <input type="hidden" name="c" value="site" />
                <input type="hidden" name="a" value="entry" />
                <input type="hidden" name="m" value="weisrc_dish" />
                <input type="hidden" name="do" value="goods" />
                <input type="hidden" name="storeid" value="<?php  echo $storeid;?>" />
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width: 100px;">关键字</label>
                    <div class="col-sm-2 col-lg-2">
                        <input class="form-control" name="keyword" id="" type="text" value="<?php  echo $_GPC['keyword'];?>">
                    </div>
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label" style="width: 100px;">商品分类</label>
                    <div class="col-sm-2 col-lg-2">
                        <select style="margin-right:15px;" name="category_id" class="form-control">
                            <option value="0">请选择商品分类</option>
                            <?php  if(is_array($category)) { foreach($category as $row) { ?>
                            <option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $_GPC['category_id']) { ?> selected="selected"<?php  } ?>><?php  echo $row['name'];?></option>
                            <?php  } } ?>
                        </select>
                    </div>
                    <div class="col-sm-2 col-lg-2">
                        <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="table-responsive panel-body">
            <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
                <input type="hidden" name="storeid" value="<?php  echo $storeid;?>" />
                <table class="table table-hover">
                    <thead class="navbar-inner">
                    <tr>
                        <?php  if($deleted != 1) { ?>
                        <th class='with-checkbox' style="width:2%;"><input type="checkbox" class="check_all" /></th>
                        <th style="width:8%;">顺序</th>
                        <?php  } ?>
                        <th style="width:6%;">编号</th>
                        <th style="width:12%">商品名称</th>
                        <th style="width:10%;">分类</th>
                        <th style="width:10%;">价格</th>
                        <?php  if($deleted != 1) { ?>
                        <th style="width:8%;">打包费</th>
                        <th style="width:8%;">赠送积分</th>
                        <th style="width:8%;">每日库存</th>
                        <th style="width:8%;">剩余库存</th>
                        <th style="width:7%;">总销量</th>
                        <th style="width:7%;">送货方式</th>
                        <?php  } ?>
                        <th style="width:10%;">是否上架</th>
                        <th style="width:10%;text-align:right;">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php  if(is_array($list)) { foreach($list as $item) { ?>
                    <tr>
                        <?php  if($deleted != 1) { ?>
                        <td class="with-checkbox"><input type="checkbox" name="check" value="<?php  echo $item['id'];?>"></td>
                        <td><input type="text" class="form-control" name="displayorder[<?php  echo $item['id'];?>]" value="<?php  echo $item['displayorder'];?>"></td>
                        <?php  } ?>
                        <td><?php  echo $item['id'];?></td>
                        <td>
                            <img src="<?php  echo tomedia($item['thumb']);?>" width="50"  style="border-radius: 3px;" /><br/>
                            <?php  echo $item['title'];?>
                        </td>
                        <!--onerror="this.src='./resource/images/nopic.jpg';"-->
                        <td>
                            <?php  if(!empty($category[$item['pcate']])) { ?><?php  echo $category[$item['pcate']]['name'];?><?php  } ?>
                            <?php  if(!empty($label[$item['labelid']])) { ?><br/><span class="label label-info">标签:<?php  echo $label[$item['labelid']]['title'];?></span><?php  } ?>
                        </td>
                        <td>
                            <?php  echo $item['marketprice'];?>元
                            <?php  if(!empty($item['memberprice'])) { ?>
                            <br/>
                            <span class="label label-info">会员:<?php  echo $item['memberprice'];?>元</span>
                            <?php  } ?>
                        </td>
                        <?php  if($deleted != 1) { ?>
                        <td>
                            <?php  echo $item['packvalue'];?>
                        </td>
                        <td>
                            <?php  echo $item['credit'];?>
                        </td>
                        <td>
                            <?php  if($item['counts']==-1) { ?>
                            <span class="label label-default">未启用</span>
                            <?php  } else { ?>
                            <?php  echo $item['counts'];?>
                            <?php  } ?>
                        </td>
                        <td>
                            <?php  if($item['counts']==-1) { ?>
                            <span class="label label-default">未启用</span>
                            <?php  } else { ?>
                            <?php  echo $item['counts']-$item['today_counts']?>
                            <?php  } ?>
                        </td>
                        <td>
                            <?php  echo $item['sales'];?>
                        </td>
                        <td>
                         <?php  if($item['send_way']==0) { ?>
                         <span>外卖和邮寄</span>
                         <?php  } else if($item['send_way']==1) { ?>
                         <span>外卖</span>
                         <?php  } else if($item['send_way']==2) { ?>
                         <span>邮寄</span>
                         <?php  } ?>
                        </td>
                        <?php  } ?>
                        <td><?php  if($item['status']) { ?><span class="label label-success">已上架</span><?php  } else { ?><span class="label label-danger">已下架</span><?php  } ?></td>
                        <td style="text-align:right;">
                            <a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('goods', array('id' => $item['id'], 'op' => 'post', 'storeid' => $storeid))?>" title="编辑">改</a>
                            <?php  if($deleted == 0) { ?>
                            <a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('goods', array('id' => $item['id'], 'op' => 'delete', 'storeid' => $storeid))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;" title="删除">删</a>
                            <?php  } ?>
                            <?php  if($item['deleted']==1 && $deleted == 1) { ?>
                            <a class="btn btn-default btn-sm" onclick="return confirm('确认删除吗？');return false;"
                               href="<?php  echo $this->createWebUrl('goods', array('id' => $item['id'], 'storeid' =>  $storeid, 'op' => 'deletetrue'))?>" title="删除">删除</a>
                            <a class="btn btn-warning btn-sm" href="<?php  echo $this->createWebUrl('goods', array('id' =>  $item['id'], 'op' => 'restore', 'storeid' => $storeid))?>" data-toggle="tooltip" title="还原" data-content="还原" >还原</a>
                            <?php  } ?>
                        </td>
                    </tr>
                    <?php  } } ?>
                    </tbody>
                    <?php  if($deleted != 1) { ?>
                    <tr>
                        <td colspan="10">
                            <input name="token" type="hidden" value="<?php  echo $_W['token'];?>" />
                            <input type="submit" class="btn btn-warning" name="submit" value="批量排序" />
                            <input type="button" class="btn btn-warning" name="btndeleteall" value="批量删除" />
                            <input type="button" class="btn btn-warning" name="btnupall" value="批量上架" />
                            <input type="button" class="btn btn-warning" name="btndownall" value="批量下架" />
                        </td>
                    </tr>
                    <?php  } ?>
                </table>
                <?php  echo $pager;?>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    <!--
    var category = <?php  echo json_encode($children)?>;
    //-->
    $(function(){
        $(".check_all").click(function(){
            var checked = $(this).get(0).checked;
            $("input[type=checkbox]").attr("checked",checked);
        });

        $("input[name=btndeleteall]").click(function(){
            var check = $("input[type=checkbox][class!=check_all]:checked");
            if(check.length < 1){
                alert('请选择要删除的商品!');
                return false;
            }
            if(confirm("确认要删除选择的商品?")){
                var id = new Array();
                check.each(function(i){
                    id[i] = $(this).val();
                });
                var url = "<?php  echo $this->createWebUrl('goods', array('op' => 'deleteall', 'storeid' => $storeid))?>";
                $.post(
                        url,
                        {idArr:id},
                        function(data){
                            alert(data.error);
                            location.reload();
                        },'json'
                );
            }
        });

        $("input[name=btnupall]").click(function(){
            var check = $("input[type=checkbox][class!=check_all]:checked");
            if(check.length < 1){
                alert('请选择要上架的商品!');
                return false;
            }
            if(confirm("确认要上架选择的商品?")){
                var id = new Array();
                check.each(function(i){
                    id[i] = $(this).val();
                });
                var url = "<?php  echo $this->createWebUrl('goods', array('op' => 'upall', 'storeid' => $storeid))?>";
                $.post(
                        url,
                        {idArr:id},
                        function(data){
                            alert(data.error);
                            location.reload();
                        },'json'
                );
            }
        });

        $("input[name=btndownall]").click(function(){
            var check = $("input[type=checkbox][class!=check_all]:checked");
            if(check.length < 1){
                alert('请选择要下架的商品!');
                return false;
            }
            if(confirm("确认要下架选择的商品?")){
                var id = new Array();
                check.each(function(i){
                    id[i] = $(this).val();
                });
                console.log(id)
                var url = "<?php  echo $this->createWebUrl('goods', array('op' => 'downall', 'storeid' => $storeid))?>";
                $.post(
                        url,
                        {idArr:id},
                        function(data){
                            alert(data.error);
                            location.reload();
                        },'json'
                );
            }
        });
    });
</script>
<?php  } else if($operation == 'post') { ?>
<?php  include $this->template('web/goods_detail');?>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/footer', TEMPLATE_INCLUDEPATH)) : (include template('public/footer', TEMPLATE_INCLUDEPATH));?>