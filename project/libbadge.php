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
 * @CreatedOn: 12-01-2018
 * @Description: Library functions Badges
*/

include_once(__DIR__ .'/assignbadge_form.php');
include_once $CFG->libdir . '/filelib.php';


function get_badgeform(){
	global $USER, $CFG;
	$data = get_mystudentlist($USER->id);
	$students = array("Select");
	if(count($data)>0){
		foreach($data as $keys=>$values){
			$students[$values->userid] = ucwords($values->firstname).' '.$values->lastname.' -'.$values->school.' '.$values->city;
		}
	}
	unset($data);	
	$projects = array("Select");	
	$badgeimgs = '<div id="assignbadge">'.getbadgeimages().'</div>';	
	$frmobject = new assignbadge_form(null,array('studentlist'=>$students,'project'=>$projects,'badgeimg'=>$badgeimgs));	
	$content = '<h4>Badges</h4><div style="margin-bottom:4%;"></div>';
	process_badgeformdata($frmobject);
	$content.= $frmobject->render();
	$content.= getjs_script();
	$content.='<div id="message"></div>';

	$content.='<form id="frmbgtest1" action="'.$CFG->wwwroot.'/project/badges.php" method="post"></form>';
	return $content;
}

function process_badgeformdata($frmobject){
	if ($frmobject->is_cancelled()) {
		
	} else if ($data = $frmobject->get_data()) {		
		//stdClass Object ( [name] => 6 [project] => 0 [submitbutton] => Award ) 
		//redirect($CFG->wwwroot.'/project/assign.php');
		if(isset($data->badge)){
			if(!empty($data->name) && !empty($data->project)){
				saveassignbadge($data->badge,$data->name,$data->project);
			}
		}
	} else {
		//noting;
	}
}

function getbadgeimages(){
	global $DB, $CFG;
	$content ='';
	$data = $DB->get_records('badge', array('type' => 1),null,'id,name,description,type,attachment');
	if(count($data)>0){
		foreach ($data as $collection=>$badge) {			
			$context = frmget_context();
			$imageurl = moodle_url::make_pluginfile_url($context->id, 'badges', 'badgeimage', $badge->id, '/', 'f1', false);
			$content.= html_writer::start_tag('div', array('class'=>'badges'));
			$content.= html_writer::start_tag('div', array('class' => 'badge-image'));
			$content.= html_writer::empty_tag('img', array('src' => $imageurl));
			$content.= html_writer::end_tag('div');
			$content.= html_writer::start_tag('div', array('class' => 'badgedetail'));
			$content.= html_writer::tag('span', $badge->name, array('class' => 'badge-name'));
			$content.= '<br>'.$badge->description.'<br></br><input type="radio" name="badge" value="'.$badge->id.'">';
			$content.= html_writer::end_tag('div');
			$content.= html_writer::end_tag('div');
		}
	}
	return $content;
}

/**
 * Use to get context instance of a badge.
 * @return context instance.
 */
function frmget_context() {
	global $CFG, $DB;
    //include this file for content /libdir/filelib.php
    return $systemcontext = context_system::instance();	
}

function getjs_script(){
	$content ='<script type= "text/javascript">
	document.getElementById("id_name").addEventListener("change",function(e) {
		var xmlhttp = new XMLHttpRequest();
		var e = document.getElementById("id_name");
		var uid = e.options[e.selectedIndex].value;
		var e2 = document.getElementById("id_project");
		e2.options.length = 0;
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var myObj = JSON.parse(this.responseText);
				if(myObj.success==1){
					var data = myObj.replyhtml;
					if(data.length>0){
						var options = document.createElement("option");		
						options.value = 0;
						options.text = "Select Innovation";
						e2.appendChild(options);
						for(var i = 0; i < data.length; i++) {
							var soption = data[i];
							var option = document.createElement("option");
							for(var j = 0; j < soption.length; j++) {
								if(j==0){
									option.value = soption[j];
								} else{
									option.text = soption[j];
								}
							}
							e2.appendChild(option);
						}
					} else{
						var options = document.createElement("option");
						options.value = 0;
						options.text = "No Innovation Assigned";
						e2.appendChild(options);
					}
				} else{
					var options = document.createElement("option");	
					options.value = 0;
					options.text = "No Innovation Found";
					e2.appendChild(options);
				}
			}
		};
		xmlhttp.open("GET", "ajax.php?id="+uid+"&mode=badge&type=getproject", true);
		xmlhttp.send();
	});
	
	document.getElementById("id_awardbtn").addEventListener("click",function(e) {
		var xmlhttp = new XMLHttpRequest();
		var e = document.getElementById("id_name");
		var uid = e.options[e.selectedIndex].value;
		var e2 = document.getElementById("id_project");
		var pid = e2.options[e2.selectedIndex].value;	
		var badges = document.getElementsByName("badge");		
		var selbadge = 0;
		var badgecnt = badges.length;
		for (var i = 0; i < badgecnt; i++)	{
			if (badges[i].checked){
				selbadge = badges[i].value;
			}
		}
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var myObj = JSON.parse(this.responseText);
				if(myObj.success==1){
					document.getElementById("message").innerHTML = myObj.msg;
					alert(myObj.msg);
					document.getElementById("frmbgtest1").submit();
				} else{
					alert(myObj.msg);
				}
			}
		};
		xmlhttp.open("GET", "ajax.php?id="+uid+"&pid="+pid+"&bid="+selbadge+"&mode=savebadge", true);
		xmlhttp.send();
	});	

	</script>
	';
	return $content;
}

//Show User's Recieved Badges.
function showmybadgeimage($badge,$userrole){
	global $DB, $CFG;
	$content ='';
	$context = frmget_context();
	$imageurl = moodle_url::make_pluginfile_url($context->id, 'badges', 'badgeimage', $badge->badgeid, '/', 'f1', false);
	$content.= html_writer::start_tag('div', array('class'=>'badges width50'));
	$content.= html_writer::start_tag('div', array('class' => 'badge-image'));
	$content.= html_writer::empty_tag('img', array('src' => $imageurl));
	$content.= html_writer::end_tag('div');
	$content.= html_writer::start_tag('div', array('class' => 'badgedetail width66'));
	$content.= html_writer::tag('span', $badge->name, array('class' => 'badge-name'));
	$content.= '<br>'.$badge->description.'</br></br>';
	$content.= html_writer::end_tag('div');
	//Project Details
	if($userrole=="student"){
		$content.= html_writer::start_tag('div', array('class' => 'badgedetail width66'));
		$content.='<span class="badge-name">Innovation: </span><span>'.$badge->fullname.'</span></br>';
		$content.='<span class="badge-name">Awarded By: </span><span>'.$badge->issuername.'</span></br>';
		$content.='<span class="badge-name">Award Date: </span><span>'.$badge->createddate.'</span>';
		$content.= html_writer::end_tag('div');
	}
	$content.= html_writer::end_tag('div');
	
	return $content;
}

//function to show LoggedIn user's Award & badges
function get_mybadges($pagetitle,$userrole){
	global $DB, $USER;
	$content = '<h4>'.$pagetitle.'</h4><div style="margin-bottom:4%;"></div>';
	
	$rccount = $DB->count_records('badge_manual_award', array('recipientid'=>$USER->id));
	if($rccount>0){
		if($userrole=="student"){
			$sql = "SELECT ba.*,DATE_FORMAT( FROM_UNIXTIME(ba.datemet),'%D %b %Y') as createddate,c.fullname,b.name,b.description,u.firstname as issuername ";
			$sql.= "FROM {badge_manual_award} ba JOIN {course} c ON ba.courseid=c.id JOIN {badge} b ON ba.badgeid=b.id ";
			$sql.= "LEFT JOIN {user} u ON ba.issuerid = u.id WHERE ba.recipientid=".$USER->id;
			$data = $DB->get_records_sql($sql);			
			if(count($data)>0){
				foreach ($data as $collection=>$badge) {
					$content.= '<div id="assignbadge">';
					$content.= showmybadgeimage($badge,$userrole);
					$content.= '</div>';
				}
			}
		}		
	} else{
		$content ='<div class="atlmessage">No '.$pagetitle.' Found</div>';
	}
	
	return $content;
}
?>