<?php defined('IN_IA') or exit('Access Denied');?><div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
    <div class="col-sm-7">
        <div class="input-group">
            <span class="input-group-addon">配送距离：</span>
            <input type="text" class="form-control" value="0" name="newbegindistance[]">
            <span class="input-group-addon no-b">公里至</span>
            <input type="text" class="form-control" value="0" name="newenddistance[]">
            <span class="input-group-addon no-b">公里,配送费</span>
            <input type="text" class="form-control" value="0" name="newdispatchprices[]">
            <!--<span class="input-group-addon no-b">元,起送费</span>-->
            <!--<input type="text" class="form-control" value="" name="sendingprice[]">-->
            <span class="input-group-addon no-l-b">元</span>
            <!--freeprice-->
        </div>
    </div>
    <div class="col-sm-1">
        <a class="btn btn-danger btn-sm" onclick="$(this).parents('.form-group').remove(); return false;" href="#">删除
        </a>
    </div>
</div>