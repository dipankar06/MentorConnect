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
 * @CreatedOn: 26-12-2017
 * @Description: Display Assign user in a Projects.
 This Page (assign) is use to enroll Users into a Project and Create Project.
 add users mentors/Students to a specific project
 Only Add/Remove of users to projects (Course) is done in this context..
*/

require_once('../config.php');

require_login(null, false);
if (isguestuser()) {
    redirect($CFG->wwwroot);
}
require_once($CFG->libdir.'/filelib.php');
$userrole = get_atalrolenamebyid($USER->msn);
if($userrole=="incharge" ){
	$cancreateproject = true;
} else if($userrole=="student"){
	$cancreateproject = true;
} else{
	$cancreateproject = false;
}
if($cancreateproject===false){
	//Only School-incharge & Students can create/edit a project Info.
	redirect($CFG->wwwroot.'/my');
}

include_once(__DIR__ .'/render.php');
include_once(__DIR__ .'/render_innovation.php');
include_once(__DIR__ .'/lib.php');
include_once(__DIR__ .'/edit_form.php'); //Create project form

$course = '';
if($id>0){
	$course = get_project($id);
	$is_enrol = isenrol_toproject($id, $course);
	if($is_enrol===false){
		redirect($CFG->wwwroot.'/my'); //can't edit other school projects..
	}
}
$url = new moodle_url('/project/assign');
$PAGE->set_url($url);

//Heading
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$strmessages = "Innovations";
$PAGE->set_title("{$SITE->fullname}");
$PAGE->set_heading("Innovations");

$renderobj = new project_render($USER->id, $USER->msn);
$innrenderobj = new innovation_render($USER->id, $USER->msn);

//Create Project Form...
$course = null;
$atalArray = get_atalvariables();
$category = $atalArray['project_categoryid'];
$catcontext = context_coursecat::instance($category);
//'maxfiles' => EDITOR_UNLIMITED_FILES
$editoroptions = array('maxfiles' => 1, 'maxbytes'=>$CFG->maxbytes, 'trusttext'=>false, 'noclean'=>true,'accepted_types'=>array('image'));
$overviewfilesoptions = course_overviewfiles_options($course);
if(empty($course)) {
	// Editor should respect category context if course context is not set.
	$editoroptions['context'] = $catcontext;
	$editoroptions['subdirs'] = 0;
	$course = file_prepare_standard_editor($course, 'summary', $editoroptions, null, 'course', 'summary', null);
	
	if ($overviewfilesoptions) {
		file_prepare_standard_filemanager($course, 'overviewfiles', $overviewfilesoptions, null, 'course', 'overviewfiles', 0);
	}
}

//get mentors & student list in my school
$myschoolid = getmyschoolid();
$mentor = getusersofschool('mentor',$myschoolid);
$student = getusersofschool('student',$myschoolid);
if(count($mentor)>0){
	$tmp['0'] = "Select";
	foreach($mentor as $key=>$val){
		$tmp[$val->userid] = ucfirst($val->firstname).' '.$val->lastname;
	}
	$mentor = $tmp;
	unset($tmp);
} else{ $mentor = array(); }
if(count($student)>0){
	$tmp['0'] = "Select";
	foreach($student as $key=>$val){
		$tmp[$val->userid] = ucfirst($val->firstname).' '.$val->lastname;
	}
	$student = $tmp;
} else{ $student = array(); }

$args = array(
    'course' => $course,
    'category' => $category,
    'editoroptions' => $editoroptions,
    'returnto' => '',
    'returnurl' => '',
	'mentorlist'=> $mentor,
	'studentlist'=> $student
);
$url = new moodle_url('/project/');
$frmprojectobj = new add_projectform($url, $args);
//Create Project form ends......

// RHS Block.
$filters = projectside_block_mentor($renderobj);
$PAGE->blocks->add_fake_block($filters, $pos=BLOCK_POS_RIGHT);

echo $OUTPUT->header();
$output = showassignproject($innrenderobj,$frmprojectobj);

// Now the page contents.

echo $output;

echo $OUTPUT->footer();
?>
<script type="text/javascript">
require(['jquery'], function($) {
		//$('#mentor').editableSelect();
		//$('#student').editableSelect();	
		$('#mentor').keyup(function(e){
				$('#mentornamelist li').show();
				var name = $('#mentor').val();
				if(name!='')
					$('#mentornamelist li:not(":contains(' + name + ')")').hide();
				else
					$('#mentornamelist li').show();
			});
				$('#student').keyup(function(e){
				$('#studentnamelist li').show();
				var name = $('#student').val();
				if(name!='')
					$('#studentnamelist li:not(":contains(' + name + ')")').hide();
				else
					$('#studentnamelist li').show();
			});
			$('#student').focus(function(e){
				$('#studentnamelist').show();
			});
			$('#mentor').focus(function(e){
				$('#mentornamelist').show();
			});
		$("body").on("click", "#studentnamelist li", function(event){
			triggerclickfornamelist($(this),'student','studentnamelist')
		});
		$("body").on("click", "#mentornamelist li", function(event){
			triggerclickfornamelist($(this),'mentor','mentornamelist');
		});
	function triggerclickfornamelist(currentelement,inputid,listid)
	{
			$("#"+listid+" li").removeClass('active');
			var textvalue = currentelement.text();
			$('#'+inputid).val(textvalue);
			 $('#'+inputid).attr('data-id',currentelement.val());
			currentelement.addClass('active');
			 $('#'+listid).hide();
			 loaduserdetail(currentelement.val());
	}
	function loaduserdetail(id){
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
	}
	$("body").on("click", ".closebtn", function(event){
			$('#atlbox2').addClass('hide');
			$('#assignbox').addClass('hide');
			$('#assignbox').hide();
			$('#popmentor').hide();
			$('#popstudent').hide();
			$('#mentor').val('');
			$('#student').val('');
			$('#selmentorschool').html('');
			$('.customeditableselect').removeClass('active');
	});
});
</script>