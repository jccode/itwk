<?php
/**
 * 财务--数据导出
 * @copyright keke-tech
 * @author Ch
 * @version v 20
 * 2012-08-20 09:44:30
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 76 );

$start_time or $start_time = date("Y-m-d",time());

if($sbt_search){
	/** Error reporting */
	error_reporting(E_ALL);
	
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
	$objActSheet->setTitle('入账记录');
	
	//头部定义
	//合并单元格
	$objActSheet->mergeCells('A1:A2');
	$objActSheet->mergeCells('B1:I2');
	$objActSheet->mergeCells('K1:L1');
	$objActSheet->mergeCells('K2:L2');
	$objActSheet->mergeCells('M1:N1');
	$objActSheet->mergeCells('M2:N2');
	
	//插入内容文字
	$objActSheet->setCellValue('B1', $start_time.'赏金入账一览表');
	$objActSheet->setCellValue('K1', '审核');
	$objActSheet->setCellValue('M1', '作成');
	$objActSheet->setCellValue('A3', '日期');
	$objActSheet->setCellValue('B3', '任务编号');
	$objActSheet->setCellValue('C3', '任务名称');
	$objActSheet->setCellValue('D3', '任务类型');
	$objActSheet->setCellValue('E3', '开始时间');
	$objActSheet->setCellValue('F3', '结束时间');
	$objActSheet->setCellValue('G3', '入账方式');
	$objActSheet->setCellValue('H3', '入账金额');
	$objActSheet->setCellValue('I3', '加价金额');
	$objActSheet->setCellValue('J3', '增值服务费');
	$objActSheet->setCellValue('K3', '加价日期');
	$objActSheet->setCellValue('L3', '责任人');
	$objActSheet->setCellValue('M3', '备注');
	$objActSheet->setCellValue('N3', '账户余额');
	//设置头部单元格样式
	$objStyleB1 = $objActSheet->getStyle('B1');
	$objFontB1 = $objStyleB1->getFont();
	$objFontB1->setSize(22);
	$objFontB1->setName('calibri');
	$objFontB1->setBold(true);
	$objAlignB1 = $objStyleB1->getAlignment();
	$objAlignB1->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objAlignB1->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objBorderB1 = $objStyleB1->getBorders();
	$objBorderB1->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objBorderB1->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objBorderB1->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objBorderB1->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	
	/* $objStyleL1 = $objActSheet->getStyle('L1');
	$objFontL1 = $objStyleL1->getFont();
	$objFontL1->setSize(22);
	$objFontL1->setName('calibri');
	$objFontL1->setBold(true);
	$objAlignL1 = $objStyleL1->getAlignment();
	$objAlignL1->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objAlignL1->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); */
	//复制L1样式到J1
	$objActSheet->duplicateStyle($objStyleB1, 'A1:N2');
	
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
	$objActSheet->duplicateStyle($objStyleA3, 'A3:N3');
	
	//设置单元格宽度
	$objActSheet->getColumnDimension('A')->setWidth(10);
	$objActSheet->getColumnDimension('B')->setWidth(10);
	$objActSheet->getColumnDimension('C')->setWidth(40);
	$objActSheet->getColumnDimension('D')->setWidth(10);
	$objActSheet->getColumnDimension('E')->setWidth(15);
	$objActSheet->getColumnDimension('F')->setWidth(15);
	$objActSheet->getColumnDimension('G')->setWidth(10);
	$objActSheet->getColumnDimension('H')->setWidth(10);
	$objActSheet->getColumnDimension('I')->setWidth(10);
	$objActSheet->getColumnDimension('J')->setWidth(10);
	$objActSheet->getColumnDimension('K')->setWidth(10);
	$objActSheet->getColumnDimension('L')->setWidth(10);
	$objActSheet->getColumnDimension('M')->setWidth(10);
	$objActSheet->getColumnDimension('N')->setWidth(10);
	//初始化当前所在行
	$line_now = 4;
	//读取充值记录
	$sql = "SELECT o.task_id,o.order_name,t.model_id,t.task_cash,t.real_cash,t.task_type,t.task_cash_coverage,t.task_status,t.start_time,t.sub_time,t.end_time,c.uid,c.pay_type,c.pay_money,c.pay_time,wo.order_name as uorder_name FROM keke_witkey_order_charge as c left join keke_witkey_order as o ON c.return_order_id=o.order_id left JOIN keke_witkey_task as t ON o.task_id=t.task_id left JOIN keke_witkey_order as wo on wo.obj_id=c.order_id WHERE c.order_status='ok' AND c.pay_time>=UNIX_TIMESTAMP('$start_time') AND c.pay_time<UNIX_TIMESTAMP('$start_time')+24*60*60";
	$recharge_arr = db_factory::query($sql);
	if($recharge_arr){
		$start_num = $line_now;
		foreach ($recharge_arr as $v){
			if(intval($v['task_id'])){
				$objActSheet->setCellValue('B'.$line_now, $v['task_id']);
				$objActSheet->setCellValue('C'.$line_now, $v['order_name']);
				$objActSheet->setCellValue('D'.$line_now, keke_glob_class::get_task_typename($v['model_id'], $v['task_type'], $v['task_cash_coverage']));
				$objActSheet->setCellValue('E'.$line_now, date('Y-m-d', $v['start_time']));
				if($v['task_status']==10){
					$objActSheet->setCellValue('F'.$line_now, '审核失败');
				}else{
					if($v['sub_time']){
						$objActSheet->setCellValue('F'.$line_now, date('Y-m-d', $v['sub_time']));
					}else{
						$objActSheet->setCellValue('F'.$line_now, date('Y-m-d', $v['end_time']));
					}
				}
			}else{
				$objActSheet->setCellValue('C'.$line_now, $v['uorder_name'].'['.$v['uid'].']');
				$objActSheet->setCellValue('D'.$line_now, '其他');
				$objActSheet->setCellValue('E'.$line_now, date('Y-m-d', $v['pay_time']));
			}
			
			if($v['pay_type']=='gopay'){
				$objActSheet->setCellValue('G'.$line_now, '国付宝');
			}elseif ($v['pay_type']=='alipayjs'){
				$objActSheet->setCellValue('G'.$line_now, '支付宝');
			}
			$objActSheet->setCellValue('H'.$line_now, $v['pay_money']);
			if($v['task_id']){
				if($v['model_id']==4 && $v['task_type']==1 && $v['task_cash_coverage']>0){
					if((intval($v['real_cash'])<intval($v['pay_money'])) && intval($v['task_id'])){
						$objActSheet->setCellValue('J'.$line_now, $v['pay_money']-intval($v['real_cash']));
					}
				}else{
					if((intval($v['task_cash'])<intval($v['pay_money'])) && intval($v['task_id'])){
						$objActSheet->setCellValue('J'.$line_now, $v['pay_money']-intval($v['task_cash']));
					}
				}
			}
			
			$line_now = $line_now + 1;
		}
		//合并A单元格
		$objActSheet->mergeCells('A'.strval($start_num).':A'.strval($line_now-1));
		$timearray = explode('-', $start_time);
		$objActSheet->setCellValue('A'.$start_num, $timearray['1'].'月'.$timearray['2'].'日');
		
		//设置列表样式
		$objStyleB = $objActSheet->getStyle('B'.$start_num);
		$objAlignB = $objStyleB->getAlignment();
		$objAlignB->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objAlignB->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objFontB = $objStyleB->getFont();
		$objFontB->setSize(10);
		$objFontB->setName('calibri');
		$objBorderB = $objStyleB->getBorders();
		$objBorderB->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objBorderB->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objBorderB->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objBorderB->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		//复制样式到其他单元格
		$objActSheet->duplicateStyle($objStyleB, 'A'.strval($start_num).':N'.strval($line_now-1));
	}else{
		$objActSheet->setCellValue('A'.strval($line_now), '没有找到相关记录');
	}
	//统计数据
	$line_now = $line_now + 1;
	$objActSheet->setCellValue('A'.strval($line_now), '合计');
	$objActSheet->setCellValue('H'.strval($line_now), '=SUM(H4:H'.strval($line_now-1).')');
	$objActSheet->duplicateStyle($objStyleB1, 'A'.strval($line_now).':N'.strval($line_now));
	
	$line_now = $line_now + 2;
	$objActSheet->mergeCells('A'.$line_now.':M'.$line_now);
	$objActSheet->setCellValue('A'.$line_now, '备注：本表统计的是所选日期，在线充值成功的记录。对应后台 财务管理->财务模块->充值审核 页面中已付款，并且充值时间对应所选日期的记录。客服手工处理的记录无法统计。');
	$objStyleAX = $objActSheet->getStyle('A'.$line_now);
	$objFontAX = $objStyleAX->getFont();
	$objFontAX->getColor()->setRGB('FF0000');
	$objAlignAX = $objStyleAX->getAlignment();
	$objAlignAX->setWrapText(true);
	
	// Redirect output to a client web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.$start_time.iconv("utf-8", "gb2312", '赏金入账').'.xls"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
}

require keke_tpl_class::template ( 'control/admin/tpl/admin_finance_recharge_export');