<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * @todo 安全验证、设置页面
 * 2011-10-18 09:10
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );


require keke_tpl_class::template ( "user/" . $do . "_".$view);


