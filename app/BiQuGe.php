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

	/**
	 * 小说搜索 默认一个网址
	 * ======
	 * @author 简强
	 * @version 17.2.25
	 */

	public static function search($title) {
		$url = 'http://www.woquge.com/modules/article/search.php?searchkey=' . urlencode($title);
//		phpQuery::newDocumentFile($url);
		phpQuery::newDocumentHTML(self::getHtml($url, 'http://www.woquge.com', array('Host:www.woquge.com')));
		$info = array();
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
		return $info;
//			$list = pq('.result-list .result-item');
//			foreach ($list as $val) {
//				$name = pq($val)->find('.result-item-title a')->attr('title');
//				//获取图片，转成base64
//				$img = pq($val)->find('.result-game-item-pic img')->attr('src');
//				$info[] = array(
//				  'name' => $name,
//				  '_name' => urlencode($name),
//				  '_img' => $img,
//				  'img' => 'data:image/jpg;base64,' . base64_encode($curl->get($img)),
//				  'title' => self::clear(pq($val)->find('.result-game-item-desc')->text()),
//				  'author' => self::clear(pq($val)->find('.result-game-item-info p')->eq(0)->find('span')->eq(1)->text()),
//				  'type' => rtrim(pq($val)->find('.result-game-item-info p')->eq(1)->find('span')->eq(1)->text()),
//				  'utime' => rtrim(pq($val)->find('.result-game-item-info p')->eq(2)->find('span')->eq(1)->text()),
//				  'new' => rtrim(pq($val)->find('.result-game-item-info p')->eq(3)->find('a')->text()),
//				  'stauts' => rtrim(pq($val)->find('.result-game-item-info p')->eq(3)->find('span')->eq(1)->text()),
//				  'link' => base64_encode(pq($val)->find('.result-game-item-pic a')->attr('href')),
//				  '_link' => pq($val)->find('.result-game-item-pic a')->attr('href'),
//				);
//			}
	}

	/**
	 * 获取小说列表
	 * ======
	 * @param string $url  小说网络路径
	 *  @param bool $clear  是否从第一章开始
	 * ======
	 * @author 简强
	 * @version 17.5.25
	 */
	public static function getList($url) {
		$_url = explode('/', $url);
		$host = $_url[2];
		phpQuery::newDocumentHTML(self::getHtml($url, 'http://' . $host, array('Host:' . $host)));
//		phpQuery::newDocumentFile($url);
		$novel = array('info' => array(), 'list' => array());
		$author = pq('#info p')->eq(0)->text();
		$type = pq('.con_top')->text();
		$utime = pq('#info p')->eq(2)->text();
		$name = pq('#info')->find('h1')->text();
		$novel['info'] = array(
		  'name' => $name,
		  'title' => self::clear(pq('#intro p')->eq(0)->text()),
		  'cover' => pq('#fmimg')->find('img')->attr('src'),
		  'author' => self::strcut('者：', '', $author),
		  'type' => self::strcut('笔趣阁 > ', ' > ' . $name, $type),
		  'new' => pq('#info p')->eq(3)->find('a')->text(),
		  'utime' => self::strcut('更新：', '', $utime),
		  'link' => $url,
		);
		$list = pq('#list dl dd');
		foreach ($list as $key => $val) {
			if ($key > 8) {
				$novel['list'][] = array(
				  'title' => pq($val)->find('a')->text(),
				  'link' =>pq($val)->find('a')->attr('href'),
				);
			}
		}
		return $novel;
	}

	/**
	 * 获取小说详情
	 * ======
	 * @author 简强
	 * @version 17.1.16
	 */
	public static function getdetail($url) {
		$_url = explode('/', $url);
//		$host = str_replace('www', 'm', $_url[2]);
		$host = $_url[2];
//		$id=  explode('_', $_url[3]);
//		$url='http://'.$host.'/wapbook/'.$id[1].'_'.$_url[4];   移动端站点
//		phpQuery::newDocumentFile($url);
		phpQuery::newDocumentHTML(self::getHtml($url, 'http://' . $host, array('Host:' . $host)));
		$content = pq('#content')->text();
		$content = str_replace(array("\r\n", "\r", "\n", ' ', '<br>','　　'), '</p><p>', $content);
		return array('title' => self::clear(pq('.bookname h1')->text()), 'content' => '<p>' . $content . '</p>');
	}

	/**
	 * 更新小说列表
	 * ======
	 * @author 简强
	 * @version 17.1.16
	 */
	public static function update($url, $chapter) {
		$_url = explode('/', $url);
		$host = $_url[2];
//		phpQuery::newDocumentFile($url);
		// $content=self::getHtml($url, 'http://' . $host, array('Host:' . $host));
		// if(!mb_check_encoding($content, 'utf-8')) {
		// 	$content = mb_convert_encoding($content,'UTF-8','gbk');
		// }
		phpQuery::newDocumentHTML(self::getHtml($url, 'http://' . $host, array('Host:' . $host)));
		$list = pq('#list dl dd');
		$start = false;
		$info = array();
		$_chapter = $chapter;
		$num = 1;
		do {
			foreach ($list as $val) {
				$title = pq($val)->find('a')->text();
				if ($start) {
					$info[] = array(
					  'title' => $title,
					  'link' =>pq($val)->find('a')->attr('href'),
					);
				}
				if ($title == $_chapter->title) {
					$start = true;
				}
			}
			if (!$start) {
				$_chapter = self::getChapter($_chapter->id);
			}
			if ($num >= 3) {
				break;
			}
			$num++;
		} while (!$start); //进行三次更新
		return $info;
	}

	/**
	 * 文本内容清理
	 * ======
	 * @author 简强
	 * @version 17.1.12
	 */
	private static function clear($content) {
		return str_replace(array("\r\n", "\r", "\n", ' '), '', $content);
	}

	/**
	 * 字符串剪切
	 * ======
	 * @author 简强
	 * @version 17.1.12
	 */
	private static function strcut($start, $end, $string) {
		$len = strlen($string) - strpos($string, $start) - strlen($start);
		$len -=$end ? (strlen($string) - strpos($string, $end)) : 0;
		return substr($string, (strpos($string, $start) + strlen($start)), $len);
	}

	private static function getHtml($url, $referer, $header) {
		$curl = new Curl();
		$referer ? $curl->setReferer($referer) : '';
		$header ? $curl->setHeader($header) : '';
		$content = $curl->get($url);
		if ($content) {
			return $content;
		} else {
			return self::getHtml($url, $referer, $header);
		}
	}

	/**
	 * 获取上一章  章节详情
	 * ======
	 * @author 简强
	 * @version 17.5.8
	 */
	private static function getChapter($id) {
		//计算小说上一章ID
		$_id = explode('_', $id);
		$num = (int) $_id[0] - 1;
		$str = '';
		for ($i = 1; $i <= (5 - strlen((string) $num)); $i++) {
			$str .='0';
		}
		$id = $str . $num . '_' . $_id[1];
		$chapter = new Chapter();
		return $chapter->getInfo($id);
	}

}