<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\PrintCss;
use App\BiQuGe;
/**
 * 数据搜索类
 * ======
 * @author 简强
 * @version 17.1.12
 */
class SearchController extends Controller {

	public function postIndex(Request $request) {
		$curl=new \App\Curl;
		$info=$curl->get('http://www.baidu.com');
		PrintCss::r($info);
		$post = $request->all();
		$info= $this->search($post['title']);
		if($info){
			echo json_encode(array('code'=>1000,'info'=>$info));
		}else{
			echo json_encode(array('code'=>1001,'error'=>'数据获取失败'));
		}
	}

	private function search($title) {
		return BiQuGe::search($title);//笔趣阁
	}

	private function getData($url) {
		
	}

}
