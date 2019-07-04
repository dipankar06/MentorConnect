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

/* @package: core_webservice
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 04-Dec-2018
 * @Description: Web services Local Internally used Library functions for atlinnonet
*/

//@return Array Object ["token":"fc2b9244c4812c96da18b9b2e6b1dd12","privatetoken":null]
function get_user_token($service,$systemcontext){
	global $CFG,$USER;
	// Get an existing token or create a new one.
    $token = external_generate_token_for_current_user($service);
    $privatetoken = $token->privatetoken;
    external_log_token_request($token);
    $siteadmin = has_capability('moodle/site:config', $systemcontext, $USER->id);
    $usertoken = new stdClass;
    $usertoken->token = $token->token;
    // Private token, only transmitted to https sites and non-admin users.
    if (is_https() and !$siteadmin) {
        $usertoken->privatetoken = $privatetoken;
    } else {
        $usertoken->privatetoken = null;
    }
    return $usertoken;
}

// Get UserId From Token
//@Return: 0 or userid ..important.
function validate_usertoken($token){
	$webservicelib = new webservice();
	$result = $webservicelib->get_user_ws_token($token);
	if(empty($result))
		return 0;
	else
		return $result->userid;
}


//@Return: User Profile array
function make_userprofilearray($user,$token){
	$array = array();
	$array['id']=$user->id;
	$array['firstname'] = $user->firstname;
	$array['lastname'] = $user->lastname;
	$array['email'] = $user->email;
	$array['policyagreed'] = $user->policyagreed;
	$array['userrole'] = get_atalrolenamebyid($user->msn);
	$array['profilepic'] = get_userprofilepic_url($user->id,$token);
	$array['mentorsessiontype'] = ($array['userrole']=='mentor')?getmentorsession_types():"";
	$array['assignedschool'] = get_userassigned_schools($user->id, $array['userrole']);
	return $array;
} 
function get_roleofmentorlink()
{
	global $CFG;
	$pdflink = get_atalvariables('roleofmentor');
	$pdflink = $CFG->wwwroot.$pdflink;
	$pdflink = array('link'=>$pdflink);
	return $pdflink;
}
function fetch_filelink($filetoload,$role='')
{
	global $CFG;
	$pdflink='';
	switch($filetoload)
	{
		case 'roleofmentor':
			$pdflink = get_atalvariables('roleofmentor');
			$pdflink = $CFG->wwwroot.$pdflink;
			$pdflink = array('link'=>$pdflink);
			break;
		case 'userguide':
			if($role!='mentor' && $role!='incharge' && $role!='student')
				return "invalidrole";
			$role = trim($role).'guide';
			$pdflink = get_atalvariables($role);
			$pdflink = $CFG->wwwroot.$pdflink;
			$pdflink = array('link'=>$pdflink);
			break;
	}
	return $pdflink;
}
function getdata_nitidashboard()
{
	$resultset = getNitiDashbordData();
	return $resultset;
}

//Common function: To Convert Object type Recordset from DB query to array
//@Params: object array $objectset
function convert_dataset_toarray($objectset){
	$result = array();
	foreach($objectset as $key=>$values){
		$result[] = $values;
	}
	return (array) $result;
}

//Properly arrange session list for Mobile APP
//@Params: object array $objectset
function arrange_sessionlist($objectset){
	$result = $myresult = array();
	$sessionarray = $sessions = array();
	
	foreach($objectset as $key=>$values){
		$sessionarray = array('id'=>$values->id,'mentorid'=>$values->mentorid,'schoolid'=>$values->schoolid,'schoolname'=>$values->schoolname,
		'dateofsession'=>$values->dateofsession,'starttime'=>$values->starttime,'endtime'=>$values->endtime,'sessiontype'=>$values->sessiontype,
		'sessiondate'=>date('d M y',$values->dateofsession));
		
		$month = date('My',$values->dateofsession);		
		$sessions[$values->mentorid][$month] = array('month'=>date('M y',$values->dateofsession),'sessiondetails'=>"");
		$sessiondetails[$values->mentorid][$month][] = $sessionarray;
		$result[$values->mentorid]['name'] = $values->mentorname;
		$result[$values->mentorid]['mentorid'] = $values->mentorid;
		$result[$values->mentorid]['sessions'] = array();
	}	
	if(count($sessions)>0){
		$tmp = array();
		foreach($sessions as $keys=>$values){
			foreach($values as $k=>$v1){
				$v1['sessiondetails'] = $sessiondetails[$keys][$k];
				$tmp[$keys][] = $v1;
			}
		}
		$sessions = $tmp;
		unset($tmp);
		foreach($sessions as $keys=>$values){
			$result[$keys]['sessions'] = $values;
		}
	}
	unset($sessions);	
	foreach($result as $key=>$values){
		$myresult[] = $values;
	}
	
	return $myresult;
}

//Common function: Gives Total Record Count in a table under a condition.
function get_tablerecordcount($tablename, $conditions){
	global $DB;
	$count = $DB->count_records($tablename, $conditions);
	return $count;
}

//To get list of mentor report session types
function getmentorsession_types(){
	global $CFG;
	require_once("$CFG->dirroot/mentor/lib.php");
	$types = getSessionType();
	$result = array();
	$i=0;
	foreach($types as $key=>$values){
		if($i>0){
			$myobj = new stdClass();
			$myobj->key = $key;
			$myobj->value = $values;
			$result[] = $myobj;
		}
		$i++;
	}
	return $result;
}

//Common function: Get user profile Pic Webserice URL
function get_userprofilepic_url($userid,$token){
	global $CFG;
	$context = context_user::instance($userid);
	$url = $CFG->wwwroot.'/webservice/pluginfile.php/'.$context->id.'/user/icon/f1?token='.$token;
	return $url;
}

//Get Uses assigned school
//@Return: array()
function get_userassigned_schools($userid, $userrole){
	global $DB;
	$result = array();
	$school = new stdClass();
	if($userrole=='admin'){
		return $result;
	} else{
		$sql="SELECT s.id,s.name FROM {user_school} us JOIN {school} s ON us.schoolid = s.id WHERE us.userid=".$userid;
		$data = $DB->get_records_sql($sql);
		if(count($data)>0){
			foreach($data as $key){
				$school->id = $key->id;
				$school->name = $key->name;
				$result[] = $school;
			}
		}
		return $result;
	}
}

//Common function: A megabyte is 1048576 bytes, 2MB = 2097152
function get_atlfilemanageroptions(){
	return array(
		'maxfiles' => 4,
		'maxbytes' => 2097152,
		'subdirs' => 0,
		'accepted_types' => 'jpeg,jpg,png'
	);
}

//Common function: to get Userrole
//@Params: Int $userid
function get_userrolefrom_id($userid){
	global $DB;
	$user = $DB->get_record('user',array('id'=>$userid),'id,msn');
	$userrole = get_atalrolenamebyid($user->msn);
	return $userrole;
}

//Get Mentor Report your session Details
//@Params: Int $sessionid
function get_mentor_sessiondetails($sessionid){
	global $DB,$CFG;
	$result = array();
	$picture_path = array();
	$recordset = $DB->get_record('mentor_sessionrpt', array('id'=>$sessionid));
	if(count($recordset)>0){
		$school = $DB->get_record('school', array('id'=>$recordset->schoolid), 'name,address');
		$result = array('id'=>$recordset->id,'mentorid'=>$recordset->mentorid,'schoolname'=>$school->name,
		'schooladdress'=>$school->address,'dateofsession'=>$recordset->dateofsession,'starttime'=>$recordset->starttime,
		'endtime'=>$recordset->endtime,'sessiontype'=>$recordset->sessiontype,'functiondetails'=>$recordset->functiondetails,
		'totalstudents'=>$recordset->totalstudents,'details'=>$recordset->details,'totaltime'=>$recordset->totaltime);
		//get images
		$context = context_user::instance($recordset->mentorid);
		$fs = get_file_storage();
		$files = $fs->get_area_files($context->id, 'mentorsession_file', 'files_1', $sessionid, "filename", false);
		foreach ($files as $file) {
			$path = '/' . $context->id . '/mentorsession_file/files_1/' .$file->get_itemid() . $file->get_filepath() . $file->get_filename();
			$url = file_encode_url("$CFG->wwwroot/pluginfile.php", $path, false);
			$picture_path[] = $url;
		}
		$result['pictures'] = $picture_path;
	}	
	return $result;
}

//To Get Project / Innovation List of a User.
//@Params: $userid INT, $userrole String, $type String (unapprove, ongoing(active), completed)
function get_user_enrollprojects($userid,$userrole,$type){
	global $DB, $CFG;
	require_once("$CFG->dirroot/project/lib.php");
	$atalvariable = get_atalvariables();
	$projectcat = $atalvariable['project_categoryid'];
	if($userrole!="mentor"){
		$schooldata = $DB->get_record('user_school', array('userid'=>$userid), null, 'id,schoolid');	
		if(count($schooldata)==0)
			$schoolid = 0;
		else
			$schoolid = $schooldata->schoolid;
	}	
	$projectarray = array();
	$result = array();
	if($userrole=='incharge'){
		//School InCharge;
		$sql = "SELECT FLOOR(RAND() * 401) + 100 AS id,p.id as projectid,p.idnumber,p.fullname as project,p.visible,u.id as userid,u.firstname,u.lastname,
		u.msn,p.startdate,p.enddate,p.completionnotify,p.createdby 
		FROM {course} p LEFT JOIN {user} u ON p.createdby=u.id WHERE p.idnumber = ? AND p.category = ? AND p.startdate>0 ORDER BY p.id desc";
		$result = $DB->get_records_sql($sql, array($schoolid,$projectcat));
	}
	elseif($userrole=='mentor'){
		$projectarray = mentorprojects_api($userid,$type);
	}
	else{
		//Student: show only those projects of his/her school which is assigned to him/her or created by him/her.
		$sql = "SELECT FLOOR(RAND() * 401) + 100 AS id,p.id as projectid,p.idnumber,p.fullname as project,p.visible,u.id as userid,u.firstname,u.lastname,
		u.msn,p.startdate,p.enddate,p.completionnotify,p.createdby 
		FROM {course} p JOIN(SELECT c.id FROM {course} c JOIN {enrol} e ON e.courseid=c.id JOIN {user_enrolments} ue ON e.id=ue.enrolid 
		WHERE e.enrol='manual' AND ue.userid=".$userid.") as mycourse ON p.id=mycourse.id LEFT JOIN {user} u ON p.createdby=u.id 
		WHERE p.idnumber = ? AND p.category = ? AND p.startdate>0 ORDER BY p.id desc";
		$result = $DB->get_records_sql($sql, array($schoolid,$projectcat));
	}	
	if(count($result)>0 && count($projectarray)==0){
		foreach($result as $key=>$values){
			$status = frmget_projectstatus($values);	
			$projectarray[] = array('id'=>$values->projectid,'name'=>$values->project,'status'=>$status,'startdate'=>$values->startdate,'enddate'=>$values->enddate);
		}
	}
	return $projectarray;
}
