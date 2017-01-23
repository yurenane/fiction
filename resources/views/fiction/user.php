<?php
$page = array(
  'id' => 'user',
);

include_once('head.php');
?>
<div class="page__hd">
	<h1 class="page__title">User</h1>
	<p class="page__desc"><?php echo $info->name; ?>的小说列表</p>
</div>
<div class="weui-panel__bd" style="margin-bottom: 60px;">
	<div id="list">
	</div>
	<div class="weui-loadmore" style='margin-bottom: 80px;display:none;'>
		<i class="weui-loading"></i>
		<span class="weui-loadmore__tips">正在加载</span>
	</div>
</div>
<div>
	<div class="weui-mask" id="mask" style="opacity: 0; display: none;"></div>
	<div class="weui-actionsheet" id="actionsheet">
		<div class="weui-actionsheet__menu">
			<div class="weui-actionsheet__cell" id="cache-all" >缓存全本</div>
			<div class="weui-actionsheet__cell" id="clear-cache" >清除缓存</div>
			<div class="weui-actionsheet__cell" id="delete" >删除小说</div>
		</div>
		<div class="weui-actionsheet__action">
			<div class="weui-actionsheet__cell" id="actionsheetCancel">取消</div>
		</div>
	</div>
</div>
<div class="js_dialog" id="dialog" style="opacity: 0;display:none">
	<div class="weui-mask"></div>
	<div class="weui-dialog">
		<div class="weui-dialog__hd"><strong class="weui-dialog__title">提示</strong></div>
		<div class="weui-dialog__bd">确认要删除该本小说？</div>
		<div class="weui-dialog__ft">
			<a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_default">取消</a>
			<a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_primary">确认</a>
		</div>
	</div>
</div>
<script>
	$(function() {
		var p = 1, isOk = false, isWork = false, img = '<?php echo IMG_PATH; ?>', id = '<?php echo $info->nlist; ?>', chapter = {}, novel_id = '';
		getList();
		$(document).on('click', '.weui-cell_link', function() {
			novel_id = $(this).attr('rel');
			$('#mask').show().css('opacity', 1);
			$('#actionsheet').addClass('weui-actionsheet_toggle');
		});
		$('#actionsheetCancel,#mask,#cache-all,#clear-cache,#delete').click(function() {
			$('#mask').hide().css('opacity', 0);
			$('#actionsheet').removeClass('weui-actionsheet_toggle');
		});
		$('#cache-all').click(function() {
			getInfo(1);
		});
		$('#clear-cache').click(function() {
			localStorage.removeItem(novel_id);
		});
		$('#delete').click(function(){
			$('#dialog').show().css('opacity', 1);
		});
		$('.weui-dialog__btn_default').click(function(){
			$('#dialog').hide().css('opacity', 0);
		});
		$('.weui-dialog__btn_primary').click(function(){
			$('#dialog').hide().css('opacity', 0);
			$.post('/ajax/delete-collect',{'id':novel_id},function(result){
				if(result.code==1000){
					send('删除成功');
					$('#'+novel_id).closest('.weui-panel_access').remove();
				}else{
					send(result.error);
				}
			},'json');
		});
		$(window).scroll(function() {
			viewH = $(this).height();
			contentH = $(document).height();
			scrollTop = $(this).scrollTop();
			if (contentH && (contentH - viewH - scrollTop <= 200)) {
				getList();
			}
		});
		function getList() {
			if (isOk) {
				return false;
			}
			$('.weui-loadmore').show();
			if (isWork) {
				return false;
			}
			isWork = true;
			$.post('/ajax/novel-list', {'id': id, 'p': p}, function(result) {
				$('.weui-loadmore').hide();
				if (result.code == 1000) {
					p++;
					setHtml(result.info);
					update();
				} else {
					if (!$('#list').html()) {
						$('#list').html('<div class="weui-loadmore weui-loadmore_line"><span class="weui-loadmore__tips">暂无数据</span></div>');
					} else {
						send('已经没有数据了');
					}
					isOk = true;
				}
				isWork = false;
			}, 'json');
		}
		function setHtml(content) {
			var html = '';
			for (var i in content) {
				html += '<div class="weui-panel weui-panel_access"><div class="weui-panel__bd"><a href="/novel/' + content[i].id + '/' + content[i].link + '" id="' + content[i].id + '" data-url="' + content[i].link + '" \n\
				class="weui-media-box weui-media-box_appmsg"><div class="weui-media-box__hd" style="width:auto;height:auto;"><img style="width:60px;height:85px;" class="weui-media-box__thumb" src="' + img + 'fiction/' + content[i].id + '.jpg" alt="">\n\
				</div><div class="weui-media-box__bd"><h4 class="weui-media-box__title">' + content[i].name + '<span class="weui-badge" style="margin-left: 5px;display:none;">更新</span></h4><p class="weui-media-box__desc">' + content[i].title + '</p><ul class="weui-media-box__info">\n\
				<li class="weui-media-box__info__meta" style="margin:0;">作者：' + content[i].author + '</li><li class="weui-media-box__info__meta" style="margin:0;">更新时间：' + content[i].utime + '</li>\n\
				<li class="weui-media-box__info__meta weui-media-box__info__meta_extra" style="margin:0;" id="new-' + content[i].id + '">' + (content[i].new ? '最新章节：' + content[i].new : '更新状态：' + content[i].status) + '</li></ul></div></a>\n\
				</div><div class="weui-panel__ft"><a href="javascript:void(0);" class="weui-cell weui-cell_access weui-cell_link" rel="' + content[i].id + '"><div class="weui-cell__bd">查看更多'+(localStorage.getItem(content[i].id)?'<span style="font-size:12px;">(已缓存)</span>':'')+'</div><span class="weui-cell__ft"></span></a></div></div>';
			}
			$('#list').append(html);
		}
		function update() {
			$('#list .weui-panel__bd a').each(function() {
				getNew($(this).attr('id'), $(this).attr('data-url'));
				updateNovel($(this).attr('id'));
			});
		}
		function getNew(id, link) {
			if (!id && !link) {
				return false;
			}
			$.post('ajax/update-list', {'id': id, 'link': link}, function(result) {
				if (result.code == 1000) {
					$('#new-' + id).text('最新章节：' + result.info);
					$('#' + id).find('.weui-media-box__title span').show();
				} else {

				}
			}, 'json');
		}
		function updateNovel(id) {
			$.post('ajax/update-novel', {'id': id}, function(result) {
			}, 'json');
		}
		function getInfo(p) {
			$.post('/ajax/chapter-list', {'id': novel_id, 'p': p}, function(result) {
				if (result.code == 1000) {
					var info = result.info;
					for (var i in info) {
						chapter[info[i].id] = {'title': info[i].title, 'content': info[i].content};
					}
					getInfo(novel_id, (p + 1));
					localStorage.setItem(novel_id, JSON.stringify(chapter));
				}
			}, 'json');
		}
	})
</script>
<?php include_once('footer.php'); ?>