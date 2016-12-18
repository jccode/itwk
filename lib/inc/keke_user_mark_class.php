<?php
/** 
 * @author Administrator
 * 
 * @see 用户信誉互评
 * 1.给威客互评
 * 2.给雇主互评
 * 3.得到威客的能力值，能力头衔，能力头标,扩展属性,好评率
 * 4.得到雇主的信誉值，头衔，头标,扩展属性,好评率
 * 
 * 
 */

keke_lang_class::load_lang_class('keke_user_mark_class');
class keke_user_mark_class {
	public function __construct() {
	}
	/**
	 * 获取互评配置
	 * @param  $type
	 * @param  $obj
	 */
	public static function get_mark_config($type = null, $obj = null) {
		$where = " where 1 = 1 ";
		$type and $where .= " and type='$type' ";
		$obj and $where .= " and obj='$obj'";
		$sql = " select * from " . TABLEPRE . "witkey_mark_config  $where ";
		$type && $obj and $mark_conf = db_factory::get_one ( $sql ) or $mark_conf = db_factory::query ( $sql );
		;
		return $mark_conf;
	}
	/**
	 * 生成互评记录
	 * @param  $model_code 评价对象 模型model_code
	 * @param  $obj_id 评价稿件(订单)Id
	 * @param  $obj_cash 评价对象获得金额     user_type为雇主时填威客中标所得，为威客时填任务总发布金额
	 * @param  $origin_id 源对象ID
	 * @param  $user_type 发起人角色 1=>威客,2=>雇主; 
	 * @param  $by_uid 	发起人
	 * @param  $by_username 发起人名
	 * @param  $uid 	被评人
	 * @param  $username 被评人名
	 */
	public static function create_mark_log($model_code,$user_type,$by_uid,$uid, $obj_id,$obj_cash,$origin_id = null, $by_username = null, $username = null) {
		global $basic_config;
		$mark_obj = new Keke_witkey_mark_class (); //互评对象
		! $by_username and $by_username = self::get_user_name ( $by_uid );
		! $username and $username = self::get_user_name ( $uid );
		/*判断此对象是否已生成过记录*/
		$mark_info = self::mark_info_exists($model_code, $obj_id,$by_uid,$uid);
		if ($mark_info){
			return false;
		}
		/*评价记录*/
		$mark_obj->_mark_id = null;
		$mark_obj->setBy_uid ( $by_uid );
		$mark_obj->setBy_username ( $by_username );
		$mark_obj->setUid ( $uid );
		$mark_obj->setUsername ( $username );
		$mark_obj->setModel_code($model_code );
		$mark_obj->setOrigin_id ( $origin_id );
		$mark_obj->setObj_id ( $obj_id );
		$mark_obj->setMark_type ($user_type);
		$mark_obj->setMark_status ( 0 );
		$mark_obj->setObj_cash($obj_cash);
		$mark_obj->setMark_time ( time () );
		$mark_obj->setMark_max_time ( time () + $basic_config ['auto_mark_time'] * 24 * 3600 );
		return $mark_obj->create_keke_witkey_mark();
	}
	/**
	 * 判断互评记录是否存在
	 * @param string $model_code 模型 code
	 * @param int $obj_id        对象 ID
	 * @param int $by_uid 发起人
	 * @param int $uid 被评人
	 */
	public static function mark_info_exists($model_code,$obj_id,$by_uid,$uid){
		return db_factory::execute(sprintf(" select model_code from %switkey_mark where model_code='%s' and obj_id = '%d' and uid='%d' and by_uid ='%d'",TABLEPRE,$model_code,$obj_id,$uid,$by_uid));
	}
	/**
	 * 用户互评完成
	 * @param  $mark_id
	 * @param  $content
	 * @param  $mark_cash 对象金额
	 * @param  $mark_status
	 * @param  $aid   辅助评价配置编号   ，分隔
	 * @param  $aid_star 辅助评价分值 ，分隔
	 */
	public static function exec_mark($mark_id, $content, $mark_status = '1', $aid = null, $aid_star = null) {
		global $_lang;
		$res = self::exec_mark_process($mark_id, $content, $mark_status, $aid, $aid_star);		
		$res and kekezu::keke_show_msg('',$_lang['congratulation_you_mark_success'],'','json') or kekezu::keke_show_msg('',$_lang['sorry_mark_fail'],'error','json');
	}	
	public static function exec_mark_process($mark_id, $content, $mark_status = '1', $aid = null, $aid_star = null){		
		global $_lang;
		$log_obj = new Keke_witkey_mark_class ();	
		$mark_info = self::get_single_mark ( $mark_id );
		$log_obj->setWhere ( "mark_id = '$mark_id'" );
		//修改评论只能往好了改   否则无效
		if (! $mark_info ['mark_status'] || $mark_status <= $mark_info ['mark_status']) {
			$log_obj->setMark_status ( $mark_status );
		}else{
			 kekezu::keke_show_msg('',$_lang['sorry_fail_level_not_down'],'error','json');
		}
		CHARSET=='gbk' and $content = kekezu::utftogbk($content);
		$log_obj->setMark_content ( $content );
		$log_obj->setAid($aid);		
		$log_obj->setAid_star($aid_star);
		$mark_value = self::get_mark_score ( $mark_info ['mark_type'], $mark_info[model_code], $mark_info['obj_cash'],$mark_status );
		$log_obj->setMark_value($mark_value);
		$res = $log_obj->edit_keke_witkey_mark();
			
		//第1次评论  结算分数
		 if(!$mark_info ['mark_status']){
		 	self::exec_rate ( $mark_status,$mark_info ['uid'], $mark_info ['obj_cash'], $mark_info ['model_code'], $mark_info ['mark_type'] ); //更新信誉，评价列率
		 	//更改稿件互评状态
		 	if(in_array($mark_info['model_code'],array('sreward','mreward','tender'))&&$mark_info['mark_type']==2){
		 		db_factory::execute('update '.TABLEPRE.'witkey_task_work set mark_status='.$mark_status.' where work_id='.$mark_info['obj_id']);
		 	}
		 }
		return $res;
	}
	/**
	 * 获取单条互评记录
	 * @param int $mark_id 互评编号
	 */
	public static function get_single_mark($mark_id){
		return db_factory::get_one(sprintf(" select * from %switkey_mark where mark_id = '%d'",TABLEPRE,$mark_id));
	}
	/**
	 * 更新space表的好评信息
	 * @param  $uid
	 * @param $mark_status 评价状态
	 * @param  $mark_cash  互评对象金额
	 * @param  $model_code 任务模型code
	 * @param  $mark_type  评价人身份 1 威客 2 雇主 。默认为雇主
	 */
	public static function exec_rate($mark_status,$uid, $mark_cash, $model_code, $mark_type = '2') {
			//计算mark_cash的信誉值(如果为计件模式，且金额<1,则信誉值不加)	
 		if($model_code=='preward'&&floatval($mark_cash)<1){
 			$mark_value=0;
 		}else{
 			$mark_value = self::get_mark_score ( $mark_type, $model_code, $mark_cash,$mark_status );
 		} 				
		//好评则好评数为1,其它评评不加
		$mark_status==1 and $good_num = 1 or $good_num = 0;
		/*** 根据评价者身份来获取被评价者的等级信息**/
		if($mark_type==1){
			$level_role = 2;
			$credit_value = $u_info['buyer_credit'];
			$edit_col = "buyer_good_num,buyer_total_num,buyer_credit";
		}else{
			$level_role = 1;
			$credit_value = $u_info['seller_credit'];
			$edit_col = "seller_good_num,seller_credit,seller_total_num";
		}
		//获取当前角色的信誉值
		$u_info = db_factory::get_one(sprintf("select %s from %switkey_space where uid = '%d'",$edit_col,TABLEPRE,$uid));
		
		if($mark_type==1){
			$credit_value = $u_info['buyer_credit'];
		}else{
			$credit_value = $u_info['seller_credit'];
		}
		//用户等级信息by 当前信誉值+已有信誉值
		$user_levels = self::get_mark_level($credit_value+$mark_value,$level_role);
		//用户等级信息序列化
		$user_level = serialize($user_levels);
	
		//更新字段数组
		if($mark_type==1){
			$space_array = array("buyer_credit"=>"`buyer_credit` + $mark_value","buyer_good_num"=>"`buyer_good_num`+$good_num",
			"buyer_total_num"=>"`buyer_total_num`+1","buyer_level"=>"$user_level"
			,'e_level'=>$user_levels['level'],'e_good_rate'=>($u_info['buyer_good_num']+$good_num)/($u_info['buyer_total_num']+1)*100);
		}elseif($mark_type==2){ 
			$space_array = array("seller_credit"=>"`seller_credit`+ $mark_value","seller_good_num"=>"`seller_good_num` +$good_num",
			"seller_total_num"=>"`seller_total_num`+1","seller_level"=>"$user_level",'w_level'=>$user_levels['level'],
			'w_good_rate'=>($u_info['seller_good_num']+$good_num)/($u_info['seller_total_num']+1)*100);
		}
		//更新条件
		$wheresqlarr = array("uid"=>$uid);
		//更新雇主的用户信息

		keke_table_class::updateself("witkey_space", $space_array, $wheresqlarr);
 	
	}
	/**
	 * 获取用户等级、头衔
	 * @param  $credit_value 信誉、能力
	 * @param  $user_type 当前用户身份1 威客 身份2 雇主身份
	 */
	public static function get_mark_level($credit_value, $user_type = '1') {
		global $_lang;
		$rule_obj = new Keke_witkey_mark_rule_class (); //规则对象
		$level_name = $_lang['level']; //LV标识
		$user_type == '1' and $credit_name = "m_value" or $credit_name = "g_value"; //取值判断
		$user_type == '1' and $title = "m_title" or $title = "g_title"; //名称判断
		$user_type == '1' and $ico = "m_ico" or $ico = "g_ico"; //图标判断
		$user_type == '1' and $tips = $_lang['ability_value'] or $tips = $_lang['credit_value']; 
		
		/*规则数组*/
		$score_rule = kekezu::get_table_data ( "*", "witkey_mark_rule", "", "$credit_name asc ", '', '', '', null );
		$size = sizeof ( $score_rule );
		for($i = 0; $i < $size; $i ++) {
			if ($credit_value <= $score_rule [0] [$credit_name]) { //比最小的还小
				$title = $score_rule [0] [$title]; //等级名
				$level = 1;//等级
				$pic = $score_rule [0] [$ico]; //等级图片
				$sc_id =$score_rule [0] ['mark_rule_id']; //等级规则编号
				$level_up = $score_rule [0] [$credit_name] - $credit_value; //升级剩余经验
				$grade_rate =number_format ( $credit_value/ ($score_rule [0] [$credit_name]), 4 ) * 100;
				
				break;
			} elseif ($credit_value <= $score_rule [$i] [$credit_name] && $credit_value >= $score_rule [$i-1] [$credit_name]) {
				$title = $score_rule [$i] [$title];
				$level = $i;
				$pic = $score_rule [$i] [$ico];
				$sc_id = $score_rule [$i] ['mark_rule_id'];
				$level_up = $score_rule [$i] [$credit_name] - $credit_value;
				$grade_rate = number_format ( ($credit_value-$score_rule [$i-1] [$credit_name]) / ($score_rule [$i] [$credit_name] - $score_rule [$i-1] [$credit_name]), 4 ) * 100;
				break;
			} elseif ($credit_value > $score_rule [$size - 1] [$credit_name]) { //比最大的还大
				$title = $score_rule [$size - 1] [$title];
				$level = $size;
				$pic = $score_rule [$size - 1] [$ico];
				$sc_id = $score_rule [$size - 1] ['mark_rule_id'];
				$level_up = '0';
				$grade_rate = "100";
				break;
			}
		}
		$experience_level_arr ['score_id'] = $sc_id;
		$experience_level_arr ['value'] = $credit_value;
		$experience_level_arr ['title'] = $title;
		$experience_level_arr ['level'] = $level;
		$experience_level_arr ['level_up'] = $level_up;
		$experience_level_arr ['level_name'] = $level_name;
		$experience_level_arr ['grade_rate'] = number_format($grade_rate,2);
		$experience_level_arr ['pic'] = '<img src="' . $pic . '" align="absmiddle" title="' . $_lang['title'] . ' ：' . $title . '&#13;&#10;'.$tips.'：' . $credit_value . '&#13;&#10;'.$level_name.'：'.$level.'">';
		return $experience_level_arr;
	}
	/**
	 * 获取等级头衔
	 * @param  $type 身份
	 */
	public static function get_mark_rule($type = null) {
		/*规则数组*/
		$score_rule = kekezu::get_table_data ( "*", "witkey_mark_rule", "", "g_value asc,m_value asc ", '', '', '', "3600" );
		$size = sizeof ( $score_rule );
		$score ['wiki'] = array (); //威客等级数组
		$score ['ploy'] = array (); //雇主等技数组
		$size = sizeof ( $score_rule );
		for($i = 0; $i < $size; $i ++) {
			$score ['wiki'] [0] ['min'] = "0";
			$score ['wiki'] [0] ['max'] = $score_rule [0] ['m_value'];
			$score ['wiki'] [$i + 1] ['min'] = $score_rule [$i] ['m_value'];
			$i <= $size - 1 and $score ['wiki'] [$i + 1] ['max'] = $score_rule [$i + 1] ['m_value'] or $score ['wiki'] [$i + 1] ['max'] = $score_rule [$size] ['m_value'];
			$score ['wiki'] [$i + 1] ['title'] = $score_rule [$i] ['m_title'];
			$score ['wiki'] [$i + 1] ['pic'] = $score_rule [$i] ['m_ico'];
			
			$score ['ploy'] [0] ['min'] = "0";
			$score ['ploy'] [0] ['max'] = $score_rule [0] ['g_value'];
			$score ['ploy'] [$i + 1] ['min'] = $score_rule [$i] ['g_value'];
			$i <= $size - 1 and $score ['wiki'] [$i + 1] ['max'] = $score_rule [$i + 1] ['g_value'] or $score ['wiki'] [$i + 1] ['max'] = $score_rule [$size] ['g_value'];
			$score ['ploy'] [$i + 1] ['title'] = $score_rule [$i] ['g_title'];
			$score ['ploy'] [$i + 1] ['pic'] = $score_rule [$i] ['g_ico'];
		}
		if ($type)
			return $score [$type];
		else
			return $score;
	}
	/**
	 * 获取互评信息
	 * @param array $w 前端查询条件数组
	 * ['mark_status'=>评论状态	
	 * 'mark_type'=>用户类型 --有值表示自己
	 * ......]
	 * @param array $p 前端传递的分页初始信息数组
	 * ['page'=>当前页面
	 * 'page_size'=>页面条数
	 * 'url'=>分页链接
	 * 'anchor'=>分页锚点]
	 * @return array $mark_arr
	 */
	public static function get_mark_info($w=array(),$p=array(),$order=null,$ext=null) {
		global $kekezu;
		$where = " select * from ".TABLEPRE."witkey_mark where 1 = 1";
		$ext and $where.=" and $ext ";
		$arr       = keke_table_class::format_condit_data($where,$order,$w,$p);
		$mark_info = db_factory::query ($arr['where'] );
		$mark_arr ['mark_info'] = $mark_info;
		$mark_arr ['pages'] = $arr['pages'];
		return $mark_arr;
	}
	
	/**
	 * 读取某个对象  或者一群对象的信誉记录
	 * 用obj type定位数据类型
	 * obj_id 可以是数字  也可以是数组
	 * obj_id的数组式设计为本函数主要目的
	 * 
	 * $model_code:模型 sreward 。。。
	 * 
	 * 典型范例：  稿件列表页..  
	 * 在控制层 $mark_log_arr = keke_mark_class::get_obj_mark_data('work','稿件ids');
	 * 则在内容模板上     $mark_log_arr[$work_id][$uid][mark_status]就可以得到用户是否已做过评价
	 * */
	public static function get_obj_mark_data($model_code, $obj_id, $pk = null) {
		$mark_log_obj = new Keke_witkey_mark_class ();
		
		if (is_array ( $obj_id )) {
			$mark_log_obj->setWhere ( "model_code = '$model_code' and obj_id in (" . implode ( ',', $obj_id ) . ") " );
		} else {
			$mark_log_obj->setWhere ( "model_code = '$model_code' and obj_id = '$obj_id'" );
		}
		
		$mark_arr = $mark_log_obj->query_keke_witkey_mark_log ();
		
		if (! is_array ( $mark_arr )) {
			return false;
		}
		$pk = $pk ? $pk : 'by_uid';
		$return = array ();
		foreach ( $mark_arr as $k => $v ) {
			if (is_array ( $obj_id )) {
				$return [$v ['obj_id']] [$v [$pk]] = $v;
			} else {
				$return [$pk] = $v;
			}
		}
		
		return $return;
	}
	/**
	 * 获取用户名
	 * @param $uid
	 */
	public function get_user_name($uid) {
		return db_factory::get_count ( " select username from " . TABLEPRE . "witkey_member where uid ='$uid' " );
	}
	/**
	 * 计算可得信誉值
	 * @param $cash 所得金额
	 * @param  $type 评价类型  1，威客 2 ，雇主
	 * @param  $mark_status 评价状态
	 * @param  $model_code 模型对象sreward。。
	 */
	public static function get_mark_score($type, $model_code, $cash, $mark_status = null) {
		$mark_config = self::get_mark_config ( $type, $model_code ); //该类型下的信誉规则
		$mark_status = intval ( $mark_status );
		$mark_status or $mark_status = 'good'; //默认好评
		$mark_status == 1 and $mark_status = 'good';
		$mark_status == 2 and $mark_status = 'normal';
		$mark_status == 3 and $mark_status = 'bad';
		return $mark_config [$mark_status] * $cash / 100;
	}
	/**
	 * 获取用户辅助评价
	 * @param  $uid   当前用户
	 * @param  $mark_type  评价发起者角色  1威客,2雇主
	 * @param $mark_status 评价 状态
	 * @param  $role_type //评价来源  1时来自他人的评价  2时对他人评价
	 * @param  $model_code  //评论对象所属模型
	 * @param  $obj_id  //评论对象编号
	 * @return $aid_info 主键：辅助配置编号 值:aid_name 辅助评标名 star：评标总分 count:评标总数
	 */
	public static function get_user_aid($uid, $mark_type, $mark_status = null, $role_type = null, $model_code = null, $obj_id = null) {
		$aid_config = self::get_mark_aid ( $mark_type ); //辅助配置
		$where = "  mark_type='$mark_type' and aid != ''";
		$role_type == '1' and $where .= " and uid='$uid'";
		$role_type == '2' and $where .= " and by_uid='$uid'";
		isset ( $mark_status )&&$mark_status!='n' and $where .= " and mark_status='$mark_status'";
		$model_code and $where .= " and model_code='$model_code' ";
		$obj_id and $where .= " and obj_id = '$obj_id' ";
		$aid_arr = db_factory::query ( " select aid,aid_star from " . TABLEPRE . "witkey_mark where $where " );
		$aid_info = array ();
		$si = sizeof ( $aid_arr );
		foreach ( $aid_config as $k => $v ) {
			if($aid_arr){
			for($i = 0; $i < $si; $i ++) {
				$aid_arr [$i] ['aid'] and $aid = explode ( ",", $aid_arr [$i] ['aid'] ) or $aid = array (); //辅助项
				$aid_arr [$i] ['aid_star'] and $star = explode ( ",", $aid_arr [$i] ['aid_star'] ) or $star = array (); //评分
				$aid&&$star and $aid_s = array_combine ( $aid, $star ); //数组合并
				$aid_info [$k] ['aid_name'] = $v ['aid_name']; //辅助项标题
				$aid_info [$k] ['star'] += floatval($aid_s [$k] ); //辅助总星数
				$aid_info [$k] ['count'] += 1; //辅助总数
			}
			}else{
				$aid_info [$k] ['aid_name'] = $v ['aid_name']; //辅助项标题
				$aid_info [$k] ['star']     = 5; //辅助总星数
				$aid_info [$k] ['count']    = 1; //辅助总数
			}
		}
		return self::consider_star ( $aid_info );
	}
	/**
	 * 计算辅助星级分数.
	 * @param $aid_info 
	 */
	
	public static function consider_star($aid_info = array()) {
		foreach ( $aid_info as $k => $v ) {
			
			$v ['count'] and $avg=$v ['star'] / $v ['count'] or $avg=0;
			
			$aid_info [$k] ['avg'] = number_format ($avg, 1 );
		}
		return $aid_info;
	}
	/**
	 *获取辅助评价配置
	 *@param $aid_type 配置类型 1=>威客 2=>雇主
	 */
	public static function get_mark_aid($aid_type = null) {
		$aid_arr = kekezu::get_table_data ( "*", "witkey_mark_aid" );
		$aid = array ();
		foreach ( $aid_arr as $v ) {
			$aid [$v ['aid_type']] [$v ['aid']] = $v;
		}
		if ($aid_type)
			return $aid [$aid_type];
		else
			return $aid;
	}
	/**
	 * 星星制造器
	 * @param int $num  星星的评分
	 * @param string $name 每组星星的名字
	 * @param boolean $disabled 星星是否可点
	 * @param $star_len 要生成的星星的长度(多少颗),有些地方需要5颗,有些地方需要10颗...
	 * @return input radio html
	 */
	public static function gen_star($num, $name,$disabled=true, $star_len=10) {
		$j = round ( $num * 2 );
		$str = "";
		$s = "";
		$disabled  and $ds = " disabled=\"disabled\" ";
		for($i = 1; $i <= $star_len; $i ++) { 
			if ($j == $i){
				$s = " checked ";
			}else{
				$s = "";
			}
			$str .= "<input name=\"star_$name\" type=\"radio\" class=\" star {split:2} star_$name\" value=\"$i\" 
     		$ds $s />";
			
		}
		$str.="<span id=\"span_star_$name\"></span>";
		//print_r($str);die();
		return  $str;
	}
	/**
	 * 星星制造，纯css
	 * @param $star 当前分数
	 * @param $f 是否显示分数
	 */
	public static function gen_star2($star,$f=true){
		$w    = round($star*16);
		$str  = '<span class="star a5"><span class="star_selected" style="width:'.$w.'px"></span></span>';
		$f&&$str.= '<b class="red">'.$star.'</b>';
		return $str;
	}
}
?>