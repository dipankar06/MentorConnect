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
 * @CreatedOn: 02-03-2018
 * @Description: Update Mentor Profile
*/

require_once('../config.php');
include_once(__DIR__ .'/editmentor_form.php');
include_once('lib.php');
require_login(null, false);

if (isguestuser()) {
    redirect($CFG->wwwroot);
}

require_once($CFG->libdir.'/filelib.php');

$userrole = get_atalrolenamebyid($USER->msn);

$id = optional_param('key', 0, PARAM_RAW);
$PAGE->set_url('/atalfeatures/creatementor.php');
if (!is_siteadmin()){
	if($userrole!=="admin"){
		redirect($CFG->wwwroot.'/my');
	}
	if($userrole!='admin'){
		$PAGE->set_pagelayout('standard');
		$PAGE->set_heading("Update Mentor Details");
		echo $OUTPUT->header();
		echo $OUTPUT->heading($strmessages);
		$content = "You are not Authorized to Edit this Member Details";
		echo $content;
		echo $OUTPUT->footer();
		die();
	}
}
//Heading
$PAGE->set_pagelayout('standard');
$strmessages = "Create ATAL Mentor<br></br>";
$PAGE->set_heading("Create ATAL Mentor");
$PAGE->set_title("{$SITE->shortname}: $strmessages");
$action_url = $CFG->wwwroot.'/atalfeatures/creatementor.php';
$frmobject = new mentor_update_form($action_url, array('status' =>'new'));
if ($frmobject->is_cancelled()) {
    // The form has been cancelled, take them back to what ever the return to is.
	$returnurl = $CFG->wwwroot.'/create/';
	 redirect($returnurl);
} else if ($formdata = $frmobject->get_data()) {
	// Process if data submitted.
	$data =new stdClass();
	$infodata = new stdClass();
	try
	{
			$transaction = $DB->start_delegated_transaction();
			$mentor_roleid = atal_get_roleidbyname("mentor");
			$data->auth = 'manual';
			$data->confirmed = '1';
			$data->mnethostid = '1';
			$data->username = trim($formdata->email);
			$ran = generate_randomstring();
			$data->passraw = $ran;
			$data->password=hash_internal_user_password($ran);
			$data->idnumber = 'mentor';
			$data->firstname = trim($formdata->name);
			$data->lastname = trim($formdata->lastname);
			$data->email = trim($formdata->email);
			$data->icq = "newuser";
			$data->skype = '';
			$data->yahoo = $formdata->dob;
			$data->aim = $formdata->state;
			$data->msn = $mentor_roleid;
			$data->phone1 = $formdata->mobileno;
			$data->phone2 = '';
			$data->institution  = ucwords(trim($formdata->educationinstitute));
			$data->department = ucwords(trim($formdata->specialization));
			$data->address  = '';
			if(isset($formdata->city))
			{
				$cityname = $DB->get_record('city', array('id'=>$formdata->city,'stateid'=>$formdata->state), 'name');
				$data->city=$cityname->name;
			}
			$data->country = 'IN';
			$data->theme  = '';
			$data->timezone  = '99';
			$data->lastip  = '';
			$data->secret  = '';
			$data->url  =$formdata->linkdlnurl;
			$data->description  =$formdata->professionalsummary;
			$data->timecreated = time();
			$data->timemodified = time();
			$data->lastnamephonetic  =$formdata->degree;
			$data->firstnamephonetic  = $formdata->registerstat;
			$data->middlename  = $formdata->yearofcomplete;
			$language = (isset($formdata->language)) ? $formdata->language : array('5');
			$data->alternatename = implode(',',$language);
			$data->gender = $formdata->gender;
			//Details For Mentor India & Refreence check info
			$infodata = new stdClass();
			$infodata->aadhar_no  = $formdata->aadhar_no;
			$infodata->schoolid  = $formdata->school;
			$infodata->timecommitperday  = $formdata->timecommit;
			$infodata->possibleareaofinterven  = $formdata->areaofinterventation;
			$infodata->otherschooloption  = $formdata->otherschool;
			$infodata->whymentor  = $formdata->effectivementor;
			$infodata->refree1_name  =isset($formdata->refree1_name)?$formdata->refree1_name:'';
			$infodata->refree1_contact  =isset($formdata->refree1_contact)?$formdata->refree1_contact:'';
			$infodata->refree1_email  =isset($formdata->refree1_email)?$formdata->refree1_email:'';
			$infodata->refree1_know   =isset($formdata->refree1_know)?$formdata->refree1_know:'';
			$infodata->refree2_name   =isset($formdata->refree2_name)?$formdata->refree2_name:'';
			$infodata->refree2_contact  =isset($formdata->refree2_contact)?$formdata->refree2_contact:'';
			$infodata->refree2_email  =isset($formdata->refree2_email)?$formdata->refree2_email:'';
			$infodata->refree2_know  =isset($formdata->refree2_know)?$formdata->refree2_know:'';
			$infodata->hearaboutmentor  =isset($formdata->hearaboutmentor)?$formdata->hearaboutmentor:'';
			$infodata->acceptterms  =1;
			$infodata->fburl  =isset($formdata->fburl)?$formdata->fburl:'';
			$uemail = trim($formdata->email);
			if(!empty($uemail)){
				$id = $DB->insert_record('user', $data);
				$infodata->userid = $id;
				$infodata->data  = 'mentor data';
				$userinfodataid = $DB->insert_record('user_info_data', $infodata);
				$usercontext = context_user::instance($id);
				$transaction->allow_commit(); 
				$name = $data->firstname.' '.$data->lastname;
				send_welcomemail($mentor_roleid,$id,$name,$data->email,$data->username,$ran);
				$urltogo = new moodle_url('/create/'); // Move To School Detail Page
				redirect($urltogo);
			}
	}
	catch(Exception $e)
	{
		$transaction->rollback($e);
	}
}
echo $OUTPUT->header();
$backlink = $CFG->wwwroot.'/create/';
		$back='<div class="card-block">
		<h1>
		  <a class="btn btn-primary pull-right" href="'.$backlink.'">Back</a>
		</h1>
		</div>';
echo $back;
echo $OUTPUT->heading($strmessages);
$content.= $frmobject->render();
echo $content;
echo $OUTPUT->footer();
?>
<script type="text/javascript">
require(['jquery','jqueryui'], function($) {
	$( "#id_dob" ).datepicker({
			changeMonth: true,
			changeYear: true,
			yearRange: '1940:2005',
			dateFormat: 'dd/mm/yy' 
	});
});
</script>
