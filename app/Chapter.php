<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\PrintCss;

class Chapter extends Model {

	protected $table = 'chapter';

	/**
	 * 获取小说列表，根据小说ID
	 * ======
	 * @author 简强
	 * @version 17.1.20
	 */
	public function getList($id, $orderByField, $orderByInfo, $limit = 10, $skip = 0) {
		if ($limit) {
			$resutl = self::whereRaw('nid = ?', [$id])
				->orderBy($orderByField, $orderByInfo)
				->skip($skip)
				->take($limit)
				->get();
		} else {
			$resutl = self::whereRaw('nid = ?', [$id])
				->orderBy($orderByField, $orderByInfo)
				->get();
		}

		$info = array();
		foreach ($resutl as $val) {
			$info[] = (Object) $val->original;
		}
		return $limit == 1 ? $info[0] : $info;
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

	public function getInfo($id) {
		$result = self::where('id', '=', $id)->first();
		return (Object) $result->original;
	}

}
