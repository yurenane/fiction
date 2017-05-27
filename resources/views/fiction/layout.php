<!DOCTYPE html>
<html lang="zh-cmn-Hans">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no"/>
		<title>Fiction</title>
		<link rel="stylesheet" href="<?php echo WEUI_CSS; ?>weui.css">
		<link rel="stylesheet" href="<?php echo WEUI_CSS; ?>example.css">
		<script src="<?php echo JS_PATH; ?>jquery-1.8.3.min.js"></script>
		<style>
			.weui-tabbar img{
			opacity: 0.5;
			}	

		</style>
	</head>
	<body ontouchstart="">
		<div class="container" id="container" style="position:static;">
			<div class="page home js_show" style="position:static;">
				<?php echo $content; ?>
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
				<div class="weui-footer" style="margin-bottom: 60px;">
					<p class="weui-footer__text">@2017</p>
				</div>
			</div>
		</div>
		<?php if (!in_array($page_id, array('login', 'register', 'detail'))) { ?>
			<div class="weui-tabbar" style="position:fixed;left:0;bottom:0;">
				<a href="/" class="weui-tabbar__item <?php echo $page_id == 'index' ? ' weui-bar__item_on' : ''; ?>">
					<img src="<?php echo IMG_PATH; ?>notebook.png" alt="" class="weui-tabbar__icon">
					<p class="weui-tabbar__label">首页</p>
				</a>
				<a href="/search" class="weui-tabbar__item <?php echo $page_id == 'search' ? ' weui-bar__item_on' : ''; ?>">
					<img src="<?php echo IMG_PATH; ?>global.png" alt="" class="weui-tabbar__icon">
					<p class="weui-tabbar__label">搜索</p>
				</a>
				<a href="/user" class="weui-tabbar__item <?php echo $page_id == 'user' ? ' weui-bar__item_on' : ''; ?>">
					<img src="<?php echo IMG_PATH; ?>user.png" alt="" class="weui-tabbar__icon">
					<p class="weui-tabbar__label">我</p>
				</a>
			</div>
		<?php } ?>
		<?php if ($page_id == 'detail') { ?>
			<div class="weui-tabbar" style="position:fixed;">
				<a href="/user" class="weui-tabbar__item ">
					<img src="<?php echo IMG_PATH; ?>user.png" alt="" class="weui-tabbar__icon">
					<p class="weui-tabbar__label">我</p>
				</a>
				<a href="javascript:;" class="weui-tabbar__item" id="on">
					<img src="<?php echo IMG_PATH; ?>up.png" alt="" class="weui-tabbar__icon">
					<p class="weui-tabbar__label">上一章</p>
				</a>
				<a href="/novel/list/<?php echo  $info->list; ?>" class="weui-tabbar__item ">
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
		<?php } ?>
		<script>
			function send(status, text) {
				text ? $('#toast .weui-toast__content').text(text) : '';
				if (!status) {
					$('#toast .weui-icon-success-no-circle').hide();
					$('#toast p').css('margin-top', '50px');
				} else {
					$('#toast .weui-icon-success-no-circle').show();
				}
				$('#toast').show().css('opacity', 1);
				setTimeout(function() {
					$('#toast').hide().css('opacity', 0);
				}, 3000);
			}
			function loading(show) {
				show ? $('#loading').show().css('opacity', 1) : $('#loading').hide().css('opacity', 0);
			}
		</script>
	</body>
</html>