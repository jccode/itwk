<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-10-8下午06:42:39
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$opps=array('add','list');

in_array($opp, $opps) or $opp='list';

require "user_" . $op."_".$opp.".php" ;


