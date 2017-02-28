<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\PrintCss;

class ReadLog extends Model {

	protected $table = 'read_log';

	/**
	 * 获取阅读记录
	 * ======
	 * @author 简强
	 * @version 17.1.20
	 */
	public function getLog($id) {
		$result = self::where('nid', $id)->first();
		return $result?(Object) $result->original:$result;
	}

}
