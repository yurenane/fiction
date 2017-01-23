<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\PrintCss;

class Chapter extends Model {

	protected $table = 'chapter';

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

	public function getInfo($id) {
		$result = self::where('id', '=', $id)->first();
		return (Object) $result->original;
	}

}
