<?php
$page=array(
  'id'=>'index',
);

include_once('head.php');
?>
<div class="page__hd">
	<h1 class="page__title">List</h1>
	<p class="page__desc">分类列表</p>
</div>
<div class="page__bd page__bd_spacing">
	<ul>
		<li class="">
			<div class="weui-flex js_category">
				<p class="weui-flex__item">表单</p>
			</div>
		</li>
		<li class="">
			<div class="weui-flex js_category">
				<p class="weui-flex__item">基础组件</p>
			</div>
		</li>
		<li class="">
			<div class="weui-flex js_category">
				<p class="weui-flex__item">操作反馈</p>
			</div>
		</li>
		<li class="">
			<div class="weui-flex js_category">
				<p class="weui-flex__item">导航相关</p>
			</div>
		</li>
		<li class="">
			<div class="weui-flex js_category">
				<p class="weui-flex__item">搜索相关</p>
			</div>
		</li>
		<li class="">
			<div class="weui-flex js_item" data-id="layers">
				<p class="weui-flex__item">层级规范</p>
			</div>
		</li>
	</ul>
</div>
<?php include_once('footer.php'); ?>