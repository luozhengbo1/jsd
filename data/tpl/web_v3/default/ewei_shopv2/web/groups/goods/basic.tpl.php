<?php defined('IN_IA') or exit('Access Denied');?><style type="text/css">
.img-thumbnail {width: 100px;height: 100px;}
</style>
<div class="form-group">
    <label class="col-lg control-label">排序</label>
    <div class="col-sm-9 col-xs-12">
        <?php if( ce('groups.goods' ,$item) ) { ?>
        <input type='text' class='form-control' name='displayorder' value="<?php  echo $item['displayorder'];?>" />
        <span class="help-block">数字越大，排名越靠前,如果为空，默认排序方式为创建时间</span>
        <?php  } else { ?>
        <div class='form-control-static'><?php  echo $item['displayorder'];?></div>
        <?php  } ?>
    </div>
</div>
<div class="form-group">
    <label class="col-lg control-label must">标题</label>
    <div class="col-sm-9 col-xs-12">
        <?php if( ce('groups.goods' ,$item) ) { ?>
        <div class='goodstype goodstype0'  <?php  if($item['goodstype']!=0) { ?>style="display:none"<?php  } ?>>
        <?php  echo tpl_selector('goodsid',
            array(
            'required'=>true,
        'preview'=>false,
        'value'=>$item['title'],
        'url'=>webUrl('groups/goods/query'),
        'items'=>$item
        ,'readonly'=>false,
        'nokeywords'=>1,
        'autosearch'=>1,
        'buttontext'=>'选择商品',
        'placeholder'=>'请输入商品标题','callback'=>'select_goods'))?>
        <span class="help-block">【选择商品】可从商城商品库中选择商品</span>
    </div>
    <?php  } else { ?>
    <div class='form-control-static'><?php  echo $item['title'];?></div>
    <?php  } ?>
</div>
</div>
<div class="form-group">
    <label class="col-lg control-label">副标题</label>
    <div class="col-sm-9 col-xs-12">
        <?php if( ce('groups.goods' ,$item) ) { ?>
        <input type='text' id='description' class='form-control' name='description' value="<?php  echo $item['description'];?>" />
        <?php  } else { ?>
        <div class='form-control-static'><?php  echo $item['description'];?></div>
        <?php  } ?>
    </div>
</div>
<div class="form-group">
    <label class="col-lg control-label must">分类</label>
    <div class="col-sm-9 col-xs-12">
        <?php if( ce('groups.goods' ,$item) ) { ?>
        <select class='form-control' name='category' data-rule-required='true' data-msg-required='请选择分类'>
            <option value=''>--请选择分类--</option>
            <?php  if(is_array($category)) { foreach($category as $cate) { ?>
            <option value="<?php  echo $cate['id'];?>" <?php  if($item['category']==$cate['id']) { ?>selected<?php  } ?>><?php  echo $cate['name'];?></option>
            <?php  } } ?>
        </select>
        <?php  } else { ?>
        <div class='form-control-static'><?php  echo $item['catename'];?></div>
        <?php  } ?>
    </div>
</div>
<div class="form-group">
    <label class="col-lg control-label must">商品图片</label>
    <div class="col-sm-9 col-xs-12 gimgs">
        <?php if( ce('groups.goods' ,$item) ) { ?>
        <?php  echo tpl_form_field_multi_image2('thumbs',$piclist)?>
        <span class="help-block image-block" style="display: block;">第一张为缩略图，建议为正方型图片，其他为详情页面图片，尺寸建议宽度为640，并保持图片大小一致</span>
        <span class="help-block">您可以拖动图片改变其显示顺序 </span>
        <?php  } else { ?>
        <?php  if(is_array($piclist)) { foreach($piclist as $p) { ?>
        <a href='<?php  echo tomedia($p)?>' target='_blank'>
            <img src="<?php  echo tomedia($p)?>" style='height:100px;border:1px solid #ccc;padding:1px;float:left;margin-right:5px;' />
        </a>
        <?php  } } ?>
        <?php  } ?>
    </div>
</div>
<div class="form-group">
    <label class="col-lg control-label">拼团价格</label>
    <div class="col-sm-9 col-xs-12">
        <?php if( ce('groups.goods' ,$item) ) { ?>
        <div class="input-group fixmore-input-group">
            <input type="text" name="groupsprice" class="form-control" value="<?php  echo $item['groupsprice'];?>" />
            <span class="input-group-addon">元 原价</span>
            <input type="text" name="price" class="form-control" value="<?php  echo $item['price'];?>" />
            <span class="input-group-addon">元</span>
        </div>
        <span class='help-block'>尽量填写完整，有助于于商品销售的数据分析</span>
        <?php  } else { ?>
        <div class='form-control-static'>拼团价：<?php  echo $item['groupsprice'];?> 元， 原价：<?php  echo $item['price'];?> 元 </div>
        <?php  } ?>
    </div>
</div>
<div class="form-group">
    <label class="col-lg control-label">购买次数限制</label>
    <div class="col-xs-12 col-sm-9">
        <div class="input-group fixsingle-input-group">
            <?php if( ce('groups.goods' ,$item) ) { ?>
            <input type="text" name="purchaselimit" class="form-control" value="<?php  echo $item['purchaselimit'];?>" />
            <span class="input-group-addon">次</span>
            <?php  } else { ?>
            <div class='form-control-static'><?php  echo $item['purchaselimit'];?>次</div>
            <?php  } ?>
        </div>
        <span class='help-block'>个人购买次数限制，默认是0，没有次数限制</span>
    </div>
</div>
<?php  if($item['more_spec'] == 0  ||($item['more_spec'] == 0 && $item['is_ladder'] == 0) ) { ?>
<div class="form-group"  <?php  if($item['is_ladder'] == 1) { ?>style="display: none"<?php  } ?>>

    <label class="col-lg control-label">开启阶梯团</label>
    <div class="col-xs-12 col-sm-9">
        <input class="js-switch small" name="is_ladder" id="ladder"  value="1"  type="checkbox"  <?php  if($item['is_ladder']) { ?>checked<?php  } ?>>
        <span class='help-block'>开启后不允许修改</span>
    </div>

</div>
<?php  } ?>
<!--阶梯团start-->
<div class="form-group" id="ladder_data"  <?php  if(!$item['is_ladder']) { ?> style="display: none" <?php  } ?>>

    <label class="col-lg control-label">阶梯团价格</label>

    <div class="col-xs-12 col-sm-9" id="ladder_box">

        <div class="input-group">
            <button class="btn btn-default" id="add_ladder" type="button">添加拼团价格区间</button>
        </div>
        <?php  if(is_array($ladder)) { foreach($ladder as $k => $v) { ?>
        <div class="input-group fixmore-input-group" style="margin-top: 10px;">
            <input type="hidden" name="ladder_id[]" value="<?php  echo $v['id'];?>">
            <input type="text" name="ladder_num[]" class="form-control" value="<?php  echo $v['ladder_num'];?>">
            <span class="input-group-addon">人团</span>
            <input type="text" name="ladder_price[]" class="form-control" value="<?php  echo $v['ladder_price'];?>">
            <span class="input-group-addon">元/件</span>
            <span class="input-group-addon"><a href="javascript:;" onclick="del_ladder(this)" title="删除"><i class="fa fa-times"></i></a></span>
        </div>
        <?php  } } ?>
    </div>
</div>
<!--阶梯团end-->

<div class="form-group">
    <label class="col-lg control-label">是否单购</label>
    <div class="col-xs-9 ">
        <?php if( ce('groups.goods' ,$item) ) { ?>
        <label class="radio radio-inline">
            <input type="radio" name="single" value="1" <?php  if(intval($item['single']) ==1 ) { ?>checked="checked"<?php  } ?>> 是
        </label>
        <label class="radio radio-inline">
            <input type="radio" name="single" value="0" <?php  if(intval($item['single']) ==0) { ?>checked="checked"<?php  } ?>> 否
        </label>
        <?php  } else { ?>
        <label class="radio radio-inline">
        <?php  if(intval($item['single']) ==1 ) { ?>是<?php  } else { ?>否<?php  } ?>
        </label>
        <?php  } ?>
    </div>
    <div class="col-sm-7 col-xs-7" id="single" <?php  if(intval($item['single']) == 0 ) { ?>style="display:none;"<?php  } ?>>
    <label class="col-xs-2 col-lg control-label">单购价</label>
        <div class="input-group">
            <?php if( ce('groups.goods' ,$item) ) { ?>
            <input type='text' class='form-control' name='singleprice' value="<?php  echo $item['singleprice'];?>" />
            <span class="input-group-addon">元</span>
            <?php  } else { ?>
            <?php  if(intval($item['single']) ==1 ) { ?>
            <div class='form-control-static'><?php  echo $item['singleprice'];?>元</div>
            <?php  } ?>
            <?php  } ?>
        </div>
    </div>
</div>
<div class="form-group  goodstype goodstype0">
    <label class="col-lg control-label">数量</label>
    <div class="col-sm-9 col-xs-12">
        <?php if( ce('groups.goods' ,$item) ) { ?>
        <div class="input-group fixsingle-input-group">
            <input type='text' class='form-control' name='goodsnum' value="<?php  if($item['goodsnum']) { ?><?php  echo $item['goodsnum'];?><?php  } else { ?>1<?php  } ?>" />
        </div>
        <span class="help-block">拼团商品自定义数量，默认是1</span>
        <?php  } else { ?>
        <div class='form-control-static'><?php  echo $item['goodsnum'];?></div>
        <?php  } ?>
    </div>
</div>
<div class="form-group  goodstype goodstype0">
    <label class="col-lg control-label">单位</label>
    <div class="col-sm-9 col-xs-12">
        <?php if( ce('groups.goods' ,$item) ) { ?>
        <div class="input-group fixsingle-input-group">
            <input type='text' class='form-control' name='units' value="<?php  if($item['units']) { ?><?php  echo $item['units'];?><?php  } else { ?>件<?php  } ?>" />
        </div>
        <span class="help-block">拼团商品自定义单位，如果不填写默认是【件】</span>
        <?php  } else { ?>
        <div class='form-control-static'><?php  echo $item['units'];?></div>
        <?php  } ?>
    </div>
</div>
<div class="form-group">
    <label class="col-lg control-label">已出售数</label>
    <div class="col-sm-6 col-xs-12">
        <?php if( ce('groups.goods' ,$item) ) { ?>
        <div class="input-group fixsingle-input-group">
            <input type="text" name="sales" id="sales" class="form-control" value="<?php  echo $item['sales'];?>" />
            <span class="input-group-addon">件</span>
        </div>
        <span class="help-block">物品虚拟出售数，会员下单此数据就增加, 无论是否支付</span>
        <?php  } else { ?>
        <div class='form-control-static'><?php  echo $item['sales'];?> 件</div>
        <?php  } ?>
    </div>
</div>
<div class="form-group">
    <label class="col-lg control-label">虚拟成团数</label>
    <div class="col-sm-6 col-xs-12">
        <?php if( ce('groups.goods' ,$item) ) { ?>
        <div class="input-group fixsingle-input-group">
            <input type="text" name="teamnum" id="teamnum" class="form-control" value="<?php  echo $item['teamnum'];?>" />
            <span class="input-group-addon">人</span>
        </div>
        <span class="help-block">拼团成团虚拟数量</span>
        <?php  } else { ?>
        <div class='form-control-static'><?php  echo $item['teamnum'];?> 人</div>
        <?php  } ?>
    </div>
</div>
<div class="form-group dispatch_info">
    <label class="col-lg control-label">运费设置</label>
    <div class="col-sm-6 col-xs-12">
        <?php if( ce('groups.goods' ,$item) ) { ?>
        <div class="input-group fixsingle-input-group">
            <span class='input-group-addon' style='border:none;display: none;'>
                <label class="radio-inline" style='margin-top:-7px;' >
                    <input type="radio" name="dispatchtype" value="0" <?php  if(empty($item['dispatchtype'])) { ?>checked="true"<?php  } ?>   /> 运费模板
                </label>
            </span>
            <select class="form-control tpl-category-parent select2" id="dispatchid" name="dispatchid" style="display: none;">
                <option value="0">默认模板</option>
                <?php  if(is_array($dispatch_data)) { foreach($dispatch_data as $dispatch_item) { ?>
                <option value="<?php  echo $dispatch_item['id'];?>" <?php  if($item['dispatchid'] == $dispatch_item['id']) { ?>selected="true"<?php  } ?>><?php  echo $dispatch_item['dispatchname'];?></option>
                <?php  } } ?>
            </select>
            <span class='input-group-addon' style='border:none;display: none;'><label class="radio-inline"  style='margin-top:-7px;' >
                <input type="radio"name="dispatchtype" value="1" <?php  if($item['dispatchtype'] == 1) { ?>checked="true"<?php  } ?>  /> 统一邮费</label>
            </span>

            <input type="text" name="freight" id="dispatchprice" class="form-control" value="<?php  echo $item['freight'];?>" />
            <span class="input-group-addon">元</span>
        </div>
        <?php  } else { ?>
        <div class='form-control-static'><?php  echo $item['freight'];?> 元</div>
        <?php  } ?>
    </div>
</div>
<?php  if($item['is_ladder'] ==0) { ?>
<div class="form-group" id="groupnum">
    <label class="col-lg control-label">开团人数</label>
    <div class="col-xs-12 col-sm-9">
        <div class="input-group fixsingle-input-group">
            <?php if( ce('groups.goods' ,$item) ) { ?>
            <input type="text" name="groupnum" class="form-control" value="<?php  echo $item['groupnum'];?>" />
            <span class="input-group-addon">人</span>
            <?php  } else { ?>
            <div class='form-control-static'><?php  echo $item['singleprice'];?> 人</div>
            <?php  } ?>
        </div>
    </div>
</div>
<?php  } ?>
<div class="form-group">
    <label class="col-lg control-label">组团限时(整数小时)</label>
    <div class="col-xs-12 col-sm-9">
        <div class="input-group fixsingle-input-group">
            <?php if( ce('groups.goods' ,$item) ) { ?>
            <input type="text" name="endtime" class="form-control" value="<?php  echo $item['endtime'];?>" />
            <span class="input-group-addon">小时</span>
            <?php  } else { ?>
            <div class='form-control-static'><?php  echo $item['singleprice'];?> 小时</div>
            <?php  } ?>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-lg control-label">状态</label>
    <div class="col-xs-12 col-sm-8">
        <div class="input-group fixsingle-input-group">
            <?php if( ce('groups.goods' ,$item) ) { ?>
            <label class="radio radio-inline">
                <input type="radio" name="status" value="1" <?php  if(intval($item['status']) ==1 ) { ?>checked="checked"<?php  } ?>> 上架
            </label>
            <label class="radio radio-inline">
                <input type="radio" name="status" value="0" <?php  if(intval($item['status']) ==0) { ?>checked="checked"<?php  } ?>> 下架
            </label>
            <?php  } else { ?>
            <div class='form-control-static'><?php  if(intval($item['status']) ==1 ) { ?>上架<?php  } else { ?>下架<?php  } ?></div>
            <?php  } ?>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-lg control-label">拼团首页显示</label>
    <div class="col-xs-12 col-sm-8">
        <div class="input-group fixsingle-input-group">
            <?php if( ce('groups.goods' ,$item) ) { ?>
            <label class="radio radio-inline">
                <input type="radio" name="isindex" value="1" <?php  if(intval($item['isindex']) ==1 ) { ?>checked="checked"<?php  } ?>> 是
            </label>
            <label class="radio radio-inline">
                <input type="radio" name="isindex" value="0" <?php  if(intval($item['isindex']) ==0) { ?>checked="checked"<?php  } ?>> 否
            </label>
            <?php  } else { ?>
            <div class='form-control-static'><?php  if(intval($item['isindex']) ==1 ) { ?>是<?php  } else { ?>否<?php  } ?></div>
            <?php  } ?>
        </div>
    </div>
</div>
<?php  if(!empty($id)) { ?>
<div class="form-group" style="display:none;">
    <label class="col-lg control-label">添加日期</label>
    <div class="col-xs-12 col-sm-8">
        <p class="form-control-static"><?php  echo date('Y-m-d H:i', $item['createtime']);?></p>
    </div>
</div>
<?php  } ?>
<!--<script src="../addons/ewei_shopv2/plugin/groups/static/js/spec.js"></script>-->
<script language="javascript">
    require(['jquery.ui'],function(){
        $('.multi-img-details').sortable();
    });

    require(['bootstrap'], function () {
        $('#myTab a').click(function (e) {
            $('#tab').val( $(this).attr('href'));
            e.preventDefault();
            $(this).tab('show');
        })
        });
</script>
<script type="text/javascript">
$(function(){
    $("input[name='single']").click(function(){
        if(this.value== true){
            $("#single").show()
        }else{
            $("#single").hide();
        }
    });

    //添加拼团价格区间
    $( '#add_ladder' ).click(function(){
        if($( '#ladder_box' ).children().length>10){
            alert('最多添加10个');return;
        }
        $( '#ladder_box' ).append("<div class=\"input-group fixmore-input-group\" style=\"margin-top: 10px;\"><input type='hidden' name='ladder_id[]' value=''>\n" +
            "            <input type=\"text\" name=\"ladder_num[]\" class=\"form-control\" value=\"\">\n" +
            "            <span class=\"input-group-addon\">人团</span>\n" +
            "            <input type=\"text\" name=\"ladder_price[]\" class=\"form-control\" value=\"\">\n" +
            "            <span class=\"input-group-addon\">元/件</span>\n" +
            "            <span class=\"input-group-addon\"><a href=\"javascript:;\" onclick='del_ladder(this)' title=\"删除\"><i class=\"fa fa-times\"></i></a></span>\n" +
            "        </div>");
    });

    //开启拼团
    $( '#ladder' ).click(function(){
        if( $( this ).is(':checked') ){
            $( '#ladder_data' ).css('display','block');
            $('#groupnum').css("display","none");
        }else{
            $( '#ladder_data' ).css( 'display' , 'none' );
            $('#groupnum').css("display","block");
        }
    });

});
//删除拼团价格区间
function del_ladder( self )
{
    $( self ).parent().parent().remove();
}

</script>
