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
 * @CreatedOn: 17-04-2018
 * @Description: Scorm Listing Page
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
$PAGE->set_url('/atalfeatures/viewscorm.php');

//Heading
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$strmessages = "Training Tutortials";
$PAGE->set_title("{$SITE->shortname}: $strmessages");
$PAGE->set_heading("Training Tutortials");

echo $OUTPUT->header();
echo $OUTPUT->heading("Mandatory Training Tutorials");
$renderObj = new AtalFeatures(0);
$content = $renderObj ->getScormlist();
echo $content;
echo $OUTPUT->footer();

?>
<script type="text/javascript">
require(['jquery'], function($) {
});
</script>
