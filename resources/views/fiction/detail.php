<?php if ($info) { ?>
	<div class="page__bd">
		<article class="weui-article">
			<h1><?php echo $info->title; ?></h1>
			<section style="font-size: 18px;">
				<?php echo $info->content; ?>
			</section>
		</article>
	</div>
	<div>
		<div class="weui-mask" id="mask" style="opacity: 0; display: none;"></div>
		<div class="weui-actionsheet" id="actionsheet">
			<div class="weui-actionsheet__menu">
				<div class="weui-actionsheet__cell" id="cache-next" >缓存后面500章节<span style="font-size:12px;">(会清空之前所有数据)</span></div>
			</div>
			<div class="weui-actionsheet__action">
				<div class="weui-actionsheet__cell" id="actionsheetCancel">取消</div>
			</div>
		</div>
	</div>
<?php } else { ?>
	<div class="weui-msg">
		<div class="weui-msg__icon-area"><i class="weui-icon-warn weui-icon_msg"></i></div>
		<div class="weui-msg__text-area">
			<h2 class="weui-msg__title">操作失败</h2>
			<p class="weui-msg__desc">数据获取过程中失败，请刷新页面重新获取</p>
		</div>
		<div class="weui-msg__opr-area">
			<p class="weui-btn-area">
				<a href="javascript:location.reload();" class="weui-btn weui-btn_primary">刷新</a>
			</p>
		</div>
	</div>
<?php } ?>
<p style="position:fixed;width: 100%;height: 25px;left: 0;bottom: 0;background-color: rgba(0,0,0,0.8);color: #fff;font-size: 12px;line-height: 25px;display:none;" id="cache-info">
	数据缓存中.........             请勿刷新页面!
</p>
<p style="position:fixed;width: 100%;height: 25px;left: 0;bottom: 53px;background-color: rgba(0,0,0,0.8);color: #fff;font-size: 12px;line-height: 25px;display:none;" id="info">
</p>
<script>
	$(function() {
		var on = '<?php echo $info->on; ?>',
				next = '<?php echo $info->next; ?>',
				chapter_id = '<?php echo $info->chapter_id; ?>',
				novel_id = '<?php echo $info->novel_id; ?>',
				total = 0,
				chapter = {};
		updateRead();
		$('.weui-tabbar__item').on('click', function() {
			$(this).addClass('weui-bar__item_on').siblings('.weui-bar__item_on').removeClass('weui-bar__item_on');
		});
		// localStorage.clear();
		$('#on').click(function() {
			if (checkCache(on)) {
				window.location.href = '/novel/detail/' + on;
			} else {
				next = chapter_id;
				chapter_id = on;
				on = _id(on, false);
				updateRead();
			}
		});
		$('#next').click(function() {
			if (checkCache(next)) {
				window.location.href = '/novel/detail/' + next;
			} else {
				on = chapter_id;
				chapter_id = next;
				next = _id(next, true);
				updateRead();
			}
		});
		$('#cache').click(function() {
			$('#mask').show().css('opacity', 1);
			$('#actionsheet').addClass('weui-actionsheet_toggle');
		});
		$('#actionsheetCancel,#mask,#cache-next').click(function() {
			$('#mask').hide().css('opacity', 0);
			$('#actionsheet').removeClass('weui-actionsheet_toggle');
		});
		$('#cache-next').click(function() {
			getInfo(chapter_id);
		});
		function checkCache(id) {
			if (localStorage.getItem(novel_id)) {
				chapter = JSON.parse(localStorage.getItem(novel_id));
			}
			if (chapter && chapter[id]) {
				var num = 0;
				for (var i in chapter) {
					if (i != 'total') {
						if (i == id) {
							break;
						}
						num++;
					}
				}
				$('#info').html('正在使用缓存（剩余<span style="color: #ff0404;">' + (chapter['total']-num) + '</span>章）');
				$('#info').show();
				setHtml(chapter[id].title, chapter[id].content);
				javascript:scroll(0, 0);
				return false;
			}
			return true;
		}
		function setHtml(title, content) {
			$('.weui-article').find('h1').text(title);
			$('.weui-article').find('section').html(content);
		}
		function getInfo(cid) {
			localStorage.clear();
			$('#cache-info').show();
			$('.weui-tabbar').hide();
			$.post('/ajax/cache-chapter-list', {'nid': novel_id, 'cid': cid, 'total': 500}, function(result) {
				$('#cache-info').hide();
				$('.weui-tabbar').show();
				if (result.code == 1000) {
					var info = result.info, isOut = false;
					chapter['total'] = result.count;
					for (var i in info) {
						chapter[info[i].id] = {'title': info[i].title, 'content': info[i].content};
					}
					try {
						localStorage.setItem(novel_id, JSON.stringify(chapter));
					} catch (oException) {
						console.log(oException);
						if (oException.name == 'QuotaExceededError') {
							send(false, '数据获取失败，超出本地存储限额！');
							return false;
						}
					}
					send(true, '数据缓存成功');
				} else {
					send(false, result.error);
				}
			}, 'json');
		}
		function _id(id, add) {
			var _id = id.split('_');
			var num = add ? String(parseInt(_id[0]) + 1) : String(parseInt(_id[0]) - 1);
			for (var i = 1; i <= (5 - String(num).length); i++) {
				num = '0' + num;
			}
			return num + '_' + _id[1];
		}
		function updateRead() {
			$.post('/ajax/update-read', {'nid': novel_id, 'cid': chapter_id}, function(result) {
				if (result.code == 1000) {

				} else {
					send(false, result.error);
				}
			}, 'json');
		}
	})
</script>