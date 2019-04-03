define(['core', 'tpl'], function (core, tpl) {

	var modal = {
		page: 1,
		level: '',
        loaded: false
	};
	modal.init = function () {
        if(modal.loaded) {
            return;
        }
        modal.loaded = true;

        $('#container').empty();
		$('.fui-content').infinite({
			onLoading: function () {
				modal.getList();
			}
		});
		
		if (modal.page == 1)
		{
            if ($(".notice-item").length <= 0) {
                $("#container").html('');
                modal.getList();
            }
		}
	};
	modal.loading = function () {
		modal.page++;
	};
	modal.getList = function () {
		core.json('shop/notice/get_list', {page: modal.page, level: modal.level}, function (ret) {
			var result = ret.result;
			if(result.total<=0){
				$('.content-empty').show();
				$('.fui-content').infinite('stop');
			} else{
				$('.content-empty').hide();
				$('.fui-content').infinite('init');
				if(result.list.length<=0 || result.list.length<result.pagesize){
					$('.fui-content').infinite('stop');
				}
			}
			core.tpl('#container', 'tpl_shop_notice', result, modal.page > 1);
            if($('.notice-item').length < result.total) {
                modal.page++;
            }
		});
	};
	return modal;
});