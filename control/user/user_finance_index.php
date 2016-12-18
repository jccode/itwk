<?php
/**
 * @copyright keke-tech
 * @author Aaron
 * @version v 2.0
 * 2012-06-25 9:03
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );


require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" .$op );