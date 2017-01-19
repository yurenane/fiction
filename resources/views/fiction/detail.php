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
				<a href="<?php echo $info->on;?>" class="weui-footer__link">上一章</a>
				<a href="<?php echo $info->list;?>" class="weui-footer__link">目录</a>
				<a href="<?php echo $info->next;?>" class="weui-footer__link">下一章</a>
			</p>
			<p class="weui-footer__text">@2017</p>
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
<?php include_once('footer.php'); ?>