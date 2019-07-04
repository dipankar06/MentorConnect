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

/*
 * @package: core_project
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 20-01-2018
 * @Description: Display create Project page.
*/

require_once('../config.php');

require_login(null, false);
if (isguestuser()) {
    redirect($CFG->wwwroot);
}

require_once($CFG->libdir.'/filelib.php');
$userrole = get_atalrolenamebyid($USER->msn);

if($userrole=="incharge" ){
	$cancreateproject = true;
} else if($userrole=="student"){
	$cancreateproject = true;
} else{ $cancreateproject = false;
}
if($cancreateproject===false){
	//Only School-incharge & Students can create/edit a project Info.
	redirect($CFG->wwwroot.'/my');
}
include_once(__DIR__ .'/render.php');
include_once(__DIR__ .'/lib.php');
include_once(__DIR__ .'/edit_form.php');
require_once($CFG->dirroot.'/course/lib.php');

$mode = optional_param('mode', 'add', PARAM_ALPHANUM); // Course Mode - Edit or Add new course.
$id = optional_param('id', 0, PARAM_ALPHANUM); // Course id.
$course = null;
if($id!=0){
	$id = encryptdecrypt_projectid($id,'de');
	$course = get_project($id);
	$is_enrol = isenrol_toproject($id, $course);
	if($is_enrol===false){
		redirect($CFG->wwwroot.'/my'); //can't edit other school projects..
	}
}

$atalArray = get_atalvariables();
$category = $atalArray['project_categoryid'];
$catcontext = context_coursecat::instance($category);
//'maxfiles' => EDITOR_UNLIMITED_FILES
$editoroptions = array('maxfiles' => 1, 'maxbytes'=>$CFG->maxbytes, 'trusttext'=>false, 'noclean'=>true,'accepted_types'=>array('image'));
$overviewfilesoptions = course_overviewfiles_options($course);
if(empty($course)) {
	// Editor should respect category context if course context is not set.
	$editoroptions['context'] = $catcontext;
	$editoroptions['subdirs'] = 0;
	$course = file_prepare_standard_editor($course, 'summary', $editoroptions, null, 'course', 'summary', null);
	
	if ($overviewfilesoptions) {
		file_prepare_standard_filemanager($course, 'overviewfiles', $overviewfilesoptions, null, 'course', 'overviewfiles', 0);
	}
}

$url = new moodle_url('/project/');
$PAGE->set_url($url);
//Heading
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$strmessages = "Innovation";
$PAGE->set_title("{$SITE->fullname}");
$PAGE->set_heading("Innovation");

$renderobj = new project_render($USER->id, $USER->msn);

$display_projectcreatemsg = 0;

//get mentors & student list in my school
$myschoolid = getmyschoolid();
$mentor = getusersofschool('mentor',$myschoolid);
$student = getusersofschool('student',$myschoolid);
if(count($mentor)>0){
	$tmp['0'] = "Select";
	foreach($mentor as $key=>$val){
		$tmp[$val->userid] = ucfirst($val->firstname).' '.$val->lastname;
	}
	$mentor = $tmp;
	unset($tmp);
} else{ $mentor = array(); }
if(count($student)>0){
	$tmp['0'] = "Select";
	foreach($student as $key=>$val){
		$tmp[$val->userid] = ucfirst($val->firstname).' '.$val->lastname;
	}
	$student = $tmp;
} else{ $student = array(); }

// First create the form.
$args = array(
    'course' => $course,
    'category' => $category,
    'editoroptions' => $editoroptions,
    'returnto' => '',
    'returnurl' => '',
	'mentorlist'=> $mentor,
	'studentlist'=> $student
);

$frmobject = new add_projectform(null, $args);

if ($frmobject->is_cancelled()) {
    // The form has been cancelled, take them back to what ever the return to is.
    redirect($returnurl);
} else if ($data = $frmobject->get_data()) {
    // Process data if submitted.	
    if ($data->courseid==0) {
		// In creating the course.
		if(isset($_POST['selmentor'])){
			$data->mentor = $_POST['selmentor'];
		}
		if(isset($_POST['selstudent'])){
			$data->student = $_POST['selstudent'];
		}
		if(create_newproject($data)){
			//Project Created Successfully
			//redirect($CFG->wwwroot.'/project/assign.php');
			$display_projectcreatemsg = 1;
		}
    } else {
        // Save any changes to the files used in the editor.        
		if(update_course($data, $editoroptions)){
			$display_projectcreatemsg = 2;
		}	
    }
}

// RHS Block.
$filters = projectside_block_mentor($renderobj);
$PAGE->blocks->add_fake_block($filters, $pos=BLOCK_POS_RIGHT);
//$filters = projectside_block_student($renderobj);
//$PAGE->blocks->add_fake_block($filters, $pos=BLOCK_POS_RIGHT);

echo $OUTPUT->header();
$output = "";

if($display_projectcreatemsg>0){
	if($display_projectcreatemsg==1){
		$output ='<div class="plink"><p>Innovation Created Successfully !</p><p> Please Wait... Until your School Atal Incharge Approves Your Innovation.';
		$output.='Till then Your Innovation will be display under Pending Approval Tab, you can add Mentors and Co-Students</p></div>';
	}
	if($display_projectcreatemsg==2){
		$output ='<div class="plink"><p>Innovation Updated Successfully, Please Wait... Until your School Atal Incharge Approves Your Innovation.</p></div>';
	}	
} else{
	if($mode=="edit"){
		echo $OUTPUT->heading("Edit Innovation");
		$output = "testdata"; //editproject();
	} else{
		echo $OUTPUT->heading("Create Innovation");
		$output = addproject($frmobject);
	}
}
// Now the page contents.

echo $output;

echo $OUTPUT->footer();

?>