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
 * @CreatedOn: 29-Aug-2018
 * @Description: Web services External Library functions for atlinnonet
*/

// *** All WEB SERVICE Functions for ATAL should be Prefix by "atl_"

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/externallib.php');
require_once("$CFG->dirroot/webservice/lib.php");
require_once("$CFG->dirroot/webservice/atllocallib.php");
require_once("$CFG->dirroot/nitiadmin/lib.php");
/*
* Parameter :  {'action':'register_mentor','data':data}
*/
function handleServices($params){
	$paramObj = json_encode($params);
	switch($paramObj->action){		
	}
}

//@WebService Function: give site name
//@Changes: 30-Nov-2018 ..By ATL Dev
function atl_getmyname($params){
	return "ATL Innonet Platform";
} 
 
//@WebService Function: Get user Token
//@param array $params the parameters of the function
//@return Array() ... code taken from /login/token.php
function atl_getusertoken($params){
	global $DB,$CFG;
	$username = $params['username'];
	$password = $params['password'];
	$serviceshortname = $params['service'];
	$error = "";
	$token = "";
	if (!$CFG->enablewebservices) {
		return array("errorflag"=>'1',"msg"=>"WebService is Not Enabled in server!");
	}
	$service = $DB->get_record('external_services', array('shortname' => $serviceshortname, 'enabled' => 1));
	if (empty($service)) {
        return array("errorflag"=>'1',"msg"=>"External Service not available!");
    }
	$username = trim($username);
	$userdata = $result = $DB->get_record('user', array('username'=>$username), 'id,msn');
	if(empty($userdata)){
		return array("errorflag"=>'1',"msg"=>"Username Not Exists!");
	} else{
		if(isset($userdata->msn)){
			//First check is this a Mentor or admin..ReDesign Strategy allow only Mentors..
			$userrole = get_atalrolenamebyid($userdata->msn);
			if($userrole=='student' || $userrole=='incharge'){
				//return array("errorflag"=>'1',"msg"=>"Students and Schools are deactivated by AIM!");
			}
		} else{
			return array("errorflag"=>'1',"msg"=>"User is disabled!");
		}
	}
	$systemcontext = context_system::instance();
	$user = authenticate_user_login($username, $password);
	if(!empty($user)){
		if (isguestuser($user)) {
			$error = "Guest users are not allowed!";
		}
		if (empty($user->confirmed)) {
			$error = "This User is not confirmed!";
		}
		// setup user session to check capability
		\core\session\manager::set_user($user);
	} else{
		$error = "Invalid Username or Password!";
	}
	if(!empty($error)){
		$token = array("errorflag"=>'1',"msg"=>$error);
	} else{
		$token = get_user_token($service,$systemcontext);
		$token->errorflag = '0';
	}	
	$params['usertoken'] = $token->token;
	$token->userdata = atl_getuserprofile($params);
	return $token;
}

//@WebService Function: Get user Profile
//@param array $params the parameters of the function
//@return Array()
function atl_getuserprofile($params){
	global $DB;
	$token = $params['usertoken'];
	$userid = validate_usertoken($token);
	if(!empty($userid)){
		$result = $DB->get_record('user', array('id'=>$userid), '*');
		$result = make_userprofilearray($result,$token);
	} else{
		$result = array("errorflag"=>'1',"msg"=>'Invalid Token!');
	}
	return $result;
}

function atl_getroleofmentor($params){
	global $DB;
	$token = $params['usertoken'];
	$userid = validate_usertoken($token);
	if(!empty($userid)){
		$result = $DB->get_record('user', array('id'=>$userid), '*');
		$result = get_roleofmentorlink();
	} else{
		$result = array("errorflag"=>'1',"msg"=>'Invalid Token!');
	}
	return $result;
}

function atl_getfilelink($params)
{
	global $DB;
	$token = $params['usertoken'];
	$userid = validate_usertoken($token);
	if(!empty($userid)){
		$result = $DB->get_record('user', array('id'=>$userid), '*');
		switch($params['rf'])
		{
			case 'roleofmentor':
				$result = fetch_filelink($params['rf']);
				break;
			case 'userguide':
				$result = fetch_filelink($params['rf'],$params['userrole']);
				if($result=='invalidrole')
					$result = array("errorflag"=>'1',"msg"=>'Invalid Role!');
				break;
		}
	} else{
		$result = array("errorflag"=>'1',"msg"=>'Invalid Token!');
	}
	return $result;	
}

function atl_niti_mentor_db($params)
{
	global $DB;
	$token = $params['usertoken'];
	$userid = validate_usertoken($token);
	$data = new stdClass();
	$data->errorflag = '0';
	if(!empty($userid)){
		$result = getdata_nitidashboard();		
		$data->data = $result;
		$result = $data;
	} else{
		$result = array("errorflag"=>'1',"msg"=>'Invalid Token!');
	}
	return $result;	
}

/*@WebService Function: Save Mentor Report Your Session
@param array $params
@return Array()
*/
function atl_reportmentorsession($params){
	global $CFG;
	require_once("$CFG->dirroot/mentor/lib.php");
	return addupdate_mentorsession($params);
}

//@WebService Function: To show List of mentor's session
//common for nitiadmin & mentor
function atl_getmentorrptsessions($params){
	global $DB, $CFG;
	require_once("$CFG->dirroot/mentor/lib.php");
	$userid = validate_usertoken($params['usertoken']);
	if(!empty($userid)){
		$user = $DB->get_record('user',array('id'=>$userid),'id,msn');
		$userrole = get_atalrolenamebyid($user->msn);
		$condition = ($userrole=='admin')?null:array('mentorid'=>$userid);
		$total_records = get_tablerecordcount('mentor_sessionrpt', $condition);
		$condition = ($userrole=='admin')?" ORDER BY msr.id DESC":" AND msr.mentorid=".$userid." ORDER BY msr.id DESC";
		$result = get_allmysession($params['limit'],$params['start'],$condition);		
		if(count($result)==0){
			$result = array("errorflag"=>'1',"msg"=>"No Records Found");
		} else{
			$data = arrange_sessionlist($result);			
			$result = array("errorflag"=>'0',"totalrecords"=>$total_records,"data"=>$data);
		}
	} else{
		$result = array("errorflag"=>'1',"msg"=>"Invalid Token!");
	}
	return $result;
}

//@WebService Function: To get a Mentor RptSession Details
//common for nitiadmin & mentor
function atl_rptsessiondetail($params){	
	$userid = validate_usertoken($params['usertoken']);	
	if(!empty($userid)){	
		$data = get_mentor_sessiondetails($params['sessionid']);
		if(count($data)==0){
			$result = array("errorflag"=>'1',"msg"=>"No Records Found");
		} else{
			$result = array("errorflag"=>'0', "data"=>$data);
		}
	} else{
		$result = array("errorflag"=>'1',"msg"=>"Invalid Token!");
	}
	return $result;
}

//@WebService Function: To get OnGoing(active) Innovation Lists of a User.
//Common for all
function atl_user_ongoingprojects($params){
	$userid = validate_usertoken($params['usertoken']);
	if(!empty($userid)){
		$data = get_user_enrollprojects($userid,$params['userrole'],'active');
		if(count($data)==0){
			$result = array("errorflag"=>'1',"msg"=>"No Records Found");
		} else{
			$result = array("errorflag"=>'0',"totalrecords"=>count($data), "data"=>$data);
		}
	} else{
		$result = array("errorflag"=>'1',"msg"=>"Invalid Token!");
	}
	return $result;
}
