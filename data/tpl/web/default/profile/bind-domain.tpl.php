<?php defined('IN_IA') or exit('Access Denied');?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="alert alert-info">
	<p><i class="fa fa-exclamation-circle"></i> 绑定域名后，访问app端将使用绑定的域名，且绑定的域名<span class="color-red">只对app端生效</span>；</p>
	<p><i class="fa fa-exclamation-circle"></i> 如访问微站链接为<span class="color-gray"><?php  echo $_W['siteroot'];?>app/index.php?i=<?php  echo $_W['uniacid'];?>&c=home</span>，<br>&nbsp;&nbsp;&nbsp;&nbsp;绑定bang.baidu.com后，访问微站链接就成为<span class="color-gray">http://bang.baidu.com/app/index.php?i=<?php  echo $_W['uniacid'];?>&c=home</span></p>
	<p><i class="fa fa-exclamation-circle"></i> 绑定域名，只支持一级域名和二级域名；</p>
	<p><i class="fa fa-exclamation-circle"></i> 绑定域名后，请将公众平台的<span class="color-red">业务域名、js接口安全域名、网页授权域名都改为您绑定的域名，</span><br>&nbsp;&nbsp;&nbsp;&nbsp;如本说明中绑定bang.baidu.com，则改为bang.baidu.com；</p>
	<p><i class="fa fa-exclamation-circle"></i> 绑定域名后，服务器配置中的服务器地址（URL）不变，仍为：<?php  echo $_W['siteroot'];?>api.php?id=<?php  echo $_W['uniacid'];?></p>
	<p><i class="fa fa-exclamation-circle"></i> 还有最后很重要的一步：将绑定的域名解析到本服务器IP并绑定到系统网站目录。</p>
	<p><i class="fa fa-exclamation-circle"></i> 此功能不支持授权接入的公众号。</p>
</div>
<div id="js-bind-domain" ng-controller="bindDomainCtrl" ng-cloak>
	<table class="table we7-table table-hover table-form wechat-menu">
		<col width="140px " />
		<col />
		<col width="150px" />
		<tr><th class="text-left" colspan="3">域名绑定设置</th></tr>
		<tr>
			<td class="table-label">域名</td>
			<td ng-bind="account.setting.bind_domain.domain"></td>
			<td><div class="link-group"><a href="javascript:;" data-toggle="modal" data-target="#domain">修改</a><a href="<?php  echo url('profile/bind-domain/delete');?>" ng-if="account.setting.bind_domain.domain">删除</a></div></td>
		</tr>
	</table>
	<div class="modal fade" id="domain" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="we7-modal-dialog modal-dialog we7-form">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<div class="modal-title">修改绑定域名</div>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<input type="text" ng-model="middleAccount.bind_domain" class="form-control" placeholder="请输入要绑定的域名，以http://或https://开头" />
						<span class="help-block"></span>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal" ng-click="httpChange()">确定</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	angular.module('profileApp').value('config', {
		account: <?php echo !empty($_W['account']) ? json_encode($_W['account']) : 'null'?>,
		token: <?php echo !empty($_W['token']) ? json_encode($_W['token']) : 'null'?>,
		links: {
			'post': "<?php  echo url('profile/bind-domain')?>",
		},
	});
	angular.bootstrap($('#js-bind-domain'), ['profileApp']);
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>