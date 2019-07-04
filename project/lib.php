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
 * @CreatedOn: 21-12-2017
 * @Description: Library functions for Project Module
*/

require_once($CFG->libdir.'/filelib.php');
require_once($CFG->dirroot.'/course/format/lib.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/forum/lib.php');
include_once(__DIR__ .'/externallib.php');
include_once(__DIR__ .'/projectlib.php');
require_once($CFG->dirroot.'/create/lib.php');
require_once('../external/commonrender.php');
function get_mentor_suggested_school()
{
	global $DB, $USER;
	//$sql = "select mu.id,mud.id as infoid,mud.userid,mud.schoolid,mu.firstname,mu.lastname,mu.email,ms.name,ms.cityid,ms.atl_id,mu.city,mu.aim FROM {user_info_data} mud join {user} mu on mu.id=mud.userid join {school} ms on ms.id=mud.schoolid where mud.data='mentor data' and mud.userid NOT IN (select userid from {user_school} where role='mentor') AND mud.schoolid IS NOT NULL AND mu.deleted=0 ";
	$sql='SELECT mmc.schoolid,mu.id,mmc.id as choiceid,mmc.userid,mmc.status,mu.firstname,mu.lastname,mu.city,mu.aim,mu.email,ms.cityid,ms.name,ms.atl_id FROM {mentor_schoice} as mmc join {user} mu on mu.id=mmc.userid join {school} ms on ms.id=mmc.schoolid where status=1 and mmc.userid not in (select userid from mdl_mentor_schoice where status=3) order by userid';
	//SELECT * FROM `mdl_mentor_schoice` WHERE userid not in (select userid from mdl_mentor_schoice where status=3)
	$result = $DB->get_records_sql($sql);
	return $result;
}
//Check for atal incharge weather this project is assign to his School..
function isenrol_toproject($id, $course){
	global $DB, $USER;
	$data = $DB->get_record('user_school', array('userid'=>$USER->id));
	$schoolid = $data->schoolid;
	if($schoolid == $course->idnumber){
		return true;
	} else{
		return false;
	}
}

function get_project($id){
	global $DB;
	$data = $DB->get_record('course', array('id'=>$id));
	return $data;
}
/**
* Adds the pretend (RHS) blocks for the project
* block_contents define in \moodle\lib\outputcomponets.php
*/
function projectside_block_mentor(project_render $renderobj){
	$filters = new block_contents();
	$filters->content = $renderobj->sidebar_mentor();
	$filters->footer = '';
	$filters->title = '';
	return $filters;
}

function projectside_block_student(project_render $renderobj){
	$filters = new block_contents();
	$filters->content = $renderobj->sidebar_student();
	$filters->footer = '';
	$filters->title = "Students";
	return $filters;
}

function projectside_block_detail(project_render $renderobj, $projectid){
	global $DB;
	//$sql="SELECT * FROM {ztmp_projectfile} WHERE projectid= ? ORDER BY type";
	//$data = $DB->get_records_sql($sql, array($projectid));
	$course = $DB->get_record('course',array('id'=>$projectid));
	$filters = new block_contents();
	$filters->content = $renderobj->sidebar_project_assets($course);
	$filters->footer = '';
	$filters->title = '';
	return $filters;
}

function projectside_block_incharge(project_render $renderobj){
	$filters = new block_contents();
	$filters->content = $renderobj->sidebar_incharge();
	$filters->footer = '';
	$filters->title = "InCharge";
	return $filters;
}

//RHS Block Ends

function addproject(add_projectform $frmobject){
	$content = html_writer::start_tag('div', array('class' => 'createproject'));
	$content.= $frmobject->render();
	$content.= html_writer::end_tag('div');
	return $content;
}

//Returns Profile image of a user;
function getprojuserimg($values){
	global $USER,$DB,$CFG,$OUTPUT;
	$uname = $values->firstname.' '.$values->lastname;
	$userobject = (object) array('id'=>$values->userid,'auth'=>$values->auth,'username'=>$values->username,
	'firstname'=>$values->firstname,'lastname'=>$values->lastname,'picture'=>$values->picture);	
	$userlink = userpicbyobject($userobject);
	return $userlink;
}

//Create Innovation By Students
function create_newproject($data){
	global $DB, $CFG, $USER;
	$atalvariables = get_atalvariables();
	$userrole = get_atalrolenamebyid($USER->msn);
	$projectcategory = (in_array("project_categoryid", $atalvariables)===true)?$atalvariables['project_categoryid']:2;
	$forum_module = $atalvariables['forum_module'];
	$maxbytes = 512000;//($CFG->maxbytes==0)512000?:$CFG->maxbytes;
	//$cm->module = $DB->get_field('modules', 'id', array('name'=>$modulename));
	//get schoolid
    $schooldata = $DB->get_record('user_school', array('userid'=>$USER->id), '*', MUST_EXIST);
	$schoolid = (isset($schooldata->schoolid))?$schooldata->schoolid:0;
	$visible = ($userrole=='student')?0:1;
	$datarc = $DB->get_record_sql('select max(sortorder) as sortorder FROM {course}');
	$sortorder = $datarc->sortorder + 1;
	unset($datarc);
	
	//Course Info.
	$course = new stdClass;
	$course->category = $projectcategory;
	$course->sortorder = $sortorder;
	$course->fullname = $data->fullname;
	$course->shortname = $data->shortname;
	$course->idnumber = $schoolid;
	$course->summary  = $data->summary_editor;
	$course->summaryformat = 1;
	$course->format = 'topics';
	$course->newsitems = 5;
	$course->startdate = time();
	$course->enddate = 0;
	$course->visible = $visible;
	$course->timecreated = time();
	$course->timemodified = time();
	$course->enablecompletion = 1;
	$course->cacherev = time();
	$course->createdby = $USER->id;
	//Course Module
	$moduledata = new stdClass;
	$moduledata->type  = 'blog';
	$moduledata->name = 'Forum on '.$data->shortname;
	$moduledata->intro = $data->shortname;
	$moduledata->introformat = 1;
	$moduledata->scale = 100;
	$moduledata->maxbytes = $maxbytes;
	$moduledata->maxattachments = 9;
	$moduledata->timemodified = time();
	//Course Enrol
	$encm = new stdClass();
	$encm->enrol     = 'manual';
	$encm->status    = 1;
	$encm->sortorder = 1;
	$encm->expirythreshold = '86400';
	$encm->roleid = 5;
	$encm->timecreated  = time();
	$encm->timemodified = time();
		
	try {
		// From this point we make database changes, so start transaction.
		$transaction = $DB->start_delegated_transaction();
		// Insert a record
		$newcourseid = $DB->insert_record('course', $course);
		$context = context_course::instance($newcourseid, MUST_EXIST);
		//course module - Forum
		$moduledata->course = $newcourseid;
		$forumid = $DB->insert_record('forum', $moduledata);
		//Course Module
		$cm = new stdClass();
		$cm->course     = $newcourseid;
		$cm->module     = $forum_module;
		$cm->instance   = $forumid;
		$cm->section    = 0;
		$cm->added      = time();
		$cm->id = $DB->insert_record('course_modules', $cm);
		unset($cm);
		//Course Enrol
		$encm->courseid  = $newcourseid;
		$enrolid = $DB->insert_record('enrol', $encm);
		
		if ($overviewfilesoptions = course_overviewfiles_options($newcourseid)) {
			// Save the course overviewfiles
			file_save_draft_area_files($data->overviewfiles_filemanager,$context->id, 'course', 'overviewfiles', 0, $overviewfilesoptions);
		}
		if(!empty($data->tags)){
			saveprojecttags($newcourseid,$data->tags);
		}
		if($userrole=='student'){
			//Project created by Student, So enroll him/her by default.
			$cm = new stdClass();
			$cm->status = 0;
			$cm->enrolid = $enrolid;
			$cm->userid = $USER->id;
			$cm->timestart = time();
			$cm->timeend = 0;
			$cm->modifierid = $USER->id;
			$cm->timecreated  = time();
			$cm->timemodified  = time();
			$DB->insert_record('user_enrolments', $cm);
			projectroleassignment($newcourseid,$USER->id,'student');
		}
		$transaction->allow_commit(); //close transaction

		if(!empty($enrolid)){
			//Assign multiple selected mentors/student into course
			assignusers_incourse($data,$newcourseid,$enrolid);
		}
	} catch(Exception $e) {
		$transaction->rollback($e);
		unset($course);
		unset($moduledata);
		return false;
	}
	unset($course);
	unset($moduledata);
	
	return true;
}

//Update course Image. (Testing Only)
//@Params: data: courseData, contextid: NewCourse_contextid
function updatecourseimgfile($data,$newcourseid,$contextid){
	global $DB;
	if(isset($data->overviewfiles_filemanager) && !empty($data->overviewfiles_filemanager)){
		$result = $DB->get_records('files', array('component'=>'user','itemid'=>$data->overviewfiles_filemanager));	
		if(count($result)>0){
			foreach($result as $key=>$values){
				$file = new stdClass();
				$source = @unserialize($values->source);
				$file->contenthash = $values->contenthash;
				$file->pathnamehash = $values->pathnamehash;
				$file->contextid = $contextid;
				$file->component = "course";
				$file->filearea = "overviewfiles";
				$file->itemid = 0;
				$file->filepath = $values->filepath;
				$file->filename = $values->filename;
				$file->userid =$values->userid;
				$file->filesize = $values->filesize;
				$file->mimetype = $values->mimetype;
				$file->status = $values->status;
				$file->source = $source->source;
				$file->author = $values->author;
				$file->license = $values->license;
				$file->timecreated = $values->timecreated;
				$file->timemodified = time();
				$file->sortorder = $values->sortorder;
				$file->referencefileid = NULL;
				$DB->insert_record('files', $file);
				unset($file);
			}
		}
	}
}

//Show Assign Projects in Innovation Page for InCharge & Students.
function showassignproject(innovation_render $renderobj, add_projectform $frmprojectobj){
	global $CFG,$USER;
	$userrole = get_atalrolenamebyid($USER->msn);
	$content = html_writer::start_tag('div', array('class' => 'assignmentor'));
	$content.= "<div class='heading'><h3>Innovations</h3></div><br></br>";	
	//$content.= "<div class='abar'></div>";
	$approvetag = ($userrole=='incharge')? "Approve": "Pending Approval";
	$assigntag = ($userrole=='incharge')? "Assign": "Ongoing";
	$archivetag = ($userrole=='incharge')? "Archive": "Completed";
	$data = projectuser();
	
	$content.= '
	<div id="atal-innovation" data-region="atldashboard">
		<ul id="block-atldashboard-view-choices" class="nav nav-tabs" role="tablist">';
		$activeTab = "active";
		if($userrole=='student'){
			//Show Create Tab;
			$content.= '<li class="nav-item">
			<a id="v1" class="nav-link '.$activeTab.'" href="#myoverview_create_view" role="tab" data-toggle="tab" data-tabname="courses"  aria-expanded="true">
			Create
			</a>
			</li>';
			$activeTab = "";
		}
		$content.= '<li class="nav-item">
		<a id="v1" class="nav-link '.$activeTab.'" href="#myoverview_approve_view" role="tab" data-toggle="tab" data-tabname="courses"  aria-expanded="true">
		'.$approvetag.'
		</a>
		</li>';	
		$content.= '<li class="nav-item">
		<a id="v2" class="nav-link" href="#myoverview_assign_view" role="tab" data-toggle="tab" data-tabname="timeline" aria-expanded="false">
		'.$assigntag.'
		</a>
		</li>';	
		$content.= '<li class="nav-item">
		<a id="v3" class="nav-link" href="#myoverview_archive_view" role="tab" data-toggle="tab" data-tabname="projects" aria-expanded="false">
		'.$archivetag.'
		</a>
		</li></ul>';
		
		$content.= '		
		<div class="tab-content content-centred tabcontentatal">';
			$tmpin = "active in"; //selected active Tab pane class
			if($userrole=='student'){
				//Show Create Project TAB for Student..
				$projectForm = html_writer::start_tag('div', array('class' => 'createproject'));
				$projectForm.= $frmprojectobj->render();
				$projectForm.= html_writer::end_tag('div');
				$content.= '<div role="tabpanel" class="tab-pane fade active in" id="myoverview_create_view" aria-expanded="true">
				<div id="approve-view" data-region="approve-view">'.$projectForm.'</div></div>';
				$tmpin = "";
			}
			
			//UnApproved projects for Atal Incharge & Pending Approval for Students.
			$content.= '<div role="tabpanel" class="tab-pane fade '.$tmpin.'" id="myoverview_approve_view" aria-expanded="true">
			<div id="approve-view" data-region="approve-view">';
			$approvedata = '';
			if(count($data)>0){
				foreach($data as $key=>$values){
					if($values[0]['status']=="unapprove" || ($userrole=='student' && $values[0]['status']=="reject")){
						$approvedata.= $renderobj->showassignproject($values,$userrole);
					}
				}
			}
			$content.= (empty($approvedata)) ? '<div class="atlmessage">Innovations Not Found</div>' : $approvedata;			
			$content.='</div></div>';
			
			//Approved projects
			$content.='<div role="tabpanel" class="tab-pane fade" id="myoverview_assign_view" aria-expanded="false">
			<div id="assign-view" data-region="assign-view">';
			$unapprovedata = '';
			if(count($data)>0){
				foreach($data as $key=>$values){
					if($values[0]['status']=="active"){
						$unapprovedata.= $renderobj->showassignproject($values,$userrole);
					}
				}
			}
			$content.= (empty($unapprovedata)) ? '<div class="atlmessage">Innovations Not Found</div>' : $unapprovedata;
			$content.='</div></div>';
			
			//Completed projects
			$content.='<div role="tabpanel" class="tab-pane fade" id="myoverview_archive_view" aria-expanded="false">
			<div id="archive-view" data-region="archive-view">';
				$completedata = '';
				if(count($data)>0){
				foreach($data as $key=>$values){
					if($values[0]['status']=="complete"){
						$completedata.= $renderobj->showassignproject($values,$userrole);
					}
				}
			}
			$content.= (empty($completedata)) ? '<div class="atlmessage">Innovations Not Found</div>' : $completedata;			
			$content.='</div></div>
			
		</div>
	</div>';
	unset($data);
	//get users assign to this school.
	$schoolid = getmyschoolid();
	$mentor = getusersofschool('mentor',$schoolid);
	$student = getusersofschool('student',$schoolid);
	$content.=$renderobj->popupbox($mentor,$student);
	$content.=$renderobj->popupbox_comments();
	$content.= html_writer::end_tag('div');
	return $content;
}

//Show All projects in assign Page.
function projectuser(){
	global $USER,$DB,$SESSION;
	$atalvariable = get_atalvariables();
	$projectcat = $atalvariable['project_categoryid'];
	if(isset($SESSION->schoolid) && $SESSION->schoolid>0){
		$schoolid = $SESSION->schoolid;
	} else{
		$schooldata = $DB->get_record('user_school', array('userid'=>$USER->id), '*', MUST_EXIST);
		$schoolid = (isset($schooldata->schoolid))?$schooldata->schoolid:0;
	}
	$userrole = get_atalrolenamebyid($USER->msn);
	if($userrole=='incharge'){
		//School InCharge;
		$sql = "SELECT FLOOR(RAND() * 401) + 100 AS id,p.id as projectid,p.idnumber,p.fullname as project,p.summary,p.visible,u.id as userid,u.username,u.firstname,u.lastname,
		u.auth,u.picture,u.deleted,u.suspended,u.msn,p.startdate,p.enddate,p.completionnotify,p.createdby,ue.timestart,ue.timeend 
		FROM {course} p LEFT JOIN {enrol} e ON e.courseid=p.id LEFT JOIN {user_enrolments} ue ON e.id=ue.enrolid  
		LEFT JOIN {user} u ON ue.userid=u.id WHERE p.idnumber = ? AND p.category = ? AND e.enrol='manual' ORDER BY p.id desc";
	}
	else{
		//Show only those projects of his/her school which is assigned to him/her or created by him/her.
		$sql = "SELECT FLOOR(RAND() * 401) + 100 AS id,p.id as projectid,p.idnumber,p.fullname as project,p.summary,p.visible,u.id as userid,u.username,u.firstname,u.lastname,
		u.auth,u.picture,u.deleted,u.suspended,u.msn,p.startdate,p.enddate,p.completionnotify,p.createdby,ue.timestart,ue.timeend  
		FROM {course} p JOIN {enrol} e ON e.courseid=p.id JOIN {user_enrolments} ue ON e.id=ue.enrolid  
		JOIN {user} u ON ue.userid=u.id JOIN(SELECT c.id FROM {course} c JOIN {enrol} e ON e.courseid=c.id JOIN {user_enrolments} ue ON e.id=ue.enrolid 
		WHERE e.enrol='manual' AND ue.userid=".$USER->id.") as mycourse ON p.id=mycourse.id 
		WHERE p.idnumber = ? AND p.category = ? AND e.enrol='manual' ORDER BY p.id desc";	
	}	
	$result = $DB->get_records_sql($sql, array($schoolid,$projectcat));
	$projectarray = array();
	if(count($result)>0){
		foreach($result as $key=>$values){
			$userlink = '';
			$uname = '';
			$coursemsgcount = 0; //any unread course comments;
			if(!empty($values->userid)){
				$userlink = getprojuserimg($values);
			}
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
			'userid'=>$values->userid,'userlink'=>$userlink,'roleid'=>$values->msn,'role'=>get_atalrolenamebyid($values->msn),'username'=>$uname,
			'firstname'=>$values->firstname,'visible'=>$values->visible,'loginuserrole'=>$userrole,'projectdetaillink'=>$projectdetail,
			'status'=>$status,'enddate'=>$values->enddate,'completionnotify'=>$values->completionnotify,'coursecommentcnt'=>$coursemsgcount,'createdby'=>$values->createdby,'uenrolstat'=>$userenrolstat);
		}
	}
	return $projectarray;
}


function getusersofschool($type,$schoolid){
	global $DB, $USER;
	$sql = "SELECT us.id,u.id as userid,u.firstname,u.lastname FROM {user_school} us JOIN {user} u  ON us.userid=u.id";
	$sql = $sql." WHERE us.schoolid=".$schoolid." AND us.role='".$type."' AND u.deleted=0 ORDER By u.firstname";
	$data = $DB->get_records_sql($sql);
	return $data;
}

function getmyschoolid(){
	global $DB, $USER;
	$data = $DB->get_record('user_school', array('userid'=>$USER->id));
	$schoolid = $data->schoolid;
	return $schoolid;
}

function frmuserimghtml($userid){
	global $DB, $USER, $CFG, $OUTPUT;
	$content = '';
	$atalarray = get_atalvariables();
	$incrementby = (int) $atalarray['search_idincrementby'];
	$values = $DB->get_record('user', array('id'=>$userid),'id,auth,confirmed,username,firstname,lastname,msn,picture');
	if(!empty($values->id)){
		$uname = $values->firstname.' '.$values->lastname;
		$key = $userid + $incrementby;
		$userurl = getuser_profilelink($values->id);
		$usrimg = get_userprofilepic($values);
		$content ='<p><a href="'.$userurl.'" target="_blank">'.$usrimg.'</a></p>'.$values->firstname.' '.$values->lastname;
		
		/* **OLD Code ..
		$userobject = (object) array('id'=>$values->id,'auth'=>$values->auth,'username'=>$values->username,
		'firstname'=>$values->firstname,'lastname'=>$values->lastname,'picture'=>$values->picture);
		$profileurl = $CFG->wwwroot.'/user/view.php?id='.$values->id;		
		$picture = $OUTPUT->user_picture($userobject);
		$userurl = '';//new moodle_url('/user/view.php', array('id' => $values->userid));
		$userlink = html_writer::link($userurl, $picture .' '. '');		
		$content.='<p>'.$userlink.'</p>'.$values->firstname;
		*/
	}
	return $content;
}

function adduser(user_create_form $frmobject){	
	$content = html_writer::start_tag('div', array('class' => 'createuser'));
	$content.= $frmobject->render();
	$content.= html_writer::end_tag('div');
	return $content;
}

function projectroleassignment($courseid,$userid,$usertype){
	global $DB, $CFG, $USER;
	$coursecontext = context_course::instance($courseid);
	$roleid = atal_get_roleidbyname($usertype);
	$cx = new stdClass();
	$cx->roleid = $roleid;
	$cx->contextid = $coursecontext->id;
	$cx->userid = $userid;
	$cx->timemodified = time();
	$cx->modifierid = $USER->id;
	$DB->insert_record('role_assignments', $cx);
}


//Show List of school with assign Mentors (assign-mentor-to-school-page)
function showassign_mentorschool(project_render $renderobj){
	global $CFG,$USER;
	$userrole = get_atalrolenamebyid($USER->msn);
	$content = html_writer::start_tag('div', array('class' => 'assignmentor'));
	$content.= "<div class='heading'><h3>Assign</h3></div>";
	$content.= "<div class='abar'></div>";
	$content.=$renderobj->filterform('school');
	$content.=showassign_mentorschooldata(1,$renderobj);
	//get users assign to this school.
	$mentor = getusers_byrole('mentor');
	$content.=$renderobj->mentorpopupbox($mentor);
	$content.= html_writer::end_tag('div');
	return $content;
}
function showassign_mentorschooldata($page,project_render $renderobj,$state=0,$city=0,$name='')
{	
	$limit=$renderobj->recordsperpage;
	$start_from = ($page-1) * $limit;  
	$data = getschoolmentor($state,$city,$name,$start_from,$limit);
	$name = trim($name);
	if($name!='')
		$condition.=" AND name LIKE '%".$name."%'";
	if($city==0 && $state!='')
		$condition.=' AND cityid IN (select id from {city} where stateid='.$state.')';
	if($city!=0)
		$condition.=' AND cityid='.$city;
	$renderobj->total_schools = getTotalSchoolCount($condition);
	$content='';
	if(count($data)>0){
		$content=CommonRender::renderLoaderContent();
		$content.='<div id="table-content-wrapper"><div class="card-block">';
		foreach($data as $key=>$values){
			$content.= $renderobj->showassignmentors($values);
		}
		$total_pages = ($renderobj->total_schools>1)?ceil(($renderobj->total_schools)/$renderobj->recordsperpage):1;	
		$content.='</div><div class="pagination-wrapper" align="center">';
		$content.= CommonRender::paginate_function($renderobj->recordsperpage,$page,$renderobj->total_schools,$total_pages,'school');
		$content.='</div></div>';
	} else{
		$content.="<div>Schools Not Found</div>";
	}
	return $content;
}
//Show List of school with assign Mentors,Students,Incharge (assign-mentor-to-school-page)
function getschoolmentor($state=0,$city=0,$name='',$start=0,$limit=0){
	global $USER,$DB,$CFG,$OUTPUT;
	$condition=" WHERE s.activestatus=1";
	$name = trim($name);
	if($name!='')
		$condition.=" AND (s.name LIKE '%".$name."%' OR s.atl_id LIKE '%".$name."%')";
		//$condition.=" AND s.name LIKE '%".$name."%'";
	if($city==0 && $state!='')
		$condition.=' AND stateid='.$state;
	if($city!=0)
		$condition.=' AND cityid='.$city;
	$schoolarray = array();
	$condition.=' ORDER BY s.name';
	$userquery = "LEFT JOIN (SELECT us.id,us.schoolid as schoolid,u.id as userid,u.firstname,u.lastname,u.auth,u.username,u.picture ";
	$userquery.="FROM {user_school} us JOIN {user} u ON us.userid=u.id WHERE us.role='mentor') as u ";
	$inchargequery = "LEFT JOIN (SELECT us.id,us.schoolid as schoolid,u.id as iuserid,u.firstname as ifirstname,u.lastname as ilastname,u.auth as iauth,
	u.username as iusername,u.picture as ipicture  FROM {user_school} us JOIN {user} u ON us.userid=u.id WHERE us.role='incharge') as inc ";
	$sql = "SELECT FLOOR(RAND() * 401) + 100 AS id,s.id as schoolid,s.name,c.name as city,c.id as cityid,c.stateid as stateid,u.userid,u.firstname,u.lastname,u.auth,u.username,u.picture,";
	$sql.="inc.iuserid,inc.ifirstname,inc.ilastname,inc.iauth,inc.iusername,inc.ipicture,s.atl_id";
	$sql.=" FROM {school} s LEFT JOIN {city} c ON s.cityid=c.id $userquery on s.id=u.schoolid $inchargequery on s.id=inc.schoolid $condition limit $start,$limit";
	//get ongoingproject in 
	$result = $DB->get_records('course', array('enddate'=>0), 'idnumber', 'id,fullname,idnumber');
	$projectschoollist = array();
	if(count($result)>0){
		$projectidnumcount = array();
		foreach($result as $key=>$values){
			$projectidnumcount[$values->idnumber] = isset($projectidnumcount[$values->idnumber])?$projectidnumcount[$values->idnumber] + 1:1;
			if($projectidnumcount[$values->idnumber]<3){
				$projectschoollist[$values->idnumber][] = $values->fullname;
			}
			//Add only 3 projects with each school listing
		}
	}
	unset($result);
	//School Listings..
	$result = $DB->get_records_sql($sql);	
	if(count($result)>0){
		foreach($result as $key=>$values){
			$userlink = '';
			$schoolarray[$values->schoolid] = array('id'=>$values->schoolid,'name'=>$values->name,'city'=>$values->city);
			if(!empty($values->userid)){
				$schoolid = $values->schoolid;
				$data = $DB->get_records_sql("SELECT mus.id,mu.id as userid,mu.firstname FROM {user_school} mus join {user} mu on mu.id=mus.userid WHERE schoolid=$schoolid and role='mentor'");
				if(count($data)>0){
					foreach($data as $ukey=>$uval)
					{
						$userlink = getprojuserimg($uval);
						$schoolarray[$values->schoolid]['mentor'][]=array('userid'=>$uval->userid,'firstname'=>$uval->firstname,'userlink'=>$userlink);
					}
				}
			} else{
				$schoolarray[$values->schoolid]['mentor'] = array();
			}
			if(!empty($values->iuserid)){
				$cn = new stdClass();
				$cn->userid = $values->iuserid;
				$cn->username = $values->iusername;
				$cn->firstname = $values->ifirstname;
				$cn->lastname = $values->ilastname;
				$cn->auth = $values->iauth;
				$cn->picture = $values->ipicture;
				$userlink = getprojuserimg($cn);
				$schoolarray[$values->schoolid]['incharge'][]=array('id'=>$values->iuserid,'firstname'=>$values->ifirstname,'userlink'=>$userlink);
			} else{
				$schoolarray[$values->schoolid]['incharge'] = array();
			}
		}
	}
	
	return $schoolarray;
}

//Fetch All Active Users according to Role in Platform
function getusers_byrole($rolename){
	global $DB;
	$roleid = atal_get_roleidbyname($rolename);
	$sql = "SELECT u.id,u.firstname,u.lastname,u.city FROM {user} u  WHERE u.deleted=0 and u.msn='".$roleid."' Order By firstname";
	$data = $DB->get_records_sql($sql);
	return $data;
}

//Display List of Mentors in LHS
function suggestedmentors(){
	global $DB, $USER,$OUTPUT, $CFG ;
	$mid = atal_get_roleidbyname('mentor');
	$content = '';
	$atalarray = get_atalvariables();
	$incrementby = (int) $atalarray['search_idincrementby'];
	$userrole = get_atalrolenamebyid($USER->msn);
	if($userrole=='incharge' || $userrole=='student'){
		//If this user is incharge, show only those Mentors in Assign page whose are assign to that school.
		$data = $DB->get_record('user_school', array('userid' => $USER->id),'schoolid');
		$schoolid = $data->schoolid;
		unset($data);
		$sql="SELECT u.id,u.username,u.firstname,u.lastname,u.auth,u.city,u.department,u.picture FROM {user} u";
		$sql.=" JOIN {user_school} us ON u.id=us.userid WHERE u.msn= ? AND u.deleted=0 AND us.schoolid=? ORDER By u.id DESC LIMIT 0,20";
		$data = $DB->get_records_sql($sql, array($mid,$schoolid));
	} else{
		$data = $DB->get_records('user', array('msn' => $mid,'deleted'=>0),'id desc','*',0,20);
	}
	if(count($data)) {
		foreach($data as $key=>$values){
			$name = $values->firstname.' '.$values->lastname;
			//get User Profile pic.			
			$userurl = getuser_profilelink($values->id);
			$usrimg = get_userprofilepic($values);
			$userpic ='<a href="'.$userurl.'">'.$usrimg.'</a>';
			$content = $content.'
			<div class="mentorrow clearfix">
				<div class="left picture">'.$userpic.'
				</div>
				<div class="topic">'
				  .$name.' , '.$values->city.'<br>'.$values->department.'
				</div>
			</div>';
	    }
    }
	return $content;
}

//Display List of Students of school in LHS (mentors/incharge/student)
function studentlist(){
	global $DB, $USER,$OUTPUT, $CFG ;
	$rolename = get_atalrolenamebyid($USER->msn);
	if($rolename=='incharge'){
		//Show students of his school;
		$data = $DB->get_record('user_school', array('userid'=>$USER->id));
		$schoolid = $data->schoolid;
	}
	$mid = atal_get_roleidbyname('student');
	$content = '';
	$sql="SELECT u.id,u.username,u.firstname,u.auth,u.lastname,u.lastname,u.msn,u.city FROM {user} u JOIN {user_school} us ON u.id=us.userid WHERE us.schoolid= ?";
	$sql.=" AND u.msn = ? ORDER BY u.id desc LIMIT 0,10";
	$data = $DB->get_records_sql($sql, array($schoolid,$mid));
	if(count($data)) {
		foreach($data as $key=>$values){
			$name = $values->firstname.' '.$values->lastname;
			//get User Profile pic.
			$profileurl = $CFG->wwwroot.'/user/view.php?id='.$values->id;
			$picture = $OUTPUT->user_picture($values);
			$userurl = new moodle_url('/user/view.php', array('id' => $values->id));
			//html_writer::link($userurl, $picture .' '. $name); ..will display name at right-side of pic
			$userlink = html_writer::link($userurl, $picture .' '. '');
			$content = $content.'
			<div class="mentorrow clearfix">
				<div class="left picture">'.$userlink.'
				</div>
				<div class="topic">'
				  .$name.' , '.$values->city.'<br>
				</div>
			</div>';
	  }
   }
	return $content;
}

//Display List of all InCharges in RHS (mentors/incharge/student) ,RHS Blocks.
function lhs_inchargelist(){
	global $DB, $USER,$OUTPUT, $CFG ;
	$mid = atal_get_roleidbyname('incharge');
	$content = '';
	$sql="SELECT u.id,u.username,u.firstname,u.auth,u.lastname,u.lastname,u.msn,u.city FROM {user} u JOIN {user_school} us ON u.id=us.userid WHERE ";
	$sql.=" u.msn = ? ORDER BY u.id desc LIMIT 0,10";
	$data = $DB->get_records_sql($sql, array($mid));
	if(count($data)) {
		foreach($data as $key=>$values){
			$name = $values->firstname.' '.$values->lastname;
			//get User Profile pic.
			$profileurl = $CFG->wwwroot.'/user/view.php?id='.$values->id;
			$picture = $OUTPUT->user_picture($values);
			$userurl = new moodle_url('/user/view.php', array('id' => $values->id));
			//html_writer::link($userurl, $picture .' '. $name); ..will display name at right-side of pic
			$userlink = html_writer::link($userurl, $picture .' '. '');
			$content = $content.'
			<div class="mentorrow clearfix">
				<div class="left picture">'.$userlink.'
				</div>
				<div class="topic">'
				  .$name.' , '.$values->city.'<br>
				</div>
			</div>';
	    }
    }
	return $content;
}

//Project collaboration/Detail page..
function showprojectdetail($renderobj, $projectid,$frmobject){
	global $DB, $USER, $CFG, $OUTPUT;

	$projectdata = $DB->get_record('course', array('id'=>$projectid), '*', MUST_EXIST);	
	$projectimg = frmgetcourse_summary_imagepath($projectid);
	$data = $DB->get_record('school',array('id'=>$projectdata->idnumber),'cityid,name');
	$citydata = $DB->get_record('city',array('id'=>$data->cityid),'name');
	$atalarray = get_atalvariables();
	$catgeoryid = $atalarray['ongoingproject_postcatgeoryid'];
	$content = html_writer::start_tag('div', array('class' => 'projectdetail'));
	//get project forum feed;
	$forum = $DB->get_record('forum',array('course'=>$projectdata->id),'id',MUST_EXIST);
	$forumid = $forum->id;
	$discount = $DB->count_records('forum_discussions', array('course'=>$projectid));
	
	$query="SELECT u.id,u.firstname,u.lastname,u.email,u.msn FROM mdl_user u JOIN mdl_user_enrolments ue ON u.id=ue.userid JOIN mdl_enrol e ON ue.enrolid=e.id ";
	$query.=" JOIN mdl_course c ON e.courseid=c.id WHERE e.enrol='manual' AND c.id=".$projectid." ORDER BY u.msn";
	$enrolusers = $DB->get_records_sql($query);
	//List of enrolled users
	if(count($enrolusers)>0){
		$userdata = '<span class="smallheading">Enroled Users: </span> ';		
		foreach($enrolusers as $key=>$values){
			$detailpagelink = $CFG->wwwroot.'/search/profile.php?key='.encryptdecrypt_userid( $values->id,"en");
			$name = $values->firstname.' '.$values->lastname;
			$name ='<a href="'.$detailpagelink.'">'.$name.'</a>';
			$userrole = get_atalrolenamebyid($values->msn);
			$userdata.="<span>$name ($userrole) </span>";
		}		
	}
	$status = frmget_projectstatus($projectdata);

	if(isset($projectdata->id)){
		$createduserdata = $DB->get_record('user', array('id'=>$projectdata->createdby), 'firstname,lastname,id');	
		if($createduserdata)
		{
			$detailpagelink = $CFG->wwwroot.'/search/profile.php?key='.encryptdecrypt_userid( $createduserdata->id,"en");
			$createdby = $createduserdata->firstname.' '.$createduserdata->lastname;
			$createdby ='<a href="'.$detailpagelink.'">'.$createdby.'</a>';
		}
		$content.='<div class="projimage"><img  width="200px" height="200px"  src="'.$projectimg.'"></div>';
		$content.='<div class="projdetail">
		<ul>
		<li><h4><span class="plink">'.$projectdata->fullname.'</span></h4></li>
		<li><span class="smallheading">StartDate:</span>&nbsp;&nbsp;'.date('d M Y', $projectdata->startdate).'</li>
		<li><span class="smallheading">CreatedBy:</span>&nbsp;&nbsp;'.$createdby.'</li>
		<li><span class="smallheading">Status:</span>&nbsp;&nbsp;'.ucwords($status).'</li>
		<li><span class="smallheading">School:</span>&nbsp;&nbsp; '.$data->name.' , '.$citydata->name.'</li>
		<li><span class="smallheading">Details:</span>&nbsp;&nbsp; '.$projectdata->summary.'</li>
		<li>'.$userdata.'</li>
		</ul>
		</div>';	
		
		$addassetlink = '<div id="addasset" style="width:100%;clear:both;" align="right"><a href="javascript:void(0);">Add Assets</a></div>';
		$content.= ($status=="active") ? $addassetlink : "";
		
		$content.= '<div class="projdisscusion" role="region" aria-label="forum-collaboration" id="colforum" style="width:100%;">';
		if($discount==0 && $status=="active"){
			//discussionCount is zero, there is no discussions happens, so start it;
			$content.='<div class="replyarea">
				<form name="chat" method="POST" action="'.$CFG->wwwroot.'/project/detail.php?id='.encryptdecrypt_projectid($projectid).'">
				<textarea name="message" class="myreplybox" rows="1" placeholder="Write your content" maxlength="400"></textarea>
				<br>
				<input class="btn atalbtn" name="submitbutton" id="id_submitbutton" value="Submit" type="submit">
				<input type="hidden" name="detailflag" value="y">
				<input type="hidden" name="sid" value="'.$projectdata->id.'">
				<input type="hidden" name="forumid" value="'.$forumid.'">
				<input type="hidden" name="name" value="'.ucwords($projectdata->shortname).'">
				<input type="hidden" name="frmcategory" value="'.$catgeoryid.'">
				<input type="hidden" name="approved" value="n">
				</form>
				</div>
			</div>';
		}else{
			//Show  project collaboration (Chat Messages)
			$content.= projectforumdata($renderobj, $forumid);
		}
		$content.= '<div>';
		//Add Assets to a project (popup-window);
		$content.= $renderobj->assetpopupbox($projectid,$frmobject);
		$content.= $renderobj->add_jsscript();
	} else{
		$content.= '<div>No Project Found </div>';
	}	
	$content.= html_writer::end_tag('div');
	
	$content.='<div style="clear:both;"></div>';
	return $content;
}

//Add first Post to Project Forum..
function savefirstprojectpost($postcontent,$projectid){
	if(!empty($projectid)){
		$data = (object) $postcontent;
		frmadd_newdiscussion($data,$projectid);	
	}
	return true;
}

function projectforumsql($condition)
{
	$sql = "SELECT p.id,p.discussion, p.userid,p.parent as postparent,p.created,DATE_FORMAT( FROM_UNIXTIME(p.created),'%D %b %Y') as createddate,p.subject,";
	$sql = $sql."p.message, p.attachment,u.firstname,u.lastname,u.auth,u.username,u.picture,d.name as discussionname, ";
	$sql = $sql." d.course, d.forum FROM {forum_posts} p ";
	$sql = $sql."LEFT JOIN {user} u ON p.userid = u.id JOIN {forum_discussions} d ON p.discussion=d.id  ".$condition." ORDER BY p.id";
	return $sql;
}

//get project Forum data..
function projectforumdata($renderobj,$forumid)
{
	global $DB,$USER;
	$content = '';
	$post_array = array();
	$condition = ' WHERE d.forum='.$forumid;
	$sql = projectforumsql($condition);
	$record = $DB->get_records_sql($sql);
	if(count($record)>0)
	{
		foreach($record as $key=>$values){
			$courseid = $values->course;
			$forumid = $values->forum;
			$post_array[$values->discussion][] = (object) frmgenratedata($values,$values->course,$values->forum);		
		}
	}
	if(count($post_array)>0){
		$tmp_discussionid = 0;
		$hasparent = false;
		foreach($post_array as $key=>$values)
		{
			$replydata = '';
			foreach($values as $k1=>$val){					
				if($val->type=='parent'){
					$renderobj->values = $val->values;
					$renderobj->userlink = $val->userlink;
					$renderobj->profileurl = $val->profileurl;
					$renderobj->post_image = $val->post_image;
					$renderobj->uname = $val->uname;
				} else{
					$renderobj->replyvalues = $val->values;
					$renderobj->replyuserlink = $val->userlink;
					$renderobj->replyprofileurl = $val->profileurl;
					$renderobj->replypost_image = $val->post_image;
					$renderobj->replyuname = $val->uname;
					$replydata = $replydata.$renderobj->render_forumreply();
				}
			}
			$renderobj->replydata = $replydata;
			$content = $content.$renderobj->project_collaboration();
		}
	}	
	return $content;
}

//This will create the HTML of Reply Post;
function getprojectreplyhtml($postid)
{
	global $DB,$USER;
	include_once(__DIR__ .'/render.php');
	$renderobj = new project_render($USER->id, $USER->msn);
	$content = '';
	$replydata = '';
	$post_array = array();
	$condition = ' WHERE p.id='.$postid;
	$sql = projectforumsql($condition);	
	$record = $DB->get_records_sql($sql);
	if(count($record)>0)
	{
		foreach($record as $key=>$values)
		{
			$courseid = $values->course;
			$forumid = $values->forum;
			$post_array[$values->discussion][] = (object) frmgenratedata($values,$values->course,$values->forum);			
		}
	}	
	if(count($post_array)>0){
		$tmp_discussionid = 0;		
		foreach($post_array as $key=>$values)
		{			
			foreach($values as $k1=>$val){				
				$renderobj->replyvalues = $val->values;
				$renderobj->replyuserlink = $val->userlink;
				$renderobj->replyprofileurl = $val->profileurl;
				$renderobj->replypost_image = $val->post_image;
				$renderobj->replyuname = $val->uname;
				$replydata = $replydata.$renderobj->render_forumreply();				
			}			
		}
	}	
	return $replydata;
}

//Assign a badge to student.
function saveassignbadge($badgeid,$recipientid,$projectid){
	global $USER, $DB;
	$flag = false;
	$params = array(
		'badgeid' => $badgeid,
		'recipientid' => $recipientid,
		'courseid' => $projectid
	);
	if (!$DB->record_exists('badge_manual_award', $params)) {
        $award = new stdClass();
        $award->badgeid = $badgeid;
        $award->issuerid = $USER->id;
        $award->issuerrole = $USER->msn;
        $award->recipientid = $recipientid;
        $award->datemet = time();
		$award->courseid = $projectid;
        if ($DB->insert_record('badge_manual_award', $award)) {
            $flag = true;
        }
    }
	return $flag;
}

//Get watson Suggest Mentors to be assign in a Project..
//Return @html with Mentor img & name
function getsuggestmentors_project($projectid){
	global $DB;
	$conditions = array('projectid'=>$projectid);
	$content = '';
	$data = $DB->get_records('watson_mentor_match',$conditions, '', 'mentorid', $limitfrom=0, $limitnum=3);
	if(count($data)>0){
		foreach($data as $key=>$value){
			$encryptid = encryptdecrypt_userid($value->mentorid,'en');			
			$content.='<div class="userprofileimg">
			'.frmuserimghtml($value->mentorid).'
			</br>
			<a href="javascript:void(0);" data-url="'.$encryptid.'" class="watsonmentor" onclick="assign_suggestmentor(this);">Assign</a>
			</div>';
		}
	}
	return $content;
}

//Get watson Suggest Mentors to be assign in a School..Assign Mentor to school (Nitiadmin)
//Return @html with Mentor img & name
function getsuggestmentors_school($schoolid){
	global $DB;
	$mid = atal_get_roleidbyname('mentor');
	$conditions = array('msn'=>$mid);
	$content = '';
	//$data = $DB->get_records('watson_mentor_match',$conditions, '', 'mentorid', $limitfrom=0, $limitnum=3);
	$data = $DB->get_records('user',$conditions, '', 'id', $limitfrom=0, $limitnum=3);
	if(count($data)>0){
		foreach($data as $key=>$value){
			$encryptid = $value->id; //encryptdecrypt_userid($value->id,'en');		
			$content.='<div class="userprofileimg">
			'.frmuserimghtml($value->id).'
			</br>
			<a href="javascript:void(0);" data-url="'.$encryptid.'" class="watsonmentor" onclick="assign_suggestmentorschool(this);">Assign</a>
			</div>';
		}
	}
	return $content;
}

//Assign a mentor to School. (Mostly Suggest mentors)
//@Params: INT projectid , INT Userid
function assign_mentortoschool($projectid, $userid){
	global $DB;
	$course = $DB->get_record("course",array('id'=>$projectid),'idnumber');
	$count = $DB->count_records('user_school', array('userid'=>$userid,'schoolid'=>$course->idnumber));
	if($count==0 && $userid>0){
		$cm = new stdClass();
		$cm->userid = $userid;
		$cm->schoolid = $course->idnumber;
		$cm->role = 'mentor';
		$DB->insert_record('user_school',$cm);
	}
}

//get List of all Schools in this Platform.
function frmget_allschoollist(){
	global $DB;
	$sql = "SELECT s.id,concat(s.name,' - ',c.name) as name FROM {school} s JOIN {city} c ON s.cityid=c.id WHERE s.activestatus=1 ORDER BY s.name";
	$data = $DB->get_records_sql($sql);
	return $data;
}


//Asign selected multiple users into a course
function assignusers_incourse($data,$newcourseid,$enrolid){
	global $DB,$USER;
	if(isset($data->mentor)){
		foreach($data->mentor as $k=>$vid){
			if($vid>0){
				$cnt = $DB->count_records('user_enrolments', array('enrolid'=>$enrolid,'userid'=>$vid));
				if($cnt==0){
					$cm = new stdClass();
					$cm->status = 0;
					$cm->enrolid = $enrolid;
					$cm->userid = $vid;
					$cm->timestart = 0;
					$cm->timeend = 0;
					$cm->modifierid = $USER->id;
					$cm->timecreated  = time();
					$cm->timemodified  = time();
					$DB->insert_record('user_enrolments', $cm);
					projectroleassignment($newcourseid,$vid,'mentor');
				}
			}
		}
		unset($vid);
	}
	$cnt = 0;
	if(isset($data->student)){
		foreach($data->student as $k=>$vid){
			if($vid>0){
				$cnt = $DB->count_records('user_enrolments', array('enrolid'=>$enrolid,'userid'=>$vid));
				if($cnt==0){
					$cm = new stdClass();
					$cm->status = 0;
					$cm->enrolid = $enrolid;
					$cm->userid = $vid;
					$cm->timestart = time();
					$cm->timeend = 0;
					$cm->modifierid = $USER->id;
					$cm->timecreated  = time();
					$cm->timemodified  = time();
					$DB->insert_record('user_enrolments', $cm);
					projectroleassignment($newcourseid,$vid,'student');
				}
			}
		}
	}
}

/*Temp function to upload project asset to
	Temp location for demo purpose
 @Created: 10-01-2018
 ...After Demo use Moodle Project assets funtionality.
*/
function saveprojectasset(){
	global $CFG,$DB;	
	//Array ( [pid] => 16 [assetflag] => y ) 
	//Array ( [projectfile] => Array ( [name] => deep.jpg [type] => image/jpeg [tmp_name] => C:\xampp\tmp\php1348.tmp [error] => 0 [size] => 4182 ) )
	$projectid = $_POST['pid'];
	$target_dir = $CFG->dirroot."/\projectupload\/";
	$filename = 'p'.$projectid. basename($_FILES["projectfile"]["name"]);
	$target_file = $target_dir.$filename;
	$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
	
	if(isset($_FILES["projectfile"]['tmp_name']) && !empty($_FILES["projectfile"]['name'])){
		if (move_uploaded_file($_FILES["projectfile"]["tmp_name"], $target_file)) {
			$flag = true;
		} else {
			$flag = false;
		}
	}
	if($flag){
		$cm = new stdClass();
		$cm->projectid = $projectid ;
		$cm->name = $filename ;
		$cm->type = $imageFileType ;
		//ztmp_projectfile  Temporary Table....
		$DB->insert_record('ztmp_projectfile', $cm);		
	}
}

