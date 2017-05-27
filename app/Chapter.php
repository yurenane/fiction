<?php

namespace App;

use Illuminate\Support\Facades\DB;

class Chapter {

	/**
	 * 获取小说章节，根据小说ID
	 * ======
	 * @author 简强
	 * @version 17.1.20
	 */
	public function getList($nid, $orderByField, $orderByInfo, $limit = 10, $skip = 0, $id = '') {
		if (!$limit) {
			return DB::table('chapter')->where('nid', $nid)->where('id', '>', $id)->orderBy($orderByField, $orderByInfo)->get();
		} elseif ($limit == 1) {
			return DB::table('chapter')->where('nid', $nid)->orderBy($orderByField, $orderByInfo)->skip($skip)->take($limit)->first();
		} else {
			return DB::table('chapter')->where('nid', $nid)->orderBy($orderByField, $orderByInfo)->skip($skip)->take($limit)->get();
		}
	}

	/**
	 * 更新小说详情
	 * ======
	 * @author 简强
	 * @version 17.1.20
	 */
	public function updateDetail($id, $content) {
		return DB::table('chapter')->where('id', $id)->update(array('content' => $content));
	}

	/**
	 * 获取小说章节详情
	 * ======
	 * @param string $id  小说章节ID
	 * @param string $field  小说章节字段
	 * ======
	 * @author 简强
	 * @version  17.5.26
	 */
	public function getInfo($id, $field = '*') {
		return DB::table('chapter')->select($field)->where('id', $id)->first();
	}
	/**
	 * 章节ID前缀补全
	 * ======
	 * @author 简强
	 * @version 17.5.26
	 */
	public function setId($num) {
		$_id = '';
		for ($i = 1; $i <= (5 - strlen((string) $num)); $i++) {
			$_id .='0';
		}
		return $_id . $num;
	}
}
