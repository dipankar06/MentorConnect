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
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 12-03-2018
 * @Description: Create a New School with School Incharge
*/

require_once('../config.php');

require_login(null, false);
if(isguestuser()) {
    redirect($CFG->wwwroot);
}
require_once($CFG->libdir.'/filelib.php');

$userrole = get_atalrolenamebyid($USER->msn);
if($userrole!="admin" && $userrole!="incharge" ){
	//Only NitiAdmin can create/edit a Info.
	redirect($CFG->wwwroot.'/my');
}
include_once(__DIR__ .'/createschool_form.php');
include_once(__DIR__ .'/schoollib.php');

$id = optional_param('id', 0, PARAM_INT);    // User id: 0 if creating new school.
$firstlogin = optional_param('firstlogin', 0, PARAM_INT);    // Check First Login or Not
$show_form_status =false;
$PAGE->set_url('/create/createschool.php', array('id' => $id));

//Heading
if($firstlogin)
	$PAGE->set_pagelayout('singlecolumn');
else
	$PAGE->set_pagelayout('standard');

if($id==0){
	$strmessages = "Add School<br></br>";
	$PAGE->set_heading("Add School");
} else{
	$strmessages = "Update School<br></br>";
	$PAGE->set_heading("Update School");
}
$PAGE->set_title("{$SITE->fullname}:");
if($id!==0)
	$action_url = $CFG->wwwroot.'/create/createschool.php?id='.$id;
else
	$action_url = $CFG->wwwroot.'/create/createschool.php';		
if($firstlogin)
	$action_url = $CFG->wwwroot.'/create/createschool.php?firstlogin=1&id='.$id;
$frmobject = new create_school_form($action_url, array('id' => $id));
if($id!==0){
	$school = $DB->get_record('school', array('id'=>$id), '*');
	$stateid =  $DB->get_record('city', array('id'=>$school->cityid), '*');
	if(!$firstlogin){
			$school->state =$stateid->stateid;
			$school->city = $school->cityid;
	}
	$school->schoolid = $school->id;
	$inchargeuserschool = $DB->get_record('user_school', array('schoolid'=>$school->id,'role'=>'incharge'), 'userid');	
	if(isset($inchargeuserschool->userid)){
		$user = $DB->get_record('user',array('id'=>$inchargeuserschool->userid),'id,firstname,lastname,email,phone1,gender');
		$school->firstname = $user->firstname;
		$school->lastname =  $user->lastname;
		$school->email = $user->email;
		$school->gender = $user->gender;
		$school->phone1 = $user->phone1;
		$school->inchargeid = $user->id;
	}
	$frmobject->set_data($school);
}
if ($frmobject->is_cancelled()) {
    // The form has been cancelled, take them back to what ever the return to is.
	if($firstlogin){
		$logouturl = new moodle_url($CFG->wwwroot.'/login/logout.php', array('sesskey' => $_POST['sesskey']));
		redirect($logouturl);
	}
} else if ($data = $frmobject->get_data()) {
	// Process if data submitted.
	if ($data->flag=='add') {
		//Add New School
		create_newschool($data);
		$urltogo = new moodle_url('/create/listschool.php'); // Move To School Detail Page
		redirect($urltogo); 
	} else{
		//Update a School.
		update_school($data);
		if($firstlogin){
			$userdata->id =$inchargeuserschool->userid;
			$userdata->profilestatus =1;
			$DB->update_record('user', $userdata);
			$urltogo = new moodle_url('/my'); // Move To School Detail Page
			redirect($urltogo); 
		 }
		$show_form_status = true;
	}
}
echo $OUTPUT->header();

if($userrole == 'incharge')
	$backlink = $CFG->wwwroot.'/user/profile.php?id='.$USER->id;
else
	$backlink = $CFG->wwwroot.'/create/listschool.php';
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
$content.='<div id="atlbox2" class="modal-backdrop in" aria-hidden="false" data-region="modal-backdrop" style="z-index: 1051;"></div> ';
}
else
{
if($show_form_status)
{
	$alert_box='<div class="alert alert-success">
				  <strong><button class="close" type="button" data-dismiss="alert">×</button>Updated Successfully! </strong></a>
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
