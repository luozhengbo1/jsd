<?php defined('IN_IA') or exit('Access Denied');?><style>
    .item_box img{
        width: 100%;
        height: 100%;
    }
</style>

<script type="text/html" id="time-form-html">
    <?php  include $this->template('web/_time_item');?>
</script>

<link rel="stylesheet" href="<?php  echo $_W['siteroot'];?>addons/weisrc_dish/public/web/css/awesome-bootstrap-checkbox.css">
<div class="main" style="margin-top: 0px;">
    <script>
        require(['jquery', 'util'], function($, u){
            $('.account p a').each(function(){
                u.clip(this, $(this).text());
            });
        });
    </script>
    <form action="" method="post" onsubmit="return check();" class="form-horizontal form" enctype="multipart/form-data">
        <div class="panel panel-default">
            <div class="panel-heading">
                门店信息 <a class="btn btn-danger btn-sm" href="#" onclick="hdurl('<?php  echo $reply['id'];?>');">商家入口</a>
            </div>
            <div class="panel-body">
                <ul class="nav nav-tabs" id="myTab">
                    <li class="active"><a href="#tab_basic" aria-controls="tab_basic" role="tab" data-toggle="pill">基本信息</a></li>
                    <li><a href="#tab_high" aria-controls="tab_high" role="tab" data-toggle="pill">高级设置</a></li>
                    <li><a href="#tab_out" aria-controls="tab_out" role="tab" data-toggle="pill">外送设置</a></li>
                    <li><a href="#tab_in" aria-controls="tab_in" role="tab" data-toggle="pill">店内设置</a></li>
                    <li><a href="#tab_coupon" aria-controls="tab_coupon" role="tab" data-toggle="pill">门店优惠</a></li>
                    <li><a href="#tab_paytype" aria-controls="tab_paytype" role="tab" data-toggle="pill">支付设置</a></li>
                    <li><a href="#tab_nave_text" aria-controls="tab_nave_text" role="tab" data-toggle="pill">个性化信息</a></li>
                    <li><a href="#tab_link" aria-controls="tab_link" role="tab" data-toggle="pill">外链设置</a></li>
                </ul>
                <div class="tab-content">
                    <?php  include $this->template('web/stores_tab_basic');?>
                    <?php  include $this->template('web/stores_tab_in');?>
                    <?php  include $this->template('web/stores_tab_coupon');?>
                    <?php  include $this->template('web/stores_tab_high');?>
                    <?php  include $this->template('web/stores_tab_paytype');?>
                    <?php  include $this->template('web/stores_tab_nave_text');?>
                    <?php  include $this->template('web/stores_tab_link');?>
                    <?php  include $this->template('web/stores_tab_out');?>
                </div>
            </div>
        </div>
        <div class="form-group col-sm-12">
            <input type="submit" name="submit" value="保存设置" class="btn btn-primary col-lg-3" />
            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
        </div>
    </form>
    <div class="modal fade" id="Modal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="  margin-top: 60px;">
        <div class="modal-dialog" style="  width: 800px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">固定链接</h4>
                </div>
                <style>.modal-body { border-bottom: 1px solid #F1F3F5;}</style>
                <div class="modal-body" style="width: 100%;float: none;">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">门店网址</label>
                        <div class="col-sm-3 col-xs-5">
                            <span id="tpindex" class="label label-success " style="  word-wrap: break-word;"></span>

                        </div>
                    </div>
                </div>
                <div class="modal-body" style="width: 100%;float: none;">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">外卖网址</label>
                        <div class="col-sm-3 col-xs-5">
                            <span id="tpwaimai" class="label label-success "></span>
                        </div>
                    </div>
                </div>
                <div class="modal-body" style="width: 100%;float: none;">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">快餐网址</label>
                        <div class="col-sm-3 col-xs-5">
                            <span  id="tpkuaican" class="label label-success "></span>
                        </div>
                    </div>
                </div>
                <div class="modal-body" style="width: 100%;float: none;">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">排号网址</label>
                        <div class="col-sm-3 col-xs-5">
                            <span  id="tppaihao" class="label label-success "></span>
                        </div>
                    </div>
                </div>
                <div class="modal-body" style="width: 100%;float: none;">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">预定网址</label>
                        <div class="col-sm-3 col-xs-5">
                            <span  id="tpyuding" class="label label-success "></span>
                        </div>
                    </div>
                </div>
                <div class="modal-body" style="width: 100%;float: none;">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">排号大屏幕</label>
                        <div class="col-sm-3 col-xs-5">
                            <span  id="tpdpm" class="label label-success "></span>
                        </div>
                    </div>
                </div>
                <div class="modal-body" style="width: 100%;float: none;">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">寄存网址</label>
                        <div class="col-sm-3 col-xs-5">
                            <span  id="tpjicun" class="label label-success "></span>
                        </div>
                    </div>
                </div>
                <div class="modal-body" style="width: 100%;float: none;">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">收银网址</label>
                        <div class="col-sm-3 col-xs-5">
                            <span  id="tpshouyin" class="label label-success "></span>
                        </div>
                    </div>
                </div>
                <div class="modal-body" style="width: 100%;float: none;">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">商品接口</label>
                        <div class="col-sm-3 col-xs-5">
                            <span  class="label label-success ">
                                <?php  echo $_W['siteroot'];?>app/index.php?i=<?php  echo $_W['uniacid'];?>&c=entry&sid=<?php  echo $reply['id'];?>&do=getdishlist&m=weisrc_dish
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    require(['util', 'clockpicker'], function(u, $){
        $('.clockpicker :text').clockpicker({autoclose: true});

        u.editor($('.richtext')[0]);

        $('#add-time').click(function(){
            $('#time-list').append($('#time-form-html').html());
            $('.clockpicker :text').clockpicker({autoclose: true});
        });

        $('#selectImage').click(function(){
            util.uploadMultiPictures(function(images){
                var s = '';
                $.each(images, function(){
                    s += '<div class="col-lg-3">'+
                            '	<input type="hidden" name="thumbs[image][]" value="'+this.filename+'">' +
                            '	<div class="panel panel-default panel-slide">'+
                            '		<div class="btnClose" onclick="$(this).parent().parent().remove()"><i class="fa fa-times"></i></div>' +
                            '		<div class="panel-body">'+
                            '			<img src="'+this.url+'" width="100%" height="170">'+
                            '			<div>'+
                            '				<input class="form-control last pull-right" placeholder="跳转链接" name="thumbs[url][]">'+
                            '			</div>'+
                            '		</div>'+
                            '	</div>'+
                            '</div>'
                });
                $('#slideContainer').append(s);
            });
        });

//        $(document).on('click', '.remind-reply-del, .comment-reply-del, .times-del, .custom-url-del', function(){
//            $(this).parent().parent().remove();
//            return false;
//        });
    });
</script>
<script>
    function hdurl(id){
        $('#Modal2').modal('toggle');
        $('#tpindex').html('<?php  echo $_W['siteroot'];?>app/index.php?i=<?php  echo $_W['uniacid'];?>&c=entry&id=' + id +
        '&do=detail&m=weisrc_dish');
        $('#tpwaimai').html('<?php  echo $_W['siteroot'];?>app/index.php?i=<?php  echo $_W['uniacid'];?>&c=entry&storeid=' + id + '&do=waplist&m=weisrc_dish&mode=2');
        $('#tpkuaican').html('<?php  echo $_W['siteroot'];?>app/index.php?i=<?php  echo $_W['uniacid'];?>&c=entry&storeid=' + id + '&do=waplist&m=weisrc_dish&mode=4');
        $('#tppaihao').html('<?php  echo $_W['siteroot'];?>app/index.php?i=<?php  echo $_W['uniacid'];?>&c=entry&storeid=' + id + '&do=queue&m=weisrc_dish');
        $('#tpyuding').html('<?php  echo $_W['siteroot'];?>app/index.php?i=<?php  echo $_W['uniacid'];?>&c=entry&storeid=' + id + '&do=reservationIndex&m=weisrc_dish');
        $('#tpdpm').html('<?php  echo $_W['siteroot'];?>app/index.php?i=<?php  echo $_W['uniacid'];?>&c=entry&storeid=' + id + '&do=Screen&m=weisrc_dish');
        $('#tpjicun').html('<?php  echo $_W['siteroot'];?>app/index.php?i=<?php  echo $_W['uniacid'];?>&c=entry&storeid=' + id +
        '&do=savewineform&m=weisrc_dish');
        $('#tpshouyin').html('<?php  echo $_W['siteroot'];?>app/index.php?i=<?php  echo $_W['uniacid'];?>&c=entry&storeid=' + id + '&do=payform&m=weisrc_dish');
    }
</script>
<script type="text/javascript">
$(function(){
    $('input:radio[name="default_jump"]').click(function() {
        if(this.checked) {
            if($(this).val() == '6') {
                $('#default_jump_url').show();
            } else {
                $('#default_jump_url').hide();
            }
        }
    });
    $('input:radio[name="btn_coupon_type"]').click(function() {
        if(this.checked) {
            if ($(this).val() == '0') {
                $('#btncouponid').hide();
                $('#btncouponurl').hide();
            } else if($(this).val() == '1') {
                $('#btncouponid').show();
                $('#btncouponurl').hide();
            } else if($(this).val() == '2') {
                $('#btncouponurl').show();
                $('#btncouponid').hide();
            }
        }
    });
});

function check() {
        if($.trim($('#title').val()) == '') {
            message('没有输入门店名称.', '', 'error');
            return false;
        }
        return true;
    }
</script>