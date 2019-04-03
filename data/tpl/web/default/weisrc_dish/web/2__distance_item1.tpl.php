<?php defined('IN_IA') or exit('Access Denied');?><div class="form-group">
    <!-- <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label> -->
    <div class="col-sm-7">
        
        <div class="input-group">
            <span class="input-group-addon">配送距离：</span>
            <input type="text" class="form-control" value="" name="begindistance">
            <span class="input-group-addon no-b">公里至</span>
            <input type="text" class="form-control" value="" name="enddistance">
            <span class="input-group-addon no-b">公里,平台承担的配送费</span>
            <input type="text" class="form-control" value="" name="dispatchprice">
            <!--<span class="input-group-addon no-b">元,起送费</span>-->
            <!--<input type="text" class="form-control" value="" name="sendingprice[]">-->
            <span class="input-group-addon no-l-b">元</span>
            <!--freeprice-->
        </div>
    </div>
    <div class="col-sm-1">
        <a class="btn btn-danger btn-sm" onclick="$(this).parents('.form-group').remove(); return false;" href="<?php  echo $this->createWebUrl('outside', array('op' => 'delete', 'id' => $row['id']))?>">删除
        </a>
    </div>

</div>