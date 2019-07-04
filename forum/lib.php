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
 * @CreatedOn: 01-01-2018
 * @Description: library functions
*/

function frmpostsql($condition,$limit='',$start=0,$common_forum=0)
{
	global $SESSION;
	$isstudentpost = $SESSION->studentpostforapproval;
	$misusepost = $SESSION->misusepostlist;
	if($isstudentpost==1){
		//To show only unapproved Post related to Project private chat to Atal Chief for Approval. Forum moderator
		$schoolid = $SESSION->schoolid;
		$getstudentroleid = atal_get_roleidbyname('student');
		$sql = "SELECT p.id,p.discussion, p.userid,p.parent as postparent,p.created,DATE_FORMAT( FROM_UNIXTIME(p.created),'%D %b %Y') as createddate,";
		$sql.="p.subject,p.message,p.attachment, u.firstname,u.lastname,u.auth,u.username,u.picture,d.name as discussionname,";
		$sql.="d.course, d.forum,d.categoryid,p.approved ";
		$sql.=" FROM {forum_posts} p JOIN {forum_discussions} d ON d.id=p.discussion JOIN {user} u ON p.userid = u.id";
		$sql.=" JOIN {user_school} us ON p.userid = us.userid WHERE p.approved='n' AND u.msn=$getstudentroleid AND us.schoolid=$schoolid ORDER by p.created desc ";
		$sql.=" limit $start, $limit ";
		return $sql;
	}
	
	if($misusepost==1){
		//To show Misuse Reported Forum Posts		
		$sql = "SELECT p.id,p.discussion, p.userid,p.parent as postparent,p.created,DATE_FORMAT( FROM_UNIXTIME(p.created),'%D %b %Y') as createddate,";
		$sql.="p.subject,p.message,p.attachment, u.firstname,u.lastname,u.auth,u.username,u.picture,d.name as discussionname,";
		$sql.="d.course, d.forum,d.categoryid,p.approved,m.reportedby ";
		$sql.=" FROM {forum_posts} p JOIN {forum_discussions} d ON d.id=p.discussion JOIN {user} u ON p.userid = u.id";
		$sql.=" LEFT JOIN(SELECT p.postid,GROUP_CONCAT(us.userreport SEPARATOR ',') as reportedby,p.created FROM mdl_forum_misuse_report p 
		JOIN(SELECT p.postid,CONCAT(u.id,'-',p.reporttype,'-',u.firstname,' ',u.lastname) as userreport FROM mdl_forum_misuse_report p JOIN mdl_user u ON p.reportedby=u.id) as us 
		ON p.postid=us.postid GROUP BY postid) as m ON p.id=m.postid";
		$sql.=" WHERE p.approved='r' ORDER by p.created desc ";
		$sql.=" limit $start, $limit ";
		return $sql;
	}
	
	if($common_forum==1){		
		/* Sql For Forum With Pagination */
		$sql = "SELECT p.id,p.discussion, p.userid,p.parent as postparent,p.created,DATE_FORMAT( FROM_UNIXTIME(p.created),'%D %b %Y') as createddate,p.subject,p.message,";
		$sql.="p.attachment,u.firstname,u.lastname,u.auth,u.username,u.picture,newdiscussion.name as discussionname, ";
		$sql.="newdiscussion.course, newdiscussion.forum, newdiscussion.categoryid,p.approved";
		$sql.=" FROM {forum_posts} as p join (select * from {forum_discussions} as d ".$condition." ORDER BY d.timemodified desc limit $start,$limit ) as newdiscussion ";
		$sql.=" ON newdiscussion.id=p.discussion JOIN {user} u ON p.userid = u.id";
		return $sql;
	}
	
	//Forum Feed Dashboard & Reply
	$sql = "SELECT p.id,p.discussion, p.userid,p.parent as postparent,p.created,DATE_FORMAT( FROM_UNIXTIME(p.created),'%D %b %Y') as createddate,p.subject,";
	$sql = $sql."p.message, p.attachment,u.firstname,u.lastname,u.auth,u.username,u.picture,d.name as discussionname,d.categoryid,p.approved, ";
	$sql = $sql." d.course, d.forum FROM {forum_posts} p ";
	$sql = $sql."LEFT JOIN {user} u ON p.userid = u.id JOIN {forum_discussions} d ON p.discussion=d.id  ".$condition." ORDER BY d.timemodified desc $limit";
	return $sql;
}

function frmgenratedata($values,$courseid,$forumid)
{
	global  $CFG, $DB, $OUTPUT,$USER;
	$postid = $values->id;
	$post_image = '';
	$discussionid = $values->discussion;
	$courseid = (isset($values->course))?$values->course:$courseid;
	$forumid = (isset($values->forum))?$values->forum:$forumid;
	$type = ($values->postparent==0)?'parent':'reply';
	
	if($values->attachment>0)
	{
		$cm = get_coursemodule_from_instance('forum', $forumid, $courseid, false, MUST_EXIST);
		if (!$context = context_module::instance($cm->id)) {
		  $post_image = '';
		}

		$fs = get_file_storage();
		$files = $fs->get_area_files($context->id, 'mod_forum', 'attachment', $postid, "filename", false);		
		if ($files)
		{
			foreach ($files as $file)
			{
				$filename = $file->get_filename();
				$mimetype = $file->get_mimetype();
				$path = file_encode_url($CFG->wwwroot.'/pluginfile.php', '/'.$context->id.'/mod_forum/attachment/'.$postid.'/'.$filename);
				if (in_array($mimetype, array('image/gif', 'image/jpeg', 'image/png'))) {
					// Image attachments don't get printed as links
					$post_image .= "<br /><img src=\"$path\" alt=\"\" width='100' height='90'/>";
					break; //need to show only 1 img from post attachment;
				}
			}
		}
	}
	$name = $values->firstname.' '.$values->lastname;
	$userobject = (object) array('id'=>$values->userid,'auth'=>$values->auth,'username'=>$values->username,
	'firstname'=>$values->firstname,'lastname'=>$values->lastname,'picture'=>$values->picture);
	$profileurl = getuser_profilelink($values->userid); //$CFG->wwwroot.'/user/view.php?id='.$values->userid;
	$userlink = userpicbyobject($userobject);
	$content = array('type'=>$type,'values'=>$values,'userlink'=>$userlink,'profileurl'=>$profileurl,'post_image'=>$post_image,'uname'=>$values->username);
	
	return $content;
 }

//Delete Forum Post & discussion records. (Bootstrap Function for delete)
function frmdelete_records($postid, $rolename){
	global  $DB;
	
	$result  = false;
	if ($post = $DB->get_record("forum_posts", array("id" => $postid))) {
		$discusionid = $post->discussion;
		$discussion = $DB->get_record('forum_discussions', array('id'=>$discusionid));		
		//$cm = get_coursemodule_from_instance('forum', $forumid, $courseid, false, MUST_EXIST);
		if (!$cm = get_coursemodule_from_instance('forum', $discussion->forum, $discussion->course)) {
			return false;
		}
        if($post->parent==0){
			//Its a Discussion Parent Post ie. Discussion, so all the related child post must be deleted;
			if (!$forum = $DB->get_record('forum', array('id'=>$discussion->forum))) {
				return false;
			}
			if (!$course = $DB->get_record('course', array('id'=>$cm->course))) {
				return false;
			}
			$result = frmforum_delete_discussion($discussion, true, $course, $cm, $forum);			
		} else{
			//Just delete the post , its Not a Parent Post.
			$forum = '';
			$course = '';
			$result = frmforum_delete_post($post, 'ignore', $course, $cm, $forum, true);
		}
    } else{
		$result = false;
	}
	return $result;
}

/**
 * Deletes a discussion and handles all associated cleanup.
 *
 * @global object
 * @param object $discussion Discussion to delete
 * @param bool $fulldelete True when deleting entire forum
 * @param object $course Course
 * @param object $cm Course-module
 * @param object $forum Forum
 * @return bool
 */
function frmforum_delete_discussion($discussion, $fulldelete, $course, $cm, $forum) {
    global $DB, $CFG;

    $result = true;
    if ($posts = $DB->get_records("forum_posts", array("discussion" => $discussion->id))) {
        foreach ($posts as $post) {
            $post->course = $discussion->course;
            $post->forum  = $discussion->forum;
            if (!frmforum_delete_post($post, 'ignore', $course, $cm, $forum, $fulldelete)) {
                $result = false;
            }
        }
    }
    // Discussion subscriptions must be removed before discussions because of key constraints.
    $DB->delete_records('forum_discussion_subs', array('discussion' => $discussion->id));
    if (!$DB->delete_records("forum_discussions", array("id" => $discussion->id))) {
        $result = false;
    }

    return $result;
}

/**
 * Deletes a single forum post.
 *
 * @global object
 * @param object $post Forum post object
 * @param mixed $children Whether to delete children. If false, returns false
 *   if there are any children (without deleting the post). If true,
 *   recursively deletes all children. If set to special value 'ignore', deletes
 *   post regardless of children (this is for use only when deleting all posts
 *   in a disussion).
 * @param object $course Course
 * @param object $cm Course-module
 * @param object $forum Forum
 * @param bool $skipcompletion True to skip updating completion state if it
 *   would otherwise be updated, i.e. when deleting entire forum anyway.
 * @return bool
 */
function frmforum_delete_post($post, $children, $course, $cm, $forum, $skipcompletion=false) {
    global $DB, $CFG, $USER;
	
    $context = context_module::instance($cm->id);

    if ($children !== 'ignore' && ($childposts = $DB->get_records('forum_posts', array('parent'=>$post->id)))) {
        if ($children) {
           foreach ($childposts as $childpost) {
               forum_delete_post($childpost, true, $course, $cm, $forum, $skipcompletion);
           }
        } else {
           return false;
        }
    }
    // Delete attachments.
    $fs = get_file_storage();
    $fs->delete_area_files($context->id, 'mod_forum', 'attachment', $post->id);
    $fs->delete_area_files($context->id, 'mod_forum', 'post', $post->id);

	$DB->delete_records('forum_misuse_report', array('postid'=>$post->id));
    if ($DB->delete_records("forum_posts", array("id" => $post->id))) {
        return true;
    }
    return false;
}

function frmadd_newdiscussion($data,$courseid){
	global $DB, $USER;
	
	$atalvariables = get_atalvariables();
	$userrole = get_atalrolenamebyid($USER->msn);
	if($data->forumid>0){
		$discussion = new stdClass();
		$discussion->course        = $courseid;
		$discussion->forum         = $data->forumid;
		$discussion->name          = $data->name;
		$discussion->userid        = $USER->id;
		$discussion->timemodified  = time();
		$discussion->usermodified  = $USER->id;
		$discussion->assessed      = 0;
		$discussion->categoryid    = $data->frmcategory;
		$discussionid = $DB->insert_record("forum_discussions", $discussion);
		$discussion->id = $discussionid;
		$post = new stdClass();
		$post->discussion    = $discussionid;
		$post->parent        = 0;
		$post->userid        = $USER->id;
		$post->created       = time();
		$post->modified      = time();
		$post->mailed        = 0;
		$post->subject       = $data->name;
		$post->message       = $data->message;
		$post->messageformat = 1;
		$post->messagetrust  = 0;
		$post->attachments   = 0;
		if(!isset($post->approved)){
			$post->approved = 'y';
		} else{
			$post->approved = $data->approved;
		}
		if($discussion->categoryid == getproject_forumcatgeoryid()){
			$post->approved = 'n'; //post related to project private chats is n
	    }
		$postid = $DB->insert_record("forum_posts", $post);
		//Update discussion;
		$discussion->id        = $discussionid;
		$discussion->firstpost  = $postid;
		$DB->update_record('forum_discussions', $discussion);
	}
	return $postid;
}

//@Only add Reply to a Discussion.(call from reply link. forum,dashboard,project-collaboration)
function frmforum_add_newpost($discussionid,$content){
	global $DB, $USER;
	$userrole = get_atalrolenamebyid($USER->msn);
	$atalvariables = get_atalvariables();	
	$postid = 0;
	if($discussionid>0){
		$discussion = $DB->get_record('forum_discussions', array('id'=>$discussionid),'name,categoryid');	
		$post = new stdClass();
		$post->discussion    = $discussionid;
		$post->parent        = 1;
		$post->userid        = $USER->id;
		$post->created       = time();
		$post->modified      = time();
		$post->mailed        = 0;
		$post->subject       = "Re: ".$discussion->name;
		$post->message       = $content;
		$post->messageformat = 1;
		$post->messagetrust  = 0;
		$post->attachments   = 0;
		$post->approved      = 'y';
		if($discussion->categoryid == getproject_forumcatgeoryid()){
			$post->approved = 'n'; //post related to project private chats is n
	    }
		$postid = $DB->insert_record("forum_posts", $post);
		frmupdate_discussion($discussionid);
	}
	return $postid;
}

//@CreatedOn: 27-Jan-2018, for Re: Reply Post..
function frmadd_newpost($discussionid,$parent,$data){
	global $DB, $USER;
	$userrole = get_atalrolenamebyid($USER->msn);
	$postid = 0;
	$post = new stdClass();
	$post->discussion    = $discussionid;
	$post->parent        = $parent;
	$post->userid        = $USER->id;
	$post->created       = time();
	$post->modified      = time();
	$post->mailed        = 0;
	$post->subject       = "Re: ".$data->name;
	$post->message       = "<p>".$data->message."</p>";
	$post->messageformat = 1;
	$post->messagetrust  = 0;
	$post->attachments   = $data->postfile;
	$post->approved      = 'y';
	$postid = $DB->insert_record("forum_posts", $post);
	return $postid;
}

//@CreatedOn:28-March-2018.. To save my/dashboard forum Post
function savedashboardfeed($data){
	global $CFG, $DB, $USER;
	$atal_variable = get_atalvariables();
	//Save data to Forum Post. each Post will be Consider as Parent/New forum post
	$forumid = 0;
	//created post will linkup to forum inside SiteCourse (course will be a site course)..
	//Forum Related to NonSite course is consider as Project Collobration Forum.
	$sql = "SELECT f.id as id,c.id as courseid FROM {forum} f JOIN {course} c ON f.course=c.id
	WHERE c.idnumber='".$atal_variable['sitecourse_idnumber']."' AND f.type='blog'";
	$record = $DB->get_records_sql($sql);
	if(count($record)>0) {
		foreach($record as $key=>$values){
			$forumid = $values->id;
			$courseid = $values->courseid;
		}
	}
	$userrole = get_atalrolenamebyid($USER->msn);
	//Forum Type "Blog"
	$discussion = new stdClass();
	$discussion->course        = $courseid; //SITEID; SITEID > courseid = 1
	$discussion->forum         = $forumid;
	$discussion->name          = $data->title;
	$discussion->userid        = $USER->id;
	$discussion->timemodified  = time();
	$discussion->usermodified  = $USER->id;
	$discussion->assessed      = 0;
	$discussion->categoryid    = $data->frmcategory;
	$discussionid = $DB->insert_record("forum_discussions", $discussion);
	$discussion->id = $discussionid;
	if ($discussionid>0)
	{
		$post = new stdClass();
		$post->discussion    = $discussionid;
		$post->parent        = 0;
		$post->userid        = $USER->id;
		$post->created       = time();
		$post->modified      = time();
		$post->mailed        = 0;
		$post->subject       = $data->title;
		$post->message       = "<p>".$data->detail."</p>";
		$post->messageformat = 1;
		$post->messagetrust  = 0;
		$post->attachments   = null;
		$post->approved      = 'y';
		$post->id = $DB->insert_record("forum_posts", $post);
		$discussion->firstpost = $post->id;
		$DB->update_record('forum_discussions', $discussion);
		//Add attachment.				
		$post->attachments = $data->postfile;
		$info = file_get_draft_area_info($post->attachments);
		$present = ($info['filecount']>0) ? '1' : '';
		if($present=='1'){
			$cm  = get_coursemodule_from_instance('forum', $forumid, $courseid);
			$context  = context_module::instance($cm->id);
			$forum = $DB->get_record('forum', array('id' => $forumid));
			file_save_draft_area_files($post->attachments, $context->id, 'mod_forum', 'attachment', $post->id,
			mod_forum_post_form::attachment_options($forum));
			$DB->set_field('forum_posts', 'attachment', $present, array('id'=>$post->id));
			//File sucessfully added mdl_file id - 156 , waterfilter22.jpg
		}
	}	
}

//This will create the Forum HTML of a single post;
//Critical Function
function frmgetmyreplyhtml($postid)
{
	global $DB,$USER;
	include_once(__DIR__ .'/render.php');
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
				$renderobj->isapprove_post = $val->values->approved;
				$replydata = $replydata.$renderobj->render_forumreply();				
			}
		}
	}
	return $replydata;
}

function get_siteforumid(){
	global $DB;
	$forumid = 0;
	$atal_variable = get_atalvariables();
	$sql = "SELECT f.id as id,c.id as courseid FROM {forum} f JOIN {course} c ON f.course=c.id
	WHERE c.idnumber='".$atal_variable['sitecourse_idnumber']."' AND f.type='blog'";
	$record = $DB->get_records_sql($sql);
	if(count($record)>0) {
		foreach($record as $key=>$values){
			$forumid = $values->id;	
		}
	}
	return $forumid;
}

//Update discussion time when a new forumpost is added
function frmupdate_discussion($discussionid){
	global $DB;
	$discussion = new stdClass();
	$discussion->id = $discussionid;
	$discussion->timemodified = time();
	$DB->update_record('forum_discussions', $discussion);
}

//Approve Forum Post of Students
function frmapprove_post($postid, $rolename){
	global  $DB;
	$flag = false;
	$result  = false;
	if($rolename=='incharge' || $rolename=='admin'){
		$flag = true;
		$result  = true;
	}
	$post = new stdClass();
	$post->id = $postid;
	$post->approved = 'y';
	if($flag){
		$DB->update_record('forum_posts', $post);
		$DB->delete_records('forum_misuse_report', array('postid'=>$postid));
	}
	return $result;
}

//@Param: approved y:n , createdby: Post userid
function checkapproved($approved,$createdby,$categoryid){
	global $USER,$SESSION;
	
	$atalvariables = get_atalvariables();
	$category_studentcannotsee = $atalvariables['mentorschool_postcatgeoryid'];
	$userrole = get_atalrolenamebyid($USER->msn);
	
	if($userrole=='admin' && $SESSION->misusepostlist==1 && $approved=='r'){
		return true;
	}
	
	if($userrole=='student' && $categoryid == $category_studentcannotsee){
		return false;
	}
	//To check, weather Post/Parent is approved
	$flag = ($approved=='y') ? true: false;
	$isstudentpost = $SESSION->studentpostforapproval;
	$userrole = get_atalrolenamebyid($USER->msn);
	//If Page is StudentPost/Forum moderator
	if($userrole=='incharge' && $isstudentpost==1){
		$flag = ($approved=='y') ? false: true;
		//Show only Unapproved Posts.
	}
	//Show unapproved Post (Which is only Project Private Chat)
	if($userrole=='student' && $approved=='n' && $categoryid>0){
		$flag = true;
		//Show this post..
	}
	return $flag;
}

//@CreatedOn:08-April-2018.. Save Event Image
function frmaddeventimage($data){
	global $CFG, $DB, $USER;
	$atal_variable = get_atalvariables();
	//Save data to Forum Post. each Post will be Consider as Parent/New forum post
	$forumid = 0;
	//created post will linkup to forum inside SiteCourse (course will be a site course)..
	//Forum Related to NonSite course is consider as Project Collobration Forum.
	$sql = "SELECT f.id as id,c.id as courseid FROM {forum} f JOIN {course} c ON f.course=c.id
	WHERE c.idnumber='".$atal_variable['sitecourse_idnumber']."' AND f.type='blog'";
	$record = $DB->get_records_sql($sql);
	if(count($record)>0) {
		foreach($record as $key=>$values){
			$forumid = $values->id;
			$courseid = $values->courseid;
		}
	}
	$post = new stdClass();
	if(isset($data->parentid) && $data->parentid > 0){
		//Update Event: Already event image is there , we are updating with another uploaded image.
		$post->id = $data->parentid;
		$cm  = get_coursemodule_from_instance('forum', $forumid, $courseid);
		$context  = context_module::instance($cm->id);
		$fs = get_file_storage();
		$fs->delete_area_files($context->id, 'mod_forum', 'attachment', $post->id);
		$fs->delete_area_files($context->id, 'mod_forum', 'post', $post->id);
	} else{
		//ADD New Event: Add Fresh new event image.
		$userrole = get_atalrolenamebyid($USER->msn);
		//Forum Type "Blog"
		$discussion = new stdClass();
		$discussion->course        = $courseid;
		$discussion->forum         = $forumid;
		$discussion->name          = $data->title;
		$discussion->userid        = $USER->id;
		$discussion->timemodified  = time();
		$discussion->usermodified  = $USER->id;
		$discussion->assessed      = 0;
		$discussion->categoryid    = 0;
		$discussionid = $DB->insert_record("forum_discussions", $discussion);
		$discussion->id = $discussionid;
		if ($discussionid>0)
		{
			$post->discussion    = $discussionid;
			$post->parent        = 0;
			$post->userid        = $USER->id;
			$post->created       = time();
			$post->modified      = time();
			$post->mailed        = 0;
			$post->subject       = $data->title;
			$post->message       = "<p>Event Images</p>";
			$post->messageformat = 1;
			$post->messagetrust  = 0;
			$post->attachments   = null;
			$post->approved      = 'n';
			$post->id = $DB->insert_record("forum_posts", $post);
			$discussion->firstpost = $post->id;
			$DB->update_record('forum_discussions', $discussion);		
		}
	}
	//Add attachment.
	$post->attachments = $data->postfile;
	$info = file_get_draft_area_info($post->attachments);
	$present = ($info['filecount']>0) ? '1' : '';
	if($present=='1'){
		$cm  = get_coursemodule_from_instance('forum', $forumid, $courseid);
		$context  = context_module::instance($cm->id);
		$forum = $DB->get_record('forum', array('id' => $forumid));
		file_save_draft_area_files($post->attachments, $context->id, 'mod_forum', 'attachment', $post->id,
		mod_forum_post_form::attachment_options($forum));
		$DB->set_field('forum_posts', 'attachment', $present, array('id'=>$post->id));
		//File sucessfully added mdl_file id - 156 , waterfilter22.jpg
	}
	return $post->id ;
}

//Get Event Image from it's Post
function getevent_imagepath($postid){
	global  $CFG, $DB, $OUTPUT,$USER;

	$imagepath = $CFG->wwwroot.'/theme/image.php/moove/theme/1512715784/competition';
	$post = $DB->get_record('forum_posts',array('id'=>$postid),'id,discussion,attachment');
	if(isset($post->id) && $post->attachment>0)
	{
		$discussion = $DB->get_record('forum_discussions',array('id'=>$post->discussion),'id,course,forum');
		$forumid = $discussion->forum;
		$courseid = $discussion->course;
		$cm = get_coursemodule_from_instance('forum', $forumid, $courseid, false, MUST_EXIST);
	
		if (!$context = context_module::instance($cm->id)) {
		    $post_image = '';
		}		
		$fs = get_file_storage();
		$files = $fs->get_area_files($context->id, 'mod_forum', 'attachment', $postid, "filename", false);
		if ($files)
		{
			foreach ($files as $file)
			{
				$filename = $file->get_filename();
				$mimetype = $file->get_mimetype();
				$path = file_encode_url($CFG->wwwroot.'/pluginfile.php', '/'.$context->id.'/mod_forum/attachment/'.$postid.'/'.$filename);
				if (in_array($mimetype, array('image/gif', 'image/jpeg', 'image/png','image/jpg'))) {
					// Image attachments don't get printed as links
					$imagepath = $path;
					break; //need to show only 1 img from post attachment;
				}
			}
		}
	}
	return $imagepath;
 }
 
//Report Forum spam or MisUse
function report_misuse($postid,$type){
	global  $DB, $USER;
	$cm = new stdClass();
	$cm->id = $postid;
	$cm->approved = 'r';
	$DB->update_record('forum_posts',$cm);
	unset($cm);
	$cm = new stdClass();
	$cm->postid = $postid;
	$cm->reportedby = $USER->id;
	$cm->reporttype = $type;
	$cm->created = time();
	$DB->insert_record("forum_misuse_report", $cm);
	return true;
}
 
?>