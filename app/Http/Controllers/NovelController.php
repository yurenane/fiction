<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\PrintCss;
use App\BiQuGe;

/**
 * 小说数据处理类
 * ======
 * @author 简强
 * @version 17.1.12
 */
class NovelController extends Controller {

	/**
	 * 小说列表
	 * ======
	 * @author 简强
	 * @version 17.1.12
	 */
	public function getIndex($name, $link) {
		$result = $this->getNovelInfo($name);
		if (!$result && $link) {
			$this->getList(base64_decode($link)); //小说搜索并入库
			$result = $this->getNovelInfo($name); //再次拿取数据
		}
		$this->updateList($result->id, $link); //更新列表
		//查看是否存在阅读记录
		$read = $this->getReadLog($result->id);
		if ($read) {
			return $this->getDetail($read->cid, $link);
			exit;
		}
		return view('fiction.list', ['info' => array(
			'info' => $result,
			'link' => $link,
			'list' => $this->getNovelList($result->id),
		)]);
	}

	/**
	 * 小说详情
	 * ======
	 * @author 简强
	 * @version 17.1.12
	 */
	public function getDetail($id, $link) {
		$result = $this->getChapter($id);
		if (!$result->content) {
			$this->_getDetail($result->link, $id);//小说内容搜索并入库
			$result = $this->getChapter($id);//再次获取数据
		}
		$_id = explode('_', $id);
		$this->updateRead($_id[1], $id);//更新阅读记录
		$result->on = '/novel/' . $this->_id((int) $_id[0] - 1) . '_' . $_id[1] . '/' . $link . '/detail';
		$result->list = '/novel/' . $_id[1] . '/' . $link;
		$result->next = '/novel/' . $this->_id((int) $_id[0] + 1) . '_' . $_id[1] . '/' . $link . '/detail';
//		PrintCss::r($result);
		return view('fiction.detail', ['info' => $result]);
	}

	/**
	 * 数据采集，小说列表
	 * ======
	 * @author 简强
	 * @version 17.1.12
	 */
	private function getList($url) {
		return $this->addList(BiQuGe::getList($url)); //笔趣阁
	}

	/**
	 * 数据采集，小说详情
	 * ======
	 * @author 简强
	 * @version 17.1.12
	 */
	private function _getDetail($url, $id) {
		$info = BiQuGe::getdetail($url); //笔趣阁
		return $this->addDetail($id, $info['content']);
	}

	/**
	 * 获取小说列表
	 * ======
	 * @author 简强
	 * @version 17.1.13
	 */
	private function getNovelList($id) {
		return DB::table('chapter')
				->select('id', 'nid', 'link', 'title')
				->where('nid', $id)
				->orderBy('sort', 'desc')
				->get();
	}

	/**
	 * 获取小说信息
	 * ======
	 * @author 简强
	 * @version 17.1.16
	 */
	private function getNovelInfo($name) {
		return DB::table('novel')
//				->select('id', 'name', 'title','author','cover','type','new','utime','status')
				->where('name', $name)
				->first();
	}

	/**
	 * 获取章节信息
	 * ======
	 * @author 简强
	 * @version 17.1.16
	 */
	private function getChapter($id) {
		return DB::table('chapter')
				->where('id', $id)
				->first();
	}

	/**
	 * 获取阅读记录
	 * ======
	 * @author 简强
	 * @version 17.1.19
	 */
	private function getReadLog($nid) {
		return DB::table('read_log')
				->where('nid', $nid)
				->first();
	}

	/**
	 * 插入列表数据
	 * ======
	 * @author 简强
	 * @version 17.1.13
	 */
	private function addList($content) {
		$id = '';
		if ($content['list']) {
			$id = uniqid();
			//保存封面图片
			file_put_contents(ROOT . '/statics/images/fiction/' . $id . '.jpg', file_get_contents($content['info']['cover']));
			DB::table('novel')->insert([
			  'id' => $id,
			  'name' => $content['info']['name'],
			  'title' => $content['info']['title'],
			  'cover' => $content['info']['cover'],
			  'author' => $content['info']['author'],
			  'type' => $content['info']['type'],
			  'utime' => strtotime($content['info']['utime']),
			  'new' => $content['info']['new'],
			  'link' => $content['info']['link'],
			]);
			$insert = array();
			$num = 0;
			foreach ($content['list'] as $val) {
				$insert[] = array(
				  'id' => $this->_id($num) . '_' . $id,
				  "nid" => $id,
				  "link" => $val['link'],
				  "title" => $val['title'],
				  'sort' => $num,
				);
				$num++;
			}
//				PrintCss::n(array($insert));
			DB::table('chapter')->insert($insert);
		}
		return $id;
	}

	/**
	 * 插入详情数据
	 * ======
	 * @author 简强
	 * @version 17.1.16
	 */
	private function addDetail($id, $content) {
		if ($id && $content) {
			DB::table('chapter')
				->where('id', $id)
				->update(['content' => $content]);
			return true;
		}
		return false;
	}

	/**
	 * 更新数据
	 * ======
	 * @author 简强
	 * @version 17.1.19
	 */
	private function updateList($id, $link) {
		$info = DB::table('chapter')
			->where('nid', $id)
			->orderBy('id', 'desc')
			->first();
		$result = BiQuGe::update(base64_decode($link), $info);
		if ($result) {
			$_id = explode('_', $info->id);
			$num = (int) $_id[0];
			$insert = array();
			foreach ($result as $val) {
				$num++;
				$insert[] = array(
				  'id' => $this->_id($num) . '_' . $id,
				  "nid" => $id,
				  "link" => $val['link'],
				  "title" => $val['title'],
				  'sort' => $num,
				);
			}
			DB::table('chapter')->insert($insert);
		}
		return true;
	}

	/**
	 * 更新阅读记录
	 * ======
	 * @author 简强
	 * @version 17.1.19
	 */
	private function updateRead($nid, $cid) {
		if ($this->getReadLog($nid)) {
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
