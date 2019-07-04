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
 * @CreatedOn: 04-01-2018
 * @Description: Display Assign Mentors to a School page. add users mentors to specific school
*/

require_once('../config.php');

require_login(null, false);
if (isguestuser()) {
    redirect($CFG->wwwroot);
}
require_once($CFG->libdir.'/filelib.php');
$userrole = get_atalrolenamebyid($USER->msn);
if($userrole!="admin"){
	//Only Niti-Admin can assign user(mentor) to a school..
	redirect($CFG->wwwroot.'/my');
}

include_once(__DIR__ .'/render.php');
include_once(__DIR__ .'/lib.php');
$url = new moodle_url('/project/assignmentor');
$PAGE->set_url($url);

//Heading
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$strmessages = "Assign";
$PAGE->set_title("{$SITE->fullname}");
$PAGE->set_heading("Assign");

$renderobj = new project_render($USER->id, $USER->msn);

// RHS Block.
$filters = projectside_block_mentor($renderobj);
$PAGE->blocks->add_fake_block($filters, $pos=BLOCK_POS_RIGHT);

echo $OUTPUT->header();
$backlink = $CFG->wwwroot.'/project/listmentorsuggestion.php';
		$back="<div class='card-block'>
		<h1>
		  <a class='btn btn-primary pull-right' href='".$backlink."'>View Mentor's Selection</a>
		</h1>
		</div>";
echo $back;
$output = showassign_mentorschool($renderobj);

// Now the page contents.

echo $output;

echo $OUTPUT->footer();

?>
<script type="text/javascript">
require(['jquery'], function($) {
		//$('#mentornamelist').hide();
		//$('#mentor').editableSelect();
			$('#mentor').keyup(function(e){
				$('#mentornamelist li').show();
				var name = $('#mentor').val();
				if(name!='')
					$('#mentornamelist li:not(":contains(' + name + ')")').hide();
				else
					$('#mentornamelist li').show();
			});
			$('#mentor').focus(function(e){
				$('#mentornamelist').show();
			});
		$("body").on("click", "#mentornamelist li", function(event){
			 $("#mentornamelist li").removeClass('active');
			//alert($(this).val());
			var id = $(this).val();
			var textvalue = $(this).text();
			$('#mentor').val(textvalue);
			 $('#mentor').attr('data-id',$(this).val());
			 $(this).addClass('active');
			 $('#mentornamelist').hide();
			 	var request = $.ajax({
				  url: "ajaxnew.php",
				  method: "POST",
				  data:  { 'id' : id,'mode':'getmentor'},
				});
				request.done(function( msg ) {
					var myObj = JSON.parse(msg);
					$('#selmentorschool').html(myObj.replyhtml);		
					$('#selmentorschool').show();
				});
				request.fail(function( jqXHR, textStatus ) {
				  alert( "Request failed: " + textStatus );
				});
		});
		
		$("body").on("click", ".closebtn", function(event){
			$('#atlbox2').hide();
			$('#mentorbox').addClass('hide');
			$('#mentorbox').hide();
			$('#mentor').val('');
			$('#selmentorschool').html('');
			$('#selmentorschool').html('');
			$('.customeditableselect').removeClass('active');
		});
		$("body").on("click", ".schmentor", function(event){
		var schoolid= $(this).attr('data-sid');
		var action= $(this).attr('data-action');
		var schoolname= $(this).attr('data-school');
		$('#modaltitle').html('Assign Mentor');
		$('#popmentor').show();
		$('#usertype').val("mentor");
		$('#popschool').text(schoolname);
		$('#sid').val(schoolid);
		$('#atlbox2').removeClass('hide');
		$('#mentorbox').removeClass('hide');
		$('#mentorbox').show();
		showwatsonmentor_forschool(schoolid);
		//alert(schoolid + '-' + action+ '-' +schoolname );
		}); 
}); 
</script>