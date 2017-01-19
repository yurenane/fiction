<div id="toast" style="opacity: 0;display:none;">
	<div class="weui-mask_transparent"></div>
	<div class="weui-toast">
		<i class="weui-icon-success-no-circle weui-icon_toast"></i>
		<p class="weui-toast__content" >添加成功</p>
	</div>
</div>
<?php if (!in_array($page['id'], array('login', 'register'))) { ?>
	<div class="weui-tabbar" style="position:fixed;">
		<a href="/" class="weui-tabbar__item <?php echo $page['id'] == 'index' ? ' weui-bar__item_on' : ''; ?>">
			<img src="<?php echo WEUI_IMG; ?>icon_nav_button.png" alt="" class="weui-tabbar__icon">
			<p class="weui-tabbar__label">首页</p>
		</a>
		<a href="/search" class="weui-tabbar__item <?php echo $page['id'] == 'search' ? ' weui-bar__item_on' : ''; ?>">
			<img src="<?php echo WEUI_IMG; ?>icon_nav_search.png" alt="" class="weui-tabbar__icon">
			<p class="weui-tabbar__label">搜索</p>
		</a>
		<a href="/user" class="weui-tabbar__item <?php echo $page['id'] == 'user' ? ' weui-bar__item_on' : ''; ?>">
			<img src="<?php echo WEUI_IMG; ?>icon_nav_button.png" alt="" class="weui-tabbar__icon">
			<p class="weui-tabbar__label">我</p>
		</a>
	</div>
	</div>
	<script type="text/javascript">
		$(function() {
			$('.weui-tabbar__item').on('click', function() {
				$(this).addClass('weui-bar__item_on').siblings('.weui-bar__item_on').removeClass('weui-bar__item_on');
			});
		});
	</script>
<?php } ?>
</div>
<script>
	function send(text) {
		text ? $('#toast .weui-toast__content').text(text) : '';
		$('#toast').show().css('opacity', 1);
		setTimeout(function() {
			$('#toast').hide().css('opacity', 0);
		}, 2000);
	}
</script>
</body>
</html>