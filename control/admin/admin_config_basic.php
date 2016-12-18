<?php
/**
 * @copyright keke-tech
 * @author Michael
 * @version v 2.0
 * 2010-5-17下午02:29:58
 */

defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

kekezu::admin_check_role ( 34 );

$config_basic_obj = new Keke_witkey_basic_config_class ();

$config_basic_arr = $config_basic_obj->query_keke_witkey_basic_config ();

foreach ( $config_basic_arr as $k => $v ) {
	$config_arr [$v ['k']] = $v ['v'];
}

isset ( $op ) and $url = "index.php?do=config&view=basic&op=$op" or $url = "index.php?do=config&view=basic&op=info";

$log_nav_arr = array (
		"info" => $_lang ['global_config'],
		"conf" => $_lang ['basic_config'],
		"seo" => $_lang ['seo_config'] 
);
// 是否编辑
if (isset ( $_POST ) && ! empty ( $_POST )) {
	foreach ( $_POST as $k => $v ) {
		$config_basic_obj->setWhere ( "k = '$k'" );
		$config_basic_obj->setV (kekezu::k_input($v));
		$res += $config_basic_obj->edit_keke_witkey_basic_config ();
	
	}
	
	kekezu::admin_system_log ( $_lang ['update'] . $log_nav_arr [$op] );
	
	if ($res) {
		
		$kekezu->_cache_obj->set ( "keke_witkey_basic_config", $config_basic_arr );
		
		kekezu::admin_show_msg ( $_lang ['submit_success'], $url, 3, '', 'success' );
	
	} else {
		
		kekezu::admin_show_msg ( $_lang ['website_config_fail'], $url, 3, '', 'warning' );
	}
}
if ($ac == 'get_url_rule') {
   $rule_arr = get_url_rule();
   require $kekezu->_tpl_obj->template( 'control/admin/tpl/admin_config_get_rule' );
   die();
}
function get_url_rule() {
	// 服务器
	$service = array (
			'apache',
			'iis6',
			'iis7',
			'nginx' 
	);
	$rule_arr = array ();
	// 参数长度为19个,步长为2
	
	foreach ( $service as $v ) {
		// apache
		switch ($v) {
			case 'apache' :
				$r='';
				$r .= "RewriteEngine On \r";
				//$r .= "RewriteBase " . rtrim ( COOKIE_PATH, '/'). "\r";
				$r .= "RewriteRule ^index.html$ index.php\r";
				$p = '(\w+)';
				for($i = 1; $i < 20; $i = $i + 2) {
					$ps = '';
					$ks = '';
					for($j = 0; $j < $i; $j ++) {
						$j == 0 ? $ps .= '(\w+)' : $ps .= '-(\w+)';
						$k = $j + 1;
						$k % 2 != 0 ? $ks .= '=$' . $k : $ks .= '&$' . $k;
					}
					$r .= "RewriteRule ^{$ps}.html$ index.php?do{$ks}\r";
				}
				$rule_arr [$v] = $r;
				break;
			case 'iis6' :
				$r = "RewriteEngine On\r";
				$r .= "RewriteCompatibility2 On\r";
				$r .= "RepeatLimit 200\r";
				$r .= "RewriteBase /\r";
				$r .= 'RewriteRule ^.*(?:global.asa|default\.ida|root\.exe|\.\.).*$ . [NC,F,O]\r';
				$r .= 'RewriteRule ^(.*)/index.html$ $1/index.php\r';
				$p = '(\w+)';
				$p = '(.*)';
				for($i = 2; $i <= 20; $i = $i + 2) {
					$ps = '';
					$ks = '';
					for($j = 0; $j < $i; $j ++) {
						if ($j == 0) {
							$ps .= $p . "/";
							$ks .= '$1/index.php?do';
						} elseif ($j == 1) {
							$ps .= '(\w+)';
						} else {
							$ps .= '-(\w+)';
						}
						$k = $j + 2;
						if ($j < $i - 1) {
							$k % 2 == 0 ? $ks .= '=$' . $k : $ks .= '&$' . $k;
						}
					}
					$r .= "RewriteRule ^{$ps}.html$ {$ks}\r";
				}
				$rule_arr [$v] = $r;
				break;
			case 'iis7' :
				$r='';
				$p = '(.*/)*';
				$h = "<rewrite><rules>\r";
				for($i = 2; $i <= 20; $i = $i + 2) {
					$ps = '';
					$ks = '';
					for($j = 0; $j < $i; $j ++) {
						if ($j == 0) {
							$ps .= $p;
							$ks .= '{R:1}/index.php';
						} elseif ($j == 1) {
							$ps .= '(\w+)';
						} else {
							$ps .= '-(\w+)';
						}
						$k = $j + 2;
						if ($j < $i - 1) {
							$k % 2 == 0 ? $ks .= '={R:' . $k . '}' : $ks .= '&amp;{R:' . $k . '}';
						}
					}
					$r .= <<<EOT
<rule name="{$i}">
	<match url="^{$ps}.index.html$" />
	<action type="Rewrite" url="{$ks}" />
</rule>
EOT;
				}
				$r = $h . $r;
				$r .= "</rules> ";
				$r .= "</rewrite>";
				$rule_arr [$v] = htmlentities($r);
				break;
			case 'nginx' :
				$r = "rewrite ^(.*)/index.html$ $1/index.php last;\r";
				$p = '(.*)/';
				for($i = 2; $i <= 20; $i = $i + 2) {
					$ps = '';
					$ks = '';
					for($j = 0; $j < $i; $j ++) {
						if($j==0){
							$ps.= $p;
							$ks .= '$1/index.php?do';
						}elseif($j==1){
							$ps.= '(\w+)';
						}else{
							$ps.= '-(\w+)';
						}
						$k = $j+2;
						if($j<$i-1){
							$k%2==0?$ks.= '=$'.$k:$ks.= '&$'.$k;
						}
					}
					$r .=  "rewrite ^{$ps}.html$ $ks last; \r";
				}
				$rule_arr [$v] = $r;
				break;
		}
	
	}
	return $rule_arr;

}


require $template_obj->template ( 'control/admin/tpl/admin_config_' . $view );