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
 * @CreatedOn: 07-02-2018
 * @Description: Create a New user (School Incharge)
*/

require_once('../config.php');

require_login(null, false);

if (isguestuser()) {
    redirect($CFG->wwwroot);
}

require_once($CFG->libdir.'/filelib.php');

$userrole = get_atalrolenamebyid($USER->msn);
if($userrole!="admin"){
	//Only NitiAdmin can create/edit a incharge Info.
	redirect($CFG->wwwroot.'/my');
}

include_once(__DIR__ .'/render.php');
include_once(__DIR__ .'/lib.php');
include_once(__DIR__ .'/createincharge_form.php');
$id     = optional_param('id', $USER->id, PARAM_INT);    // User id; -1 if creating new user.
$PAGE->set_url('/project/createincharge.php', array('id' => $id));

//Heading
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$strmessages = "Add Incharge";
$PAGE->set_title("{$SITE->fullname}");
$PAGE->set_heading("Project");

$renderobj = new project_render($USER->id, $USER->msn);

$schoollist = frmget_allschoollist();
//passing Parameters to a Form..
$frmobject = new user_create_form($CFG->wwwroot.'/project/createincharge.php?id='.$id,array('schoollist'=>$schoollist));

if ($frmobject->is_cancelled()) {
    // The form has been cancelled, take them back to what ever the return to is.
    redirect($returnurl);
} else if ($data = $frmobject->get_data()) {
    // Process data if submitted.
	//print_r($data); exit;
    if ($data->flag=='add') {
		// In creating the course.
		if(create_newuser($data)){
			redirect($CFG->wwwroot.'/project/assignmentor.php');
		}
    } else {
        // Save any changes to the files used in the editor.
        // update_course($data, $editoroptions);      
    }
}

// RHS Block.
$filters = projectside_block_incharge($renderobj);
$PAGE->blocks->add_fake_block($filters, $pos=BLOCK_POS_RIGHT);

echo $OUTPUT->header();
if($mode=="edit"){
	echo $OUTPUT->heading("Edit School Incharge");
	$output = "dfsfsfd";
} else{
	echo $OUTPUT->heading("Create School Incharge");
	$output = adduser($frmobject);
}
// Now the page contents.

echo $output;

echo $OUTPUT->footer();
