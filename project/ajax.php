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
 * @CreatedOn: 28-12-2017
 * @Description: ajax function
*/

require_once(__DIR__ . '/../config.php');
include_once(__DIR__ .'/lib.php');
include_once(__DIR__ .'/render.php');
require_login();
global $DB;
//define('AJAX_SCRIPT', true);
$urole = $USER->msn;
$rolename = get_atalrolenamebyid($urole);
$mode = $_REQUEST['mode'];
if($mode=='movetopage_school')
{
	$page = $_REQUEST['id'];
	$name= isset($_REQUEST['name'])?$_REQUEST['name']:'';
	$state= isset($_REQUEST['state'])?$_REQUEST['state']:0;
	$city= isset($_REQUEST['city'])?$_REQUEST['city']:0;
	$renderobj = new project_render($USER->id, $USER->msn);
	$content = showassign_mentorschooldata($page,$renderobj,$state,$city,$name);
	echo $content; 
	die;
}
if($mode=='assignmentorschool')
{
	$page = $_REQUEST['id'];
	$userid = $_REQUEST['userid'];
	$schoolid = $_REQUEST['schoolid'];
	$choiceid = $_REQUEST['choiceid'];
	$data = new StdClass();
	$data->userid=$userid;
	$data->schoolid=$schoolid;
	$data->role='mentor';
	$check_exist = $DB->get_record('user_school',array('userid'=>$userid,'schoolid'=>$schoolid));
	if(!$check_exist)
		$id = $DB->insert_record('user_school',$data);
	$data = new StdClass();
	$data->id=$choiceid;
	$data->status=3;
	$id = $DB->update_record('mentor_schoice',$data);
	if($id)
		echo "success";
	else
		echo "failed";
	die;
}
if($mode=='rejectmentorschool')
{
	$page = $_REQUEST['id'];
	$userid = $_REQUEST['userid'];
	$infoidid = $_REQUEST['infoidid'];
	$data = new StdClass();
	$data->id=$infoidid;
	$data->schoolid='';
	$id = $DB->update_record('user_info_data',$data);
	if($id)
		echo "success";
	else
		echo "failed";
	die;
}
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
if($mode=='enrol'){
	$projectid = $_REQUEST['id'];
	$user1 = $_REQUEST['uid1'];
	$user2 = $_REQUEST['uid2'];
	$usertype = $_REQUEST['type'];
	$temp = explode("A",$projectid);
	if(count($temp)==3){
		$pid = $temp[2];
		$pid1 = $temp[1] - 1;
		if($pid==$pid1){
			//projectid match, enrol user now.
			$userid = (empty($user1))?$user2:$user1;
			$enrol = $DB->get_record('enrol',array('enrol'=>'manual','courseid'=>$pid),'id');
			$count = $DB->count_records('user_enrolments', array('enrolid'=>$enrol->id,'userid'=>$userid));			
			if($count==0 && $userid>0){
				//add enrolment..
				$ue = new stdClass();
				$ue->enrolid      = $enrol->id;
				$ue->status       = 0;
				$ue->userid       = $userid;
				$ue->timestart    = ($usertype=='mentor')?0:time();
				$ue->timeend      = 0;
				$ue->modifierid   = $USER->id;
				$ue->timecreated  = time();
				$ue->timemodified = time();
				$DB->insert_record('user_enrolments', $ue);
				projectroleassignment($pid,$userid,$usertype);

				$html = frmuserimghtml($userid);
				//Remove Link
				$remove='<p><a href="javascript:void(0);" data-user="'.encryptdecrypt_userid($userid).'"';
				$remove.=' data-project="'.encryptdecrypt_projectid($pid).'" onclick="unenroluser(this)">Remove</a>';
				$remove.=($usertype=='mentor') ? '</br><span>Approval Pending</span>' : '';
				$html.= $remove.'</p>';
				$outcome->success = 1;
				$outcome->msg = "Record Saved !";
				$outcome->replyhtml = $html;
			} else{
				$outcome->msg = "Already Enroled !";
				$outcome->replyhtml = '';
			}
		}
	}
} elseif($mode=='assign'){
	//Assign mentor to a school;
	$schoolid = $_REQUEST['id'];
	$userid = $_REQUEST['uid1'];	
	$temp = explode("A",$schoolid);
	if(count($temp)==3){
		$pid = $temp[2];
		$pid1 = $temp[1] - 1;
		if($pid==$pid1){
			//match schholid with assign mentor		
			$count = $DB->count_records('user_school',array('userid'=>$userid,'schoolid'=>$pid));			
			if($count==0 && $userid>0 && !empty($pid)){
				//add mentor..
				$ue = new stdClass();
				$ue->userid     = $userid;
				$ue->schoolid   = $pid;
				$ue->role       = "mentor";
				$DB->insert_record('user_school', $ue);
				$html = frmuserimghtml($userid);
				$remove='<p><a href="javascript:void(0);" data-user="'.encryptdecrypt_userid($userid).'" 
				data-sid="'.$pid.'" onclick="removementorschool(this)">Remove</a>';
				$html.= $remove;
				$outcome->success = 1;
				$outcome->msg = "Record Saved !";
				$outcome->replyhtml = $html;
			} else{
				$outcome->success = 0;
				$outcome->msg = "Error, Duplicate value !";
				$outcome->replyhtml = "";
			}
		}
	}
} elseif($mode=='badge'){
	//Badge feature;
	$userid = $_REQUEST['id'];
	$type = $_REQUEST['type'];
	if(!empty($userid)){
		//get Student Projects
		$html = array();
		$data = get_myactiveprojects($userid);
		if(count($data)>0){
			foreach($data as $key=>$proj){
				$html[]= array($proj->id , $proj->project);
			}
		}
		$outcome->success = 1;
		$outcome->msg = "Record Saved !";
		$outcome->replyhtml = $html;
	} else{
		$outcome->success = 0;
		$outcome->msg = "Error !";
		$outcome->replyhtml = "";
	}
} elseif($mode=='savebadge'){
	//Assign Badge to student;
	$userid = $_REQUEST['id'];
	$projectid = $_REQUEST['pid'];
	$badgeid = $_REQUEST['bid'];
	if(!empty($userid) && !empty($projectid) && !empty($badgeid)){
		$flag = saveassignbadge($badgeid,$userid,$projectid);
		$outcome->success = 1;
		$outcome->msg = ($flag)?"Badge Assign Successfully !":"Badge Already Assigned";
		$outcome->replyhtml = $html;
	} else{
		$outcome->success = 0;
		$outcome->msg = "Error occurs, Please check selected badge or innovation!";
		$outcome->replyhtml = "";
	}
} elseif($mode=='suggestmentor'){
	//Show Watson Suggest Mentor in Assign Mentor Page.
	$projectid = $_REQUEST['id'];
	if(!empty($projectid)){
		$mentorshtml = "";//getsuggestmentors_project($projectid);
		$outcome->success = 1;
		$outcome->msg = "Approve";
		$outcome->replyhtml = urlencode($mentorshtml);
	}
} elseif($mode=='enrolmentor'){
	$projectid = $_REQUEST['id'];
	$key = $_REQUEST['key'];
	$temp = explode("A",$projectid);
	if(count($temp)==3){
		$pid = $temp[2];
		$pid1 = $temp[1] - 1;
		if($pid==$pid1){
			//projectid match, enrol user now.
			$usertype = "mentor";
			$userid = (empty($key)) ? 0 : encryptdecrypt_userid($key,'de');
			$enrol = $DB->get_record('enrol',array('enrol'=>'manual','courseid'=>$pid),'id');
			$count = $DB->count_records('user_enrolments', array('enrolid'=>$enrol->id,'userid'=>$userid));
			if($count==0 && $userid>0){
				//add enrolment..
				// From this point we make database changes, so start transaction.
				$transaction = $DB->start_delegated_transaction();
				$ue = new stdClass();
				$ue->enrolid      = $enrol->id;
				$ue->status       = 0;
				$ue->userid       = $userid;
				$ue->timestart    = 0;
				$ue->timeend      = 0;
				$ue->modifierid   = $USER->id;
				$ue->timecreated  = time();
				$ue->timemodified = time();
				$DB->insert_record('user_enrolments', $ue);
				projectroleassignment($pid,$userid,$usertype);
				assign_mentortoschool($pid,$userid);
				$transaction->allow_commit(); //close transaction
			}
			$html = frmuserimghtml($userid);
			$outcome->success = 1;
			$outcome->msg = "Record Saved !";
			$outcome->replyhtml = $html;
		}
	}
} elseif($mode=='del'){
	$postid = $_REQUEST['id'];
	$temp = explode("A",$postid);
	if(count($temp)==3){
		$pid = $temp[2];
		$pid1 = $temp[1] - 1;
		if($pid==$pid1){
			//id match, Delete Forum Post & discussion records
			$result = frmdelete_records($pid,$rolename);
			if($result){
				$outcome->success = 1;
				$outcome->msg = "Delete Success !";
			}else{
				$outcome->success = 0;
				$outcome->msg = "Error !";
				$outcome->replyhtml = "";
			}
		} else{
			$outcome->success = 0;
			$outcome->msg = "Error with id!";
			$outcome->replyhtml = "";
		}
	}
} elseif($mode=='suggestmentorschool'){
	//Show Watson Suggest Mentor in Assign Mentor To School Page. (NitiAdmin)
	$schoolid = $_REQUEST['id'];
	if(!empty($schoolid)){
		$mentorshtml = "";//getsuggestmentors_school($schoolid);
		$outcome->success = 1;
		$outcome->msg = "Success";
		$outcome->replyhtml = urlencode($mentorshtml);
	}
} elseif($mode=='unenroll'){
	//Remove user from a Project
	$projectid = $_REQUEST['id'];
	$userid = $_REQUEST['uid'];
	$projectid = encryptdecrypt_projectid($projectid,'de');
	$userid = encryptdecrypt_userid($userid,'de');
	if(!empty($userid)){
		//Unassign/remove mentors/student from a project
		try	{
			frmunenroll_userproject($userid,$projectid);
			$outcome->success = 1;
			$outcome->msg = "Record Saved !";
			$outcome->replyhtml = "";
		} catch(Exception $e){
			$outcome->success = 0;
			$outcome->msg = "Error!";
			$outcome->replyhtml = $e->getMessage();
		}
	}
} elseif($mode=='deleteproject'){
	//Delete a project
	$projectid = $_REQUEST['id'];
	$msg = $_REQUEST['msg'];
	if(!empty($projectid) && $rolename=='incharge'){
		$projectid = encryptdecrypt_projectid($projectid,'de');
		$course = $DB->get_record('course', array('id' => $projectid));
		delete_course($course,false);
		record_projectlog($course,'delete',$msg);
		$outcome->success = 1;
		$outcome->msg = "Innovation Deleted";
		$outcome->replyhtml = "Success ";
	} else{
		$outcome->success = 0;
		$outcome->msg = "Error !";
		$outcome->replyhtml = "";
	}
} elseif($mode=='approve'){
	//ATAL Chief Approves a Project created by Student.
	$projectid = $_REQUEST['id'];
	if(!empty($projectid) && $rolename=='incharge'){
		$projectid = encryptdecrypt_projectid($projectid,'de');
		$approveflag = approveproject($projectid);
		if($approveflag){
			activate_userenrolment($projectid,'student',$userid=0);
			$outcome->success = 1;
			$outcome->msg = "Approve";
			$outcome->replyhtml = "Approved";
		} else{
			$outcome->msg = "Cannot approve. Mentor acceptance still pending!";
		}
	} else{
		$outcome->success = 0;
		$outcome->msg = "Error !";
		$outcome->replyhtml = "";
	}
} elseif($mode=='reject'){
	//Atal Incharge Reject a Project created by Student.
	$projectid = $_REQUEST['id'];
	$msg = $_REQUEST['msg'];
	if(!empty($projectid) && $rolename=='incharge'){
		$projectid = encryptdecrypt_projectid($projectid,'de');
		rejectproject($projectid,$msg);
		record_projectlog($projectid,'reject',$msg);
		$outcome->success = 1;
		$outcome->msg = "Reject";
		$outcome->replyhtml = "Rejected";
	} else{
		$outcome->success = 0;
		$outcome->msg = "Error !";
		$outcome->replyhtml = "";
	}
} elseif($mode=='completeproject'){
	//Complete a Project created by Student.
	$projectid = $_REQUEST['id'];
	if(!empty($projectid) && $rolename=='incharge'){
		$projectid = encryptdecrypt_projectid($projectid,'de');
		$flag = completeproject($projectid);
		$outcome->success = 1;
		$outcome->msg = "Completed";
		$outcome->replyhtml = "Completed";
	} else{
		$outcome->success = 0;
		$outcome->msg = "Error !";
		$outcome->replyhtml = "";
	}
} elseif($mode=='mentoraccept'){
	//Project Accept or Reject By Mentor
	$projectid = $_REQUEST['id'];
	$status = $_REQUEST['flag'];
	$msg = $_REQUEST['msg'];	
	if(!empty($projectid) && $rolename=='mentor'){
		$projectid = encryptdecrypt_projectid($projectid,'de');
		frmproject_acceptbymentor($projectid,$status,$msg);
		$outcome->success = 1;
		$outcome->msg = "Completed";
		$outcome->replyhtml = "Completed";
	} else{
		$outcome->success = 0;
		$outcome->msg = "Error !";
		$outcome->replyhtml = "";
	}
} else{
	$data = json_decode(file_get_contents("php://input"));
	if(isset($data->disid)){
		$discussionid = $data->disid;
		$content = $data->reply;
		$temp = explode("A",$discussionid);
		$content = trim($content);
		if(count($temp)==3 && !empty($content)){
			$discussionid = $temp[2];
			include_once(__DIR__ . '/../forum/wordfilter.php');
			//Filter Badwords
			$badwordstr = filter_postreply($content);
			if(empty($badwordstr)){
				//Save your reply to DB;
				$postid = frmforum_add_newpost($discussionid,$content);
				if($postid>0){
					$html = getprojectreplyhtml($postid);
					$outcome->success = 1;
					$outcome->msg = "Record Saved !";
					$outcome->replyhtml = $html;
				}
			} else{
				$outcome->msg = "badwords";
				$outcome->replyhtml = $badwordstr;
			}
		}
	}
}

echo json_encode($outcome);

die();
?>