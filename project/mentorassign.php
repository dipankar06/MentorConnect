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
 * @CreatedOn: 17-03-2018
 * @Description: Display Ongoing , Completed & Pending Approval Projects
*/

require_once('../config.php');

require_login(null, false);
if (isguestuser()) {
    redirect($CFG->wwwroot);
}
require_once($CFG->libdir.'/filelib.php');
$userrole = get_atalrolenamebyid($USER->msn);
$cancreateproject = false;
if($userrole=="mentor" ){
	$cancreateproject = true;
}
if($cancreateproject===false){
	//Only Mentor can view this page
	redirect($CFG->wwwroot.'/my');
}

include_once(__DIR__ .'/render_innovation.php');
include_once(__DIR__ .'/lib.php');
include_once(__DIR__ .'/projectlib.php');

$url = new moodle_url('/project/mentorassign');
$PAGE->set_url($url);

//Heading
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$strmessages = "Innovations";
$PAGE->set_title("{$SITE->fullname}");
$PAGE->set_heading("Innovations");

$innrenderobj = new innovation_render($USER->id, $USER->msn);

echo $OUTPUT->header();
$output = showassignproject_mentor($innrenderobj);

// Now the page contents.

echo $output;

echo $OUTPUT->footer();

?>