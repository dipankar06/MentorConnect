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

/* @package: core_mentor
 * @CreatedBy: Jothi (IBM)
 * @CreatedOn: 21-11-2018
 * @Description: Mentor Sessions Landing Page
*/

require_once('../config.php');	
require_once('../nitiadmin/render.php');
require_once('../nitiadmin/lib.php');
require_once('render.php');

require_login(null, false);
if (isguestuser()) {
    redirect($CFG->wwwroot);
}
require_once($CFG->libdir.'/filelib.php');
$userrole = get_atalrolenamebyid($USER->msn);
if($userrole!=="mentor"){
	redirect($CFG->wwwroot.'/my');
}

//Heading
$PAGE->set_url('/mentor/mysession.php');
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$strmessages = "My Sessions";
$PAGE->set_title("{$SITE->shortname}: $strmessages");
$PAGE->set_heading("My Sessions");

echo $OUTPUT->header();
$back='<div class="card-block">
	<h1>
	<a class="btn btn-primary pull-right goBack">Back</a>
	</h1>
	</div>';
//echo $back;
$renderObj = new MentorRender();
$content = $renderObj->getMentorSessionsHtml($USER->id);
echo $content;
echo $OUTPUT->footer();
?>
<script type="text/javascript">
require(['jquery'], function($) {

});
</script>
