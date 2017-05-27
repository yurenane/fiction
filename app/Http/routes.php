<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | This route group applies the "web" middleware group to every route
  | it contains. The "web" middleware group is defined in your HTTP
  | kernel and includes session state, CSRF protection, and more.
  |
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\PrintCss;
//use phpQuery;
use App\Curl;
use App\Novel;
use App\Chapter;
use App\Video;

Route::get('/test', function() {
//		$content=phpQuery::newDocumentFile('http://www.jianqiang.win');
//		var_dump(file_put_contents('1.mp4', file_get_contents('http://mvvideo1.meitudata.com/58866171a65918300.mp4')));
			view()->share('data', [1, 2, 3]);
			return view('test');
		});
Route::get('/weui', function() {
			return view('weui');
		});
//===========
//小说阅读
//===========
Route::group(['middleware' => ['login']], function() {
			/**
			 * 首页
			 * ======
			 * @author 简强
			 * @version 17.1.12
			 */
			Route::get('/', function() {
						return view('fiction.layout', array(
						  'content' => view()->make('fiction.index')->render(),
						  'page_id' => 'index',
						));
					});
			/**
			 * 搜索页面
			 * ======
			 * @author 简强
			 * @version 17.1.12
			 */
			Route::get('/search', function() {
						return view('fiction.layout', array(
						  'content' => view()->make('fiction.search')->render(),
						  'page_id' => 'search',
						));
					});
			/**
			 * 用户小说列表页面
			 * ======
			 * @author 简强
			 * @version 17.1.12
			 */
			Route::get('/user', function() {
						$user_info = session('user');
						view()->share('info', $user_info);
						return view('fiction.layout', array(
						  'content' => view()->make('fiction.user')->render(),
						  'page_id' => 'user',
						));
					});
			/**
			 * 登录页面
			 * ======
			 * @author 简强
			 * @version 17.1.12
			 */
			Route::get('/login', function() {
						return view('fiction.layout', array(
						  'content' => view()->make('fiction.login')->render(),
						  'page_id' => 'login',
						));
					});
			//
			Route::post('/login', function(Request $request) {
						$post = $request->all();
						$info = DB::table('user')
								->where('name', $post['name'])
								->where('password', md5($post['pwd']))
								->first();
						if ($info) {
							setcookie('user', $info->id, time() + 60 * 60 * 24);
							$request->session()->set('user', $info);
							echo json_encode(array('code' => 1000, 'info' => '登录成功'));
						} else {
							echo json_encode(array('code' => 1001, 'error' => '登录失败'));
						}
					});
			/**
			 * 注册页面
			 * ======
			 * @author 简强
			 * @version 17.1.12
			 */
			Route::get('/register', function() {
						return view('fiction.layout', array(
						  'content' => view()->make('fiction.register')->render(),
						  'page_id' => 'register',
						));
					});
			Route::post('/register', function(Request $request) {
						$post = $request->all();
						$info = DB::table('user')
								->where('name', $post['name'])
								->first();
						if ($info) {
							echo json_encode(array('code' => 1001, 'error' => '用户名重复'));
							exit;
						}
						DB::table('user')->insert(['id' => uniqid(), 'name' => $post['name'], 'password' => md5($post['pwd']), 'ctime' => time()]);
						echo json_encode(array('code' => 1000, 'info' => '注册成功'));
					});
			/**
			 * 小说列表
			 * ======
			 * @author 简强
			 * @version 17.5.26
			 */
			Route::get('/novel/list/{id}', function($id) {
						if ($id) {
							$novel = new Novel();
							$read_log = new \App\ReadLog();
							$result = $novel->getInfo($id);
							//小说封面
							$img_url='statics/images/fiction/';
							if(is_file($img_url.$result->id.'.jpg')){
								$result->img_url='/'.$img_url.$result->id.'.jpg';
							}else{
								$result->img_url='/'.$img_url.'default.jpg';
							}
							//查看是否存在阅读记录
							$read = $read_log->getLog(array($result->id));
							//更新小说阅读时间
							$novel->setRtime($result->id);
							view()->share([
							  'info' => $result,
							  'cid' => $read ? $read->cid : '',
							]);
							return view('fiction.layout', [
							  'content' => view()->make('fiction.list')->render(),
							  'page_id' => 'list'
							]);
						}
					});
			/**
			 * 小说详情页
			 * ======
			 * @author 简强
			 * @version 17.5.26
			 */
			Route::get('/novel/detail/{id}', function($id) {
						if ($id) {
							$chapter = new Chapter();
							$crawl = new App\Crawl();
							$result = $chapter->getInfo($id);
							$_id = explode('_', $id);
							if(!$result){
								return redirect('/novel/list/' . $_id[1]);
							}
							if (strlen($result->content)<100) {//章节还未载入
								$crawl->getDetail($result->link, $id); //小说内容搜索并入库
								$result = $chapter->getInfo($id); //再次获取数据
							}
							$on = (int) $_id[0] <= 1 ? 0 : (int) $_id[0] - 1;
							$result->on = $chapter->setId($on) . '_' . $_id[1];
							$result->list = $_id[1];
							$result->next = $chapter->setId((int) $_id[0] + 1) . '_' . $_id[1];
							$result->chapter_id = $id;
							$result->novel_id = $_id[1];
							view()->share('info', $result);
							return view('fiction.layout', [
							  'content' => view()->make('fiction.detail')->render(),
							  'page_id' => 'detail'
							]);
						}
					});
			/**
			 * 小说搜索并入库
			 * ======
			 * @author 简强
			 * @version 17.5.25
			 */
			Route::get('/novel/add/{link}', function($link) {
						if ($link) {
							$crawl = new App\Crawl();
							$id = $crawl->getList(base64_decode($link)); //小说搜索并入库
							return redirect('/novel/list/' . $id);
						}
					});
			Route::controller('search', 'SearchController');
//			Route::controller('/novel/{name?}/{link?}', 'NovelController');
			Route::controller('ajax', 'AjaxController');
		});
