<?php
$indus_arr = db_factory::query("select * from keke_witkey_industry where indus_type=1 order by listorder asc ");
$res = kekezu::echojson('get_indus',1,$indus_arr);
die();