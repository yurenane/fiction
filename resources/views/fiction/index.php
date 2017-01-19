<?php
$page=array(
  'id'=>'index',
);

include_once('head.php');
?>
<div class="page__hd">
	<h1 class="page__title">List</h1>
	<p class="page__desc">小说列表</p>
</div>
<div class="weui-panel__bd">
	<?php if($info){
		foreach ($info as $val){?>
	<a href="/novel/<?php echo $val->name; ?>/<?php echo base64_encode($val->link); ?>" class="weui-media-box weui-media-box_appmsg">
		<div class="weui-media-box__hd" style="width:auto;height:auto;">
			<img style="width:100px;height:125px;" class="weui-media-box__thumb" src="<?php echo IMG_PATH.'fiction/'. $val->id.'.jpg' ?>" alt="">
		</div>
		<div class="weui-media-box__bd">
			<h4 class="weui-media-box__title"><?php echo  $val->name; ?></h4>
			<p class="weui-media-box__desc"><?php echo  $val->title; ?></p>
			<ul class="weui-media-box__info">
				<li class="weui-media-box__info__meta" style="margin:0;">作者：<?php echo $val->author; ?></li>
				<li class="weui-media-box__info__meta" style="margin:0;">更新时间：<?php echo  date('Y-m-d',$val->utime); ?></li>
				<li class="weui-media-box__info__meta weui-media-box__info__meta_extra" style="margin:0;"><?php echo $val->new?'最新章节：'. $val->new:'更新状态：'. $val->status; ?></li>
			</ul>
		</div>
	</a>
		<?php } }else{?>
	<div class="weui-loadmore weui-loadmore_line">
            <span class="weui-loadmore__tips">暂无数据</span>
         </div>
	<?php }?>
</div>
<?php include_once('footer.php'); ?>