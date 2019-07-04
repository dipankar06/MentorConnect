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

/* @package: core_nitiadmin
 * @CreatedBy: Jothi (IBM)
 * @CreatedOn: 04-10-2018
 * @Description: Student Activty Page
*/

require_once('../config.php');
require_once('render.php');
require_once('lib.php');
require_login(null, false);
if (isguestuser()) {
    redirect($CFG->wwwroot);
}
require_once($CFG->libdir.'/filelib.php');
$userrole = get_atalrolenamebyid($USER->msn);
if($userrole != 'admin')
	redirect($CFG->wwwroot);
$PAGE->set_url('/nitiadmin/studentreport.php');

//Heading
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$strmessages = "NITI Administration : Student Report";
$PAGE->set_title("{$SITE->shortname}: $strmessages");
$PAGE->set_heading("NITI Administration : Student Report");

echo $OUTPUT->header();
echo '<div class="card-block"<h1>
	<a class="btn btn-primary pull-right goBack">Back</a>
	</h1></div>';
echo $OUTPUT->heading("Student Activity Report");
$content = '';
$renderObj = new NitiAdministrationRender();
$content = $renderObj ->getStudentReportHtml();
echo $content;
echo $OUTPUT->footer();
?>
