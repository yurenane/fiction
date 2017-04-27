<div id="toast" style="opacity: 0;display:none;">
	<div class="weui-mask_transparent"></div>
	<div class="weui-toast">
		<i class="weui-icon-success-no-circle weui-icon_toast"></i>
		<p class="weui-toast__content" >添加成功</p>
	</div>
</div>
<div id="loading" style="opacity: 0; display: none;">
	<div class="weui-mask_transparent"></div>
	<div class="weui-toast">
		<i class="weui-loading weui-icon_toast"></i>
		<p class="weui-toast__content">数据加载中</p>
	</div>
</div>
</div>
</div>
<?php if (!in_array($page['id'], array('login', 'register', 'detail'))) { ?>
	<div class="weui-tabbar" style="position:fixed;left:0;bottom:0;">
		<a href="/" class="weui-tabbar__item <?php echo $page['id'] == 'index' ? ' weui-bar__item_on' : ''; ?>">
			<img src="<?php echo IMG_PATH; ?>notebook.png" alt="" class="weui-tabbar__icon">
			<p class="weui-tabbar__label">首页</p>
		</a>
		<a href="/search" class="weui-tabbar__item <?php echo $page['id'] == 'search' ? ' weui-bar__item_on' : ''; ?>">
			<img src="<?php echo IMG_PATH; ?>global.png" alt="" class="weui-tabbar__icon">
			<p class="weui-tabbar__label">搜索</p>
		</a>
		<a href="/user" class="weui-tabbar__item <?php echo $page['id'] == 'user' ? ' weui-bar__item_on' : ''; ?>">
			<img src="<?php echo IMG_PATH; ?>user.png" alt="" class="weui-tabbar__icon">
			<p class="weui-tabbar__label">我</p>
		</a>
	</div>
<?php } ?>
<script>
	function send(status, text) {
		text ? $('#toast .weui-toast__content').text(text) : '';
		if (!status) {
			$('#toast .weui-icon_toast').removeClass('weui-icon-success-no-circle').addClass('weui-icon-warn').css('font-size','55px');
		}
		$('#toast').show().css('opacity', 1);
		setTimeout(function() {
			$('#toast').hide().css('opacity', 0);
		}, 2000);
	}
	function loading(show) {
		show ? $('#loading').show().css('opacity', 1) : $('#loading').hide().css('opacity', 0);
	}
</script>
</body>
</html>