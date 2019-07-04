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

/* @package: core_moodle
 * @CreatedBy: Jothi (IBM)
 * @CreatedOn: 18-07-2018
 * @Description: This file is Custom Library File for atlinnonet
*/

/*
* moodlelib_custom.php - Moodle Custom library File
* This file is included from lib/setup.php
* Functions Written in this File is accessible to whole application.
*/

defined('MOODLE_INTERNAL') || die();


//This function return RoleName by ID
function get_atalrolenamebyid($roleid){
    //Similar Function Written in /theme/moove/lib.php
    if($roleid==4){
        return 'mentor';
    } elseif ($roleid==5) {
        return 'student';
    } elseif ($roleid==1) {
        return 'admin'; //Niti Admin-manager
    } elseif ($roleid==3) {
        return 'incharge';
    } else{
        return 'guest';
    }
}

//** user-define functions added for Atal functunality.
//which returns an Array with , pre-define values.
//**At Production , update/check the values
function get_atalvariables($key=''){
    $array = array('sitecourse_idnumber'=>'id1',
    'sitecourse_name'=>'sitecourse',
	'project_categoryid'=>2,
	'forum_module'=>9,
	'coursecontext'=>50,
	'search_idincrementby'=>2000,
	'mentorschool_postcatgeoryid'=>4,
	'ongoingproject_postcatgeoryid'=>2, //project private chat
	'sitename'=>'Mentor Connect',
	'eventcourse_idnumber'=>'et1',
	'roleofmentor'=>'/mentorguides/Mentor StarterKit v2-3-ATL Mentor of Change.pdf#zoom=75', //Role of Mentor PDF File
	'mentorguide'=>'/mentorguides/mentor-user-guide.pdf#zoom=100', //Mentor How to Use Portal PDF Guide
	'inchargeguide'=>'/mentorguides/atalincharge-user-guide.pdf#zoom=100', //Atal School Incharge How to Use Portal PDF Guide
	'studentguide'=>'/mentorguides/student-user-guide.pdf#zoom=100' //Student How to Use Portal PDF Guide
	);
	if($key)
		$array = $array[$key];
    return $array;
}

//module name forum id = 9

/*This function will return roleid
 Role-id in Moodle..
 Teacher - 3 , Non-editing teacher - 4 , Manager - 1, Student - 5
*/
function atal_get_roleidbyname($rolename){
	//Similar Function Written in /theme/moove/lib.php
   $rolename = strtolower($rolename);
    if($rolename=='mentor'){
        return 4;
    } elseif ($rolename=='student') {
        return 5;
    } elseif ($rolename=='mentees') {
        return 5;
    } elseif ($rolename=='incharge') {
        return 3;
    } elseif ($rolename=='admin') {
        return 1; //Niti Admin-manager
    } elseif ($rolename=='teacher') {
        return 4; //mentor
    } else{
        return 6; //guest
    }
}

//** user-define functions added for Atal functunality.
//Return List of active students under a LoggedIn mentor.
function get_mystudentlist($uid){
    global $DB;	
	$studentroleid = atal_get_roleidbyname('student');
	$sql="SELECT u.id as userid,u.auth,u.username,u.firstname,u.lastname,u.msn,u.picture,s.name as school,s.id as schoolid,c.name as city,mentor.project
	FROM {user} u JOIN {user_school} us ON u.id=us.userid LEFT JOIN {school} s ON us.schoolid=s.id LEFT JOIN {city} c ON s.cityid=c.id
	JOIN (SELECT c.idnumber as id,c.id as courseid,c.shortname as project FROM {user} u JOIN {user_enrolments} ue ON u.id=ue.userid JOIN {enrol} e ON ue.enrolid=e.id
	JOIN {course} c ON e.courseid=c.id WHERE e.enrol='manual' AND u.id=".$uid." AND c.enddate=0 ) As mentor ON s.id=mentor.id
	WHERE u.msn=".$studentroleid." AND u.deleted=0 ORDER By u.firstname";
	$data = $DB->get_records_sql($sql);
	return $data;
}

//** user-define functions added for Atal functunality.
//Return List of active projects under a User.
function get_myactiveprojects($uid,$conditions=''){
	global $DB;
	$atalvariables = get_atalvariables();
	$projectcat = $atalvariables['project_categoryid'];
	$sql="SELECT c.id as id,c.shortname as project,s.name as school,c.createdby as createdbyuserid FROM {user} u JOIN {user_enrolments} ue ON u.id=ue.userid JOIN {enrol} e ON ue.enrolid=e.id
	JOIN {course} c ON e.courseid=c.id LEFT JOIN {school} s ON c.idnumber=s.id WHERE c.category=$projectcat AND e.enrol='manual' AND u.id=".$uid." 
	AND c.enddate=0 AND c.visible=1 $conditions ORDER BY s.name";	
	$data = $DB->get_records_sql($sql);
	return $data;
}

//** user-define functions added for Atal functunality.
//Return count of project under a user
function get_userprojectcount($uid,$userroleid){
	global $DB;
	$count = 0;
	$rolename = get_atalrolenamebyid($userroleid);
	$atalvariables = get_atalvariables();
	$projectcat = $atalvariables['project_categoryid'];
	if($rolename=='mentor'){
		$sql="SELECT count(c.id) as cnt FROM {user} u JOIN {user_enrolments} ue ON u.id=ue.userid JOIN {enrol} e ON ue.enrolid=e.id 
		JOIN {course} c ON e.courseid=c.id WHERE e.enrol='manual' AND u.id='".$uid."' AND c.startdate>0 AND c.category=$projectcat AND ue.status=1";
		$data = $DB->get_records_sql($sql);
		foreach($data as $key){
			$count = $key->cnt;
		}
	} elseif($rolename=='student'){
		$sql="SELECT count(c.id) as cnt FROM {user} u JOIN {user_enrolments} ue ON u.id=ue.userid JOIN {enrol} e ON ue.enrolid=e.id 
		JOIN {course} c ON e.courseid=c.id WHERE e.enrol='manual' AND u.id='".$uid."' AND c.startdate>0 AND c.category=$projectcat";
		$data = $DB->get_records_sql($sql);
		foreach($data as $key){
			$count = $key->cnt;
		}
	} else{
		//incharge
		$sql="SELECT count(c.id) as cnt FROM {course} c JOIN (SELECT schoolid FROM {user_school} WHERE userid = ".$uid.") as s ON c.idnumber=s.schoolid 
		AND c.startdate>0  AND c.category=$projectcat";
		$data = $DB->get_records_sql($sql);
		foreach($data as $key){
			$count = $key->cnt;
		}
	}
	return $count;
}

//** user-define functions added for Atal functunality.
//Return count of Award of a user
function get_userawardcount($uid,$userroleid){
	global $DB;
	$count = 0;
	$rolename = get_atalrolenamebyid($userroleid);
	$sql="SELECT count(b.id) as cnt FROM {badge_manual_award} b WHERE b.recipientid=".$uid;
	$data = $DB->get_records_sql($sql);
	foreach($data as $key){
		$count = $key->cnt;
	}
	return $count;
}

//** user-define functions added for Atal functunality.
//Return user School-Name
function get_userschoolname($userid){
	global $DB;
	$schoolname = '';
	$sql="SELECT s.name FROM {user_school} us JOIN {school} s ON us.schoolid = s.id WHERE us.userid=".$userid;
	$data = $DB->get_records_sql($sql);
	foreach($data as $key){
		$schoolname = $key->name;
	}
	return $schoolname;
}

//** user-define functions added for Atal functunality.
//based on optional_param()
//Return GET paramtere
function get_requestparam($parname) {
	
    if (isset($_GET[$parname])) {
        $param = $_GET[$parname];
    } else if (isset($_REQUEST[$parname])) {
        $param = $_REQUEST[$parname];
    } else {
        $param = '';
    }
    $param = urldecode($param);
    return $param;
}

//** user-define functions to Generate an Random String
//@Params integer string Length
//Return random Alphanumeric String
function generate_randomstring($length = 5) {
    $characters = '0123456789cdefghijklmnopqrstuvwxyzCDEFGHIJKLMNOPQRSUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

//** get User profile pic Only
//@Param object user, Int size of image
//return image tag
function get_userprofilepic($user,$size=35){
	global $OUTPUT;
	$avatar = new user_picture($user);
	$avatar->link = false;
	$avatar->size = $size;
	$aa = $OUTPUT->render($avatar);
	return $aa;
}

//** check is User is in this project
//@Param id INT projectid, Userid int
function is_myenrolproject($id, $userid){
	global $DB;
	$result = false;
	$sql="SELECT count(c.id) as cnt FROM {user} u JOIN {user_enrolments} ue ON u.id=ue.userid JOIN {enrol} e ON ue.enrolid=e.id 
	JOIN {course} c ON e.courseid=c.id  WHERE u.id=".$userid." AND e.enrol='manual' AND c.id=".$id;	
	$data = $DB->get_records_sql($sql);
	foreach($data as $k=>$val){
		if($val->cnt>0){
			$result = true;
		}
	}
	return $result;
}

//** get UserProfilePic with its detail page Link.
// @Param: User Object
// @Return: HTML image tag inside href tag.
function userpicbyobject($user){
	global $CFG;
	//$atalarray = get_atalvariables();
	//$incrementby = (int) $atalarray['search_idincrementby'];
	//$key = $user->id + $incrementby;
	$userurl = getuser_profilelink($user->id); //new moodle_url('/search/profile.php', array('key' => $key,'id'=>$user->id + 345));
	$usrimg = get_userprofilepic($user);
	$userlink ='<a href="'.$userurl.'">'.$usrimg.'</a>';
	return $userlink;
}

//** get User Profile Detail Page URL
//** User detail profile page is in Search module.
// @Param: Userid Integer
// @Return: String URL Link path only
function getuser_profilelink($userid){
	global $CFG;
	$key = encryptdecrypt_userid($userid);
	$userurl = new moodle_url('/search/profile.php', array('key' => $key));
	return $userurl;
}


//** Encrypt/Decrypt Userid (Mentor/Student/admin/inchargeid), for Ajax calls
// @Param: Userid Integer, Flag string en,de
// @Return: String encrpyt id or decrypt Int userid.
function encryptdecrypt_userid($userid,$flag='en'){
	if($flag=='en'){
		$num = generate_randomstring();
		$userid = $num.'A'.$userid;
		$userid = trim($userid);
		// Randomnumber-A-actualUserid
	} else{
		$tmp = explode("A",$userid);
		unset($userid);
		$userid = $tmp[1];
	}
	return $userid;
}

//** Encrypt/Decrypt ProjectId, for Ajax calls
// @Param: Projectid Integer, Flag string en,de
// @Return: String encrpyt id or decrypt Int Projectid.
function encryptdecrypt_projectid($id,$flag='en'){
	$num = generate_randomstring();
	$atalarray = get_atalvariables();
	$incrementby = (int) $atalarray['search_idincrementby'];
	if($flag=='en'){
		$id = $id + $incrementby;
		$id = $num.'T'.$id;
		$id = trim($id);
		// Randomnumber-T-actualProjectid
	} else{
		//Decrypt
		$tmp = explode("T",$id);
		unset($id);
		$id = $tmp[1];
		$id = abs((int)($id - $incrementby));
	}
	return $id;
}

/* Function to Fetch User Details based on the User Id
 * @params : userid
 *Return : User Array
 @CreatedBy: Jothi (IBM)
*/
function atal_getUserDetailsbyId($userid){
	global $DB;
	$result = array();
	$result = $DB->get_record('user', array('id'=>$userid));
	if(count($result)>0){
		return $result;	
	}
}

/* Function to Delete Course/Project Tags from tag_project table
 * Also additional new tables & Records if any..
 * @params : courseid
 *Return : Boolean
 @CreatedBy: Dipankar (IBM)
*/
function atal_deletetags($courseid){
	global $DB;
	$DB->delete_records("tag_project", array('projectid'=>$courseid));
	//Delete data from comments table.
	$DB->delete_records("course_comment", array('courseid'=>$courseid));
	return true;
}

/* Function to fetch states in ATAL Portal
*Return : object
@CreatedBy: Jothi (IBM)
*/
function get_atal_allstates(){
	global $DB;
	//get state values without where clause and order by name
	$resultset = $DB->get_records('state',array(),'name asc');
	return $resultset;
}

/* Function to fetch citys in ATAL Portal
* params : stateid
*Return : object
@CreatedBy: Jothi (IBM)
*/
function get_atal_citybystateid($stateid){
	global $DB;
	$resultset = $DB->get_records('city',array('stateid' =>$stateid),'name');
	return $resultset;
}

/* Function to fetch State details in ATAL Portal
* params : stateid
*Return : object
@CreatedBy: Jothi (IBM)
*/
function get_atal_statebystateid($stateid){
	global $DB;
	$resultset = $DB->get_record('state',array('id' =>$stateid),'name');
	return $resultset;
}
/* Function to fetch city details in ATAL Portal
* params : cityid
*Return : object
@CreatedBy: Jothi (IBM)
*/
function get_atal_citybycityid($cityid){
	global $DB;
	$resultset = $DB->get_records('city',array('id' =>$cityid),'name');
	return $resultset;
}
/* Fucntion to get the language list 
* Params : Null
* Return  : Language Array
@CreatedBy : Jothi (IBM)
*/
function get_languages() {
	$language =  array('1'=>'Assamese','2'=>'Bengali','3'=>'Bodo','4'=>'Dogri','5'=>'English',"6"=>"Gujarati","7"=>"Hindi","8"=>"Kannada","9"=>"Kashmiri",
	"10"=>"Konkani","11"=>"Maithili","12"=>"Malayalam","13"=>"Marathi","14"=>"Meitei (Manipuri)","15"=>"Nepali","16"=>"Odia","17"=>"Punjabi","18"=>"Sanskrit",
	"19"=>"Santali","20"=>"Sindhi","21"=>"Tamil","22"=>"Telugu","23"=>"Urdu");
	return $language;
}
 
/* Fucntion to get the Time Commitment Array List 
* Params : Null
* Return  :  Array
@CreatedBy : Jothi (IBM)
*/
function get_timecommit(){
	$timecommit = array('1 -2 hrs per week'=>'1 -2 hrs per week','2 – 4 hrs per week'=>'2 – 4 hrs per week','4 – 6 hrs per week'=>'4 – 6 hrs per week',
	'6+ hrs per week'=>'6+ hrs per week');
	return $timecommit ;
}
 
//Return Ongoing project collaboration(Mentor & Student project chats) Forum categoryid
function getproject_forumcatgeoryid(){
	$atalvariable = get_atalvariables();
	return $atalvariable['ongoingproject_postcatgeoryid'];
}
 
//List of Forum Misuse/Spam types
function get_misusetypes(){
    $array = array(
	'1'=>'Misleading or Spam',
	'2'=>'Adult content',
	'3'=>'Promotes violent or Repulsive content',
	'4'=>'Hateful or Abusive content',
	'5'=>'Child abuse'
	);
    return $array;
}

//Record Project Delete and Reject reason into a Log Table for Reporting purpose
//We are not deleting any thing from course Log.
//@Params: courseorid: Integer or Object , reason: string (delete/reject), msg: String message
function record_projectlog($courseorid,$reason,$msg){
	global $DB;
	if (is_object($courseorid)) {
        $courseid = $courseorid->id;
        $course   = $courseorid;
    } else {
        $courseid = $courseorid;
        if (!$course = $DB->get_record('course', array('id' => $courseid))) {
            return false;
        }
    }
	if(!empty($courseid) && !empty($reason)){
		$cm = new stdClass();
		if($reason=="delete"){
			$cm->deletereason = $msg;
			$cm->isdeleted = 1;
			$cm->deletedate = time();
		} else{
			$cm->rejectreason = $msg;
			$cm->isrejected = 1;
			$cm->rejectdate = time();
		}
		$log = $DB->get_records('course_log',array('courseid'=>$courseid),'id');		
		if(count($log)>0){
			//Update
			foreach($log as $k1=>$val1){
				$cm->id = $val1->id;
			}
			$DB->update_record('course_log', $cm);
		} else{
			//Insert
			$cm->courseid = $course->id;
			$cm->name = $course->fullname;
			$cm->schoolid  = $course->idnumber;
			$cm->createdby = $course->createdby;
			$cm->createdate = $course->timecreated;
			$DB->insert_record('course_log', $cm);
		}
	}
	return true;
}
/*
* Function to send welcome email to the newly created users
* Params : Roleid, Userid, Name, Email, Username, Password
*/
function send_welcomemail($roleid,$userid,$name,$email,$username,$password)
{
	global $DB,$CFG;
	$name = ucwords(strtolower($name));
	$file= $CFG->wwwroot.'/atalfeatures/mailtemplate.json';
	$jsonString = file_get_contents($file);
	$data = json_decode($jsonString, true);
	$mailbody='';
	switch ($roleid) {
    case "5":
       		$mailbody =$data['student']; 
			$mailbody = str_replace("&lt;name&gt;",$name,$mailbody);
			$mailbody = str_replace("&lt;username&gt;",$username,$mailbody);
			$mailbody = str_replace("&lt;password&gt;",$password,$mailbody);
        break;
    case "3":
		$sql = "select name from {school} s join {user_school} us on us.schoolid=s.id where us.userid=".$userid;
		$result = $DB->get_record_sql($sql);
		$schoolname='';
		if($result)
			$schoolname = $result->name;
		if($schoolname)
		{
			$schoolname = ucwords(strtolower($schoolname));
			$mailbody =$data['school']; 
			$mailbody = str_replace("&lt;schoolname&gt;",$schoolname,$mailbody);
			$mailbody = str_replace("&lt;username&gt;",$username,$mailbody);
			$mailbody = str_replace("&lt;password&gt;",$password,$mailbody);
		}
        break;
    case "4":
			$mailbody =$data['mentor']; 
			$mailbody = str_replace("&lt;name&gt;",$name,$mailbody);
			$mailbody = str_replace("&lt;username&gt;",$username,$mailbody);
			$mailbody = str_replace("&lt;password&gt;",$password,$mailbody);
        break;
    default:
			$mailbody =$data['guest']; 
			$mailbody = str_replace("&lt;name&gt;",$name,$mailbody);
			$mailbody = str_replace("&lt;username&gt;",$username,$mailbody);
			$mailbody = str_replace("&lt;password&gt;",$password,$mailbody);
	}	
	//Script to Send Mail
	$from = portal_fromemail();
	$subject='Welcome to AtlInnonet';
	$useremail = new StdClass();
	$useremail->email = $email ;
	$useremail->id = $userid;
	$useremail->deleted =0;
	//email_to_user($useremail, $from,$subject,$mailbody); 
	return true;
}

function portal_fromemail(){
	return "atlinno.net-niti@gov.in";
}

//To sent Mentor Schedule Meeting invitation emails to Atal Chief & Mentors
//@CreatedOn: 4-June-2018
function frmsent_meetingmail($event,$flag){
	global $DB,$USER,$SESSION,$CFG;
	$userrole = get_atalrolenamebyid($USER->msn);
	$tomentorschool = "";
	$message = "";
	if($userrole=="incharge"){
		//LoggedIn user is an AtalChief, get School details
		$schoolid = $SESSION->schoolid;
		$school = $DB->get_record('school',array('id'=>$schoolid),'name,address');
	}else{
		//LoggedIn user is mentor.
		if($USER->id==$event->userid){
			//this event created by mentor
			$inchargeid = $event->parentid;
		} else{
			//this event created for mentor
			$inchargeid = $event->userid;
		}
		$data = $DB->get_record('user_school',array('userid'=>$inchargeid),'schoolid');
		$school = $DB->get_record('school',array('id'=>$data->schoolid),'name,address');
	}
	if($flag=='new'){
		$touser = $DB->get_record('user',array('id'=>$event->parentid));
	} else{
		$touser = $DB->get_record('user',array('id'=>$event->userid));
	}
	$tomentorschool = ($userrole=="incharge")? $touser->firstname." ".$touser->lastname : $school->name;
	$tomentorschool = ucwords($tomentorschool);
	$from = portal_fromemail();
	$subject ="ATL Mentor of Change Session Invitation";
	$meetingdate = date('d-F-Y gA',$event->timestart);
	if(isset($event->meetingstatus)){
		//Update event
		if($event->meetingstatus==1)
			$status = "Approved";
		if($event->meetingstatus==2)
			$status = "Rejected";
		else
			$status = "Still open";
		$message = "Meeting ".$event->name." schedule for ".$meetingdate." by ".$school->name." is ".$status;
		$message ="<p>".$message."</p>";
	} else{
		//New Event
		if($userrole=="incharge"){
			//Dear <Mentor Name>
			$message ="The Atal Tinkering Lab in ".$school->name." wants to schedule a mentor session on ".$meetingdate ;
			$message.=". Please log in to ATL Innonet (".$CFG->wwwroot.") and go to Schedule Your Session to accept or reject the session schedule proposed by ".$school->name ;
		} else{
			//Dear <School Name>
			$message ="The Mentor of Change - ".$USER->firstname." ".$USER->lastname." for the Atal Tinkering Lab in your school wants to schedule a mentor session on ".$meetingdate ;
			$message.=". Please log in to ATL Innonet (".$CFG->wwwroot.") and go to Schedule a Mentor Session to accept or reject the session schedule proposed by ".$USER->firstname." ".$USER->lastname ;
		}
		$message ="<p>".$message."</p>";
		if(!empty($event->description)){
			$message.="<p>".strip_tags($event->description)."</p>";
		}
	}

	$mailbody ="
	Dear ".$tomentorschool.",
	".$message."<p>Disclaimer: This is an auto-generated email. Please do not reply to this email.</p>";

	email_to_user($touser, $from,$subject, $mailbody);
	return true;
}
function getUserMeetingStatus($index)
{
	$status = array("Open","Approved by the Assignee","Rejected by the Assignee","Meeting Successfully Completed","Meeting Doesn't Happened");
	if(isset($status[$index]))
		return $status[$index];
	else
		return "Invalid";
}
function getSchools_Dropdown($city='')
{
	global $DB;
	$sql = "SELECT s.id,s.name,c.name as city FROM {school} s JOIN {city} c ON s.cityid=c.id ORDER BY s.name";
	$schdata = $DB->get_records_sql($sql);
	$schooloption = array();
	$i=0;
	if(count($schdata)>0){
		foreach($schdata as $key=>$values){
			$schooloption[$i]['val'] = $values->id;
			$schooloption[$i]['txt'] = $values->name.' - '.$values->city;
			$i++;
		}
	}
	return $schooloption;
}
function getBulkMailReport_Dropdown($val='')
{
	if($val=="mentor")
		$dropdown_array = array("no_mentor"=>"Send to All","mnotlogged"=>"Not Logged In","mnotupdated"=>"Not Updated Profile","mnotstartedtu"=>"Not Started Training Tutorial","mnotcompletetu"=>"Not Completed Training Tutorial","mnotsentinvi"=>"Not Send Session Request");
	else if($val=="incharge")
		$dropdown_array = array("no_school"=>"Send to All","snotlogged"=>"Not Logged In","snotupdated"=>"Not Updated Profile");
	else 
		$dropdown_array = array("no_aschool"=>"Send to All","asnotlogged"=>"Not Logged In","asnotupdated"=>"Not Updated Profile");
	return $dropdown_array;
}
function getCustomSettings($key='')
{
	global $DB;
	if($key)
		$result = $DB->get_record('custom_settings',array('atl_key'=>$key));
	else
		$result = $DB->get_records('custom_settings');
	return $result;
}
function getPercentage($value=0,$total=0)
{
	$perentage = ($value/$total)*100;
	return round(floatval($perentage), 2)."%";
}
function stateForMoodleForm()
{
	$state = array();
	$state_records = get_atal_allstates();
	$state['select_state']='Select State';
	foreach($state_records as $key=>$values)
	{
		$state[$key] = $values->name;
	}
	return $state;
}
function getCityIdbyCityname($cityname,$stateid)
{
	global $DB;
	$result = $DB->get_record('city',array('name'=>$cityname,'stateid'=>$stateid));
	if($result)
		return $result->id;
	else
		return 0;
}

/* Temporary Function to check wheather LoggedIn user is a mentor.
* Portal Re-Design Stratagy, to Deactivate Schools till April-2019
* @CreatedBy: Dipankar (IBM), 14-Nov-18
* @ This function is called from \moodlelib.php\authenticate_user_login()	
*/
function redesignIsMentor($user){
	global $CFG;
	$role = get_atalrolenamebyid($user->msn);
	if($role=="incharge" || $role=="student"){
		//Portal is DeActivated for Schools & Students Till April 2019
		$flag = ($role=="incharge")?1:2;
		redirect(new moodle_url('/login/index.php?flag='.$flag));
	}
	return true;
}
