<?php

namespace App;

use Illuminate\Support\Facades\DB;

/**
 * 小说模型
 * ======
 * @author 简强
 * @version 17.1.20
 */
class Novel {

	/**
	 * 获取小说信息
	 * ======
	 * @param string $id  小说ID
	 * @param string $name  小说名字
	 * @param string $field  小说字段
	 * ======
	 * @author 简强
	 * @version  17.5.26
	 */
	public function getInfo($id = '', $name = '', $field = '*') {
		$where = array();
		if ($id) {
			$where['id'] = $id;
		}
		if ($name) {
			$where['name'] = $name;
		}
		return DB::table('novel')->select($field)->where($where)->first();
	}

	/**
	 * 小说名字模糊搜索
	 * ======
	 * @param string $name  小说名字
	 * ======
	 * @author 简强
	 * @version 17.5.26
	 */
	public function getSearch($name) {
		return DB::table('novel')->where('name', 'like', '%' . $name . '%')->get();
	}

	/**
	 * 获取小说列表
	 * ======
	 * @param  array $id  小说ID数组
	 * @param int $limit  记录条数
	 * @param int $skip  偏移量
	 * @param string $order 排序值
	 * ======
	 * @author 简强
	 * @version  17.5.26
	 */
	public function getList($id, $limit = 10, $skip = 0, $order = 'rtime desc') {
		if ($id) {
			return DB::table('novel')->whereIn('id', $id)->orderByRaw($order)->skip($skip)->take($limit)->get();
		} else {
			return DB::table('novel')->orderByRaw($order)->skip($skip)->take($limit)->get();
		}
	}

	/**
	 * 更新小说阅读时间
	 * ======
	 * @param string $id  小说id
	 * ======
	 * @author 简强
	 * @version 17.5.26
	 */
	public function setRtime($id) {
		return DB::table('novel')->where('id', $id)->update(array('status' => 1, 'rtime' => time()));
	}

}
