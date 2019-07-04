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

/* @package: core_forum
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 13-04-2018
 * @Description: Show Misuse Report for Nitiadmin
*/

require_once('../config.php');

require_login(null, false);

if (isguestuser()) {
    redirect($CFG->wwwroot);
}

require_once($CFG->libdir.'/filelib.php');
require_once($CFG->dirroot . '/forum/lib.php');
require_once($CFG->dirroot . '/forum/locallib.php');

$url = new moodle_url('/forum/index.php');

$PAGE->set_url($url);

$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');

$strmessages = "Forum";
$PAGE->set_title("{$SITE->fullname}");
$PAGE->set_heading("Forum");

$SESSION->misusepostlist = 1;
// Now the page contents.
echo $OUTPUT->header();
echo $OUTPUT->heading("Forum");

if(get_atalrolenamebyid($USER->msn)=='admin'){
	$output = showmisusepost();
} else{
	$output = showforumfeed();
}
echo $output;

echo $OUTPUT->footer();

?>
