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
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 29-11-2018
 * @Description: Delete DB Record, session feedback (Mentor)
*/

require_once('../config.php');
include_once(__DIR__ .'/locallib.php');
require_login(null, false);
$userrole = get_atalrolenamebyid($USER->msn);
if (isguestuser() || $userrole!="admin") {
    redirect($CFG->wwwroot);
}

$id = optional_param('id', -1, PARAM_INT);
$flag = optional_param('flag', 0, PARAM_INT);
//flag 0 - do nothing, 1 - delete record.
$show_form_status = false;
$content='';
$returnurl = $CFG->wwwroot.'/mentor/deletesession.php';

//Heading
$PAGE->set_url('/mentor/deletesession.php', array('id' => $id));
$PAGE->set_title("{$SITE->shortname}");
$PAGE->set_heading("Delete Session");
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');

$message='';
if($id!==-1 && $flag==1){
	//Delete session
	$result = delete_mentorsession($id);
	if($result){
		$message='
		<span id="user-notifications"><div class="alert alert-info alert-block fade in " role="alert">
		<button type="button" class="close" data-dismiss="alert">×</button>
		Session Record Deleted Successfully.
		</div></span>
		';
	}
}
$content.='<div style="margin-bottom:2%;">'.$message.'</div>';
//Show data;
$start = 0;
$limit = 10;
$sql="SELECT m.id,m.mentorid,u.email,m.dateofsession,m.timecreated FROM {mentor_sessionrpt} m JOIN {user} u ON m.mentorid=u.id limit $start,$limit";
$result = $DB->get_records_sql($sql);	
if(count($result)>0){
	$content.="<table style='border: 1px solid black;width:80%;'>
	<tr>
	<th>SessionId</th>
	<th>MentorId</th>
	<th>MentorEmail</th>
	<th>Session Date</th>
	<th>Create Date</th>
	<th>Action</th>
	</tr>";
	foreach($result as $keys=>$values){
		$link = $returnurl."?flag=1&id=".$values->id;
		$date_ofsession = date("d-m-Y",$values->dateofsession);
		$createdon = date("d-m-Y",$values->timecreated);
		$content.="<tr><td>$values->id</td><td>$values->mentorid</td><td>$values->email</td><td>$date_ofsession</td><td>$createdon</td><td><a href='$link'>Delete</a></td></tr>";
	}
	$content.="</table>";
}

echo $OUTPUT->header();
echo $OUTPUT->heading("Delete Mentor Reported Sessions");
echo $content;
echo $OUTPUT->footer();
?>
