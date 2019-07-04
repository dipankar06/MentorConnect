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
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 01-01-2018
 * @Description: Create a student
*/

require_once('../config.php');
include_once(__DIR__ .'/render.php');
include_once(__DIR__ .'/lib.php');
include_once(__DIR__ .'/createuser_form.php');

require_login(null, false);

if (isguestuser()) {
    redirect($CFG->wwwroot);
}

require_once($CFG->libdir.'/filelib.php');

$userrole = get_atalrolenamebyid($USER->msn);
//$id     = optional_param('id', -1, PARAM_INT);    // User id; -1 if creating new user.
$firstlogin = optional_param('firstlogin', 0, PARAM_INT);    // Check First Login or Not
$redirect = true;
$key = optional_param('key', 0, PARAM_RAW);
$id=encryptdecrypt_userid($key,"de");
if(!$key)
	$id = -1;
// Only Incharge and Respective Student can Access the Page
if($userrole == 'student' && $USER->id == $id )
		$redirect = false;
elseif($userrole == 'incharge')
		$redirect = false;
if($redirect)
	redirect($CFG->wwwroot.'/my');

//$PAGE->set_url('/project/createstudent.php', array('id' => $id));
$PAGE->set_url('/project/createstudent.php', array('key' =>$key));
$show_form_status = false;
$content='';
if($id==-1){
	$strmessages = "Add New Student<br></br>";
	$PAGE->set_heading("Add New Student");
	$PAGE->set_title('Mentor Connect : Add New Student');
} else{
	$strmessages = "Update Student Details<br></br>";
	$PAGE->set_heading("Update Student Details");
	$PAGE->set_title('Mentor Connect : Update Student Details');
}
//Heading
$PAGE->set_context(context_user::instance($USER->id));
//Heading
if($firstlogin)
	$PAGE->set_pagelayout('singlecolumn');
else
	$PAGE->set_pagelayout('standard');
$renderobj = new project_render($USER->id, $USER->msn);
$action_url = $CFG->wwwroot.'/project/createstudent.php?key='.$key;
if($firstlogin)
	$action_url = $CFG->wwwroot.'/project/createstudent.php?firstlogin=1&key='.$key;
$frmobject = new user_create_form($action_url, array('id' => $id));
if($id!==-1){
	$studentinfo = new StdClass();
	$student = $DB->get_record('user', array('id'=>$id), '*');
	$student_school = $DB->get_record('user_school', array('userid'=>$id), '*');
	$studentinfo->firstname =$student->firstname;
	$studentinfo->lastname =$student->lastname;
	$studentinfo->studentclass =$student_school->studentclass;
	$studentinfo->phone1 =$student->phone1;
	$studentinfo->gender =$student->gender;
	$studentinfo->email =$student->email;
	$studentinfo->username =$student->username;
	$studentinfo->studentid =$id;
	$studentinfo->user_school_id=$student_school->id;
	$j = $frmobject->set_data($studentinfo);
}
if ($frmobject->is_cancelled()) {
    // The form has been cancelled, take them back to what ever the return to is.
    redirect($returnurl);
} else if ($data = $frmobject->get_data()) {
    // Process data if submitted.	
    if ($data->flag=='add') {
		// Create Student - New User 
		if(create_newuser($data)){
			redirect($CFG->wwwroot.'/create/liststudent.php');
		}
    } else {
		$result = array_diff((array)$data,(array)$studentinfo);
		//Update School Details
		$record1 = new stdClass();
		$record2 = new stdClass();
		$record1->id=   $data->studentid;
		isset($result['firstname'])?$record1->firstname =$data->firstname:'';
		isset($result['lastname'])?$record1->lastname =$data->lastname:'';
		isset($result['phone1'])?$record1->phone1 =$data->phone1:'';
		isset($result['gender'])?$record1->gender =$data->gender:'';
		isset($result['email'])?$record1->email =$data->email:'';
		isset($result['email'])?$record1->username =$data->email:'';
		if(isset($result['studentclass'])){
			$record2->id=   $data->user_school_id;
			$record2->studentclass =$data->studentclass;
			$updatedid = $DB->update_record('user_school', $record2);
		}		
		if(count((array)$record1)>1)
			$updatedid = $DB->update_record('user', $record1);
		if($firstlogin){
			$userdata->id =$id;
			$userdata->profilestatus =1;
			$DB->update_record('user', $userdata);
			$urltogo = new moodle_url('/my'); // Move To School Detail Page
			redirect($urltogo); 
		 }
		$show_form_status = true;
		//$urltogo = new moodle_url('/create/liststudent.php'); // Move To School Detail Page
		//redirect($urltogo);
    }
}

// RHS Block.
$filters = projectside_block_student($renderobj);
$PAGE->blocks->add_fake_block($filters, $pos=BLOCK_POS_RIGHT);

echo $OUTPUT->header();
if($mode=="edit"){
	$strmessages=="Edit Student";
} else{
	$strmessages=="Create Student";
	//$content = adduser($frmobject);
}
if($firstlogin)
{
$content.='<div id="myprofilebox" class="modal moodle-has-zindex show" data-region="modal-container" aria-hidden="false" role="dialog" style="z-index: 1052;">
	<div class="modal-dialog modal-lg" role="document" data-region="modal" aria-labelledby="0-modal-title" style="width:100%; height:90%;max-width: 80% !important;">
	<div class="modal-content">
	<div class="modal-header " data-region="header">

	<h4 id="modaltitle" class="modal-title" data-region="title" tabindex="0">'.$OUTPUT->heading($strmessages).'</h4>
	</div>
	<div class="modal-body" data-region="body" style="">
		'.adduser($frmobject).'

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
echo $renderobj->getbackbutton();
echo $OUTPUT->heading($strmessages);
$content.= adduser($frmobject);
}
echo $content;
echo $OUTPUT->footer();
