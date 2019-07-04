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

require_once('../config.php');

require_login(null, false);
if (isguestuser()) {
    redirect($CFG->wwwroot);
}
require_once($CFG->libdir.'/filelib.php');
$userrole = get_atalrolenamebyid($USER->msn);
include_once(__DIR__ .'/projectlib.php');

$innovationtxt = ($userrole=="mentor") ? "Approved Innovations" : "Innovations";
//Heading
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$PAGE->set_url('/project/myinnovations.php', $params);
$PAGE->set_title("{$SITE->fullname}");

$filters = innovationright_block();
$PAGE->blocks->add_fake_block($filters, $pos=BLOCK_POS_RIGHT);

echo $OUTPUT->header();

$output='';
//$renderobj = new innovation_render($USER->id, $USER->msn);
$output.='<h1>
    <a class="btn btn-primary pull-right" href="'.$CFG->wwwroot.'/my">Back</a>
    </h1>';
$output.='<div><h3>'.$innovationtxt.'.</h3></div></br>';
$output.='<table class="table">';
$condition = " AND ue.status=1 ";
$data = myenrollprojects($condition);
if(count($data)>0){
foreach($data as $key=>$values){
$detailpagelink = $CFG->wwwroot.'/search/profile.php?key='.encryptdecrypt_userid($values[0]['userid'],"en");
$output.= '<tr>
	<td>
	<div class="project">
	<h5>Innovation : <a href="'.$values[0]['projectdetaillink'].'" class="plink">'.ucwords($values[0]['name']).'</a></h5></div>
	<div>Summary : '.$values[0]['summary'].'</div><div>Created By : <a href="'.$detailpagelink.'">'.$values[0]['firstname'].' '.$values[0]['lastname'].'</a></div>
	<div>Status : <b>'.$values[0]['status'].'</b></div>
	</td>
	</tr>';
}
} else {
	$output.= '<tr><td>No Innovations Found!</td></tr>';
}
$output.='</table>';
//Center Page
echo $output;
echo $OUTPUT->footer();
?>