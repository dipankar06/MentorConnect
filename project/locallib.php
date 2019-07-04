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
 * @Description: Secondary Library functions for Project Module
 use by Mostly New modules & ajaxnew.php
*/


require_once($CFG->libdir.'/filelib.php');

//Delete the Project if its Rejected By Atal Incharge.
function delete_rejectproject($projectid){
	global $DB;
	$msg = "";
	$atalvar = get_atalvariables();
	$course = $DB->get_record('course',array('id'=>$projectid),'id,startdate,enddate,visible');
	if($course->startdate==0 && $course->visible==0 && $course->enddate==0){
		//Project is Rejected by Atal Incharge...Delete it.
		delete_innovation($course->id);
		$msg="<p>This Innovation is Rejected, It will Be Deleted From ".$atalvar['sitename']."</p>";

		record_projectlog($course,'delete',"Innovation deleted after rejected by school");
	}
	return $msg;
}

//Delete a project.
function delete_innovation($courseid){
	global $DB, $CFG, $USER;
	$coursecontext = context_course::instance($courseid);
	$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
	$fs = get_file_storage();
	try {
		$transaction = $DB->start_delegated_transaction();
		enrol_course_delete($course);
		$DB->delete_records("forum", array("course" => $courseid));
		$DB->delete_records("course_modules", array("course" => $courseid));
		$DB->delete_records("enrol", array("courseid" => $courseid));
		$DB->delete_records("course_format_options", array("courseid" => $courseid));	
		$fs->delete_area_files($coursecontext->id, 'course'); // Files from summary and section.	
		$DB->delete_records("tag_project", array('projectid'=>$courseid));
		$DB->delete_records("course_comment", array('courseid'=>$courseid));
		$DB->delete_records("course", array("id" => $courseid));
		
		$transaction->allow_commit(); //close transaction
	} catch(Exception $e) {
		$transaction->rollback($e);
		return false;
	}
	return true;
}

//Get User Profile img with name.
function frmuserimghtmlnew($userid){
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
		$content ='<p><a href="'.$userurl.'">'.$usrimg.'</a></p>'.$values->firstname.' '.$values->lastname;
	}
	return $content;
}

//UnEnroll Mentor From a Project
function frmunenroll_mentorproject($userid,$projectid,$roleid){
	global $DB;
	//Get Course enrollid
	$sql = "SELECT e.id FROM {course} p LEFT JOIN {enrol} e ON e.courseid=p.id WHERE p.id = ? AND e.enrol='manual'";
	$data = $DB->get_record_sql($sql, array($projectid));
	$enrolid = $data->id;
	$DB->delete_records("user_enrolments", array('enrolid'=>$enrolid,'userid'=>$userid));
	$coursecontext = context_course::instance($projectid);
	$DB->delete_records("role_assignments", array('userid'=>$userid,'contextid'=>$coursecontext->id,'roleid'=>$roleid));
}
