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
 * @CreatedOn: 09-01-2018
 * @Description: Display Project Detail Page. 
 Detail page for specific project i.e project collaboration
 This Page (collaboration) will be accessable to mentors & Mentees
*/

require_once('../config.php');

require_login(null, false);
if (isguestuser()) {
    redirect($CFG->wwwroot);
}
require_once($CFG->libdir.'/filelib.php');
$id = optional_param('id', 0, PARAM_ALPHANUM); // Project id.
$assetmsg = optional_param('flag', 0, PARAM_INT);

if(empty($id)){
	redirect($CFG->wwwroot.'/my');
}

$id = encryptdecrypt_projectid($id,'de');
	
$is_enrol = is_myenrolproject($id,$USER->id);
if($is_enrol===false){
	redirect($CFG->wwwroot.'/my'); //can't edit other school projects..
}

include_once(__DIR__ .'/render.php');
include_once(__DIR__ .'/lib.php');
include_once(__DIR__ .'/addresource_form.php');

//Check for POST Data from project collaboration;
if(isset($_POST['detailflag'])){
	//When project gets created it does not have any forum post..
	//So users need to add some things to start the first project Chat..
	$postcontent = $_POST['message'];
	if(!empty(trim($postcontent))){
		savefirstprojectpost($_POST,$_POST['sid']);
	}
} else{
	if(isset($_POST['assetflag'])){	
		//Check for POST Data from project Asset Upload;
		//when user uploads a Files as asset.		
		saveprojectasset();
	}
}

$url = new moodle_url('/project/detail');
$PAGE->set_url($url);

//Heading
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$strmessages = "Detail";
$PAGE->set_title("{$SITE->fullname}");
$PAGE->set_heading("");

$renderobj = new project_render($USER->id, $USER->msn);

$module = $DB->get_record('modules', array('name'=>'resource'));
$frmobject = new mod_resource_mod_form($CFG->wwwroot.'/project/resource.php?id='.$id,array('course'=>$id,'module'=>$module->id));
// RHS Block.
$filters = projectside_block_detail($renderobj, $id);
$PAGE->blocks->add_fake_block($filters, $pos=BLOCK_POS_RIGHT);

echo $OUTPUT->header();

$output = '';
if($assetmsg > 0){
$uploadassestmsg = ($assetmsg==1)?'Asset Uploaded Successfully':'Error in Asset Upload !';
$output = '
<span id="user-notifications"><div class="alert alert-info alert-block fade in " role="alert">
    <button type="button" class="close" data-dismiss="alert">×</button>'.$uploadassestmsg.'   
</div></span>
';
}

$output.= showprojectdetail($renderobj, $id, $frmobject);

// Now the page contents.

echo $output;

echo $OUTPUT->footer();
?>