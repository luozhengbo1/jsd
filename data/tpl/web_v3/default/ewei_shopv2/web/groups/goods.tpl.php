<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<style type="text/css">
.table_kf {display: none;width:100%}
.table_kf.active {display: table;}
</style>
<div class="page-header">
    当前位置：<span class="text-primary">商品管理</span>
</div>
<div class="page-content">
    <form action="./index.php" method="get" class="form-horizontal" plugins="form">
        <input type='hidden' id='tab' name='type' value="<?php  echo $_GPC['type'];?>"/>
        <input type="hidden" name="c" value="site"/>
        <input type="hidden" name="a" value="entry"/>
        <input type="hidden" name="m" value="ewei_shopv2"/>
        <input type="hidden" name="do" value="web"/>
        <input type="hidden" name="r" value="groups.goods"/>
        <div class="page-toolbar m-b-sm m-t-sm">
            <div class="col-sm-6">
                <span class='pull-left'>
                <?php if(cv('groups.goods.add')) { ?>
                    <a class='btn btn-primary btn-sm' href="<?php  echo webUrl('groups/goods/add')?>"><i class='fa fa-plus'></i> 添加商品</a>
                <?php  } ?>
                </span>
            </div>
            <div class="col-sm-6 pull-right">
                <div class="input-group">
                    <div class="input-group-select">
                        <select name="category" class='form-control' style="width:120px;">
                            <option value="" <?php  if(empty($_GPC['category'])) { ?>selected<?php  } ?> >商品分类</option>
                            <?php  if(is_array($category)) { foreach($category as $cate) { ?>
                            <option value="<?php  echo $cate['id'];?>" <?php  if($_GPC['category']==$cate['id']) { ?>selected<?php  } ?>><?php  echo $cate['name'];?></option>
                            <?php  } } ?>
                        </select>
                    </div>
                    <div class="input-group-select">
                        <select name='status' class='form-control  input-sm' style='width:100px;'>
                            <option value='' <?php  if($_GPC['status']=='') { ?>selected<?php  } ?>>状态</option>
                            <option value='0' <?php  if($_GPC['status']=='0') { ?>selected<?php  } ?>>下架</option>
                            <option value='1' <?php  if($_GPC['status']=='1') { ?>selected<?php  } ?> >上架</option>
                        </select>
                    </div>
                    <input type="text" class="input-sm form-control" name='keyword' value="<?php  echo $_GPC['keyword'];?>" placeholder="请输入关键词">
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="submit">搜索</button>
                    </span>
                </div>
            </div>
        </div>
        <ul class="nav nav-arrow-next nav-tabs" id="myTab" >
            <li class="<?php  if($_GPC['type']=='sale' || empty($_GPC['type'])) { ?>active<?php  } ?>" >
                <a href="<?php  echo webUrl('groups/goods',array('type'=>sale))?>">出售中 (<span class='goods-ing'>-</span>)</a>
            </li>
            <li class="<?php  if($_GPC['type']=='sold') { ?>active<?php  } ?>" >
                <a href="<?php  echo webUrl('groups/goods',array('type'=>sold))?>">已售罄 (<span class='goods-sold'>-</span>)</a>
            </li>
            <li class="<?php  if($_GPC['type']=='store') { ?>active<?php  } ?>" >
                <a href="<?php  echo webUrl('groups/goods',array('type'=>store))?>">仓库中 (<span class='goods-store'>-</span>)</a>
            </li>
            <li class="<?php  if($_GPC['type']=='recycle') { ?>active<?php  } ?>">
                <a href="<?php  echo webUrl('groups/goods',array('type'=>recycle))?>">回收站 (<span class='goods-recycle'>-</span>)</a>
            </li>
        </ul>
    </form>
    <form action="" method="post">
        <div class="table_kf <?php  if($_GPC['type']=='sale' || empty($_GPC['type'])) { ?>active<?php  } ?>" id="tab_sale"><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('groups/goods/sale', TEMPLATE_INCLUDEPATH)) : (include template('groups/goods/sale', TEMPLATE_INCLUDEPATH));?></div>
        <div class="table_kf <?php  if($_GPC['type']=='sold') { ?>active<?php  } ?>" id="tab_sold"><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('groups/goods/sold', TEMPLATE_INCLUDEPATH)) : (include template('groups/goods/sold', TEMPLATE_INCLUDEPATH));?></div>
        <div class="table_kf <?php  if($_GPC['type']=='store') { ?>active<?php  } ?>" id="tab_store"><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('groups/goods/store', TEMPLATE_INCLUDEPATH)) : (include template('groups/goods/store', TEMPLATE_INCLUDEPATH));?></div>
        <div class="table_kf <?php  if($_GPC['type']=='recycle') { ?>active<?php  } ?>" id="tab_recycle"><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('groups/goods/recycle', TEMPLATE_INCLUDEPATH)) : (include template('groups/goods/recycle', TEMPLATE_INCLUDEPATH));?></div>

    </form>
</div>
<script type="text/javascript">
$(function(){
    /*
     * 出售中 1 已售罄 2 仓库中 3 回收站 4
     * */
    $.getJSON("<?php  echo webUrl('groups/goods/total');?>",{ type: "1"},function(json){
        $(".goods-ing").text(json);
        $.getJSON("<?php  echo webUrl('groups/goods/total');?>",{ type: "2"},function(json){
            $(".goods-sold").text(json);
            $.getJSON("<?php  echo webUrl('groups/goods/total');?>",{ type: "3"},function(json){
                $(".goods-store").text(json);
                $.getJSON("<?php  echo webUrl('groups/goods/total');?>",{ type: "4"},function(json){
                    $(".goods-recycle").text(json);
                });
            });
        });
    });
})
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>