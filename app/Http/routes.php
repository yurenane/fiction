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
//汽车之家车型抓取
Route::get('/car', function() {
			set_time_limit(0);
			$curl = new Curl();
			$curl->setReferer('http://www.autohome.com.cn/beijing/');
			$curl->setHeader(array('Host:www.autohome.com.cn', 'Content-Type:text/plain;charset=gb2312'));
			$content = $curl->get('http://www.autohome.com.cn/ashx/AjaxIndexCarFind.ashx?type=1');
			$content = json_decode(iconv('gb2312', 'utf-8', $content));
//		PrintCss::r(json_decode(iconv('gb2312','utf-8',$content)));
			$car = array();
			header("Content-type:text/csv;charset=UTF-8");
			header("Content-Disposition:attachment;filename=car-" . date('Ymd') . ".csv");
			header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
			header('Expires:0');
			header('Pragma:public');
			echo "品牌,车类,车系,年份,车型\n";
			foreach ($content->result->branditems as $key => $val) {
				$info = $curl->get('http://www.autohome.com.cn/ashx/AjaxIndexCarFind.ashx?type=3&value=' . $val->id);
				$info = json_decode(iconv('gb2312', 'utf-8', $info));
				foreach ($info->result->factoryitems as $v) {
					foreach ($v->seriesitems as $v1) {
						$info2 = $curl->get('http://www.autohome.com.cn/ashx/AjaxIndexCarFind.ashx?type=5&value=' . $v1->id);
						$info2 = json_decode(iconv('gb2312', 'utf-8', $info2));
//					PrintCss::n(array($content, $list, $list2));
						foreach ($info2->result->yearitems as $v2) {
							foreach ($v2->specitems as $v3) {
								echo $val->name . "," . $v->name . "," . $v1->name . "," . $v2->name . "," . $v3->name . "\n";
							}
						}
					}
				}
			}
		});
//===========
//小说阅读
//===========
Route::group(['middleware' => ['login']], function() {
			Route::get('/', function() {
						return view('fiction.layout', array(
						  'content' => view()->make('fiction.index')->render(),
						  'page_id' => 'index',
						));
					});
			Route::get('/search', function() {
						return view('fiction.layout', array(
						  'content' => view()->make('fiction.search')->render(),
						  'page_id' => 'search',
						));
					});
			Route::get('/user', function() {
						$user_info = session('user');
						$user_info = DB::table('user')
								->where('id', $user_info->id)
								->first();
						view()->share('info', $user_info);
						return view('fiction.layout', array(
						  'content' => view()->make('fiction.user')->render(),
						  'page_id' => 'user',
						));
					});
			Route::get('/login', function() {
						return view('fiction.layout', array(
						  'content' => view()->make('fiction.login')->render(),
						  'page_id' => 'login',
						));
					});
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
			Route::controller('search', 'SearchController');
			Route::controller('/novel/{name}/{link}', 'NovelController');
			Route::controller('ajax', 'AjaxController');
		});
