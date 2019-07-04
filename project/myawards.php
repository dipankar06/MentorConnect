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
 * @CreatedOn: 20-03-2018
 * @Description: Display List of Badges, Awards & Recognitions issue to LoggedIn user
 Show recipient Badges of this LoggedIn user (Mentor,Student & ATLChief)
*/

require_once(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/my/lib.php'); //To display RHS Block

redirect_if_major_upgrade_required();
require_login(null, false);
if (isguestuser()) {
    redirect($CFG->wwwroot);
}

$userid = optional_param('id', 0, PARAM_ALPHANUM);
$userrole = get_atalrolenamebyid($USER->msn);

include_once(__DIR__ .'/libbadge.php');

$userid = $USER->id;  // Owner of the page
$context = context_user::instance($USER->id);
$PAGE->set_blocks_editing_capability('moodle/my:manageblocks');
$header = fullname($USER);
if($userrole!="mentor"){
	$pagetitle = "Awards Recieved";
} else{
	$pagetitle = "Recognitions Achived";
}

// Get the My Moodle page info.  Should always return something unless the database is broken.
if (!$currentpage = my_get_page($userid, MY_PAGE_PRIVATE)) {
    print_error('mymoodlesetup');
}

// Start setting up the page
$params = array();
$PAGE->set_context($context);
$PAGE->set_url('/project/myawards.php', $params);
$PAGE->set_pagelayout('project');
$PAGE->set_pagetype('my-index');
$PAGE->blocks->add_region('content');
$PAGE->set_subpage($currentpage->id);
$PAGE->set_title("{$SITE->fullname}");
$PAGE->set_heading($header);


echo $OUTPUT->header();

//Center Page
$output = get_mybadges($pagetitle , $userrole);
echo $output;

echo $OUTPUT->footer();

?>