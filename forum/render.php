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
 * @CreatedOn: 15-04-2018
 * @Description: render
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
	public $total_rows_forum_discussion=1;
	
	public $records_perpage = 10;
	
	public $isapprove_post = "y";
	
	public function __construct($userid, $userrole) {
	  global $DB, $PAGE, $CFG;
	  $this->userid = $userid;
	  $this->userrole = get_atalrolenamebyid($userrole);
	}

	public function render_forum()
	{
		$yettoapprove = "";
		if($this->isapprove_post=='n'){
			$yettoapprove='<div class="atlapprove smalltext"></div>'; //UnApprove , text written as div innerHTML;
		}
		$content = '<div class="forumpost clearfix lastpost firstpost starter" role="region" aria-label="forum-collaboration" id="p'.$this->values->id.'">
		<div class="row header clearfix">
		<div class="left picture">'.$this->userlink.'</div>

		<div class="topic firstpost starter">
		<div class="subject" role="heading" aria-level="2">'.$this->values->subject.'</div>
		<div class="atlclearfix">
		<div class="author" role="heading" aria-level="2">by
		<a href="'.$this->profileurl.'">'.$this->uname.'</a> - '.$this->values->createddate.'&nbsp;&nbsp;<span class="smallheading">'.$this->category.'</span>
		</div>';
		if($this->values->userid == $this->userid || $this->userrole=='admin'){
			//user Who Creates a Post can only delete it.
			$content.='<div class="atlright">
			<a href="javascript:void(0);" data-id="'.$this->values->id.'" onclick="postdelete(this);"></a>
			</div>';
		}
		if($this->values->userid != $this->userid && $this->userrole!='admin'){
			//Misuse Report flag
			$content.='<div class="atlmisuse" title="Report Misuse">
			<a id="misa'.$this->values->id.'" href="javascript:void(0);" data-id="'.$this->values->id.'" onclick="postmisuse(this);"></a>
			</div>';
		}
		$content.= $yettoapprove;
		$content.='</div>
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
		
		if($this->hideCollapseDiv !=0){
			$content.='<div class="expandpost" data-toggle="collapse" data-target="#togglepost-'.$this->values->id.'">
			<i class="icon fa fa-arrow-down fa-fw " aria-hidden="true" title="Down" aria-label="Down"  style="display: inline;padding: 5px;"><span  id="updateText-'.$this->values->id.'" style="padding: 4px; font-family: Poppins,sans-serif;font-size: 1.03rem;">View Replies </span></i> </div>';
		}
		$content.='<div class="postreply collapse" id="togglepost-'.$this->values->id.'">'.$this->replydata.'</div>';		

		//if($this->isapprove_post=='y'){
			//Show Reply Box..
			$content.='<div id="myrep'.$this->values->discussion.'" data-text="show my Reply here"></div>
			'.$this->add_myreply();
		//}

		$content.='<div id="myerrmsg'.$this->values->discussion.'" data-text="show error messages" class="atlerrormessage"></div>
		<div style="clear:both;"></div>
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
	
	public function render_forumreply()
	{
		$yettoapprove = "";
		if($this->isapprove_post=='n'){
			$yettoapprove='<div class="atlapprove smalltext"></div>'; //UnApprove
		}
		$content = '<div class="reply" id="p'.$this->replyvalues->id.'">
		<div class="row header clearfix">
		<div class="left picture">'.$this->replyuserlink.'</div>

		<div class="topic firstpost starter">
		<div class="subject" role="heading" aria-level="2">'.$this->replyvalues->subject.'</div>
		<div class="atlclearfix">
		<div class="author" role="heading" aria-level="2">by
		<a href="'.$this->replyprofileurl.'">'.$this->replyuname.'</a> - '.$this->replyvalues->createddate.'
		</div>';

		if($this->replyvalues->userid == $this->userid || $this->userrole=='admin'){
			$content.='<div class="atlright">
			<a href="javascript:void(0);" data-id="'.$this->replyvalues->id.'" onclick="postdelete(this);"></a>
			</div>';
		}
		if($this->replyvalues->userid != $this->userid && $this->userrole!='admin'){
			//Misuse Report flag
			$content.='<div class="atlmisuse" title="Report Misuse">
			<a id="misa'.$this->replyvalues->id.'" href="javascript:void(0);" data-id="'.$this->replyvalues->id.'" onclick="postmisuse(this);"></a>
			</div>';
		}
		$content.= $yettoapprove;

		$content.='</div>
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

	public function showpopulartags($populartagdata)
	{
		/* commented on 09-May-2018
			<div class="inovation">
			<h5>Areas of Inovation</h5>
			<p>Agricultre</p>
			<p>Health care</p>
			<p>Education</p>
			<p>Transportation</p>
			<p>Climate</p>
			</div>
		*/
		$content = '
		<div class="populartags">
		  <span>Most Popular Tags</span>
		  <div class="ptag">'.$populartagdata.'</div>
		</div>
		';
		return $content;

	}

	public function popupbox(add_form $addformobj)
	{
		$content = '
		<div id="atlbox1" class="modal moodle-has-zindex hide" data-region="modal-container" aria-hidden="false" role="dialog" style="z-index: 1052;">
			<div class="modal-dialog modal-lg" role="document" data-region="modal" aria-labelledby="0-modal-title" >
			<div class="modal-content">
			<div class="modal-header " data-region="header">
			<button type="button" class="close closebtn" data-action="hide" aria-label="Close">
				<span aria-hidden="true">×</span>
			</button>
			<h4 id="0-modal-title" class="modal-title" data-region="title" tabindex="0">Add New Post</h4>
			</div>
			<div class="modal-body" data-region="body" style="">'.$addformobj->render().'

			</div>
			<div class="modal-footer" data-region="footer">
			<button id="postcancel1" type="button" class="btn btn-secondary closebtn" data-action="cancel">Cancel</button>
			</div>
			</div>
			</div>
		</div>';
		
		$content.= '
		<div id="atlboxdel" class="modal moodle-has-zindex hide" data-region="modal-container" aria-hidden="false" role="dialog" style="z-index: 1052;">
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
					<input type="hidden" name="liburlpath" id="liburlpath" value="">
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
					<input type="hidden" name="liburlpath" id="liburlpath" value="">
				</div>
			</div>
			</div>
		</div>';
		
		$content.= '
		<script type= "text/javascript">
			//atlbox2 is the Main Modal window ...inc_end.mustache
			
			var classname = document.getElementsByClassName("closebtn");

			var mycloseFunction = function() {
				//var attribute = this.getAttribute("data-myattribute");
				document.getElementById("atlbox2").classList.add("hide");					
				document.getElementById("atlboxdel").classList.add("hide");
				document.getElementById("atlboxdel").style.display = "none";
				document.getElementById("atlboxmisuse").classList.add("hide");
				document.getElementById("atlboxmisuse").style.display = "none";
				document.getElementById("atlbox1").classList.add("hide");
				document.getElementById("atlbox1").style.display = "none";
				//make field values blank if user close the poup;
				var searchelement =  document.getElementById("id_name");
				if (typeof(searchelement) != "undefined" && searchelement != null) {
					document.getElementById("id_name").value = "";
					document.getElementById("id_message").value = "";
					document.getElementById("id_postfile").value = "";
					document.getElementById("id_error_name").innerHTML = "";
					document.getElementById("id_error_message").innerHTML = "";
					document.getElementById("id_error_postfile").innerHTML = "";
					var x = document.getElementsByClassName("filepicker-filename");
					x[0].innerHTML = "";
				}
			};
			for (var i = 0; i < classname.length; i++) {
				classname[i].addEventListener("click", mycloseFunction, false);
			}
			
			document.getElementById("postdeletebtn").addEventListener("click",function(e) {
				deleteforumpost("");			
			});

			document.getElementById("postmisusebtn").addEventListener("click",function(e) {
				reportpostmisuse("");			
			});

			var searchelm =  document.getElementById("addnewpost");
			if (typeof(searchelm) != "undefined" && searchelm != null) {
				document.getElementById("addnewpost").addEventListener("click",function(e) {
					var searchname =  document.getElementById("id_name");
					if (typeof(searchname) != "undefined" && searchname != null) {
						document.getElementById("id_name").value = "";
						document.getElementById("id_message").value = "";
					}
					document.getElementById("atlbox2").classList.remove("hide");
					document.getElementById("atlbox1").classList.remove("hide");
					document.getElementById("atlbox1").style.display = "block";
				});
			}
		</script>';	

		return $content;
	}
	
	public function render_forummoderator()
	{
		$content = '<div class="forumpost clearfix lastpost firstpost starter" role="region" aria-label="forum-collaboration" id="p'.$this->values->id.'">
		<div class="row header clearfix">
		<div class="left picture">'.$this->userlink.'</div>

		<div class="topic firstpost starter">
		<div class="subject" role="heading" aria-level="2">'.$this->values->subject.'</div>
		<div class="atlclearfix">
		<div class="author" role="heading" aria-level="2">by
		<a href="'.$this->profileurl.'">'.$this->uname.'</a> - '.$this->values->createddate.'&nbsp;&nbsp;<span class="smallheading">'.$this->category.'</span>
		</div>';		
		//Approve or Delete an Student Posts By Atal Chief.
		$content.='<div class="atlright">
		<a href="javascript:void(0);" data-id="'.$this->values->id.'" onclick="postdelete(this);"></a>
		</div>';
		
		//$content.='<div class="atlapprove" data-id="'.$this->values->id.'"><a href="javascript:void(0);"></a></div>';	
		//Atal Incharge Project private Chat Approval button , 17-April-2018, currently Project chats cant be in forum..	
		
		$content.='</div>
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

		$content.='<div style="clear:both;"></div>
		</div>' ;

		return $content;
	}
	
	public function approvepopupbox(){
		$content.= '
		<div id="atlnewpopupbox" class="modal moodle-has-zindex hide" data-region="modal-container" aria-hidden="false" role="dialog" style="z-index: 1052;">
		<div class="modal-dialog modal-lg" role="document" data-region="modal" aria-labelledby="0-modal-title" >
		<div class="modal-content">
			<input name="action" value="forum" type="hidden">
			<input name="sesskey" value="txxcPlyyA6a" type="hidden">
			<input name="_qf__forum_approve_form" value="1" type="hidden">
			<div class="modal-header " data-region="header">
			<button type="button" class="close newclosebtn" data-action="hide" aria-label="Close">
				<span aria-hidden="true">×</span>
			</button>
			<h4 id="0-modal-title" class="modal-title" data-region="title" tabindex="0">Approve</h4>
			</div>
			<div class="modal-body" data-region="body" style="">Sure Want To Approve This Post ?
			</div>
			<div class="modal-footer" data-region="footer">
				<span id="approvemsg" style="display:none;float:left;color:blue;">Processing ...</span>
				<button id="postapprovebtn" type="button" class="btn btn-primary" data-action="save">Yes</button>
				<button type="button" class="btn btn-secondary newclosebtn" data-action="cancel">No</button>
				<input type="hidden" name="approvepostid" id="approvepostid">			
			</div>	
		</div>
		</div>
		</div>
		';
		return $content;
	}

	function renderForumCategory($categorylist)
	{
		$content = "<div class='card-block pull-left'><select id='category-listing' name='category' class='custom-select'><option value='0'>Select All</option>";
		foreach($categorylist as $category)
		{
			$content.="<option value='".$category->id."'>".$category->name."</option>";
		}
		$content.='</select>';
		//$content.='<input style="margin-left:10px;" id="list-category-btn" type="button" class="btn btn-primary" value="Apply">';
		$content.='</div>';
		return $content;
	}
	
	//Forum Misuse Page for NitiAdmin
	public function render_forummisuse()
	{
		$reportuser = array();
		$misusetype = get_misusetypes();
		$reportedBy = "";
		//,13-1-Rahul Sharma (userid,type,name)
		if(isset($this->values->reportedby)){
			$tmp = explode(",",$this->values->reportedby);
			foreach($tmp as $key=>$val){
				$tmpnew = explode("-",$val);
				$reportuser[$tmpnew['0']] = array($tmpnew['1'],$tmpnew['2']);
			}
		}
		if(count($reportuser)>0){
			$reportedBy.="<ul>";
			foreach($reportuser as $k=>$v){
				$reportedBy.= '<li><a href="'.getuser_profilelink($k).'">'.$v['1'].'</a> ('.$misusetype[$v['0']].')</li>';
			}
			$reportedBy.="</ul>";
		}
		$content = '<div class="forumpost clearfix lastpost firstpost starter" role="region" aria-label="forum-collaboration" id="p'.$this->values->id.'">
		<div class="row header clearfix">
		<div class="left picture">'.$this->userlink.'</div>

		<div class="topic firstpost starter">
		<div class="subject" role="heading" aria-level="2">'.$this->values->subject.'</div>
		<div class="atlclearfix">
		<div class="author" role="heading" aria-level="2">by
		<a href="'.$this->profileurl.'">'.$this->uname.'</a> - '.$this->values->createddate.'&nbsp;&nbsp;<span class="smallheading">'.$this->category.'</span>
		</br><span class="projectmessage" style="margin-left:0px;">Misuse Reported By:</span>
		<div class="misuselist">'.$reportedBy.'</div>
		</div>';		
		//Approve or Delete an Student Posts By Atal Chief.
		$content.='<div class="atlright">
		<a href="javascript:void(0);" data-id="'.$this->values->id.'" onclick="postdelete(this);"></a>
		</div>';
		$content.='<div class="atlapprove" data-id="'.$this->values->id.'">
		<a href="javascript:void(0);"></a>
		</div>';		
		
		$content.='</div>
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

		$content.='<div style="clear:both;"></div>
		</div>' ;

		return $content;
	}
}

?>
