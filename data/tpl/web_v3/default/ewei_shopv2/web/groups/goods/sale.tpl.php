<?php defined('IN_IA') or exit('Access Denied');?><?php  if($list) { ?>
<div class="page-table-header" style="border-top: none">
    <input type="checkbox">
    <div class="btn-group">
        <?php if(cv('groups.goods.edit')) { ?>
        <?php  if($_GPC['type']=='store') { ?>
        <button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch' data-href="<?php  echo webUrl('groups/goods/status',array('status'=>1))?>">
            <i class='icow icow-shangjia2'></i> 上架
        </button>
        <?php  } ?>
        <?php  if($_GPC['type']=='sale' || empty($_GPC['type'])) { ?>
        <button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch' data-href="<?php  echo webUrl('groups/goods/status',array('status'=>0))?>">
            <i class='icow icow-xiajia3'></i> 下架
        </button>
        <?php  } ?>
        <?php  } ?>
        <?php  if($_GPC['type']=='recycle') { ?>
        <?php if(cv('groups.goods.restore')) { ?>
        <button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch-remove' data-confirm="确认要恢复?" data-href="<?php  echo webUrl('groups/goods/restore')?>">
            <i class='icow icow-huifu1'></i> 恢复到仓库
        </button>
        <?php  } ?>
        <?php if(cv('groups.goods.delete1')) { ?>
        <button class="btn btn-default btn-sm  btn-operation" type="button" data-toggle='batch-remove' data-confirm="如果商品存在购买记录，会无法关联到商品, 确认要彻底删除吗?" data-href="<?php  echo webUrl('groups/goods/delete1')?>">
            <i class='icow icow-shanchu5'></i> 彻底删除
        </button>
        <?php  } ?>
        <?php  } else { ?>
        <?php if(cv('groups.goods.delete')) { ?>
        <button class="btn btn-default btn-sm  btn-operation" type="button" data-toggle='batch-remove' data-confirm="确认要删除吗?" data-href="<?php  echo webUrl('groups/goods/delete')?>">
            <i class='icow icow-shanchu1'></i> 删除
        </button>
        <?php  } ?>
        <?php  } ?>
    </div>
</div>
<table class="table table-hover table-responsive">
<thead>
<tr>
    <th style="width:25px;"></th>
    <th style="width:50px;">排序</th>
    <th style="width:70px;">商品标题</th>
    <th style="width:200px;">&nbsp;</th>
    <th>团购价</th>
    <th>单购价</th>
    <th>库存</th>
    <th>销量</th>
    <th>状态</th>
    <th style="text-align: center;width: 65px;">操作</th>
</tr>
</thead>
<tbody>
<?php  if(is_array($list)) { foreach($list as $row) { ?>
<tr>
    <td><input type='checkbox' value="<?php  echo $row['id'];?>"/></td>
    <td>
        <?php if(cv('groups.goods.edit')) { ?>
        <a href='javascript:;' data-toggle='ajaxEdit' data-href="<?php  echo webUrl('groups/goods/property',array('type'=>'displayorder','id'=>$row['id']))?>"><?php  echo $row['displayorder'];?></a>
        <?php  } else { ?>
        <?php  echo $row['displayorder'];?>
        <?php  } ?>
    </td>
    <td><img src="<?php  echo tomedia($row['thumb'])?>" style="width:40px;height:40px;padding:1px;border:1px solid #ccc;" onerror="this.src='../addons/ewei_shopv2/static/images/nopic.png'"/>
    </td>
    <td>
        <?php  if(!empty($row['subtitle'])) { ?><span class='label label-warning'><?php  echo $row['subtitle'];?></span><?php  } ?>
        <?php  echo $row['title'];?>
        <br/>
        <span class='text-primary'>[<?php  echo $row['name'];?>]</span>
    </td>
    <td><?php  echo $row['groupsprice'];?></td>
    <td ><?php  if($row['singleprice']) { ?><?php  echo $row['singleprice'];?><?php  } else { ?>--<?php  } ?></td>
    <td><?php  echo $row['stock'];?></td>
    <td>
        <?php  echo $row['sales'];?>
        <!--<span class='label <?php  if($row['ishot']==1) { ?>label-success<?php  } else { ?>label-default<?php  } ?>'
        <?php if(cv('groups.goods.edit')) { ?>
        data-toggle='ajaxSwitch'
        data-switch-value='<?php  echo $row['ishot'];?>'
        data-switch-value0='0|否|label label-default|<?php  echo webUrl('groups/goods/property',array('type'=>'ishot', 'value'=>1,'id'=>$row['id']))?>'
        data-switch-value1='1|是|label label-success|<?php  echo webUrl('groups/goods/property',array('type'=>'ishot', 'value'=>0,'id'=>$row['id']))?>'
        <?php  } ?>><?php  if($row['ishot']==1) { ?>否<?php  } else { ?>是<?php  } ?></span>-->
    </td>
    <td >
        <span class='label <?php  if($row['status']==1) { ?>label-primary<?php  } else { ?>label-default<?php  } ?>'
        <?php if(cv('groups.goods.edit')) { ?>
        data-toggle='ajaxSwitch'
        data-switch-value='<?php  echo $row['status'];?>'
        data-switch-value0='0|下架|label label-default|<?php  echo webUrl('groups/goods/property',array('type'=>'status', 'value'=>1,'id'=>$row['id']))?>'
        data-switch-value1='1|上架|label label-primary|<?php  echo webUrl('groups/goods/property',array('type'=>'status', 'value'=>0,'id'=>$row['id']))?>'
        <?php  } ?>><?php  if($row['status']==1) { ?>上架<?php  } else { ?>下架<?php  } ?></span>
    </td>
    <td >
        <?php if(cv('groups.goods.view|groups.goods.edit')) { ?>
        <a class='btn btn-default btn-sm btn-op btn-operation' href="<?php  echo webUrl('groups/goods/edit',array('id' => $row['id']));?>">
            <span data-toggle="tooltip" data-placement="top" title="" data-original-title=" <?php if(cv('groups.goods')) { ?>编辑<?php  } else { ?>查看<?php  } ?>">
                <?php if(cv('groups.goods')) { ?>
                    <i class='icow icow-bianji2'></i>
                <?php  } else { ?>
                    <i class='icow icow-chakan-copy'></i>
                <?php  } ?>
           </span>
        </a>
        <?php  } ?>
        <?php if(cv('groups.goods.delete')) { ?>
        <a class='btn btn-default btn-sm btn-op btn-operation' data-toggle='ajaxRemove' href="<?php  echo webUrl('groups/goods/delete',array('id' => $row['id']));?>" data-confirm="确定要删除该商品吗？">
            <span data-toggle="tooltip" data-placement="top" title="" data-original-title="删除">
                 <i class='icow icow-shanchu1'></i>
            </span>
        </a>
        <?php  } ?>
    </td>
</tr>
<?php  } } ?>
</tbody>
<tfoot>
    <tr>
        <td><input type="checkbox"></td>
        <td colspan="3">
            <div class="btn-group">
                <?php if(cv('groups.goods.edit')) { ?>
                <?php  if($_GPC['type']=='store') { ?>
                <button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch' data-href="<?php  echo webUrl('groups/goods/status',array('status'=>1))?>">
                    <i class='icow icow-shangjia2'></i> 上架
                </button>
                <?php  } ?>
                <?php  if($_GPC['type']=='sale' || empty($_GPC['type'])) { ?>
                <button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch' data-href="<?php  echo webUrl('groups/goods/status',array('status'=>0))?>">
                    <i class='icow icow-xiajia3'></i> 下架
                </button>
                <?php  } ?>
                <?php  } ?>
                <?php  if($_GPC['type']=='recycle') { ?>
                <?php if(cv('groups.goods.restore')) { ?>
                <button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch-remove' data-confirm="确认要恢复?" data-href="<?php  echo webUrl('groups/goods/restore')?>">
                    <i class='icow icow-huifu1'></i> 恢复到仓库
                </button>
                <?php  } ?>
                <?php if(cv('groups.goods.delete1')) { ?>
                <button class="btn btn-default btn-sm  btn-operation" type="button" data-toggle='batch-remove' data-confirm="如果商品存在购买记录，会无法关联到商品, 确认要彻底删除吗?" data-href="<?php  echo webUrl('groups/goods/delete1')?>">
                    <i class='icow icow-shanchu5'></i> 彻底删除
                </button>
                <?php  } ?>
                <?php  } else { ?>
                <?php if(cv('groups.goods.delete')) { ?>
                <button class="btn btn-default btn-sm  btn-operation" type="button" data-toggle='batch-remove' data-confirm="确认要删除吗?" data-href="<?php  echo webUrl('groups/goods/delete')?>">
                    <i class='icow icow-shanchu1'></i> 删除
                </button>
                <?php  } ?>
                <?php  } ?>
            </div>
        </td>
        <td colspan="6" class="text-right"><?php  echo $pager;?></td>
    </tr>
</tfoot>
</table>
<?php  } else { ?>
<div class='panel panel-default'>
    <div class='panel-body' style='text-align: center;padding:30px;'>暂时没有任何商品</div>
</div>
<?php  } ?>