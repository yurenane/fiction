<?php

namespace App;

use Illuminate\Support\Facades\DB;
use App\PrintCss;
use App\Chapter;
use App\BiQuGe;
use App\Curl;

class Crawl {

	/**
	 * 数据采集，小说列表
	 * ======
	 * @author 简强
	 * @version 17.1.12
	 */
	public function getList($url) {
		return $this->addList(BiQuGe::getList($url)); //笔趣阁
	}

	/**
	 * 数据采集，小说详情
	 * ======
	 * @author 简强
	 * @version 17.1.12
	 */
	public function getDetail($url, $id) {
		$chapter = new Chapter;
		$info = BiQuGe::getdetail($url); //笔趣阁
		return $chapter->updateDetail($id, $info['content']);
	}

	/**
	 * 获取最新数据
	 * ======
	 * @author 简强
	 * @version 17.1.20
	 */
	public function updateList($id, $link) {
		$chapter = new Chapter;
		$info = $chapter->getList($id, 'id', 'desc', 1);
		$result = BiQuGe::update($link, $info);
//		PrintCss::r($result);
		if ($result) {
			$_id = explode('_', $info->id);
			$num = (int) $_id[0];
			$insert = array();
			foreach ($result as $val) {
				$content = BiQuGe::getdetail($val['link']); //笔趣阁
				$num++;
				$insert[] = array(
				  'id' => $this->_id($num) . '_' . $id,
				  "nid" => $id,
				  "link" => $val['link'],
				  "title" => $val['title'],
				  'content' => $content['content'],
				  'sort' => $num,
				);
			}
			DB::table('chapter')->insert($insert);
			$title = $result[count($result) - 1]['title'];
			DB::table('novel')->where('id', $id)->update(array('new' => $title,'status'=>2));
			return $title;
		} else {
			return false;
		}
	}

	/**
	 * 插入列表数据
	 * ======
	 * @author 简强
	 * @version 17.1.13
	 */
	private function addList($content) {
		$curl = new Curl();
		$_url = explode('/',  $content['info']['cover']);
		$host = $_url[2];
		$curl->setReferer('http://' . $host) ;
		 $curl->setHeader(array('Host:' . $host));
		$id = '';
		if ($content['list']) {
			$id = uniqid();
			//保存封面图片
			file_put_contents(ROOT . '/statics/images/fiction/' . $id . '.jpg', $curl->get($content['info']['cover']));
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
