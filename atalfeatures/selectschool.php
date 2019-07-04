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
 * @CreatedOn: 09-10-2018
 * @Description: Page for Mentors to select schools on his/her choice
*/

require_once('../config.php');
require_once('render.php');
require_once(__DIR__ .'/selectschool_form.php');
require_once('lib.php');
require_login(null, false);
if (isguestuser()) {
    redirect($CFG->wwwroot);
}
require_once($CFG->libdir.'/filelib.php');
$show_form_status = false;
$userrole = get_atalrolenamebyid($USER->msn);
$id = optional_param('id', 0, PARAM_INT);    // User id; -1 if creating new school.

$PAGE->set_url('/atalfeatures/selectschool.php');

//Heading
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$strmessages = "Choose your School";
$PAGE->set_title("{$SITE->shortname}: $strmessages");
$PAGE->set_heading("Choose your School");

echo $OUTPUT->header();
// Now the page contents.

$schoolformObj = new SelectSchoolForm();

if ($schoolformObj->is_cancelled()) {
    // The form has been cancelled, take them back to what ever the return to is.
	// redirect($returnurl);
} else if ($data = $schoolformObj->get_data()) {
	$newdata =new stdClass();

	$newdata->userid = $USER->id;
	$newdata->status =1;
	$schoolid_arr = explode(',',$data->schoolid);
	if(count($schoolid_arr) > 1)
	{
		foreach($schoolid_arr as $value)
		{
			$newdata->schoolid =$value ;
			$check = $DB->get_record('mentor_schoice',array('userid'=>$USER->id,'schoolid'=>$value));
			if(!$check)
				$DB->insert_record('mentor_schoice', $newdata);
			else
				continue;
		}
	}
	else
	{
		$newdata->schoolid =  $data->schoolid ;
		$DB->insert_record('mentor_schoice', $newdata);
	}
	$show_form_status = true;
}
if($show_form_status)
{
	echo '<div class="alert alert-success">
				  <strong>Thank you ! Request has been sent to Admin . We will update you shortly! </strong><button class="close" type="button" data-dismiss="alert">×</button></a>
		</div>';
}
echo $schoolformObj->render();
echo $OUTPUT->footer();

?>
<script type="text/javascript">
require(['jquery'], function($) {
	$('#id_cityid').change(function()
	{
		var cityid = $(this).val();
		var data = {'id' :cityid,'mode':'loadschool-by-cityid-mchoice'};
		var selectField = $('#id_school');
		selectField.empty();
		selectField.append($("<option />").val("").text("Select Atal Schools"));
		selectField.append($("<option />").val("load").text("Loading ... "));
		var request = $.ajax({
		  url: "ajax.php",
		  method: "POST",
		  data:  data,
		  dataType: "json",
		  success : function(replydata) {
				var opts = replydata.replyhtml;         
				$.each(opts, function(i, item) {
					var itemtext = item.schoolname + ' - ' + item.cityname;
				  $('#id_school').append($("<option />").val(item.id).text(itemtext)); 
				});
				document.getElementsByName("city")[0].value = cityid;
				 $('#id_school option[value="load"]').remove();
			},
			faliure : function (replydata)
			{
				alert("Something has gone Wrong! Please try again later!");
			}
		});
	});
	$('#id_school').click(function()
	{
        var schools = [];
        $.each($("#id_school option:selected"), function(){            
            schools.push($(this).val());
        });
		document.getElementsByName("schoolid")[0].value = schools.join(", ");
	});
});
</script>
