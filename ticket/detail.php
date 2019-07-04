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

/* @package: core_ticket
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 24-07-2018
 * @Description: Ticketing System Create New Ticket Page
*/

require_once('../config.php');

require_login(null, false);
if (isguestuser()) {
    redirect($CFG->wwwroot);
}
$id = optional_param('id', 0, PARAM_INT);

require_once('lib.php');
require_once('render.php');
$userrole = get_atalrolenamebyid($USER->msn);
$statuslist = get_allstatus();
$showdetailpage = true;
if($userrole!="admin"){
	$showdetailpage = is_ticketcreatedbyuser($id);
}

$PAGE->set_url('/ticket/detail.php');
//Heading
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$strmessages = "Ticketing System";
$PAGE->set_title("{$SITE->shortname}");
$PAGE->set_heading($strmessages);

echo $OUTPUT->header();
echo '<div class="card-block"><h1>
	<a href="'.$CFG->wwwroot.'/ticket" class="btn btn-primary pull-right goBack">Back</a>
	</h1></div>';	

echo $OUTPUT->heading($heading);
if($showdetailpage){
	$renderObj = new techticket_render($statuslist);
	$content = get_ticketdetail($id,$renderObj);
} else{
	$content = 'You can only view Ticket raised by you';
}

echo $content;
echo $OUTPUT->footer();
?>
