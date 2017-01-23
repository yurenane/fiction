<?php
$page = array(
  'id' => 'list',
);

include_once('head.php');
?>
<?php if ($info['info']) { ?>
	<div class="weui-panel weui-panel_access">
		<div class="weui-panel__bd">
			<a href="javascript:;" class="weui-media-box weui-media-box_appmsg">
				<div class="weui-media-box__hd" style="width:auto;height:auto;">
					<img style="width:100px;height:125px;" class="weui-media-box__thumb" src="<?php echo IMG_PATH . 'fiction/' . $info['info']->id . '.jpg' ?>" alt="">
				</div>
				<div class="weui-media-box__bd">
					<h4 class="weui-media-box__title"><?php echo $info['info']->name; ?></h4>
					<p class="weui-media-box__desc"><?php echo $info['info']->title; ?></p>
					<ul class="weui-media-box__info">
						<li class="weui-media-box__info__meta" style="margin:0;">作者：<?php echo $info['info']->author; ?></li>
						<li class="weui-media-box__info__meta" style="margin:0;">更新时间：<?php echo date('Y-m-d', $info['info']->utime); ?></li>
						<li class="weui-media-box__info__meta weui-media-box__info__meta_extra" style="margin:0;"><?php echo $info['info']->new ? '最新章节：' . $info['info']->new : '更新状态：' . $info['info']->status; ?></li>
					</ul>
				</div>
			</a>
		</div>
		<div class="weui-panel__ft">
			<a href="javascript:void(0);" class="weui-cell weui-cell_access weui-cell_link">
				<div class="weui-cell__bd" style="text-align: right;" id="add">收藏</div>
			</a>    
		</div>
	</div>
	<div class="page__bd">
		<div class="weui-cells" id='list'>
			<?php foreach ($info['list'] as $val) { ?>
				<a class="weui-cell weui-cell_access" href="/novel/<?php echo $val->id; ?>/<?php echo $info['link']; ?>/detail">
					<div class="weui-cell__bd">
						<p><?php echo $val->title; ?></p>
					</div>
					<div class="weui-cell__ft"></div>
				</a>
			<?php } ?>
		</div>
		<div class="weui-loadmore" style='margin-bottom: 80px;display:none;' >
			<i class="weui-loading"></i>
			<span class="weui-loadmore__tips">正在加载</span>
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
		var id = '<?php echo $info['info']->id; ?>', p = 2, isOk = false,isWork=false;
		getList();
		$('#add').click(function() {
			$.post('/ajax/collect', {'id': id}, function(result) {
				if (result.code == 1000) {
					send();
				} else {
					alert(result.error);
				}
			}, 'json');
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
			if(isWork){
				return false;
			}
			isWork=true;
			$.post('/ajax/chapter-list', {'id': id, 'p': p}, function(result) {
				$('.weui-loadmore').hide();
				if (result.code == 1000) {
					p++;
					setHtml(result.info);
				} else {
					isOk = true;
					send('已经没有数据了');
				}
				isWork=false;
			}, 'json');
		}
		function setHtml(content) {
			var html = '';
			for (var i in content) {
				html += '<a class="weui-cell weui-cell_access" href="/novel/' + content[i].id + '/' + content[i].link + '/detail"><div class="weui-cell__bd"><p>' + content[i].title + '</p></div><div class="weui-cell__ft"></div></a>';
			}
			$('#list').append(html);
		}
	});
</script>
<?php include_once('footer.php'); ?>