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
 * @CreatedOn: 17-03-2018
 * @Description:  Only Functions specific to Innovations/Project is here.
*/

require_once($CFG->libdir.'/filelib.php');

//Show Assign Projects in Innovation Page for Mentor.
function showassignproject_mentor(innovation_render $renderobj){
	global $CFG,$USER;
	$userrole = get_atalrolenamebyid($USER->msn);
	$content = html_writer::start_tag('div', array('class' => 'assignmentor'));
	$content.= "<div class='heading'><h3>Innovations</h3></div><br></br>";	
	
	$data = mentorprojects();
	$content.= '
	<div id="atal-innovation" data-region="atldashboard">
		<ul id="block-atldashboard-view-choices" class="nav nav-tabs" role="tablist">';		
		
		$content.= '<li class="nav-item">
		<a id="v1" class="nav-link active" href="#myoverview_approve_view" role="tab" data-toggle="tab" data-tabname="courses"  aria-expanded="true">
		Acceptance Pending
		</a>
		</li>';	
		$content.= '<li class="nav-item">
		<a id="v2" class="nav-link" href="#myoverview_assign_view" role="tab" data-toggle="tab" data-tabname="timeline" aria-expanded="false">
		Ongoing
		</a>
		</li>';	
		$content.= '<li class="nav-item">
		<a id="v3" class="nav-link" href="#myoverview_archive_view" role="tab" data-toggle="tab" data-tabname="projects" aria-expanded="false">
		Completed Projects	
		</a>
		</li></ul>';
		
		$content.= '		
		<div class="tab-content content-centred tabcontentatal">';			
			
			$content.= '<div role="tabpanel" class="tab-pane fade active in" id="myoverview_approve_view" aria-expanded="true">
			<div id="approve-view" data-region="approve-view">';
			$approvedata = '';
			if(count($data)>0){				
				foreach($data as $key=>$values){
					if($values[0]['status']=="unapprove"){
						$approvedata.= $renderobj->showunapproveproject_mentor($values);
					}
				}
			}
			$content.= (empty($approvedata)) ? '<div class="atlmessage">Innovations Not Found</div>' : $approvedata;			
			$content.='</div></div>';
			
			$content.='<div role="tabpanel" class="tab-pane fade" id="myoverview_assign_view" aria-expanded="false">
			<div id="assign-view" data-region="assign-view">';
			$unapprovedata = '';
			if(count($data)>0){
				foreach($data as $key=>$values){
					if($values[0]['status']=="active" && $values[0]['mytimestart']>0){
						$unapprovedata.= $renderobj->showassignproject_mentor($values);
					}
				}
			}
			$content.= (empty($unapprovedata)) ? '<div class="atlmessage">Innovations Not Found</div>' : $unapprovedata;
			$content.='</div></div>';
			
			$content.='<div role="tabpanel" class="tab-pane fade" id="myoverview_archive_view" aria-expanded="false">
			<div id="archive-view" data-region="archive-view">';
				$completedata = '';
				if(count($data)>0){
				foreach($data as $key=>$values){
					if($values[0]['status']=="complete"){
						$completedata.= $renderobj->showassignproject_mentor($values);
					}
				}
			}
			$content.= (empty($completedata)) ? '<div class="atlmessage">Innovations Not Found</div>' : $completedata;			
			$content.='</div></div>
			
		</div>
	</div>';
	unset($data);
	
	$content.= html_writer::end_tag('div');
	return $content;
}

//Show All projects in assign/Innovation Page for mentor Login..
//Similar function mentorproject_api() is there.
function mentorprojects(){
	global $USER, $DB;
	$atalvariable = get_atalvariables();
	$projectcat = $atalvariable['project_categoryid'];	
	$userrole = get_atalrolenamebyid($USER->msn);
	//Show only those projects of his/her school which is assigned to him/her or created by him/her.
	$sql = "SELECT FLOOR(RAND() * 401) + 100 AS id,p.id as projectid,p.idnumber,p.fullname as project,p.summary,p.visible,u.id as userid,u.username,u.firstname,u.lastname,
	u.auth,u.picture,u.deleted,u.suspended,u.msn,p.startdate,p.enddate,p.completionnotify,ue.timestart FROM {course} p JOIN {enrol} e ON e.courseid=p.id JOIN {user_enrolments} ue ON e.id=ue.enrolid  
	JOIN {user} u ON ue.userid=u.id JOIN(SELECT c.id FROM {course} c JOIN {enrol} e ON e.courseid=c.id JOIN {user_enrolments} ue ON e.id=ue.enrolid 
	WHERE e.enrol='manual' AND ue.userid=".$USER->id.") as mycourse ON p.id=mycourse.id 
	WHERE p.category = ? AND e.enrol='manual' ORDER BY p.id desc";
	$result = $DB->get_records_sql($sql, array($projectcat));	
	$projectarray = array();
	$myprojectstatus = array();
	$mytimestart = array();
	if(count($result)>0){
		foreach($result as $key=>$values){
			$userlink = '';
			$uname = '';
			if(!empty($values->userid)){
				$userlink = getprojuserimg($values);
			}
			$dbuserrole = get_atalrolenamebyid($values->msn);
			$status = "";
			$projectdetail = "";
			if($values->userid==$USER->id){
				$myprojectstatus[$values->projectid] = frmget_projectstatus($values,$dbuserrole);
				$mytimestart[$values->projectid] = $values->timestart;
			}			
			$projectarray[$values->projectid][] = array('id'=>$values->projectid,'name'=>$values->project,'summary'=>substr($values->summary, 0, 50),
			'userid'=>$values->userid,'userlink'=>$userlink,'roleid'=>$values->msn,'role'=>get_atalrolenamebyid($values->msn),'username'=>$uname,
			'firstname'=>$values->firstname,'visible'=>$values->visible,'loginuserrole'=>$userrole,'projectdetaillink'=>$projectdetail,
			'status'=>$status,'enddate'=>$values->enddate,'completionnotify'=>$values->completionnotify,'timestart'=>$values->timestart,'mytimestart'=>0);		
		}
	}
	//Now Loop the Projectarray to update the Status for LoggedIn Mentor. project Status & Detail Link should in 0th place in ProjectArray
	if(count($projectarray)>0){
		foreach($myprojectstatus as $key=>$value){
			$projectarray[$key][0]['status'] = $value;
			$projectarray[$key][0]['mytimestart'] = $mytimestart[$key];
			if($value=="unapprove"){
				$projectdetail = new moodle_url('/search/detail.php', array('key' => encryptdecrypt_projectid($key)));
			} else{
				//show details for active or completed projects.
				$projectdetail = new moodle_url('/project/detail.php', array('id' => encryptdecrypt_projectid($key)));
			}
			$projectarray[$key][0]['projectdetaillink'] = $projectdetail;
		}
	}
	return $projectarray;
}

//To get Project Status, @params: Course Object
//Also written in /search/render.php resultprojectstatus();
function frmget_projectstatus($values, $dbuserrole=null){
	$status = "";
	//Status: active , complete, unapprove, reject
	if($values->startdate>0 && $values->visible==0){
		$status = "unapprove";
	} else if($values->startdate==0 && $values->visible==0){
		$status = "reject";
	} else if($values->enddate>0){
		$status = "complete";
	} else{
		$status = "active";
	}
	//Project w.r.t to LoggedIn Mentor User.
	if($dbuserrole=="mentor"){
		//This Project is Approved By Atal Incharge But this Mentor have not yet approve this project, so Show this
		// Project under his/her unapprove section..
		if($values->timestart==0 && $values->visible>0){
			$status = "unapprove";
		}		
		//Student creates a Project add this Mentor, Pending Approval from Both Mentor & Atal Incharge
		if($values->timestart==0 && $values->visible==0){
			$status = "donotshow";
		}
	}
	return $status;
}

//Project Accept or Reject By LoggedIn Mentor
function frmproject_acceptbymentor($projectid, $status, $msg=""){
	global $DB, $USER;
	if($status=="approve"){
		//mentor accept this project
		$sql = "SELECT ue.id FROM {course} p LEFT JOIN {enrol} e ON e.courseid=p.id LEFT JOIN {user_enrolments} ue ON ue.enrolid=e.id ";
		$sql.=" WHERE p.id = ? AND e.enrol='manual' AND ue.userid=".$USER->id;
		$data = $DB->get_record_sql($sql, array($projectid));
		$enrolid = $data->id;
		unset($data);
		$data = new stdClass();
		$data->id = $enrolid;
		$data->timestart = time();
		$data->status = 1;
		$DB->update_record('user_enrolments', $data);
		$msg = "Project was accepted by ".$USER->firstname;
		frmadd_projectcomment($projectid,$msg);
	} else{
		//mentor reject this project
		frmunenroll_userproject($USER->id,$projectid);
		$msg = "Project was rejected by ".$USER->firstname;
		frmadd_projectcomment($projectid,$msg);
	}
}

//Add project Comment
function frmadd_projectcomment($projectid,$msg){
	global $DB;
	$comment = new stdClass();
	$comment->courseid = $projectid;
	$comment->comment  = trim($msg);
	$comment->createdtime = time();
	$comment->isread = 'n';
	$DB->insert_record('course_comment', $comment);
}

//UnEnroll Users From a Project(Course).
function frmunenroll_userproject($userid,$projectid){
	global $DB;
	//Get Course enrollid
	$sql = "SELECT e.id FROM {course} p LEFT JOIN {enrol} e ON e.courseid=p.id WHERE p.id = ? AND e.enrol='manual'";
	$data = $DB->get_record_sql($sql, array($projectid));
	$enrolid = $data->id;
	$DB->delete_records("user_enrolments", array('enrolid'=>$enrolid,'userid'=>$userid));
	$coursecontext = context_course::instance($projectid);
	$user = $DB->get_record("user",array('id'=>$userid),'msn');
	$roleid = $user->msn;
	$DB->delete_records("role_assignments", array('userid'=>$userid,'contextid'=>$coursecontext->id,'roleid'=>$roleid));
}

//Mark Complete a Project by incharge.
function completeproject($projectid){
	global $DB;
	$data = new stdClass();
	$data->id = $projectid;
    $data->visible = 1;
	$data->enddate = time();
	$data->completionnotify = 1;
    $DB->update_record('course', $data);
	return true;
}

//Approve a Project by incharge.
function approveproject($projectid){
	global $DB;
	if(is_mentorapproveproject($projectid)){
		$data = new stdClass();
		$data->id = $projectid;
		$data->visible = 1;
		$DB->update_record('course', $data);
		return true;
	} else{
		return false;
	}
}

//Reject a Project by incharge.
function rejectproject($projectid,$msg=""){
	global $DB;
	$data = new stdClass();
	$data->id = $projectid;
    $data->visible = 0;
	$data->startdate = 0;
    $DB->update_record('course', $data);
	frmadd_projectcomment($projectid,$msg);
	unenrolluser_exceptcreatedby($projectid);
	return true;
}

//Save Project Tags..
function saveprojecttags($courseid , $tags){
	global $DB;
	$temp = explode("#",$tags);
	if(count($temp)>0){
		foreach($temp as $k=>$v){
			$cm = new stdClass();
			$cm->name      = $v;
			$cm->projectid = $courseid;
			if(!empty($v)){
				$DB->insert_record('tag_project', $cm);
			}
			unset($cm);
		}
	}
}

//To Update status flag to active in user enrolment for a project.
//@Params: projectid: INT, flag: String: If student update all student
//@Params: userid.. if flag is not student then update w.r.t userid
function activate_userenrolment($projectid,$flag,$userid=0){
	global $DB;
	$userroleid = atal_get_roleidbyname('student');
	$enrol = $DB->get_record('enrol', array('courseid'=>$projectid,'enrol'=>'manual'),'id');
	if($flag=="student"){
		//Get all Student users of this project and set their enrol status to 1.
		$sql = "SELECT u.id,u.msn FROM {user} u JOIN {user_enrolments} ue ON u.id=ue.userid WHERE u.msn=".$userroleid." AND ue.enrolid=".$enrol->id;
		$course = $DB->get_records_sql($sql);
		if(count($course)>0){
			foreach($course as $keys=>$values){
				$sql = "UPDATE {user_enrolments} SET status=1 WHERE userid=".$values->id." AND enrolid=".$enrol->id;
				$DB->execute($sql);
			}
		}
	} else{
		$sql = "UPDATE {user_enrolments} SET status=1 WHERE userid=".$userid." AND enrolid=".$enrol->id;
		$DB->execute($sql);
	}
}

//Remove or Un-Enroll all the users from a Project Except one who created it.
function unenrolluser_exceptcreatedby($projectid){
	global $DB;
	$sql = "SELECT e.id FROM {course} p LEFT JOIN {enrol} e ON e.courseid=p.id WHERE p.id = ? AND e.enrol='manual'";
	$data = $DB->get_record_sql($sql, array($projectid));
	$enrolid = $data->id;
	unset($data);
	$coursecontext = context_course::instance($projectid);
	$sql="SELECT u.id,u.msn,c.createdby FROM {course} c JOIN {enrol} e ON c.id=e.courseid JOIN {user_enrolments} ue ON e.id=ue.enrolid JOIN {user} u";
	$sql.=" ON ue.userid=u.id WHERE e.enrol='manual' AND c.id=".$projectid;
	$data = $DB->get_records_sql($sql);
	if(count($data)>0){
		//UnEnroll users
		foreach($data as $keys=>$values){
			$userid = $values->id;
			$roleid = $values->msn;
			if($userid!=$values->createdby){
				//Remove Other Users , Excluding the One who created the project..
				$DB->delete_records("user_enrolments", array('enrolid'=>$enrolid,'userid'=>$userid));
				$DB->delete_records("role_assignments", array('userid'=>$userid,'contextid'=>$coursecontext->id,'roleid'=>$roleid));
			}
		}
	}
}

//Show All My Projects in myInnovation Page
function myenrollprojects($condition=''){
	global $USER,$DB,$SESSION;
	$atalvariable = get_atalvariables();
	$projectcat = $atalvariable['project_categoryid'];
	if(isset($SESSION->schoolid) && $SESSION->schoolid>0){
		$schoolid = $SESSION->schoolid;
	} else{
		$schooldata = $DB->get_records('user_school', array('userid'=>$USER->id), null, 'id,schoolid');		
		if(count($schooldata)==0){
			$schoolid = 0;
		} else{
			$tmp = array();
			foreach($schooldata as $key=>$val1){
				$tmp[] = $val1->schoolid;
			}
			$schoolid = implode(",",$tmp);
		}
	}
	$userrole = get_atalrolenamebyid($USER->msn);
	if($userrole=='incharge'){
		//School InCharge;
		$sql = "SELECT FLOOR(RAND() * 401) + 100 AS id,p.id as projectid,p.idnumber,p.fullname as project,p.summary,p.visible,u.id as userid,u.firstname,u.lastname,
		u.msn,p.startdate,p.enddate,p.completionnotify,p.createdby 
		FROM {course} p LEFT JOIN {user} u ON p.createdby=u.id WHERE p.idnumber = ? AND p.category = ? AND p.startdate>0 ORDER BY p.id desc";
		$result = $DB->get_records_sql($sql, array($schoolid,$projectcat));
	}
	elseif($userrole=='mentor'){		
		//Show only those projects of his/her school which is assigned to him/her or created by him/her.
		$sql = "SELECT FLOOR(RAND() * 401) + 100 AS id,p.id as projectid,p.idnumber,p.fullname as project,p.summary,p.visible,u.id as userid,u.firstname,u.lastname,
		u.msn,p.startdate,p.enddate,p.completionnotify,p.createdby 
		FROM {course} p JOIN(SELECT c.id FROM {course} c JOIN {enrol} e ON e.courseid=c.id JOIN {user_enrolments} ue ON e.id=ue.enrolid 
		WHERE e.enrol='manual' ".$condition." AND ue.userid=".$USER->id.") as mycourse ON p.id=mycourse.id LEFT JOIN {user} u ON p.createdby=u.id 
		WHERE p.idnumber IN (".$schoolid.") AND p.category = ? AND p.startdate>0 ORDER BY p.id desc";
		$result = $DB->get_records_sql($sql, array($projectcat));
	}
	else{
		//Show only those projects of his/her school which is assigned to him/her or created by him/her.
		$sql = "SELECT FLOOR(RAND() * 401) + 100 AS id,p.id as projectid,p.idnumber,p.fullname as project,p.summary,p.visible,u.id as userid,u.firstname,u.lastname,
		u.msn,p.startdate,p.enddate,p.completionnotify,p.createdby 
		FROM {course} p JOIN(SELECT c.id FROM {course} c JOIN {enrol} e ON e.courseid=c.id JOIN {user_enrolments} ue ON e.id=ue.enrolid 
		WHERE e.enrol='manual' AND ue.userid=".$USER->id.") as mycourse ON p.id=mycourse.id LEFT JOIN {user} u ON p.createdby=u.id 
		WHERE p.idnumber = ? AND p.category = ? AND p.startdate>0 ORDER BY p.id desc";
		$result = $DB->get_records_sql($sql, array($schoolid,$projectcat));
	}
	
	$projectarray = array();
	if(count($result)>0){
		foreach($result as $key=>$values){
			$userlink = '';
			$uname = '';
			$coursemsgcount = 0; //any unread course comments;
			//if(!empty($values->userid)){
				//$userlink = getprojuserimg($values);
			//}
			if($userrole=="student" && $values->createdby==$USER->id){
				//get course comments unread msg count
				$coursemsgcount = $DB->count_records("course_comment", array('courseid'=>$values->projectid,'isread'=>'n'));
			}
			if($userrole=="incharge"){
				//get course comments unread msg count, But it will still Unread
				$coursemsgcount = $DB->count_records("course_comment", array('courseid'=>$values->projectid,'isread'=>'n'));
			}
			$status = frmget_projectstatus($values);
			$showprojectdetails = false;
			if($userrole=='student'){
				$showprojectdetails = ($status=="unapprove" || $status=="reject") ? false : true;
			}
			if($showprojectdetails){
				$projectdetail = new moodle_url('/project/detail.php', array('id' => encryptdecrypt_projectid($values->projectid)));
			} else{
				$projectdetail = new moodle_url('/search/detail.php', array('key' => encryptdecrypt_projectid($values->projectid)));
			}
			$userenrolstat = 1; //Student/Mentors user_enrolments status
			if(isset($values->timestart)){
				$userenrolstat = ($values->timestart==0 && $values->timeend==0)?0:1;
				//To check wheather mentor accept this project or not, for students its 1
			}
			$projectarray[$values->projectid][] = array('id'=>$values->projectid,'name'=>$values->project,'summary'=>substr($values->summary, 0, 50),
			'userid'=>$values->userid,'roleid'=>$values->msn,'role'=>get_atalrolenamebyid($values->msn),'username'=>$uname,
			'firstname'=>$values->firstname,'lastname'=>$values->lastname,'visible'=>$values->visible,'projectdetaillink'=>$projectdetail,
			'status'=>$status,'enddate'=>$values->enddate);
		}
	}
	return $projectarray;
}

function innovationright_block(){	
	$filters = new block_contents();
	$filters->content = getinnoblock_content('content');
	$filters->footer = '';
	$filters->title = getinnoblock_content('title');
	return $filters;
}

function getinnoblock_content($flag){
	global $USER,$DB,$SESSION;
	$content = '';
	$rolename = get_atalrolenamebyid($USER->msn);
	if($flag=='content'){
		if($rolename=='mentor'){		
			$content = showstudentmentorlist('','student');
		} elseif($rolename=='student'){		
			$content = showstudentmentorlist($SESSION->schoolid,'student');
		} else{
			$content = showstudentmentorlist($SESSION->schoolid,'mentor');
		}
	} else{
		if($rolename=='mentor'){
			$content = "Students";
		} elseif($rolename=='student'){
			$content = "Students";
		} else{
			$content = "Mentors";
		}
	}
	return $content;
}

function showstudentmentorlist($schoolid,$flag)
{
	global $DB, $USER,$OUTPUT;
	$content = "";
	$uid = $USER->id;
	$teacherroleid = atal_get_roleidbyname('mentor');
	$studentroleid = atal_get_roleidbyname('student');
	if(!empty($schoolid)){
		$sql="SELECT u.id as userid,u.username,u.firstname,u.auth,u.lastname,u.lastname,u.msn,u.city,u.picture FROM {user} u JOIN {user_school} us ON u.id=us.userid WHERE us.schoolid= ?";
		$sql.=" AND u.msn = ? ORDER BY u.id desc LIMIT 0,20";
		$mid = ($flag=='student')?$studentroleid:$teacherroleid;
		$data = $DB->get_records_sql($sql, array($schoolid,$mid));
	} else{
		$data = get_mystudentlist($USER->id);
	}
	if(count($data))
	{
		foreach($data as $key=>$values){
			$name = $values->firstname.' '.$values->lastname;
			//get User Profile pic.
			$userobject = (object) array('id'=>$values->userid,'auth'=>$values->auth,'username'=>$values->username,
			'firstname'=>$values->firstname,'lastname'=>$values->lastname,'picture'=>$values->picture);
			$userlink = userpicbyobject($userobject);
			$content = $content.'
			<div class="mentorrow clearfix">
			  <div class="left picture">'.$userlink.'
			  </div>
			  <div class="topic">'
				.$name.'
			  </div>
			</div>';
		}
	}
	return $content;
}

//To check metor approves the project
//@createdOn:20-May-2018
function is_mentorapproveproject($projectid){
	return true;
	//As on 3-June-2018,new logic change..
	//The innovation created by the student has to first go to the ATL In-charge for approval, rejection or add/remove mentor, students. 
	//Once approved by the ATL In-charge, should then go to the mentor for approval	
	//global $DB;
	//Get list of mentors assign to this project.
	//If any assigned Mentor approves it then return true else false.
	/*$result = false;
	$mentorrole = atal_get_roleidbyname('mentor');
	$sql = "SELECT u.id,ue.timestart FROM {course} p JOIN {enrol} e ON e.courseid=p.id JOIN {user_enrolments} ue ON ue.enrolid=e.id ";
	$sql.=" JOIN {user} u ON ue.userid=u.id";
	$sql.=" WHERE p.id = ? AND e.enrol='manual' AND u.msn=".$mentorrole;
	$data = $DB->get_records_sql($sql, array($projectid));
	if(count($data)>0){
		foreach($data as $key=>$values){
			if($values->timestart>0){
				$result = true;
				break;
			}
		}
	}
	return $result;*/
}

//Show All projects in assign/Innovation Page for mentor Login in WebService APP..
function mentorprojects_api($userid,$statustype){
	global $USER, $DB;
	$atalvariable = get_atalvariables();
	$projectcat = $atalvariable['project_categoryid'];
	$userrole = "mentor";
	//Show only those projects of his/her school which is assigned to him/her.
	$sql = "SELECT FLOOR(RAND() * 401) + 100 AS id,p.id as projectid,p.idnumber,p.fullname as project,p.summary,p.visible,u.id as userid,u.username,u.firstname,u.lastname,
	u.auth,u.picture,u.deleted,u.suspended,u.msn,p.startdate,p.enddate,p.completionnotify,ue.timestart FROM {course} p JOIN {enrol} e ON e.courseid=p.id JOIN {user_enrolments} ue ON e.id=ue.enrolid  
	JOIN {user} u ON ue.userid=u.id JOIN(SELECT c.id FROM {course} c JOIN {enrol} e ON e.courseid=c.id JOIN {user_enrolments} ue ON e.id=ue.enrolid 
	WHERE e.enrol='manual' AND ue.userid=".$userid.") as mycourse ON p.id=mycourse.id 
	WHERE p.category = ? AND e.enrol='manual' ORDER BY p.id desc";
	$result = $DB->get_records_sql($sql, array($projectcat));
	$projectarray = array();
	if(count($result)>0){
		foreach($result as $key=>$values){
			$status = "";
			if($values->userid==$userid){
				$status = frmget_projectstatus($values,"mentor");
				if($statustype==$status){
					$projectarray[] = array('id'=>$values->projectid,'name'=>$values->project,
					'status'=>$status,'startdate'=>$values->startdate,'enddate'=>$values->enddate);
				}
			}
		}
	}
	return $projectarray;
}