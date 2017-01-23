<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\PrintCss;
use App\BiQuGe;
use App\Novel;
use App\Chapter;
use App\ReadLog;
use App\Crawl;

/**
 * 小说数据处理类
 * ======
 * @author 简强
 * @version 17.1.12
 */
class NovelController extends Controller {

	private $novel = '';
	private $chapter = '';
	private $read_log = '';
	private $crawl = '';

	function __construct() {
		$this->novel = new Novel;
		$this->chapter = new Chapter;
		$this->read_log = new ReadLog;
		$this->crawl = new Crawl;
	}

	/**
	 * 小说列表
	 * ======
	 * @author 简强
	 * @version 17.1.12
	 */
	public function getIndex($name, $link) {
		$result = $this->novel->getInfo($name);
		if (!$result && $link) {
			$this->crawl->getList(base64_decode($link)); //小说搜索并入库
			$result = $this->novel->getInfo($name); //再次拿取数据
		}
//		$this->updateList($result->id, $link); //更新列表
		//查看是否存在阅读记录
//		$read = $this->read_log->getLog($result->id);
//		if ($read && $jump) {
//			return $this->getDetail($read->cid, $link);
//			exit;
//		}
		return view('fiction.list', ['info' => array(
			'info' => $result,
			'link' => $link,
			'list' => $this->chapter->getList($result->id, 'sort', 'desc'),
		)]);
	}

	/**
	 * 小说详情
	 * ======
	 * @author 简强
	 * @version 17.1.12
	 */
	public function getDetail($id, $link) {
		$result = $this->chapter->getInfo($id);
		$_id = explode('_', $id);
		if (!$result) {
			return $this->getIndex($_id[1], $link, false);
			exit;
		}
		if (!$result->content) {
			$this->crawl->getDetail($result->link, $id); //小说内容搜索并入库
			$result = $this->chapter->getInfo($id); //再次获取数据
		}
		$this->updateRead($_id[1], $id); //更新阅读记录
		$result->on = $this->_id((int) $_id[0] - 1) . '_' . $_id[1];
		$result->link = $link;
		$result->list = $_id[1];
		$result->next = $this->_id((int) $_id[0] + 1) . '_' . $_id[1];
		$result->chapter_id=$id;
		$result->novel_id=$_id[1];
//		PrintCss::r($result);
		return view('fiction.detail', ['info' => $result]);
	}


	/**
	 * 更新阅读记录
	 * ======
	 * @author 简强
	 * @version 17.1.19
	 */
	private function updateRead($nid, $cid) {
		if ($this->read_log->getLog($nid)) {
			DB::table('read_log')
				->where('nid', $nid)
				->update(['cid' => $cid]);
		} else {
			DB::table('read_log')
				->insert(array(
				  'id' => uniqid(),
				  'nid' => $nid,
				  'cid' => $cid,
				  'utime' => time(),
			));
		}
		return true;
	}

	/**
	 * 章节ID前缀补全
	 * ======
	 * @author 简强
	 * @version 17.1.16
	 */
	private function _id($num) {
		$_id = '';
		for ($i = 1; $i <= (5 - strlen((string) $num)); $i++) {
			$_id .='0';
		}
		return $_id . $num;
	}

}
