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
require_login(null, false);

if (isguestuser()) {
    redirect($CFG->wwwroot);
}
$content="";
$show_form_status = false;
$alert_box='';
require_once($CFG->libdir.'/filelib.php');
$userrole = get_atalrolenamebyid($USER->msn);
$id = optional_param('key', 0, PARAM_RAW);
$key = optional_param('key', 0, PARAM_RAW);
$firstlogin = optional_param('firstlogin', 0, PARAM_INT);   // Check First Login or Not
$filemanageroptions =  array('maxfiles' => 1,'maxbytes' => 1048576,'subdirs' => 0,'accepted_types' => 'jpg,jpeg,png');

$PAGE->set_url('/atalfeatures/editmentor.php', array('key' =>$id));
$id=encryptdecrypt_userid($id,"de");
if (!is_siteadmin()){
	if($userrole!=="admin" && $userrole!=="mentor"){
		redirect($CFG->wwwroot.'/my');
	}
	if($id!= $USER->id && $userrole!='admin'){
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
$mentor = $DB->get_record('user', array('id'=>$id));

//Heading
if($firstlogin)
	$PAGE->set_pagelayout('singlecolumn');
else
	$PAGE->set_pagelayout('standard');
$strmessages = "Update Mentor Details<br></br>";
$PAGE->set_heading("Update Mentor Details");
$PAGE->set_title("{$SITE->shortname}: $strmessages");
if($firstlogin)
	$action_url = $CFG->wwwroot.'/atalfeatures/editmentor.php?firstlogin=1&key='.$key;
else
	$action_url = $CFG->wwwroot.'/atalfeatures/editmentor.php?key='.$key;
$frmobject = new mentor_update_form($action_url,array('key' => $key,'firstlogin'=>$firstlogin));
if($userrole=='mentor')
	$backlink = $CFG->wwwroot.'/user/profile.php?id='.$id;
else
	$backlink = $CFG->wwwroot.'/create/';
if($id!==0){
	$data_mentor = 'mentor data';
	$sql = "Select * from {user_info_data} where data='mentor data' AND userid=".$id;
	$infodata = $DB->get_record_sql($sql); 
	//Hidden for first login
	if(!$firstlogin)
		$city = $DB->get_record('city', array('stateid'=>$mentor->aim,'name'=>$mentor->city), 'id'); 
	$moreuserdata = $DB->get_record('user', array('id'=>$id), 'description');
	$description = '';
	$description=(isset($moreuserdata->description))?$moreuserdata->description:$description;
	$data = new stdClass();
	if(count($infodata)<=0)
	{
		$data->school='';
		$data->timecommit ='';
		$data->fburl='';
		$data->areaofinterventation='';
		$data->otherschool='';
		$data->effectivementor='';
	}
	else
	{
		if($firstlogin)
		{
			$role = "mentor";
			$userschoolresult = $DB->get_record('user_school', array('userid'=>$id,'role'=>$role));
			if($userschoolresult)
			{
				$schoolresult = $DB->get_record('school', array('id'=>$userschoolresult->schoolid), 'name,address,school_emailid,phone,principal_name ');
				$data->schoolname = $schoolresult->name ;
				$data->schooladdress =($schoolresult->address)?$schoolresult->address:'NA' ;
				$data->schoolcontactname = ($schoolresult->principal_name)?$schoolresult->principal_name:'NA' ;
				$data->schoolcontactphone = ($schoolresult->phone)?$schoolresult->phone:'NA' ;
				$data->schoolcontactemail = ($schoolresult->school_emailid)?$schoolresult->school_emailid:'NA' ; 
			}
		}
		//$data->school = $infodata->schoolid ; //Shool
		$data->timecommit = $infodata->timecommitperday; //TimeCommitment/Week
		$data->fburl = $infodata->fburl; //Facebook URL 
		$data->areaofinterventation = $infodata->possibleareaofinterven; //Possible Area of Intervention
		$data->otherschool=$infodata->	otherschooloption ;//ATAL School From other Location
		$data->effectivementor=$infodata->whymentor; //Why you want to become a mentor to ATAL
		$data->refree1_name=$infodata->refree1_name; 
		$data->refree1_contact=$infodata->refree1_contact; 
		$data->refree1_email=$infodata->refree1_email; 
		$data->refree1_know=$infodata->refree1_know; 
		$data->refree2_name=$infodata->refree2_name; 
		$data->refree2_contact=$infodata->refree2_contact; 
		$data->refree2_email=$infodata->refree2_email; 
		$data->refree2_know=$infodata->refree2_know; 
		$data->hearaboutmentor=$infodata->hearaboutmentor; 
		$data->aadhar_no=$infodata->aadhar_no; 
	}
	$data->name = $mentor->firstname; // FirstName
	$data->lastname = $mentor->lastname; //LastName
	$data->email = $mentor->email; // FirstName
	$data->gender = $mentor->gender; //Gender
	$data->dob = $mentor->yahoo; //Date of Bith
	$data->yearofcomplete = $mentor->middlename; // Year of Completion
	$data->language = $mentor->alternatename; //Languages Known 
	$data->mobileno = $mentor->phone1; // Contact Number
	$data->linkdlnurl = $mentor->url; //Linked URL
	$data->degree = $mentor->lastnamephonetic; // Highest Degree
	$data->registerstat = $mentor->firstnamephonetic; // Registered As
	$data->educationinstitute = $mentor->institution; //Educational Institute 
	$data->specialization = $mentor->department; //Area of Specialization 
	$data->professionalsummary = $description; //Professional Summery
	if(!$firstlogin){
		$data->state=$mentor->aim;
		$data->city=$city->id;
		$data->cityid=$city->id;
		//Set Govt PhotoId
		$sql="SELECT ud.id,ud.userid,uf.id as fieldid FROM {user_info_data} ud JOIN {user_info_field} uf ON ud.fieldid=uf.id WHERE uf.shortname=? AND ud.userid=?";
		$mydata = $DB->get_record_sql($sql,array('govtphotoid',$USER->id));
		if(isset($mydata->id)){
			$context = context_user::instance($USER->id);
			$draftitemid = file_get_submitted_draft_itemid('govtphotoid');
			file_prepare_draft_area($draftitemid, $context->id, 'profilefield_file', "files_{$mydata->fieldid}", 0, $filemanageroptions);
			$data->govtphotoid = $draftitemid;
		}
	}
	$data->currentemail=$mentor->email;
	$frmobject->set_data($data);
}
if ($frmobject->is_cancelled()) {
    // The form has been cancelled, take them back to what ever the return to is.
	// Logout the User if he Press Cancel Button Without Updating Forms 
	if($firstlogin){
		$logouturl = new moodle_url($CFG->wwwroot.'/login/logout.php', array('sesskey' => $_POST['sesskey']));
		redirect($logouturl);
	}
	else
		redirect($backlink);
		
} else if ($formdata = $frmobject->get_data()) {
	// Process if data submitted.
	unset($formdata->submitbutton);
	$result = array_diff((array)$formdata,(array)$data);
	//$result = (object)$result;
	$userdata =new stdClass();
	$userinfodata = new stdClass();
	//Update Data into User Table	
	$userdata->id = $mentor->id;
	isset($result['name'])?$userdata->firstname = $result['name']:'';
	isset($result['lastname'])?$userdata->lastname = $result['lastname']:'';
	isset($result['email'])?$userdata->email = $result['email']:'';
	isset($result['email'])?$userdata->username = $result['email']:'';
	isset($result['gender'])?$userdata->gender = $result['gender']:'';
	isset($result['dob'])?$userdata->yahoo = $result['dob']:'';
	isset($result['yearofcomplete'])?$userdata->middlename = $result['yearofcomplete']:'';
	isset($result['mobileno'])?$userdata->phone1 = $result['mobileno']:'';
	isset($result['linkdlnurl'])?$userdata->url = $result['linkdlnurl']:'';
	isset($result['degree'])?$userdata->lastnamephonetic = $result['degree']:'';
	isset($result['registerstat'])?$userdata->firstnamephonetic = $result['registerstat']:'';
	isset($result['educationinstitute'])?$userdata->institution = $result['educationinstitute']:'';
	isset($result['specialization'])?$userdata->department = $result['specialization']:'';
	isset($result['professionalsummary'])?$userdata->description = $result['professionalsummary']:'';
	$language = implode(",",$result['language']);
	(count($result['language'])>0)?$userdata->alternatename = $language:'';
	isset($result['state'])?$userdata->aim = $result['state']:'';
	if(isset($result['city']))
	{
		if(!isset($result['state']))
			$cityname = $DB->get_record('city', array('id'=>$result['city'],'stateid'=>$mentor->aim), 'name');
		else
			$cityname = $DB->get_record('city', array('id'=>$result['city'],'stateid'=>$userdata->aim), 'name');
		$userdata->city=$cityname->name;
	}
	//Update Data into User_info_data Table
	isset($result['aadhar_no'])?$userinfodata->aadhar_no=$result['aadhar_no']:'';
	isset($result['school'])?$userinfodata->schoolid=$result['school']:'';
	isset($result['timecommit'])?$userinfodata->timecommitperday=$result['timecommit']:'';
	isset($result['fburl'])?$userinfodata->fburl=$result['fburl']:'';
	isset($result['areaofinterventation'])?$userinfodata->possibleareaofinterven=$result['areaofinterventation']:'';
	isset($result['otherschool'])?$userinfodata->otherschooloption=$result['otherschool']:'';
	isset($result['effectivementor'])?$userinfodata->whymentor=$result['effectivementor']:'';
	isset($result['refree1_name'])?$userinfodata->refree1_name=$result['refree1_name']:'';
	isset($result['refree1_contact'])?$userinfodata->refree1_contact=$result['refree1_contact']:'';
	isset($result['refree1_email'])?$userinfodata->refree1_email=$result['refree1_email']:'';
	isset($result['refree1_know'])?$userinfodata->refree1_know=$result['refree1_know']:'';
	isset($result['refree2_name'])?$userinfodata->refree2_name=$result['refree2_name']:'';
	isset($result['refree2_contact'])?$userinfodata->refree2_contact=$result['refree2_contact']:'';
	isset($result['refree2_email'])?$userinfodata->refree2_email=$result['refree2_email']:'';
	isset($result['refree2_know'])?$userinfodata->refree2_know=$result['refree2_know']:'';
	isset($result['hearaboutmentor'])?$userinfodata->hearaboutmentor=$result['hearaboutmentor']:'';
	try
	{
		$transaction = $DB->start_delegated_transaction();
		if(count((array)$userdata)>=1)
		{			
			$updateuserdata = $DB->update_record('user', $userdata);
		}
		if(count((array)$userinfodata)>=1){
			if(!isset($infodata->id))
			{
				$userinfodata->userid = $id;
				$userinfodata->data = 'mentor data';
				$userinfodata->acceptterms=1;
				$updateuserinfodata = $DB->insert_record('user_info_data', $userinfodata);
			}
			else
			{
				$userinfodata->id = $infodata->id;
				$updateuserinfodata = $DB->update_record('user_info_data', $userinfodata);
			}
		}
		$transaction->allow_commit();

		//Add Govt Photo ID
		$govtid = ($firstlogin) ? $result['govtphotoid'] : $formdata->govtphotoid;
		$info = file_get_draft_area_info($govtid);
		$present = ($info['filecount']>0) ? '1' : '';
		if($present=='1'){
			$fielddata = $DB->get_record('user_info_field', array('shortname'=>'govtphotoid'),'id');
			$filearea = 'files_'.$fielddata->id;
			$usercontext = context_user::instance($USER->id, MUST_EXIST);
			$fs = get_file_storage();
			$fs->delete_area_files($usercontext->id, 'profilefield_file', $filearea, 0);
			file_save_draft_area_files($govtid, $usercontext->id, 'profilefield_file', $filearea, 0, $filemanageroptions);
			if($firstlogin){
				$userinfodatanew = new stdClass();
				$userinfodatanew->userid = $id;
				$userinfodatanew->fieldid = $fielddata->id;
				$userinfodatanew->data = '0';
				//$userinfocnt = $DB->count_records('user_info_data', array('userid'=>$USER->id,'fieldid'=>$fielddata->id));
				$updateuserinfodata = $DB->insert_record('user_info_data', $userinfodatanew);
			}
		}
		if($firstlogin){
			$userdata->id = $mentor->id;
			$userdata->profilestatus =1;
			$DB->update_record('user', $userdata);
			$urltogo = new moodle_url('/my'); // Move To Dashboard Page
			redirect($urltogo);
		} else{
			$show_form_status = true;
		}
	}
	catch(Exception $e){
		$transaction->rollback($e);
	}
}
echo $OUTPUT->header();
// href="'.$backlink.'"
$back='<div class="card-block">
	<h1>
	<a class="btn btn-primary pull-right goBack">Back</a>
	</h1>
	</div>';
if($firstlogin)
{
$content.='<div id="myprofilebox" class="modal moodle-has-zindex show" data-region="modal-container" aria-hidden="false" role="dialog" style="z-index: 1052;">
	<div class="modal-dialog modal-lg" role="document" data-region="modal" aria-labelledby="0-modal-title" style="width:100%; height:90%;overflow: scroll; max-width: 80% !important;">
	<div class="modal-content">
	<div class="modal-header " data-region="header">

	<h4 id="modaltitle" class="modal-title" data-region="title" tabindex="0">'.$OUTPUT->heading($strmessages).'</h4>
	</div>
	<div class="modal-body" data-region="body" style="">
		'.$frmobject->render().'

	</div>
	<div class="modal-footer" data-region="footer">			
	</div>
	</div>
	</div>
</div> ';
$content.='<div id="atlbox2" class="modal-backdrop in" aria-hidden="false" data-region="modal-backdrop" style="z-index: 1051;">
</div> ';
}
else
{
if($show_form_status)
{
	$alert_box='<div class="alert alert-success">
				  <strong>Updated Successfully! </strong><button class="close" type="button" data-dismiss="alert">×</button></a>
		</div>';
}
echo $alert_box;
echo $back;
echo $OUTPUT->heading($strmessages);
$content.= $frmobject->render();
}
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
