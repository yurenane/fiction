<?php
$page = array(
  'id' => 'search',
);

include_once('head.php');
?>
<div class="page__hd">
	<h1 class="page__title">SearchBar</h1>
	<p class="page__desc">搜索栏</p>
</div>
<div class="page__bd">
	<div class="weui-search-bar" id="searchBar">
		<form class="weui-search-bar__form" >
			<div class="weui-search-bar__box">
				<i class="weui-icon-search"></i>
				<input type="search" class="weui-search-bar__input" id="searchInput" placeholder="搜索" required="">
				<a href="javascript:" class="weui-icon-clear" id="searchClear"></a>
			</div>
			<label class="weui-search-bar__label" id="searchText" style="transform-origin: 0px 0px 0px; opacity: 1; transform: scale(1, 1);">
				<i class="weui-icon-search"></i>
				<span>搜索</span>
			</label>
		</form>
		<a href="javascript:" class="weui-search-bar__cancel-btn" id="search">确认</a>
	</div>
	<div class="weui-cells searchbar-result weui-panel weui-panel__bd" id="searchList" style="display: none;margin-bottom: 60px;">
	</div>
</div>
<script type="text/javascript">
	$(function() {
		$('.weui-tabbar__item').on('click', function() {
			$(this).addClass('weui-bar__item_on').siblings('.weui-bar__item_on').removeClass('weui-bar__item_on');
		});
		function hideSearchResult() {
			 $('#searchList').hide();
			$('#searchInput').val('');
		}
		function cancelSearch() {
			$('#searchBar').removeClass('weui-search-bar_focusing');
			$('#searchText').show();
		}

		$('#searchText').on('click', function() {
			$('#searchBar').addClass('weui-search-bar_focusing');
			$('#searchInput').focus();
		});
		$('#searchInput').on('blur', function() {
			if (!this.value.length) {
				cancelSearch();
			}
		});
		$('#searchClear').on('click', function() {
			hideSearchResult();
			$('#searchInput').focus();
		});
		$('#search').bind('click', function() {
			getData();
		});
		$('#searchInput').bind('keydown', function(event) {
			if(event.keyCode==13){
				getData();
				event.preventDefault();
			}
		});
		function getData(){
			$('#searchInput').blur();
			loading(true);
			$.post('/search', {'title': $('#searchInput').val()}, function(result) {
				if (result.code == 1000) {
					setHtml(result.info);
					loading(false);
					$('#searchList').show();
				} else {
					send(false,result.error);
				}
			}, 'json');
		}
		function setHtml(content) {
			var html = '';
			if (content[0].img) {
				for (var i in content) {
					html += '<a href="/novel/'+ content[i]._name+'/' + content[i].link +'" class="weui-media-box weui-media-box_appmsg"><div class="weui-media-box__hd" style="width:auto;height:auto;">\n\
				<img style="width:100px;height:125px" class="weui-media-box__thumb" src="' + content[i].img + '" alt="">\n\
				</div><div class="weui-media-box__bd"><h4 class="weui-media-box__title">' + content[i].name + '</h4><p class="weui-media-box__desc">' + content[i].title + '</p><ul class="weui-media-box__info">\n\
				<li class="weui-media-box__info__meta" style="margin:0;">作者：' + content[i].author + '</li><li class="weui-media-box__info__meta" style="margin:0;">更新时间：' + content[i].utime + '</li>\n\
				<li class="weui-media-box__info__meta weui-media-box__info__meta_extra" style="margin:0;">' + (content[i].new?'最新章节：'+content[i].new:'更新状态：'+content[i].status)  + '</li></ul></div></a>';
				}
			}else{
				for (var i in content) {
					html += '<a href="/novel/'+ content[i]._name+'/' + content[i].link + '" class="weui-media-box weui-media-box_appmsg"><div class="weui-media-box__bd">\n\
				<h4 class="weui-media-box__title">' + content[i].name + '</h4><ul class="weui-media-box__info"><li class="weui-media-box__info__meta" style="margin:0;">\n\
				作者：' + content[i].author + '</li><li class="weui-media-box__info__meta" style="margin:0;">更新时间：' + content[i].utime + '</li>\n\
				<li class="weui-media-box__info__meta weui-media-box__info__meta_extra" style="margin:0;">' +(content[i].new?'最新章节：'+content[i].new:'更新状态：'+content[i].status) + '</li></ul></div></a>';
				}
			}
			$('#searchList').append(html);
		}
	});
</script>
<?php include_once('footer.php'); ?>