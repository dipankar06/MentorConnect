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
require_once('edit_form.php');

$userrole = get_atalrolenamebyid($USER->msn);
$statuslist = get_allstatus();

// First create the form.
$args = array(
    'ticketid' => $id,
	'category'=> get_ticketcategory()
);
$frmobject = new ticketform(null, $args);
$heading = ($id==0)?"Create a Ticket":"Edit a Ticket";

if ($frmobject->is_cancelled()) {
    // The form has been cancelled, take them back to what ever the return to is.
    redirect($CFG->wwwroot.'/ticket/create.php');
} else if ($data = $frmobject->get_data()) {
	if ($data){
		$usrmsg = "update";
		$cm = new stdClass();
		foreach ($data as $key => $value) {
			$cm->$key = $value;
		}
		$cm->createdby = $USER->id;
		$cm->timemodified = time();
		$cm->statusid = 1;
		$cm->assigned_to = getnitiadminid();
		if($data->id==0){
			$cm->timecreated = time();
			$cm->latest_comment = NULL; //$cm->description ;
			$usrmsg = "add";
			$cm->id = $DB->insert_record('tech_ticket', $cm);
			if($cm->id>0){
				ticket_sentmail($cm);
			}
		} else{
			$cm->id = $data->id;
			$DB->update_record('tech_ticket', $cm);
		}
		if(!empty($cm->id)){
			$SESSION->ticketflag = $usrmsg;
			redirect($CFG->wwwroot.'/ticket');
		}
	}
}

$PAGE->set_url('/ticket/create.php');
//Heading
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$strmessages = "Ticketing System";
$PAGE->set_title("{$SITE->shortname}");
$PAGE->set_heading($strmessages);

echo $OUTPUT->header();
echo '<div class="card-block"<h1>
	<a class="btn btn-primary pull-right goBack">Back</a>
	</h1></div>';	

echo $OUTPUT->heading($heading);
$content = '';
$content.= html_writer::start_tag('div', array('class' => 'createproject'));
$content.= $frmobject->render();
$content.= html_writer::end_tag('div');
echo $content;
echo $OUTPUT->footer();
?>
