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
 * @CreatedOn: 01-05-2018
 * @Description: Page that shows Video How to Use this Portal
*/

require_once('../config.php');

require_login(null, false);

if (isguestuser()) {
    redirect($CFG->wwwroot);
}
require_once($CFG->libdir.'/filelib.php');

$userrole = get_atalrolenamebyid($USER->msn);
$PAGE->set_url('/atalfeatures/help.php');
$userrole = get_atalrolenamebyid($USER->msn);

//Heading
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$strmessages = "Help";
$PAGE->set_title("{$SITE->shortname}: $strmessages");
$PAGE->set_heading("Help");

echo $OUTPUT->header();
echo $OUTPUT->heading("How to Use This Portal");

if($userrole=="admin"){
	//NitiAdmin
	$src = $CFG->wwwroot.'/mentorguides/video/nitiadminrecording.mp4';
} elseif($userrole=="mentor"){
	//Mentor
	$src = $CFG->wwwroot.'/mentorguides/video/mentorrecording.mp4';
} elseif($userrole=="incharge"){
	//Atal Incharge
	$src = $CFG->wwwroot.'/mentorguides/video/atlinchargerecording.mp4';
} else{
	//Student
	$src = $CFG->wwwroot.'/mentorguides/video/studentrecord.mp4';
}

if($userrole=="mentor"){
	$pdflink = $CFG->wwwroot.'/mentorguides/mentor-user-guide.pdf#zoom=100';
	$test = '<input type="hidden" id="id_frmcategory"><form id="mform2"></form>';
	$content = '<br><embed src="'.$pdflink.'" width="100%" height="580px" />'.$test;

} elseif($userrole=="incharge"){
	$pdflink = $CFG->wwwroot.'/mentorguides/atalincharge-user-guide.pdf#zoom=100';
	$test = '<input type="hidden" id="id_frmcategory"><form id="mform2"></form>';
	$content = '<br><embed src="'.$pdflink.'" width="100%" height="580px" />'.$test;

} elseif($userrole=="student"){
	$pdflink = $CFG->wwwroot.'/mentorguides/student-user-guide.pdf#zoom=100';
	$test = '<input type="hidden" id="id_frmcategory"><form id="mform2"></form>';
	$content = '<br><embed src="'.$pdflink.'" width="100%" height="580px" />'.$test;

} else{
$content = '<div style="margin-top:5%;margin-left:2%; width:90%;">
<video width="820" height="540" controls>
  <source src="'.$src.'" type="video/mp4">
  <source src="movie.ogg" type="video/ogg">
Your browser does not support the video tag.
</video>
</div>
';
}

echo $content;
echo $OUTPUT->footer();

?>