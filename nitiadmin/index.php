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
 * @CreatedOn: 20-06-2018
 * @Description: index
*/

require_once('../config.php');
require_login(null, false);
if (isguestuser()) {
    redirect($CFG->wwwroot);
}
require_once($CFG->libdir.'/filelib.php');
$userrole = get_atalrolenamebyid($USER->msn);
if($userrole != 'admin')
	redirect($CFG->wwwroot);
$PAGE->set_url('/nitiadmin/index.php');

//Heading
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$strmessages = "NITI Administration : Admin Page";
$PAGE->set_title("{$SITE->shortname}: $strmessages");
$PAGE->set_heading("NITI Administration : Admin Page");

echo $OUTPUT->header();
echo '<div class="card-block"<h1>
	<a class="btn btn-primary pull-right goBack">Back</a>
	</h1></div>';
echo $OUTPUT->heading("Admin Page");
$content = '<p>
Niti Admin Report Page Contains the following Report
<ul>
<li>Total Mentors in the Portal</li>
<li>Total Schools in the Potal</li>
<li>Total Students in the Portal</li>
<li>User Based Activity Reports
	<ul>
		<li>Mentor Activity Report</li>
		<li>School Activity Report</li>
	</ul>
</li>
</ul>
</p>';
echo $content;
echo $OUTPUT->footer();
?>
