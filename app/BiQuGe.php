<?php

namespace App;

use phpQuery;
use App\Curl;
use App\PrintCss;

/**
 * 笔趣阁网站
 * ======
 * @author 简强
 * @version 17.1.12
 */
class BiQuGe {

	private static $url = array(
	  1 => 'http://www.biquge.com', //站外搜索 287293036948159515
	  2 => 'http://www.biquge.com.tw', //站外搜索 8353527289636145615
	  3 => 'http://www.woquge.com', //站内搜索
	  4 => 'http://www.xxbiquge.com', //站外搜索  8823758711381329060
	  5 => 'http://www.biquge5200.com', //站内搜索
	  6 => 'http://www.qu.la', //站外搜索  920895234054625192
	  7 => 'http://www.bqg5200.com', //站外搜索  17194782488582577862
	); //网站域名
	private static $s = array(
	  1 => '287293036948159515',
	  2 => '8353527289636145615',
	  4 => '8823758711381329060',
	  6 => '920895234054625192',
	  7 => '17194782488582577862',
	); //搜索ID
	private static $id = 2;

	function __construct() {
		self::$id = rand(2, 6);
	}

	/**
	 * 小说搜索
	 * ======
	 * @author 简强
	 * @version 17.1.12
	 */
	public static function search($title) {
		$curl = new Curl();
		$curl->setReferer(self::$url[self::$id]);
		$curl->setHeader(array('Host:www.baidu.com'));
		$url = in_array(self::$id, array(3, 5)) ? self::$url[self::$id] . '/modules/article/search.php?searchkey=' . urlencode($title) : 'http://zhannei.baidu.com/cse/search?q=' . urlencode($title) . '&p=0&s=' . self::$s[self::$id];
		$content = $curl->get($url);
		phpQuery::newDocumentHTML($content);
		$info = array();
		if (in_array(self::$id, array(3, 5))) {
			$list = pq('.grid tr');
			foreach ($list as $key => $val) {
				if ($key) {
					$name = pq($val)->find('td')->eq(0)->find('a')->text();
					$info[] = array(
					  'name' => $name,
					  '_name' => urlencode($name),
					  'img' => '',
					  'title' => '',
					  'author' => pq($val)->find('td')->eq(2)->text(),
					  'type' => '',
					  'utime' => pq($val)->find('td')->eq(4)->text(),
					  'new' => pq($val)->find('td')->eq(1)->find('a')->text(),
					  'stauts' => pq($val)->find('td')->eq(5)->attr('title'),
					  'link' => base64_encode(pq($val)->find('td')->eq(0)->find('a')->attr('href')),
					  '_link' => pq($val)->find('td')->eq(0)->find('a')->attr('href'),
					);
				}
			}
		} else {
			$list = pq('.result-list .result-item');
			foreach ($list as $val) {
				$name = pq($val)->find('.result-item-title a')->attr('title');
				$info[] = array(
				  'name' => $name,
				  '_name' => urlencode($name),
				  'img' => pq($val)->find('.result-game-item-pic img')->attr('src'),
				  'title' => self::clear(pq($val)->find('.result-game-item-desc')->text()),
				  'author' => self::clear(pq($val)->find('.result-game-item-info p')->eq(0)->find('span')->eq(1)->text()),
				  'type' => rtrim(pq($val)->find('.result-game-item-info p')->eq(1)->find('span')->eq(1)->text()),
				  'utime' => rtrim(pq($val)->find('.result-game-item-info p')->eq(2)->find('span')->eq(1)->text()),
				  'new' => rtrim(pq($val)->find('.result-game-item-info p')->eq(3)->find('a')->text()),
				  'stauts' => rtrim(pq($val)->find('.result-game-item-info p')->eq(3)->find('span')->eq(1)->text()),
				  'link' => base64_encode(pq($val)->find('.result-game-item-pic a')->attr('href')),
				  '_link' => pq($val)->find('.result-game-item-pic a')->attr('href'),
				);
			}
		}
		return $info;
	}

	/**
	 * 获取小说列表
	 * ======
	 * @author 简强
	 * @version 17.1.12
	 */
	public static function getList($url) {
		$_url = explode('/', $url);
		$host = $_url[2];
		$curl = new Curl();
		$curl->setReferer('http://' . $host);
		$curl->setHeader(array('Host:' . $host));
		$content = $curl->get($url);
		phpQuery::newDocumentHTML($content);
		$novel = array('info' => array(), 'list' => array());
		$author = pq('#info p')->eq(0)->text();
		$type = pq('.con_top')->text();
		$utime = pq('#info p')->eq(2)->text();
		$name = pq('#info')->find('h1')->text();
		$novel['info'] = array(
		  'name' => $name,
		  'title' => self::clear(pq('#intro p')->eq(0)->text()),
		  'cover' => 'http://' . $host . pq('#fmimg')->find('img')->attr('src'),
		  'author' => self::strcut('者：', '', $author),
		  'type' => self::strcut('笔趣阁 > ', ' > ' . $name, $type),
		  'new' => pq('#info p')->eq(3)->find('a')->text(),
		  'utime' => self::strcut('更新：', '', $utime),
		  'link'=>$url,
		);
		$list = pq('#list dl dd');
		$start = false;
		foreach ($list as $val) {
			$title = pq($val)->find('a')->text();
			if (strpos('*' . $title, '第一章')) {
				$start = true;
			}
			if ($start) {
				$novel['list'][] = array(
				  'title' => $title,
				  'link' => 'http://' . $host . pq($val)->find('a')->attr('href'),
				);
			}
		}
//		PrintCss::r($novel);
		return $novel;
	}

	/**
	 * 获取小说详情
	 * ======
	 * @author 简强
	 * @version 17.1.16
	 */
	public static function getdetail($url) {
//		$_url = explode('/', $url);
//		$host = str_replace('www', 'm', $_url[2]);
//		$id=  explode('_', $_url[3]);
//		$url='http://'.$host.'/wapbook/'.$id[1].'_'.$_url[4];   移动端站点
		phpQuery::newDocumentFile($url);
//		phpQuery::newDocumentHTML($content);
		$content=pq('#content')->text();
		$content=str_replace(array("\r\n", "\r", "\n", ' '), '</p><p>', $content);
//		PrintCss::r('<p>'.$content.'</p>');
		return array('title' => self::clear(pq('.bookname h1')->text()), 'content' => '<p>'.$content.'</p>');
	}
	/**
	 * 更新小说列表
	 * ======
	 * @author 简强
	 * @version 17.1.16
	 */
	public static function update($url,$total){
		
	}

	private static function clear($content) {
		return str_replace(array("\r\n", "\r", "\n", ' '), '', $content);
	}

	private static function strcut($start, $end, $string) {
		$len = strlen($string) - strpos($string, $start) - strlen($start);
		$len -=$end ? (strlen($string) - strpos($string, $end)) : 0;
		return substr($string, (strpos($string, $start) + strlen($start)), $len);
	}

}