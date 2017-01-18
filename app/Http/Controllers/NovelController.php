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

	public function getIndex($name, $link) {
		$result = $this->getNovelInfo($name);
		if (!$result&&$link) {
			$info = $this->getList(base64_decode($link));
			$this->addList($info);
		}
		$result = $this->getNovelInfo($name);
		return view('fiction.list', ['info' => array(
			'info' => $result,
			'link'=>$link,
			'list' => $this->getNovelList($result->id),
		)]);
	}

	public function getDetail($name,$link) {
		$result = $this->getChapter($name);
		if($result){
		} 
		if (!$result->content) {
			$result = $this->_getDetail($result->link);
			$this->addDetail($name, $result['content']);
		}
		$result=(array) $result;
		$id = explode('_', $name);
//		PrintCss::r($this->_id((int)$id[0]-1));
		$result['on'] ='/novel/'.$this->_id((int)$id[0]-1).'_'.$id[1].'/'.$link.'/detail';
		$result['list'] ='/novel/'.$id[1].'/'.$link;
		$result['next'] ='/novel/'.$this->_id((int)$id[0]+1).'_'.$id[1].'/'.$link.'/detail';
		return view('fiction.detail', ['info' => $result]);
	}

	private function getList($url) {
		return BiQuGe::getList($url); //笔趣阁
	}

	private function _getDetail($url) {
		return BiQuGe::getdetail($url);
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
	 * 插入列表数据
	 * ======
	 * @author 简强
	 * @version 17.1.13
	 */
	private function addList($content) {
		$id = '';
		if ($content['list']) {
			$id = uniqid();
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
	
	private function updateList($id){
		
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
