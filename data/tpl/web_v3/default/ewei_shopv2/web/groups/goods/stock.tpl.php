<?php defined('IN_IA') or exit('Access Denied');?><div class="form-group">
    <label class="col-lg control-label">编码</label>
    <div class="col-sm-5">
        <?php if( ce('groups.goods' ,$item) ) { ?>
        <input type="text" name="goodssn" id="goodssn" class="form-control hasoption" value="<?php  echo $item['goodssn'];?>" <?php  if($item['hasoption']) { ?>readonly<?php  } ?>//>
        <?php  } else { ?>
        <div class='form-control-static'><?php  echo $item['goodssn'];?></div>
        <?php  } ?>
    </div>
</div>
<div class="form-group">
    <label class=" col-lg control-label">条码</label>
    <div class="col-sm-5">
        <?php if( ce('groups.goods' ,$item) ) { ?>
        <input type="text" name="productsn" id="productsn" class="form-control hasoption" value="<?php  echo $item['productsn'];?>" <?php  if($item['hasoption']) { ?>readonly<?php  } ?>//>
        <?php  } else { ?>
        <div class='form-control-static'><?php  echo $item['productsn'];?></div>
        <?php  } ?>
    </div>
</div>
<div class="form-group" style="display: none;">
    <label class="col-lg control-label">重量</label>
    <div class="col-sm-10">
        <?php if( ce('groups.goods' ,$item) ) { ?>
        <div class="input-group">
            <input type="text" name="weight" id="weight" class="form-control hasoption" value="<?php  echo $item['weight'];?>" <?php  if($item['hasoption']) { ?>readonly<?php  } ?>/>
            <span class="input-group-addon">克</span>
        </div>
        <?php  } else { ?>
        <div class='form-control-static'><?php  echo $item['weight'];?> 克</div>
        <?php  } ?>
    </div>
</div>

<div class="form-group">
    <label class="col-lg control-label">库存</label>
    <div class="col-sm-10">
        <?php if( ce('groups.goods' ,$item) ) { ?>
        <input type="text" name="stock" id="stock" class="form-control hasoption" value="<?php  echo $item['stock'];?>"  style="width:150px;display: inline;margin-right: 20px;" <?php  if($item['hasoption']) { ?>readonly<?php  } ?>/>
        <label class="checkbox-inline">
            <input type="checkbox" id="showstock" value="1" name="showstock" <?php  if($item['showstock']==1) { ?>checked<?php  } ?> />显示库存
        </label>
        <?php  } else { ?>
        <div class='form-control-static'><?php  echo $item['stock'];?> 件 <?php  if(empty($item['showstock'])) { ?>隐藏库存<?php  } else { ?>显示库存<?php  } ?></div>
        <?php  } ?>
    </div>
</div>
<?php  if($item['is_ladder'] == 0  ) { ?>
<div class="form-group-title">
    多规格
    <div class="pull-right" style="text-align: right;padding-right: 28px;">
        是否开启多规格 <input class="js-switch small" id="more" name="more_spec" value="1" <?php  if($item['more_spec'] == 1) { ?>checked<?php  } ?> type="checkbox" style="display: none;">
    </div>
</div>
<?php  } ?>
<div class="form-group" id="more_spec" <?php  if($item['more_spec'] == 0) { ?>style="display: none"<?php  } ?>>
    <label class="col-lg control-label"></label>
    <div class="col-sm-10">
        <div id="options" style="padding:0;">
            <table class="table table-bordered table-condensed">
                <thead>
                    <tr class="active">
                        <th>
                            <div class="">
                                <div style="padding-bottom:10px;text-align:center;">商品名称</div>

                            </div>
                        </th>
                        <th>
                            <div class="">
                                <div style="padding-bottom:10px;text-align:center;">原价</div>
                                <div class="input-group">
                                    <input type="text" class="form-control  input-sm option_marketprice_all valid" value="" aria-invalid="false">
                                    <span class="input-group-addon">
                                        <a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol('option_marketprice');"></a>
                                    </span>
                                </div>
                            </div>
                        </th>
                        <th class="type-4">
                            <div class="">
                                <div style="padding-bottom:10px;text-align:center;">单独购买价格</div>
                                <div class="input-group">
                                    <input type="text" class="form-control  input-sm option_single_price_all valid" value="" aria-invalid="false">
                                    <span class="input-group-addon">
                                        <a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol('option_single_price');"></a>
                                    </span>
                                </div>
                            </div>
                        </th>
                        <th class="type-4">
                            <div class="">
                                <div style="padding-bottom:10px;text-align:center;">拼团价格</div>
                                <div class="input-group">
                                    <input type="text" class="form-control  input-sm option_price_all valid" value="" aria-invalid="false">
                                    <span class="input-group-addon">
                                        <a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol( 'option_price' );"></a>
                                    </span>
                                </div>
                            </div>
                        </th>
                        <th class="type-4">
                            <div class="">
                                <div style="padding-bottom:10px;text-align:center;">库存</div>
                                <div class="input-group">
                                    <input type="text" class="form-control  input-sm option_stock_all valid" value="" aria-invalid="false">
                                    <span class="input-group-addon">
                                        <a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol('option_stock');"></a>
                                    </span>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody id="spec_s">
                <?php  if(is_array($specs)) { foreach($specs as $key => $value) { ?>
                    <tr>
                        <input type='hidden' name='spec[<?php  echo $key;?>][specs]' value="<?php  echo $value['specs'];?>">
                        <input type='hidden' name='spec[<?php  echo $key;?>][id]' value="<?php  echo $value['id'];?>">
                        <input type='hidden' name='spec[<?php  echo $key;?>][goods_option_id]' value="<?php  echo $value['goods_option_id'];?>">
                        <td>
                            <input type="text" class="form-control  valid " name='spec[<?php  echo $key;?>][name]' value="<?php  echo $value['title'];?>" aria-invalid="false">
                        </td>
                        <td>
                            <input type="text" class="form-control   valid option_marketprice" name='spec[<?php  echo $key;?>][marketprice]' value="<?php  echo $value['marketprice'];?>" aria-invalid="false">
                        </td>
                        <td>
                            <input type="text" class="form-control   valid option_single_price" name='spec[<?php  echo $key;?>][single_price]' value="<?php  echo $value['single_price'];?>" aria-invalid="false">
                        </td>
                        <td class="type-4">
                            <input type="text" class="form-control  option_price" name='spec[<?php  echo $key;?>][price]' value="<?php  echo $value['price'];?>">
                        </td>
                        <td class="type-4">
                            <input type="text" class="form-control  option_stock" name='spec[<?php  echo $key;?>][stock]' value="<?php  echo $value['stock'];?>">
                        </td>
                    </tr>
                <?php  } } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>