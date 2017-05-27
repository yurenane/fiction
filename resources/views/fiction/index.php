<div class="page__hd">
	<h1 class="page__title"><img src="<?php echo IMG_PATH;?>logo.png" height="21px" /></h1>
	<p class="page__desc">小说列表</p>
</div>
<div class="weui-panel__bd" style="margin-bottom: 60px;">
	<div id="list">

	</div>
	<div class="weui-loadmore" style='margin-bottom: 80px;display:none;'>
		<i class="weui-loading"></i>
		<span class="weui-loadmore__tips">正在加载</span>
	</div>
</div>
<script>
	$(function() {
		var p = 1, isOk = false, isWork = false, img = '<?php echo IMG_PATH; ?>';
		getList();
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
			$.post('/ajax/novel-list', {'p': p}, function(result) {
				$('.weui-loadmore').hide();
				if (result.code == 1000) {
					p++;
					setHtml(result.info);
				} else {
					if (!$('#list').html()) {
						$('#list').html('<div class="weui-loadmore weui-loadmore_line"><span class="weui-loadmore__tips">暂无数据</span></div>');
					} else {
						send(false,'已经没有数据了');
					}
					isOk = true;
				}
				isWork = false;
			}, 'json');
		}
		function setHtml(content) {
			var html = '';
			for (var i in content) {
				getNew(content[i].id);
				html += '<a href="' + content[i].url + '" class="weui-media-box weui-media-box_appmsg"><div class="weui-media-box__hd" style="width:auto;height:auto;">\n\
				<img style="width:60px;height:85px;" class="weui-media-box__thumb" src="'+ content[i].img_url + '" alt=""></div><div class="weui-media-box__bd">\n\
				<h4 class="weui-media-box__title">' + content[i].name + '</h4><p class="weui-media-box__desc">' + content[i].title + '</p><ul class="weui-media-box__info">\n\
				<li class="weui-media-box__info__meta" style="margin:0;width: 100%;">' + (content[i].new ? '最新章节：' + content[i].new : '') + '</li></ul></div></a>';
			}
			$('#list').append(html);
		}
		function getNew(id) {
			$.post('ajax/update-list', {'id': id}, function(result) {
				if (result.code == 1000) {
					$('#new-' + id).text('最新章节：' + result.info);
					$('#' + id).find('.weui-media-box__title span').show();
				} else {

				}
			}, 'json');
		}
	})
</script>