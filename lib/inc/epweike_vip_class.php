<?php
/**
 * epweike的vip实现类
 * @copyright keke-tech
 * @author tank
 * @version v 2.0
 * 2012-5-23下午14:15:00
 */

class epweike_vip_class {
	
	static function special_valid($sp_id){
		
	}
	
	static function level_list(){
		return kekezu::get_table_data('*',"witkey_vip_level",'','listorder','','','level_id',null);
	}

	static function special_list(){
		return kekezu::get_table_data('*',"witkey_vip_special",'','','','','sp_key',null);
	}
	
}
?>