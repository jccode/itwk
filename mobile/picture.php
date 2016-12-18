<?php
/**
 * 头像更改
 * @copyright keke-tech
 */
defined ( 'IN_KEKE' ) && defined ( 'ISWAP' ) && ISWAP or kekezu::echojson ( $wap_msg, 0 );
//kekezu::echojson ( array ('r' => $_FILES ['uploadedfile'] ['name']."___".$pic_uid ),0 );die();
if ($_FILES ['uploadedfile'] ['name']) {
	$file_path = keke_file_class::upload_file ( 'uploadedfile' );
	if ($file_path) {
		$file_type = 'jpg';
		$dir = S_ROOT . "./data/avatar";
		
		$pic_path = "$_K[siteurl]/data/avatar/" . keke_user_avatar_class::get_avatar ( $pic_uid, "larger" );
		
		$pic_uid = sprintf ( "%09d", $pic_uid );
		$dir1 = substr ( $pic_uid, 0, 3 );
		$dir2 = substr ( $pic_uid, 3, 2 );
		$dir3 = substr ( $pic_uid, 5, 2 );
		! is_dir ( $dir . '/' . $dir1 ) && mkdir ( $dir . '/' . $dir1, 0777 );
		! is_dir ( $dir . '/' . $dir1 . '/' . $dir2 ) && mkdir ( $dir . '/' . $dir1 . '/' . $dir2, 0777 );
		! is_dir ( $dir . '/' . $dir1 . '/' . $dir2 . '/' . $dir3 ) && mkdir ( $dir . '/' . $dir1 . '/' . $dir2 . '/' . $dir3, 0777 );
		$save_path = $dir . '/' . $dir1 . '/' . $dir2 . '/' . $dir3;
		
		//kekezu::echojson ( array ('r' =>$pic_path), 0 );
		
		@file_put_contents($save_path."/" . substr ( $pic_uid, 7, 2 ) . "_avatar.txt","cus|".substr ( $pic_uid, 7, 2 ),FILE_APPEND);
		$size_a = array (48, 48, "$save_path/" . substr ( $pic_uid, 7, 2 ) . "_avatar_small." . $file_type );
		$size_b = array (120, 120, "$save_path/" . substr ( $pic_uid, 7, 2 ) . "_avatar_middle." . $file_type );
		$size_c = array (200, 200, "$save_path/" . substr ( $pic_uid, 7, 2 ) . "_avatar_big." . $file_type );
		$res = keke_img_class::resize ( $file_path, $size_a, $size_b, $size_c, true );
		$res and kekezu::echojson ( array ('r' =>$pic_path), 1 ) or kekezu::echojson ( array ('r' => 'upload failed!' ), 0 );
		die ();
	}
}else{
	kekezu::echojson ( array ('r' => 'upload failed!' ),0 );
}
die ();
