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
 * @CreatedOn: 18-07-2018
 * @Description: Ticketing System
*/


require_once('../config.php');

require_login(null, false);
if (isguestuser()) {
    redirect($CFG->wwwroot);
}

require_once('render.php');
require_once('lib.php');

$userrole = get_atalrolenamebyid($USER->msn);
$PAGE->set_url('/ticket/index.php');
//Heading
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$strmessages = ($userrole=="admin")? "Users Tickets" : "Your Tickets";
$PAGE->set_title("{$SITE->shortname}");
$PAGE->set_heading($strmessages);

echo $OUTPUT->header();

if(isset($SESSION->ticketflag) && !empty($SESSION->ticketflag)){ //add update sucessfull msg
$usrmsg = ($SESSION->ticketflag=='add')?'added':'updated';
$SESSION->ticketflag = '';
echo '<div class="alert alert-info alert-block fade in " role="alert">
<button type="button" class="close" data-dismiss="alert">×</button>
    Ticket '.$usrmsg.' Successfully
</div>';
}
if($userrole=="admin"){
/*echo '<div class="card-block"<h1>
	<a class="btn btn-primary pull-right goBack">Back</a>
	</h1></div>';*/
}
echo $OUTPUT->heading($strmessages);
$content = '';
$statuslist = get_allstatus();
$renderObj = new techticket_render($statuslist);
$content = get_ticketlist($renderObj);
echo $content;

echo "<br><div>You can create a Ticket, if you face any problems or have any queries in this Portal. Solutions of your problem will be replied by AIM team, once they review your ticket</div>";
echo $OUTPUT->footer();
?>
