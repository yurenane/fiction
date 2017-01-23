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
			<p class="weui-footer__links">
				<a href="javascript:;" class="weui-footer__link" id="on">上一章</a>
				<a href="/novel/<?php echo $info->list; ?>/<?php echo $info->link; ?>/" class="weui-footer__link" id="list">目录</a>
				<a href="javascript:;" class="weui-footer__link" id="next">下一章</a>
				<a href="javascript:;" class="weui-footer__link" id="cache" >缓存</a>
			</p>
			<p class="weui-footer__text">@2017</p>
		</div>
	</div>
	<div>
		<div class="weui-mask" id="mask" style="opacity: 0; display: none;"></div>
		<div class="weui-actionsheet" id="actionsheet">
			<div class="weui-actionsheet__menu">
				<div class="weui-actionsheet__cell" id="cache-next" >缓存后面章节</div>
				<div class="weui-actionsheet__cell" id="cache-all" >缓存全本</div>
			</div>
			<div class="weui-actionsheet__action">
				<div class="weui-actionsheet__cell" id="actionsheetCancel">取消</div>
			</div>
		</div>
	</div>
<?php } else {
	?>
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
<script>
	$(function() {
		var on = '<?php echo $info->on; ?>',
				next = '<?php echo $info->next; ?>',
				link = '<?php echo $info->link; ?>',
				chapter_id = '<?php echo $info->chapter_id; ?>',
				novel_id = '<?php echo $info->novel_id; ?>',
				p = 1,
				chapter = {};
//		localStorage.setItem(novel_id, '{}');
		console.log(JSON.parse(localStorage.getItem(novel_id)));
		$('#on').click(function() {
			if (checkCache(on)) {
				window.location.href='/novel/'+on+'/'+link+'/detail';
			} else {
				next = chapter_id;
				chapter_id = on;
				on = _id(on, false);
			}
		});
		$('#next').click(function() {
			if (checkCache(next)) {
				window.location.href='/novel/'+next+'/'+link+'/detail';
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
		$('#actionsheetCancel,#mask,#cache-next,#cache-all').click(function() {
			$('#mask').hide().css('opacity', 0);
			$('#actionsheet').removeClass('weui-actionsheet_toggle');
		});
		$('#cache-next').click(function() {
			getInfo(false);
		});
		$('#cache-all').click(function() {
			getInfo(true);
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
		function getInfo(all) {
			$.post('/ajax/chapter-list', {'id': novel_id, 'p': p}, function(result) {
				if (result.code == 1000) {
					var info = result.info, isOut = false;
					for (var i in info) {
						chapter[info[i].id] = {'title': info[i].title, 'content': info[i].content};
						if (!all && (info[i].id == chapter_id)) {
							isOut = true;
							break;
						}
					}
					if (!isOut) {
						p++;
						getInfo(all);
					}
					localStorage.setItem(novel_id, JSON.stringify(chapter));
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