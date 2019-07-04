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
 * @CreatedOn: 15-02-2018
 * @Description: School Detail Page
*/

require_once('../config.php');
require_once('render.php');
require_login(null, false);

if (isguestuser()) {
    redirect($CFG->wwwroot);
}
require_once($CFG->libdir.'/filelib.php');

$userrole = get_atalrolenamebyid($USER->msn);
$id = optional_param('id', 0, PARAM_INT);    // User id; -1 if creating new school.

$PAGE->set_url('/atalfeatures/schooldetail.php', array('id' => $id));

//Heading
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$strmessages = "School Details";
$PAGE->set_title("{$SITE->shortname}: $strmessages");
$PAGE->set_heading("School Details");

echo $OUTPUT->header();
// Now the page contents.
$records = $DB->get_record('school', array('id' => $id));
if (count($records)==1) 
{
	$featureObj = new AtalFeatures($id);
	$featureObj->renderSchoolDetails($records);
}

echo $OUTPUT->footer();

?>
<script type="text/javascript">
require(['jquery'], function($) {
	$('#searchstudent').keyup(function(e){
		$('#studentname li').show();
		var name = $('#searchstudent').val();
		if(name!='')
			$('#studentname li:not(":contains(' + name + ')")').hide();
		else
		$('#studentname li').show();
	});
});
</script>
