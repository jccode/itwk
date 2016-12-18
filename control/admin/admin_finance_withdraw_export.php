<?php
/**
 * 财务--数据导出
 * @copyright keke-tech
 * @author Ch
 * @version v 20
 * 2012-08-20 09:44:30
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 5 );

$start_time or $start_time = date("Y-m-d",time());
$finance_action = keke_glob_class::get_finance_action();

if($sbt_search){
	/** Error reporting */
	error_reporting(0);
	
	date_default_timezone_set('Europe/London');
	
	/** Include PHPExcel */
	require_once 'PHPExcel/PHPExcel.php';
	
	
	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	
	// Set document properties
	$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
								 ->setLastModifiedBy("Maarten Balliauw")
								 ->setTitle("Office 2007 XLSX Test Document")
								 ->setSubject("Office 2007 XLSX Test Document")
								 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Test result file");

	$objPHPExcel->setActiveSheetIndex(0);
	$objActSheet = $objPHPExcel->getActiveSheet();
	$objActSheet->setTitle('提现记录');
	
	//头部定义
	//合并单元格
	$objActSheet->mergeCells('A1:K2');
	$objActSheet->mergeCells('A3:A4');
	$objActSheet->mergeCells('B3:B4');
	$objActSheet->mergeCells('C3:C4');
	$objActSheet->mergeCells('D3:D4');
	$objActSheet->mergeCells('E3:F3');
	$objActSheet->mergeCells('G3:G4');
	$objActSheet->mergeCells('H3:H4');
	$objActSheet->mergeCells('I3:I4');
	$objActSheet->mergeCells('J3:L3');
	$objActSheet->mergeCells('M3:M4');
	//插入内容文字
	$objActSheet->setCellValue('A1', $start_time.'赏金支付申请表');
	$objActSheet->setCellValue('L1', '确认');
	$objActSheet->setCellValue('M1', '作成');
	$objActSheet->setCellValue('A3', 'NO.');
	$objActSheet->setCellValue('B3', '流水号');
	$objActSheet->setCellValue('C3', '申请人');
	$objActSheet->setCellValue('D3', '申请提现金额（元）');
	$objActSheet->setCellValue('E3', '手续费（元）');
	$objActSheet->setCellValue('E4', '手续费');
	$objActSheet->setCellValue('F4', '净收入');
	$objActSheet->setCellValue('G3', '实付赏金（元）');
	$objActSheet->setCellValue('H3', '提现账户');
	$objActSheet->setCellValue('I3', '支付账号');
	$objActSheet->setCellValue('J3', '提现明细');
	$objActSheet->setCellValue('J4', '任务编号');
	$objActSheet->setCellValue('K4', '任务类型');
	$objActSheet->setCellValue('L4', '金额');
	$objActSheet->setCellValue('M3', '备注');
	//设置头部单元格样式
	$objStyleA1 = $objActSheet->getStyle('A1');
	$objFontA1 = $objStyleA1->getFont();
	$objFontA1->setSize(22);
	$objFontA1->setName('calibri');
	$objFontA1->setBold(true);
	$objAlignA1 = $objStyleA1->getAlignment();
	$objAlignA1->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objAlignA1->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
	$objStyleL1 = $objActSheet->getStyle('L1');
	$objFontL1 = $objStyleL1->getFont();
	$objFontL1->setSize(14);
	$objFontL1->setName('calibri');
	$objFontL1->setBold(true);
	$objAlignL1 = $objStyleL1->getAlignment();
	$objAlignL1->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objAlignL1->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	//复制L1样式到M1
	$objActSheet->duplicateStyle($objStyleL1, 'M1');
	
	$objStyleA3 = $objActSheet->getStyle('A3');
	$objFontA3 = $objStyleA3->getFont();
	$objFontA3->setSize(10);
	$objFontA3->setName('calibri');
	$objFontA3->setBold(true);
	$objAlignA3 = $objStyleA3->getAlignment();
	$objAlignA3->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objAlignA3->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objAlignA3->setWrapText(true);
	$objFillA3 = $objStyleA3->getFill();
	$objFillA3->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objFillA3->getStartColor()->setRGB('FF99CC');
	$objBorderA3 = $objStyleA3->getBorders();
	$objBorderA3->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objBorderA3->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objBorderA3->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objBorderA3->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	//复制样式
	$objActSheet->duplicateStyle($objStyleA3, 'A3:M4');
	
	//设置单元格宽度
	$objActSheet->getColumnDimension('A')->setWidth(10);
	$objActSheet->getColumnDimension('B')->setWidth(10);
	$objActSheet->getColumnDimension('C')->setWidth(10);
	$objActSheet->getColumnDimension('D')->setWidth(15);
	$objActSheet->getColumnDimension('E')->setWidth(10);
	$objActSheet->getColumnDimension('F')->setWidth(10);
	$objActSheet->getColumnDimension('G')->setWidth(10);
	$objActSheet->getColumnDimension('H')->setWidth(25);
	$objActSheet->getColumnDimension('I')->setWidth(10);
	$objActSheet->getColumnDimension('J')->setWidth(15);
	$objActSheet->getColumnDimension('K')->setWidth(10);
	$objActSheet->getColumnDimension('L')->setWidth(10);
	$objActSheet->getColumnDimension('M')->setWidth(10);
	//初始化当前所在行
	$line_now = 5;
	//读取提现数据
	$withdraw_arr = db_factory::query("select * from keke_witkey_withdraw where withdraw_status=1 and applic_time>=UNIX_TIMESTAMP('$start_time') and applic_time<=UNIX_TIMESTAMP('$start_time')+24*60*60");
	
	if(count($withdraw_arr)>0){
		$i = 1;
		foreach ($withdraw_arr as $v){
			$finance_withdraw_arr = array();
			$finance_arr = array();
			//判断提现记录，作为获取财务变动记录的依据
			$finance_withdraw_arr = db_factory::query("SELECT fina_id,user_balance FROM keke_witkey_finance WHERE uid='".$v['uid']."' AND fina_action='withdraw' and fina_time<=".($v['applic_time']+30)." ORDER BY fina_id DESC LIMIT 0,2");
			$sql = "select a.*,t.task_id,t.task_title,t.model_id,t.task_type,t.task_cash_coverage,c.task_id as wtask_id,c.task_title as wtask_title,c.model_id as wmodel_id,c.task_type as wtask_type,c.task_cash_coverage as wtask_cash_coverage from keke_witkey_finance a left join keke_witkey_task_work b on a.obj_id=b.work_id left join keke_witkey_task c on b.task_id=c.task_id left join keke_witkey_task t on a.obj_id = t.task_id";
			if(count($finance_withdraw_arr)<2){
				$sql .= " where a.uid='".$v['uid']."' and a.fina_id<".$finance_withdraw_arr[0]['fina_id'];
			}else {
				$sql .= " where a.uid='".$v['uid']."' and a.fina_id<".$finance_withdraw_arr[0]['fina_id']." and a.fina_id>".$finance_withdraw_arr[1]['fina_id'];
			}
			$sql .= " order by fina_id asc";
			
			//读取上一次提现到本次提现之间的财务记录
			$finance_arr = db_factory::query($sql);
			$start_num = $line_now;
			if(count($finance_arr)){//当存在其他财务记录
				if(count($finance_withdraw_arr)==2){
					if($finance_withdraw_arr[1]['user_balance']>0){
						$objActSheet->setCellValue('k'.$line_now, '余额');
						$objActSheet->setCellValue('L'.$line_now, $finance_withdraw_arr[1]['user_balance']);
						$line_now = $line_now + 1;
					}
				}
				/* if ($finance_arr[0]['fina_cash']!=$finance_arr[0]['user_balance']){
					$objActSheet->setCellValue('k'.$line_now, '余额');
					$objActSheet->setCellValue('L'.$line_now, $finance_arr[0]['user_balance']-$finance_arr[0]['fina_cash']);
					$line_now = $line_now + 1;
				} */
				foreach ($finance_arr as $v1){
					if($v1['task_id']){
						$objActSheet->setCellValue('J'.$line_now, $v1['task_id']);
						$objActSheet->setCellValue('K'.$line_now, keke_glob_class::get_task_typename($v1['model_id'], $v1['task_type'], $v1['task_cash_coverage']));
					}elseif ($v1['wtask_id']){
						$objActSheet->setCellValue('J'.$line_now, $v1['wtask_id']);
						$objActSheet->setCellValue('K'.$line_now, keke_glob_class::get_task_typename($v1['wmodel_id'], $v1['wtask_type'], $v1['wtask_cash_coverage']));
					}else{
						$objActSheet->setCellValue('K'.$line_now, '其他');
					}
					if($v1['fina_type']=='out'){
						$objActSheet->setCellValue('L'.$line_now, '-'.strval($v1['fina_cash']));
					}else{
						$objActSheet->setCellValue('L'.$line_now, $v1['fina_cash']);
					}
					$objActSheet->setCellValue('M'.$line_now, $finance_action[$v1['fina_action']]);
					$line_now = $line_now + 1;
				}
				//单元格合并
				$objActSheet->mergeCells('A'.strval($start_num).':A'.strval($line_now-1));
				$objActSheet->mergeCells('B'.strval($start_num).':B'.strval($line_now-1));
				$objActSheet->mergeCells('C'.strval($start_num).':C'.strval($line_now-1));
				$objActSheet->mergeCells('D'.strval($start_num).':D'.strval($line_now-1));
				$objActSheet->mergeCells('E'.strval($start_num).':E'.strval($line_now-1));
				$objActSheet->mergeCells('F'.strval($start_num).':F'.strval($line_now-1));
				$objActSheet->mergeCells('G'.strval($start_num).':G'.strval($line_now-1));
				$objActSheet->mergeCells('H'.strval($start_num).':H'.strval($line_now-1));
				$objActSheet->mergeCells('I'.strval($start_num).':I'.strval($line_now-1));
			}else{//两条连续的提现记录
				//读取本次提现记录之前的那条记录，计算账户余额
				$before_info = db_factory::query("select * from keke_witkey_finance where uid=".$v['uid']." and fina_id<".$finance_withdraw_arr[0]['fina_id']." order by fina_id desc limit 1");
				if($before_info){
					$objActSheet->setCellValue('K'.$line_now, '余额');
					$objActSheet->setCellValue('L'.$line_now, $before_info[0]['user_balance']);
				}
				$line_now = $line_now + 1;
			}
			
			
			//主记录赋值
			$objActSheet->setCellValue('A'.strval($start_num), $i);
			$objActSheet->setCellValue('B'.strval($start_num), $v['withdraw_id']);
			if($v['pay_username']){
				$objActSheet->setCellValue('C'.strval($start_num), $v['pay_username']);
			}else{
				$objActSheet->setCellValue('C'.strval($start_num), '未填写');
			}
			$objActSheet->setCellValue('D'.strval($start_num), $v['withdraw_cash']);
			//手续费暂时不填
			$objActSheet->setCellValue('G'.strval($start_num), $v['withdraw_cash']);
			//$objActSheet->setCellValue('H'.strval($start_num), strval($v['pay_account']));
			$objActSheet->setCellValueExplicit('H'.strval($start_num), $v['pay_account'], PHPExcel_Cell_DataType::TYPE_STRING);
			//设置列表样式
			$objStyleA = $objActSheet->getStyle('A'.strval($start_num));
			$objFontA = $objStyleA->getFont();
			$objFontA->setSize(10);
			$objFontA->setName('calibri');
			$objAlignA = $objStyleA->getAlignment();
			$objAlignA->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objAlignA->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objBorderA = $objStyleA->getBorders();
			$objBorderA->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objBorderA->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objBorderA->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objBorderA->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			//复制到其他单元格
			$objActSheet->duplicateStyle($objStyleA, 'A'.strval($start_num).':M'.strval($line_now-1));
			//设置长数字格式
			$objStyleH = $objActSheet->getStyle('H'.strval($start_num));
			$objStyleH->getNumberFormat()
					  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
			
			$i = $i + 1;
		}
		//统计数据
		$line_now = $line_now + 5;
		$objActSheet->mergeCells('A'.strval($line_now).':B'.strval($line_now));
		$objActSheet->setCellValue('A'.strval($line_now), '合计');
		//$objActSheet->setCellValue('D'.strval($line_now), 'SUM(D5:D23)');
		$objActSheet->setCellValue('D'.strval($line_now), '=SUM(D5:D'.strval($line_now-1).')');
		$objActSheet->setCellValue('G'.strval($line_now), '=SUM(G5:G'.strval($line_now-1).')');
		$objActSheet->duplicateStyle($objStyleA, 'A'.strval($line_now).':M'.strval($line_now));
	}else{
		$objActSheet->setCellValue('A'.strval($line_now), '没有找到相应记录');
	}
	//备注信息
	$line_now = $line_now + 2;
	$objActSheet->mergeCells('A'.$line_now.':M'.$line_now);
	$objActSheet->setCellValue('A'.$line_now, '备注：本表统计的是当前日期还未处理的提现记录。其中的提现明细为上次提现到本次提现之间的财务记录，负值表示账户支出。');
	$objStyleAX = $objActSheet->getStyle('A'.$line_now);
	$objFontAX = $objStyleAX->getFont();
	$objFontAX->getColor()->setRGB('FF0000');
	$objAlignAX = $objStyleAX->getAlignment();
	$objAlignAX->setWrapText(true);
	
	// Redirect output to a client web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.$start_time.iconv("utf-8", "gb2312", '提现申请').'.xls"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
}

require keke_tpl_class::template ( 'control/admin/tpl/admin_finance_withdraw_export');