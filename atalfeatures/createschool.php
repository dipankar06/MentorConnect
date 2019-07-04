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
 * @CreatedOn: 19-02-2018
 * @Description: Create a New School
*/

require_once('../config.php');
include_once(__DIR__ .'/createschool_form.php');
require_login(null, false);

if (isguestuser()) {
    redirect($CFG->wwwroot);
}

require_once($CFG->libdir.'/filelib.php');

$userrole = get_atalrolenamebyid($USER->msn);
if($userrole!="admin"){
	//Only NitiAdmin can create/edit a Info.
	redirect($CFG->wwwroot.'/my');
}

$id = optional_param('id', -1, PARAM_INT);    // User id; -1 if creating new school.
$PAGE->set_url('/atalfeatures/createschool.php', array('id' => $id));

//Heading
$PAGE->set_pagelayout('standard');
if($id==-1){
	$strmessages = "Add School<br></br>";
	$PAGE->set_heading("Add School Details");
} else{
	$strmessages = "Update School Details<br></br>";
	$PAGE->set_heading("Update School Details");
}

$PAGE->set_title("{$SITE->shortname}: $strmessages");

$frmobject = new CreateSchoolForm(null, array('id' => $id));
if($id!==-1){
	$school = $DB->get_record('school', array('id'=>$id), '*');
	$stateid =  $DB->get_record('city', array('id'=>$school->cityid), '*');
	$school->state =$stateid->stateid;
	$school->city = $school->cityid;
	$school->schoolid = $school->id;
	$j = $frmobject->set_data($school);
}
if ($frmobject->is_cancelled()) {
    // The form has been cancelled, take them back to what ever the return to is.
	// redirect($returnurl);
} else if ($data = $frmobject->get_data()) {
		// Process if data submitted.
		$record1 = new stdClass();
		$record1->name=  ucfirst($data->name);
		$record1->cityid = $data->city;
		$record1->address = $data->address;
		$record1->atl_id = $data->atl_id;
		$record1->school_emailid = $data->school_emailid;
		$record1->phone = $data->phone;
		$record1->principal_email = $data->principal_email;
		$record1->principal_phone = $data->principal_phone;
		$record1->principal_name = ucfirst($data->principal_name);
		if ($data->flag=='add') {
		// Add School Details
		try
		{
			$lastinsertid = $DB->insert_record('school', $record1); 
			$urltogo = new moodle_url('/atalfeatures/schooldetail.php', array('id' => $lastinsertid)); // Move To School Detail Page
			redirect($urltogo);
		}
		catch(Exception $e)
		{
			echo $e->getMessage();die;
		}
    } else {
		//Update School Details
		$record1->id=   $data->schoolid;
		$updatedid = $DB->update_record('school', $record1);
		$urltogo = new moodle_url('/atalfeatures/schooldetail.php', array('id' => $record1->id)); // Move To School Detail Page
		redirect($urltogo);
    }
} 
echo $OUTPUT->header();
echo $OUTPUT->heading($strmessages);
$content.= $frmobject->render();
echo $content;
echo $OUTPUT->footer();
?>
