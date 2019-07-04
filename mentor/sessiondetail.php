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
 * @CreatedOn: 22-11-2018
 * @Description: Mentor Report Your Sessions Detail page
*/

require_once('../config.php');	

require_login(null, false);

if (isguestuser()) {
    redirect($CFG->wwwroot);
}
$id = optional_param('id', 0, PARAM_INT); //Session id
$key = optional_param('key', '', PARAM_ALPHANUM);

if(!empty($key))
		$id = encryptdecrypt_userid($key,"de");

$userrole = get_atalrolenamebyid($USER->msn);
require_once('render.php');
require_once('lib.php');
require_once('locallib.php');

//Heading
$PAGE->set_url('/mentor/sessiondetail.php', array('id' => $id));
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$strmessages = "Session Detail";
$PAGE->set_title("{$SITE->shortname}: $strmessages");
$PAGE->set_heading("Mentor Session Detail");

//Only admin & mentor who create this session can view Details.
if ($userrole=="admin" || $userrole=="mentor") {
	$checkflag = true;
	if($userrole=="mentor"){
		if(check_isMentorSession($id)==false)
			$checkflag = false;
	}
} else{
	$checkflag = false;
}

echo $OUTPUT->header();
if ($checkflag) {
	$renderObj = new MentorRender();
	$recordset = get_mentorsession_dbdata($id);
	$content = get_mentorsession_details($recordset,$id,$renderObj);
	//Get Session Files: *****
	$output1="<div style='margin-left:4%;'><table><tr><td width='60%' valign='top'><ul class='profilesearch' style='list-style-type:none;'>";
	$output1.="<li class='details' style='padding-top:1%'><p><h7>Pictures:</h7>";
	$user_id = ($userrole=="admin")?$recordset->mentorid:$USER->id;
	$context = context_user::instance($user_id, MUST_EXIST);
	$fs = get_file_storage();
	$files = $fs->get_area_files($context->id, 'mentorsession_file', 'files_1', $id, "filename", false);
	foreach ($files as $file) {
		$path = '/' . $context->id . '/mentorsession_file/files_1/' .$file->get_itemid() . $file->get_filepath() . $file->get_filename();
		$url = file_encode_url("$CFG->wwwroot/pluginfile.php", $path, false);
		$output1.= "<p><img src='$url' width='60'></p>"; ///Display image in browser..	
	}
	$output1.= "</li></ul></td></tr></table></div>";
	$content.= $output1;
} else{
	$content = '<div class="mgtop">You cannot view session details.</div>';
}
echo $content;

echo $OUTPUT->footer();
?>