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
 * @CreatedOn: 14-11-2018
 * @Description: Add mentor-school session feedback (Mentor)
 Landing page of Feedback form.
*/

require_once('../config.php');
include_once(__DIR__ .'/lib.php');
include_once(__DIR__ .'/createsession_form.php');

require_login(null, false);
$userrole = get_atalrolenamebyid($USER->msn);
if (isguestuser() || $userrole!="mentor") {
    redirect($CFG->wwwroot);
}

$id = optional_param('id', -1, PARAM_INT);   
//function encryptdecrypt_userid($userid,$flag='en') ..need to encrypt sessio id at URL
$key = optional_param('key', '', PARAM_ALPHANUM);

if(!empty($key))
	$id = encryptdecrypt_userid($key,"de");
if($id!==-1){
	//Edit mode, check is it Mentor's session.
	if(check_isMentorSession($id)===false)
		redirect($CFG->wwwroot);
	//Mentor Cannot Edit Other's session.
}
$PAGE->set_url('/mentor/mentorsession.php', array('id' => $id));
$show_form_status = false;
$content='';
$school = array();

$PAGE->set_title("{$SITE->shortname}");
$PAGE->set_heading("Report Your Session");

//Heading
$PAGE->set_context(context_user::instance($USER->id));
//Heading
$PAGE->set_pagelayout('standard');

$schoollist[] = "Select a School";
if($id!==-1){
	//Assign Table values to Edit Form	
	$sql = "SELECT s.id,s.name FROM {school} s JOIN {user_school} u ON s.id=u.schoolid WHERE u.userid = ? ORDER By s.name";
	$school = $DB->get_records_sql($sql, array($USER->id));
	if(count($school)>0){
		foreach($school as $keys=>$values){
			$schoollist[$values->id] = $values->name;
		}
	}
	$mentorinfo = new StdClass();	
	$recordset = $DB->get_record('mentor_sessionrpt', array('id'=>$id));	
	$mentorinfo->schoolid = $recordset->schoolid;
	$mentorinfo->dateofsession = $recordset->dateofsession;
	$mentorinfo->starttime = $recordset->starttime;
	$mentorinfo->endtime = $recordset->endtime;
	$mentorinfo->sessiontype = $recordset->sessiontype;
	$mentorinfo->functiondetails = (isset($data->schoolfunction))?$recordset->functiondetails:NULL;
	$mentorinfo->schoolfunction = (isset($data->schoolfunction))?$recordset->functiondetails:NULL; //hidden field in form.
	$mentorinfo->totalstudents = $recordset->totalstudents;
	$mentorinfo->details = $recordset->details;
	$mentorinfo->timecreated = $recordset->timecreated;
	$mentorinfo->declarationagree = $recordset->declarationagree;
	$mentorinfo->dateofsession_date = $recordset->dateofsession_date;
	//To populate text-area for school function details, if option c is selected.
	$functiondetail = $recordset->functiondetails;
	$action_url = $CFG->wwwroot.'/mentor/mentorsession.php?key='.$key ;
	$frmobject = new session_create_form($action_url, array('id' => $id,'sttime'=>$mentorinfo->starttime,'edtime'=>$mentorinfo->endtime,
	'fundetail'=>$functiondetail,'sesstype'=>$mentorinfo->sessiontype,'schoollist'=>$schoollist));
	//File Area.
	$draftitemid = file_get_submitted_draft_itemid('rptmentorfiles');
	$fid = 1;
	$context = context_user::instance($USER->id, MUST_EXIST);
	file_prepare_draft_area($draftitemid, $context->id, 'rptmentorfiles', "files_{$fid}", 0, get_session_filemanageroptions());	
	$mentorinfo->rptmentorfiles = $draftitemid;
	//Set Form Values for Edit.
	$j = $frmobject->set_data($mentorinfo);
	//$school[] = $mentorinfo->schoolid;
} else{
	//Add form.
	$mentorinfo = new StdClass();
	$sql = "SELECT s.id,s.name FROM {school} s JOIN {user_school} u ON s.id=u.schoolid WHERE u.userid = ? ORDER By s.name";
	$school = $DB->get_records_sql($sql, array($USER->id));
	if(count($school)>0){
		foreach($school as $keys=>$values){
			$schoollist[$values->id] = $values->name;
		}
	}
	$mentorinfo->starttime = '';
	$mentorinfo->endtime = '';
	$functiondetail = '';
	$session_type = '';
	if(isset($_POST['starttime']) && isset($_POST['endtime'])){
		$mentorinfo->starttime = $_POST['starttime'];
		$mentorinfo->endtime = $_POST['endtime'];
	}
	if(isset($_POST['schoolfunction']) && !empty($_POST['schoolfunction'])){
		$functiondetail = $_POST['schoolfunction'];
	}
	if(isset($_POST['sessiontype']) && !empty($_POST['sessiontype'])){
		$session_type = $_POST['sessiontype'];
	}
	$frmobject = new session_create_form($null, array('id' => $id,'sttime'=>$mentorinfo->starttime,'edtime'=>$mentorinfo->endtime,
	'fundetail'=>$functiondetail,'sesstype'=>$session_type,'schoollist'=>$schoollist));
	
	$j = $frmobject->set_data($mentorinfo);
}
$returnurl = $CFG->wwwroot.'/mentor/mysession.php';
if ($frmobject->is_cancelled()) {
    // The form has been cancelled, take them back to what ever the return to is.	
    redirect($returnurl);
} else if ($data = $frmobject->get_data()) {
    // Process data if submitted.	
    if ($data->flag=='add') {
		// Add new feedback		
		addUpdateSessionfeedback($data);
		redirect($returnurl);		
    } else {
		//Update feedback
		addUpdateSessionfeedback($data);
		redirect($returnurl);
    }
}

if(count($school)>0){
	$daylimit = getMaxdays_forsessionreport();	
	$daylimit--;
	$content.= '<div class="mgtop">'.$frmobject->render().'</div>';
	$content.= '<div>&#42; NOTES: 
	<ol>
	<li>The details entered in this form will be used to assess each mentor as per an <a href="javascript:void(0)" onClick="openincentivescheme(); return false;">incentivization scheme.</a></li>
	<li>You can only report start and end time of session between 6AM - 10PM and not more than 8hrs.</li>
	<li>You cannot report two sessions in one day. </li>
	<li>In Upload Pictures, you can upload up to 4 files, only 1 file upload is compulsory.</li>
	<li>You cannot select date of session before 1st Dec 2018</li>
	</ol></div>';
} else{
	$content.= '<div class="mgtop">You cannot add a session feedback, as there is no school assigned to you.</div>';
}
echo $OUTPUT->header();
echo $OUTPUT->heading("Report Your Session");

$content.=get_schemepopuphtml();
echo $content;
echo $OUTPUT->footer();
?>
<script type="text/javascript">

function openincentivescheme(){
	document.getElementById("atlbox2").style.display = "block";
	document.getElementById("atlboxscheme").style.display = "block";
}
var mycloseFunction = function() {
	document.getElementById("atlbox2").style.display = "none";
	document.getElementById("atlboxscheme").style.display = "none";
};
require(['jquery'], function($) {
$("#atlbox2").hide();
$("#atlloader").hide();

$("#schemepopup1").click(function(){
    mycloseFunction();
});
$("#myclose1").click(function(){
    mycloseFunction();
});
});
</script>
<?php
/* Validations @19-Nov-2018
1) a mentor should not be able to report a session of more than 8 hrs.    
2) a mentor should not be able to report a session on a date more than 7 days earlier the present date. Eg. if the mentor is filling the form on 8th December 2018, he.she should not be  able to select a date before 1st December 2018. Please note an exception will have to be made for the days 1st Dec 2018 to 7th Dec 2018 to ensure that the mentor is not able to select a date in November 2018. 
3) a mentor should not be able to report 2 sessions on the same day.
4) a mentor should be able to report a session with timings only between 0600hrs and 2200hrs on a particular day.

Validations Updates @29-Nov-2018 : Email Subject "IBM portal - Report my Session form deployment on 1st Dec 2018"..
1) Regarding point 3 in the NOTES, please remove it and disable the 7-day earlier limit. We fell that for the initial 1 month, maintaining the 1-week limit to report the sessions may not be conducive, we will enable this later on in JAN 19.
	//You cannot report a session held more then '.$daylimit.' days ago
	
*/
?>