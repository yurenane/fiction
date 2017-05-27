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
	<div class="weui-cells searchbar-result" id="searchList" style="transform-origin: 0px 0px 0px; opacity: 1; transform: scale(1, 1);"">
	</div>
	<div class="weui-loadmore" style='margin-bottom: 80px;display:none;'>
		<i class="weui-loading"></i>
		<span class="weui-loadmore__tips">正在加载</span>
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
			if (event.keyCode == 13) {
				getData();
				event.preventDefault();
			}
		});
		function getData() {
			$('#searchInput').blur();
			$('.weui-loadmore').show();
			$.post('/search', {'title': $('#searchInput').val()}, function(result) {
				if (result.code == 1000) {
					setHtml(result.info);
					$('.weui-loadmore').hide();
					$('#searchList').show();
				} else {
					send(false, result.error);
					$('.weui-loadmore').hide();
				}
			}, 'json');
		}
		function setHtml(content) {
			var html = '<div class="weui-cell weui-cell_access" style="background-color: #ececec;font-size:14px;"><div class="weui-cell__bd weui-cell_primary"><p>书名</p></div>\
					<div class="weui-cell__bd weui-cell_primary"><p>作者</p></div><div class="weui-cell__bd weui-cell_primary"><p>最新章节</p></div></div><div class="weui-cells">';
			for (var i in content) {
				html +='<a class="weui-cell weui-cell_access" href="/novel/add/' + content[i].link + '" style="font-size:12px;">\
					<div class="weui-cell__bd">\
						<p>' + content[i].name + '</p>\
					</div>\
						<div class="weui-cell__bd">\
						<p>' + content[i].author + '</p>\
					</div>\
						<div class="weui-cell__bd">\
						<p>' + (content[i].new ? content[i].new :content[i].status) + '</p>\
					</div>\
					<div class="weui-cell__ft">\
					</div>\
				</a>';

			}
			html +='</div>';
			$('#searchList').append(html);
		}
	});
</script>