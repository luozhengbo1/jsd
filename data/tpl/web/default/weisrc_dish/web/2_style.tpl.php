<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/header', TEMPLATE_INCLUDEPATH)) : (include template('public/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/comhead', TEMPLATE_INCLUDEPATH)) : (include template('public/comhead', TEMPLATE_INCLUDEPATH));?>
<script src="../addons/weisrc_dish/template/js/jquery-sortable.js"></script>
<style>
    .dragged {
        position: absolute;
        opacity: 0.5;
        z-index: 2000;
    }
    li {
        list-style: none;
    }
</style>
<script>
    $(function() {
        $("ol.banner").sortable({handle: '.input-group-addon'});
        $("ol.contents").sortable({handle: '.mmove'});
    });
</script>
<ul class="nav nav-tabs">
    <li<?php  if($operation == 'display') { ?> class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('style', array())?>">主题设置</a></li>
    <?php  if($operation == 'display') { ?>
    <li<?php  if($operation == 'default') { ?> class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('style', array(op => 'default'))?>">添加默认板块</a></li>
    <?php  } ?>
    <?php  if($operation == 'post') { ?><li class="active"><a href="#">主题编辑</a></li><?php  } ?>
</ul>
<?php  if($operation == 'display') { ?>
<div class="main">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
        <div class="panel panel-default">
            <div class="panel-heading">
                首页设置
            </div>
            <div class="panel-body">
                <ol class="contents" style="padding: 0px;">
                    <?php  $isfirst = 0;?>
                    <?php  if(is_array($styles)) { foreach($styles as $style) { ?>
                    <li class="panel-group">
                        <input type="hidden" name="mtype[<?php  echo $style['id'];?>]" value="<?php  echo $style['type'];?>">
                        <div class="panel panel-default">
                            <span class="mmove"><i class="fa fa-arrows" style="padding: 12px;"></i></span>
                            <?php  if($style['type']=='home_slide') { ?>
                            <a class="pull-right" style="margin-top: 10px;margin-right: 18px;"
                               href="<?php  echo $this->createWebUrl('style', array('op' => 'delete','id' =>  $style['id']))?>"  onclick="return confirm('确认删除吗？');return false;" >删除</a>
                            <a class="pull-right" style="margin-top: 10px;margin-right: 5px;"
                               href="<?php  echo $this->createWebUrl('style', array('op' => 'post','id' =>  $style['id']))?>" >编辑</a>
                            <?php  } ?>
                            <div class="panel-heading">
                                <h5 class="panel-title">
                                    <div>
                                        <?php  echo $style['title'];?>
                                        <?php  if($style['type']=='home_slide') { ?>
                                        (
                                        <?php  if($style['slidetype']==1) { ?>
                                        单图横幅
                                        <?php  } else if($style['slidetype']==2) { ?>
                                        双图并排
                                        <?php  } else if($style['slidetype']==3) { ?>
                                        三图排列
                                        <?php  } ?>
                                        )
                                        <?php  } ?>
                                        <?php  if($isfirst==0) { ?>
                                        <span class="color-green" style="font-size: 13px;color:#079200!important;">支持拖动排序</span>
                                        <?php  } ?>
                                        <span class="label label-default <?php  if($style['status']==1) { ?>label-success<?php  } ?> pull-right"
                                          onclick="setProperty(this,<?php  echo $style['id'];?>)" data='<?php  echo $style['status'];?>'>
                                        显示/隐藏</span>
                                    </div>
                                </h5>
                            </div>
                        </div>
                    </li>
                    <?php  $isfirst=1;?>
                    <?php  } } ?>
                </ol>
                <a class="btn btn-default col-lg-2" onclick="addModules();">添加图片组</a>
            </div>
        </div>
        <div class="form-group col-sm-12">
            <input type="hidden" name="id" value="<?php  echo $setting['id'];?>" />
            <input type="submit" name="submit" value="保存设置" class="btn btn-primary col-lg-3" />
            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
        </div>
	</form>
</div>
<script type="text/html" id="banner-form-html">
    <?php  include $this->template('web/_banner');?>
</script>
<script type="text/javascript">
    <!--
    function setProperty(obj,id){
        $(obj).html($(obj).html() + "...");
        $.post("<?php  echo $this->createWebUrl('setstyleproperty')?>"
                ,{id:id, data: obj.getAttribute("data")}
                ,function(d){
                    $(obj).html($(obj).html().replace("...",""));
                    $(obj).attr("data",d.data);
                    if(d.result==1){
                        $(obj).toggleClass("label-success");
                    } else {
                        $(obj).toggleClass("label-default");
                    }
                },"json"
        );
    }

    function addModules(){
        $.ajax({
            url: "<?php  echo $this->createWebUrl('getbanner')?>"
            ,cache: false
            ,type :'post'
            ,data :{}
        }).done(function(html) {
//            $(".contents").append(html);

            $('.contents').append($('#banner-form-html').html());
        });
    }
    //-->
</script>
<?php  } else if($operation == 'post') { ?>
<div class="main">
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
        <div class="panel panel-default">
            <div class="panel-heading">
                主题编辑
            </div>
            <div class="panel-body">
                <input type="hidden" name="id" value="<?php  echo $id;?>" />
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">组合名称</label>
                    <div class="col-sm-9">
                        <input type="text" name="title" class="form-control" value="<?php  echo $item['title'];?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">组合类型</label>
                    <div class="col-sm-9">
                        <select class="form-control" name="slidetype" id="slidetype">
                            <option value="1" <?php  if($item['slidetype']==1) { ?>selected<?php  } ?>>单图横幅</option>
                            <option value="2" <?php  if($item['slidetype']==2) { ?>selected<?php  } ?>>双图并排</option>
                            <option value="3" <?php  if($item['slidetype']==3) { ?>selected<?php  } ?>>三图排列</option>
                        </select>
                        <div class="help-block"></div>
                    </div>
                </div>
            </div>

        <div class="panel-heading">
            图片信息
        </div>
        <div class="panel-body">
            <?php  if($prize) { ?>
            <?php  if(is_array($prize)) { foreach($prize as $row) { ?>
            <div>
                <input type="hidden" name="prize_id[<?php  echo $row['id'];?>]" value="<?php  echo $row['id'];?>" />
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 control-label">名称</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" value="<?php  echo $row['pictitle'];?>" name="pictitle[<?php  echo $row['id'];?>]">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 control-label">链接</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" value="<?php  echo $row['picurl'];?>" name="picurl[<?php  echo $row['id'];?>]">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">图片</label>
                    <div class="col-sm-9">
                        <?php  echo tpl_form_field_image('picimage['.$row['id'].']',$row['picimage']);?>
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9">
                        <button type="button" class="btn btn-danger btn_del_award2" data-id="<?php  echo $row['id'];?>">删除</button>
                    </div>
                </div>
                <hr/>
            </div>
            <?php  } } ?>
            <?php  } ?>
            <div id="prize" style="display: none">
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 control-label">名称</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" value="" name="pictitle_new[]">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 control-label">链接</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" value="" name="picurl_new[]">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">图片</label>
                    <div class="col-sm-9">
                        <?php  echo tpl_form_field_image('picimage_new[]','');?>
                        <div class="help-block"></div>
                    </div>
                </div>
            </div>
            <?php  if(empty($prize)) { ?>
            <div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 control-label">名称</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" value="" name="pictitle_new[]">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 control-label">链接</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" value="" name="picurl_new[]">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">图片</label>
                    <div class="col-sm-9">
                        <?php  echo tpl_form_field_image('picimage_new[]','');?>
                        <div class="help-block"></div>
                    </div>
                </div>
                <hr/>
            </div>
            <?php  } ?>
            <span id="award_insert_flag" style="display: none"></span>
            <div class="form-group">
                <div class="col-sm-5"></div>
                <div class="col-sm-7">
                    <button id="btn_add_award" type="button" class="btn btn-warning">
                        <span class="glyphicon glyphicon-plus"></span> 添加图片
                    </button>
                </div>
            </div>
        </div>
        </div>
        <div class="form-group col-sm-12">
            <input type="submit" name="submit" value="保存设置" class="btn btn-primary col-lg-3" />
            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
        </div>

    </form>
</div>
<script>
    $('#btn_add_award').bind('click', function(){
        var content = '<div>';
        content += $("#prize").html();
        content += '<div class="form-group">';
        content += '<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>';
        content += '<div class="col-sm-9">';
        content += '';
        content += '</div>';
        content += '</div>';
        content += '<hr/>';
        content += '</div>';
        $('#award_insert_flag').before(content);
    });
    $('.btn_del_award').bind('click', function(){
        alert('123123');
        var obj = $(this).parent().parent().parent();
        obj.slideUp(300, function() {
            obj.remove();
        });
    });
    $('.btn_del_award2').bind('click', function(){
        var obj = $(this).parent().parent().parent();
        obj.slideUp(300, function() {
            obj.remove();
        });

        id = $(this).attr("data-id");
        $.post('<?php  echo $this->createWebUrl('style',array('op' => 'deletepic'));?>', {picid:id},function(data){
        },'json');
    });
</script>
<?php  } ?>


<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('public/footer', TEMPLATE_INCLUDEPATH)) : (include template('public/footer', TEMPLATE_INCLUDEPATH));?>