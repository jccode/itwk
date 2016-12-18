<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2010-5-17下午02:29:58
 */

defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
require $template_obj->template ( "control/admin/tpl/admin_{$do}_{$view}" );
