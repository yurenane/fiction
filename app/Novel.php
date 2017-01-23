<?php

namespace App;

use Illuminate\Support\Facades\DB;
use App\PrintCss;

class Novel {

	/**
	 * 获取小说信息
	 * ======
	 * @author 简强
	 * @version 17.1.20
	 */
	public function getInfo($name) {
		return DB::table('novel')->where('name', $name)->orWhere('id', $name)->first();
	}

	/**
	 * 获取小说列表
	 * ======
	 * @param array $id  小说ID数组
	 * @param int $limit  记录条数
	 * @param int $skip  偏移量
	 * ======
	 * @author 简强
	 * @version 17.1.22
	 */
	public function getList($id = array(), $limit = 10, $skip = 0) {
		if (!$id) {
			return DB::table('novel')->orderBy('utime', 'desc')->skip($skip)->take($limit)->get();
		} elseif (is_array($id)) {
			return DB::table('novel')->whereIn('id', $id)->orderBy('utime', 'desc')->skip($skip)->take($limit)->get();
		} else {
			return DB::table('novel')->where('id', $id)->orderBy('utime', 'desc')->skip($skip)->take($limit)->get();
		}
	}

}
