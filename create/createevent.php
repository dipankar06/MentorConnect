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

/* @package: core_create
 * @CreatedBy: Jothi (IBM)
 * @CreatedOn: 7-04-2018
 * @Description: Create a New Event(NitiAdmin)
*/

require_once('../config.php');
require_login(null, false);
if (isguestuser()) {
    redirect($CFG->wwwroot);
}
require_once($CFG->libdir.'/filelib.php');

$userrole = get_atalrolenamebyid($USER->msn);
if($userrole!="admin"){
	//Only School-incharge can create/edit a student Info.
	redirect($CFG->wwwroot.'/my');
}
include_once(__DIR__ .'/render.php');
include_once(__DIR__ .'/lib.php');
include_once(__DIR__ .'/atalevent_form.php');
require_once($CFG->dirroot.'/forum/lib.php');

$PAGE->set_url('/create/createevent.php');

//Heading
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');

$id = optional_param('id', 0, PARAM_INT);    // User id: 0 if creating new school.
$heading = '';
if($id==0){
	$heading = 'Add New Event';
	$PAGE->set_title('ATL Innonet : Add New Event');
	$action_url = $CFG->wwwroot.'/create/createevent.php';
} else{
	$PAGE->set_title('ATL Innonet : Update Event');
	$heading = 'Update Event';
	$action_url = $CFG->wwwroot.'/create/createevent.php?id='.$id;
}
$PAGE->set_heading("Update Event");

$frmobject = new atalevent_form($action_url,array('id' => $id));
$show_form_status = false;

if($id!==0){
	$eventformval = frmcreateeventobject($id);
	$frmobject->set_data($eventformval);
}
if ($frmobject->is_cancelled()) {
    // The form has been cancelled, take them back to what ever the return to is.
    redirect($CFG->wwwroot.'/create/listevent.php');
} else if ($data = $frmobject->get_data()) {
    // Process data if submitted.
	$atal_variable = get_atalvariables();
	if ($data){
	    if (!empty($data->name)) {
			if($data->flag=="add"){
				//Save data of event created by Admin.
				$cm = new stdClass();
				foreach ($data as $key => $value) {
					$cm->$key = $value;
				}
				$cm->timemodified = time();
				$cm->userid = $USER->id;
				$cm->courseid = SITEID;
				$cm->format = 1;
				$cm->eventtype = 'site';
				$cm->modulename = 0;
				if($cm->duration > 0){
					$until_time = (int) ($cm->timedurationuntil - $cm->timestart);
					if ($until_time < 1){
						$until_time = 86400; //1day default;
					}
					$cm->timeduration = $until_time;
				}
				$cm->id = $DB->insert_record('event', $cm);
				if(!empty($cm->id)){
					$postid = 0;
					$cmnew = new stdClass();
					$cmnew->title = $cm->name;
					$cmnew->postfile = $cm->postfile;
					$info = file_get_draft_area_info($cm->postfile);
					$present = ($info['filecount']>0) ? '1' : '';
					if($present=='1'){
						$postid = frmaddeventimage($cmnew);
					}
					if($postid>0){
						$cm->parentid = $postid;
						$DB->update_record('event', $cm);
					}
					redirect($CFG->wwwroot.'/create/listevent.php');
				}
			}
			else
			{
				$data->timemodified = time();
				if($data->duration > 0){
					$until_time = (int) ($data->timedurationuntil - $data->timestart);
					if ($until_time < 1){
						$until_time = 86400; //1day default;
					}
					$data->timeduration = $until_time;
				}
				$DB->update_record('event', $data);
				//Update Image
				$info = file_get_draft_area_info($data->postfile);
				$present = ($info['filecount']>0) ? '1' : '';
				if($present=='1'){
					//New File added. Update this image.
					$freshparentrecord = $DB->get_record('event',array('id'=>$data->id),'parentid');
					if($freshparentrecord)
						$data->parentid = $freshparentrecord->parentid;
					else
						$data->parentid =0;
					$data->title = $data->name;
					$postid = frmaddeventimage($data);
					if($postid>0){
						$data->parentid = $postid;
						$DB->update_record('event', $data);
					}
				} else{
					//If User has removed image from file manager,Remove img from event and related post.
					if($data->parentid>0){
						remove_eventimage($data->id,$data->parentid);
						$data->parentid = 0;
						$DB->update_record('event', $data);
					}
				}			
				$show_form_status = true;
				$id = $data->id;				
			}
	    }
	}
	unset($data);
}
$alert_box='';
if($show_form_status)
{
	//Reset Form Data...25-May-2018
	$eventformval = frmcreateeventobject($id);
	$eventformval->parentid = $postid;
	$frmobject = new atalevent_form($action_url,array('id' => $id));
	$frmobject->set_data($eventformval);
	$alert_box='<div class="alert alert-success">
	<strong><button class="close" type="button" data-dismiss="alert">×</button>Updated Successfully! </strong></a>
	</div>';
}
echo $OUTPUT->header();
$backlink = $CFG->wwwroot.'/create/listevent.php';
echo $alert_box;
$back='<div class="card-block">
<h1>
  <a class="btn btn-primary pull-right" id="backbutton" data-href="'.$backlink.'" href="#">Back</a>
</h1>
</div>';
echo $back;
echo $OUTPUT->heading($heading);
$content = "<br></br>".$frmobject->render();
echo $content;
echo $OUTPUT->footer();
?>
<script type="text/javascript">
require(['jquery'], function($) {
	$(document).ready(function(){
		var link = $('#backbutton').attr('data-href');
		$('#backbutton').attr("href", link);
	});
});
</script>