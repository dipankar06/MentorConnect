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
 * @CreatedOn: 20-01-2018
 * @Description: External Library functions for Project Module.
 This File is used in other Modules outside Project.
*/

require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir. '/filestorage/file_storage.php');
require_once($CFG->dirroot. '/course/lib.php');

function frmgetcourse_overviewfiles($courseid) {
	global $CFG;
	$fs = get_file_storage();
	$context = context_course::instance($courseid);
	$files = $fs->get_area_files($context->id, 'course', 'overviewfiles', false, 'filename', false);
	if (count($files)) {
		$overviewfilesoptions = course_overviewfiles_options($courseid);
		$acceptedtypes = $overviewfilesoptions['accepted_types'];
		if ($acceptedtypes !== '*') {
			// Filter only files with allowed extensions.
			foreach ($files as $key => $file) {
				if (!file_extension_in_typegroup($file->get_filename(), $acceptedtypes)) {
					unset($files[$key]);
				}
			}
		}
		if (count($files) > $CFG->courseoverviewfileslimit) {
			// Return no more than $CFG->courseoverviewfileslimit files.
			$files = array_slice($files, 0, $CFG->courseoverviewfileslimit, true);
		}
	}
	return $files;
}

/**
 * Returns the first course's summary issue
 *
 * @param int courseid
 * @return string
 */
function frmgetcourse_summary_image($courseid) {
	global $CFG;
	$contentimage = '';
	foreach (frmgetcourse_overviewfiles($courseid) as $file) {
		$isimage = $file->is_valid_image();
		$url = file_encode_url("$CFG->wwwroot/pluginfile.php",
		'/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
		$file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
		if ($isimage) {
			$contentimage = html_writer::empty_tag('img', array('src' => $url, 'alt' => 'Course Image '. $course->fullname,'class' => 'card-img-top w-100'));
			break;
		}
	}
	if (empty($contentimage)) {
		$url = $CFG->wwwroot . "/theme/".$CFG->theme."/pix/default_course.jpg";
		$contentimage = html_writer::empty_tag('img', array('src' => $url, 'alt' => 'Course Image '. $course->fullname,'class' => 'card-img-top w-100'));
	}
	return $contentimage;
}

/**
 * Returns the first course's summary issue
 *
 * @param int courseid
 * @return string
 */
function frmgetcourse_summary_imagepath($courseid) {
	global $CFG;
	$contentimage = '';
	foreach (frmgetcourse_overviewfiles($courseid) as $file) {
		$isimage = $file->is_valid_image();
		$url = file_encode_url("$CFG->wwwroot/pluginfile.php",
		'/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
		$file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
		if ($isimage) {
			$contentimage = $url;
			break;
		}
	}
	if (empty($contentimage)) {
		$url = $CFG->wwwroot . "/theme/".$CFG->theme."/pix/folder.png";
		$contentimage = $url;
	}
	return $contentimage;
}

//New Student/Incharge Created By School InCharge OR by nitiadmin..
//@CreatedOn: 20-02-2018
function create_newuser($data){
	global $DB, $CFG, $USER;
	$atalvariables = get_atalvariables();
	$roleid = theme_get_roleidbyname('student');
	$schoolname = '';
	$city = '';
	if ($data->flag == 'add') {
		// Creating new user.
		$usertype = $data->usertype;
		if($usertype=='student'){
			//Can only be created by incharge login [logged in user id used to fetch the school id]
			$schooldata = $DB->get_record('user_school', array('userid'=>$USER->id), '*', MUST_EXIST);
			$schoolid = (isset($schooldata->schoolid))?$schooldata->schoolid:0;
			$roleid = theme_get_roleidbyname('student');
		} else{
			$schoolid = $data->school;
			$roleid = theme_get_roleidbyname($usertype);
		}
		$state = "0";
		$sql = "SELECT s.name,c.name as city,ss.name as state FROM {school} s JOIN {city} c ON s.cityid = c.id LEFT JOIN {state} ss ON c.stateid=ss.id WHERE s.id=".$schoolid;
		$result = $DB->get_record_sql($sql);
		if(isset($result->name)){
			$schoolname = $result->name;
		    $city = $result->city;
			$state = (!empty($result->state))?$result->state:'0';
		}
		$user = new stdClass();
		$user->auth = 'manual';
		$user->confirmed = 1;
		$user->deleted = 0;
		$user->timezone = '99';
		$user->mnethostid = 1;
		if(isset($data->username)){
			$user->username  = $data->username;
		}
		else
			$user->username = trim($data->email);
		//$user->password = hash_internal_user_password($data->newpassword);
		$ran = generate_randomstring();
		$user->passraw = $ran;
		$user->password=hash_internal_user_password($ran);
		$user->firstname = $data->firstname;
		$user->lastname = $data->lastname;
		$user->email = $data->email;
		$user->icq = "newuser";
		$user->msn = $roleid;
		$user->institution = $schoolname;
		$user->city = $city;
		/* if(isset($data->state)){
			$user->aim = $data->state;
		} */
		$user->aim = (isset($data->state) && !empty($data->state))?$data->state:$state;
		$user->country = 'IN';
		$user->timecreated  = time();
		$user->timemodified  = time();
		if(isset($data->gender)){
			$user->gender  = $data->gender;
		}
		if(isset($data->phone1)){
			$user->phone1  = $data->phone1;
		}
		// Insert the user into the database.
		$newuserid = $DB->insert_record('user', $user);
		$usercontext = context_user::instance($newuserid);
		$cm = new stdClass();
		$cm->userid = $newuserid;
		$cm->schoolid = $schoolid;
		$cm->role = $usertype;
		if(isset($data->studentclass)){
			$cm->studentclass = $data->studentclass;
		}
		$DB->insert_record('user_school', $cm);
	}
	$name = $user->firstname.' '.$user->lastname;
	send_welcomemail($roleid,$newuserid,$name,$data->email,$user->username,$ran);
	return true;
}
/* Create a Course
 * @Params: data object
 * @Return: Boolean True or False
 * Not in Use Yet
*/
function frmext_createcourse($data){
	$return = false;
	
	return $return;
}

//Get Assign or Approved Projectid List for this user
//@Param: uid Userid int , rolename string
//@Return: Array projectid
function get_activeprojectids($uid,$rolename){
	global $DB;
	$count = 0;
	$projectArray = array();
	$atalvariables = get_atalvariables();
	$projectcat = $atalvariables['project_categoryid'];
	if($rolename=='mentor'){
		$sql="SELECT c.id FROM {user} u JOIN {user_enrolments} ue ON u.id=ue.userid JOIN {enrol} e ON ue.enrolid=e.id 
		JOIN {course} c ON e.courseid=c.id WHERE e.enrol='manual' AND u.id='".$uid."' AND c.startdate>0 AND c.category=$projectcat AND ue.status=1";
		$data = $DB->get_records_sql($sql);
		foreach($data as $key){
			$projectArray[] = $key->id;
		}
	} elseif($rolename=='student'){
		$sql="SELECT c.id FROM {user} u JOIN {user_enrolments} ue ON u.id=ue.userid JOIN {enrol} e ON ue.enrolid=e.id 
		JOIN {course} c ON e.courseid=c.id WHERE e.enrol='manual' AND u.id='".$uid."' AND c.startdate>0 AND c.category=$projectcat";
		$data = $DB->get_records_sql($sql);
		foreach($data as $key){
			$projectArray[] = $key->id;
		}
	} else{
		//incharge
		$sql="SELECT c.id FROM {course} c JOIN (SELECT schoolid FROM {user_school} WHERE userid = ".$uid.") as s ON c.idnumber=s.schoolid 
		AND c.startdate>0  AND c.category=$projectcat";
		$data = $DB->get_records_sql($sql);
		foreach($data as $key){
			$projectArray[] = $key->id;
		}
	}
	return $projectArray;
}
?>