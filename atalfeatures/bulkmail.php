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

/* @package core_project
 * @CreatedBy:Jothi (IBM)
 * @CreatedOn:10-09-2018
 * @Description: To Send a mail to Bulk Mail to Users
*/

/*
Mentors
<login>
<password>
<firstname>
<lastname>
<allotedschoolname>
<allotedschoolcode>
 
Schools
<login>
<password>
<schoolname>
<schoolcode>
 
Students
<login>
<password>
<firstname>
<lastname>
<schoolname>

Filters for Mentors

-all mentors who have not attempted login even once
-all mentors who have not finished updating their profile
-all mentors who have not started the mentor training
-all mentors who have completed the mentor training
-all mentors who have not sent any session request

Filters for schools

-all schools who have not attempted login even once
-all schools who have not finished updating their profile
*/
require_once('../config.php');
require_once('render.php');
include_once(__DIR__ .'/bulkmail_form.php');
include_once(__DIR__ .'/lib.php');
require_login(null, false);
if (isguestuser()) {
    redirect($CFG->wwwroot);
}
require_once($CFG->libdir.'/filelib.php');
$userrole = get_atalrolenamebyid($USER->msn);
$PAGE->set_url('/atalfeatures/bulkmail.php');
$show_form_status=false;
//Heading
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$strmessages = "Send Bulk Mail to User ";
$PAGE->set_title("{$SITE->shortname}: $strmessages");
$PAGE->set_heading("Send Bulk Mail to User");
echo $OUTPUT->header();
$formobj = new CreateMailForm();
if ($formobj->is_cancelled()) {
    // The form has been cancelled, take them back to what ever the return to is.
	// redirect($returnurl);
} else if ($data = $formobj->get_data()) {
		try
		{
			$subject=  ucfirst($data->subject);			
			$roleid = atal_get_roleidbyname($data->role);
			$sql = getSqlQuery_bulkmail($data->criteria_text);
			$user = $DB->get_records_sql($sql);
			$from = getCustomSettings('atl_email');
			if($from)
				$from = $from->atl_value;
			else
				$from = 'atlinno.net-niti@gov.in';
			if(count($user)>0){
					foreach($user as $key=>$value)
					{
						$useremail = new StdClass();
						$useremail->id = $value->id;		
						$useremail->deleted =0;
						$value->firstname = ucwords(strtolower($value->firstname));
						$mailbody= replaceBulkMail_Placeholder($data->mailbody['text'],$value,$data->role);
						if($data->ccyes)
						{
							//$email_arr = explode(",",$value->mentoreemails);
							$email_arr = array('0'=>'kumalata@in.ibm.com','1'=>'dichak78@in.ibm.com');
							$CFG->bccmails = $email_arr;
						}
						if($data->mailmode == 'test')
							$useremail->email = $data->email_test;
						else
							$useremail->email = 'jothip1@in.ibm.com';
							//$useremail->email = $value->email;
						email_to_user($useremail, $from,$subject,$mailbody); 
						//if($data->mailmode == 'test')
						break;
					}
					$show_form_status=true;
				}
				else
				{
					echo '<div class="alert alert-danger"><strong>No User Found to send mail</strong><button class="close" type="button" data-dismiss="alert">×</button></a></div>';
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
<script type="text/javascript">
require(['jquery'], function($) {
	var val = $('#id_role').val();
	if(val != 'mentor')
		updateCriteria();
	$('#id_submitbutton').click(function()
	{
		var criteria_val = $('#id_criteria').val();
		$("input[name=criteria_text]").val(criteria_val);
		var role = $("#id_role option:selected").text();
		var criteria = $("#id_criteria option:selected").text();
		if($("#id_mailmode_test").is(":checked"))
			var msg = "Ae you Sure you want to trigger a Test Mail?";
		else
		{
			if(criteria_val == 'no_mentor'  ||  criteria_val == 'no_school')  
				var msg = "Are you Sure you want to send a Bulk Mail to All "+role+"?";
			else
				var msg = "Are you Sure you want to send a Bulk Mail to All "+role+" who have "+criteria+"?";
		}
		var r = confirm(msg);
		if (!r)
			return false;
	}); 
	$('#id_role').change(function(e){
		updateCriteria();
	});
	function updateCriteria()
	{
		var val = $('#id_role').val();
		$('#id_criteria').empty();
		$('#id_criteria').append($("<option/>").val("load").text("Loading ... "));
		var request = $.ajax({
			url: "ajax.php",
			method: "POST",
			data: {'id':val,'mode':'getbulkmail_dropdown'},
			dataType: "json"
			});
		request.done(function(reply) {
				$.each(reply, function(i, obj) {
					$('#id_criteria').append($("<option />").val(i).text(obj)); 
						});
					$('#id_criteria option[value="load"]').remove();
			});
			request.fail(function( jqXHR, textStatus,errorMessage ){
				alert( "Request failed: " + textStatus + errorMessage );
			});
	}
});
</script>