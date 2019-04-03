<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<div class="page-header">
    当前位置：<span class="text-primary"><?php  if(!empty($item['id'])) { ?>编辑<?php  } else { ?>添加<?php  } ?>商品 <small><?php  if(!empty($item['id'])) { ?>修改【<?php  echo $item['title'];?>】
    <a href='javascript:;' title='点击复制连接' class='js-clip' data-url='<?php  echo mobileUrl('groups/goods',array('id'=>$item['id']),true)?>'>复制连接</a> <?php  } ?> </small></span>
</div>
<div class="page-content">
    <div class="page-sub-toolbar">
        <span class=''>
            <?php if(cv('groups.goods.add')) { ?>
            <a class="btn btn-primary btn-sm" href="<?php  echo webUrl('groups/goods/add')?>">添加新商品</a>
            <?php  } ?>
        </span>
    </div>
<?php  if(!$category) { ?>
    <p style="height:30px;line-height: 30px;">暂无商品分类，请先添加商品分类</p>
    <a class="btn btn-primary btn-sm" href="<?php  echo webUrl('groups/category/add')?>">添加新商品分类</a>
<?php  } else { ?>
<form id="dataform" action="" method="post" class="form-horizontal form-validate">
    <input type='hidden' id='tab' name='tab' value='basic' />
    <input type='hidden' id='gid' name='gid' value="<?php  echo $item['gid'];?>" />
    <ul class="nav nav-arrow-next nav-tabs" id="myTab">
        <li <?php  if($_GPC['tab']=='basic' || empty($_GPC['tab'])) { ?>class="active"<?php  } ?> ><a href="#tab_basic">商品</a></li>
        <li <?php  if($_GPC['tab']=='stock') { ?>class="active"<?php  } ?> ><a href="#tab_stock">库存及编码</a></li>
        <li <?php  if($_GPC['tab']=='info') { ?>class="active"<?php  } ?> ><a href="#tab_info">详情</a></li>
        <li <?php  if($_GPC['tab']=='marketing') { ?>class="active"<?php  } ?> ><a href="#tab_marketing">营销</a></li>
        <li <?php  if($_GPC['tab']=='verify') { ?>class="active"<?php  } ?>><a href="#tab_verify">线下核销</a></li>
        <li <?php  if($_GPC['tab']=='share') { ?>class="active"<?php  } ?>><a href="#tab_share">关注及分享</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane  <?php  if($_GPC['tab']=='basic' || empty($_GPC['tab'])) { ?>active<?php  } ?>" id="tab_basic"><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('groups/goods/basic', TEMPLATE_INCLUDEPATH)) : (include template('groups/goods/basic', TEMPLATE_INCLUDEPATH));?></div>
        <div class="tab-pane  <?php  if($_GPC['tab']=='stock') { ?>active<?php  } ?>" id="tab_stock"><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('groups/goods/stock', TEMPLATE_INCLUDEPATH)) : (include template('groups/goods/stock', TEMPLATE_INCLUDEPATH));?></div>
        <div class="tab-pane  <?php  if($_GPC['tab']=='info') { ?>active<?php  } ?>" id="tab_info"><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('groups/goods/info', TEMPLATE_INCLUDEPATH)) : (include template('groups/goods/info', TEMPLATE_INCLUDEPATH));?></div>
        <div class="tab-pane  <?php  if($_GPC['tab']=='marketing') { ?>active<?php  } ?>" id="tab_marketing"><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('groups/goods/marketing', TEMPLATE_INCLUDEPATH)) : (include template('groups/goods/marketing', TEMPLATE_INCLUDEPATH));?></div>
        <div class="tab-pane  <?php  if($_GPC['tab']=='verify') { ?>active<?php  } ?>" id="tab_verify"><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('groups/goods/verify', TEMPLATE_INCLUDEPATH)) : (include template('groups/goods/verify', TEMPLATE_INCLUDEPATH));?></div>
        <div class="tab-pane  <?php  if($_GPC['tab']=='share') { ?>active<?php  } ?>" id="tab_share"><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('groups/goods/share', TEMPLATE_INCLUDEPATH)) : (include template('groups/goods/share', TEMPLATE_INCLUDEPATH));?></div>
    </div>
    <div class="form-group"></div>
    <div class="form-group">
        <label class="col-lg control-label"></label>
        <div class="col-sm-9 col-xs-12">
            <?php if( ce('groups.goods' ,$item) ) { ?>
                <input type="submit"  value="保存商品" class="btn btn-primary" />
            <?php  } ?>
            <a class="btn btn-default " href="<?php  echo webUrl('groups/goods')?>">返回列表</a>
        </div>
    </div>
    </form>
</div>
<script language='javascript'>
    goods_id = "<?php  echo $item['id'];?>";
    option_open = true;
    //是否开启了多规格
    more_spec = "<?php  echo $item['more_spec'];?>";
    group_goods_id = "<?php  echo $group_goods_id;?>";
    shop_goods_id = "<?php  echo $item['gid'];?>";
    require(['bootstrap'],function(){
        $('#myTab a').click(function (e) {
            e.preventDefault();
            $('#tab').val( $(this).attr('href'));
            $(this).tab('show');
        })
    });

    $(function(){
        $('#more').click(function(){
            option_open = $('#more').is(':checked');
            if( option_open ){
                get_spec(  );
            }else{
                $( '#more_spec' ).hide();
            }
        });

    });

    function get_spec(  )
    {
        if( more_spec != 1 ) {
            $.getJSON("<?php  echo webUrl('groups/goods/get_spec')?>", {
                goods_id: goods_id,
                group_goods_id: group_goods_id,
                shop_goods_id:shop_goods_id
            }, function (msg) {
                if (msg.status == 1) {
                    $('#spec_s').empty();
                    $.each(msg.result.data, function (k, v) {
                        $('#spec_s').append("<tr><input type='hidden' name='spec[" + k + "][specs]' value='" + v.specs + "'><input type='hidden' name='spec[" + k + "][id]' value=''><input type='hidden' name='spec[" + k + "][goods_option_id]' value='" + v.id + "'>\n" +
                            "                    <td>\n" +
                            "                        <input type=\"text\" class=\"form-control   valid \" name='spec[" + k + "][name]' value='" + v.title + "' aria-invalid=\"false\">\n" +
                            "                    </td>\n" +
                            "                    <td>\n" +
                            "                        <input type=\"text\" class=\"form-control   valid option_marketprice\" name='spec[" + k + "][marketprice]' value='" + v.marketprice + "' aria-invalid=\"false\">\n" +
                            "                    </td>" +
                            "                   <td>" +
                            "                       <input type=\"text\" class=\"form-control  valid option_single_price\" name=\"spec[" + k + "]['single_price']\" value='" + v.marketprice + "'>\n" +
                            "                   </td>" +
                            "                    <td class=\"type-4\">\n" +
                            "                        <input type=\"text\" class=\"form-control  option_price\" name='spec[" + k + "][price]' value=\"\">\n" +
                            "                    </td>\n" +
                            "                    <td class=\"type-4\">\n" +
                            "                        <input type=\"text\" class=\"form-control  option_stock\" name='spec[" + k + "][stock]' value='" + v.stock + "'>\n" +
                            "                    </td>\n" +
                            "                </tr>");
                    });

                } else {
                    //如果保存取消按钮开启状态
                    $('#more').attr('checked', false).next('div').removeClass('checked');
                    tip.msgbox.err(msg.result.message);
                    $( '#more_spec' ).hide();
                    return false;
                }

            });
        }

        $( '#more_spec' ).show();

    }


    $('form').submit(function(){
        if($(':radio[name=goodstype]:checked').val()=='0'){
            if($(':input[name=goodsid_text]').val()==''){
            return false;
            }
        }
        if($('select[name=cate]').val()==''){
            $('#myTab a[href="#tab_basic"]').tab('show');
            return false;
        }
        return true;
    });
    function toimgsrc(src) {
        if(typeof src != 'string') {
            return '';
        }
        if(src.indexOf('http://')==0 || src.indexOf('https://')==0 || src.indexOf('../addons')==0) {
            return src;
        }
        else if(src.indexOf('images/') == 0) {
            return "<?php  echo $_W['attachurl'];?>" +  src;
        }
    }
    function select_goods(o){
        goods_id = o.gid;
        var $thumb = '';
        /*商品缩略图*/
        if(o.thumb){
            $thumb += '<div class="multi-item"><img src="'+ toimgsrc(o.thumb)+'" ' +
                    'onerror="this.src=\'./resource/images/nopic.jpg\'; this.title=\'图片未找到.\'" class="img-responsive img-thumbnail">' +
                    '<input type="hidden" name="thumbs[0]" value="'+ o.thumb+'">' +
                    '<em class="close" title="删除这张图片" onclick="deleteMultiImage(this)">×<\/em></div>';
        }
        /*商品幻灯片*/
        if(o.thumb_url){
            $.each(o.thumb_url,function(index,value){
                var i = index++;
                $thumb += '<div class="multi-item"><img src="'+ toimgsrc(value)+'" ' +
                        'onerror="this.src=\'./resource/images/nopic.jpg\'; this.title=\'图片未找到.\'" class="img-responsive img-thumbnail">' +
                        '<input type="hidden" name="thumbs['+i+']" value="'+value+'">' +
                        '<em class="close" title="删除这张图片" onclick="deleteMultiImage(this)">×<\/em></div>';
            });
        }
        $(".multi-img-details").html($thumb);
        $(":input[name=gid]").val(o.gid);
        $(":input[name=total]").val(o.total);
        $(":input[name=description]").val(o.subtitle);
        $(":input[name=price]").val(o.marketprice);
        $(":input[name=goodssn]").val(o.goodssn);
        $(":input[name=productsn]").val(o.productsn);
        $("textarea[name=followtext]").val(o.followtip);
        $("textarea[name=followurl]").val(o.followurl);
        var ue = UE.getEditor('content', {
            autoHeight: false
        });
        ue.ready(function() {
            //设置编辑器的内容
            ue.setContent(o.content);
        });
    }

    function setCol( class_ ){
        var value = $( '.'+class_+'_all' ).val();
        $.each( $( '.'+class_ ) , function(k,v){
            $( v ).val( value );
        });
    }

</script>

    <?php  } ?>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>