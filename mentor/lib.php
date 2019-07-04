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

/* @package: core_mentor
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 16-11-2018
 * @Description: Library functions for Mentor New Module
 Lib file can be use for mentor & other modules
*/

//Report Your session:
function getSessionType($key=''){
	$sessiontype = array(''=>'Select Session Type',
	'a'=>'In-Person visit - Meeting school officials only',
	'b'=>'In-Person visit - Own mentoring sessions with students',
	'c'=>'In-Person visit - Attend School function',
	'd'=>'Online - Video Conference session with students');
	if($key)
		return $sessiontype[$key];
	else
		return $sessiontype;
}

//Add Mentor Session Feedback i.e Report Your Session at School.
//@Created: 19-Nov-18 By Dipankar
//@Params: $data object array, Form Post data
function addUpdateSessionfeedback($data){
	global $USER, $DB, $CFG;
	// Insert,Update a record
	try {
		// From this point we make database changes, so start transaction.
		$transaction = $DB->start_delegated_transaction();
		$cm = make_rptobjectarray($data,$USER->id);
		if($data->flag=="edit"){
			$cm->id = $data->id;
			$DB->update_record('mentor_sessionrpt', $cm);
		} else{
			$cm->id = $DB->insert_record('mentor_sessionrpt', $cm);
		}
		//Add file.
		$file_id = 1;
		$post = new stdClass();
		$post->attachments = $data->rptmentorfiles;
		$post->id = $cm->id;	
		$info = file_get_draft_area_info($post->attachments);
		$present = ($info['filecount']>0) ? '1' : '';
		if($present=='1'){
			$usercontext = context_user::instance($USER->id, MUST_EXIST);	
			file_save_draft_area_files($post->attachments, $usercontext->id, 'mentorsession_file', "files_{$file_id}", $cm->id, get_session_filemanageroptions());		
		}
		$transaction->allow_commit(); //close transaction
		return true;
	} catch(Exception $e) {
		$transaction->rollback($e);
		return false;
	}
}

function make_rptobjectarray($data,$userid){
	$cm = new stdClass();
	$cm->mentorid = $userid;
	$cm->schoolid = $data->schoolid;
	$cm->dateofsession = $data->dateofsession;
	$cm->starttime = $data->starttime;
	$cm->endtime = convertEndtime($data->endtime);
	$cm->sessiontype = $data->sessiontype;
	$cm->functiondetails = (isset($data->schoolfunction))?$data->schoolfunction:NULL;
	$cm->totalstudents = $data->totalstudents;
	$cm->details = $data->details;
	$cm->timecreated = time();
	$cm->declarationagree = $data->declarationagree;
	$cm->totaltime = calc_timediff_New($data->starttime,$data->endtime);
	$cm->attachments  = 1;
	$cm->dateofsession_date = date('d-m-Y',$data->dateofsession);
	return $cm;
}

//Report Your session:
function convertEndtime($endtime){
	$tmp = explode("-",$endtime);
	$tmp[0] = get_iso_convertion($tmp[0],'m');
	return implode("-",$tmp);
}

//Format session times to 00:00 AM
//@params String. 6-1-AM. @Return: 6:01 AM
function format_timeforReport($time){
	$tmp = explode("-",$time);
	$hrs = $tmp[0];
	if($hrs>12)
		$hrs = get_iso_convertion($hrs,'m');
	$mins = ($tmp[1]<10)?'0'.$tmp[1]:$tmp[1];
	return $hrs.':'.$mins.' '.$tmp[2];
}

//Validation for Report Your Session Form start date & time
function validateFormSessionDate($formdata){
	$daylimit = getMaxdays_forsessionreport();
	$daylimit--;
	if(checkFutureDate($formdata['dateofsession'])===true){
		return array('0'=>'error','1'=>'Error, You cannot report a session for future dates');
	}
	//Will be activated after launch of this form..
	if(checkBeforeDec($formdata['dateofsession'])===true){
		return array('0'=>'error','1'=>'Error, You cannot select date of session before 1st Dec 2018');
	}
	//if(checkSessionDaterpt($formdata['dateofsession'],$formdata['dateofsession_date'],$formdata['id'])===false){
		// 7-days Limit of date of Session....will be activated after 19Jan-2019
		//return array('0'=>'error','1'=>'You cannot report a session held more then '.$daylimit.' days ago');		
	//}
	if(checkDuplicateSession($formdata['dateofsession'],$formdata['id'])===true){
		return array('0'=>'error','1'=>'Error, You cannot report 2 sessions on the same day');
	}
	$check_time = checkSessionTimerpt($formdata);
	if($check_time[0]===false){
		return array('0'=>'error','1'=>$check_time[1]);
	}
	return array('0'=>'ok','1'=>'ok');
}

//Report Your session:
//A validation, a mentor should not be able to report a session on a date more than 7 days earlier the present date
function getMaxdays_forsessionreport(){
	return 8; //atl_key = mentorsessionrpt_datediff
}

//To Check the Session Report date for: mentor Report your session form.
//@Params date in UTC
// date('d.m.Y',strtotime("-7 days")); o/p 13.11.2018
function checkSessionDaterpt($date_utc,$datestr,$id){
	if($id!==-1){
		//Edit mode.
		$dateofsession_date = date('d-m-Y',$date_utc);
		if($dateofsession_date == $datestr){
			//no change in session date
			return true;
		} else{
			return check_maxsessiondate($date_utc);
		}
	} else{
		//Add new mode.
		return check_maxsessiondate($date_utc);
	}
}

function check_maxsessiondate($date_utc){
	$daylimit = getMaxdays_forsessionreport();
	$rptday = $daylimit - 1;
	$rptday = "-".$rptday." days";
	$last7day = date('d.m.Y H:i:s',strtotime($rptday)); //o/p 13.11.2018 17:28:14
	$last7day = strtotime($last7day); //o/p 1542110294
	$lastdate = date('d.m.Y H:i:s',$last7day); //o/p 13.11.2018 17:28:14
	$result = ($date_utc <= $last7day)?false:true;
	return $result;
}

//Report Your session: You cannot report 2 sessions on the same day
//@Params date in UTC
function checkDuplicateSession($date_utc,$sessionid){
	global $DB,$USER;
	$sessionDate = date('d-m-Y',$date_utc);
	if($sessionid!==-1){
		$cnt = 0;
		$sdate = date('d-m-Y',$date_utc);
		$result = $DB->get_records_sql('SELECT count(*) as cnt FROM {mentor_sessionrpt} WHERE mentorid=? AND dateofsession_date = ? AND id != ?', array($USER->id,$sdate,$sessionid));
		foreach($result as $k=>$val){
			$cnt = $val->cnt;
		}
		$result = ($cnt>0)?1:0;
	} else{
		$result = $DB->record_exists('mentor_sessionrpt', array('mentorid'=>$USER->id,'dateofsession_date'=>$sessionDate));
	}
	return ($result==1)?true:false;
}

//Report Your session: validate Start & End Time of sessions.
//@Params Form data as Array
//@Default Format of time-data: starttime: 0-0-AM , endtime: 0-0-AM
function checkSessionTimerpt($data){
	$return = array('0'=>true,'1'=>'');
	$tmpstart = explode("-",$data['starttime']);
	$tmpend = explode("-",$data['endtime']);
	if($tmpstart[2]=="PM" && $tmpend[2]=="AM"){
		$return = array('0'=>false,'1'=>'Error, Check AM PM options');
	} else{
		$sessionTotaltime = validate_sessionTime($data['starttime'],$data['endtime']);
		if($sessionTotaltime == 0){
			$return = array('0'=>false,'1'=>'Error, Start and End Time should between 6AM - 10PM');
		}
		if($sessionTotaltime > 8){
			$return = array('0'=>false,'1'=>'Error, total session time should not be more then 8hrs');
		}
	}
	return $return;
}

//Report Your session:
//@Param: INT, String. @return INT
//flag m = morning time i.e AM is selected w.r.t Start/end time
function get_iso_convertion($time,$flag='m'){
	if($flag=="m"){
		$timearray = array('13'=>'1','14'=>'2','15'=>'3','16'=>'4',
		'17'=>'5','18'=>'6','19'=>'7','20'=>'8','21'=>'9','22'=>'10','23'=>'11','24'=>'12');
	} else{
		$timearray = array('1'=>'13','2'=>'14','3'=>'15','4'=>'16','5'=>'17','6'=>'18',
		'7'=>'19','8'=>'20','9'=>'21','10'=>'22','11'=>'23','12'=>'24');
	}
	return $timearray[$time];
}

//Report Your session: Do validation on Start & end Time Then call Time difference
//@params starttime , endtime as STRING
//@Default Format: starttime: 0-0-AM , endtime: 0-0-AM
//@Return Total Number of hrs
function validate_sessionTime($starttime,$endtime){
	$totalhrs =0;
	$tmpstart = explode("-",$starttime);
	$tmpend = explode("-",$endtime);
	if($tmpend[2]=="PM"){
		//session should not exceed after 10PM.
		if($tmpend[0]==23)
			return 0;
		if($tmpend[0]==22 && $tmpend[1]>0)
			return 0;
		//should not take 10:01PM
	}
	if($tmpend[2]=="AM"){
		//session should start at 12AM
		if($tmpend[0]==24)
			return 0;
	}
	if($tmpstart[2]=="AM"){
		//session should not start before 6AM.
		if($tmpstart[0]<6)
			return 0;
	}
	if($tmpstart[2]=="AM" && $tmpend[2]=="AM"){
		//user select startTime between 12 AM & endtime 10 AM
		$time_iso = get_iso_convertion($tmpend[0],'m');
		if($tmpstart[0] > $time_iso){
			return 0;
		}
	}
	if($tmpstart[2]=="PM" && $tmpend[2]=="PM"){
		$sttime_iso = get_iso_convertion($tmpstart[0],'e');
		if($tmpstart[0]<12){
			//startTime between 8 PM & End time 1 PM
			if($tmpend[0] < $sttime_iso){
				return 0;
			}
		}
		if($tmpend[0]>22 && $tmpend[0]<25){
			//Not take after 10 to 12 PM
			return 0;
		}
	}
	if($tmpstart[2]=="PM" && $tmpend[2]=="AM"){
		$totalhrs = 0;
	} else{
		$totalhrs = calculate_timediff($starttime,$endtime);
	}
	return $totalhrs;
}

//Report Your session: To Calculate TOTAL Session Time in Hrs: Report your session
//@params starttime , endtime as STRING
//@Default Format: starttime: 0-0-AM , endtime: 0-0-AM
//mktime (hour,minute,second,month,day,year)
function calculate_timediff($starttime,$endtime){
	$totalhrs =0;
	$currentyear = date("Y");
	$year = $currentyear - 1;
	$tmpstart = explode("-",$starttime);
	$tmpend = explode("-",$endtime);
	$time_start = $tmpstart[0];
	$time_end = $tmpend[0];
	if($tmpend[2]=="AM")
		$time_end = get_iso_convertion($tmpend[0]);
	if($tmpstart[2]=="PM"){
		if($tmpstart[0]!=12)
			$time_start = get_iso_convertion($tmpstart[0],'e');
	}
	if($tmpend[2]=="PM"){
		if($tmpend[0]==24)
			$time_end = get_iso_convertion($tmpend[0],'m');
	}
	$starttimestamp = mktime($time_start, $tmpstart[1], 0, 12, 12, $year);
	$endtimestamp = mktime($time_end, $tmpend[1], 0, 12, 12, $year);
	$difference = abs($endtimestamp - $starttimestamp)/3600;
	$totalhrs = number_format($difference, 2, '.', '');
	return $totalhrs;
}

//Report Your session:To check weather sessionid belongs to LoggedIn mentor.
function check_isMentorSession($sessionid){
	global $DB,$USER;
	$result = $DB->record_exists('mentor_sessionrpt', array('id'=>$sessionid,'mentorid'=>$USER->id));
	return ($result==1)?true:false;
}

//Report Your session:
function get_file_elementname(){
	return 'rptmentorfiles';
}

// A megabyte is 1048576 bytes, 2MB = 2097152
function get_session_filemanageroptions(){
	return array(
		'maxfiles' => 4,
		'maxbytes' => 2097152,
		'subdirs' => 0,
		'accepted_types' => 'jpeg,jpg,png'
	);
}

//Report Your session: This Function called from filelib.php file_pluginfile();
//@file_pluginfile(), is use to display,download a file..
function show_mentorsessionfiles($course, $cm, context $context, $filearea, $args, $forcedownload) {
    global $DB, $USER;
    if ($context->contextlevel != CONTEXT_USER) {
        return false;
    }
    if (strpos($filearea, 'files_') !== 0) {
        return false;
    }
	$itemid = (count($args)>0)?$args[0]:0;
	//http://<domainname>/pluginfile.php/159/mentorsession_file/files_1/4/mysession1.jpg  
	require_login(null, false);
    $fieldid = substr($filearea, strlen('files_'));
    array_shift($args); // ignore revision - designed to prevent caching problems only
    $relativepath = implode('/', $args);
    $fullpath = "/{$context->id}/mentorsession_file/$filearea/$itemid/$relativepath";
    $fs = get_file_storage();
    if (!($file = $fs->get_file_by_hash(sha1($fullpath))) || $file->is_directory()) {
        return false;
    }
    // Force download
    send_stored_file($file, 0, 0, true);
}

//@Params: date as Timestamp
function checkFutureDate($date){
	$tomorrow = date("Y-m-d", strtotime('tomorrow'));
	$timestamp = strtotime($tomorrow);
	//$tomorrow_date = date("Y-m-d", $timestamp);
	if($date>=$timestamp)
		return true;
	else
		return false;
}
//Mentor Report Your session: mysession
function mentor_sessioncount($condition=''){
	global $DB;
	$sql = "SELECT count(*) as total FROM `mdl_mentor_sessionrpt` msr join mdl_user mu on mu.id=msr.mentorid join mdl_school ms on msr.schoolid=ms.id WHERE mu.deleted=0 $condition";
	$result = $DB->get_record_sql($sql);
	return $result->total;
}

//Mentor Report Your session: render mysession
function get_allmysession($limit='',$start=0,$condition=''){
	global $DB;
	$addlimit='';
	if($limit)
		$addlimit = "limit $start,$limit";
	$sql = "SELECT msr.*,mu.id as mentorid,mu.email as mentoremail,CONCAT(mu.firstname,' ',mu.lastname) as mentorname,ms.name as schoolname,ms.id as schoolid,ms.atl_id as schoolatlid FROM `mdl_mentor_sessionrpt` msr join mdl_user mu on mu.id=msr.mentorid join mdl_school ms on msr.schoolid=ms.id WHERE mu.deleted=0 $condition $addlimit";
	$result = $DB->get_records_sql($sql);
	return ($result);
}

//@Params: date as Timestamp
function checkBeforeDec($date){
	//Session date should not be before 1st Dec 2018
	//mktime(hour,minute,second,month,day,year)
	$timestamp = mktime(23,50,0,11,30,'2018'); //30-nov-18	
	if($date<$timestamp)
		return true;
	else
		return false;
}

function get_schemepopuphtml(){
	global $CFG;
	$pdflink = $CFG->wwwroot.'/mentorguides/Incentivisation Strategy.pdf#zoom=75';
	$test = '<input type="hidden" id="id_frmcategory"><form id="mform2"></form>';
	$pdf = '<br><embed src="'.$pdflink.'" width="100%" height="480px" />'.$test;
	$content = '
	<div id="atlboxscheme" class="modal moodle-has-zindex hide" data-region="modal-container" aria-hidden="false" role="dialog" style="z-index: 1052;">
		<div class="modal-dialog modal-lg" role="document" data-region="modal" aria-labelledby="0-modal-title" >
		<div class="modal-content">
		<div class="modal-header " data-region="header">
		<button id="myclose1" type="button" class="close closebtn" data-action="hide" aria-label="Close">
			<span aria-hidden="true">×</span>
		</button>
		<h4 id="0-modal-title" class="modal-title" data-region="title" tabindex="0">Incentivization Scheme</h4>
		</div>
		<div class="modal-body" data-region="body" style="">'.$pdf.'

		</div>
		<div class="modal-footer" data-region="footer">
		<button id="schemepopup1" type="button" class="btn btn-secondary closebtn" data-action="cancel">Cancel</button>
		</div>
		</div>
		</div>
	</div>';
	return $content;
}

//@WebService api: To add/update mentor session data from Mobile App
//@params: data as Array()
function addupdate_mentorsession($data){
	global $DB;
	$result = validateFormSessionDate($data);
	if(is_array($result) && $result[0]=='ok'){
		$user = $DB->get_record('user',array('email'=>$data['email']),'id');
		$data = (object) $data;
		$cm = make_rptobjectarray($data,$user->id);
		try {
			$transaction = $DB->start_delegated_transaction();
			if($cm->flag=="edit"){
				$DB->update_record('mentor_sessionrpt', $cm);
			} else{
				$cm->id = $DB->insert_record('mentor_sessionrpt', $cm);
			}
			$transaction->allow_commit(); //close transaction
			return array('errorflag'=>0,'msg'=>'success','id'=>$cm->id);
		} catch(Exception $e) {
			$transaction->rollback($e);
			return array('errorflag'=>1,'msg'=>'Database error occurs');
		}
	} else{
		return array('errorflag'=>1,'msg'=>$result[1]);
	}
}

//Report Your session: To Calculate TOTAL Session Time in Hrs.Mins(0.00) while saving to DB
//@params: starttime , endtime as STRING
//@Params: Default Format: starttime: 6-0-AM , endtime: 14-0-AM (24hrs format)
//@Params: EndTime, for 10AM it should be entered as 22AM , 8AM = 20AM
//mktime (hour,minute,second,month,day,year)
function calc_timediff_New($starttime,$endtime){
	$totalhrs =0;
	$currentyear = date("Y");
	$year = $currentyear - 1;
	$tmpstart = explode("-",$starttime);
	$tmpend = explode("-",$endtime);
	$time_start = $tmpstart[0];
	$time_end = $tmpend[0];
	if($tmpend[2]=="AM")
		$time_end = get_iso_convertion($tmpend[0]);
	if($tmpstart[2]=="PM"){
		if($tmpstart[0]!=12)
			$time_start = get_iso_convertion($tmpstart[0],'e');
	}
	if($tmpend[2]=="PM"){
		if($tmpend[0]==24)
			$time_end = get_iso_convertion($tmpend[0],'m');
	}
	date_default_timezone_set('UTC');
	$starttimestamp = mktime($time_start, $tmpstart[1], 0, 12, 12, $year);	
	$endtimestamp = mktime($time_end, $tmpend[1], 0, 12, 12, $year);
	$difference = ($endtimestamp - $starttimestamp)/3600;
	//Calculate
	if($difference==0){
		$totalhrs = 0;
	} elseif($difference<1){
		//Less then zero say 0.63
		if($tmpstart[1]>=30){
			$stmin = 60 - $tmpstart[1];
		} else{
			$stmin = $tmpstart[1];
		}
		$totalhrs = $stmin + $tmpend[1];
		$totalhrs = ($totalhrs==60)?1:'0.'.$totalhrs;
		$totalhrs = floatval($totalhrs);
		$totalhrs = number_format($totalhrs, 2, '.', '');
	} elseif(is_float($difference)===false){
		//1,2,3,4,... whole number
		$totalhrs = number_format($difference, 2, '.', '');
	} else{
		$totalhrs = abs(floor($difference));
		$minutes_sum = $tmpstart[1] + $tmpend[1];
		$minutes_diff = abs($tmpstart[1] - $tmpend[1]);
		if($tmpend[1]>$tmpstart[1]){
			//7-30-AM , 14-40-PM .. 40>30
			$maxmins = $tmpend[1] - $tmpstart[1];
			$maxmins = ($maxmins<10)?'0'.$maxmins:$maxmins;
			$totalhrs = (float) $totalhrs.'.'.$maxmins;
		} else{
			if($minutes_sum<=30){
				$totalhrs = (float) $totalhrs.'.'.$minutes_sum;
			} else{
				if($tmpstart[1]>=30){
					$stmin = 60 - $tmpstart[1];
				} else{
					$stmin = $tmpstart[1];
				}
				$edmin = $tmpend[1];
				$maxmins = $stmin + $edmin;
				if($maxmins>=60)
					$totalhrs++;
				else
					$totalhrs = (float) $totalhrs.'.'.$maxmins;
			}
		}
		$totalhrs = number_format($totalhrs, 2, '.', '');
	}
	return $totalhrs;
}

//mentor session: Show TOTAL Hours in proper format (hr:mins) at Nitiadmin Report
//@params totalhrs Float (Hr.Min) 142.78
function showrpt_totalmentor_hrs($totalhrs){
	if(is_float($totalhrs)){
		$tmp = explode(".",$totalhrs);
		$hr = $tmp[0];
		$min = (!isset($tmp[1]))?0:$tmp[1];
		if($min==60){
			$totalhrs = ++$hr;
			$totalhrs = (float) $totalhrs.'.00';
		} elseif($min>60){
			$mins = $min - 60;
			$totalhrs = ++$hr;
			$totalhrs = (float) $totalhrs.'.'.$mins;
		} else{
			$totalhrs = (float) $hr.'.'.$min;
		}
		$totalhrs = number_format($totalhrs, 2, '.', '');
	}
	return $totalhrs;
}
