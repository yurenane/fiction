<?php

namespace App;

use Illuminate\Support\Facades\DB;

class ReadLog {

	/**
	 * 获取阅读记录
	 * ======
	 * @param array $id  小说ID数组
	 * ======
	 * @author 简强
	 * @version 17.1.20
	 */
	public function getLog($id) {
		$result = DB::table('read_log')->whereIn('nid', $id)->get();
		return count($result) ==1 ? $result [0]: $result;
	}

}
