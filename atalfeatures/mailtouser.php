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
 * @CreatedOn: 23-08-2018
 * @Description: To Send a mail to Individual Users
*/

require_once('../config.php');
require_once('render.php');
include_once(__DIR__ .'/mailtouser_form.php');
require_login(null, false);
if (isguestuser()) {
    redirect($CFG->wwwroot);
}
require_once($CFG->libdir.'/filelib.php');
$userrole = get_atalrolenamebyid($USER->msn);
$PAGE->set_url('/atalfeatures/mailtouser.php');
$show_form_status=false;
//Heading
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$strmessages = "Send Mail to User ";
$PAGE->set_title("{$SITE->shortname}: $strmessages");
$PAGE->set_heading("Send Mail  to User ");
echo $OUTPUT->header();
$formobj = new CreateMailForm();
if ($formobj->is_cancelled()) {
    // The form has been cancelled, take them back to what ever the return to is.
	// redirect($returnurl);
} else if ($data = $formobj->get_data()) {
		try
		{
			$subject=  ucfirst($data->subject);			
			$from = "atlinno.net-niti@gov.in";
			$user = $DB->get_record('user',array('email'=>$data->email),'id');
			if($user){
					$useremail = new StdClass();
					$useremail->email = $data->email;					
					$useremail->id = $user->id;					
					$useremail->deleted =0;
					$mailbody=$data->mailbody['text'];
					email_to_user($useremail, $from,$subject,$mailbody); 
					$show_form_status=true;
				}
				else
				{
					echo '<div class="alert alert-danger"><strong>Email Id not Registered!</strong><button class="close" type="button" data-dismiss="alert">×</button></a></div>';
				}
			}
			catch(Exception $e)
			{
				echo $e->getMessage();die;
			}
		} 
$alert_box='';
if($show_form_status)
{
	$alert_box='<div class="alert alert-success">
	<strong>Mail Sent Successfully! </strong><button class="close" type="button" data-dismiss="alert">×</button></a>
	</div>';
}
echo $alert_box;
echo $formobj->render();
echo $OUTPUT->footer();
?>