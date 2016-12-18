<?php
/*
	模板文件夹在 tpl 目录下， 默认是default文件夹。
	模板缓存在 data/tpl_c目录下。
*/

class keke_tpl_class {
	static function parse_code($tag_code, $tag_id, $tag_type = 'tag') {
		global $_K;
		$tplfile = 'db/' . $tag_type . '_' . $tag_id;
		$objfile = S_ROOT . './data/tpl_c/' . str_replace ( '/', '_', $tplfile ) . '.php';
		//read
		$tag_code = keke_tpl_class::parse_rule ( $tag_code );
		//write
		keke_tpl_class::swritefile ( $objfile, $tag_code ) or exit ( "File: $objfile can not be write!" );
		
		return $objfile;
	
	}
	static function parse_template($tpl) {
		global $_K;
		//包含模板
		// 		$sub_tpls = array ($tpl );
		

		$tplfile = S_ROOT . './' . $tpl . '.htm';
		$objfile = S_ROOT . './data/tpl_c/' . str_replace ( '/', '_', $tpl ) . '.php';
		//read
		

		if (! file_exists ( $tplfile )) {
			$tpl = str_replace ( '/' . $_K ['template'] . '/', '/default/', $tpl );
			$tplfile = S_ROOT . './' . $tpl . '.htm';
		
		}
		
		$template = keke_tpl_class::sreadfile ( $tplfile );
		empty ( $template ) and exit ( "Template file : $tplfile Not found or have no access!" );
		
		$template = keke_tpl_class::parse_rule ( $template, $tpl );
		//write
		keke_tpl_class::swritefile ( $objfile, $template ) or exit ( "File: $objfile can not be write!" );
	
	}
	/**
	 * 
	 * 解析规则
	 * @param string $content  -html
	 * @param array  $sub_tpls 
	 * @param string $tpl
	 * @return string
	 */
	public static function parse_rule($template, $tpl = null) {
		global $_K;
		($_K['inajax'])&&ob_start();
		$template = preg_replace ( "/\<\!\-\-\{template\s+([a-z0-9_\/]+)\}\-\-\>/ie", "keke_tpl_class::readtemplate('\\1')", $template );
		//处理子页面中的代码
		$template = preg_replace ( "/\<\!\-\-\{template\s+([a-z0-9_\/]+)\}\-\-\>/ie", "keke_tpl_class::readtemplate('\\1')", $template );
		//挂件调用
		$template = preg_replace ( "/\<\!\-\-\{widget\((.+?)\)\}\-\-\>/ie", "keke_tpl_class::runwidget('\\1')", $template );
		//标签调用
		$template = preg_replace ( "/\<\!\-\-\{tag\s+([^!@#$%^&*(){}<>?,.\'\"\+\-\;\":~`]+)\}\-\-\>/ie", "keke_tpl_class::readtag(\"'\\1'\")", $template );
		//广告调用
		$template = preg_replace ( "/\<\!\-\-\{showad\((.+?)\)\}\-\-\>/ie", "keke_tpl_class::showad('\\1')", $template );
		//多广告调用
		$template = preg_replace ( "/\<\!\-\-\{showads\((.+?)\)\}\-\-\>/ie", "keke_tpl_class::showads('\\1')", $template );
		//预设广告调用
		$template = preg_replace ( "/\<\!\-\-\{ad_show\((.+?),(.+?)\)\}\-\-\>/ie", "keke_tpl_class::ad_show('\\1','\\2')", $template );
		$template = preg_replace ( "/\<\!\-\-\{ad_show\((.+?)\)\}\-\-\>/ie", "keke_tpl_class::ad_show('\\1')", $template );
		//动态调用
		$template = preg_replace ( "/\<\!\-\-\{loadfeed\((.+?)\)\}\-\-\>/ie", "keke_tpl_class::loadfeed('\\1')", $template );
		//时间处理
		$template = preg_replace ( "/\<\!\-\-\{date\((.+?),(.+?)\)\}\-\-\>/ie", "keke_tpl_class::datetags('\\1','\\2')", $template );
		//头像处理
		$template = preg_replace ( "/\<\!\-\-\{userpic\((.+?),(.+?)\)\}\-\-\>/ie", "keke_tpl_class::userpic('\\1','\\2')", $template );
		//PHP代码
		$template = preg_replace ( "/\<\!\-\-\{eval\s+(.+?)\s*\}\-\-\>/ies", "keke_tpl_class::evaltags('\\1')", $template );
		//开始处理
		//变量
		$var_regexp = "((\\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)(\[[a-zA-Z0-9_\-\.\"\'\[\]\$\x7f-\xff]+\])*)";
		$template = preg_replace ( "/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $template );
		$template = preg_replace ( "/([\n\r]+)\t+/s", "\\1", $template );
		$template = preg_replace ( "/(\\\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff]+)\.([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/s", "\\1['\\2']", $template );
		$template = preg_replace ( "/\{(\\\$[a-zA-Z0-9_\[\]\'\"\$\.\x7f-\xff]+)\}/s", "<?=\\1?>", $template );
		$template = preg_replace ( "/$var_regexp/es", "keke_tpl_class::addquote('<?=\\1?>')", $template );
		$template = preg_replace ( "/\<\?\=\<\?\=$var_regexp\?\>\?\>/es", "keke_tpl_class::addquote('<?php echo \\1;?>')", $template );
		//逻辑
		$template = preg_replace ( "/\{elseif\s+(.+?)\}/ies", "keke_tpl_class::stripvtags('<?php } elseif(\\1) { ?>','')", $template );
		$template = preg_replace ( "/\{else\}/is", "<?php } else { ?>", $template );
		//循环
		for($i = 0; $i < 7; $i ++) {
			$template = preg_replace ( "/\{loop\s+(\S+)\s+(\S+)\}(.+?)\{\/loop\}/ies", "keke_tpl_class::stripvtags('<?php if(is_array(\\1)) { foreach(\\1 as \\2) { ?>','\\3<?php } } ?>')", $template );
			$template = preg_replace ( "/\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}(.+?)\{\/loop\}/ies", "keke_tpl_class::stripvtags('<?php if(is_array(\\1)) { foreach(\\1 as \\2 => \\3) { ?>','\\4<?php } } ?>')", $template );
			$template = preg_replace ( "/\{if\s+(.+?)\}(.+?)\{\/if\}/ies", "keke_tpl_class::stripvtags('<?php if(\\1) { ?>','\\2<?php } ?>')", $template );
		}
		//常量
		$template = preg_replace ( "/\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}/s", "<?php echo \\1;?>", $template );
		//换行
		$template = preg_replace ( "/ \?\>[\n\r]*\<\? /s", " ", $template );
		//附加处理
		$template = "<?php keke_tpl_class::checkrefresh('$tpl', '{$_K['timestamp']}' );?>$template<?php keke_tpl_class::ob_out();?>";
		
		//替换
		empty ( $_K ['block_search'] ) or $template = str_replace ( $_K ['block_search'], $_K ['block_replace'], $template );
		$template = str_replace("<?=", "<?php echo ", $template);
		return $template;
	}
	
	static function addquote($var) {
		return str_replace ( "\\\"", "\"", preg_replace ( "/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var ) );
	}
	
	static function striptagquotes($expr) {
		$expr = preg_replace ( "/\<\?\=(\\\$.+?)\?\>/s", "\\1", $expr );
		$expr = str_replace ( "\\\"", "\"", preg_replace ( "/\[\'([a-zA-Z0-9_\-\.\x7f-\xff]+)\'\]/s", "[\\1]", $expr ) );
		return $expr;
	}
	
	static function evaltags($php) {
		global $_K;
		$_K ['i'] ++;
		$search = "<!--EVAL_TAG_{$_K['i']}-->";
		$_K ['block_search'] [$_K ['i']] = $search;
		$_K ['block_replace'] [$_K ['i']] = "<?php " . keke_tpl_class::stripvtags ( $php ) . " ?>";
		return $search;
	}
	
	static function datetags($parameter, $value) {
		global $_K;
		$_K ['i'] ++;
		$search = "<!--DATE_TAG_{$_K['i']}-->";
		$_K ['block_search'] [$_K ['i']] = $search;
		$_K ['block_replace'] [$_K ['i']] = "<?php if({$value}){echo date({$parameter},{$value}); } ?>";
		return $search;
	}
	
	//广告调用
	static function showad($adid) {
		global $_K;
		$content = '<!--{eval keke_loaddata_class::ad(' . $adid . ')}-->';
		return $content;
	
	}
	/**
	 * 显示指定位置的广告
	 * @param $target 广告位置代码
	 * @param $do	     当前路由DO
	 */
	static function ad_show($target, $is_slide = null) {
		global $_K, $do;
		//var_dump($target, $is_slide);
		
		$content = '<!--{eval keke_loaddata_class::ad_show(\'' . $target . '\',\'' . $do . '\',\'' . $is_slide . '\')}-->';
		//var_dump($content);die();
		return $content;
	}
	static function runwidget($widgetname) {
		global $_K;
		$content = '<!--{eval $widgetname = \'' . $widgetname . '\'; include(S_ROOT.\'widget/run.php\')}-->';
		return $content;
	}
	
	//广告群调用
	static function showads($adname) {
		global $_K;
		$content = '<!--{eval keke_loaddata_class::adgroup(' . $adname . ')}-->';
		return $content;
	}
	
	//头像调用
	static function userpic($uid, $size) {
		global $_K;
		return '<!--{eval echo  keke_user_class::get_user_pic(' . $uid . ',' . $size . ')}-->';
	}
	
	static function stripvtags($expr, $statement = '') {
		$res = preg_replace ( "/\<\?\=(\\\$.+?)\?\>/s", "\\1", $expr );
		$expr = str_replace ( "\\\"", "\"", $res );
		$statement = str_replace ( "\\\"", "\"", $statement );
		return $expr . $statement;
	}
	
	static function readtemplate($name) {
		global $_K;
		
		$tpl = keke_tpl_class::tpl_exists ( $name );
		$tplfile = S_ROOT . './' . $tpl . '.htm';
		
		$sub_tpls [] = $tpl;
		
		if (! file_exists ( $tplfile )) {
			$tplfile = str_replace ( '/' . $_K ['template'] . '/', '/default/', $tplfile );
		}
		$content = trim ( keke_tpl_class::sreadfile ( $tplfile ) );
		return $content;
	}
	
	static function readtag($name) {
		global $kekezu; 	
		$tag_arr = $kekezu->_tag;
		$tag_info = $tag_arr [$name];
		if ($tag_info ['tag_type'] == 5) {
			$content = htmlspecialchars_decode ( $tag_info ['code'] );
		} else {
			$content = '<!--{eval keke_loaddata_class::readtag(' . $name . ')}-->';
		}
		
		return $content;
	
	}
	
	static function loadfeed($name) {
		$content = '<!--{eval keke_loaddata_class::readfeed(' . $name . ')}-->';
		return $content;
	
	}
	
	//获取文件内容
	static function sreadfile($filename) {
		$content = '';
		if (function_exists ( 'file_get_contents' )) {
			@$content = file_get_contents ( $filename );
		} else {
			if (@$fp = fopen ( $filename, 'r' )) {
				@$content = fread ( $fp, filesize ( $filename ) );
				@fclose ( $fp );
			}
		}
		return $content;
	}
	
	//写入文件
	static function swritefile($filename, $writetext, $openmod = 'w') {
		if (@$fp = fopen ( $filename, $openmod )) {
			flock ( $fp, 2 );
			fwrite ( $fp, $writetext );
			fclose ( $fp );
			return true;
		} else {
			return false;
		}
	}
	//判断字符串$haystack中是否存在字符$needle 返回第一次出现的位置   三个等号 判断绝对相等  uican 2009-12-03
	static function strexists($haystack, $needle) {
		return ! (strpos ( $haystack, $needle ) === FALSE);
	}
	
	static function tpl_exists($tplname) {
		global $_K;
		is_file ( S_ROOT . "tpl/" . $_K ['template'] . "/" . $tplname . ".htm" ) and $tpl = "tpl/{$_K['template']}/$tplname" or $tpl = $tplname;
		return $tpl;
	}
	
	static function template($name) {
		global $_K;
		
		$tpl = keke_tpl_class::tpl_exists ( $name );
		$objfile = S_ROOT . './data/tpl_c/' . str_replace ( '/', '_', $tpl ) . '.php';
		(! file_exists ( $objfile ) || ! TPL_CACHE) and keke_tpl_class::parse_template ( $tpl );
		return $objfile;
	}
	
	/**
	 * //子模板更新检查 
	 *
	 * @param string $subfiles 模板路径
	 * @param int $mktime 时间  
	 * @param string $tpl  当前页面模板
	 */
	static function checkrefresh($tpl, $mktime) {
		global $_K;
		if ($tpl) {
			$tplfile = S_ROOT . './' . $tpl . '.htm';
			(! file_exists ( $tplfile )) and $tplfile = str_replace ( '/' . $_K ['template'] . '/', '/default/', $tplfile );
			$submktime = filemtime ( $tplfile );
			($submktime > $mktime) and keke_tpl_class::parse_template ( $tpl );
		}
	}
	
	//调整输出
	static function ob_out() {
		global $_K, $do, $view;
		$content = ob_get_contents ();
		$preg_searchs = $preg_replaces = $str_searchs = $str_replaces = array();
		if ($_K ['is_rewrite'] ==1) {	
			$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=hire"';
			$str_replaces[] = 'href="' . $_K['siteurl'] . '/hire/"';
			$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=hire_list"';
			$str_replaces[] = 'href="' . $_K['siteurl'] . '/hire/wait/"';
			$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=task_list"';
			$str_replaces[] = 'href="' . $_K['siteurl'] . '/task/"';
			$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=talent"';
			$str_replaces[] = 'href="' . $_K['siteurl'] . '/talent/"';
			$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=vip"';
			$str_replaces[] = 'href="' . $_K['siteurl'] . '/vip/"';
			$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=special"';
			$str_replaces[] = 'href="' . $_K['siteurl'] . '/case/"';
			$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=help&view=wanzhuanyipin"';
			$str_replaces[] = 'href="' . $_K['siteurl'] . '/help/novice"';
			$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=service"';
			$str_replaces[] = 'href="' . $_K['siteurl'] . '/sellfuwu/"';
			$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=help&view=list"';
			$str_replaces[] = 'href="' . $_K['siteurl'] . '/help/bang/"';			
			$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=match"';
			$str_replaces[] = 'href="' . $_K['siteurl'] . '/match/"';
			$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=prom"';
			$str_replaces[] = 'href="' . $_K['siteurl'] . '/prom/"';

			// 文章链接
			if ( $do == 'article' ) {
				$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=article&art_cat_id=587"';
				$str_replaces[] = 'href="' . $_K['siteurl'] . '/dongtai/"';
				$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=article&art_cat_id=597"';
				$str_replaces[] = 'href="' . $_K['siteurl'] . '/gonggao/"';
				$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=article&art_cat_id=589"';
				$str_replaces[] = 'href="' . $_K['siteurl'] . '/meiti/"';
				$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=article&art_cat_id=590"';
				$str_replaces[] = 'href="' . $_K['siteurl'] . '/meijie/"';
				$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=article&art_cat_id=591"';
				$str_replaces[] = 'href="' . $_K['siteurl'] . '/internet/"';
			}
			
			// 任务链接
			$preg_searchs [] = "/href\=\"(.*?)index\.php\?do\=task\&task_id\=(\d+)\"/i";
			$preg_replaces [] = 'href="' . $_K['siteurl'] . '/task/\\2/"';
			
			// VIP 链接
                        if ( $do == 'vip' ) {
                            $preg_searchs [] = "/href\=\"(.*?)index\.php\?do\=vip\&view\=(desc|help|open)\"/i";
                            $preg_replaces [] = 'href="' . $_K['siteurl'] . '/vip/\\2.html"';

                            $preg_searchs [] = "/href\=\"(.*?)index\.php\?do\=article\&art_cat_id\=599\&art_id\=(\d+)\"/i";
                            $preg_replaces [] = 'href="' . $_K['siteurl'] . '/vip/story/vip_\\2.html"';

                            $str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=vip&view=story"';
                            $str_replaces[] = 'href="' . $_K['siteurl'] . '/vip/story/"';
                        }
                        
 			// 特色专题
                        $str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=special&view=special_list"';
                        $str_replaces[] = 'href="' . $_K['siteurl'] . '/zt/"';
                        
                        if ( $do == 'special' ) {
                            $str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=special&view=video_list"';
                            $str_replaces[] = 'href="' . $_K['siteurl'] . '/zt/video/"';
                            $str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=article&article_cat_id=593"';
                            $str_replaces[] = 'href="' . $_K['siteurl'] . '/guzhu/"';

                            // 精彩专题翻页
                            $preg_searchs [] = "/href\=\"(.*?)index\.php\?do\=special\&view\=special_list\&page\=(\d+)\"/i";
                            $preg_replaces [] = 'href="' . $_K['siteurl'] . '/zt/page\\2.html"';
                            $preg_searchs [] = "/href\=\"(.*?)index\.php\?do\=special\&view\=special_list\&cat_id\=1\&page\=(\d+)\"/i";
                            $preg_replaces [] = 'href="' . $_K['siteurl'] . '/zt/rwzt/page\\2.html"';
                            $preg_searchs [] = "/href\=\"(.*?)index\.php\?do\=special\&view\=special_list\&cat_id\=2\&page\=(\d+)\"/i";
                            $preg_replaces [] = 'href="' . $_K['siteurl'] . '/zt/rczt/page\\2.html"';
                            $preg_searchs [] = "/href\=\"(.*?)index\.php\?do\=special\&view\=special_list\&cat_id\=3\&page\=(\d+)\"/i";
                            $preg_replaces [] = 'href="' . $_K['siteurl'] . '/zt/hdzt/page\\2.html"';
                            $preg_searchs [] = "/href\=\"(.*?)index\.php\?do\=special\&view\=special_list\&cat_id\=4\&page\=(\d+)\"/i";
                            $preg_replaces [] = 'href="' . $_K['siteurl'] . '/zt/qtzt/page\\2.html"';
                            $preg_searchs [] = "/href\=\"(.*?)index\.php\?do\=special\&view\=special_list\&cat_id\=5\&page\=(\d+)\"/i";
                            $preg_replaces [] = 'href="' . $_K['siteurl'] . '/zt/cyds/page\\2.html"';

                            // 成功雇主翻页
                            $preg_searchs [] = "/href\=\"(.*?)index\.php\?do\=special\&view\=employer_list(.+)\&page\=(\d+)\"/i";
                            $preg_replaces [] = 'href="' . $_K['siteurl'] . '/guzhu/page\\3.html"';

                            // 优秀威客翻页
                            $preg_searchs [] = "/href\=\"(.*?)index\.php\?do\=special\&view\=witkey_list(.+)\&page\=(\d+)\"/i";
                            $preg_replaces [] = 'href="' . $_K['siteurl'] . '/weike/page\\3.html"';

                            // 成功案例翻页 
                            $preg_searchs[] = "/href\=\"(.*?)index\.php\?do\=special\&view\=case_list(.+)\&art_cat_id\=(\w+)(.+)\"/ie";
                            $preg_replaces[] = 'epweike_seo_class::cat_rewrite_url(\'cat\', \'\\3\', \'\\4\')';
                            $preg_searchs[] = "/href\=\"(.*?)index\.php\?do\=special\&view\=case_list(.+)&page=(\d+)\"/i";
                            $preg_replaces[] = 'href="' . $_K['siteurl'] . '/anli/page\\3.html"';
                            
                            // 特色视频
                            $str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=special&view=video_list&v_cat=1"';
                            $str_replaces[] = 'href="' . $_K['siteurl'] . '/zt/video/chenggong/"';
                            $str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=special&view=video_list&v_cat=2"';
                            $str_replaces[] = 'href="' . $_K['siteurl'] . '/zt/video/wanzhuan/"';
                            $str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=special&view=video_list&v_cat=3"';
                            $str_replaces[] = 'href="' . $_K['siteurl'] . '/zt/video/youxiu/"';
                            $str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=special&view=video_list&v_cat=4"';
                            $str_replaces[] = 'href="' . $_K['siteurl'] . '/zt/video/meijie/"';

                            $preg_searchs[] = "/href\=\"(.*?)index\.php\?do\=special\&view\=video_info\&v_cate\=1\&v_id\=(\d+)\"/i";
                            $preg_replaces[] = 'href="' . $_K['siteurl'] . '/zt/video/chenggong/view_\\2.html"';
                            $preg_searchs[] = "/href\=\"(.*?)index\.php\?do\=special\&view\=video_info\&v_cate\=2\&v_id\=(\d+)\"/i";
                            $preg_replaces[] = 'href="' . $_K['siteurl'] . '/zt/video/wanzhuan/view_\\2.html"';
                            $preg_searchs[] = "/href\=\"(.*?)index\.php\?do\=special\&view\=video_info\&v_cate\=3\&v_id\=(\d+)\"/i";
                            $preg_replaces[] = 'href="' . $_K['siteurl'] . '/zt/video/youxiu/view_\\2.html"';
                            $preg_searchs[] = "/href\=\"(.*?)index\.php\?do\=special\&view\=video_info\&v_cate\=4\&v_id\=(\d+)\"/i";
                            $preg_replaces[] = 'href="' . $_K['siteurl'] . '/zt/video/meijie/view_\\2.html"';

                            $preg_searchs[] = "/href\=\"(.*?)index\.php\?do\=special\&view\=video_list\&v_cat\=0\&page\=(\d+)\"/i";
                            $preg_replaces[] = 'href="' . $_K['siteurl'] . '/zt/video/page\\2.html"';
                            $preg_searchs[] = "/href\=\"(.*?)index\.php\?do\=special\&view\=video_list\&v_cat\=1\&page\=(\d+)\"/i";
                            $preg_replaces[] = 'href="' . $_K['siteurl'] . '/zt/video/chenggong/page\\2.html"';
                            $preg_searchs[] = "/href\=\"(.*?)index\.php\?do\=special\&view\=video_list\&v_cat\=2\&page\=(\d+)\"/i";
                            $preg_replaces[] = 'href="' . $_K['siteurl'] . '/zt/video/wanzhuan/page\\2.html"';
                            $preg_searchs[] = "/href\=\"(.*?)index\.php\?do\=special\&view\=video_list\&v_cat\=3\&page\=(\d+)\"/i";
                            $preg_replaces[] = 'href="' . $_K['siteurl'] . '/zt/video/youxiu/page\\2.html"';
                            $preg_searchs[] = "/href\=\"(.*?)index\.php\?do\=special\&view\=video_list\&v_cat\=4\&page\=(\d+)\"/i";
                            $preg_replaces[] = 'href="' . $_K['siteurl'] . '/zt/video/meijie/page\\2.html"';
                        }
                        
			// 帮助中心
			$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=help"';
			$str_replaces[] = 'href="' . $_K['siteurl'] . '/help/"';
			
			$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=help&view=self"';
			$str_replaces[] = 'href="' . $_K['siteurl'] . '/help/selffuwu/"';
			$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=help&view=get_username"';
			$str_replaces[] = 'href="' . $_K['siteurl'] . '/help/selffuwu-username.html"';
			$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=help&view=get_password"';
			$str_replaces[] = 'href="' . $_K['siteurl'] . '/help/selffuwu-paseword.html"';
			$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=help&view=get_securitycode"';
			$str_replaces[] = 'href="' . $_K['siteurl'] . '/help/selffuwu-securitycode.html"';
			$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=user&view=finance&op=recharge"';
			$str_replaces[] = 'href="' . $_K['siteurl'] . '/help/selffuwu-recharge.html"';
			$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=user&view=finance&op=withdraw"';
			$str_replaces[] = 'href="' . $_K['siteurl'] . '/help/selffuwu-withdraw.html"';
			$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=user&view=setting&op=auth&auth_code=email"';
			$str_replaces[] = 'href="' . $_K['siteurl'] . '/help/selffuwu-email.html"';
			$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=user&view=setting&op=auth&auth_code=bank"';
			$str_replaces[] = 'href="' . $_K['siteurl'] . '/help/selffuwu-bank.html"';
			$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=user&view=setting&op=auth&auth_code=realname"';
			$str_replaces[] = 'href="' . $_K['siteurl'] . '/help/selffuwu-realname.html"';
			$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=user&view=setting&op=auth&auth_code=mobile"';
			$str_replaces[] = 'href="' . $_K['siteurl'] . '/help/selffuwu-mobile.html"';
			$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=user&view=finance"';
			$str_replaces[] = 'href="' . $_K['siteurl'] . '/help/selffuwu-finance.html"';
			$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=user&view=finance&op=detail"';
			$str_replaces[] = 'href="' . $_K['siteurl'] . '/help/selffuwu-query.html"';
			$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=user&view=setting&op=credit_record"';
			$str_replaces[] = 'href="' . $_K['siteurl'] . '/help/selffuwu-record.html"';
			$str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=help&view=service"';
			$str_replaces[] = 'href="' . $_K['siteurl'] . '/help/callfuwu.html"';

                        /*
			//任务 
			$preg_searchs [] = "/href\=\"(.*?)index\.php\?do\=task_list\"/ie";
			$preg_replaces[] = 'epweike_seo_class::epweike_cat_rewrite_url(\'task_list\')';
			$preg_searchs [] = "/href\=\"(.*?)index\.php\?do\=task_list(.+?)\"/ie";
			$preg_replaces [] = 'epweike_seo_class::epweike_cat_rewrite_url(\'task_list\',\'\',\'\\2\')';
			$preg_searchs [] = "/href\=\"(.*?)index\.php\?do\=task_list(.+?)\#(\w+)\"/ie";
			$preg_replaces [] = 'epweike_seo_class::epweike_cat_rewrite_url(\'task_list\',,\'\\2\',\'\\3\')';
			
			$preg_searchs [] = "/action\=\"(.*?)index\.php\?do\=task_list(.+?)\"/i";
			$preg_replaces [] = "action='$1task?'";

			//维护类
			$preg_searchs [] = "/href\=\"(.*?)index\.php\?do\=indus\&indus_id\=(\w+)\"/ie";
			$preg_replaces [] = 'epweike_seo_class::epweike_cat_rewrite_url(\'indus\', \'\\2\')';
			$preg_searchs [] = "/href\=\"(.*?)index\.php\?do\=indus\&indus_id\=(\w+)(.+?)\#(\w+)\"/ie";
			$preg_replaces [] = 'epweike_seo_class::epweike_cat_rewrite_url(\'indus\', \'\\2\',\'\\3\',\'\\4\')';
			$preg_searchs [] = "/href\=\"(.*?)index\.php\?do\=indus\&indus_id\=(\w+)(.+?)\"/ie";
			$preg_replaces [] = 'epweike_seo_class::epweike_cat_rewrite_url(\'indus\', \'\\2\',\'\\3\')';
                        */
                        
			//全部任务
                        if ( $do == 'task_list' || $do == 'indus' ) {
                            $preg_searchs[] = "/href\=\"(.*?)index\.php\?do\=(task_list|indus)(.+?)\"/ie";
                            $preg_replaces[] = 'epweike_seo_class::cat_rewrite_url(\'task_list\', \'\', \'\\3\')';                        
                        }
                        
			//人才库
                        if ( $do == 'talent' ) {
                            $preg_searchs[] = "/href\=\"(.*?)index\.php\?do\=talent(.+?)\"/ie";
                            $preg_replaces[] = 'epweike_seo_class::cat_rewrite_url(\'talent_indus\', \'\', \'\\2\')';
                        }
                        
                        // 任务详情
                        if ( $do == 'task' ) {
                            // 淘汰稿件链接
                            $preg_searchs[] = "/(href|value)\=\"(.*?)index\.php\?do=task&task_id=(\d+)\&st=15\&page_size=(\d+)&order=(\d+)\"/i";
                            $preg_replaces[] = '\\1="' . $_K['siteurl'] . '/task/\\3/taotai/s\\4-o\\5.html"';
                            // 备选稿件链接
                            $preg_searchs[] = "/(href|value)\=\"(.*?)index\.php\?do=task&task_id=(\d+)\&st=14\&page_size=(\d+)&order=(\d+)\"/i";
                            $preg_replaces[] = '\\1="' . $_K['siteurl'] . '/task/\\3/bx/s\\4-o\\5.html"';
                            // 入围稿件链接
                            $preg_searchs[] = "/(href|value)\=\"(.*?)index\.php\?do=task&task_id=(\d+)\&st=13\&page_size=(\d+)&order=(\d+)\"/i";
                            $preg_replaces[] = '\\1="' . $_K['siteurl'] . '/task/\\3/ruwei/s\\4-o\\5.html"';
                            // 合格稿件链接
                            $preg_searchs[] = "/(href|value)\=\"(.*?)index\.php\?do=task&task_id=(\d+)\&st=12\&page_size=(\d+)&order=(\d+)\"/i";
                            $preg_replaces[] = '\\1="' . $_K['siteurl'] . '/task/\\3/hege/s\\4-o\\5.html"';
                            // 中标稿件链接
                            $preg_searchs[] = "/(href|value)\=\"(.*?)index\.php\?do=task&task_id=(\d+)\&st=11\&page_size=(\d+)&order=(\d+)\"/i";
                            $preg_replaces[] = '\\1="' . $_K['siteurl'] . '/task/\\3/zhongbiao/s\\4-o\\5.html"';
                            // 未操作稿件链接
                            $preg_searchs[] = "/(href|value)\=\"(.*?)index\.php\?do=task&task_id=(\d+)\&st=00\&page_size=(\d+)&order=(\d+)\"/i";
                            $preg_replaces[] = '\\1="' . $_K['siteurl'] . '/task/\\3/wcz/s\\4-o\\5.html"';
                            // 提交稿件链接
                            $preg_searchs[] = "/(href|value)\=\"(.*?)index\.php\?do=task&task_id=(\d+)(\&st=)?\&page_size=(\d+)&order=(\d+)\"/i";
                            $preg_replaces[] = '\\1="' . $_K['siteurl'] . '/task/\\3/s\\5-o\\6.html"';
                            // 我的稿件链接
                            $preg_searchs[] = "/(href|value)\=\"(.*?)index\.php\?do=task&task_id=(\d+)\&ut=my\&page_size=(\d+)&order=(\d+)\"/i";
                            $preg_replaces[] = '\\1="' . $_K['siteurl'] . '/task/\\3/mylv/s\\4-o\\5.html"';
                        }
                        
                        // 雇佣大厅
                        $str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=hire_list&view=wait"';
                        $str_replaces[] = 'href="' . $_K['siteurl'] . '/hire/wait/"';
                        $str_searchs[] = 'href="' . $_K['siteurl'] . '/index.php?do=hire_list&view=success"';
                        $str_replaces[] = 'href="' . $_K['siteurl'] . '/hire/success/"';

                       if ( $do == 'hire_list' ) {
                            $preg_searchs[] = "/href\=\"(.*?)index\.php\?do\=hire_list\&view\=(wait|success)(.+?)\"/ie";
                            $preg_replaces[] = 'epweike_seo_class::rebuild_hire_url(\'\\2\', \'\\3\')';
                        }
                        
                        // 店铺
                        $preg_searchs[] = '/href\="(.*?)index\.php\?do\=shop\&sid\=(\w+?)"/i';
                        $preg_replaces[] = 'href="' . str_replace('www', 'shop', $_K['siteurl']). '/\\2/"';

                        if ( $do == 'shop' ) {
                            $preg_searchs[] = '/((href|value)\="(.*?)index\.php\?do\=shop\&sid\=(\w+)(.+?)")/ie';
                            $preg_replaces [] = 'epweike_seo_class::rebuild_shop_url("\\4", "\\1")';
                        }
                        /*
			//实现店铺2级域名2级目录用的   性能负载还是有点大的
			$preg_searchs [] = "/href\=\"(.*?)index\.php\?do\=shop\&u_id\=(\w+)\"/ie";
			$preg_replaces [] = 'epweike_seo_class::domain_rewrite_url(0,\'\\2\')';
			$preg_searchs [] = "/href\=\"(.*?)index\.php\?do\=shop\&u_id\=(\w+)(.+?)\#(\w+)\"/ie";
			$preg_replaces [] = 'epweike_seo_class::domain_rewrite_url(0, \'\\2\',\'\\3\',\'\\4\')';
			$preg_searchs [] = "/href\=\"(.*?)index\.php\?do\=shop\&u_id\=(\w+)(.+?)\"/ie";
			$preg_replaces [] = 'epweike_seo_class::domain_rewrite_url(0, \'\\2\',\'\\3\')';
			
			$preg_searchs [] = "/href\=\"(.*?)index\.php\?do\=shop\&sid\=(\w+)\"/ie";
			$preg_replaces [] = 'epweike_seo_class::domain_rewrite_url(\'\\2\',0)';
			$preg_searchs [] = "/href\=\"(.*?)index\.php\?do\=shop\&sid\=(\w+)(.+?)\#(\w+)\"/ie";
			$preg_replaces [] = 'epweike_seo_class::domain_rewrite_url(\'\\2\',0,\'\\3\',\'\\4\')';
			$preg_searchs [] = "/href\=\"(.*?)index\.php\?do\=shop\&sid\=(\w+)(.+?)\"/ie";
			$preg_replaces [] = 'epweike_seo_class::domain_rewrite_url(\'\\2\',0,\'\\3\')';
			*/
			
			//栏目seo
			$preg_searchs [] = "/href\=\"(.*?)index\.php\?do\=indus\&indus_id\=(\w+)\"/ie";
			$preg_replaces [] = 'epweike_seo_class::cat_rewrite_url(\'indus\', \'\\2\')';
			$preg_searchs [] = "/href\=\"(.*?)index\.php\?do\=indus\&indus_id\=(\w+)(.+?)\#(\w+)\"/ie";
			$preg_replaces [] = 'epweike_seo_class::cat_rewrite_url(\'indus\', \'\\2\',\'\\3\',\'\\4\')';
			$preg_searchs [] = "/href\=\"(.*?)index\.php\?do\=indus\&indus_id\=(\w+)(.+?)\"/ie";
			$preg_replaces [] = 'epweike_seo_class::cat_rewrite_url(\'indus\', \'\\2\',\'\\3\')';
            
//			$preg_searchs [] = "/href\=\"index\.php\?do\=indus\&indus_id\=(\w+)\"/ie";
//			$preg_replaces [] = 'epweike_seo_class::cat_rewrite_url(\'indus\', \'\\1\')';
			
			$preg_searchs [] = "/href\=\"(.*?)index\.php\?do\=article\&art_cat_id\=(\w+)\"/ie";
			$preg_replaces [] = 'epweike_seo_class::cat_rewrite_url(\'cat\', \'\\2\')';
			$preg_searchs [] = "/href\=\"(.*?)index\.php\?do\=article\&art_cat_id\=(\w+)(.+?)\#(\w+)\"/ie";
			$preg_replaces [] = 'epweike_seo_class::cat_rewrite_url(\'cat\', \'\\2\',\'\\3\',\'\\4\')';
			$preg_searchs [] = "/href\=\"(.*?)index\.php\?do\=article\&art_cat_id\=(\w+)(.+?)\"/ie";
			$preg_replaces [] = 'epweike_seo_class::cat_rewrite_url(\'cat\', \'\\2\',\'\\3\')';
			
			$preg_searchs [] = "/href\=\"index\.php\?(.+?)\#(\w+)\"/ie";
			$preg_replaces [] = 'keke_tpl_class::rewrite_url(\'index-\',\'\\1\',\'\\2\')';
			
			$preg_searchs [] = "/href\=\"index\.php\"/i";
			$preg_replaces [] = 'href="index.html"';
			
			$preg_searchs [] = "/href\=\"http\:\/\/(.*)\/index\.php\?(.+?)\#(\w+)\"/ie";
			$preg_replaces [] = 'keke_tpl_class::rewrite_url(\'http://\\1/index-\',\'\\2\',\'\\3\')';
			
			$preg_searchs [] = "/href\=\"http\:\/\/(.*)\/index\.php\?(.+?)\"/ie";
			$preg_replaces [] = 'keke_tpl_class::rewrite_url(\'http://\\1/index-\',\'\\2\')';
			
			$preg_searchs [] = "/href\=\"index\.php\?(.+?)\"/ie";
			$preg_replaces [] = 'keke_tpl_class::rewrite_url(\'index-\',\'\\1\')';
			
		}
		 
		if ($_K ['inajax']) {
			$preg_searchs [] = "/([\x01-\x09\x0b-\x0c\x0e-\x1f])+/";
			$preg_replaces [] = ' ';
			
			$str_searchs [] = ']]>';
			$str_replaces [] = ']]&gt;';
		}

		if ($str_searchs) {
			$content = trim ( str_replace ( $str_searchs, $str_replaces, $content ) );
		}
		
		if ($preg_searchs) {
			$content = preg_replace ( $preg_searchs, $preg_replaces, $content );
		}
		keke_tpl_class::obclean ();
		($_K ['inajax']) and self::xml_out ( $content );
		//header ( 'Content-Type: text/html; charset='.CHARSET);
		echo $content;

		if ( $_K ['DEVEL'] ) {
			// 输出所有 SQL / INCLUDE 的文件列表
			$included_files = get_included_files();
			$all_sql = db_factory::debug();

			echo '<div id="debug_info" style="position: absolute; top: 10px; left: 10px; z-index: 99999;">';
			echo '<dl><dt onclick="$(this).next().toggle()"><b>SQL (' . sizeof($all_sql) . ')</b></dt><dd style="display:none; background-color: black; color: white">';

			foreach ($all_sql as $sql) echo "<a href='###'>$sql</a><br />\n";
			echo '</dd></dl>';

			echo '<dl><dt onclick="$(this).next().toggle()"><b>INCLUDED FILES (' . sizeof($included_files) . ')</b></dt><dd style="display:none; background-color: black; color: white">';

			foreach ($included_files as $filename) {
				echo "<a href='$filename'>$filename</a><br />\n";
			}
			echo '</dd></dl></div>';
		}
	}
	static function obclean() {
		global $_K;
		 ob_get_length()>0 and ob_end_clean();
		 ob_start();
	}
	//'keke_tpl_class::rewrite_url('index-','URL参数')';
	//路径中暂时没用到$pre
	static function rewrite_url($pre, $para, $hot = '') {
		global $_K;
		$str = '';
		parse_str ( $para, $joint );
		/* $str   = $joint['do'];
		unset($joint['do']); */
		$s = array_filter ( $joint );
		$url = http_build_query ( $s );
		
		$url = str_replace ( array ("do=", '&', '=' ), array ("", '-', '-' ), $url );
		//$url and $str.="-".$url;
		$hot = $hot ? "#" . $hot : '';
		return 'href="'.$_K['siteurl'].'/'.$url . '.html' . $hot . '"';
		
	}
	static function xml_out($content) {
		global $_K;
		header ( "Expires: -1" );
		header ( "Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE );
		header ( "Pragma: no-cache" );
		header ( "Content-type: application/xml; charset=".CHARSET );
		echo '<' . "?xml version=\"1.0\" encoding=\"".CHARSET."\"?>\n";
		echo "<root><![CDATA[" . trim ( $content ) . "]]></root>";
		exit ();
	}

}
?>