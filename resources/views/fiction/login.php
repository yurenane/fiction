<?php
$page = array(
  'id' => 'login',
);

include_once('head.php');
?>
<div class="page__hd">
	<h1 class="page__title">login</h1>
	<p class="page__desc">登录</p>
</div>
<div class="page__bd ">
	<div class="weui-cells__title">用户名</div>
	<div class="weui-cells">
		<div class="weui-cell">
			<div class="weui-cell__bd">
				<input class="weui-input" type="text" id="name" placeholder="用户名">
			</div>
		</div>
	</div>
	<div class="weui-cells__title">密码</div>
	<div class="weui-cells">
		<div class="weui-cell">
			<div class="weui-cell__bd">
				<input class="weui-input" type="password" id="pwd" placeholder="密码">
			</div>
		</div>
	</div>
	<label for="weuiAgree" class="weui-agree">
		<span class="weui-agree__text">
			前往<a href="/register">注册</a>
		</span>
	</label>
	<div class="weui-btn-area">
		<a class="weui-btn weui-btn_primary" href="javascript:" id="showTooltips">登录</a>
	</div>
</div>
<script>
$(function(){
	$('#showTooltips').click(function(){
		var name=$('#name').val();
		var pwd=$('#pwd').val();
		if(!name||!pwd){
			send('信息不能为空');
			return false;
		}
		$.post('/login',{'name':name,'pwd':pwd},function(result){
			if(result.code==1000){
				send('登录成功');
				setTimeout(function(){
					window.location.href='/user';
				},1000);
			}else{
				alert(result.error);
			}
		},'json');
	});
});
</script>
<?php include_once('footer.php'); ?>