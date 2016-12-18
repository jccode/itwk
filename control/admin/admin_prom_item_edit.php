<?php
/**
 * @copyright keke-tech
 * @author Liyingqing,update:2012-06-12 author wrh
 * @version v 1.3
 * 2012-06-12
 */
defined ( 'ADMIN_KEKE' ) or exit('Access Denied');

kekezu::admin_check_role ( 60 );
$tab_obj = keke_table_class::get_instance("witkey_prom_style");
$upload_obj = new keke_upload_class(UPLOAD_ROOT,array("gif",'jpeg','jpg','png'),UPLOAD_MAXSIZE);


if( $sbt_edit ) {
		$fds['s_type']=='site' and $fds['s_type'] = 'image';
		$fds['on_time'] = time();
		$files = $upload_obj->run('s_img1',1);
		$files = $upload_obj->run('s_img2',1);
		$files = $upload_obj->run('s_img3',1);
		$files = $upload_obj->run('s_img4',1);
		$files = $upload_obj->run('s_img5',1);
		$files = $upload_obj->run('s_img6',1);
		$files = $upload_obj->run('s_img7',1);
		$files = $upload_obj->run('s_img8',1);
		
		//$files!='The uploaded file is Unallowable!' and $s_img1 = $files['0']['saveName'];
         if ($files != 'The uploaded file is Unallowable!') {
			//获得文件名
			$img1_file = $files ['0'] ['saveName'];
			$img2_file = $files ['1'] ['saveName'];
			$img3_file = $files ['2'] ['saveName'];
			$img4_file = $files ['3'] ['saveName'];
			$img5_file = $files ['4'] ['saveName'];
			$img6_file = $files ['5'] ['saveName'];
			$img7_file = $files ['6'] ['saveName'];
			$img8_file = $files ['7'] ['saveName'];
		}
        $s_img1 = "data/uploads/" . UPLOAD_RULE.$img1_file;
        $s_img2 = "data/uploads/" . UPLOAD_RULE.$img2_file;
        $s_img3 = "data/uploads/" . UPLOAD_RULE.$img3_file;
        $s_img4 = "data/uploads/" . UPLOAD_RULE.$img4_file;
        $s_img5 = "data/uploads/" . UPLOAD_RULE.$img5_file;
        $s_img6 = "data/uploads/" . UPLOAD_RULE.$img6_file;
        $s_img7 = "data/uploads/" . UPLOAD_RULE.$img7_file;
        $s_img8 = "data/uploads/" . UPLOAD_RULE.$img8_file;
		
		
		$s_img1 and $fds['s_img1'] = $s_img1;
		$s_img2 and $fds['s_img2'] = $s_img2;
		$s_img3 and $fds['s_img3'] = $s_img3;
		$s_img4 and $fds['s_img4'] = $s_img4;
		$s_img5 and $fds['s_img5'] = $s_img5;
		$s_img6 and $fds['s_img6'] = $s_img6;
		$s_img7 and $fds['s_img7'] = $s_img7;
		$s_img8 and $fds['s_img8'] = $s_img8;
		if ($s_id) {
			
			$edit=$tab_obj->save($fds,$pk); //关系编辑
			kekezu::admin_system_log($_lang['edit_prom_material'] . $s_id );
			$edit &&  kekezu::admin_show_msg($_lang['prom_material_edit_success'],'',3,'','success');//die
		}
		
		$add = $tab_obj->save($fds); //关系添加
		kekezu::admin_system_log($_lang['add_prom_material']);
		$add && kekezu::admin_show_msg($_lang['prom_material_add_success'],'',3,'','success');
} else {
	$s_id and $item_info = db_factory::get_one(" select * from ".TABLEPRE."witkey_prom_style where s_id = '$s_id'");
}
require $template_obj->template ( 'control/admin/tpl/admin_'.$do.'_' . $view );