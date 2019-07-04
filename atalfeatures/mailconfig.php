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
 * @CreatedBy: Jothi (IBM)
 * @CreatedOn: 23-04-2018
 * @Description: Configuring Email Template for Sucessfull registration mail and 
  other features which require sent mail to stakeholders.
*/

require_once('../config.php');
require_once('render.php');
include_once(__DIR__ .'/mailtemplateform.php');
require_login(null, false);
if (isguestuser()) {
    redirect($CFG->wwwroot);
}
require_once($CFG->libdir.'/filelib.php');
$userrole = get_atalrolenamebyid($USER->msn);
$PAGE->set_url('/atalfeatures/mailconfig.php');
$show_form_status=false;
//Heading
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$strmessages = "Configure Mailing Template";
$PAGE->set_title("{$SITE->shortname}: $strmessages");
$PAGE->set_heading("Configure Mailing Template");
echo $OUTPUT->header();
$formobj = new MailTemplateForm();

//send_welcomemail(5,4,'Jothi Mentor','jothi1105@gmail.com','jothi','mypassword');
//$result = $DB->get_record('mailconfig_template',array('id'=>1));
$setdata = new StdClass();
$jsonString = file_get_contents('mailtemplate.json');
$newdata = json_decode($jsonString, true);
$setdata->mentor_email_template['text']= $newdata['mentor'];
$setdata->school_email_template['text']= $newdata['school'];
$setdata->student_email_template['text']= $newdata['student'];
$setdata->guest_email_template['text'] = $newdata['guest'];
$formobj->set_data($setdata);
if ($formobj->is_cancelled()) {
    // The form has been cancelled, take them back to what ever the return to is.
	// redirect($returnurl);
} else if ($data = $formobj->get_data()) {
	$record = new Stdclass();
	($data->mentor_email_template['text']!='')?$record->mentor =$data->mentor_email_template['text']:$record->mentor ='';
	($data->school_email_template['text']!='')?$record->school =$data->school_email_template['text']:$record->school ='';
	($data->guest_email_template['text']!='')?$record->guest =$data->guest_email_template['text']:$record->guest ='';
	($data->student_email_template['text']!='')?$record->student =$data->student_email_template['text']:$record->student ='';
	$jsonString = file_get_contents('jsonFile.json');
	$newdata = json_encode($record, true);
	$status = file_put_contents('mailtemplate.json', $newdata);
	
	if($status)
		$show_form_status = true;
	
} 
$alert_box='';
if($show_form_status)
{
	$alert_box='<div class="alert alert-success">
				  <strong>Updated Successfully! </strong><button class="close" type="button" data-dismiss="alert">×</button></a>
		</div>';
}
echo $OUTPUT->heading("Configure Email Template");
echo $alert_box;
echo $content = $formobj->render();
echo $OUTPUT->footer();
?>
<script type="text/javascript">
require(['jquery'], function($) {
});
</script>

