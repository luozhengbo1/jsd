<?php defined('IN_IA') or exit('Access Denied');?><div class="tab-pane" id="tab_style">
    <div class="panel panel-default">
        <div class="panel-heading">
            个性化设置
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">桌台图文封面</label>

                <div class="col-sm-9 col-xs-12">
                    <?php  echo tpl_form_field_image('table_cover', $setting['table_cover']);?>
                    <div class="help-block">建议尺寸：800 × 450</div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">桌台图文描述</label>

                <div class="col-sm-9">
                    <input type="text" name="table_desc" value="<?php  echo $setting['table_desc'];?>" class="form-control"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">首页列表颜色</label>

                <div class="col-sm-9">
                    <?php  if(!empty($setting['style_base'])) { ?>
                    <?php  echo tpl_form_field_color('style_base', $setting['style_base']);?>
                    <?php  } else { ?>
                    <?php  echo tpl_form_field_color('style_base', '#3190e8');?>
                    <?php  } ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">列表头部颜色1</label>

                <div class="col-sm-9">
                    <?php  if(!empty($setting['style_list_btn1'])) { ?>
                    <?php  echo tpl_form_field_color('style_list_btn1', $setting['style_list_btn1']);?>
                    <?php  } else { ?>
                    <?php  echo tpl_form_field_color('style_list_btn1', '#6a3f34');?>
                    <?php  } ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">列表头部颜色2</label>

                <div class="col-sm-9">
                    <?php  if(!empty($setting['style_list_btn2'])) { ?>
                    <?php  echo tpl_form_field_color('style_list_btn2', $setting['style_list_btn2']);?>
                    <?php  } else { ?>
                    <?php  echo tpl_form_field_color('style_list_btn2', '#a57664');?>
                    <?php  } ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">列表头部颜色3</label>

                <div class="col-sm-9">
                    <?php  if(!empty($setting['style_list_btn3'])) { ?>
                    <?php  echo tpl_form_field_color('style_list_btn3', $setting['style_list_btn3']);?>
                    <?php  } else { ?>
                    <?php  echo tpl_form_field_color('style_list_btn3', '#9995a3');?>
                    <?php  } ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">列表按钮颜色</label>

                <div class="col-sm-9">
                    <?php  if(!empty($setting['style_list_base'])) { ?>
                    <?php  echo tpl_form_field_color('style_list_base', $setting['style_list_base']);?>
                    <?php  } else { ?>
                    <?php  echo tpl_form_field_color('style_list_base', '#FE4F4E');?>
                    <?php  } ?>
                </div>
            </div>
        </div>
    </div>
</div>