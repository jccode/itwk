<?php

/**
 * this not free,powered by keke-tech
 * @auther 九江
 * 
 */

class keke_base_class {
	
	/**
	 * 方法功能的描述与说明
	 * 过滤form提交的$GET和$POST的值
	 * @parmers string $string
	 *
	 * @return 数据类型 (string)
	 */
	static public function k_addslashes($string) {
		
		if (is_array ( $string )) {
			$key = array_keys ( $string );
			$s = sizeof ( $key );
			for($i = 0; $i < $s; $i ++) {
				$string [$key [$i]] = self::k_addslashes ( $string [$key [$i]] );
			}
		} else {
			$string = addslashes ( self::escape(trim ( $string )) );
		}
		return $string;
	}
	
	/**
	 * 
	 * Enter 字符串星号装换
	 * @param string $str   
	 * @param int $start    开始位置
	 * @param int $end		倒数多少个字符结束
	 * @param int $start_num  要替换成多少星星
	 * @param string $preg_str  要替换的特殊字符串
	 */
	public static function set_star($str, $start, $end, $start_num = 3, $preg_str = "*") {
		$start = '{' . $start . '}';
		$end = '{' . $end . '}';
		preg_match_all ( "/(.{$start})(.*)(.{$end})/", $str, $matches );
		$start_str = $matches [1];
		$str = $matches [2];
		$end_str = $matches [3];
		$replace_str = str_repeat ( $preg_str, $start_num );
		$str = str_replace ( $str, $replace_str, $str );
		$star_str = $start_str [0] . $str [0] . $end_str [0];
		return $star_str;
	}
	
	static public function k_stripslashes($string) {
		if (is_array ( $string )) {
			$key = array_keys ( $string );
			$s = sizeof ( $key );
			for($i = 0; $i < $s; $i ++) {
				$string [$key [$i]] = self::k_stripslashes ( $string [$key [$i]] );
			}
		} else {
			$string = stripcslashes ( trim ( $string ) );
		}
		return $string;
	}
	static public function k_input($_r) {
		if (! get_magic_quotes_gpc ()) {
			return kekezu::k_addslashes ( $_r );
		} else {
			//return kekezu::k_addslashes ( $_r );
			return $_r;
		}
	}
	
	static public function refer_url() {
		global $_K;
		$url = $_SERVER ['REQUEST_URI'];
		if (stristr ( $url, 'do=login' )) {
			$url = str_replace ( "do=login", "do=register", $url );
		} elseif (stristr ( $url, 'do=register' )) {
			$url = str_replace ( "do=register", "do=login", $url );
		}
		if (stristr ( $url, '&refer' )) {
			$_K ['refer'] = str_replace ( "refer=", "", strstr ( $url, "refer" ) );
			return $url;
		} else {
			$refer_url = $url . "&refer=" . $_SERVER ['HTTP_REFERER'];
			$_K ['refer'] = $_SERVER ['HTTP_REFERER'];
			return $refer_url;
		}
	
	}
	/**
	 * 按主键重组数组
	 *
	 * @param 数组 $arr        	
	 * @param 键名字 $key        	
	 */
	static public function get_arr_by_key($arr = array(), $key) {
		$size = sizeof ( $arr );
		$tmp = array ();
		for($i = 0; $i < $size; $i ++) {
			$tmp [$arr [$i] [$key]] = $arr [$i];
		}
		return $tmp;
	}
	static function escape($string) {
		$search = array ('/</i', '/>/i', '/\">/i', '/\bunion\b/i', '/load_file(\s*(\/\*.*\*\/)?\s*)+\(/i', '/into(\s*(\/\*.*\*\/)?\s*)+outfile/i', '/\bor\b/i', '/\<([\/]?)script([^\>]*?)\>/si', '/\<([\/]?)iframe([^\>]*?)\>/si', '/\<([\/]?)frame([^\>]*?)\>/si' );
		$replace = array ('&lt;', '&gt;', '&quot;&gt;', 'union&nbsp;', 'load_file&nbsp; (', 'into&nbsp; outfile', 'or&nbsp;', '&lt;\\1script\\2&gt;', '&lt;\\1iframe\\2&gt;', '&lt;\\1frame\\2&gt;' );
		if (is_array ( $string )) {
			$key = array_keys ( $string );
			$size = sizeof ( $key );
			for($i = 0; $i < $size; $i ++) {
				$string [$key [$i]] = self::escape ( $string [$key [$i]] );
			}
		} else {
			$string = self::filter_input(str_replace ( array ('\n', '\r' ), array (chr ( 10 ), chr ( 13 ) ), preg_replace ( $search, $replace, $string ) ));
		}
		
		return $string;
	}
	/**
	 * 过滤通过文本输入的内容中的特殊标签
	 * [input|textarea|button|marquee|iframe|frame...]
	 * Enter description here ...
	 * @param unknown_type $string
	 */
	static function filter_input($str) {
		//$search = array('/<(input|textarea|select|button|marquee|iframe|frame|form|script)/iU','/<a(.*)>(.*)<\/a>/iU');
		$search = array('/<(input|textarea|select|button|marquee|iframe|frame|form|script)/iU');
		$replace= array('< \\1','\\2');
		if (is_array ( $str )) {
			$key = array_keys ( $str );
			$s = sizeof ( $str );
			for($i = 0; $i < $s; $i ++) {
				$str [$key [$i]] = self::filter_input ( $str [$key [$i]] );
			}
		} else {
			$str = htmlspecialchars_decode ( $str );
			$str = preg_replace ($search,$replace, $str );
		}
		return $str;
	}
	
	static function filter_xss() {
		global $_lang;
		keke_lang_class::loadlang ( 'public', 'public' );
		$temp = strtoupper ( urldecode ( urldecode ( $_SERVER ['REQUEST_URI'] ) ) );
		/*
		if (strpos ( $temp, '<' ) !== false || strpos ( $temp, '>' ) !== false || strpos ( $temp, '"' ) !== false || strpos ( $temp, 'CONTENT-TRANSFER-ENCODING' ) !== false) {
			
			kekezu::show_msg ( $_lang ['operate_notice'], "index.php", 9999, $_lang ['xss_attack_warning_notice'], "error" );
			die ();
		}*/
		return true;
	
	}
	/**
	 * 无限分类显示
	 *
	 * @param 输入 $array        	
	 * @param 输出 $temp_arr        	
	 * @param 显示方式 $out
	 * option=><select> !option=>array()
	 * @param 选中项索引 $index
	 * selected=selected
	 */
	static function get_tree($array, &$temp_arr, $out = 'option', $index = null, $id = 'indus_id', $pid = 'indus_pid', $name = 'indus_name') {
		$tree = array ();
		
		if ($array) {
			foreach ( $array as $v ) {
				$pt = $v [$pid];
				$list = @$tree [$pt] ? $tree [$pt] : array ();
				array_push ( $list, $v );
				$tree [$pt] = $list;
			}
		}
		if ($tree) {
			
			$tree [0] or $tree [0] = $tree [$array [0] [$pid]];
			
			foreach ( $tree [0] as $k => $v ) {
				if ($out == 'option') {
					if ($index == $v [$id]) {
						$temp_arr [] = "<option value=$v[$id] selected=selected>$v[$name]</option>";
					} else {
						$temp_arr [] = "<option value=$v[$id]>$v[$name]</option>";
					}
				
				} elseif ($out == 'cat') {
					$v ['ext'] = '';
					$temp_arr [] = $v;
				} else {
					
					isset ( $v [$name] ) and $v ['ext'] = $v [$name];
					$v ['level'] = 0;
					$temp_arr [] = $v;
				
				}
				if ($tree [$v [$id]])
					self::draw_tree ( $tree [$v [$id]], $tree, 0, $temp_arr, $out, $index, $id, $pid, $name );
			}
		
		}
	}
	
	static function draw_tree($arr, $tree, $level, &$temp_arr, $out, $index, $id, $pid, $name) {
		
		$level ++;
		
		$prefix = str_pad ( " |", $level + 2, '---', STR_PAD_RIGHT );
		$n = str_pad ( '', $level + 2, '--', STR_PAD_RIGHT );
		$n = str_replace ( "-", "&nbsp;", $n );
		foreach ( $arr as $k2 => $v2 ) {
			if ($out == 'option') {
				if ($index == $v2 [$id]) {
					$temp_arr [] = "<option value={$v2[$id]} selected=selected>$n$prefix$v2[$name]</option>";
				} else {
					$temp_arr [] = "<option value={$v2[$id]}>$n$prefix$v2[$name]</option>";
				}
			
			} elseif ($out == 'cat') {
				$v2 ['ext'] = $n . $prefix;
				$v2 ['level'] = $level;
				$temp_arr [] = $v2;
			} else {
				// ┗
				

				isset ( $v2 [$name] ) and $v2 ['ext'] = $n . "┣" . $v2 [$name];
				$v2 ['level'] = $level;
				$temp_arr [] = $v2;
			
			}
			if (isset ( $tree [$v2 [$id]] )) {
				self::draw_tree ( $tree [$v2 [$id]], $tree, $level, $temp_arr, $out, $index, $id, $pid, $name );
			}
		
		}
	
	}
	
	static function gbktoutf($string) {
		
		$string = self::charset_encode ( "gb2312", "utf-8", $string );
		return $string;
	}
	static function utftogbk($string) { // gbk是gb2312(简体中文)的扩展,包括繁体等
		

		$string = self::charset_encode ( "utf-8", "gb2312", $string );
		return $string;
	}
	
	static function objtoarray($obj) {
		if (is_object ( $obj )) {
			$obj = ( array ) $obj;
		}
		if ($obj)
			foreach ( $obj as $k => $o ) {
				if (is_object ( $o ) || is_array ( $o )) {
					$obj [$k] = kekezu::objtoarray ( $o );
				}
			}
		return $obj;
	
	}
	
	static function charset_encode($_input_charset, $_output_charset, $input) {
		$output = "";
		$string = $input;
		if (is_array ( $input )) {
			$key = array_keys ( $string );
			$size = sizeof ( $key );
			for($i = 0; $i < $size; $i ++) {
				$string [$key [$i]] = self::charset_encode ( $_input_charset, $_output_charset, $string [$key [$i]] );
			}
			return $string;
		} else {
			if (! isset ( $_output_charset ))
				$_output_charset = $_input_charset;
			if ($_input_charset == $_output_charset || $input == null) {
				$output = $input;
			} elseif (function_exists ( "mb_convert_encoding" )) {
				$output = mb_convert_encoding ( $input, $_output_charset, $_input_charset );
			} elseif (function_exists ( "iconv" )) {
				$output = iconv ( $_input_charset, $_output_charset, $input );
			} else
				die ( "sorry, you have no libs support for charset change." );
			
			return $output;
		}
	}
	/**
	 * echo json
	 *
	 * @param unknown_type $msg        	
	 * @param unknown_type $status        	
	 * @param unknown_type $dataarr        	
	 */
	static function echojson($msg = '', $status = 0, $dataarr = array()) {
		global $_K;
		if ($_K ['charset'] == 'gbk') {
			$msg = self::gbktoutf ( $msg );
			$status = self::gbktoutf ( $status );
			$dataarr = self::gbktoutf ( $dataarr );
		}
		
		$arr = array ('msg' => $msg, 'status' => $status, 'data' => $dataarr );
		echo self::json_encode_k ( $arr );
		exit ();
	}
	static function json_encode_k($array) {
		if (function_exists ( 'json_encode' )) {
			return json_encode ( $array );
		} else {
			require_once S_ROOT . 'lib/helper/keke_json_class.php';
			$json_obj = new json ();
			return $json_obj->encode ( $array );
		}
	}
	
	// strtotime的重写， 如果不重置时间区， 会自动的被减去8小时
	static function sstrtotime($time, $now = null) {
		date_default_timezone_set ( 'Etc/GMT' );
		$time = strtotime ( $time, $now );
		date_default_timezone_set ( 'Asia/Shanghai' );
		return $time;
	}
	/**
	 * 随机生成字符串
	 *
	 * @param Int $length        	
	 * @return String Time Elapsed
	 * @author shangjinglong
	 * @copyright keke-tech
	 */
	static function randomkeys($length) {
		$key = null;
		$pattern = '1234567890abcdefghijklmnopqrstuvwxyz
                   ABCDEFGHIJKLOMNOPQRSTUVWXYZ'; // 字符池
		for($i = 0; $i < $length; $i ++) {
			$key .= $pattern {mt_rand ( 0, 35 )}; // 生成php随机数
		}
		return $key;
	
	}
	/**
	 * 获取随即客服
	 */
	static function get_rand_kf() {
		$kf_arr = kekezu::get_table_data ( 'uid', 'witkey_space', ' group_id = 7' );
		$kf_arr_count = count ( $kf_arr );
		$randno = rand ( 0, $kf_arr_count - 1 );
		return $kf_uid = $kf_arr [$randno] [uid] ? $kf_arr [$randno] [uid] : ADMIN_UID;
	}
	
	/**
	 *
	 *
	 *
	 * 将时间戳转化为
	 *
	 * @param unknown_type $time        	
	 */
	public static function time2Units($time, $limit = null) {
		global $_lang;
		$tt = getdate ();
		$tt = $tt ['year'];
		
		$year = floor ( $time / 60 / 60 / 24 / 365 );
		$time -= $year * 60 * 60 * 24 * 365;
		$month = floor ( $time / 60 / 60 / 24 / 30 );
		$time -= $month * 60 * 60 * 24 * 30;
		$day = floor ( $time / 60 / 60 / 24 );
		$time -= $day * 60 * 60 * 24;
		$hour = floor ( $time / 60 / 60 );
		$time -= $hour * 60 * 60;
		$minute = floor ( $time / 60 );
		$time -= $minute * 60;
		$second = $time;
		$elapse = '';
		$unitArr = array ($_lang ['year'] => 'year', $_lang ['months'] => 'month', $_lang ['day'] => 'day', $_lang ['hour'] => 'hour', $_lang ['minute'] => 'minute' );
		
		foreach ( $unitArr as $cn => $u ) {
			if ($$u > 0) {
				$elapse .= $$u . $cn;
			
			}
			if ($u == $limit) {
				return $elapse;
			}
		}
		return $elapse;
	}
	
	/**
	 * 时间差计算
	 *
	 * @param Timestamp $time        	
	 * @return String Time Elapsed
	 * @author shangjinglong
	 * @copyright keke-tech
	 */
 	
	static function time_to_units($end_time) {
		global $_lang;
		$now = time (); // 当前时间
		$res = $end_time - $now;
		$oneday = 24 * 60 * 60; // 一天的时间秒数
		$onehour = 60 * 60; // 一小时秒数
		if ($res <= 0) {
			$res = $_lang ['choosing'];
//		} else if ($res > 0 && $res < $oneday) {
//			$res = $_lang ['going_to_expired'];
		} else {
			if ($res / $oneday > 0) {
				$day = floor ( $res / $oneday ); // 天
				$left_sec = $res % $oneday; // 剩余的秒
				

				if ($left_sec / $onehour > 0) {
					$hour = number_format ( ($left_sec / $oneday) * 24, 0 ); // 小时
				} else
					$hour = 0; // 小时
			} else {
				$day = 0; // 天
				$left_sec = $res % $oneday; // 剩余的秒
				if ($left_sec / $onehour > 0) {
					$hour = number_format ( ($left_sec / $oneday) * 24, 0 ); // 小时
				} else
					$hour = 0; // 小时
			}
			$res = $day . $_lang ['day'] . $hour . $_lang ['hour'];
		}
		return $res;
	} 
	
	/**
	 * 同城配送短信时间差计算
	 *
	 * @param Timestamp $vip_end_time VIP结束时间
	 * @param Timestamp $match_end_time 短信配送结束时间
	 * @return array Time Elapsed,day
	 * @author ch
	 * @copyright keke-tech
	 */
	static function match_time_to_units($vip_end_time,$match_end_time){
		global $_lang;
		$now = time();
		if(!$match_end_time){
			$match_end_time = 0;
		}
		$res = $vip_end_time - $now;
		$match = $match_end_time - $now;
		$oneday = 24 * 60 * 60; // 一天的时间秒数
		$onehour = 60 * 60; // 一小时秒数
		if($res<=0){
			$status = 0; //VIP到期
			$res=array("vip_left"=>"0","month"=>"0天","day"=>"0","status"=>$status);
		}else{
			//计算VIP剩余天数
			if($res / $oneday >0){
				$vip_day = floor ( $res / $oneday ); // 天
				if($vip_day / 30 > 0){
					$vip_month = floor($vip_day / 30); //月
					$vip_left_day = $vip_day % 30; //剩余天数
				}else{
					$vip_month = 0;
					$vip_left_day = $vip_day;
				}
			}else{
				$vip_day = 0;
				$vip_month = 0;
				$vip_left_day = 0;
			}
			if($vip_month>0){
				$vip_left = $vip_month."个月".$vip_left_day."天";
			}else{
				$vip_left = $vip_left_day."天";
			}
			
			$status = 1; //短信配送已到期
			if($match>0){
				$res = $vip_end_time - $match_end_time;
				$status = 2; //短信配送未到期
				if($res / $oneday >0){
					$day = floor ( $res / $oneday ); // 天
					if($day / 30 > 0){
						$month = floor($day / 30); //月
						$left_day = $day % 30; //剩余天数
					}else{
						$month = 0;
						$left_day = $day;
					}
				}else{
					$day = 0;
					$month = 0;
					$left_day = 0;
				}
			}else{
				$day = $vip_day;
				$month = $vip_month;
				$left_day = $vip_left_day;
			}
			
			if($month>0){
				$res = array("vip_left"=>$vip_left,"month"=>$month."个月".$left_day."天","day"=>$day,"status"=>$status);
			}else{
				$res = array("vip_left"=>$vip_left,"month"=>$left_day."天","day"=>$day,"status"=>$status);
			}
		}
		return $res;
	}
	
	// 用户中心－图片更新 end
	static function cutstr($string, $length, $in_slashes = 0, $out_slashes = 0, $censor = '', $html = 0) {
		global $_K;
		
		$string = trim ( $string );
		
		if ($in_slashes) {
			// 传入的字符有slashes
			$string = stripslashes ( $string );
		}
		if ($html < 0) {
			// 去掉html标签
			$string = preg_replace ( "/(\<[^\<]*\>|\r|\n|\s|\[.+?\])/is", ' ', $string );
			$string = htmlspecialchars ( $string );
		} elseif ($html == 0) {
			// 转换html标签
			$string = htmlspecialchars ( $string );
		}
		if ($length && strlen ( $string ) > $length) {
			// 截断字符
			$wordscut = '';
			if ($_K ['charset'] == 'utf-8') {
				// utf8编码
				$n = 0;
				$tn = 0;
				$noc = 0;
				while ( $n < strlen ( $string ) ) {
					$t = ord ( $string [$n] );
					if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
						$tn = 1;
						$n ++;
						$noc ++;
					} elseif (194 <= $t && $t <= 223) {
						$tn = 2;
						$n += 2;
						$noc += 2;
					} elseif (224 <= $t && $t < 239) {
						$tn = 3;
						$n += 3;
						$noc += 2;
					} elseif (240 <= $t && $t <= 247) {
						$tn = 4;
						$n += 4;
						$noc += 2;
					} elseif (248 <= $t && $t <= 251) {
						$tn = 5;
						$n += 5;
						$noc += 2;
					} elseif ($t == 252 || $t == 253) {
						$tn = 6;
						$n += 6;
						$noc += 2;
					} else {
						$n ++;
					}
					if ($noc >= $length) {
						break;
					}
				}
				if ($noc > $length) {
					$n -= $tn;
				}
				$wordscut = substr ( $string, 0, $n );
			} else {
				for($i = 0; $i < $length - 1; $i ++) {
					if (ord ( $string [$i] ) > 127) {
						$wordscut .= $string [$i] . $string [$i + 1];
						$i ++;
					} else {
						$wordscut .= $string [$i];
					}
				}
			}
			$string = $wordscut . $censor;
		}
		
		if ($out_slashes) {
			$string = addslashes ( $string );
		}
		$string = htmlspecialchars_decode ( $string );
		return trim ( $string );
	}
	/**
	 * 关键词过滤
	 */
	static function str_filter($content = '') {
		global $basic_config;
		if (is_array ( $content )) {
			foreach ( $content as $k => $v ) {
				$content [$k] = self::str_filter ( $v );
			}
			return $content;
		} else {
			$basic_info = $basic_config;
			$censor = $basic_info [ban_content];
			if (empty ( $censor ) || $content == '*' || $content == '?') {
				return $content;
			}
			$censor_arr = explode ( '|', $censor );
			$censor_arr = array_filter ( $censor_arr );
			foreach ( $censor_arr as $v ) {
				if (! ($v == '*' || $v == '?')) {
					$find [] = '/' . $v . '/i';
					$replase [] = '*';
				}
			}
			
			return preg_replace ( $find, $replase, $content );
		
		}
	}
	// 用户名禁用词匹配
	static function k_strpos($k) {
		global $basic_config;
		$user_arr = explode ( '|', $basic_config ['ban_users'] );
		$r = '';
		foreach ( $user_arr as $value ) {
			if (preg_match ( '/' . $value . '/', $k )) {
				$r = $value;
				break;
			}
		}
		return $r ? true : false;
	}
	// 在内容中匹配关键字
	public static function k_match($k_arr, $content) {
		$m = 0;
		foreach ( $k_arr as $value ) {
			if (preg_match ( '/' . $value . '/', $content )) {
				$m += 1;
			}
		}
		return $m;
	
	}
	// 安装锁定检查
	static function check_install() {
		global $_lang;
		$lock_file = S_ROOT . './data/keke_kppw_install.lck';
		
		die ();
		file_exists ( $lock_file ) == false or kekezu::show_msg ( $_lang ['kppw_install_notice'], 'install/index.php', 3, $_lang ['you_not_install_kppw_notice'] );
	}
	// 时间计算
	static function get_gmdate($timestamp) {
		global $_lang;
		global $_K;
		$time = $_K ['timestamp'] - $timestamp;
		if ($time > 24 * 3600) {
			$result = intval ( $time / (24 * 3600) ) . $_lang ['day_before'];
		} elseif ($time > 3600) {
			$result = intval ( $time / 3600 ) . $_lang ['hour_before'];
		} elseif ($time > 60) {
			$result = intval ( $time / 60 ) . $_lang ['minute_before'];
		} elseif ($time > 0) {
			$result = $time . $_lang ['seconds_before'];
		} else {
			$result = $_lang ['now'];
		}
		return $result;
	}
	
	/**
	 * 生成表单hash
	 *
	 * @return hash值
	 */
	static function formhash() {
		$uid = null;
		$username = null;
		if (isset ( $_SESSION ['uid'] )) {
			$uid = $_SESSION ['uid'];
		}
		if (isset ( $_SESSION ['username'] )) {
			$username = $_SESSION ['username'];
		}
		return substr ( md5 ( substr ( time (), 0, - 7 ) . $uid . $username . ENCODE_KEY ), - 6 );
	}
	/**
	 *
	 * @todo 表单检查防止重复提交与跨服务器提交
	 * @param $var hash值        	
	 * @param $return_json 指定返回值类型(返回bool值,还是直接show_msg)        	
	 *
	 */
	static function submitcheck($var, $return_json = false) {
		global $_lang;
		global $_K, $kekezu;
		if (! empty ( $var ) && $_SERVER ['REQUEST_METHOD'] == 'POST') {
			if ((empty ( $_SERVER ['HTTP_REFERER'] ) || preg_replace ( "/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER ['HTTP_REFERER'] ) == preg_replace ( "/([^\:]+).*/", "\\1", $_SERVER ['HTTP_HOST'] )) && $var == FORMHASH) {
				return true;
			} elseif ($return_json == true) {
				return false;
			} elseif ($_K [inajax]) {
				kekezu::show_msg ( $_lang ['operate_error'], "", 5, $_lang ['repeat_form_submit'], 'alert_error' );
			} else {
				kekezu::show_msg ( $_lang ['operate_error'], "index.php", 30, $_lang ['illegal_or_repeat_submit'], 'alert_error' );
			}
		} else {
			return false;
		}
	}
	static function curl_file_get_contents($durl) {
		
		$ch = curl_init ();
		
		curl_setopt ( $ch, CURLOPT_URL, $durl );
		
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_TIMEOUT, 5 );
		
		curl_setopt ( $ch, CURLOPT_USERAGENT, _USERAGENT_ );
		
		curl_setopt ( $ch, CURLOPT_REFERER, _REFERER_ );
		
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		$r = curl_exec ( $ch );
		curl_close ( $ch );
		
		return $r;
	}
	static function show_error($data = '') {
		require keke_tpl_class::template ( 'tpl/default/show_error' );
	}
	static function get_ip() {
		global $_lang;
		if (! empty ( $_SERVER ["HTTP_CLIENT_IP"] ))
			$cip = $_SERVER ["HTTP_CLIENT_IP"];
		else if (! empty ( $_SERVER ["HTTP_X_FORWARDED_FOR"] ))
			$cip = $_SERVER ["HTTP_X_FORWARDED_FOR"];
		else if (! empty ( $_SERVER ["REMOTE_ADDR"] ))
			$cip = $_SERVER ["REMOTE_ADDR"];
		else
			$cip = $_lang ['can_not_get'];
		return $cip;
	}
	/**
	 * 处理js escape 编码
	 */
	static function unescape($escstr) {
		
		preg_match_all ( "/%u[0-9A-Za-z]{4}|%.{2}|[0-9a-zA-Z.+-_]+/", $escstr, $matches );
		$ar = &$matches [0];
		$c = "";
		foreach ( $ar as $val ) {
			if (substr ( $val, 0, 1 ) != "%") {
				$c .= $val;
			} elseif (substr ( $val, 1, 1 ) != "u") {
				$x = hexdec ( substr ( $val, 1, 2 ) );
				$c .= chr ( $x );
			} else {
				$val = intval ( substr ( $val, 2 ), 16 ); // 0000-007F
				if ($val < 0x7F) {
					$c .= chr ( $val ); // 0080-0800
				} elseif ($val < 0x800) {
					$c .= chr ( 0xC0 | ($val / 64) );
					$c .= chr ( 0x80 | ($val % 64) ); // 0800-FFFF
				} else {
					$c .= chr ( 0xE0 | (($val / 64) / 64) );
					$c .= chr ( 0x80 | (($val / 64) % 64) );
					$c .= chr ( 0x80 | ($val % 64) );
				}
			}
		}
		strtolower ( CHARSET ) == 'gbk' and $c = self::utftogbk ( $c );
		return $c;
	}
	
	static function is_email($email) {
		return strlen ( $email ) > 6 && preg_match ( "/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email );
	}
	static function is_mobile($mobile) {
		return preg_match ( "/^13[0-9]{1}[0-9]{8}$|^15[0189]{1}[0-9]{8}$|^18[0-9]{9}$/", $mobile );
	}
	static function socket_request($url, $sim = true, $time_out = "60") {
		$sim and $url .= "&sim_request=1";
		$urlarr = parse_url ( $url );
		$input_charset = strtolower ( CHARSET );
		$errno = "";
		$errstr = "";
		$transports = "";
		$responseText = "";
		if ($urlarr ["scheme"] == "https") {
			$transports = "ssl://";
			$urlarr ["port"] = "443";
		} else {
			$transports = "tcp://";
			$urlarr ["port"] = "80";
		}
		$fp = @fsockopen ( $transports . $urlarr ['host'], $urlarr ['port'], $errno, $errstr, $time_out );
		if (! $fp) {
			die ( "ERROR: $errno - $errstr<br />\n" );
		} else {
			if (trim ( $input_charset ) == '') {
				fputs ( $fp, "POST " . $urlarr ["path"] . " HTTP/1.1\r\n" );
			} else {
				fputs ( $fp, "POST " . $urlarr ["path"] . '?_input_charset=' . $input_charset . " HTTP/1.1\r\n" );
			}
			fputs ( $fp, "Host: " . $urlarr ["host"] . "\r\n" );
			fputs ( $fp, "Content-type: application/x-www-form-urlencoded\r\n" );
			fputs ( $fp, "Content-length: " . strlen ( $urlarr ["query"] ) . "\r\n" );
			fputs ( $fp, "Connection: close\r\n\r\n" );
			fputs ( $fp, $urlarr ["query"] . "\r\n\r\n" );
			while ( ! feof ( $fp ) ) {
				$responseText .= @fgets ( $fp, 1024 );
			}
			fclose ( $fp );
			$responseText = trim ( stristr ( $responseText, "\r\n\r\n" ), "\r\n" );
			return $responseText;
		}
	}
	public static function curl_request($url, $sim = true, $method = "get", $postfields = NULL) {
		$sim or $url .= "&sim_request=1";
		$ci = curl_init ();
		curl_setopt ( $ci, CURLOPT_URL, $url );
		curl_setopt ( $ci, CURLOPT_HEADER, FALSE );
		curl_setopt ( $ci, CURLOPT_RETURNTRANSFER, TRUE );
		curl_setopt ( $ci, CURLOPT_USERAGENT, $_SERVER ['HTTP_USER_AGENT'] );
		curl_setopt ( $ci, CURLOPT_SSL_VERIFYPEER, 0 );
		curl_setopt ( $ci, CURLOPT_SSL_VERIFYHOST, 0 );
		if ('post' == strtolower ( $method )) {
			curl_setopt ( $ci, CURLOPT_POST, TRUE );
			if (is_array ( $postfields )) {
				$field_str = "";
				foreach ( $postfields as $k => $v ) {
					$field_str .= "&$k=" . urlencode ( $v );
				}
				curl_setopt ( $ci, CURLOPT_POSTFIELDS, $field_str );
			}
		}
		$response = curl_exec ( $ci );
		if (curl_errno ( $ci )) {
			throw new Exception ( curl_error ( $ci ), 0 );
		} else {
			$httpStatusCode = curl_getinfo ( $ci, CURLINFO_HTTP_CODE );
			if (200 !== $httpStatusCode) {
				throw new Exception ( $response, $httpStatusCode );
			}
		}
		curl_close ( $ci );
		return $response;
	
	}
	/**
	 * 远程获取数据
	 * @param $url 远程链接
	 */
	static function get_remote_data($url, $sim = false) {
		if(!$url){
			return false;
		}
		if ($sim == true) {
			if (function_exists ( 'fsockopen' )) {
				return self::socket_request ( $url, false );
			} elseif (function_exists ( 'curl_init' )) {
				return self::curl_request ( $url, false, 'post' );
			}
		} else {
			if (function_exists ( 'file_get_contents' )) {
				return file_get_contents ( $url );
			}
		}
	}
	/**
	 * 中文转化为拼音
	 */
	public static function get_pinyin($_String, $_Code = 'gb2312') {
		$_DataKey = "a|ai|an|ang|ao|ba|bai|ban|bang|bao|bei|ben|beng|bi|bian|biao|bie|bin|bing|bo|bu|ca|cai|can|cang|cao|ce|ceng|cha" . "|chai|chan|chang|chao|che|chen|cheng|chi|chong|chou|chu|chuai|chuan|chuang|chui|chun|chuo|ci|cong|cou|cu|" . "cuan|cui|cun|cuo|da|dai|dan|dang|dao|de|deng|di|dian|diao|die|ding|diu|dong|dou|du|duan|dui|dun|duo|e|en|er" . "|fa|fan|fang|fei|fen|feng|fo|fou|fu|ga|gai|gan|gang|gao|ge|gei|gen|geng|gong|gou|gu|gua|guai|guan|guang|gui" . "|gun|guo|ha|hai|han|hang|hao|he|hei|hen|heng|hong|hou|hu|hua|huai|huan|huang|hui|hun|huo|ji|jia|jian|jiang" . "|jiao|jie|jin|jing|jiong|jiu|ju|juan|jue|jun|ka|kai|kan|kang|kao|ke|ken|keng|kong|kou|ku|kua|kuai|kuan|kuang" . "|kui|kun|kuo|la|lai|lan|lang|lao|le|lei|leng|li|lia|lian|liang|liao|lie|lin|ling|liu|long|lou|lu|lv|luan|lue" . "|lun|luo|ma|mai|man|mang|mao|me|mei|men|meng|mi|mian|miao|mie|min|ming|miu|mo|mou|mu|na|nai|nan|nang|nao|ne" . "|nei|nen|neng|ni|nian|niang|niao|nie|nin|ning|niu|nong|nu|nv|nuan|nue|nuo|o|ou|pa|pai|pan|pang|pao|pei|pen" . "|peng|pi|pian|piao|pie|pin|ping|po|pu|qi|qia|qian|qiang|qiao|qie|qin|qing|qiong|qiu|qu|quan|que|qun|ran|rang" . "|rao|re|ren|reng|ri|rong|rou|ru|ruan|rui|run|ruo|sa|sai|san|sang|sao|se|sen|seng|sha|shai|shan|shang|shao|" . "she|shen|sheng|shi|shou|shu|shua|shuai|shuan|shuang|shui|shun|shuo|si|song|sou|su|suan|sui|sun|suo|ta|tai|" . "tan|tang|tao|te|teng|ti|tian|tiao|tie|ting|tong|tou|tu|tuan|tui|tun|tuo|wa|wai|wan|wang|wei|wen|weng|wo|wu" . "|xi|xia|xian|xiang|xiao|xie|xin|xing|xiong|xiu|xu|xuan|xue|xun|ya|yan|yang|yao|ye|yi|yin|ying|yo|yong|you" . "|yu|yuan|yue|yun|za|zai|zan|zang|zao|ze|zei|zen|zeng|zha|zhai|zhan|zhang|zhao|zhe|zhen|zheng|zhi|zhong|" . "zhou|zhu|zhua|zhuai|zhuan|zhuang|zhui|zhun|zhuo|zi|zong|zou|zu|zuan|zui|zun|zuo";
		$_DataValue = "-20319|-20317|-20304|-20295|-20292|-20283|-20265|-20257|-20242|-20230|-20051|-20036|-20032|-20026|-20002|-19990" . "|-19986|-19982|-19976|-19805|-19784|-19775|-19774|-19763|-19756|-19751|-19746|-19741|-19739|-19728|-19725" . "|-19715|-19540|-19531|-19525|-19515|-19500|-19484|-19479|-19467|-19289|-19288|-19281|-19275|-19270|-19263" . "|-19261|-19249|-19243|-19242|-19238|-19235|-19227|-19224|-19218|-19212|-19038|-19023|-19018|-19006|-19003" . "|-18996|-18977|-18961|-18952|-18783|-18774|-18773|-18763|-18756|-18741|-18735|-18731|-18722|-18710|-18697" . "|-18696|-18526|-18518|-18501|-18490|-18478|-18463|-18448|-18447|-18446|-18239|-18237|-18231|-18220|-18211" . "|-18201|-18184|-18183|-18181|-18012|-17997|-17988|-17970|-17964|-17961|-17950|-17947|-17931|-17928|-17922" . "|-17759|-17752|-17733|-17730|-17721|-17703|-17701|-17697|-17692|-17683|-17676|-17496|-17487|-17482|-17468" . "|-17454|-17433|-17427|-17417|-17202|-17185|-16983|-16970|-16942|-16915|-16733|-16708|-16706|-16689|-16664" . "|-16657|-16647|-16474|-16470|-16465|-16459|-16452|-16448|-16433|-16429|-16427|-16423|-16419|-16412|-16407" . "|-16403|-16401|-16393|-16220|-16216|-16212|-16205|-16202|-16187|-16180|-16171|-16169|-16158|-16155|-15959" . "|-15958|-15944|-15933|-15920|-15915|-15903|-15889|-15878|-15707|-15701|-15681|-15667|-15661|-15659|-15652" . "|-15640|-15631|-15625|-15454|-15448|-15436|-15435|-15419|-15416|-15408|-15394|-15385|-15377|-15375|-15369" . "|-15363|-15362|-15183|-15180|-15165|-15158|-15153|-15150|-15149|-15144|-15143|-15141|-15140|-15139|-15128" . "|-15121|-15119|-15117|-15110|-15109|-14941|-14937|-14933|-14930|-14929|-14928|-14926|-14922|-14921|-14914" . "|-14908|-14902|-14894|-14889|-14882|-14873|-14871|-14857|-14678|-14674|-14670|-14668|-14663|-14654|-14645" . "|-14630|-14594|-14429|-14407|-14399|-14384|-14379|-14368|-14355|-14353|-14345|-14170|-14159|-14151|-14149" . "|-14145|-14140|-14137|-14135|-14125|-14123|-14122|-14112|-14109|-14099|-14097|-14094|-14092|-14090|-14087" . "|-14083|-13917|-13914|-13910|-13907|-13906|-13905|-13896|-13894|-13878|-13870|-13859|-13847|-13831|-13658" . "|-13611|-13601|-13406|-13404|-13400|-13398|-13395|-13391|-13387|-13383|-13367|-13359|-13356|-13343|-13340" . "|-13329|-13326|-13318|-13147|-13138|-13120|-13107|-13096|-13095|-13091|-13076|-13068|-13063|-13060|-12888" . "|-12875|-12871|-12860|-12858|-12852|-12849|-12838|-12831|-12829|-12812|-12802|-12607|-12597|-12594|-12585" . "|-12556|-12359|-12346|-12320|-12300|-12120|-12099|-12089|-12074|-12067|-12058|-12039|-11867|-11861|-11847" . "|-11831|-11798|-11781|-11604|-11589|-11536|-11358|-11340|-11339|-11324|-11303|-11097|-11077|-11067|-11055" . "|-11052|-11045|-11041|-11038|-11024|-11020|-11019|-11018|-11014|-10838|-10832|-10815|-10800|-10790|-10780" . "|-10764|-10587|-10544|-10533|-10519|-10331|-10329|-10328|-10322|-10315|-10309|-10307|-10296|-10281|-10274" . "|-10270|-10262|-10260|-10256|-10254";
		$_TDataKey = explode ( '|', $_DataKey );
		$_TDataValue = explode ( '|', $_DataValue );
		$_Data = (PHP_VERSION >= 5.0) ? array_combine ( $_TDataKey, $_TDataValue ) : kekezu::_Array_Combine ( $_TDataKey, $_TDataValue );
		arsort ( $_Data );
		reset ( $_Data );
		if ($_Code != 'gb2312')
			$_String =kekezu::_U2_Utf8_Gb ( $_String );
		$_Res = '';
		for($i = 0; $i < strlen ( $_String ); $i ++) {
			$_P = ord ( substr ( $_String, $i, 1 ) );
			if ($_P > 160) {
				$_Q = ord ( substr ( $_String, ++ $i, 1 ) );
				$_P = $_P * 256 + $_Q - 65536;
			}
			$_Res .= kekezu::_Pinyin ( $_P, $_Data );
		}
		return preg_replace ( "/[^a-z0-9]*/", '', $_Res );
	}
	
	public static function _Pinyin($_Num, $_Data) {
		if ($_Num > 0 && $_Num < 160)
			return chr ( $_Num );
		elseif ($_Num < - 20319 || $_Num > - 10247)
		return;
		else {
			foreach ( $_Data as $k => $v ) {
				if ($v <= $_Num)
					break;
			}
			return $k;
		}
	}
	
	public static function _U2_Utf8_Gb($_C) {
		$_String = '';
		if ($_C < 0x80)
			$_String .= $_C;
		elseif ($_C < 0x800) {
			$_String .= chr ( 0xC0 | $_C >> 6 );
			$_String .= chr ( 0x80 | $_C & 0x3F );
		} elseif ($_C < 0x10000) {
			$_String .= chr ( 0xE0 | $_C >> 12 );
			$_String .= chr ( 0x80 | $_C >> 6 & 0x3F );
			$_String .= chr ( 0x80 | $_C & 0x3F );
		} elseif ($_C < 0x200000) {
			$_String .= chr ( 0xF0 | $_C >> 18 );
			$_String .= chr ( 0x80 | $_C >> 12 & 0x3F );
			$_String .= chr ( 0x80 | $_C >> 6 & 0x3F );
			$_String .= chr ( 0x80 | $_C & 0x3F );
		}
		return iconv ( 'UTF-8', 'GB2312', $_String );
	}
	
	public static function _Array_Combine($_Arr1, $_Arr2) {
		for($i = 0; $i < count ( $_Arr1 ); $i ++)
			$_Res [$_Arr1 [$i]] = $_Arr2 [$i];
		return $_Res;
	}
	
/*
	 * 拼接获得当前url的参数部分
	 * 只拼接get部分
	 * 说明：	本来用PHP_SELF就可以直接获得的  不过考虑到$_GET的重写赋值可能  
	 * 			以及使用$pagename参数可完成提交地址转移   故创造此函数
	 * $pagename 文件名  默认当前页面
	 * 返回：url
	 * */
	static function url_get($pagename=null){
		$pagename or $pagename = $_SERVER['SCRIPT_NAME'];
		$r = "$pagename?";
		$i = 0;
		foreach ($_GET as $k=>$v){
			if(is_array($v)){
				$i and $r .="&";
				$r.="{$k}={$v}";
				$i++;
			}
		}
		return $r;
	}
	
	/*
	 * 拼接获得当前url的参数部分
	 * 只拼接post部分
	 * 说明：	将post的参数直接转为get参数url  不过对于数组提交无效
	 * $pagename 文件名  默认当前页面
	 * 返回：url
	 * */
	static function url_post($pagename=null){
		$pagename or $pagename = $_SERVER['SCRIPT_NAME'];
		$r = "$pagename?";
		$i = 0;
		foreach ($_POST as $k=>$v){
			if(is_array($v)){
				$i and $r .="&";
				$r.="{$k}={$v}";
				$i++;
			}
		}
		return $r;
	}
	
	/*
	 * 拼接获得当前url的参数部分
	 * get post全部拼接
	 * 说明：	将post的参数直接转为get 并覆盖get  不过对于数组提交无效
	 * $pagename 文件名  默认当前页面
	 * 返回：url
	 * */
	static function url_request($pagename=null){
		$pagename or $pagename = $_SERVER['SCRIPT_NAME'];
		$r = "$pagename?";
		$request = array_merge($_GET,$_POST);
		$i = 0;
		foreach ($request as $k=>$v){
			if(is_array($v)){
				$i and $r .="&";
				$r.="{$k}={$v}";
				$i++;
			}
		}
		return $r;
	}
	
	/*
	 * 将url变成参数数组
	 * $url 传入的url地址
	 * $clearnull 是否清除空值
	 * 返回：数组
	 * */
	static function urlparam_to_array($url,$clearnull = false){
		$t = explode('?',$url);
		$r = array();
		$pstr = $t[count($t)-1];
		$pstr and $p_arr = explode('&',$pstr);
		if($p_arr)
		foreach($p_arr as $v){
			if($v){
				$v1 = explode('=',$v);
				if(!strlen($v1[1])&&$clearnull){continue;}
				$r[$v1[0]] = $v1[1];
			}
		}
		
		return $r;
	}
	/*
	 * 将参数数组变成url
	 * $param 传入的参数数组
	 * $pagename 返回值是否带页面名
	 * 返回：url
	 * */
	static function array_to_urlparam($param,$pagename = ''){
		$r = '';
		$t = array();
		if($param)
		foreach($param as $k=>$v){
			$t[] = "$k=$v";
		}
		$r = implode('&',$t);
		$pagename and $r = $pagename."?".$r;
		return $r;
	}
	
	/*
	 * url重新整理
	 * $url 传入的url
	 * $dataarr 附加数组
	 * $resort
	 * 返回：$url
	 * */
	static function url_format($url,$dataarr = array(),$resort=false){
		$t = explode('?',$url);
		$pre = '';
		$param = array();
		if(count($t)>1){
			$pre = $t[0];
			$param = self::urlparam_to_array($t[1]);
		}
		else{
			$param = self::urlparam_to_array($t[0]);
		}
		
		$param = array_merge($param,$dataarr);
		$resort and ksort($param);
		
		return self::array_to_urlparam($param,$pre);
		
	}
	
	/*
	 * 获取字符串长度
	 * $string 传入的string
	 * 返回：count
	 * */
	static function utf8_strlen($string = null) {
		// 将字符串分解为单元
		preg_match_all("/./us", $string, $match);
		// 返回单元个数
		return count($match[0]);
	}
	
}



?>