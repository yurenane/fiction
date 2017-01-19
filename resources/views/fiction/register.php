<?php
$page=array(
  'id'=>'register',
);

include_once('head.php');
?>
<div class="page__hd">
	<h1 class="page__title">Register</h1>
	<p class="page__desc">注册</p>
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
	<div class="weui-cells__title">输入密码</div>
	<div class="weui-cells">
		<div class="weui-cell">
			<div class="weui-cell__bd">
				<input class="weui-input" type="password" id="pwd" placeholder="输入密码">
			</div>
		</div>
	</div>
	<div class="weui-cells__title">确认密码</div>
	<div class="weui-cells">
		<div class="weui-cell">
			<div class="weui-cell__bd">
				<input class="weui-input" type="password" id="repwd" placeholder="确认密码">
			</div>
		</div>
	</div>
	<label for="weuiAgree" class="weui-agree">
		<span class="weui-agree__text">
			前往<a href="/login">登录</a>
		</span>
	</label>
	<div class="weui-btn-area">
		<a class="weui-btn weui-btn_primary" href="javascript:" id="register">注册</a>
	</div>
</div>
<script>
$(function(){
	$('#register').click(function(){
		var name=$('#name').val();
		var pwd=$('#pwd').val();
		var repwd=$('#repwd').val();
		if(!name||!pwd||!repwd){
			send('信息不能为空');
			return false;
		}
		if(repwd!=pwd){
			send('两次密码输入不一致');
			return false;
		}
		$.post('/register',{'name':name,'pwd':pwd},function(result){
			if(result.code==1000){
				send('注册成功');
				setTimeout(function(){
					window.location.href='/login';
				},2000);
			}else{
				alert(result.error);
			}
		},'json');
	});
});
</script>
<?php include_once('footer.php'); ?>