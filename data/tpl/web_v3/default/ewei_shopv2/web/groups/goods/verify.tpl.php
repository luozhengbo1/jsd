<?php defined('IN_IA') or exit('Access Denied');?><style type='text/css'>
    body { width:100%;}
 
    .img-thumbnail { width:100px; height:100px;}
    .img-nickname { height:25px;line-height:25px;width:90px;margin-left:5px;margin-right:5px;position: absolute;bottom:55px;color:#fff;text-align: center;background:rgba(0,0,0,0.7)}
</style>
 
<div class="form-group">
    <label class="col-lg control-label">支持线下核销</label>
    <div class="col-sm-6 col-xs-6">
        <?php if( ce('groups.goods' ,$item) ) { ?>
        <label class="radio-inline"><input type="radio" name="isverify" value="1" <?php  if($item['isverify'] == 1) { ?>checked="true"<?php  } ?> /> 支持</label>
        <label class="radio-inline"><input type="radio" name="isverify" value="0" <?php  if(empty($item['isverify']) || $item['isverify'] == 0) { ?>checked="true"<?php  } ?>  /> 不支持</label>
        <?php  } else { ?>
        <div class='form-control-static'>
            <?php  if($item['isverify'] == 1) { ?>支持<?php  } else { ?>不支持<?php  } ?>
        </div>
        <?php  } ?>
    </div>
</div>

<div class="form-group">
    <label class="col-lg control-label">核销类型</label>
    <div class="col-sm-8 col-xs-8">
        <?php if( ce('groups.goods' ,$item) ) { ?>
        <label class="radio-inline"><input type="radio" name="verifytype" value="0" <?php  if(empty($item['verifytype'])) { ?>checked="true"<?php  } ?>  /> 按订单核销</label>
        <label class="radio-inline"><input type="radio" name="verifytype" value="1" <?php  if($item['verifytype'] == 1) { ?>checked="true"<?php  } ?>   /> 按次核销</label>
        <div class='input-group' id="verifynum" style="margin:10px 0;<?php  if($item['verifytype'] == 0) { ?>display:none;<?php  } ?>">
            <span class="input-group-addon">核销次数</span>
            <input type="text" name="verifynum" value="<?php  echo $item['verifynum'];?>" class="form-control"/>
            <span class="input-group-addon">次</span>
        </div>
        <?php  } else { ?>
        <div class='form-control-static'>
            <?php  if(empty($item['isverify'])) { ?>按订单核销<?php  } else if($item['verifytype'] == 1) { ?>按消费码核销<?php  } ?>
        </div>
        <?php  } ?>
        <p class="help-block">
            按订单核销： 不管购买多少 一次核销完成<br>
            按次核销：  一个消费码使用多次（购买的数量）
        </p>
    </div>
</div>

<div class="form-group">
    <label class="col-lg control-label">核销门店选择</label>
    <div class="col-sm-9 col-xs-12 chks">
       <?php if( ce('groups.goods' ,$item) ) { ?>
        <?php  echo tpl_selector('storeids',
        array('text'=>'storename',
        'multi'=>1,
        'type'=>'text',
        'placeholder'=>'门店名称',
        'buttontext'=>'选择门店 ',
        'items'=>$stores,
        'url'=>webUrl('shop/verify/store/query')))?>
          <?php  } else { ?>
          <div class='form-control-static'>
             <?php  if(is_array($stores)) { foreach($stores as $store) { ?>
                  <?php  echo $store['storename'];?>; 
             <?php  } } ?>
         </div>
          <?php  } ?>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        $("input[name='verifytype']").click(function(){
            if(this.value== true){
                $("#verifynum").show()
            }else{
                $("#verifynum").hide();
            }
        })
    })
</script>