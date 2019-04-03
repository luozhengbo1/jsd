<?php defined('IN_IA') or exit('Access Denied');?><div class="tab-pane" id="tab_coupon">
    <div class=" alert alert-warning">
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">按钮类型</label>
            <div class="col-sm-9">
                <div class="radio radio-info radio-inline">
                    <input type="radio" id="btn_coupon_type3" value="0" name="btn_coupon_type" <?php  if($reply['btn_coupon_type']==0) { ?>checked<?php  } ?>>
                    <label for="btn_coupon_type3"> 不显示 </label>
                </div>
                <div class="radio radio-info radio-inline">
                    <input type="radio" id="btn_coupon_type1" value="1" name="btn_coupon_type" <?php  if($reply['btn_coupon_type']==1|| empty($reply)) { ?>checked<?php  } ?>>
                    <label for="btn_coupon_type1"> 优惠券 </label>
                </div>
                <div class="radio radio-info radio-inline">
                    <input type="radio" id="btn_coupon_type2" value="2" name="btn_coupon_type" <?php  if($reply['btn_coupon_type']==2 || empty($reply['btn_coupon_type'])) { ?>checked<?php  } ?>>
                    <label for="btn_coupon_type2"> 自定义链接 </label>
                </div>
                <div class="help-block">
                </div>
            </div>
        </div>
        <div class="form-group" style="<?php  if($reply['btn_coupon_type']==0) { ?>display:none;<?php  } ?>" id="btncouponid">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠券ID</label>
            <div class="col-sm-9">
                <input type="text" name="btn_coupon_id" class="form-control" value="<?php  echo $reply['btn_coupon_id'];?>"/>
            </div>
        </div>
        <div class="form-group" style="<?php  if($reply['btn_coupon_type']==1) { ?>display:none;<?php  } ?>" id="btncouponurl">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠链接</label>
            <div class="col-sm-9">
                <input type="text" name="btn_coupon_url" class="form-control" value="<?php  echo $reply['btn_coupon_url'];?>"/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠价格</label>
            <div class="col-sm-9">
                <input type="text" name="btn_coupon_price" class="form-control" value="<?php  echo $reply['btn_coupon_price'];?>"/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠标题</label>
            <div class="col-sm-9">
                <input type="text" name="btn_coupon_title" class="form-control" value="<?php  echo $reply['btn_coupon_title'];?>"/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠描述</label>

            <div class="col-sm-9">
                <input type="text" name="btn_coupon_desc" class="form-control" value="<?php  echo $reply['btn_coupon_desc'];?>"/>
            </div>
        </div>
    </div>
    <div class=" alert alert-warning">
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">新顾客满减优惠</label>
        <div class="col-sm-9">
            <div class="radio radio-info radio-inline">
                <input type="radio" id="is_newlimitprice1" value="1" name="is_newlimitprice" <?php  if($reply['is_newlimitprice']==1|| empty($reply)) { ?>checked<?php  } ?>>
                <label for="is_newlimitprice1"> 开启 </label>
            </div>
            <div class="radio radio-info radio-inline">
                <input type="radio" id="is_newlimitprice2" value="0" name="is_newlimitprice" <?php  if(isset($reply['is_newlimitprice']) && empty($reply['is_newlimitprice'])) { ?>checked<?php  } ?>>
                <label for="is_newlimitprice2"> 关闭 </label>
            </div>
            <div class="radio radio-info radio-inline">
                <a href="<?php  echo $this->createWebUrl('coupon', array('storeid' => $storeid, 'op' => 'display'))?>" style="color: #f00">
                    点击设置活动</a>
            </div>
            <div class="help-block">
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
        <div class="col-sm-9 form-control-static">
            <?php  if(is_array($clist1)) { foreach($clist1 as $row1) { ?>
            <a href="<?php  echo $this->createWebUrl('coupon', array('storeid' => $storeid, 'op' => 'post', 'id' => $row1['id']))?>">(<?php  echo $row1['id'];?>)<?php  echo $row1['title'];?>[编辑]</a>
            <br/>
            <?php  } } ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">老顾客满减优惠</label>
        <div class="col-sm-9">
            <div class="radio radio-info radio-inline">
                <input type="radio" id="is_oldlimitprice1" value="1" name="is_oldlimitprice"  <?php  if($reply['is_oldlimitprice']==1 || empty($reply)) { ?>checked<?php  } ?>>
                <label for="is_oldlimitprice1"> 开启 </label>
            </div>
            <div class="radio radio-info radio-inline">
                <input type="radio" id="is_oldlimitprice2" value="0" name="is_oldlimitprice" <?php  if(isset($reply['is_oldlimitprice']) && empty($reply['is_oldlimitprice'])) { ?>checked<?php  } ?>>
                <label for="is_oldlimitprice2"> 关闭 </label>
            </div>
            <div class="radio radio-info radio-inline">
                <a href="<?php  echo $this->createWebUrl('coupon', array('storeid' => $storeid, 'op' => 'display'))?>"
                   style="color: #f00">
                    点击设置活动</a>
            </div>
            <div class="help-block">
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
        <div class="col-sm-9 form-control-static">
            <?php  if(is_array($clist2)) { foreach($clist2 as $row2) { ?>
            <a href="<?php  echo $this->createWebUrl('coupon', array('storeid' => $storeid, 'op' => 'post', 'id' => $row2['id']))?>">(<?php  echo $row2['id'];?>)<?php  echo $row2['title'];?>[编辑]</a>
            <br/>
            <?php  } } ?>
        </div>
    </div>
    </div>
</div>