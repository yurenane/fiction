<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\PrintCss;
use App\BiQuGe;

/**
 * 数据搜索类
 * ======
 * @author 简强
 * @version 17.1.12
 */
class AjaxController extends Controller {

	/**
	 * 添加收藏
	 * ======
	 * @author 简强
	 * @version 17.1.18
	 */
	public function postCollect(Request $request) {
		$post = $request->all();
		$user_info = $request->session()->get('user');
		$id = array();
		if ($user_info->nlist) {
			$id = explode(',', $user_info->nlist);
		}
		if (!in_array($post['id'], $id)) {
			$user_info->nlist = $user_info->nlist ? $user_info->nlist . ',' . $post['id'] : $post['id'];
			$request->session()->set('user', $user_info);
			DB::table('user')
				->where('id', $user_info->id)
				->update(['nlist' => $user_info->nlist]);
		}
		echo json_encode(array('code' => 1000, 'info' => ''));
	}

}
