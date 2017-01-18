<?php
$page = array(
  'id' => 'user',
);

include_once('head.php');
?>
<div class="page__hd">
	<h1 class="page__title">User</h1>
	<p class="page__desc"><?php echo $user_name?>的小说列表</p>
</div>
<div class="weui-panel__bd">
	<?php if($info){
		foreach ($info as $val){?>
	<a href="/novel/<?php echo $val->name; ?>/<?php echo base64_encode($val->link); ?>" class="weui-media-box weui-media-box_appmsg">
		<div class="weui-media-box__hd">
			<img class="weui-media-box__thumb" src="<?php echo IMG_PATH.'fiction/'.  $val->id.'.jpg' ?>" alt="">
		</div>
		<div class="weui-media-box__bd">
			<h4 class="weui-media-box__title"><?php echo $val->name; ?></h4>
			<p class="weui-media-box__desc"><?php echo $val->title; ?></p>
		</div>
	</a>
		<?php } }else{?>
	<div class="weui-loadmore weui-loadmore_line">
            <span class="weui-loadmore__tips">暂无数据</span>
         </div>
	<?php }?>
</div>
<?php include_once('footer.php'); ?>