<?php
$page = array(
  'id' => 'detail',
);

include_once('head.php');
?>
<?php if ($info) { ?>
	<div class="page__bd">
		<article class="weui-article">
			<h1><?php echo $info->title; ?></h1>
			<section style="font-size: 18px;">
				<?php echo $info->content; ?>
			</section>
		</article>
		<div class="weui-footer" style="margin-bottom: 60px;">
	<!--			<p class="weui-footer__links">
				<a href="javascript:;" class="weui-footer__link" id="on">上一章</a>
				<a href="/novel/<?php //echo $info->list;            ?>/<?php //echo $info->link;            ?>/" class="weui-footer__link" id="list">目录</a>
				<a href="javascript:;" class="weui-footer__link" id="next">下一章</a>
				<a href="javascript:;" class="weui-footer__link" id="cache" >缓存</a>
			</p>-->
			<p class="weui-footer__text">@2017</p>
		</div>
	</div>
	<div>
		<div class="weui-mask" id="mask" style="opacity: 0; display: none;"></div>
		<div class="weui-actionsheet" id="actionsheet">
			<div class="weui-actionsheet__menu">
				<div class="weui-actionsheet__cell" id="cache-next" >缓存后面1000章节</div>
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
<p style="position:fixed;width: 100%;height: 25px;left: 0;bottom: 0;background-color: #bdbdbd;opacity: 0.8;color: #000;font-size: 12px;line-height: 25px;display:none;" id="cache-info">
	数据缓存中.........             请勿刷新页面!
</p>
<div class="weui-tabbar" style="position:fixed;">
	<a href="/user" class="weui-tabbar__item ">
		<img src="<?php echo IMG_PATH; ?>user.png" alt="" class="weui-tabbar__icon">
		<p class="weui-tabbar__label">我</p>
	</a>
	<a href="javascript:;" class="weui-tabbar__item" id="no">
		<img src="<?php echo IMG_PATH; ?>up.png" alt="" class="weui-tabbar__icon">
		<p class="weui-tabbar__label">上一章</p>
	</a>
	<a href="/novel/<?php echo $info->list; ?>/<?php echo $info->link; ?>/" class="weui-tabbar__item ">
		<img src="<?php echo IMG_PATH; ?>adjustments.png" alt="" class="weui-tabbar__icon">
		<p class="weui-tabbar__label">目录</p>
	</a>
	<a href="javascript:;" class="weui-tabbar__item" id="next">
		<img src="<?php echo IMG_PATH; ?>lower.png" alt="" class="weui-tabbar__icon">
		<p class="weui-tabbar__label">下一章</p>
	</a>
	<a href="javascript:;" class="weui-tabbar__item" id="cache">
		<img src="<?php echo IMG_PATH; ?>lightbulb.png" alt="" class="weui-tabbar__icon">
		<p class="weui-tabbar__label">缓存</p>
	</a>
</div>
<script>
	$(function() {
		var on = '<?php echo $info->on; ?>',
				next = '<?php echo $info->next; ?>',
				link = '<?php echo $info->link; ?>',
				chapter_id = '<?php echo $info->chapter_id; ?>',
				novel_id = '<?php echo $info->novel_id; ?>',
				chapter = {};
		$('.weui-tabbar__item').on('click', function() {
			$(this).addClass('weui-bar__item_on').siblings('.weui-bar__item_on').removeClass('weui-bar__item_on');
		});
		// localStorage.clear();
		$('#on').click(function() {
			if (checkCache(on)) {
				window.location.href = '/novel/' + on + '/' + link + '/detail';
			} else {
				next = chapter_id;
				chapter_id = on;
				on = _id(on, false);
			}
		});
		$('#next').click(function() {
			if (checkCache(next)) {
				window.location.href = '/novel/' + next + '/' + link + '/detail';
			} else {
				on = chapter_id;
				chapter_id = next;
				next = _id(next, true);
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
			$('#cache-info').show();
			$('.weui-tabbar').hide();
			$.post('/ajax/chapter-list', {'nid': novel_id, 'id': cid, 'limit': 1000, 'p': 1}, function(result) {
				$('#cache-info').hide();
				$('.weui-tabbar').show();
				if (result.code == 1000) {
					var info = result.info, isOut = false;
					for (var i in info) {
						chapter[info[i].id] = {'title': info[i].title, 'content': info[i].content};
					}
					try {
						localStorage.setItem(novel_id, JSON.stringify(chapter));
					} catch (oException) {
						console.log(oException);
						if (oException.name == 'QuotaExceededError') {
							send(false,'数据获取失败，超出本地存储限额！');
							return false;
						}
					}
					send(true,'数据缓存成功');
				} else {
					send(false,'数据获取失败');
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
	})
</script>
<?php include_once('footer.php'); ?>