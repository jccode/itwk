<?php
define("IN_KEKE",TRUE);
include '../../app_comm.php';

db_factory::execute("update keke_witkey_task set task_cash = 0.01,real_cash=0.01,task_status = 0 where task_id = 192151");

db_factory::execute("update keke_witkey_order_charge set order_status = 'wait' where order_id = 2454");
db_factory::execute("update keke_witkey_order set order_status = 'wait' where order_id = 3688");
