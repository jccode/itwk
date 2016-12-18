<?php
/**
 * @copyright keke-tech
 * @author Aaron
 * @version v 2.0
 * 2012-2-25早上9:50
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
//$page_title = $_lang['mobi_version'].'- '.$_K['html_title'];


$dynamic = kekezu::get_table_data ( "art_id,art_cat_id,art_title,content,seo_desc,pub_time", "witkey_article", " art_cat_id=587 ", " pub_time desc", "", "5", "", 86400 );

require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );
