<?php
/*
Copyright (C) 2019  IBM Corporation 
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.
 
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details at 
http://www.gnu.org/licenses/gpl-3.0.html
*/

/* @package: core_nitiadmin
 * @CreatedBy: Jothi (IBM)
 * @CreatedOn: 22-06-2018
 * @Description: Download Mentor Training Report in Excel Sheet
*/

/** Include PHPExcel */
require_once('../config.php');
require_once('render.php');
require_once('lib.php');
//require_once dirname(__FILE__) . '/PHPExcel-1.8/Classes/PHPExcel.php';
require_once("$CFG->libdir/phpexcel/PHPExcel.php");
$tp = $_REQUEST['tp']; // To Be Printed
if(isset($tp)){
$renderObj = new NitiAdministrationRender();
switch($tp)
{
	case 'mentoractivity' :
		$title = 'Mentor Activity Report';
		$appendfilename = 'MentorActivityreport-';
		$heading = array('Email','S.No','First Name','Last Name','Gender','DOB','Schools','Has the mentor attempted a login (Y/N)','Has the mentor finished updating the profile? (Y/N)','Has the mentor started the mentor training module? (Y/N)','Has the mentor finished the mentor training module? (Y/N)','No.of session invitation sent by the mentor to the school','No.of sessions invitation accepted by the mentor from the school','No.of sessions invitation rejected by the mentor from the school','No.of sessions taken by the mentor');
		$reportObj = $renderObj->getContentForExcel();
		break;
	case 'mentoribm' :
		$title = 'IBM Mentor Report';
		$appendfilename = 'ibmmentor-';
		$heading = array('Email','S.No','First Name','Last Name','City','Contact','Created On','Has the mentor attempted a login (Y/N)','Has the mentor finished updating the profile? (Y/N)','Has the mentor started the mentor training module? (Y/N)','Has the mentor finished the mentor training module? (Y/N)','No.of session invitation sent by the mentor to the school','No.of sessions invitation accepted by the mentor from the school','No.of sessions invitation rejected by the mentor from the school','No.of sessions taken by the mentor');
		$reportObj = $renderObj->getContentForExcel_Ibmmentor();
		break;
	case 'schoolactivity' :
		$title = 'School Activity Report';
		$appendfilename = 'SchoolActivityreport-';
		$heading = array('School Name','S.No','ATL id','District','State','School Email Id','Has the School attempted a login (Y/N)','Has the School finished updating the profile? (Y/N)','No.of session invitation sent by the school to the mentor','No.of session invitation accepted by the school from the mentor','No.of session invitation rejected by the school from the mentor');
		$reportObj = $renderObj->getSchoolActivtyReportExcel();
		break;
	case 'meetingreport':
		$title = 'Meeting School Meeting Report';
		$appendfilename = 'mentor-school-meetingreport-';
		$heading = array('Title','S.No','intiated By','Assigned to','Meeting Description','Date Created','Meeting Status');
		$reportObj = $renderObj->getMeetingReportExcel();
		break;
	case 'studentactivity':
		$title = 'Student Activity Report';
		$appendfilename = 'studentactivity-meetingreport-';
		$heading = array('Student Email id','S.No','Student Name','Town/District','Student Contact','School Name','School ATL id','School EmailId','School Phone','Has the Student attempted a login','Has the Student finished updating the profile?');
		$reportObj = $renderObj->getStudentActivtyReportExcel();
		break;
	case 'sessionreport':
		$title = 'Mentor & School Session Report';
		$appendfilename = 'mentorschool-sessionreport-';
		$heading = array('Mentor Name','S.No','Mentor Email','School Name','School ATL id','Session Date','Session Started At','Session Ended At','Session Type','Total Students','Session Details');
		$reportObj = $renderObj->getSessionReportExcel();
		break;
}
 // Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Niti Admin")
							 ->setLastModifiedBy("Niti Admin")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");
// Print the Headings
$objPHPExcel->getDefaultStyle()->getAlignment()->setWrapText(true);
//$objPHPExcel->getActiveSheet()->getStyle("$from:$to")->getFont()->setBold( true );
$range = range("A", "Z");
foreach($heading as $key=>$value)
{
	$cell = $range[$key].'1';
	$objPHPExcel->getActiveSheet()->getColumnDimension($range[$key])->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00'); 
	$objPHPExcel->getActiveSheet()->getStyle($cell)->getFont()->setBold( true );
	//$objPHPExcel->getActiveSheet()->getStyle($cell)->getAlignment()->setWrapText( true );
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cell,$value);
}
// Print Data
$i=2;
foreach($reportObj as $key=>$value)
{
	$cell = $range[$key].($key+1);
	foreach($value as $innerkey=>$reportvalue)
	{
		$index= array_search($innerkey,array_keys($value));
		$cell = $range[$index].$i;
		//$objPHPExcel->getActiveSheet()->getStyle($cell)->getAlignment()->setWrapText( true );
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cell,$reportvalue);
		//$range[$index].$i."=>".$reportvalue;
	}
	$i++;
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle($title);
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = $appendfilename.gmdate('d-M-Y:H:i:s').'.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=$filename");
header('Cache-Control: max-age=0');
// If IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
}
