<?php
/**
 * epweike的seo转化类
 * @copyright keke-tech
 * @author tank
 * @version v 2.0
 * 2012-5-17下午16:59:00
 */

class epweike_seo_class {
	
	public static function create_htacess(){
		$indus_arr = keke_core_class::get_industry();
		$r = 'RewriteEngine On 
RewriteRule ^index.html$ index.php
';
		
		//任务栏目处理
		$arr2 = array();
		foreach ($indus_arr as $i){
			//if ($i['seo_catname']){
				
				$str = 'RewriteRule ^';
				$cat_trac = self::cat_trac('indus',$i['indus_id']);
				$str .= $cat_trac;
				$str .= '/$ index.php?do=indus&indus_id='.$i['indus_id'];
				$str .= '
';
				$arr2[$cat_trac] = 'RewriteRule ^'.$cat_trac.'/(.*)$ $1';
				$r .= $str;
				
			//}
		}
		krsort($arr2);
		
		$r .= implode('
', $arr2);
		
		$r.='

';
		//文章栏目处理
		$eptarc_cat_arr = db_factory::get_table_data("*","witkey_article_category","",'','','','art_cat_id',null);
		$arr2 = array();
		foreach ($eptarc_cat_arr as $cat){
			//if ($i['seo_catname']){
				
				$str = 'RewriteRule ^';
				$cat_trac = self::cat_trac('cat',$cat['art_cat_id']);
				$str .= $cat_trac;
				$str .= '/$ index.php?do=article&art_cat_id='.$cat['art_cat_id'];
				$str .= '
';
				$arr2[$cat_trac] = 'RewriteRule ^'.$cat_trac.'/(.*)$ $1';
				$r .= $str;
				
			//}
		}
		krsort($arr2);
		
		$r .= implode('
', $arr2);
		
		$r .='

';
		
		
		//专题的id
		$zt_seo_arr = keke_glob_class::get_special_seocatname();
		foreach ($zt_seo_arr as $k=>$v){
			$r .= 'RewriteRule ^'.$v.'/$ index.php?do=special&view=special_list&cat_id='.$k.'
';
			$r .= 'RewriteRule ^'.$v.'/(.*)$ $1
';
		}
		$r .= 'RewriteRule ^zhuanti/$ index.php?do=special&view=special_list
';
		$r .= 'RewriteRule ^zhuanti/(.*)$ $1
RewriteRule ^case/$ index.php?do=special&view=case_list
RewriteRule ^case/(.*)$ $1
';
		
		
		$r .= '
RewriteRule ^(\w+).html$ index.php?do=$1
RewriteRule ^(.*).html$ rewriteurl.php?url_param=$1
';
		
		file_put_contents(S_ROOT.'./.htaccess', $r);
		//return $r;
		
		self::create_ngiax_config();
	}
	
public static function create_ngiax_config(){
		$indus_arr = keke_core_class::get_industry();
		$r = '';
		
		//任务栏目处理
		$arr2 = array();
		foreach ($indus_arr as $i){
			//if ($i['seo_catname']){
				
				$str = 'rewrite ^(.*)/';
				$cat_trac = self::cat_trac('indus',$i['indus_id']);
				$str .= $cat_trac;
				$str .= '/$ $1/index.php?do=indus&indus_id='.$i['indus_id'].' last;';
				$str .= '
';
				$arr2[$cat_trac] = 'rewrite ^(.*)/'.$cat_trac.'/(.*)\.html#(.*)$ $1/rewriteurl.php?url_param=$2#$3 last;
rewrite ^(.*)/'.$cat_trac.'/(.*)\.html$ $1/rewriteurl.php?url_param=$2 last;
rewrite ^(.*)/'.$cat_trac.'/(.*)$ $1/$2 last;';
				$r .= $str;
				
			//}
		}
		krsort($arr2);
		
		$r .= implode('
', $arr2);
		
		$r.='

';
		//文章栏目处理
		$eptarc_cat_arr = db_factory::get_table_data("*","witkey_article_category","",'','','','art_cat_id',null);
		$arr2 = array();
		foreach ($eptarc_cat_arr as $cat){
			//if ($i['seo_catname']){
				
				$str = 'rewrite ^(.*)/';
				$cat_trac = self::cat_trac('cat',$cat['art_cat_id']);
				$str .= $cat_trac;
				$str .= '/$ $1/index.php?do=article&art_cat_id='.$cat['art_cat_id'].' last;';
				$str .= ' 
';
				$arr2[$cat_trac] = 'rewrite ^(.*)/'.$cat_trac.'/(.*)\.html#(.*)$ $1/rewriteurl.php?url_param=$2#$3 last;
rewrite ^(.*)/'.$cat_trac.'/(.*)\.html$ $1/rewriteurl.php?url_param=$2 last;
rewrite ^(.*)/'.$cat_trac.'/(.*)$ $1/$2 last;
';
				$r .= $str;
				
			//}
		}
		krsort($arr2);
		
		$r .= implode('
', $arr2);
		
		$r .='

';
		
		
		//专题的id
		$zt_seo_arr = keke_glob_class::get_special_seocatname();
		foreach ($zt_seo_arr as $k=>$v){
			$r .= 'rewrite ^(.*)/'.$v.'/$ $1/index.php?do=special&view=special_list&cat_id='.$k.' last;
';
			$r .= 'rewrite ^(.*)/'.$v.'/(.*)\.html#(.*)$ $1/rewriteurl.php?url_param=$2#$3 last;
rewrite ^(.*)/'.$v.'/(.*)\.html$ $1/rewriteurl.php?url_param=$2 last;
';
			$r .= 'rewrite ^(.*)/'.$v.'/(.*)$ $1/$2 last;
';
		}
		$r .= 'rewrite ^(.*)/zhuanti/$ $1/index.php?do=special&view=special_list last;
';		
		$r .= 'rewrite ^(.*)/zhuanti/(.*)\.html#(.*)$ $1/rewriteurl.php?url_param=$2#$3 last;
rewrite ^(.*)/zhuanti/(.*)\.html$ $1/rewriteurl.php?url_param=$2 last;
';
		$r .= 'rewrite ^(.*)/zhuanti/(.*)$ $1/$2 last;
rewrite ^(.*)/case/$ $1/index.php?do=special&view=case_list last;
rewrite ^(.*)/case/(.*)\.html#(.*)$ $1/rewriteurl.php?url_param=$2#$3 last;
rewrite ^(.*)/case/(.*)\.html$ $1/rewriteurl.php?url_param=$2 last;
rewrite ^(.*)/case/(.*)$ $1/$2 last;
';
		
		
		
		
		
		
		
	
		file_put_contents(S_ROOT.'./keke.conf', $r);
		//return $r;
		
	}
	
	
	public static function create_301_config(){
		$indus_arr = keke_core_class::get_industry();
		$r = '';
		foreach($indus_arr as $k=>$v){
			$cat_trac = self::cat_trac('indus',$v['indus_id']);
			if($v['indus_pid']){
				if($v['indus_type']<2){
					$r .= 'if ( $query_string ~ "sid='.($v['indus_id']-1000).'" ){
	rewrite ^(.*)/TaskRoomAtCatalog\.asp$ $1/'.$cat_trac.'/? permanent;
	rewrite ^(.*)/taskRoomAtCatalog\.asp$ $1/'.$cat_trac.'/? permanent;
}
';
				}
				else{
					$r .= 'if ( $query_string ~ "sid='.($v['indus_id']-1000).'" ){
	rewrite ^(.*)/ServiceRoom\.asp$ $1/'.$cat_trac.'/? permanent;
	rewrite ^(.*)/serviceRoom\.asp$ $1/'.$cat_trac.'/? permanent;
}
';
				}
			}
			else{
				if($v['indus_type']<2){
					$r .= 'if ( $query_string ~ "cid='.$v['indus_id'].'" ){
	rewrite ^(.*)/TaskRoomAtCatalog\.asp$ $1/'.$cat_trac.'/? permanent;
	rewrite ^(.*)/taskRoomAtCatalog\.asp$ $1/'.$cat_trac.'/? permanent;
}
if ( $query_string ~ "cid='.$v['indus_id'].'" ){
	rewrite ^(.*)/TaskRoomAtCatalog_00\.asp$ $1/'.$cat_trac.'/? permanent;
	rewrite ^(.*)/taskRoomAtCatalog_00\.asp$ $1/'.$cat_trac.'/? permanent;
}
';
				}
				else{
					$r .= 'if ( $query_string ~ "cid='.$v['indus_id'].'" ){
	rewrite ^(.*)/ServicekRoom\.asp$ $1/'.$cat_trac.'/? permanent;
	rewrite ^(.*)/servicekRoom\.asp$ $1/'.$cat_trac.'/? permanent;
}
';
				}
			}
		}
		
		
		$art_id_arr = array(
			'27'=>'597',
			'330'=>'589',
			'428'=>'587',
			'690'=>'590',
			'690'=>'590',
			'421'=>'591',
			'418'=>'591',
			'323'=>'593',
			'324'=>'593',
			'325'=>'593',
			'326'=>'594',
			'327'=>'594',
			'328'=>'594',
			'329'=>'594',
			'751'=>'598',
		);
		
		foreach($art_id_arr as $k=>$v){
			$cat_trac = self::cat_trac('cat',$v);
			$r .= 'if ( $query_string ~ "sid='.$k.'$" ){
	rewrite ^(.*)/news_list\.asp$ $1/'.$cat_trac.'/? permanent;
}
';
			$article_list = db_factory::query("select art_id from ".TABLEPRE."witkey_article where art_cat_id = $v");
			
			if($article_list){
				foreach($article_list as $k1=>$v1){
					$r .= 'if ( $query_string ~ "id='.$v1['art_id'].'$" ){
	rewrite ^(.*)/news_view\.asp$ $1/'.$cat_trac.'/article-i-'.$v.'-art_id-'.$v1['art_id'].'.html? permanent;
}
';
$r .= 'if ( $query_string ~ "id='.$v1['art_id'].'&" ){
	rewrite ^(.*)/news_view\.asp$ $1/'.$cat_trac.'/article-i-'.$v.'-art_id-'.$v1['art_id'].'.html? permanent;
}
';
				}
			}
			
		}
		
		
		
		
		
		
		file_put_contents(S_ROOT.'./301.conf', $r);
	}
	
	
	public static function cat_trac($type,$catid){

		switch ($type){
			case 'indus':
				global $eptarc_indus_arr;
				
				$eptarc_indus_arr or $eptarc_indus_arr = keke_core_class::get_industry();
				$r = '';
				$eptarc_indus_arr[$catid]['seo_catname'] and $r = $eptarc_indus_arr[$catid]['seo_catname'] or $r = $type.$catid;
				
				
				if($eptarc_indus_arr[$catid]['indus_pid']&&$eptarc_indus_arr[$catid]['indus_pid']!=$catid){
					$r = self::cat_trac($type, $eptarc_indus_arr[$catid]['indus_pid']).'/'.$r;
				}
				
				break;
			case 'cat':
				global $eptarc_cat_arr;
				$eptarc_cat_arr or $eptarc_cat_arr = db_factory::get_table_data("*","witkey_article_category","",'','','','art_cat_id',null);
				$r = '';
				$eptarc_cat_arr[$catid]['seo_catname'] and $r = $eptarc_cat_arr[$catid]['seo_catname'] or $r = $type.$catid;
				
				if($eptarc_cat_arr[$catid]['indus_pid']&&$eptarc_cat_arr[$catid]['art_cat_pid']!=$catid){
					$r = self::cat_trac($type, $eptarc_cat_arr[$catid]['art_cat_pid']).'/'.$r;
				}
				
				break;
			default:
				
				break;
		}
		return $r;
	}
	// t3/a1s5/ps50-o2/page2.html?z=厦门&k=关键词
        public static function rebuild_talent_url ( $p ) {
            $fields = array();
            
            if ( $p['t'] ) {
                $fields['t'] = 't' .$p['t'];
            }
            if ( $p['a'] ) {
                $fields['t'] && $fields['a'] = '/';
                $fields['a'] .= 'a' .$p['a'];
            }
            if ( $p['s'] ) {
                ( ! $fields['a'] && $fields['t']) && $fields['s'] = '/';
                $fields['s'] .= 's' .$p['s'];
            }

            if ( $p['p_s'] ) {
                ( $p['t'] ||  $p['a'] || $p['s'] ) && $fields['p_s'] = '/';
                $fields['p_s'] .= 'ps' .$p['p_s'];
            }
            if ( $p['o'] ) {
                if ( $p['p_s'] ) {
                    $fields['o'] = '-';
                } elseif ( $p['t'] ||  $p['a'] || $p['s'] ) {
                    $fields['o'] = '/';
                }
                $fields['o'] .= 'o' .$p['o'];
            }
          
            if ( $p['page'] ) {
                ( $p['t'] ||  $p['a'] || $p['s'] || $p['p_s'] || $p['o'] ) && $fields['page'] = '/';
                $fields['page'] .= 'page' .$p['page'];
            }
            
            if ( $fields['t'] && ! $fields['a'] && ! $fields['s'] && ! $fields['p_s'] && ! $fields['o'] && ! $fields['page'] ) {
                $fields['ext'] .= '/';
            } elseif ( $fields ) {
                $fields['ext'] .= '.html';
            }
            
            if ( $p['z'] ) {
                $fields['z'] = '?z=' . trim($p['z']);
            }
            if ( $p['k'] ) {
                $fields['k'] = $fields['z'] ? '&' : '?';
                $fields['k'] .='k=' .  trim($p['k']);
            }
            
            return implode('', $fields);
        }
        
        // m1/t1f1r1e1/s15-o2/page2.html?k=关键词
        public static function rebuild_tasklist_url ( $p ) {
            $fields = array();
            
            if ( $p['m'] ) {
                $fields['m'] = 'm' .$p['m'];
            }
            if ( $p['t'] ) {
                $fields['m'] && $fields['t'] = '/';
                $fields['t'] .= 't' .$p['t'];
            }

            if ( $p['f'] ) {
               ( ! $fields['t'] &&  $fields['m'] ) && $fields['f'] = '/';
                $fields['f'] .= 'f' .$p['f'];
            }
            if ( $p['r'] ) {
                ( ( ! $fields['t'] && ! $fields['f']) && $fields['m'] ) && $fields['r'] = '/';
                $fields['r'] .= 'r' .$p['r'];
            }
            if ( $p['e'] ) {
                ( ( ! $fields['t'] && ! $fields['f'] && ! $fields['r']) && $fields['m'] ) && $fields['e'] = '/';
                $fields['e'] .= 'e' .$p['e'];
            }
            if ( $p['p_s'] ) {
                ( $p['m'] ||  $p['t'] || $p['f'] || $p['r'] || $p['e'] ) && $fields['p_s'] = '/';
                $fields['p_s'] .= 's' . $p['p_s'];
            }
            if ( $p['o'] ) {
                if ( $p['p_s'] ) {
                    $fields['o'] = '-';
                } elseif  ( $p['m'] ||  $p['t'] || $p['f'] || $p['r'] || $p['e'] ) {
                    $fields['o'] = '/';
                }
                $fields['o'] .= 'o' .$p['o'];
            }
          
            if ( $p['page'] ) {
                ( $p['m'] ||  $p['t'] || $p['f'] || $p['r'] || $p['e'] || $p['p_s'] || $p['o'] ) && $fields['page'] = '/';
                $fields['page'] .= 'page' .$p['page'];
            }
            
            if ( $fields['m'] && ! $fields['t'] && ! $fields['f'] && ! $p['r'] && ! $p['e'] && ! $fields['p_s'] && ! $fields['o'] && ! $fields['page'] ) {
                $fields['ext'] .= '/';
            } elseif ( $fields ) {
                $fields['ext'] .= '.html';
            }

            if ( $p['k'] ) {
                $fields['k'] ='?k=' .  trim($p['k']);
            }
            
            return implode('', $fields);
        }
        
	public static function cat_rewrite_url($type,$catid,$para="",$hot=""){
		global $basic_config;
		switch($type){
			case 'talent_indus':
				if ($para){
					parse_str( $para, $joint );
					$s = array_filter ( $joint );
					$pre = 'talent/';
					
					if ( $s ) {
                                            $catid = $s['c'] ? $s['c'] : $s['p'];
                                            $subcat = ( $catid ) ? self::cat_trac('indus', $catid).'/' : '';
                                            $url = self::rebuild_talent_url($s);
                                            // $url = implode(',', $s);
                                            $r = 'href="' . $basic_config['website_url'] . '/' . $pre . $subcat . $url . '"';
					} else {
                                           $subcat = ( $catid ) ? self::cat_trac('indus', $catid).'/' : '';
                                           $r = 'href="' . $basic_config['website_url'] . '/' . $pre . $subcat . '"';
					}
				}else{
					$r = 'href="' . $basic_config['website_url'] . '/' . $pre . '"';
				}
				break;
			case 'task_list':
				if ($para){
					parse_str( $para, $joint );
					$s = array_filter ( $joint );
					$pre = '';
					
					if ( $s ) {
                                            $catid = $s['i'] ? $s['i'] : $s['indus_id'];
                                            $subcat = ( $catid ) ? self::cat_trac('indus', $catid).'/' : 'task/';
                                            $url = self::rebuild_tasklist_url($s);
                                            //$url = $para;
                                            $r = 'href="' . $basic_config['website_url'] . '/' . $pre . $subcat . $url . '"';
					} else {
                                           $subcat = ( $catid ) ? self::cat_trac('indus', $catid).'/' : 'task/';
                                           $r = 'href="' . $basic_config['website_url'] . '/' . $pre . $subcat . '"';
					}
				}else{
					$r = 'href="' . $basic_config['website_url'] . '/' . $pre . '"';
				}
				break;	
			case 'indus':
			if ($para){
				parse_str ( $para, $joint );
				/* $str   = $joint['do'];
				unset($joint['do']); */
				$s = array_filter ( $joint );
				$url = http_build_query ( $s );
				
				$url = str_replace ( array ("do=", '&', '=' ), array ("", '-', '-' ), $url );
				//$url and $str.="-".$url;
				$hot = $hot ? "#" . $hot : '';
			
				$url and $r = 'href="'.$basic_config['website_url'].'/'.self::cat_trac($type,$catid).'/indus-i-'.$catid.'-'.$url . '.html' . $hot . '"'
				or $r = 'href="'.$basic_config['website_url'].'/'.self::cat_trac($type,$catid).'/"';
			}
			else{
				$r = 'href="'.$basic_config['website_url'].'/'.self::cat_trac($type,$catid).'/"';
			}
			break;
			case 'cat':
				global $eptarc_cat_arr;
				$eptarc_cat_arr or $eptarc_cat_arr = db_factory::get_table_data("*","witkey_article_category","",'','','','art_cat_id',3600);
				$seo_catname = self::cat_trac($type,$catid);
				
				// 帮助类型
				if ( $eptarc_cat_arr[$catid]['cat_type'] == 'help') {
					$pre = 'help/';
				}elseif ( $eptarc_cat_arr[$catid]['cat_type'] == 'case') {
					$pre = 'anli/';
				}elseif ( $eptarc_cat_arr[$catid]['cat_type'] == 'article') {
					$pre = '';
				}
				
				if ($para){
					parse_str ( $para, $joint );
					/* $str   = $joint['do'];
					unset($joint['do']); */
					$s = array_filter ( $joint );
					$url = http_build_query ( $s );
					
					$url = str_replace ( array ("do=", '&', '=' ), array ("", '-', '-' ), $url );
					//$url and $str.="-".$url;
					$hot = $hot ? "#" . $hot : '';

					if ( $eptarc_cat_arr[$catid]['cat_type'] == 'help') {
						$r = 'href="' . $basic_config['website_url'] . '/' . $pre . 'bang_' . $s['art_id'] . '.html"';
					}elseif ( $eptarc_cat_arr[$catid]['cat_type'] == 'case') {
						// 翻页
						if ($s['page']) {
							$r = 'href="'.$basic_config['website_url'].'/'. $pre . $seo_catname.'/page' . $s['page'] . '.html"';
						// 详情
						} else {
							$r = 'href="'.$basic_config['website_url'].'/'. $pre . $seo_catname.'/news_' . $s['art_id'] . '.html"';
						}
					}elseif ( $eptarc_cat_arr[$catid]['cat_type'] == 'article') {
						if ( $seo_catname == 'guzhu' || $seo_catname == 'weike' ) {
							$r = 'href="'.$basic_config['website_url'].'/'.$seo_catname.'/niu_' . $s['art_id'] . '.html"';
						} elseif ($url) {
							$r = 'href="'.$basic_config['website_url'].'/'.$seo_catname.'/article-i-'.$catid.'-'.$url . '.html' . $hot . '"';
						} else {
							$r = 'href="'.$basic_config['website_url'].'/'.$seo_catname.'/"';
						}
					}
				}
				else{
					if ( $eptarc_cat_arr[$catid]['cat_type'] == 'help' ) {
						$r = 'href="'.$basic_config['website_url'].'/help/bang/' . $seo_catname.'.html"';
					} else {
						$r = 'href="'.$basic_config['website_url'].'/'. $pre . $seo_catname.'/"';
					}					
				}
			break;
		}
		return $r;
	}

	public static function domain_key_check($key){
		global $kekezu;
		$deny_array = $kekezu->_cache_obj->get('domain_deny_names_list');
		
		if(!$deny_array){
			//作为系统目录而被禁止使用的名称  以及倍禁用的关键词
			$deny_array = array(
				'api',
				'auth',
				'base',
				'config',
				'control',
				'data',
				'epht',
				'keke_client',
				'lang',
				'lib',
				'model',
				'payment',
				'resource',
				'sql',
				'task',
				'tpl',
				'zt',
				'zhuanti',
				'case',
				'special',
				'anli',
				'shop',
				's',
			);
			//行业seoname
			$indus_seocat_list = db_factory::query("select seo_catname from ".TABLEPRE."witkey_industry");
			foreach($indus_seocat_list as $v){
				$deny_array[] = $v['seo_catname'];
			}
			//文章seoname
			$art_seocat_list = db_factory::query("select seo_catname from ".TABLEPRE."witkey_article_category");
			foreach($art_seocat_list as $v){
				$deny_array[] = $v['seo_catname'];
			}
		
			$kekezu->_cache_obj->set('domain_deny_names_list',$deny_array);
		}
		
		return !in_array($key,$deny_array);
		
	}
	
	public static function domain_rewrite_url($sid=0,$u_id=0,$para="",$hot=""){
		global $basic_config;
		//2个带缓存的索引数组
		
		$domain_uhash = db_factory::get_table_data('*',"witkey_shop_domain","d_status=1",'','','','uid',3600);
		$domain_shash = db_factory::get_table_data('*',"witkey_shop_domain","d_status=1",'','','','shop_id',3600);
		
		//查找对应的数据
		$sid and $domain_record = $domain_shash[$sid];
		$u_id and $domain_record = $domain_uhash[$u_id];
		
		//没有记录  原记录返回
		if(!$domain_record){
//			$t_str = 'sid='.$sid;
//			$sid or $t_str = 'u_id='.$u_id;
//			$r = 'href="'.$basic_config['website_url'].'/index.php?do=shop&'.$t_str;
//			
//			$para and $r.=$para;
//			$hot and $r.='#'.$hot;
//			$r.='"';
//			return $r;
			
			$t_str = 'sid='.$sid;
			$sid or $t_str = 'u_id='.$u_id;
			$param = "do=shop&";
			$param.= $t_str;
			$para and $param.=$para;
			return keke_tpl_class::rewrite_url($basic_config['website_url'].'/index-',$param,$hot);
			
		}
		
		
		if ($domain_record['d_type']==1){
			$d_url = "http://{$domain_record['d_key']}.itbangshou.com";
		}
		elseif ($domain_record['d_type']==2){
			$d_url = "http://{$domain_record['d_key']}";
		}
		else{
			$d_url = "/{$domain_record['d_key']}";
		}
		
		if ($para){
			parse_str ( $para, $joint );
			/* $str   = $joint['do'];
			unset($joint['do']); */
			$s = array_filter ( $joint );
			$url = http_build_query ( $s );
			
			$url = str_replace ( array ("do=", '&', '=' ), array ("", '-', '-' ), $url );
			//$url and $str.="-".$url;
			$hot = $hot ? "#" . $hot : '';
			
			
			
			$url and $r = 'href="'.$d_url.'/shop-sid-'.$domain_record['shop_id'].'-'.$url . '.html' . $hot . '"'
			or $r = 'href="'.$d_url.'/"';
		}
		else{
			$r = 'href="'.$d_url.'/"';
		}
			
		
		return $r;
	}
	
	public static function create_domain_config(){
		$r = '';
		$r2 = '';
		$domain_shash = db_factory::get_table_data('*',"witkey_shop_domain","d_status=1",'','','','shop_id',3600);
		
		foreach ($domain_shash as $k=>$v){
			if($v['d_type']==1){
				$r.='rewrite ^(.*)'.$v['d_key'].'.itbangshou.com/$ $1/index.php?do=shop&sid='.$v['shop_id'].'$ last
';
			}
			elseif($v['d_type']==2){
				$r.='rewrite ^(.*)'.$v['d_key'].'/$ $1/index.php?do=shop&sid='.$v['shop_id'].'$ last
';
			}
			else{
				$r.='rewrite ^(.*)/'.$v['d_key'].'/$ $1/index.php?do=shop&sid='.$v['shop_id'].'$ last
';
				$r2.='rewrite ^(.*)/'.$v['d_key'].'/(.*)$ $1/$2 last;
';
			}
			
		}
		$r.=$r2;
		
		file_put_contents(S_ROOT.'./domain.conf', $r);
	}
	/**
	 *SEO原因URL改写，新添类
	 *$pre_page判断page‘/'添加情况，1为要添加，0为不要添加
	 *$pre_so判断参数s与o前‘/’添加情况，1为要添加，0不要添加
	 *$pre_o判断参数o前‘-’添加情况，1为要添加，0不要添加
	 *@date 2012-09-20
	 *@param $para URL参数
	 *@author xilylg
	 * */
	private static function process_params($para){
		parse_str ( $para, $joint );
		$s = array_filter ( $joint );
		$s_proc=array();
		if($s['m']){
			$k='m'.$s['m'].'/';
			$s_proc[$k]='';
			$pre_page=0;
			$pre_so=0;
		}
		if($s['t']){
			$k='t'.$s['t'];
			$s_proc[$k]='';
			$pre_page=1;
			$pre_so=1;
		}
		if($s['f']){
			$k='f'.$s['f'];
			$s_proc[$k]='';
			$pre_so=1;
			$pre_page=1;
		}
		if($s['r']){
			$k='r'.$s['r'];
			$s_proc[$k]='';
			$pre_page=1;
			$pre_so=1;
		}
		if($s['e']){
			$k='e'.$s['e'];
			$s_proc[$k]='';
			$pre_page=0;
			$pre_so=1;
		}
		if($s['p_s']){
			$pre_so?$k='/':$k='';
			$k=$k.'s'.$s['p_s'];
			$s_proc[$k]='';
			$pre_o=1;//用于判断后面0的-
			$pre_so=0;
			$pre_page=1;
		}
		if($s['o']){
			if($pre_o){
				$k='-o';
			}elseif($pre_so){
				$k='/o';
			}else{
				$k='o';
			}
			$k=$k.$s['o'];
			$s_proc[$k]='';
			$pre_page=1;
		}
		if($s['page']){
			$pre_page?$k='/':$k='';
			$k=$k.'page'.$s['page'].".html";
			$s_proc[$k]='';
		}
		if($s['k']){
			$k='?K~'.$s['k'];
			$s_proc[$k]='';
		}
		$url = http_build_query ( $s_proc );
		$url=urldecode($url);
		$url = str_replace ( array ("do=", '&', '=','~' ), array ("", '', '','=' ), $url );
		return $url;
	}
	public static function epweike_cat_rewrite_url($type,$catid='',$para='',$hot=''){
		global $basic_config;
		switch($type){
			case 'indus':
				if ($para){
					$url=self::process_params($para);
					$hot = $hot ? "#" . $hot : '';
					if($url){
						if(substr($url,-1)!="/"&&substr($url,-5)!='.html'&&strpos($url,'=')===false){
							$r = 'href="'.$basic_config['website_url'].'/'.self::cat_trac($type,$catid).'/'.$url . '.html' . $hot . '"';
						}else{
							$r = 'href="'.$basic_config['website_url'].'/'.self::cat_trac($type,$catid).'/'.$url.'"';
						}
					}else{
						$r = 'href="'.$basic_config['website_url'].'/'.self::cat_trac($type,$catid).'/"';
					}
				}else{
					$r = 'href="'.$basic_config['website_url'].'/'.self::cat_trac($type,$catid).'/"';
				}
				break;
			case 'task_list':
				if ($para){
					$url=self::process_params($para);
					$hot = $hot ? "#" . $hot : '';
					$url&&substr($url,-1)!="/" and $r = 'href="'.$basic_config['website_url'].'/task/'.$url . '.html' . $hot . '"'
							or $r = 'href="'.$basic_config['website_url'].'/task/'.$url .$hot.'"';
				}else{
					$r = 'href="'.$basic_config['website_url'].'/task/"';
				}
				break;
			case 'cat':
				global $eptarc_cat_arr;
				$eptarc_cat_arr or $eptarc_cat_arr = db_factory::get_table_data("*","witkey_article_category","",'','','','art_cat_id',null);
				$seo_catname = self::cat_trac($type,$catid);
	
				// 帮助类型
				if ( $eptarc_cat_arr[$catid]['cat_type'] == 'help') {
					$pre = 'help/';
				}elseif ( $eptarc_cat_arr[$catid]['cat_type'] == 'case') {
					$pre = 'anli/';
				}elseif ( $eptarc_cat_arr[$catid]['cat_type'] == 'article') {
					$pre = '';
				}
	
				if ($para){
					parse_str ( $para, $joint );
					/* $str   = $joint['do'];
						unset($joint['do']); */
					$s = array_filter ( $joint );
					$url = http_build_query ( $s );
						
					$url = str_replace ( array ("do=", '&', '=' ), array ("", '-', '-' ), $url );
					//$url and $str.="-".$url;
					$hot = $hot ? "#" . $hot : '';
	
					if ( $eptarc_cat_arr[$catid]['cat_type'] == 'help') {
						$r = 'href="' . $basic_config['website_url'] . '/help/bang_' . $s['art_id'] . '.html"';
					}elseif ( $eptarc_cat_arr[$catid]['cat_type'] == 'case') {
						// 翻页
						if ($s['page']) {
							$r = 'href="'.$basic_config['website_url'].'/'. $pre . $seo_catname.'/page' . $s['page'] . '.html"';
							// 详情
						} else {
							$r = 'href="'.$basic_config['website_url'].'/'. $pre . $seo_catname.'/news_' . $s['art_id'] . '.html"';
						}
					}elseif ( $eptarc_cat_arr[$catid]['cat_type'] == 'article') {
						if ( $seo_catname == 'guzhu' || $seo_catname == 'weike' ) {
							$r = 'href="'.$basic_config['website_url'].'/'.$seo_catname.'/niu_' . $s['art_id'] . '.html"';
						} elseif ($url) {
							$r = 'href="'.$basic_config['website_url'].'/'.$seo_catname.'/article-i-'.$catid.'-'.$url . '.html' . $hot . '"';
						} else {
							$r = 'href="'.$basic_config['website_url'].'/'.$seo_catname.'/"';
						}
					}
				}
				else{
					$r = 'href="'.$basic_config['website_url'].'/'. $pre . $seo_catname.'/"';
				}
				break;
		}
		return $r;
	}

	// c1r1e1/s15-o2/page2.html?k=关键词
        public static function rebuild_hire_url ( $type, $param ) {
            global $_K;
            
            $fields = array();
            parse_str ( $param, $joint );
            $p = array_filter ( $joint );

            if ( $p['c'] ) {
                $fields['c'] = 'c' .$p['c'];
            }
            if ( $p['r'] ) {
                $fields['r'] = 'r' .$p['r'];
            }
            if ( $p['e'] ) {
                $fields['e'] = 'e' .$p['e'];
            }

            if ( $p['p_s'] ) {
                ( $p['c'] ||  $p['r'] || $p['e'] ) && $fields['p_s'] = '/';
                $fields['p_s'] .= 's' .$p['p_s'];
            }
            if ( $p['o'] ) {
                if ( $p['p_s'] ) {
                    $fields['o'] = '-';
                } elseif ( $p['c'] ||  $p['r'] || $p['e'] ) {
                    $fields['o'] = '/';
                }
                $fields['o'] .= 'o' .$p['o'];
            }
          
            if ( $p['page'] ) {
                ( $p['c'] ||  $p['r'] || $p['e'] || $p['p_s'] || $p['o'] ) && $fields['page'] = '/';
                $fields['page'] .= 'page' .$p['page'];
            }
            
            $fields['ext'] .= '.html';
            
            if ( $p['k'] ) {
                $fields['k'] = '?k=' . trim($p['k']);
            }
            
            $r = 'href="' . $_K['siteurl'] . '/hire/' . $type . '/' . implode('', $fields) . '"';
            return $r;
        }

        public static function rebuild_shop_url ( $sid, $param ) {
            global $_K;

           // $param = htmlspecialchars_decode(stripslashes($param));
	
            $shop_url = 'http://';

            if ( preg_match('/^www(.epweike(\.(com|net))?(\.cn)?)$/i', $_SERVER['HTTP_HOST'], $matchs) ) {
            	$shop_url .= 'shop' . $matchs[1] . '/' . $sid ;            	
            }elseif ( preg_match('/^shop.epweike(\.(com|net))?(\.cn)?$/i', $_SERVER['HTTP_HOST']) ) {
                    preg_match('/^(\/[^\/]+)\//i', $_SERVER['REQUEST_URI'], $matchs);

                    $shop_url .= $_SERVER['HTTP_HOST'] . $matchs[1];
            } else {
                    $shop_url .= $_SERVER['HTTP_HOST'];
            }

            $preg_searchs[] = "/(href|value)\=\"(.*?)index\.php\?do\=shop\&sid\=\w+\&view\=index\"/i";
            $preg_replaces[] = '\\1="' . $shop_url . '/"';

            $preg_searchs[] = "/(href|value)\=\"(.*?)index\.php\?do\=shop\&sid\=\w+\&view\=case_list\"/i";
            $preg_replaces[] = '\\1="' . $shop_url . '/anli/"';
            
            $preg_searchs[] = "/(href|value)\=\"(.*?)index\.php\?do\=shop\&sid\=\w+\&view\=service_list\"/i";
            $preg_replaces[] = '\\1="' . $shop_url . '/chushou/"';
            
            $preg_searchs[] = "/(href|value)\=\"(.*?)index\.php\?do\=shop\&sid\=\w+\&view\=mark\"/i";
            $preg_replaces[] = '\\1="' . $shop_url . '/jilu/"';            
            $preg_searchs[] = "/(href|value)\=\"(.*?)index\.php\?do\=shop\&sid\=\w+\&view\=mark\&mt\=1\"/i";
            $preg_replaces[] = '\\1="' . $shop_url . '/jilu/entry.html"';
            $preg_searchs[] = "/(href|value)\=\"(.*?)index\.php\?do\=shop\&sid\=\w+\&view\=mark\&mt\=2\"/i";
            $preg_replaces[] = '\\1="' . $shop_url . '/jilu/win.html"';
            $preg_searchs[] = "/(href|value)\=\"(.*?)index\.php\?do\=shop\&sid\=\w+\&view\=mark\&mt\=3\"/i";
            $preg_replaces[] = '\\1="' . $shop_url . '/jilu/cha.html"';
            
            $preg_searchs[] = "/(href|value)\=\"(.*?)index\.php\?do\=shop\&sid\=\w+\&view\=desc\"/i";
            $preg_replaces[] = '\\1="' . $shop_url . '/desc/"';
            
            $preg_searchs[] = "/(href|value)\=\"(.*?)index\.php\?do\=shop\&sid\=\w+\&view\=about\"/i";
            $preg_replaces[] = '\\1="' . $shop_url . '/about/"';
            
            $preg_searchs[] = "/(href|value)\=\"(.*?)index\.php\?do\=shop\&sid\=\w+\&view\=news_list\"/i";
            $preg_replaces[] = '\\1="' . $shop_url . '/news/"';

            $preg_searchs[] = "/(href|value)\=\"(.*?)index\.php\?do\=shop\&sid\=\w+\&view\=job_list\"/i";
            $preg_replaces[] = '\\1="' . $shop_url . '/job/"';

            $preg_searchs[] = "/(href|value)\=\"(.*?)index\.php\?do\=shop\&sid\=\w+\&view\=service_list\&service_type\=2\"/i";
            $preg_replaces[] = '\\1="' . $shop_url . '/chushou/idea/"';
            
            $preg_searchs[] = "/(href|value)\=\"(.*?)index\.php\?do\=shop\&sid\=\w+\&view\=service_list\&service_type\=1\"/i";
            $preg_replaces[] = '\\1="' . $shop_url . '/chushou/labout/"';

            $preg_searchs[] = "/(href|value)\=\"(.*?)index\.php\?do\=shop\&sid\=\w+\&view\=case_list\&cate_id\=(\w+)\"/i";
            $preg_replaces[] = '\\1="' . $shop_url . '/anli/\\3/"';

            $preg_searchs[] = "/(href|value)\=\"(.*?)index\.php\?do\=shop\&sid\=\w+\&view\=case_info\&case_id\=(\w+)\"/i";
            $preg_replaces[] = '\\1="' . $shop_url . '/anli/case_\\3.html"';
            
            $preg_searchs[] = "/(href|value)\=\"(.*?)index\.php\?do\=shop\&sid\=\w+\&view\=service_info\&service_id\=(\w+)\&service_type\=2\"/i";
            $preg_replaces[] = '\\1="' . $shop_url . '/chushou/idea_\\3.html"';
            
            $preg_searchs[] = "/(href|value)\=\"(.*?)index\.php\?do\=shop\&sid\=\w+\&view\=service_info\&service_id\=(\w+)\&service_type\=1\"/i";
            $preg_replaces[] = '\\1="' . $shop_url . '/sellfuwu_\\3.html"';

            $param = preg_replace ( $preg_searchs, $preg_replaces, $param );
            $r = $param ;
            
            return $r;
        }
        
}
?>