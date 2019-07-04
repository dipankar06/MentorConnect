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

/* @package: core_project
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 26-03-2018
 * @Description: To reduce load on Ajax file And to use locallib functions.
*/

require_once(__DIR__ . '/../config.php');
include_once(__DIR__ .'/locallib.php');

require_login();
//define('AJAX_SCRIPT', true);
$urole = $USER->msn;
$rolename = get_atalrolenamebyid($urole);
$mode = $_REQUEST['mode'];

$html = '';
$outcome = new stdClass();
$outcome->success = 0;
$outcome->msg = "Error occurs";
$outcome->replyhtml = '';
if($mode == 'getcityvalue')
{
	$stateid = $_REQUEST['id'];
	//$citydata = $DB->get_records('city',array('stateid' =>$stateid));
	$citydata = get_atal_citybystateid($stateid);
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
}

if($mode=='getprojectcomments'){
	//Get List of Project Comments
	$projectid = encryptdecrypt_projectid($_REQUEST['id'],'de');
	if($projectid>0){
		$data = $DB->get_records('course_comment', array('courseid'=>$projectid,'isread'=>'n'));
		$html = "No Notification Found";
		if(count($data)>0){
			$html="";
			foreach($data as $key=>$values){
				$comdate = date('d-M-Y',$values->createdtime);
				$html.="<p>".$values->comment." (".$comdate.")</p>";
			}
			if($rolename=='student'){
				$sql="UPDATE {course_comment} SET isread='y' WHERE courseid=".$projectid;
				$DB->execute($sql);
				$html.= delete_rejectproject($projectid);
			}
		}
		$outcome->success = 1;
		$outcome->msg = "New Notification !";
		$outcome->replyhtml = $html;
	} else{
		$outcome->msg = "No Notification Found";
		$outcome->replyhtml = "No Notification Found";
	}
}

if($mode=="getmentor"){
	$id = $_REQUEST['id'];	
	$html = frmuserimghtmlnew($id);
	$outcome->success = 1;
	$outcome->msg = "user";
	$outcome->replyhtml = $html;
}

if($mode=="mentorschool"){
	//Remove Mentor From a School
	$schoolid = $_REQUEST['sid'];
	$mentorid = $_REQUEST['id'];
	$mentorid = encryptdecrypt_userid($mentorid,'de');
	if(!empty($schoolid) && !empty($mentorid)){	
		//UnEnroll Mentor from projects
		$sql="SELECT id from {course} WHERE idnumber=$schoolid AND enddate<>0";
		$projects = $DB->get_records_sql($sql);
		if(count($projects)>0){
			$user = $DB->get_record("user",array('id'=>$mentorid),'msn');
			$roleid = $user->msn;
			foreach($projects as $keys=>$values){
				$projectid = $values->id;
				frmunenroll_mentorproject($mentorid,$projectid,$roleid);
			}
		}
		$DB->delete_records("user_school", array('userid'=>$mentorid,'schoolid'=>$schoolid));
		$outcome->success = 1;
		$outcome->msg = "success";
		$outcome->replyhtml = '';
	}
}

echo json_encode($outcome);

die();
?>
