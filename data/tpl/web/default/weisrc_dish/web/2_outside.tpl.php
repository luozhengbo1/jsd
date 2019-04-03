<?php defined('IN_IA') or exit('Access Denied');?><script type="text/html" id="distance-form-html">
    <?php  include $this->template('web/_distance_item1');?>
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/header', TEMPLATE_INCLUDEPATH)) : (include template('public/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/comhead', TEMPLATE_INCLUDEPATH)) : (include template('public/comhead', TEMPLATE_INCLUDEPATH));?>

<ul class="nav nav-tabs">   
    <li<?php  if($operation=='post') { ?> class="active"<?php  } ?>><a
        href="<?php  echo $this->createWebUrl('outside', array('op' => 'display'))?>">平台外送设置</a></li>
</ul>


<?php  if($operation == 'display') { ?>
<div class="main">
    <div class="panel panel-default">
        <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
        <?php  if($setting['is_auto_address'] == 0 || empty($setting)) { ?>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">按距离收外送费</label>
                <div class="col-sm-9">
                    <div class="help-block">
                        请设置添加 <a id="add-distance"><i class="fa fa-plus-circle"></i> 添加按距离收外送费</a>
                    </div>
                </div>
            </div>
            <div id="distance-list">
                <?php  if(!empty($distancelist)) { ?>
                <?php  if(is_array($distancelist)) { foreach($distancelist as $row) { ?>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-7">
                        <div class="input-group">
                            <span class="input-group-addon">配送距离：</span>
                            <input type="text" class="form-control" value="<?php  echo $row['begindistance'];?>" name="begindistance">
                            <span class="input-group-addon no-b">公里至</span>
                            <input type="text" class="form-control" value="<?php  echo $row['enddistance'];?>" name="enddistance">
                            <span class="input-group-addon no-b">公里,平台承担的配送费</span>
                            <input type="text" class="form-control" value="<?php  echo $row['dispatchprice'];?>" name="dispatchprices">
                            <!--<span class="input-group-addon no-b">元,起送费</span>-->
                            <!--<input type="text" class="form-control" value="" name="sendingprice[]">-->
                            <span class="input-group-addon no-l-b">元</span>
                            <!--freeprice-->
                        </div>
                    </div>
                    <div class="col-sm-1">
                        <a class="btn btn-danger btn-sm" href="<?php  echo $this->createWebUrl('outside', array('op' => 'delete', 'id' => $row['id']))?>">删除
                        </a>
                    </div>
                </div>
                <?php  } } ?>
                <?php  } ?>
            </div>
          
            <div class="form-group">
                <div class="col-sm-12">
                    <input name="submit" type="submit" value="保存设置" class="btn btn-primary col-lg-3" />
                    <input type="hidden" name="token" value="<?php  echo $_W['token'];?>"/>
                </div>
            </div>
            <?php  } ?>
        </form>
    </div>
</div>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/_modal_fans', TEMPLATE_INCLUDEPATH)) : (include template('web/_modal_fans', TEMPLATE_INCLUDEPATH));?>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/footer', TEMPLATE_INCLUDEPATH)) : (include template('public/footer', TEMPLATE_INCLUDEPATH));?>
<script type="text/javascript">
    $('#add-distance').click(function(){
        $('#distance-list').append($('#distance-form-html').html());
        $('#distance-form-html').html();
    });
</script>