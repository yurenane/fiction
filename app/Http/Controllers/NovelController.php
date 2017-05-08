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
//		PrintCss::r(array($result,$link));
//		$this->updateList($result->id, $link); //更新列表
		//查看是否存在阅读记录
		$read = $this->read_log->getLog($result->id);
//		if ($read && $jump) {
//			return $this->getDetail($read->cid, $link);
//			exit;
//		}
		view()->share([
		  'info' => $result,
		  'link' => $link,
		  'cid' => isset($read) ? $read->cid : '',
		]);
		return view('fiction.layout', [
		  'content' => view()->make('fiction.list')->render(),
		  'page_id' => 'list'
		]);
	}

	/**
	 * 小说详情
	 * ======
	 * @author 简强
	 * @version 17.1.12
	 */
	public function getDetail($id, $link) {
		//判断是否跳转到最新阅读记录
//		$_id = explode('_', $id);
//		$read = $this->read_log->getLog($_id[1]);
//		if(isset($read)){
//			$_2id=explode('_', $read->cid);
//			if((int)$_2id[0]>(int)$_id[0]){
//				$id=$read->cid;
//			}
//		}		
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
		$result->on = $this->_id((int) $_id[0] - 1) . '_' . $_id[1];
		$result->link = $link;
		$result->list = $_id[1];
		$result->next = $this->_id((int) $_id[0] + 1) . '_' . $_id[1];
		$result->chapter_id = $id;
		$result->novel_id = $_id[1];
		view()->share('info', $result);
		return view('fiction.layout', [
		  'content' => view()->make('fiction.detail')->render(),
		  'page_id' => 'detail'
		]);
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
