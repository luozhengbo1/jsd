<?php defined('IN_IA') or exit('Access Denied');?><div class="form-group">
    <label class="col-lg control-label">商品简介</label>
    <div class="col-sm-9 col-xs-12">
        <?php if( ce('groups.goods' ,$item) ) { ?>
        <?php  echo tpl_ueditor('content',$item['content'],array('height'=>'200'))?>
        <?php  } else { ?>
        <textarea id='detail' name='content' style='display:none;'><?php  echo $item['content'];?></textarea>
        <a href="javascript:preview_html('#detail')" class="btn btn-default">查看内容</a>
        <?php  } ?>
    </div>
</div>
