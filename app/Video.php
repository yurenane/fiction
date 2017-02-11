<?php

namespace App;

use Illuminate\Support\Facades\DB;
use App\PrintCss;
use phpQuery;
use App\Curl;

class Video {

	private $type = array(
	  'comedy' => 0, //喜剧
	  'music' => 1, //音乐
	  'adventure' => 2, //冒险
	  'drama' => 3, //剧情
	  'thriller' => 4, //惊悚
	  'suspense' => 5, //悬疑
	  'action' => 6, //动作
	  'love' => 7, //爱情
	  'family' => 8, //家庭
	  'fantasy' => 9, //奇幻
	  'animation' => 10, //动画
	  'crime' => 11, //犯罪
	  'terror' => 12, //恐怖
	  'science-fiction' => 13, //科幻
	  'documentary' => 14, //纪录片
	  'history' => 15, //历史
	  'war' => 16, //战争
	  'video' => 17, //短片
	  'ancient-costume' => 18, //古装
	  'dance' => 19, //歌舞
	  'ethics' => 21, //伦理
	  'biographies' => 22, //传记
	  'west' => 23, //西部
	  'emprize' => 24, //武侠
	  'micro-film' => 25, //微电影
	);

	/**
	 * 获取视频信息
	 * ======
	 * @author 简强
	 * @version 17.1.20
	 */
	public function getVideo() {
		$this->bluRaydisc(); //蓝光电影网
	}

	private function bluRaydisc() {//http://blu-raydisc.tv/
		$month = array('一月' => '01', '二月' => '02', '三月' => '03', '四月' => '04', '五月' => '05', '六月' => '06', '七月' => '07', '八月' => '08', '九月' => '09', '十月' => '10', '十一月' => '11', '十二月' => '12');
		$url = 'http://blu-raydisc.tv/film/720p-1080p/';
		phpQuery::newDocumentHTML($this->getHtml($url, 'http://blu-raydisc.tv', array('Host:blu-raydisc.tv')));
		$list = pq('#gkComponent .teaser-item');
		foreach ($list as $val) {
			$title = pq($val)->find('a')->text();
			$_url = pq($val)->find('a')->attr('href');
			phpQuery::newDocumentHTML($this->getHtml('http://blu-raydisc.tv' . $_url, $url, array('Host:blu-raydisc.tv')));
			$info=$this->clear(pq('.pos-specification')->text());
			$time = explode(' ', $this->strcut('发行日期: ', '', pq('.element-date')->text()));
			$insert = array(
			  'id' => uniqid(),
			  'name' => $title,
			  'e_name' => $this->strcut('	英文名:', '	中文名:', $info),
			  'c_name' => strpos('别名', $info)?$this->strcut('	中文名:', '	别名:', $info):$this->strcut('	中文名:', '	分类:', $info),
			  'o_name' => strpos('别名', $info)?$this->strcut('	别名:', '', $info):'',
			  'type' => '',
			  'country' => $this->strcut('英文名:  ', '', pq('.pos-specification')->find('li')->eq(0)->text()),
			  'publish_time' => strtotime($time[2] . '-' . $month[$time[1]] . '-' . $time[0]),
			  'url' => $this->strcut('英文名:  ', '', pq('.pos-specification')->find('li')->eq(0)->text()),
			  'publisher' => $this->strcut('英文名:  ', '', pq('.pos-specification')->find('li')->eq(0)->text()),
			  'language' => '',
			  'format' => '',
			  'resolution' => '',
			  'clarity' => '',
			  'time' => '',
			  'size' => '',
			  'link' => '',
			  'subtitle' => '',
			  'detail' => '',
			  'score' => '',
			  'create_time' => time(),
			  'status' => 1,
			);
			PrintCss::r($insert);
//			DB::table('video')->insert($insert);
//			echo $title.'=>'.$_url.'=>'.strtotime($this->strcut('发行日期: ','',$time)).'=>'.$time.'=>'.  uniqid().'<br />';
			var_dump($time);
			echo $time[2] . '-' . $month[$time[1]] . '-' . $time[0] . '<br />';
			echo strtotime($time[2] . '-' . $month[$time[1]] . '-' . $time[0]);
			exit;
		}
	}
	/**
	 * 文本内容清理
	 * ======
	 * @author 简强
	 * @version 17.1.12
	 */
	private  function clear($content) {
		return str_replace(array("\r\n", "\r", "\n", ' '), '', $content);
	}
	/**
	 * 字符串剪切
	 * ======
	 * @author 简强
	 * @version 17.1.12
	 */
	private function strcut($start, $end, $string) {
		$len = strlen($string) - strpos($string, $start) - strlen($start);
		$len -=$end ? (strlen($string) - strpos($string, $end)) : 0;
		return substr($string, (strpos($string, $start) + strlen($start)), $len);
	}

	private function getHtml($url, $referer, $header) {
		$curl = new Curl();
		$referer ? $curl->setReferer($referer) : '';
		$header ? $curl->setHeader($header) : '';
		$content = $curl->get($url);
		if ($content) {
			return $content;
		} else {
			$this->getHtml($url, $referer, $header);
		}
	}

}
