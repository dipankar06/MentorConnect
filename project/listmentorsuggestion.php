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
 * @CreatedOn: 12-04-2018
 * @Description: List Mentor Suggesstion on Schools (Niti Admin)
*/

require_once('../config.php'); //Include Global Cofiguration file
require_once('render.php'); //include create folder render file
require_once('lib.php'); //include create folder library file
require_once($CFG->libdir.'/filelib.php'); //Include global library

require_login(null, false);

if (isguestuser()) {
    redirect($CFG->wwwroot);
}
$userrole = get_atalrolenamebyid($USER->msn);
$PAGE->set_url('/project/listmentorsuggestion.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Mentor Connect : Mentor Suggestion');
$strmessages = "Mentor's Suggestion on Schools";
echo $OUTPUT->header();

if (!is_siteadmin()){
	if($userrole!=="admin"){
		redirect($CFG->wwwroot.'/my');
	}
}
$content='';
$backlink = $CFG->wwwroot.'/project/assignmentor.php';
		$back='<div class="card-block">
		<h1>
		 &nbsp;&nbsp;<a class="btn btn-primary pull-right" href="'.$backlink.'">Back</a>
		</h1>
		</div>';
echo $back;
// <a class="btn btn-primary pull-right" href="'.$backlink.'" style="margin-right:5px;">View Mentor School of Choice Details</a>
echo $OUTPUT->heading($strmessages);
$renderobj = new project_render($USER->id, $USER->msn);
$content.=$renderobj->render_mentor_suggested_school();
echo $content;
echo $OUTPUT->footer();
?>
<script type="text/javascript">
require(['jquery','jqueryui'], function($) {
					//Delete Event From Event Lisitng Page
		$("body").on("click", ".assign-mentor-school", function(event){
		var isChecked = $(this).closest('td').prev('td').find("input:radio.schooldetail-choice:checked").val()?true:false;
		if(!isChecked){
			alert("Please Select Some School to Assign");
			return false;
		}
		var r = confirm("Are you Sure You Want to Assign?");
		if (r == true) {
			var assign= true;
		} else {
			var assign= false;
		}
		if(assign){
				var userid =$(this).closest('td').prev('td').find("input:radio.schooldetail-choice:checked").attr('data-user-id');
				var schoolid = $(this).closest('td').prev('td').find("input:radio.schooldetail-choice:checked").attr('data-school-id');
				var choiceid = $(this).closest('td').prev('td').find("input:radio.schooldetail-choice:checked").attr('data-choice-id');
				//var userid = $(this).attr('data-user-id');
				//var schoolid = $(this).attr('data-school-id');
				//var choiceid = $(this).attr('data-choice-id');
				var request = $.ajax({
				  url: "ajax.php",
				  method: "POST",
				  data:  { 'id' : 1,'mode':'assignmentorschool','userid':userid,'schoolid':schoolid,'choiceid':choiceid},
				  dataType: "html",
				  beforeSend: function() {
						$("#loaderDiv").show();
					},
				});
				request.done(function( msg ) {
						if(msg=="success")
						{
							alert("Assigned Successfully!");
							location.reload(true);
						}
						else
						{
							alert("Failed to Assign. Try Again Later!");
						}
				});
				request.fail(function( jqXHR, textStatus ) {
				  alert( "Request failed: " + textStatus );
				});
			} 
		});
							//Delete Event From Event Lisitng Page
		$("body").on("click", ".remove-mentor-school", function(event){
		var r = confirm("Are you Sure You Want to Reject ?");
		if (r == true) {
			var reject= true;
		} else {
			var reject= false;
		}
		if(reject){
				var userid = $(this).attr('data-user-id');
				var infoidid = $(this).attr('data-info-id');
				var request = $.ajax({
				  url: "ajax.php",
				  method: "POST",
				  data:  { 'id' : 1,'mode':'rejectmentorschool','userid':userid,'infoidid':infoidid},
				  dataType: "html",
				  beforeSend: function() {
						$("#loaderDiv").show();
					},
				});
				request.done(function( msg ) {
						if(msg=="success")
						{
							alert("Mentor is Rejected !");
							location.reload(true);
						}
						else
						{
							alert("Failed. Try Again Later!");
						}
				});
				request.fail(function( jqXHR, textStatus ) {
				  alert( "Request failed: " + textStatus );
				});
			} 
		});
});
</script>