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

/* @package: block_atl_dashboard
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 12-12-2017
 * @Description: Plugin ajax calls
*/

require_once(__DIR__ . '/../../config.php');
include_once(__DIR__ .'/lib.php');

require_login();

define('AJAX_SCRIPT', true);

$urole = $USER->msn;
$rolename = get_atalrolenamebyid($urole);
$postid = $_REQUEST['id'];
$mode = $_REQUEST['mode'];

$outcome = new stdClass();
$outcome->success = 0;
$outcome->msg = "Error occurs";
$outcome->replyhtml = '';

if($mode=='del'){
	$temp = explode("A",$postid);	
	if(count($temp)==3){
		$pid = $temp[2];
		$pid1 = $temp[1] - 1;
		if($pid==$pid1){
			//id match, delete the record now
			$result = frmdelete_records($pid,$rolename);
			if($result){			
				$outcome->success = 1;
				$outcome->msg = "Delete Success !";
			}
		}
	}
} elseif($mode=='reportspam'){
	$temp = explode("A",$postid);
	if(count($temp)==3){
		$pid = $temp[2];
		$pid1 = $temp[1] - 1;
		if($pid==$pid1){
			//id match, Report Spam/Misuse
			$typeid = $_REQUEST['type'];
			$result = report_misuse($pid,$typeid);
			if($result){
				$outcome->success = 1;
				$outcome->msg = "Success !";
			}
		}
	}
} else{
	$data = json_decode(file_get_contents("php://input"));
	$discussionid = $data->disid;
	$content = $data->reply;
	$temp = explode("A",$discussionid);
	$content = trim($content);
	if(count($temp)==3 && !empty($content)){
		$discussionid = $temp[2];
		//Save your reply to DB;
		$postid = frmforum_add_newpost($discussionid,$content);
		if($postid>0){
			$html = dashboardmyreplyhtml($postid);
			$outcome->success = 1;
			$outcome->msg = "Record Saved !";
			$outcome->replyhtml = $html;
		}
	}
}

echo json_encode($outcome);

die();