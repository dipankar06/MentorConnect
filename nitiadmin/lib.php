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

/* @package: core_nitiadmin
 * @CreatedBy: Jothi (IBM)
 * @CreatedOn: 20-06-2018
 * @Description:  Library functions For NitiAdministrationLinks
*/

/*
 * Function to fetch the list of Mentors
 * Returns Resultset as Object
*/
	//getNitiDashbordData();
function getMentorReportList($limit='',$start=0,$condition='')
{
	global $DB;
	$sql = "select u.*,ud.scormstatus,count(CASE WHEN me.userid = u.id THEN 1 END) as meetingcount,count(CASE WHEN (me.meetingstatus = 1 AND me.parentid=u.id) THEN 1 END) as approved,count(CASE WHEN (me.meetingstatus=2 AND me.parentid=u.id) THEN 1 END) as rejected,count(CASE WHEN (me.meetingstatus=3 AND me.parentid=u.id) THEN 1 END) as completed from {user} u left join {user_info_data} ud on ud.userid=u.id  left join (select * from {event} where eventtype='user' and parentid!=0 ) me on (u.id=me.userid or u.id=me.parentid) where msn=4 and deleted=0 and ud.data='mentor data' $condition group by u.id order by u.id asc  limit $start,$limit";
	//echo $sql;die;
	$result = $DB->get_records_sql($sql);
	return $result;
}
function getMentorReportListforExcel()
{
	global $DB;
	//$sql = "select u.*,ud.scormstatus from {user} u left join {user_info_data} ud on ud.userid=u.id where msn=4 and deleted=0 and ud.data='mentor data' order by u.id asc";
	$sql = "select u.*,ud.scormstatus,count(CASE WHEN me.userid = u.id THEN 1 END) as meetingcount,count(CASE WHEN (me.meetingstatus = 1 AND me.parentid=u.id) THEN 1 END) as approved,count(CASE WHEN (me.meetingstatus=2 AND me.parentid=u.id) THEN 1 END) as rejected,count(CASE WHEN (me.meetingstatus=3 AND me.parentid=u.id) THEN 1 END) as completed from {user} u left join {user_info_data} ud on ud.userid=u.id  left join (select * from {event} where eventtype='user' and parentid!=0 ) me on (u.id=me.userid or u.id=me.parentid) where msn=4 and deleted=0 and ud.data='mentor data' group by u.id order by u.id asc ";
	//echo $sql;die;
	$result = $DB->get_records_sql($sql);
	return $result;
}
function getIbmMentorReportListforExcel()
{
	global $DB;
	$sql = "select u.*,ud.scormstatus,count(CASE WHEN me.userid = u.id THEN 1 END) as meetingcount,count(CASE WHEN (me.meetingstatus = 1 AND me.parentid=u.id) THEN 1 END) as approved,count(CASE WHEN (me.meetingstatus=2 AND me.parentid=u.id) THEN 1 END) as rejected,count(CASE WHEN (me.meetingstatus=3 AND me.parentid=u.id) THEN 1 END) as completed from {user} u left join {user_info_data} ud on ud.userid=u.id  left join (select * from {event} where eventtype='user' and parentid!=0 ) me on (u.id=me.userid or u.id=me.parentid) where msn=4 and deleted=0 and ud.data='mentor data' and u.email like '%in.ibm.%' group by u.id order by u.id asc ";
	$result = $DB->get_records_sql($sql);
	return $result;
}
function msmeeting_report_excel()
{
	global $DB;
	$sql = "select e.*,u.username as initiated,p.username as assignee from {event} e join {user} u on e.userid=u.id join {user} p on p.id=e.parentid where e.eventtype='user' and e.parentid!=0";
	$result = $DB->get_records_sql($sql);
	return $result;
}
function getSchoolnamesofMentor($userid)
{
	global $DB;
	$sql = "select us.userid,group_concat(s.name) as schoolname from {user_school} us join {school} s on s.id=us.schoolid where us.userid=$userid group by us.userid";
	$result = $DB->get_record_sql($sql);
	return $result;
}
function checkTrainingMentorExist($userid)
{
	global $DB;
	$sql = "SELECT * FROM {grade_grades} where userid=".$userid;
	$result = $DB->get_record_sql($sql);
	return $result;
}
function getTutorialCompletedMentor()
{
	global $DB;
	$userid=0;
	$sql = "select userid from {user_info_data} where scormstatus=1";
	$result = $DB->get_records_sql($sql);
	if($result)
		$userid = implode(', ', array_column($result, 'userid'));
	return $userid;
}
function getTutorialNotStartedMentor()
{
	global $DB;
	$userid=0;
	//$sql = "select distinct(u.id) as userid from {user} u join {grade_grades} gg on gg.userid=u.id where u.msn=4 and u.deleted=0 ";
	$sql = "select distinct(u.id) as userid from mdl_user u join mdl_grade_grades gg on gg.userid=u.id join mdl_grade_items gi on gg.itemid=gi.id join mdl_course c on gi.courseid=c.id where u.msn=4 and u.deleted=0 and c.shortname='mentorscorm' and gi.itemmodule='scorm'";
	$result = $DB->get_records_sql($sql);
	if($result)
		$userid = implode(', ', array_column($result, 'userid'));
	return $userid;
}
function getTotalStudentCount($condition='')
{
	global $DB;
	$sql = "select u.*,s.name from {user} u join {user_school} us on us.userid=u.id join {school} s on s.id=us.schoolid where u.msn=5 and u.deleted=0 $condition";
	$result = $DB->get_records_sql($sql);
	return count($result);
}
function allstudent_list($limit='',$start=0,$condition='')
{
	global $DB;
	$sql = "select u.*,s.name,s.atl_id,s.school_emailid,s.phone from {user} u join {user_school} us on us.userid=u.id join {school} s on s.id=us.schoolid where u.msn=5 and u.deleted=0 $condition order by u.id asc limit $start,$limit";
	$result = $DB->get_records_sql($sql);
	return ($result);
}
function getAllMeetings($limit='',$start=0,$condition='')
{
	global $DB;
	$sql = "select e.*,CONCAT(u.firstname,' ',u.lastname)as initiated,CONCAT(p.firstname,' ',p.lastname)as assignee from {event} e join {user} u on e.userid=u.id join {user} p on p.id=e.parentid where e.eventtype='user' and e.parentid!=0 $condition limit $start,$limit";
	$result = $DB->get_records_sql($sql);
	return ($result);
}
function getTotalMeetingCount($condition='')
{
	global $DB;
	$sql = "select e.* from {event} e join {user} u on e.userid=u.id join {user} p on p.id=e.parentid where e.eventtype='user' and e.parentid!=0 $condition";
	$result = $DB->get_records_sql($sql);
	return count($result);
}
function getSchoolIncharge($atlid)
{
	global $DB;
	//$sql = "select u.id from {user} u join {event} e on e.userid=u.id join {user_school} us on us.userid=u.id join {school} s on s.id=us.schoolid where (u.username LIKE '%$atlid%' OR s.name LIKE '%$atlid%') and e.eventtype='user' and e.parentid!=0 and u.deleted=0" ;
	//$sql = "select u.id from mdl_user u join mdl_school s on s.atl_id=u.username where (u.username LIKE '%$atlid%' OR s.name LIKE '%$atlid%')";
	$sql = "select u.id from {user} u join {user_school} us on us.userid=u.id join {school} s on s.id=us.schoolid where (u.username LIKE '%$atlid%' OR s.name LIKE '%$atlid%') and u.deleted=0" ;
	$result = $DB->get_records_sql($sql);

	return ($result);
}
function school_list_activityreport($limit='',$start=0,$condition='')
{
	//$sql = "SELECT ms.id as schoolid,count(me.id) as meetingcount,count(CASE WHEN me.meetingstatus = 1 THEN 1 END) as open,count(CASE WHEN me.meetingstatus = 1 THEN 1 END) as approved,count(CASE WHEN me.meetingstatus = 2 THEN 1 END) as rejected,ms.*,mus.userid,mus.schoolid,mu.firstname,mu.lastname,mu.profilestatus,mu.policyagreed,me.name as title FROM mdl_school ms left join (select * from mdl_user_school where role='incharge') mus on mus.schoolid=ms.id left join mdl_user mu on mu.id=mus.userid left join mdl_event me on mu.id=me.userid WHERE mu.deleted=0 $condition group by ms.id order by ms.id desc limit $start,$limit";
	global $DB;
	$sql ="SELECT ms.*,count(CASE WHEN me.userid = mu.id THEN 1 END) as meetingcount,count(CASE WHEN me.meetingstatus = 1 THEN 1 END) as open,count(CASE WHEN (me.meetingstatus = 1  AND me.parentid=mu.id) THEN 1 END) as approved,count(CASE WHEN (me.meetingstatus = 2 AND me.parentid=mu.id) THEN 1 END) as rejected,mus.userid,mus.schoolid,mu.firstname,mu.lastname,mu.profilestatus,mu.policyagreed,me.name as title 	FROM {school} ms left join";
	$sql.="(select * from {user_school} where role='incharge') mus on mus.schoolid=ms.id left join {user} mu on mu.id=mus.userid left join (select * from {event} where eventtype='user' and parentid!=0 ) me on (mu.id=me.userid or mu.id=me.parentid)";
	$sql.=" WHERE mu.deleted=0 $condition group by ms.id order by ms.id desc limit $start,$limit ";
	$result = $DB->get_records_sql($sql);
	return $result;
}
function school_activityreport_excel($limit='',$start=0,$condition='')
{
	global $DB;
	$sql ="SELECT ms.*,count(CASE WHEN me.userid = mu.id THEN 1 END) as meetingcount,count(CASE WHEN me.meetingstatus = 1 THEN 1 END) as open,count(CASE WHEN (me.meetingstatus = 1  AND me.parentid=mu.id) THEN 1 END) as approved,count(CASE WHEN (me.meetingstatus = 2 AND me.parentid=mu.id) THEN 1 END) as rejected,mus.userid,mus.schoolid,mu.firstname,mu.lastname,mu.profilestatus,mu.policyagreed,me.name as title 	FROM {school} ms left join";
	$sql.="(select * from {user_school} where role='incharge') mus on mus.schoolid=ms.id left join {user} mu on mu.id=mus.userid left join (select * from {event} where eventtype='user' and parentid!=0 ) me on (mu.id=me.userid or mu.id=me.parentid)";
	$sql.=" WHERE mu.deleted=0 group by ms.id order by ms.id desc";
	$result = $DB->get_records_sql($sql);
	return $result;
}

//Mentor Report Your session: To get all Mentor report sessions.
function getAllSessions($limit='',$start=0,$condition='')
{
	global $DB;
	$addlimit='';
	if($limit)
		$addlimit = "limit $start,$limit";
	$sql = "SELECT msr.*,mu.id as mentorid,mu.email as mentoremail,CONCAT(mu.firstname,' ',mu.lastname) as mentorname,ms.name as schoolname,ms.id as schoolid,ms.atl_id as schoolatlid FROM `mdl_mentor_sessionrpt` msr join mdl_user mu on mu.id=msr.mentorid join mdl_school ms on msr.schoolid=ms.id WHERE mu.deleted=0 $condition $addlimit";
	$result = $DB->get_records_sql($sql);
	return ($result);
}
//Mentor Report Your session:
function getTotalSessionCount($condition='')
{
	global $DB;
	$sql = "SELECT count(*) as total FROM `mdl_mentor_sessionrpt` msr join mdl_user mu on mu.id=msr.mentorid join mdl_school ms on msr.schoolid=ms.id WHERE mu.deleted=0 $condition";
	$result = $DB->get_record_sql($sql);
	return $result->total;
}
//Mentor Report Your session:
function getSessionTiming($condition='')
{
	global $DB;
	$sql = "SELECT sum(totaltime) as total FROM `mdl_mentor_sessionrpt` where mentorid is not null $condition";
	$result = $DB->get_record_sql($sql);
	return $result->total;
}
function getNitiDashbordData($condition='')
{
	global $DB;
	$startedtut=0;
	$sql = "select count(mu.id) as totalmentor,count(CASE WHEN mu.policyagreed=1 THEN 1 END) as attemptedlogin,count(CASE WHEN mu.policyagreed=0 THEN 1 END) as notloggedin,count(CASE WHEN mu.profilestatus=1 THEN 1 END) as updatedprofile,count(CASE WHEN mu.profilestatus IS NULL THEN 1 END) as pendingprofile,count(CASE WHEN mud.scormstatus=1 THEN 1 END) as completedtut from mdl_user mu join mdl_user_info_data mud on mud.userid=mu.id where mu.deleted=0 and mu.msn=4 and mud.data='mentor data' $condition";
	$result = $DB->get_record_sql($sql);
	$conditionresult = getTutorialNotStartedMentor(); // Userid Not Started the Tutorial
	if($conditionresult)
		$userid = explode(', ', $conditionresult);
	$startedtut = count($userid);
	$result->startedtut = $startedtut;
	$sql = "SELECT sum(totaltime) as total FROM `mdl_mentor_sessionrpt` where mentorid is not null $condition";
	$sql = " SELECT sum(totaltime) as total,(SELECT SUM(totaltime) FROM mdl_mentor_sessionrpt WHERE sessiontype ='d' and mentorid is not null) onlinementor, (SELECT SUM(totaltime) FROM mdl_mentor_sessionrpt WHERE mentorid is not null and sessiontype in ('a','c','b') ) inperson FROM mdl_mentor_sessionrpt where mentorid is not null ";
	$mentorhr = $DB->get_record_sql($sql);
	if($mentorhr)
	{
		$result->totalmentorhrs = $mentorhr->total;
		$result->totalonlinementorhrs = $mentorhr->onlinementor;
		$result->totalinpersonmentorhrs = $mentorhr->inperson;
	}
	return $result;
}
