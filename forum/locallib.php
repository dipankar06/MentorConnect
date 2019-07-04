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

/* @package: core_forum
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 14-12-2017
 * @Description: Library functions for Collabartion Forum
*/

include_once(__DIR__ .'/add_form.php');
include_once(__DIR__ .'/render.php');
require_once('../external/commonrender.php');
include_once(__DIR__ .'/lib.php');

$addformobj = new add_form();
$renderobj = new forum_render($USER->id, $USER->msn);

//Add new forum post & discusion.15/12/2017.
$userrole = get_atalrolenamebyid($USER->msn);
$data = $addformobj->get_data();
if ($data)
{
	$savedata = true;
	$atal_variable = get_atalvariables();
	$discussionid = $data->discussionid;
	$parent = 1;
	$forumid = 0;
	$courseid = 0;
	$post = new stdClass();
	$post->attachments = $data->postfile;	
	//Admin created post will linkup to forum inside SiteCourse (1 course will be a site course)..
	$sql = "SELECT f.id as id,c.id as courseid FROM {forum} f JOIN {course} c ON f.course=c.id
	WHERE c.idnumber='".$atal_variable['sitecourse_idnumber']."' AND f.type='blog'";
	$record = $DB->get_records_sql($sql);
	    if(count($record)>0) {
		foreach($record as $key=>$values){
			$forumid = $values->id;
			$courseid = $values->courseid;
	    }
	}
	$cm         = get_coursemodule_from_instance('forum', $forumid, $courseid);
	$context    = context_module::instance($cm->id);
	$forum      = $DB->get_record('forum', array('id' => $forumid));

	if($discussionid==0){
	    //New Post & Discussion added to Forum related to Site-Course only.
	    $parent = 0;
	    $data->forumid = $forumid;
	    $post->id = frmadd_newdiscussion($data,$courseid);
	    $savedata = false;
	}	
	if($savedata===true){
		$post->id = frmadd_newpost($discussionid,$parent,$data);
	}
	//If image is added in message section i.e text-Editor
	//$post->message = file_save_draft_area_files($post->itemid, $context->id, 'mod_forum', 'post', $post->id,
	//  mod_forum_post_form::editor_options($context, null), $post->message);
	//$DB->set_field('forum_posts', 'message', $post->message, array('id'=>$post->id));
	//forum_add_attachment($post, $forum, $cm, $mform);

	$info = file_get_draft_area_info($post->attachments);
	$present = ($info['filecount']>0) ? '1' : '';
	if($present=='1'){
		file_save_draft_area_files($post->attachments, $context->id, 'mod_forum', 'attachment', $post->id,
			mod_forum_post_form::attachment_options($forum));
		$DB->set_field('forum_posts', 'attachment', $present, array('id'=>$post->id));
		//File sucessfully added mdl_file id - 156 , waterfilter22.jpg
	}
}

function showstudentpost(){
	global $CFG;
	$forumpost_link = $CFG->wwwroot.'/forum/';
	$studentpost_link = $CFG->wwwroot.'/forum/studentpost.php';
	$content = '<div class="card-text content">
	<div id="block-createtab" class="block-createtab" data-region="createtab">
	<div class="forumtabs">
	<ul id="block-createtab-view-choices" class="nav nav-tabs" role="tablist">
	<li class="nav-item">
	<a id="v1" class="nav-link" href="'.$forumpost_link.'" data-tabname="mentors" aria-expanded="true">Forum Post </a>
	</li>
	<li class="nav-item">
	<a id="v2" class="nav-link active" href="'.$studentpost_link.'" data-tabname="schools" aria-expanded="false">Innovation Post</a>
	</li>
	</ul>
	</div>
	<div class="tab-content content-centred tabcontentatal">';
	$content.= showforumfeed_student();
	$content.= approvestudentpost();
	$content.='</div></div></div>'; //Close tabcotent,block-createab,card-text
	return $content;
}

function showforumfeed_student($page=1)
{
	global $OUTPUT, $addformobj, $renderobj, $SESSION;
	$records_perpage = $renderobj->records_perpage;
	$content = '';
	$populartagdata = frmget_populartags();
	//$forum_category=getForumCategory();
	$isstudentpost = $SESSION->studentpostforapproval; //Altal Chief on studentpost page;
	$ismisusereport = $SESSION->misusepostlist;
	$content.= '
	<div role="tabpanel" aria-expanded="true" id="forumcontent">
	<div id="colforum" data-region="timeline-view">
	<div class="create">';
	//if($isstudentpost !==1){
		//$content.=$renderobj->renderForumCategory($forum_category); //Category Dropdown
	//}
	$content.='</div>';
	//Forum data Card..
	$content.='<div id="forumdata">';
	$content = $content.frmdisplaydata_studentpost($page,$records_perpage);
	($renderobj->total_rows_forum_discussion>1)?$total_pages =ceil(($renderobj->total_rows_forum_discussion)/$records_perpage):$total_pages =1;	
	$content.='<div class="pagination-wrapper" align="center">';
	$content.= CommonRender::paginate_function($records_perpage,$page,$renderobj->total_rows_forum_discussion,$total_pages);
	$content.='</div>';
	$content.='</div>';	
	$populartags = $renderobj->showpopulartags($populartagdata);
	$content.='</div><div id="forumsidebar">'.$populartags.'</div> ';
	$content.="</div>";
	$content = $content.$renderobj->popupbox($addformobj);	
	$content = $content.'<div id="loaderDiv" style="position: fixed; left: 50%; top: 50%; display: none;">
    <img src="../theme/moove/pix/indicator.gif"></img></div>';
	
	return $content;
}

function approvestudentpost(){
	global $renderobj;
	$content = $renderobj->approvepopupbox();	
	return $content;
}

//Show Forum for Incharged LoggedIn
function showforumfeedforincharge(){
	global $CFG;
	$forumpost_link = $CFG->wwwroot.'/forum/';
	$studentpost_link = $CFG->wwwroot.'/forum/studentpost.php';
	$content = '<div class="card-text content">
	<div id="block-createtab" class="block-createtab" data-region="createtab">
	<div class="forumtabs">
	<ul id="block-createtab-view-choices" class="nav nav-tabs" role="tablist">
	<li class="nav-item">
	<a id="v1" class="nav-link active" href="'.$forumpost_link.'" data-tabname="mentors" aria-expanded="true">Forum Post </a>
	</li>
	<li class="nav-item">
	<a id="v2" class="nav-link" href="'.$studentpost_link.'" data-tabname="schools" aria-expanded="false">Innovation Post</a>
	</li>
	</ul>
	</div>
	<div class="tab-content content-centred tabcontentatal">';
	$content.= showforumfeed();
	$content.='</div></div></div>'; //Close tabcotent,block-createab,card-text
	return $content;
}

function showmisusepost(){
	global $CFG;
	$forumpost_link = $CFG->wwwroot.'/forum/';
	$post_link = $CFG->wwwroot.'/forum/misusereport.php';
	$content = '<div class="card-text content">
	<div id="block-createtab" class="block-createtab" data-region="createtab">
	<div class="forumtabs">
	<ul id="block-createtab-view-choices" class="nav nav-tabs" role="tablist">
	<li class="nav-item">
	<a id="v1" class="nav-link" href="'.$forumpost_link.'" data-tabname="mentors" aria-expanded="true">Forum Post </a>
	</li>
	<li class="nav-item">
	<a id="v2" class="nav-link active" href="'.$post_link.'" data-tabname="schools" aria-expanded="false">Misuse Report</a>
	</li>
	</ul>
	</div>
	<div class="tab-content content-centred tabcontentatal">';
	$content.= showmissuse_reportfeed();
	$content.= approvestudentpost();
	$content.='</div></div></div>'; //Close tabcotent,block-createab,card-text
	return $content;
}

function showforumfeedforadmin(){
	global $CFG;
	$forumpost_link = $CFG->wwwroot.'/forum/';
	$post_link = $CFG->wwwroot.'/forum/misusereport.php';
	$content = '<div class="card-text content">
	<div id="block-createtab" class="block-createtab" data-region="createtab">
	<div class="forumtabs">
	<ul id="block-createtab-view-choices" class="nav nav-tabs" role="tablist">
	<li class="nav-item">
	<a id="v1" class="nav-link active" href="'.$forumpost_link.'" data-tabname="mentors" aria-expanded="true">Forum Post </a>
	</li>
	<li class="nav-item">
	<a id="v2" class="nav-link" href="'.$post_link.'" data-tabname="schools" aria-expanded="false">Misuse Report</a>
	</li>
	</ul>
	</div>
	<div class="tab-content content-centred tabcontentatal">';
	$content.= showforumfeed();
	$content.='</div></div></div>'; //Close tabcotent,block-createab,card-text
	return $content;
}

function showforumfeed($page=1)
{
	global $OUTPUT, $addformobj, $renderobj, $SESSION;	
	$records_perpage = $renderobj->records_perpage;
	$content = '';
	$populartagdata = frmget_populartags();
	$forum_category=getForumCategory();
	$isstudentpost = $SESSION->studentpostforapproval; //Altal Chief on studentpost page;
	$ismisusereport = $SESSION->misusepostlist;
	$showaddicon = ($isstudentpost==1 || $ismisusereport==1)?false:true;	
	
	$content.= '
	<div role="tabpanel" aria-expanded="true" id="forumcontent">
	<div id="colforum" data-region="timeline-view">
	<div class="create">';
	if($showaddicon){
		$content.='<a id="addnewpost" href="javascript:void(0);" title="Add a New Post"><img src="'.$OUTPUT->image_url('addnew', 'theme').'" width="20" height="20"></a>';
	}
	if($isstudentpost !==1){
		$content.=$renderobj->renderForumCategory($forum_category); //Category Dropdown
	}
	$content.='</div>';
	//Forum data Card..
	$content.='<div id="forumdata">';
	$content = $content.frmdisplaydata($page,$records_perpage);
	($renderobj->total_rows_forum_discussion>1)?$total_pages =ceil(($renderobj->total_rows_forum_discussion)/$records_perpage):$total_pages =1;	
	$content.='<div class="pagination-wrapper" align="center">';
	$content.= CommonRender::paginate_function($records_perpage,$page,$renderobj->total_rows_forum_discussion,$total_pages);
	$content.='</div>';
	$content.='</div>';
	
	$populartags = $renderobj->showpopulartags($populartagdata);
	$content.='</div><div id="forumsidebar">'.$populartags.'</div> ';
	$content.="</div>";

	$content = $content.$renderobj->popupbox($addformobj);
	
	$content = $content.'<div id="loaderDiv" style="position: fixed; left: 50%; top: 50%; display: none;">
    <img src="../theme/moove/pix/indicator.gif"></img></div>';
	
	return $content;
}

function showmissuse_reportfeed($page=1)
{
	global $OUTPUT, $addformobj, $renderobj, $SESSION;	
	$records_perpage = $renderobj->records_perpage;
	$content = '';
	$populartagdata = frmget_populartags();
	$forum_category=getForumCategory();	
	$ismisusereport = $SESSION->misusepostlist;
	$content.= '
	<div role="tabpanel" aria-expanded="true" id="forumcontent">
	<div id="colforum" data-region="timeline-view">
	<div class="create">';
	$content.=$renderobj->renderForumCategory($forum_category); //Category Dropdown
	$content.='</div>';
	//Forum data Card..
	$content.='<div id="forumdata">';
	$content = $content.frmdisplaydata_missuserpt($page,$records_perpage);
	($renderobj->total_rows_forum_discussion>1)?$total_pages =ceil(($renderobj->total_rows_forum_discussion)/$records_perpage):$total_pages =1;
	$content.='<div class="pagination-wrapper" align="center">';
	$content.= CommonRender::paginate_function($records_perpage,$page,$renderobj->total_rows_forum_discussion,$total_pages);
	$content.='</div>';
	$content.='</div>';
	
	$populartags = $renderobj->showpopulartags($populartagdata);
	$content.='</div><div id="forumsidebar">'.$populartags.'</div> ';
	$content.="</div>";

	$content = $content.$renderobj->popupbox($addformobj);
	
	$content = $content.'<div id="loaderDiv" style="position: fixed; left: 50%; top: 50%; display: none;">
    <img src="../theme/moove/pix/indicator.gif"></img></div>';
	
	return $content;
}

function showforumfeed_nextpage($page=1,$category=0)
{
	global $renderobj;	
	$records_perpage = $renderobj->records_perpage;
	$content = '';
	$isstudentpost = $SESSION->studentpostforapproval; //Altal Chief on studentpost page;
	$ismisusereport = $SESSION->misusepostlist;
	if($ismisusereport==1){
		$content = $content.frmdisplaydata_missuserpt($page,$records_perpage,$category);
	} elseif($isstudentpost==1){
		$content = $content.frmdisplaydata($page,$records_perpage,$category);
	} else{
		$content = $content.frmdisplaydata($page,$records_perpage,$category);
	}	
	($renderobj->total_rows_forum_discussion>1)?$total_pages =ceil(($renderobj->total_rows_forum_discussion)/$records_perpage):$total_pages =1;	
	$content.='<div class="pagination-wrapper" align="center">';
	$content.= CommonRender::paginate_function($records_perpage,$page,$renderobj->total_rows_forum_discussion,$total_pages);
	$content.='</div>';	
	
	return $content;
}

function frmdisplaydata($page=1,$limit=0,$category=0)
{
	global $DB,$USER,$SESSION,$renderobj;

	$content = '';
	$post_array = array();
	$siteforumid = get_siteforumid();
	$userrole = get_atalrolenamebyid($USER->msn);
	if($category==0)
		$condition = ' WHERE d.forum='.$siteforumid; /**This will prevent showing Project Private chat in forum , as forum id differs for each project */
	else
		$condition = ' WHERE d.categoryid='.$category.' AND d.forum='.$siteforumid;
	$sql_discussion_total = "SELECT (d.id) FROM {forum_discussions} d ".$condition;
	$discussion_record = $DB->get_records_sql($sql_discussion_total);
	$renderobj->total_rows_forum_discussion = count($discussion_record);	
	$start_from = ($page-1) * $limit;  
	$sql = frmpostsql($condition,$limit,$start_from,1);	
	$record = $DB->get_records_sql($sql);
	$redflag_discussion = array();
	if(count($record)>0)
	{
		foreach($record as $key=>$values)
		{
			$courseid = $values->course;
			$forumid = $values->forum;
			if(checkapproved($values->approved,$values->userid,$values->categoryid)===true){
				$post_array[$values->discussion][] = (object) frmgenratedata($values,$values->course,$values->forum);
			}
			//Donot show Post which have its Parent post Red Flaged excluding admin user.
			//If parent post is red flaged the all its subsequent Replies will be hidden
			if($values->parent==0 && $values->approved=='r'){
				$redflag_discussion[] = $values->discussion;
			}
		}
	}
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

	if(count($post_array)>0){
		$tmp_discussionid = 0;
		$hasparent = false;		
		foreach($post_array as $key=>$values)
		{
			if(in_array($key,$redflag_discussion)===false || $SESSION->misusepostlist==1){
				$content.= genfrmdisplaydata($renderobj,$values,$category_array);
			}
		}
	}
	else		
		$content.='<div class="clearfix lastpost firstpost starter" role="region" aria-label="forum-collaboration"><p>No Results Found!</p> </div>';
	return $content;
}

function frmdisplaydata_missuserpt($page=1,$limit=0,$category=0)
{
	global $DB,$USER,$SESSION,$renderobj;

	$content = '';
	$post_array = array();
	$siteforumid = get_siteforumid();
	$userrole = get_atalrolenamebyid($USER->msn);
	if($category==0)
		$condition = ' WHERE d.forum='.$siteforumid; /**This will prevent showing Project Private chat in forum , as forum id differs for each project */
	else
		$condition = ' WHERE d.categoryid='.$category.' AND d.forum='.$siteforumid;
	$sql_discussion_total = "SELECT (d.id) FROM {forum_discussions} d JOIN(SELECT discussion as id FROM {forum_posts} WHERE approved='r') as p ON d.id=p.id ".$condition;
	$discussion_record = $DB->get_records_sql($sql_discussion_total);
	$renderobj->total_rows_forum_discussion = count($discussion_record);	
	$start_from = ($page-1) * $limit;  
	$sql = frmpostsql($condition,$limit,$start_from,1);	
	$record = $DB->get_records_sql($sql);
	$redflag_discussion = array();
	if(count($record)>0)
	{
		foreach($record as $key=>$values)
		{
			$courseid = $values->course;
			$forumid = $values->forum;
			if(checkapproved($values->approved,$values->userid,$values->categoryid)===true){
				$post_array[$values->discussion][] = (object) frmgenratedata($values,$values->course,$values->forum);
			}
			//Donot show Post which have its Parent post Red Flaged excluding admin user.
			//If parent post is red flaged the all its subsequent Replies will be hidden
			if($values->parent==0 && $values->approved=='r'){
				$redflag_discussion[] = $values->discussion;
			}
		}
	}
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

	if(count($post_array)>0){
		$tmp_discussionid = 0;
		$hasparent = false;		
		foreach($post_array as $key=>$values)
		{
			if(in_array($key,$redflag_discussion)===false || $SESSION->misusepostlist==1){
				$content.= genfrmdisplaydata($renderobj,$values,$category_array);
			}
		}
	}
	else		
		$content.='<div class="clearfix lastpost firstpost starter" role="region" aria-label="forum-collaboration"><p>No Results Found!</p> </div>';
	return $content;
}

function frmdisplaydata_studentpost($page=1,$limit=0,$category=0)
{
	global $DB,$USER,$SESSION,$renderobj;
	$atalvariables = get_atalvariables();
	$projectchat_category = $atalvariables['ongoingproject_postcatgeoryid'];
	$content = '';
	$post_array = array();
	$siteforumid = get_siteforumid();
	$userrole = get_atalrolenamebyid($USER->msn);
	$condition = ' WHERE d.categoryid='.$projectchat_category;
	$sql_discussion_total = "SELECT (d.id) FROM {forum_discussions} d ".$condition;
	$discussion_record = $DB->get_records_sql($sql_discussion_total);
	$renderobj->total_rows_forum_discussion = count($discussion_record);	
	$start_from = ($page-1) * $limit;  
	$sql = frmpostsql($condition,$limit,$start_from,1);	
	$record = $DB->get_records_sql($sql);
	$redflag_discussion = array();
	if(count($record)>0)
	{
		foreach($record as $key=>$values)
		{
			$courseid = $values->course;
			$forumid = $values->forum;
			if(checkapproved($values->approved,$values->userid,$values->categoryid)===true){
				$post_array[$values->discussion][] = (object) frmgenratedata($values,$values->course,$values->forum);
			}
			//Donot show Post which have its Parent post Red Flaged excluding admin user.
			//If parent post is red flaged the all its subsequent Replies will be hidden
			if($values->parent==0 && $values->approved=='r'){
				$redflag_discussion[] = $values->discussion;
			}
		}
	}
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

	if(count($post_array)>0){
		$tmp_discussionid = 0;
		$hasparent = false;		
		foreach($post_array as $key=>$values)
		{
			if(in_array($key,$redflag_discussion)===false || $SESSION->misusepostlist==1){
				$content.= genfrmdisplaydata($renderobj,$values,$category_array);
			}
		}
	}
	else		
		$content.='<div class="clearfix lastpost firstpost starter" role="region" aria-label="forum-collaboration"><p>No Results Found!</p> </div>';
	return $content;
}

function genfrmdisplaydata(forum_render $renderobj, $values, $category_array){
	global $SESSION;
	
	$replydata = '';
	$content = '';
	$isstudentpost = $SESSION->studentpostforapproval; //Atal Chief on studentpost page;
	$ismisusepost  = $SESSION->misusepostlist; //NitiAdmin on misusereport page;
	if($isstudentpost==1){
		foreach($values as $k1=>$val){
			$renderobj->values = $val->values;
			$renderobj->userlink = $val->userlink;
			$renderobj->profileurl = $val->profileurl;
			$renderobj->post_image = $val->post_image;
			$renderobj->uname = $val->uname;
			$renderobj->category = $category_array[$val->values->categoryid];
			$content = $content.$renderobj->render_forummoderator();
		}
	} elseif($ismisusepost==1){		
		foreach($values as $k1=>$val){
			$renderobj->values = $val->values;
			$renderobj->userlink = $val->userlink;
			$renderobj->profileurl = $val->profileurl;
			$renderobj->post_image = $val->post_image;
			$renderobj->uname = $val->uname;
			$renderobj->category = $category_array[$val->values->categoryid];
			$content = $content.$renderobj->render_forummisuse();
		}
	} else{
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
		if($replydata=='') {
			$renderobj->hideCollapseDiv = 0;
		} else{
			$renderobj->hideCollapseDiv = 1;
		}
		$renderobj->replydata = $replydata;
		$content = $content.$renderobj->render_forum();
    }
	return $content;
}

function frmget_populartags(){
	global $DB,$CFG;
	$conditions = array();
	$tags = "<ul>";
	$data = $DB->get_records('tag_project',$conditions,'id desc','*',0,10);
	if(count($data)>0){
		foreach($data as $key=>$values){
			$tags.='<li><a href="'.$CFG->wwwroot.'/search/?search='.$values->name.'">'.ucwords($values->name).'</a></li>';
		}
	} else{
		$tags.='<li><a href="'.$CFG->wwwroot.'/search/?search=Robotics">Robotics</a></li>';
		$tags.='<li><a href="'.$CFG->wwwroot.'/search/?search=computing">Computing</a></li>';
		$tags.='<li><a href="'.$CFG->wwwroot.'/search/?search=design">Design</a></li>';
		$tags.='<li><a href="'.$CFG->wwwroot.'/search/?search=Automation">Automation</a></li>';
		$tags.='<li><a href="'.$CFG->wwwroot.'/search/?search=Sensors">Sensors</a></li>';
	}
	$tags.= "</ul>";
	return $tags;
}
function getForumCategory()
{
	global $DB;
	$result = $DB->get_records('forum_category');
	return $result;
}
?>