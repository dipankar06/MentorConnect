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
 * @CreatedBy: Jothi (IBM)
 * @CreatedOn: 17-04-2018
 * @Description: Library functions For atalfeatures
*/

//Function to fetch the list of Scorm Videos for Mentors
//Returns Resultset as Object

function getScormResultset()
{
	global $DB;
	$sql = 'select s.id as scormid,c.id as courseid,s.name as scormname,s.intro,cm.id as modid,ss.id as scormscosid from {course} c join {course_categories} cc on cc.id= c.category  join {scorm} s on s.course=c.id join {course_modules} cm on cm.instance=s.id join {scorm_scoes} ss on ss.scorm=s.id where  cc.id=1 and  c.shortname="mentorscorm" and cm.module=18 and ss.organization="Course_ID1_ORG" order by scormid asc';
	$result = $DB->get_records_sql($sql);
	//echo "<pre>";
	//print_r($result);die;
	return $result;
}
function checkScormStatusbyId($scormid,$courseid)
{
	global $DB,$USER;
	$sql = "SELECT finalgrade FROM {grade_grades} gg join {grade_items} gi on gi.id=gg.itemid where gi.courseid=$courseid and gi.iteminstance=$scormid and gi.itemmodule='scorm' and gg.userid=".$USER->id;
	$result = $DB->get_record_sql($sql);
	if($result)
	{
		if(((int)$result->finalgrade)>=60)
			return true;
	}
	else
		return false;
}
//RawSql
/*
select c.shortname,c.id,cm.id as modid,ss.id as scormid from mdl_course c join mdl_course_categories cc on cc.id= c.category join mdl_course_modules cm on cm.course=c.id join mdl_scorm s on s.course=c.id join mdl_scorm_scoes ss on ss.scorm=s.id where cc.id=1 and c.shortname='mentorscorm' and cm.module=18 and ss.organization='Course_ID1_ORG' 
*/
//RefinedSql
/* select s.id as scormid,c.id as courseid,s.name as scormname,s.intro,cm.id as modid,ss.id as scormid from mdl_course c join mdl_course_categories cc on cc.id= c.category  join mdl_scorm s on s.course=c.id join mdl_course_modules cm on cm.instance=s.id join mdl_scorm_scoes ss on ss.scorm=s.id where  cc.id=1 and  c.shortname="mentorscorm" and cm.module=18 and ss.organization='Course_ID1_ORG' */

//Status RawSql
/* select gi.id,c.shortname,c.id,cm.id as modid,gg.itemid,gg.finalgrade from mdl_course c join mdl_course_categories cc on cc.id= c.category join mdl_course_modules cm on cm.course=c.id join mdl_grade_items gi on gi.courseid=c.id join mdl_grade_grades gg on gg.itemid=gi.id where cc.id=1 and c.shortname='mentorscorm' and cm.module=18 and gi.itemtype='mod' and gi.itemmodule='scorm' and gg.userid=14 */
function enrol_user_course($courseid,$userid)
{
	global $USER,$DB;
	try{
		$transaction = $DB->start_delegated_transaction();
		$enrol = $DB->get_record('enrol',array('enrol'=>'manual','courseid'=>$courseid),'id');				
		if($userid>0){
			//add enrolment..
			$ue = new stdClass();
			$ue->enrolid      = $enrol->id;
			$ue->status       = 0;
			$ue->userid       = $userid;
			$ue->timestart    =  time();
			$ue->timeend      = 0;
			$ue->modifierid   = $USER->id;
			$ue->timecreated  = time();
			$ue->timemodified = time();
			$DB->insert_record('user_enrolments', $ue);
			$coursecontext = context_course::instance($courseid);
			$cx = new stdClass();
			$cx->roleid = 4;
			$cx->contextid = $coursecontext->id;
			$cx->userid = $userid;
			$cx->timemodified = time();
			$cx->modifierid = $USER->id;
			$DB->insert_record('role_assignments', $cx);
			$transaction->allow_commit(); 
		}
	}catch(Exception $e)
	{
		$transaction->rollback($e);
	}
}
function enrol_user_scormcourse($userid)
{
	global $USER,$DB;
	try{
		$course_result = $DB->get_record('course',array('shortname'=>'mentorscorm','category'=>1),'id');
		$courseid = $course_result->id;	
		$transaction = $DB->start_delegated_transaction();
		$enrol = $DB->get_record('enrol',array('enrol'=>'manual','courseid'=>$courseid),'id');
		if($enrol && $userid>0){
			$checkenrol = $DB->get_record('user_enrolments',array('enrolid'=>$enrol->id,'userid'=>$userid),'id');
			if($checkenrol)
				return true;
			$ue = new stdClass();
			$ue->enrolid      = $enrol->id;
			$ue->status       = 0;
			$ue->userid       = $userid;
			$ue->timestart    =  time();
			$ue->timeend      = 0;
			$ue->modifierid   = $USER->id;
			$ue->timecreated  = time();
			$ue->timemodified = time();
			$DB->insert_record('user_enrolments', $ue);
			$coursecontext = context_course::instance($courseid);
			$cx = new stdClass();
			$cx->roleid = 4;
			$cx->contextid = $coursecontext->id;
			$cx->userid = $userid;
			$cx->timemodified = time();
			$cx->modifierid = $USER->id;
			$DB->insert_record('role_assignments', $cx);
			$transaction->allow_commit(); 
		}
	}catch(Exception $e)
	{
		$transaction->rollback($e);
	}
}
function getschool_bycity($city="")
{
	global $DB;
	$result= array();
	$condition = '';
	if($city)
		$condition.="where s.cityid=".$city;
	// Query Added with sequence numbers since the response from ajax automatically sorts the results by school id .
	$sql = "SELECT @a:=@a+1 as serial_number, s.id,s.name as schoolname,c.name as cityname FROM (select @a:=0) initvars,{school} s JOIN {city} c ON s.cityid=c.id $condition ORDER BY s.name";
	//$sql = "SELECT s.id,s.name as schoolname,c.name as cityname FROM {school} s JOIN {city} c ON s.cityid=c.id  $condition ORDER BY s.name";
	//$sql = "select ms.id,ms.name as schoolname,mc.name as cityname from mdl_school ms join mdl_city mc on mc.id=ms.cityid $condition ORDER BY ms.name";
	$result = $DB->get_records_sql($sql);
	return $result;
	
}
function getschool_bycity_mentorchoice($city="")
{
	global $DB;
	$result= array();
	$condition = '';
	if($city)
		$condition.="where s.cityid=".$city;
	$max_mentor = getCustomSettings('school_maxmentor');
	if($max_mentor)
		$max_mentor=$max_mentor->atl_value;
	else
		$max_mentor=5;
	$sql = "SELECT @a:=@a+1 as serial_number,s.id,s.name as schoolname,c.name as cityname FROM (select @a:=0) initvars,mdl_school s JOIN mdl_city c ON s.cityid=c.id join (SELECT schoolid,role,userid FROM `mdl_user_school` where role='mentor' group by schoolid having count(id)<$max_mentor) as mus on mus.schoolid=s.id $condition ORDER BY s.name ";
	$result = $DB->get_records_sql($sql);
	return $result;
}
function getSqlQuery_bulkmail($val){
	$sql='';
	switch($val)
	{
		case 'no_mentor':
		$sql='SELECT mu.id,mu.email,mu.username,mu.firstname,mu.lastname,mu.passraw,GROUP_CONCAT(ms.atl_id) as allotedschoolcode,GROUP_CONCAT(ms.name) as allotedschoolname FROM `mdl_user` mu join mdl_user_school mus on mus.userid=mu.id join mdl_school ms on ms.id=mus.schoolid where msn=4 and deleted=0 group by mu.id ';
		break;
		case 'mnotlogged':
		$sql='SELECT mu.id,mu.email,mu.username,mu.firstname,mu.lastname,mu.passraw,GROUP_CONCAT(ms.atl_id) as allotedschoolcode,GROUP_CONCAT(ms.name) as allotedschoolname FROM `mdl_user` mu join mdl_user_school mus on mus.userid=mu.id join mdl_school ms on ms.id=mus.schoolid where msn=4 and deleted=0 and policyagreed=0 group by mu.id ';
		break;
		case 'mnotupdated':
		$sql='SELECT mu.id,mu.email,mu.username,mu.firstname,mu.lastname,mu.passraw,GROUP_CONCAT(ms.atl_id) as allotedschoolcode,GROUP_CONCAT(ms.name) as allotedschoolname FROM `mdl_user` mu join mdl_user_school mus on mus.userid=mu.id join mdl_school ms on ms.id=mus.schoolid where msn=4 and deleted=0 and policyagreed=1 and profilestatus is null group by mu.id '; 
		break;
		case 'mnotstartedtu':
		$sql='SELECT mu.id,mu.email,mu.email,mu.username,mu.firstname,mu.lastname,mu.passraw,GROUP_CONCAT(ms.atl_id) as allotedschoolcode,GROUP_CONCAT(ms.name) as allotedschoolname FROM `mdl_user` mu join mdl_user_school mus on mus.userid=mu.id join mdl_school ms on ms.id=mus.schoolid where msn=4 and deleted=0 and mu.id not in (select distinct(gg.userid) from mdl_course c join mdl_grade_items gi on gi.courseid=c.id join mdl_grade_grades gg on gg.itemid=gi.id where c.shortname="mentorscorm" and gi.itemmodule="scorm") and mu.profilestatus=1 group by mu.id'; 
		break;
		case 'mnotcompletetu':
		$sql='SELECT mu.id,mu.email,mu.username,mu.firstname,mu.lastname,mu.passraw,GROUP_CONCAT(ms.atl_id) as allotedschoolcode,GROUP_CONCAT(ms.name) as allotedschoolname FROM `mdl_user` mu join mdl_user_info_data mud on mud.userid=mu.id join mdl_user_school mus on mus.userid=mu.id join mdl_school ms on ms.id=mus.schoolid where msn=4 and deleted=0 and mud.data="mentor data" and mud.scormstatus is null and mu.id not in (select distinct(gg.userid) from mdl_course c join mdl_grade_items gi on gi.courseid=c.id join mdl_grade_grades gg on gg.itemid=gi.id where c.shortname="mentorscorm" and gi.itemmodule="scorm") and mu.profilestatus=1 group by mu.id '; 
		break;
		case 'mnotsentinvi':
		$sql='SELECT mu.id,mu.email,mu.username,mu.firstname,mu.lastname,mu.passraw,GROUP_CONCAT(ms.atl_id) as allotedschoolcode,GROUP_CONCAT(ms.name) as allotedschoolname FROM `mdl_user` mu join mdl_user_school mus on mus.userid=mu.id join mdl_school ms on ms.id=mus.schoolid where msn=4 and deleted=0 and mu.id not in (SELECT distinct(userid) FROM `mdl_event` where parentid!=0) and mu.profilestatus=1 group by mu.id '; 
		break;
		case 'no_school':
		$sql='SELECT mu.id,mu.email,mu.username,mu.firstname,mu.lastname,mu.passraw,ms.name as schoolname,ms.atl_id as schoolcode FROM `mdl_user` mu join mdl_user_school mus on mus.userid=mu.id join mdl_school ms on ms.id=mus.schoolid where msn=3 and deleted=0 and mus.role="incharge" group by mu.id';
		break;
		case 'snotlogged':
		$sql='SELECT mu.id,mu.email,mu.username,mu.firstname,mu.lastname,mu.passraw,ms.name as schoolname,ms.atl_id as schoolcode FROM `mdl_user` mu join mdl_user_school mus on mus.userid=mu.id join mdl_school ms on ms.id=mus.schoolid where msn=3 and deleted=0 and mus.role="incharge" and policyagreed=0 group by mu.id';
		break;
		case 'snotupdated':
		$sql='SELECT mu.id,mu.email,mu.username,mu.firstname,mu.lastname,mu.passraw,ms.name as schoolname,ms.atl_id as schoolcode FROM `mdl_user` mu join mdl_user_school mus on mus.userid=mu.id join mdl_school ms on ms.id=mus.schoolid where msn=3 and deleted=0 and mus.role="incharge" and policyagreed=1 and profilestatus is null group by mu.id';
		break;
		case 'no_aschool':
		$sql="SELECT mu.id,mu.email,mu.username,mu.firstname,mu.lastname,mu.passraw,ms.name as schoolname,ms.atl_id as schoolcode,ms.mentorids,ms.mentoreemails FROM `mdl_user` mu join mdl_user_school mus on mus.userid=mu.id join (select mdl_school.id as id,GROUP_CONCAT(userid) as mentorids,GROUP_CONCAT(mdl_user.email) mentoreemails,mdl_school.name,mdl_school.atl_id from mdl_user_school join mdl_school on mdl_school.id=mdl_user_school.schoolid join mdl_user on mdl_user.id=mdl_user_school.userid where role='mentor' and deleted=0 group by mdl_school.id) ms on ms.id=mus.schoolid where msn=3 and deleted=0 and mus.role='incharge' group by mu.id";
		break;
		case 'asnotlogged':
		$sql="SELECT mu.id,mu.email,mu.username,mu.firstname,mu.lastname,mu.passraw,ms.name as schoolname,ms.atl_id as schoolcode,ms.mentorids,ms.mentoreemails FROM `mdl_user` mu join mdl_user_school mus on mus.userid=mu.id join (select mdl_school.id as id,GROUP_CONCAT(userid) as mentorids,GROUP_CONCAT(mdl_user.email) mentoreemails,mdl_school.name,mdl_school.atl_id from mdl_user_school join mdl_school on mdl_school.id=mdl_user_school.schoolid join mdl_user on mdl_user.id=mdl_user_school.userid where role='mentor' and deleted=0 group by mdl_school.id) ms on ms.id=mus.schoolid where msn=3 and deleted=0 and mus.role='incharge' and policyagreed=0 group by mu.id";
		break;
		case 'asnotupdated':
		$sql="SELECT mu.id,mu.email,mu.username,mu.firstname,mu.lastname,mu.passraw,ms.name as schoolname,ms.atl_id as schoolcode,ms.mentorids,ms.mentoreemails FROM `mdl_user` mu join mdl_user_school mus on mus.userid=mu.id join (select mdl_school.id as id,GROUP_CONCAT(userid) as mentorids,GROUP_CONCAT(mdl_user.email) mentoreemails,mdl_school.name,mdl_school.atl_id from mdl_user_school join mdl_school on mdl_school.id=mdl_user_school.schoolid join mdl_user on mdl_user.id=mdl_user_school.userid where role='mentor' and deleted=0 group by mdl_school.id) ms on ms.id=mus.schoolid where msn=3 and deleted=0 and mus.role='incharge' and policyagreed=1 and profilestatus is null group by mu.id";
		break;
	}
	return $sql;
}
function replaceBulkMail_Placeholder($mailbody,$value,$role)
{
	if (strpos($mailbody, '&lt;username&gt;') !== false) {
			$mailbody = str_replace("&lt;username&gt;",$value->username,$mailbody);
	}
	if (strpos($mailbody, '&lt;password&gt;') !== false) {
		if($value->passraw)
			$mailbody = str_replace("&lt;password&gt;",$value->passraw,$mailbody);
		else
			$mailbody = str_replace("&lt;password&gt;",'Your Updated Password/Use Forgot Password help to reset the passowrd.',$mailbody);
	}
	if (strpos($mailbody, '&lt;firstname&gt;') !== false) {
			$mailbody = str_replace("&lt;firstname&gt;",$value->firstname,$mailbody);
	}
	if (strpos($mailbody, '&lt;lastname&gt;') !== false) {
			$mailbody = str_replace("&lt;lastname&gt;",$value->lastname,$mailbody);
	}
	if($role == 'mentor'){
		if (strpos($mailbody, '&lt;allotedschoolname&gt;') !== false) {
			if($value->allotedschoolname)
				$mailbody = str_replace("&lt;allotedschoolname&gt;",$value->allotedschoolname,$mailbody);
			else
				$mailbody = str_replace("&lt;allotedschoolname&gt;",'No School Assgined',$mailbody);
		}
		if (strpos($mailbody, '&lt;allotedschoolcode&gt;') !== false) {
			if($value->allotedschoolcode)
				$mailbody = str_replace("&lt;allotedschoolcode&gt;",$value->allotedschoolcode,$mailbody);
			else
				$mailbody = str_replace("&lt;allotedschoolcode&gt;",'No School Assgined',$mailbody);
		}
	}
	if($role == 'incharge' || $role == 'student' ){
		if (strpos($mailbody, '&lt;schoolname&gt;') !== false) {
				$mailbody = str_replace("&lt;schoolname&gt;",$value->schoolname,$mailbody);
		}
		if (strpos($mailbody, '&lt;schoolcode&gt;') !== false) {
				$mailbody = str_replace("&lt;schoolcode&gt;",$value->schoolcode,$mailbody);
		}
	}
	return $mailbody;
}