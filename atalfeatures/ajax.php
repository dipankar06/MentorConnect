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

/* @package core_project
 * @CreatedBy:Jothi (IBM)
 * @CreatedOn:14-2-2018
 * @Description: Ajax calls are written here
*/
require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/lib.php');
$id = $_REQUEST['id'];
$mode = $_REQUEST['mode'];
$html = '';
$outcome = new stdClass();
$outcome->success = 0;
$outcome->msg = "Error occurs";
$outcome->replyhtml = '';

if($mode == 'getcityvalue')
{
	//$citydata = $DB->get_records('city',array('stateid' =>$id));
	$citydata = get_atal_citybystateid($id);
	if(count($citydata)>0){
		$city_options = '';
		$html = array();
		foreach($citydata as $key=>$values){
			$html[]= array($values->id , $values->name);
		}
		$outcome->success = 1;
		$outcome->msg = "Record Saved !";
		$outcome->replyhtml = $html;	
	} else{
		$outcome->success = 0;
		$outcome->msg = "Error, No Records !";
		$outcome->replyhtml = "";
	}
	echo json_encode($outcome);
	die();
}else if($mode == 'mentorschedule'){
	if(!empty($id)){
		$status = ($_REQUEST['status']=='y')?3:4;
		$cm  = new stdClass();
		$cm->id = $id;
		$cm->meetingstatus = $status;
		$DB->update_record('event', $cm);
		$outcome->success = 1;
		$outcome->msg = '';
		$outcome->replyhtml = '';
	}
	echo json_encode($outcome);
	die();
}else if($mode == 'loadschool-by-cityid')
{
	$school_array = array();
	$school_array = getschool_bycity($id);
	$outcome->success=1;
	$outcome->msg="";
	if($school_array)
		$outcome->replyhtml = $school_array;
	echo json_encode($outcome);
	die();
}
else if($mode == 'loadschool-by-cityid-mchoice') 
{
	$school_array = array();
	$school_array = getschool_bycity_mentorchoice($id);
	$outcome->success=1;
	$outcome->msg="";
	if($school_array)
		$outcome->replyhtml = $school_array;
	echo json_encode($outcome);
	die();
}
else if($mode == 'getbulkmail_dropdown')
{
	if($id == 'incharge')
		$criteria = getBulkMailReport_Dropdown('school');
	else if ($id == 'mentor')
		$criteria = getBulkMailReport_Dropdown('mentor');
	else if ($id == 'aschool')
		$criteria = getBulkMailReport_Dropdown('aschool');
	echo json_encode($criteria);
	die();
}

?>