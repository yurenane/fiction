<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\PrintCss;
use App\BiQuGe;
use App\Chapter;
use App\Novel;
use App\ReadLog;
use App\Crawl;

/**
 * 数据搜索类
 * ======
 * @author 简强
 * @version 17.1.12
 */
class AjaxController extends Controller {

	private $post = array();
	private $request = '';
	private $novel = '';
	private $chapter = '';
	private $read_log = '';
	private $crawl = '';

	function __construct(Request $request) {
		$this->novel = new Novel;
		$this->chapter = new Chapter;
		$this->read_log = new ReadLog;
		$this->crawl = new Crawl;
		if ($request) {
			$this->request = $request;
			$this->post = $request->all();
		}
	}

	/**
	 * 添加收藏
	 * ======
	 * @author 简强
	 * @version 17.1.18
	 */
	public function postCollect() {
		$user_info = $this->request->session()->get('user');
		$id = array();
		if ($user_info->nlist) {
			$id = explode(',', $user_info->nlist);
		}
		if (!in_array($this->post['id'], $id)) {
			$user_info->nlist = $user_info->nlist ? $user_info->nlist . ',' . $this->post['id'] : $this->post['id'];
			$this->request->session()->set('user', $user_info);
			DB::table('user')
				->where('id', $user_info->id)
				->update(['nlist' => $user_info->nlist]);
		}
		echo json_encode(array('code' => 1000, 'info' => ''));
	}

	/**
	 * 获取图片
	 * ======
	 * @author 简强
	 * @version 17.1.20
	 */
	public function postImage() {
		
	}

	/**
	 * 异步更新数据
	 * ======
	 * @author 简强
	 * @version 17.1.22
	 */
	public function postUpdateNovel() {
		$novel = $this->novel->getInfo($this->post['id']);
		if (!$novel->is_new) {
			$info = $this->chapter->getList($this->post['id'], 'id', 'desc', 0);
			$is_down = false;
			foreach ($info as $val) {
				if (!$val->content) {
					$this->crawl->getDetail($val->link, $val->id); //小说内容搜索并入库
					$is_down = true;
				}
			}
			if (!$is_down) {
				DB::table('novel')->where('id', $this->post['id'])->update(array('is_new' => 1));
			}
		}
	}

	/**
	 * 获取最新数据
	 * ======
	 * @author 简强
	 * @version 17.1.20
	 */
	public function postUpdateList() {
		$title = $this->crawl->updateList($this->post['id'], base64_decode($this->post['link']));
		if ($title) {
			echo json_encode(array('code' => 1000, 'info' => $title));
		} else {
			echo json_encode(array('code' => 1001, 'info' => ''));
		}
	}

	/**
	 * 获取章节
	 * ======
	 * @author 简强
	 * @version 17.1.22
	 */
	public function postChapterList() {
		$limit = isset($this->post['limit']) ? $this->post['limit'] : 10;
		$id = isset($this->post['id']) ? $this->post['id'] : '';
		$sort = isset($this->post['sort']) ? $this->post['sort'] : 'desc';
		$list = $this->chapter->getList($this->post['nid'], 'sort', $sort, $limit, ($this->post['p'] - 1) * $limit, $id);
		foreach ($list as $key => $val) {
			$list[$key]->link = base64_encode($val->link);
		}
		if ($list) {
			echo json_encode(array('code' => 1000, 'info' => $list));
		} else {
			echo json_encode(array('code' => 1001, 'error' => '数据获取失败'));
		}
	}

	/**
	 * 获取小说
	 * ======
	 * @author 简强
	 * @version 17.1.22
	 */
	public function postNovelList() {
		$id = isset($this->post['id']) ? explode(',', $this->post['id']) : array();
		$list = $this->novel->getList($id, 10, ($this->post['p'] - 1) * 10);
		foreach ($list as $key => $val) {
			$list[$key]->link = base64_encode($val->link);
			$list[$key]->utime = date('Y-m-d', $val->utime);
		}
		if ($list) {
			echo json_encode(array('code' => 1000, 'info' => $list));
		} else {
			echo json_encode(array('code' => 1001, 'error' => '数据获取失败'));
		}
	}

	/**
	 * 取消收藏
	 * ======
	 * @author 简强
	 * @version 17.1.23
	 */
	public function postDeleteCollect() {
		$user_info = $this->request->session()->get('user');
		$id = array();
		if ($user_info->nlist) {
			$id = explode(',', $user_info->nlist);
		}
		if (!in_array($this->post['id'], $id)) {
			echo json_encode(array('code' => 1001, 'error' => '小说不存在'));
		} else {
			foreach ($id as $key => $val) {
				if ($this->post['id'] == $val) {
					unset($id[$key]);
				}
			}
			$user_info->nlist = implode(',', $id);
			$this->request->session()->set('user', $user_info);
			DB::table('user')
				->where('id', $user_info->id)
				->update(['nlist' => $user_info->nlist]);
			echo json_encode(array('code' => 1000, 'info' => ''));
		}
	}
	/**
	 * 更新阅读记录
	 * ======
	 * @author 简强
	 * @version 17.1.19
	 */
	public function postUpdateRead() {
		$nid=$this->post['nid'];
		$cid=$this->post['cid'];
		if(!$nid||!$cid){
			echo json_encode(array('code'=>1001,'error'=>'参数错误'));
			exit;
		}
		$user_info=session('user');
		if ($this->read_log->getLog($nid)) {
			DB::table('read_log')
				->where('uid', $user_info->id)
				->where('nid', $nid)
				->update(['cid' => $cid]);
		} else {
			DB::table('read_log')
				->insert(array(
				  'id' => uniqid(),
				  'uid' => $user_info->id,
				  'nid' => $nid,
				  'cid' => $cid,
				  'utime' => time(),
			));
		}
		echo json_encode(array('code'=>1000,'info'=>'数据更新成功'));
	}

}
