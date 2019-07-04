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

/* @package: core_nitiadmin
 * @CreatedBy: Jothi (IBM)
 * @CreatedOn: 20-09-2018
 * @Description: Custom Settings Page
*/

require_once('../config.php');
require_once('render.php');
require_once('lib.php');
$id = optional_param('id', 0, PARAM_INT);
include_once(__DIR__ .'/customsettings_form.php');
require_login(null, false);
if (isguestuser()) {
    redirect($CFG->wwwroot);
}
require_once($CFG->libdir.'/filelib.php');
$context = context_system::instance();
$PAGE->set_context($context);
require_capability('moodle/site:config', $context);
$PAGE->set_url('/nitiadmin/settings.php');

//Heading
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$strmessages = "NITI Administration : Site Settings";
$PAGE->set_title("{$SITE->shortname}: $strmessages");
$PAGE->set_heading("NITI Administration : Site Settings");


$PAGE->set_heading("Update Event");

echo $OUTPUT->header();
echo '<div class="card-block"<h1>
	<a class="btn btn-primary pull-right goBack">Back</a>
	</h1></div>';
echo $OUTPUT->heading("Site Settings");
$content = '';
if($show_form_status)
{
	$alert_box='<div class="alert alert-success">
	<strong>Updated Successfully! </strong><button class="close" type="button" data-dismiss="alert">×</button></a>
	</div>';
	echo $alert_box;
}
echo $formobj->render();
echo "<p><h4> Custom Settings Keys & Values</h4></p>";
echo "<div class='card-block'>
<table class='table table-stripped'>
<th> Settings Key </th>
<th> Settings Value </th>";
foreach($custom_settings as $key=>$value)
{
	echo "<tr>";
	echo "<td>".$value->atl_key."</td>";
	echo "<td>".$value->atl_value."</td>";
	echo "<td> <a href='../nitiadmin/settings.php?id=".$value->id."'> Edit </a></td>";
	echo "</tr>";
}
echo "</table>";
echo $OUTPUT->footer();
?>
<script type="text/javascript">
require(['jquery'], function($) {
	$(document).ready(function()
	{
		var currentURL = document.URL;
		var pieces = currentURL.split("?");
		if (pieces[1]) {
                var param = pieces[1].split("=");
				if(param)
				{
					var result= param[1];
					$('#id_schoolfilter').val(result);
				}
            }
	});
	$("body").on("click", "#studentreport-filter-email", function(event){
		var email=$('#id_studentemail').val();
		var school=$('#id_schoolfilter').val();
		if(school=='' && email=='')
			return false;
		if(email)
		{
			var mode='filter-student-email';
			triggerAjaxRequest(1,mode,email);
		}
		else if(school)
		{
			var mode='filter-student-schooldetail';
			triggerAjaxRequest(1,mode,school);
		}
	});
	$("body").on("click", "#studentreport-reset", function(event){
		var mode='allstudent-list';
		$('#id_studentemail').val('');
		$('#id_schoolfilter').val('');
		triggerAjaxRequest(1,mode,'all');
	});
	$("body").on("click", ".movetopage-student-report", function(event){
		var mode='allstudent-list';
		var email=$('#id_studentemail').val();
		var school=$('#id_schoolfilter').val();
		if(school=='' && email=='')
			triggerAjaxRequest(this.id,mode,'all');
		if(school!='')
		{
			var mode='filter-student-schooldetail';
			triggerAjaxRequest(this.id,mode,school);
		}
	});
	function triggerAjaxRequest(id,mode,val)
	{
		var request = $.ajax({
			  url: "ajax.php",
			  method: "POST",
			  data:  { 'id' : id,'mode':mode,'filterby':val},
			  dataType: "html",
			  beforeSend: function() {
					$(".overlay").show();
					$("#atlloaderimage").show();
				},
			});
			request.done(function( msg ) {		
				$( "#table-content-wrapper" ).html(msg);
				$(".overlay").hide();
				$("#atlloaderimage").hide();
			});
			request.fail(function( jqXHR, textStatus ) {
			  alert( "Request failed: " + textStatus );
			}); 
	}
});
</script>
