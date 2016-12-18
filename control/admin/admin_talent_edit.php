<?php
defined ( 'ADMIN_KEKE' ) or die ( 'Access Denied' );

// kekezu::admin_check_role ( 200 );
kekezu::get_tree ( $kekezu->_indus_arr, $indus_option_arr, "option" );
$table_obj = keke_table_class::get_instance("witkey_talent_link");
$tid = intval($tid);

switch( $ac ){
	case 'delete':
            if ( $tid ) {
                $ret = $table_obj->del('tid', $tid );
                
                kekezu::admin_system_log ( '删除人才推荐: ' .$tid );
                kekezu::show_msg('消息提示', $_SERVER['HTTP_REFERER'], 3, ( $ret) ? '操作成功!' : '操作失败!', $ret ? 'success' : 'warning');
            }
        break;
        case 'edit':
            if ( $link_info = $table_obj->get_table_info('tid', $tid) ) {
                $user_info = keke_user_class::get_user_info($link_info['uid']);
            }
            
        break;
	case 'add':
            $data = $pk = array();
            $data['uid'] = intval($txt_uid);
            $data['catid'] = intval($txt_catid);
            $data['level'] = intval($txt_level);
            $data['create_date'] = date('Y-m-d: H;i:s');

            // 存在 tid, 则为编辑
            $tid && $pk = array('tid' => $tid);
            
            if ( $data['uid'] ) {
                $ret = $table_obj->save ( $data, $pk );

                $ret && kekezu::show_msg( '操作成功!', 'index.php?do=' . $do, 3, '', 'success' );
            }
	break;
}
require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view );