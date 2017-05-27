<?php if ($info) { ?>
	<div class="weui-panel weui-panel_access">
		<div class="weui-panel__bd">
			<a href="javascript:;" class="weui-media-box weui-media-box_appmsg">
				<div class="weui-media-box__hd" style="width:auto;height:auto;">
					<img style="width:100px;height:125px;" class="weui-media-box__thumb" src="<?php echo  $info->img_url; ?>" alt="">
				</div>
				<div class="weui-media-box__bd">
					<h4 class="weui-media-box__title"><?php echo $info->name; ?></h4>
					<p class="weui-media-box__desc"><?php echo $info->title; ?></p>
					<ul class="weui-media-box__info">
						<li class="weui-media-box__info__meta" style="margin:0;">作者：<?php echo $info->author; ?></li>
						<li class="weui-media-box__info__meta" style="margin:0;">更新时间：<?php echo date('Y-m-d', $info->utime); ?></li>
						<li class="weui-media-box__info__meta weui-media-box__info__meta_extra" style="margin:0;"><?php echo $info->new ? '最新章节：' . $info->new : ''; ?></li>
					</ul>
				</div>
			</a>
		</div>
		<div class="weui-panel__bd">
			<div class="weui-flex">
				<div class="weui-flex__item">
					<div class="placeholder">
						<a href="/novel/detail/<?php echo $cid?$cid:'00000_'.$info->id; ?>" class="weui-cell_link"><?php echo $cid?'继续阅读':'开始阅读';  ?></a> 
					</div>
				</div>
				<div class="weui-flex__item">
					<div class="placeholder">
						<a href="javascript:void(0);" class="weui-cell_link" id="sort">反序</a> 
					</div>
				</div>
				<div class="weui-flex__item">
					<div class="placeholder">
						<a href="javascript:void(0);" class="weui-cell_link" id="add">收藏</a> 
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="page__bd">
		<div class="weui-cells" id='list'>
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
		var id = '<?php echo $info->id; ?>', p = 1, isOk = false,isWork=false,sort='desc';
		getList();
		$('#add').click(function() {
			$.post('/ajax/collect', {'id': id}, function(result) {
				if (result.code == 1000) {
					send(true);
				} else {
					send(false,result.error);
				}
			}, 'json');
		});
		$('#sort').toggle(function(){
				sort='asc';	
				$('#list').html('');
				p=1;
				getList();
			},function(){
				sort='desc';	
				$('#list').html('');
				p=1;
				getList();
			});
		$(window).scroll(function() {
			viewH = $(this).height();
			contentH = $(document).height();
			scrollTop = $(this).scrollTop();
			if (contentH && (contentH - viewH - scrollTop <= 500)) {
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
			$.post('/ajax/chapter-list', {'nid': id, 'p': p,'limit':50,'sort':sort}, function(result) {
				$('.weui-loadmore').hide();
				if (result.code == 1000) {
					p++;
					setHtml(result.info);
				} else {
					isOk = true;
					send(false,'已经没有数据了');
				}
				isWork=false;
			}, 'json');
		}
		function setHtml(content) {
			var html = '';
			for (var i in content) {
				html += '<a class="weui-cell weui-cell_access" href="/novel/detail/' + content[i].id +'"><div class="weui-cell__bd"><p>' + content[i].title + '</p></div><div class="weui-cell__ft"></div></a>';
			}
			$('#list').append(html);
		}
	});
</script>