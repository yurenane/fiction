<?php
namespace App;
/**
 * 调试输出类
 * ======
 * @author 简强
 * @version 16.10.27
 */

class PrintCss {

	public static function r($conent) {
		echo '<pre>';
		print_r($conent);
		echo '</pre>';
		exit;
	}

	public static function v($conent) {
		echo '<pre>';
		var_dump($conent);
		echo '</pre>';
		exit;
	}
	public static function n($content){
		echo '<pre>';
		foreach ($content as $val){
			print_r($val);
		}
		echo '</pre>';
		exit;
	}
}