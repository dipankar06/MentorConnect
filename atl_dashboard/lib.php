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

/* @package: block_atl_dashboard
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 12-12-2017
 * @Description: Library functions for mydashboard
*/

include_once(__DIR__ .'/newpost_form.php');
include_once(__DIR__ .'/render.php');
require_once($CFG->dirroot.'/forum/lib.php');

require_once($CFG->dirroot.'/project/externallib.php');
save_data();

//Add New Forum/Event
function save_data()
{
	global $DB, $USER;	
	$mform1 = new newpost_form();
	$data = $mform1->get_data();
	if ($data)
	{	
		if ($data->flag=='post' && !empty($data->title)){
		    savedashboardfeed($data);
		}
	}
}

function showatlforumfeed()
{
	global $USER, $OUTPUT;
	$renderobj = new forum_render($USER->id, $USER->msn);
	$addpostobj = new newpost_form();
	$role = get_atalrolenamebyid($USER->msn);
	$view_timeline = ($role=='student' || $role=='mentor')?false:true;
	//Mentors & Students will only see project instead of TimeLine Tab..
	$view_forum = ($role=='mentor')?false:true;
	$activeclass = ($view_forum) ? "":"active in";

	$content = '
		<div id="block-atldashboard" class="block-atldashboard" data-region="atldashboard">
		<ul id="block-atldashboard-view-choices" class="nav nav-tabs" role="tablist">';
	if($view_forum){
	$content.= '		
			<li class="nav-item">
			<a id="v1" class="nav-link active" href="#myoverview_forum_view" role="tab" data-toggle="tab" data-tabname="courses"  aria-expanded="true">
			Forum Feeds
			</a>
		</li>';
	}
	if($view_timeline){
	$content.= '<li class="nav-item">
		<a id="v2" class="nav-link" href="#myoverview_timeline_view" role="tab" data-toggle="tab" data-tabname="timeline" aria-expanded="false">
			Timeline
		</a>
		</li>';
	}
	if($view_timeline===false){
	$content.= '<li class="nav-item">
		<a id="v3" class="nav-link" href="#myoverview_project_view" role="tab" data-toggle="tab" data-tabname="projects" aria-expanded="false">
			Innovations
		</a>
		</li>';
	}
	$content.= '</ul>';
	
	if($role!='mentor'){
	$content.='<div id="createmenus" class="createmenu">
		<a class="createlink" href="#"><img src="'.$OUTPUT->image_url('addnew', 'theme').'" width="20" height="20"></a>			
		<ul class="subs">
		<li><a id="atlnewpost" href="javascript:void(0);">New Post</a></li>	
		</ul>
	</div>';
	}
	$content.='<div class="tab-content content-centred tabcontentatal">';
		
		if($view_forum){
		$content.='<div role="tabpanel" class="tab-pane fade active in" id="myoverview_forum_view" aria-expanded="true">
		<div id="courses-view" data-region="courses-view">
			'.frmdisplaydata().'
		</div>
		</div>';
		}

		$content.='<div role="tabpanel" class="tab-pane fade" id="myoverview_timeline_view" aria-expanded="false">
		<div id="timeline-view" data-region="timeline-view">                
			'.frmtimelinedata().'
		</div>
		</div>';
			
		$content.='	<div role="tabpanel" class="tab-pane fade '.$activeclass.'" id="myoverview_project_view" aria-expanded="false">
		<div id="projectview" data-region="project-view">
			'.frmprojectdata($renderobj).'
		</div>
		</div>';
			
		$content.=($role=='mentor')?'<div class="csstest1"></div>' : '<div class="csstest1">*Latest Feed</div>';
		$content.='</div>
	'.$renderobj->popupbox($addpostobj).'
	</div>';
	return $content;
}

//ForumFeeds in dashboard will Display SiteForum Post and UserEnrol Project Forum Post ONLY.(Mentors/Students)
//For NitAdmin its all, for school incharge its only school.
function frmdisplaydata()
{
	global $DB,$USER,$SESSION;
	$renderobj = new forum_render($USER->id, $USER->msn);
	$content = '';
	$sitecourseid = $SESSION->sitecourseid;
	$post_array = array();
	$userrole = get_atalrolenamebyid($USER->msn);
	$redflag_discussion = array();
	//Get Forum Post category
	$category_array = array();
	if(!isset($SESSION->forumcategoryarray)){
		$categorydata = $DB->get_records('forum_category');
		if(count($categorydata)>0){
			foreach($categorydata as $keys=>$values){
				$category_array[$values->id] = $values->name;
			}
			unset($keys);
		}
	} else{
		$category_array = $SESSION->forumcategoryarray;
	}
	//get only that Forum which is related to Enrol Project.
	$condition = " JOIN(
	SELECT f.id,usercourse.courseid as cid FROM {forum} f LEFT JOIN(SELECT c.id as courseid FROM {user} u JOIN {user_enrolments} ue ON u.id=ue.userid 
	JOIN {enrol} e ON ue.enrolid=e.id JOIN {course} c ON e.courseid=c.id 
	WHERE e.enrol='manual' AND u.id=".$USER->id." ) as usercourse ON f.course=usercourse.courseid WHERE f.type='blog' HAVING cid IS NOT NULL
	UNION
	SELECT f.id,NULL as cid FROM {forum} f WHERE f.course='".$sitecourseid."' AND f.type='blog') as userforum ON d.forum=userforum.id
	";
	if($userrole=='incharge'){
		$schoolid = $SESSION->schoolid;
		$condition = " JOIN(SELECT f.id,c.id as cid FROM {forum} f JOIN {course} c ON f.course=c.id 
		WHERE f.type='blog' AND c.idnumber='".$schoolid."'
		UNION 
		SELECT f.id,NULL as cid FROM {forum} f WHERE f.course='".$sitecourseid."' AND f.type='blog'
		)as userforum ON d.forum=userforum.id";
	}
	if($userrole=='admin'){
		$condition = ' WHERE d.categoryid<>0 ';
	}
	$limit = " LIMIT 0,20";
	$sql = frmpostsql($condition,$limit);
	$record = $DB->get_records_sql($sql);
	if(count($record)>0)
	{
		foreach($record as $key=>$values)
		{
			$courseid = $values->course;
			$forumid = $values->forum;
			if(checkapproved($values->approved,$values->userid,$values->categoryid)===true){
				//Show only Approved Posts from forum and project Private chat(which is unaproved)..in Feeds
				$post_array[$values->discussion][] = (object) frmgenratedata($values,$values->course,$values->forum);
			}
			//Donot show Post which have its Parent post Red Flaged.
			//If parent post is red flaged the all its subsequent Replies will be hidden
			if($values->parent==0 && $values->approved=='r'){
				$redflag_discussion[] = $values->discussion;
			}
		}
	}
	
	if(count($post_array)>0){
		$tmp_discussionid = 0;
		$hasparent = false;
		foreach($post_array as $key=>$values)
		{
			if(in_array($key,$redflag_discussion)===false){
				$replydata = '';
				foreach($values as $k1=>$val){
					$renderobj->isapprove_post = $val->values->approved;
					if($val->type=='parent'){
						$renderobj->values = $val->values;
						$renderobj->userlink = $val->userlink;
						$renderobj->profileurl = $val->profileurl;
						$renderobj->post_image = $val->post_image;
						$renderobj->uname = $val->uname;
						$renderobj->category = $category_array[$val->values->categoryid];
					} else{
						$renderobj->replyvalues = $val->values;
						$renderobj->replyuserlink = $val->userlink;
						$renderobj->replyprofileurl = $val->profileurl;
						$renderobj->replypost_image = $val->post_image;
						$renderobj->replyuname = $val->uname;
						$replydata = $replydata.$renderobj->render_forumreply();
					}
				}
				if($replydata=='')
					$renderobj->hideCollapseDiv = 0;
				else
					$renderobj->hideCollapseDiv = 1; 
				$renderobj->replydata = $replydata;
				$content = $content.$renderobj->render_forum();
			}
		}
	}
	
	return $content;
}

function frmtimelinedata()
{
	global $DB,$USER;
	$renderobj = new forum_render($USER->id, $USER->msn);
	$userrole = get_atalrolenamebyid($USER->msn);
	$content = '';
	$post_array = array();
	$condition = ' WHERE d.userid='.$USER->id;
	if($userrole=='admin'){
		$condition.= ' AND d.categoryid<>0 ';
	}
	$limit = " LIMIT 0,20";
	$sql = frmpostsql($condition,$limit);
	$record = $DB->get_records_sql($sql);
	if(count($record)>0)
	{
		foreach($record as $key=>$values)
		{
			$courseid = $values->course;
			$forumid = $values->forum;
			$post_array[$values->discussion][] = (object) frmgenratedata($values,$values->course,$values->forum);			
		}
	}	
	if(count($post_array)>0){
		$tmp_discussionid = 0;
		$hasparent = false;
		foreach($post_array as $key=>$values)
		{
			$replydata = '';
			foreach($values as $k1=>$val){			
				if($val->type=='parent'){
					$renderobj->values = $val->values;
					$renderobj->userlink = $val->userlink;
					$renderobj->profileurl = $val->profileurl;
					$renderobj->post_image = $val->post_image;
					$renderobj->uname = $val->uname;
				} else{
					$renderobj->replyvalues = $val->values;
					$renderobj->replyuserlink = $val->userlink;
					$renderobj->replyprofileurl = $val->profileurl;
					$renderobj->replypost_image = $val->post_image;
					$renderobj->replyuname = $val->uname;
					$replydata = $replydata.$renderobj->render_forumreply();
				}
			}
			if($replydata=='') {
			$renderobj->hideCollapseDiv = 0;
			} else{
				$renderobj->hideCollapseDiv = 1;
			}
			$renderobj->replydata = $replydata;
			$content = $content.$renderobj->render_forum(true);
		}
	}
	
	return $content;
}
    
//Display Project Listing in Dashboard for Mentors/Students
function frmprojectdata(forum_render $renderobj)
{
	global $DB,$USER,$OUTPUT;
	$content = '';
	$project_array = array();
	$role = get_atalrolenamebyid($USER->msn);
	$inchargeroleid = atal_get_roleidbyname('incharge');
	$atalvariable = get_atalvariables();
	$projectcategory = (in_array("project_categoryid", $atalvariable)===true)?$atalvariable['project_categoryid']:2;
	$condition = ' WHERE p.enddate=0 AND p.visible=1 AND c.contextlevel='.$atalvariable['coursecontext'].' AND p.category='.$projectcategory;	
	$query="JOIN (SELECT c.id as pid,sl.name as sname FROM {course} c JOIN {user_school} s ON c.idnumber=s.schoolid JOIN {school} sl ON s.schoolid=sl.id WHERE s.userid=".$USER->id.") as m";
	$inchargequery = "LEFT JOIN (SELECT c.id as pid,u.id,u.username,u.msn,u.firstname,u.lastname,u.picture,u.auth FROM {course} c JOIN {user_school} uc 
	ON c.idnumber=uc.schoolid LEFT JOIN {user} u ON uc.userid=u.id WHERE u.msn=".$inchargeroleid.") as inc";
	$onlymine = " JOIN(SELECT c.id FROM {course} c JOIN {context} cx ON c.id=cx.instanceid JOIN {role_assignments} r ON cx.id=r.contextid WHERE r.userid=".$USER->id.") as mcs";
	$sql = "SELECT concat(p.id,u.id) as id,p.id as projectid,p.fullname,p.shortname,p.startdate, DATE_FORMAT( FROM_UNIXTIME(p.startdate),'%D %b %Y') as createddate,p.maxbytes,p.idnumber,";
	$sql.="u.id as userid,u.username,u.msn,u.firstname,u.lastname,u.picture,u.auth,m.sname as schoolname,";
	$sql.="inc.id as inchargeid,inc.username as iusername,inc.firstname as ifirstname,inc.lastname as ilastname,inc.auth as iauth,inc.picture as ipicture";
	$sql.=" FROM {course} p JOIN {context} c ON p.id=c.instanceid JOIN {role_assignments} r ON c.id=r.contextid ";
	$sql.=" LEFT JOIN {user} u ON r.userid=u.id $query ON p.id=m.pid $inchargequery ON p.id=inc.pid $onlymine ON p.id=mcs.id $condition ORDER BY p.id desc";
	$record = $DB->get_records_sql($sql);
	if(count($record)>0)
	{
		foreach($record as $key=>$values)
		{
			$userlink = getprojuserpic($values);
			$projectimg = frmgetcourse_summary_imagepath($values->projectid);
			$project_array[$values->projectid]['detail']=(object) array('id'=>$values->projectid,'fullname'=>ucwords($values->fullname),'shortname'=>$values->shortname,
			'startdate'=>$values->createddate,'idnumber'=>$values->idnumber,'projectpic'=>$projectimg,'school'=>$values->schoolname);
			$userrole = get_atalrolenamebyid($values->msn);
			if($userrole=='mentor'){
				$project_array[$values->projectid]['mentor'][]=(object) array('id'=>$values->userid,'firstname'=>$values->firstname,
				'lastname'=>$values->lastname,'pic'=>$userlink);
			} elseif($userrole=='student'){
				$project_array[$values->projectid]['student'][]=(object) array('id'=>$values->userid,'firstname'=>$values->firstname,
				'lastname'=>$values->lastname,'pic'=>$userlink);
			}
			$userlink = getproject_inchargepic($values);
			$project_array[$values->projectid]['incharge'] = array('id'=>$values->inchargeid,'firstname'=>$values->ifirstname,
				'lastname'=>$values->ilastname,'pic'=>$userlink);
		}
		//get list of active innovations
		$activeprojects = ($role=="mentor") ? get_activeprojectids($USER->id,$role) : array();
		foreach($project_array as $k=>$val){
			if($role=="mentor"){
				//Show only active innovations in list for mentors
				if(in_array($k,$activeprojects)){
					$content.= $renderobj->showsprojects($val, $role);
				}
			} else{
				$content.= $renderobj->showsprojects($val, $role);
			}
		}
	} else{ $content="No Innovations Found"; }
	
	return $content;
}

function getprojuserpic($values){	
	$name = $values->firstname.' '.$values->lastname;
	$userobject = (object) array('id'=>$values->userid,'auth'=>$values->auth,'username'=>$values->username,
	'firstname'=>$values->firstname,'lastname'=>$values->lastname,'picture'=>$values->picture);
	$userlink = userpicbyobject($userobject);
	return $userlink;
}

function getproject_inchargepic($values){
	$name = $values->ifirstname.' '.$values->ilastname;
	$userobject = (object) array('id'=>$values->inchargeid,'auth'=>$values->iauth,'username'=>$values->iusername,
	'firstname'=>$values->ifirstname,'lastname'=>$values->ilastname,'picture'=>$values->ipicture);
	$userlink = userpicbyobject($userobject);
	return $userlink;
}

//This will create the Forum HTML of a single post;
function dashboardmyreplyhtml($postid)
{
	global $DB,$USER;
	$renderobj = new forum_render($USER->id, $USER->msn);
	$content = '';
	$replydata = '';
	$post_array = array();
	$condition = ' WHERE p.id='.$postid;
	$sql = frmpostsql($condition);	
	$record = $DB->get_records_sql($sql);
	if(count($record)>0)
	{
		foreach($record as $key=>$values)
		{
			$courseid = $values->course;
			$forumid = $values->forum;
			$post_array[$values->discussion][] = (object) frmgenratedata($values,$values->course,$values->forum);			
		}
	}	
	if(count($post_array)>0){
		$tmp_discussionid = 0;		
		foreach($post_array as $key=>$values)
		{			
			foreach($values as $k1=>$val){				
				$renderobj->replyvalues = $val->values;
				$renderobj->replyuserlink = $val->userlink;
				$renderobj->replyprofileurl = $val->profileurl;
				$renderobj->replypost_image = $val->post_image;
				$renderobj->replyuname = $val->uname;
				$replydata = $replydata.$renderobj->render_forumreply();				
			}			
		}
	}	
	return $replydata;
}