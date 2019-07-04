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
 * @Description: html codes
*/

class forum_render extends plugin_renderer_base {

	public $values;
	public $userlink;
	public $profileurl;
	public $post_image;
	public $uname;
	public $replydata;
	public $category;
	
	public $userid;
	public $userrole;

	public $replyvalues;
	public $replyuserlink;
	public $replyprofileurl;
	public $replypost_image;
	public $replyuname;

	public $hideCollapseDiv=1;
	
	public $isapprove_post = "y";
	
	public function __construct($userid, $userrole) {
	  global $DB, $PAGE, $CFG;
	  $this->userid = $userid;
	  $this->userrole = get_atalrolenamebyid($userrole);
	}

	public function render_forum($timelime=false)
	{		
		$atalvariables = get_atalvariables();
		$privatechat_category = $atalvariables['ongoingproject_postcatgeoryid'];
		$showflag = ($this->values->categoryid == $privatechat_category)?false:true;

		$content = '<div class="forumpost clearfix lastpost firstpost starter" role="region" aria-label="forum-collaboration" id="p'.$this->values->id.'">
		<div class="row header clearfix">
		<div class="left picture">'.$this->userlink.'</div>

		<div class="topic firstpost starter">
		<div class="subject" role="heading" aria-level="2">'.$this->values->subject.'</div>
		<div class="atlclearfix">
		<div class="author" role="heading" aria-level="2">by
		<a href="'.$this->profileurl.'">'.$this->uname.'</a> - '.$this->values->createddate.'&nbsp;&nbsp;<span class="smallheading">'.$this->category.'</span>
		</div>
		<div class="atlright">';		
		if($this->values->userid == $this->userid || $this->userrole=='admin'){
			//user Who Creates a Post can only delete it.
			$content.= '<a href="javascript:void(0);" data-id="'.$this->values->id.'" onclick="postdelete(this);"></a>';
		}
		if($showflag==true && $this->values->userid != $this->userid && $this->userrole!='admin'){
			//Misuse Report flag
			$content.='<div class="atlmisuse">
			<a id="misa'.$this->values->id.'" href="javascript:void(0);" data-id="'.$this->values->id.'" onclick="postmisuse(this);"></a>
			</div>';
		}
		$content.= '</div>
		</div>
		</div>
		</div>

		<div class="row maincontent clearfix">
		<div class="left"><div class="grouppictures">&nbsp;</div>
		</div>

		<div class="no-overflow">
		<div class="content">
		<div class="posting fullpost">'.$this->values->message.'

		<div class="attachedimages">'.$this->post_image.'</div>
		</div>
		</div>
		</div>
		</div>
		<div class="row side">
		</div>';
		if($timelime){
		if($this->hideCollapseDiv !=0)
		{
			$id= 'timelimeText-'.$this->values->id;
			$content.='<div class="expandpost" data-toggle="collapse" data-target="#toggleposttimelime-'.$this->values->id.'">
			<i class="icon fa fa-arrow-down fa-fw " aria-hidden="true" title="Down" aria-label="Down"  style="display: inline;padding: 5px;"><span  id="'.$id.'" style="padding: 4px; font-family: Poppins,sans-serif;font-size: 1.03rem;">View Replies </span></i> </div>';
		}
		
		$content.= '<div class="postreply collapse" id="toggleposttimelime-'.$this->values->id.'">
		'.$this->replydata.'
		</div>
		<div id="myrep'.$this->values->discussion.'" data-text="show my Reply here"></div>';
		
		//$content.= $this->add_myreply();

		$content.='<div style="clear:both;"></div>
		</div>' ;
	}
	else{
		if($this->hideCollapseDiv !=0)
		{
			$id= 'updateText-'.$this->values->id;
			$content.='<div class="expandpost" data-toggle="collapse" data-target="#togglepost-'.$this->values->id.'">
			<i class="icon fa fa-arrow-down fa-fw " aria-hidden="true" title="Down" aria-label="Down"  style="display: inline;padding: 5px;"><span  id="'.$id.'" style="padding: 4px; font-family: Poppins,sans-serif;font-size: 1.03rem;">View Replies </span></i> </div>';
		}
		
		$content.= '<div class="postreply collapse" id="togglepost-'.$this->values->id.'">
		'.$this->replydata.'
		</div>
		<div id="myrep'.$this->values->discussion.'" data-text="show my Reply here"></div>';
		
		//$content.= $this->add_myreply();
		$content.='<div style="clear:both;"></div>
		</div>' ;	
	}
	return $content;
	}
	
	public function render_forumreply()
	{		
		$showflag = true;
		$yettoapprove = "";
		if($this->isapprove_post=='n'){
			$yettoapprove='<div class="fltright smalltext"></div>'; //UnApprove
			$showflag = false;
		}
		$content = '<div class="reply" id="p'.$this->replyvalues->id.'">
		<div class="row header clearfix">
		<div class="left picture">'.$this->replyuserlink.'</div>

		<div class="topic firstpost starter">
		<div class="subject" role="heading" aria-level="2">'.$this->replyvalues->subject.'</div>
		<div class="atlclearfix">
		<div class="author" role="heading" aria-level="2">by
		<a href="'.$this->replyprofileurl.'">'.$this->replyuname.'</a> - '.$this->replyvalues->createddate.'
		</div>		
		<div class="atlright">';		
		if($this->replyvalues->userid == $this->userid  || $this->userrole=='admin'){
			//user Who Creates a Post can only delete it.
			$content.= '<a href="javascript:void(0);" data-id="'.$this->replyvalues->id.'" onclick="postdelete(this);"></a>';
		}
		if($showflag==true && $this->replyvalues->userid != $this->userid && $this->userrole!='admin'){
			//Misuse Report flag
			$content.='<div class="atlmisuse">
			<a id="misa'.$this->replyvalues->id.'" href="javascript:void(0);" data-id="'.$this->replyvalues->id.'" onclick="postmisuse(this);"></a>
			</div>';
		}
		$content.= '</div>'.$yettoapprove.'
		</div>
		</div>
		</div>

		<div class="row maincontent clearfix">
		<div class="left"><div class="grouppictures">&nbsp;</div>
		</div>

		<div class="no-overflow">
		<div class="content">
		<div class="posting fullpost">'.$this->replyvalues->message.'

		<div class="attachedimages">'.$this->replypost_image.'</div>
		</div>
		</div>
		</div>
		</div>
		<div class="row side">
		</div>
		</div>' ;

		return $content;
	}
	
	private function add_myreply(){
		$myreply = '
		<div class="myreply">
			<div class="myreplya">
				<textarea id="area'.$this->values->discussion.'" class="myreplybox" rows="1" placeholder="Write your Reply" maxlength="400"></textarea>
			</div>
			<div class="myreplyb"><a data-id="'.$this->values->discussion.'" class="sentreply" href="javascript:void(0);">Reply</a></div>
		</div>';
		return $myreply;
	}
	public function popupbox(newpost_form $addpostobj)
	{
		global $CFG, $USER;
		$role = get_atalrolenamebyid($USER->msn);
		$urlpath = $CFG->wwwroot.'/blocks/atl_dashboard/ajax.php';
		$content = '		
		<div id="addpostbox" class="modal moodle-has-zindex iepos hide" data-region="modal-container" aria-hidden="false" role="dialog" style="z-index: 1052;">
			<div class="modal-dialog modal-lg" role="document" data-region="modal" aria-labelledby="0-modal-title" >
			<div class="modal-content">
			<div class="modal-header " data-region="header">
			<button type="button" class="close closebtn" data-action="hide" aria-label="Close">
				<span aria-hidden="true">×</span>
			</button>
			<h4 id="0-modal-title" class="modal-title" data-region="title" tabindex="0">Add New Post</h4>
			</div>
			<div class="modal-body" data-region="body" style="">'.$addpostobj->render().'

			</div>
			<div class="modal-footer" data-region="footer">
			<button id="postcancel1" type="button" class="btn btn-secondary closebtn" data-action="cancel">Cancel</button>
			</div>
			</div>
			</div>
		</div>
		
		<div id="atlboxdel" class="modal moodle-has-zindex iepos hide" data-region="modal-container" aria-hidden="false" role="dialog" style="z-index: 1052;">
			<div class="modal-dialog modal-lg" role="document" data-region="modal" aria-labelledby="0-modal-title" >
				<div class="modal-content">			
				<input name="action" value="forum" type="hidden">
				<input name="sesskey" value="txxcPlyyA6a" type="hidden">
				<input name="_qf__forum_delete_form" value="1" type="hidden">
				<div class="modal-header " data-region="header">
					<button type="button" class="close closebtn" data-action="hide" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
					<h4 id="0-modal-title" class="modal-title" data-region="title" tabindex="0">Delete</h4>
				</div>
				<div class="modal-body" data-region="body" style="">Sure Want To Delete This Post ?
				</div>
				<div class="modal-footer" data-region="footer">
					<span id="deletemsg" style="display:none;float:left;color:blue;">Processing ...</span>
					<button id="postdeletebtn" type="button" class="btn btn-primary" data-action="save">Delete</button>
					<button type="button" class="btn btn-secondary closebtn" data-action="cancel">Cancel</button>
					<input type="hidden" name="delpostid" id="delpostid">
					<input type="hidden" name="urlpath" id="liburlpath" value="'.$urlpath.'">
				</div>			
				</div>
			</div>
		</div>';		
		
		$get_misusetypes = get_misusetypes();
		$selectbox = '<select id="misusetype">';
		foreach($get_misusetypes as $key=>$values){
			$selectbox.='<option value="'.$key.'">'.$values.'</option>';
		}
		$selectbox.='</select>';		
		$content.= '
		<div id="atlboxmisuse" class="modal moodle-has-zindex hide" data-region="modal-container" aria-hidden="false" role="dialog" style="z-index: 1052;">
			<div class="modal-dialog modal-lg" role="document" data-region="modal" aria-labelledby="0-modal-title" >
			<div class="modal-content">			
				<input name="action" value="forum" type="hidden">
				<input name="sesskey" value="txxcPlyyA6a" type="hidden">
				<input name="_qf__forum_misuse_form" value="1" type="hidden">
				<div class="modal-header " data-region="header">
				<button type="button" class="close closebtn" data-action="hide" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
				<h4 id="0-modal-title" class="modal-title" data-region="title" tabindex="0">Report Misuse</h4>
				</div>
				<div class="modal-body" data-region="body" style=""></br>
				<p>'.$selectbox.'</p><br></br>
				<p>Sure Want To Report This Post ? </p>
				<p class="smalltext">Warning false reporting will result in action against you !</p>
				</div>
				<div class="modal-footer" data-region="footer">
					<span id="misusemsg" style="display:none;float:left;color:blue;">Processing ...</span>
					<button id="postmisusebtn" type="button" class="btn btn-primary" data-action="save">Report</button>
					<button type="button" class="btn btn-secondary closebtn" data-action="cancel">Cancel</button>
					<input type="hidden" name="mispostid" id="mispostid">					
				</div>
			</div>
			</div>
		</div>';
		
		$content.= '	
		<script type= "text/javascript">
			//atlbox2 is the Main Modal window ...inc_end.mustache
			';
			if($role=='admin'){
				/*$content.= 'document.getElementById("atlnewevent").addEventListener("click",function(e) {
					document.getElementById("atlbox2").classList.remove("hide");
					document.getElementById("addeventbox").classList.remove("hide");
					document.getElementById("addeventbox").style.display = "block";
				});'; */
			}
			//Open Addnew Post PopUp
			$content.= 'document.getElementById("atlnewpost").addEventListener("click",function(e) {
				document.getElementById("atlbox2").classList.remove("hide");
				document.getElementById("addpostbox").classList.remove("hide");
				document.getElementById("addpostbox").style.display = "block";
				document.getElementById("id_title").value = "";
				document.getElementById("id_detail").value = "";
				document.getElementById("id_error_title").innerHTML = "";
				document.getElementById("id_error_detail").innerHTML = "";
				document.getElementById("id_error_postfile").innerHTML = "";
			});
			
			var classname = document.getElementsByClassName("closebtn");
			var mycloseFunction = function() {
				//var attribute = this.getAttribute("data-myattribute");
				document.getElementById("atlbox2").classList.add("hide");
				document.getElementById("atlboxdel").classList.add("hide");
				document.getElementById("atlboxdel").style.display = "none";
				//document.getElementById("addeventbox").classList.add("hide");
				//document.getElementById("addeventbox").style.display = "none";
				document.getElementById("addpostbox").classList.add("hide");
				document.getElementById("addpostbox").style.display = "none";
				//make field values blank if user close the poup;
				//document.getElementById("id_name").value = "";
				document.getElementById("id_detail").value = "";
				document.getElementById("atlboxmisuse").classList.add("hide");
				document.getElementById("atlboxmisuse").style.display = "none";				
				var elx = document.getElementsByClassName("filepicker-filename");
				elx[0].innerHTML = "";
			};
			for (var i = 0; i < classname.length; i++) {
				classname[i].addEventListener("click", mycloseFunction, false);
			}	
			
			document.getElementById("postdeletebtn").addEventListener("click",function(e) {
				deleteforumpost("path");
			});
			
			document.getElementById("postmisusebtn").addEventListener("click",function(e) {
				reportpostmisuse("path");
			});
			
			var genraterandomnum1 = function(id) {
			var idd = Number(id) + Number(1);
			var randam = Math.floor(Math.random() * 666) + 1;
			randam = "3E4"+randam+"A"+idd+"A"+id;
			return randam;
		}		
		
		</script>
		';

		return $content;
	}
	
	public function showsprojects($obj,$role)
	{
		global $CFG;
		$content = '<div class="forumpost clearfix starter" role="region" aria-label="forum-collaboration" id="p">';
		
		$content.='<div class="projimage"><a href="'.$CFG->wwwroot.'/project/detail.php?id='.encryptdecrypt_projectid($obj['detail']->id).'">
		<img src="'.$obj['detail']->projectpic.'" width="100"></a></div>';
		$content.='<div class="projdetail">
			<ul>
			<li><a href="'.$CFG->wwwroot.'/project/detail.php?id='.encryptdecrypt_projectid($obj['detail']->id).'"><h4>'.$obj['detail']->fullname.'</h4></a></li>
			<li>StartDate:&nbsp;&nbsp;'.$obj['detail']->startdate.'</li>
			<li>Status:&nbsp;&nbsp;InProgress</li>
			<li>School:&nbsp;&nbsp;'.$obj['detail']->school.'</li>
			<li><div class="userprofile">Student:&nbsp;&nbsp;</div>';
				$students = $obj['student'];
				foreach($students as $s=>$sval){
					$content.='<div class="userprofile pics"><p>'.$sval->pic.'</p>'.$sval->firstname.'</div>';
				}
				if($role=='mentor'){
					$incharge = $obj['incharge'];					
					$content.='<div class="userprofile moreleft">ATL Incharge:&nbsp;&nbsp;</div>';
					$content.='<div class="userprofile mentorpics"><p>'.$incharge['pic'].'</p>'.$incharge['firstname'].'</div>';
				} else{
					$mentor = $obj['mentor'];
					$content.='<div class="userprofile moreleft">Mentor:&nbsp;&nbsp;</div>';
					foreach($mentor as $s=>$sval){
						$content.='<div class="userprofile mentorpics"><p>'.$sval->pic.'</p>'.$sval->firstname.'</div>';
					}
				}
		$content.='	</li>
			</ul>
		</div>';
		$content.= '</div>';
		
		return $content;
	}
}

?>
