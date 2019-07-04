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

/* @package: core_create
 * @CreatedBy: Jothi (IBM)
 * @CreatedOn: 09-03-2018
 * @Description: Library functions for Create Module
*/

/*
 * Function to fetch the list of ATAL Mentors
 * Returns Resultset as Object
*/
function mentor_list($limit='',$start=0,$condition='')
{
	global $DB;
	$sql ="SELECT u.id,u.firstname,u.lastname,u.email,u.city,u.picture,u.department FROM {user} u left join {user_info_data} ud on ud.userid=u.id ";
	$sql.="WHERE ud.data='mentor data' and ud.acceptterms=1 and u.deleted=0 $condition order by u.timemodified desc limit $start,$limit";
	//echo $sql;die;
	$result = $DB->get_records_sql($sql);
	if(count($result)>0){
		foreach($result as $mentordata)
		{
			$user = new stdClass();
			$user->id = $mentordata->id;
			//$picture = get_userprofilepic($user);
			$userurl = getuser_profilelink($user->id);
			$usrimg = get_userprofilepic($user);
			$picture ='<div class="left picture"><a href="'.$userurl.'" >'.$usrimg.'</a></div>'; 
			$mentordata->picture = $picture;
		}
	}
	return $result;
}
/*
 * Function to fetch the list of ATAL Schools
 * Returns Resultset as Object
*/
function school_list($limit='',$start=0,$condition='')
{
	global $DB;
	$sql ="SELECT ms.*,mus.userid,mus.schoolid,mu.firstname,mu.lastname FROM {school} ms left join ";
	$sql.="(select * from {user_school} where role='incharge') mus on mus.schoolid=ms.id left join {user} mu on mu.id=mus.userid";
	$sql.=" WHERE ms.activestatus=1 and mu.deleted=0 $condition group by ms.id order by ms.id desc limit $start,$limit ";
	$result = $DB->get_records_sql($sql);
	return $result;
}

/*
 * Function to fetch the list of ATAL Schools
 * Returns Resultset as Object
*/
function student_list($schoolid)
{
	global $DB;
	$sql ="SELECT mus.*,mu.firstname,mu.lastname,mu.email,mu.phone1,mu.picture FROM {user_school} mus join {user} mu on mu.id=mus.userid WHERE mus.role='student' ";
	$sql.="and mus.schoolid=$schoolid AND mu.deleted=0 ORDER BY mu.timemodified desc";
	$result = $DB->get_records_sql($sql);
	if(count($result)>0){
		foreach($result as $student)
		{
					$user = new stdClass();
					$user->id = $student->userid;
					//$picture = get_userprofilepic($user);
					$userurl = getuser_profilelink($user->id);
					$usrimg = get_userprofilepic($user);
					$picture ='<div class="left picture"><a href="'.$userurl.'">'.$usrimg.'</a></div>'; 
					$student->picture = $picture;
		}
	}
	return $result;
}
/*
* Fucntion to Get All the Events in ATAL Portal
* Returns : Resultset Object
*/
function event_list(){
	global $DB;
	$currenttime = time();
	$query = "SELECT * FROM {event} WHERE eventtype='site' ORDER By id DESC";
    $result = $DB->get_records_sql($query);
	return $result;
}
/*
 * Function to get Total mentors in ATAL Portal
 * Returns Resultset as Object
*/
function getTotalMentorsCount($condition='')
{
	global $DB;
	$sql ="SELECT u.id,u.firstname,u.lastname,u.email,u.city,u.picture,u.department FROM {user} u left join  (select * from {user_info_data} ud where data='mentor data') ud on ud.userid=u.id where u.deleted=0 and u.msn=4 $condition";
	$result = $DB->get_records_sql($sql);
	$count=0;
	if(count($result)>0)
		$count = count($result);
	return $count;
}
/*
 * Function to get Total Schools in ATAL Portal
 * Returns Resultset as Object
*/
function getTotalSchoolCount($condition='')
{
	global $DB;
	$sql ="SELECT ms.*,mus.userid,mus.schoolid,mu.firstname,mu.lastname FROM {school} ms left join (select * from {user_school} where role='incharge') mus ";
	$sql.=" on mus.schoolid=ms.id left join {user} mu on mu.id=mus.userid WHERE ms.activestatus=1 and mu.deleted=0 $condition";
	$result = $DB->get_records_sql($sql);
	$count=0;
	if(count($result)>0)
		$count = count($result);
	return $count;
}
/*
 * Function to return schoolid of the incharge
 * Returns school resultset as object
*/
function get_incharge_schoolid($userid)
{	
	global $DB;
	$sql ="SELECT * FROM {user_school} mus join mdl_school ms on ms.id=mus.schoolid  WHERE userid=$userid";
	$result = $DB->get_record_sql($sql);
	return $result;
}
function displayeventimage($parentid)
{
	global $DB,$CFG,$USER;

	$atal_variable = get_atalvariables();
	$sql = "SELECT f.id as id,c.id as courseid FROM {forum} f JOIN {course} c ON f.course=c.id
	WHERE c.idnumber='".$atal_variable['sitecourse_idnumber']."' AND f.type='blog'";
	$record = $DB->get_records_sql($sql);
	if(count($record)>0) {
		foreach($record as $key=>$values){
			$forumid = $values->id;
			$courseid = $values->courseid;
		}
	}
	$cm  = get_coursemodule_from_instance('forum', $forumid, $courseid);
	$context  = context_module::instance($cm->id);
	$draftitemid = file_get_submitted_draft_itemid('attachment');
	file_prepare_draft_area($draftitemid, $context->id, 'mod_forum', 'attachment', $parentid,
							array('subdirs' => 0, 'maxbytes' =>1024, 'maxfiles' => 50));
	return $draftitemid;
}

//Remove Event image
function remove_eventimage($eventid,$postid){
	global $DB,$CFG;

	$atal_variable = get_atalvariables();
	$forumid = 0;
	$courseid = 0;
	$sql = "SELECT f.id as id,c.id as courseid FROM {forum} f JOIN {course} c ON f.course=c.id
	WHERE c.idnumber='".$atal_variable['sitecourse_idnumber']."' AND f.type='blog'";
	$record = $DB->get_records_sql($sql);
	if(count($record)>0) {
		foreach($record as $key=>$values){
			$forumid = $values->id;
			$courseid = $values->courseid;
		}
	}
	$cm  = get_coursemodule_from_instance('forum', $forumid, $courseid);
	$context  = context_module::instance($cm->id);
	$fs = get_file_storage();
	$fs->delete_area_files($context->id, 'mod_forum', 'attachment', $postid);
	$fs->delete_area_files($context->id, 'mod_forum', 'post', $postid);
	//Delete this Post
	$discussion = $DB->get_record('forum_posts',array('id'=>$postid),'discussion');
	$DB->delete_records('forum_posts', array('id' => $postid));
	$DB->delete_records('forum_discussions', array('id' => $discussion->discussion));
}

//Get Event Data
function frmcreateeventobject($id){
	global $DB;
	$eventformval = new StdClass();
	$event = $DB->get_record('event', array('id'=>$id), '*');
	$eventformval->id = $event->id;
	$eventformval->name = $event->name;
	$eventformval->description = $event->description;
	//Set time
	//06/26/2018 01:15:00
	if($event->timestart){
	$timestart = date('d/m/Y/H/i/s', $event->timestart);
	$timestart = explode('/',$timestart);
	$eventformval->timestart['day']=$timestart[0];
	$eventformval->timestart['month']=$timestart[1];
	$eventformval->timestart['year']=$timestart[2];
	$eventformval->timestart['hour']=$timestart[3];
	$eventformval->timestart['minute']=$timestart[4];
	}
	$eventformval->duration=$event->timeduration;
	if($event->timeduration)
	{
		$eventformval->duration=1;
		$actualduration =$event->timestart+$event->timeduration;
		$actualduration = date('d/m/Y/H/i/s', $actualduration);
		$durationend = explode('/',$actualduration);
		$eventformval->timedurationuntil['day']=$durationend[0];
		$eventformval->timedurationuntil['month']=$durationend[1];
		$eventformval->timedurationuntil['year']=$durationend[2];
		$eventformval->timedurationuntil['hour']=$durationend[3];
		$eventformval->timedurationuntil['minute']=$durationend[4];
	}
	//$eventformval->postfile = $draftitemid;
	$eventformval->postfile = displayeventimage($event->parentid);
	$eventformval->parentid = $event->parentid;
	return $eventformval;
}

