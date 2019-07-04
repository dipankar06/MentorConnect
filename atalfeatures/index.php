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
 * @CreatedOn: 07-01-2018
 * @Description: mentor under Maintenance Page.
*/

if (!file_exists('../config.php')) {
    header('Location: install.php');
    die;
}

require_once('../config.php');

require_once($CFG->libdir .'/filelib.php');

require_login(null, false);

redirect_if_major_upgrade_required();

$urlparams = array();
if (!empty($CFG->defaulthomepage) && ($CFG->defaulthomepage == HOMEPAGE_MY) && optional_param('redirect', 1, PARAM_BOOL) === 0) {
    $urlparams['redirect'] = 0;
}
$PAGE->set_url('/atalfeatures', $urlparams);
$PAGE->set_pagelayout('frontpage');

// Prevent caching of this page to stop confusion when changing page after making AJAX changes.
$PAGE->set_cacheable(false);
user_accesstime_log();

$hasmaintenanceaccess = has_capability('moodle/site:maintenanceaccess', context_system::instance());

//print_maintenance_message();

$PAGE->set_pagetype('site-index');
$PAGE->set_docs_path('');
$editing = $PAGE->user_is_editing();
$PAGE->set_title($SITE->fullname);
//$PAGE->set_heading($SITE->fullname);

echo $OUTPUT->header();

echo "This Module is Under development";

echo $OUTPUT->footer();
