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
 * @CreatedOn: 21-12-2017
 * @Description: Render Class of Project Module
*/

class project_render extends plugin_renderer_base {

	public $userid;
	public $userrole;
	
	public $values;
	public $userlink;
	public $profileurl;
	public $post_image;
	public $uname;
	public $replydata;

	public $replyvalues;
	public $replyuserlink;
	public $replyprofileurl;
	public $replypost_image;
	public $replyuname;
	public $recordsperpage;
	public $total_schools;
	public function __construct($userid, $userrole) {
		global $DB, $PAGE, $CFG;
		$this->userid = $userid;
		$this->userrole = $userrole;
		$this->total_schools=0;
		$this->recordsperpage=25;
	}
	public function getbackbutton()
	{
		global $CFG;
		$backlink = $CFG->wwwroot.'/create/liststudent.php';
		$content='<div class="card-block">
	<h1>
	<a class="btn btn-primary pull-right goBack">Back</a>
	</h1>
	</div>';
		return $content;
	}
	public function sidebar_mentor(){
		$content = '<div><h3 id="instance-0-header" class="blocktitle">Mentors</h3>
		</div>';
		$content.= suggestedmentors();
		return $content;
	}
	
	public function sidebar_student(){		
		$content= studentlist();
		return $content;
	}
	
	public function sidebar_project_assets($course){
		global $OUTPUT, $CFG, $PAGE;
		
		//$OUTPUT->image_url('projectimg/robotarm', 'theme')
		$content = '<div><h3 id="instance-0-header" class="blocktitle">Assets</h3>
		</div>';		
		$content.='<div id="projectasset">';
		
		$renderer = $PAGE->get_renderer('format_topics');
		$aa = $renderer->print_multi_section_pagenew($course, null, null, null, null);
		//print multi section - is in Moove theme classes - format-topic-render
		$content.=$aa .'</div>';		
		
		$content.= '<div class="watsonlistsrch">
		<div>
		<input type="text" name="wastsonsearch" size="10">
		</div>
		<div><a href="javascript:void(0);"><img src="'.$OUTPUT->image_url('watson', 'theme').'"></a>
		</div>
		<div style="clear:both;"></div>';
		return $content;
	}
	
	public function sidebar_incharge(){		
		$content= lhs_inchargelist();
		return $content;
	}
	
	//Side-Bar Function Ends..
	
	
	
	/* Display List of Schools with Assign Mentors
	@CreatedBy: Dipankar (IBM)
	@CreatedOn: 04/01/2017	
	*/
	public function showassignmentors($values){
		global $USER;
		$userrole = get_atalrolenamebyid($USER->msn);
		$content = '<div class="project">';
		if($userrole=='admin'){
			$detailpageurl = new moodle_url('/atalfeatures/schooldetail.php', array('id' => $values['id']));
			$content.='<div><h5><a href="'.$detailpageurl.'" class="badge-name">'.ucwords($values['name']).', '.$values['city'].'</a></h5></div>';
		} else{
			$content.='<div><h5>'.ucwords($values['name']).', '.$values['city'].'</h5></div>';
		}
		
		$content.='		
		<div class="add">
			<a href="javascript:void(0);" data-school="'.ucwords($values['name']).'" data-action="mentor" data-sid="'.$values['id'].'" class="schmentor">Add mentor</a>			
		</div>		
		<div class="pcontent">
			<div class="detail"></div>
			<div class="mentor">ATL Incharge <div id="ein'.$values['id'].'">';
				if(count($values['incharge']>0)){
					foreach($values['incharge'] as $k=>$v){						
						$content.='<div class="userprofile"><p>'.$v['userlink'].'</p>'.$v['firstname'].'</div>';
					}
				}
		$content.='</div></div>
			<div class="student">Assigned Mentors <div id="ems'.$values['id'].'">';
				if(count($values['mentor']>0)){
					foreach($values['mentor'] as $k=>$v){
						$remove='<p><a href="javascript:void(0);" data-user="'.encryptdecrypt_userid($v['userid']).'" 
						data-sid="'.$values['id'].'" onclick="removementorschool(this)">Remove</a>';
						$content.='<div class="userprofile"><p>'.$v['userlink'].'</p>'.$v['firstname'].$remove.'</div>';
					}
				}
		$content.='</div></div>
		</div>
		</div>';
		return $content;
	}
	
	//Show List of school with assign Mentors,Students,Incharge (assign-mentor-to-school-page)
	public function mentorpopupbox($mentor)
	{
		$content = '
		<div id="mentorbox" class="modal moodle-has-zindex hide" data-region="modal-container" aria-hidden="false" role="dialog" style="z-index: 1052;">
			<div class="modal-dialog modal-lg" role="document" data-region="modal" aria-labelledby="0-modal-title" style="width:44%;">
			<div class="modal-content">
			<div class="modal-header " data-region="header">
			<button type="button" class="close closebtn" data-action="hide" aria-label="Close">
				<span aria-hidden="true">×</span>
			</button>
			<h4 id="modaltitle" class="modal-title" data-region="title" tabindex="0"></h4>
			</div>
			<div class="modal-body" data-region="body" style="">
				<div id="popschool" class="protitle"></div>
				<div id="popmentor" style="display:none;">
					<div class="sugmentor"><h6>Suggested Mentor</h6></div>
					<div class="watsonlist"></div>
					<div style="clear:both;margin-bottom:2%;"></div>
					<div class="mentorimg">					
					<div id="watsonmentorsuggest">
						
					</div>
					</div>
					<div style="clear:both;margin-bottom:4%;"></div>
					<input data-id="" id="mentor" style="width:50%" type="text">
					<ul id="mentornamelist" class="custom-select" style="width: 50%; max-height: 170px; overflow: auto; padding: 2px; ">
					<li style="cursor:pointer;" value="0">Select Mentor</li>';
					if(count($mentor)>0){
						foreach($mentor as $key=>$value){
							$content.='<li class="customeditableselect" value="'.$value->id.'">'.ucwords($value->firstname).' '.$value->lastname.'</li>';
						}
					}
				$content.='</ul>
				</div>
				<div id="selmentorschool" data-url="show selected mentor pic" style="float: right; margin-right: 20%;"></div>
				<div style="clear:both;margin-bottom:6%;"></div>				
			</div>
			<div class="modal-footer" data-region="footer">
			<button class="btn btn-primary" id="assignbtn" type="button" data-action="save">Add</button>
			<button type="button" class="btn btn-secondary closebtn" data-action="cancel">Cancel</button>
			<input type="hidden" name="sid" id="sid">
			<input type="hidden" name="usertype" id="usertype">
			</div>
			</div>
			</div>
		</div>
		
		<script type="text/javascript">
		//atlbox2 is the Main Modal window ...inc_end.mustache		
		
		/* var classname1 = document.getElementsByClassName("schmentor");
		var mentortoschool = function() {
			var schoolid = this.getAttribute("data-sid");
			var type = this.getAttribute("data-action");
			var schoolname = this.getAttribute("data-school");
			document.getElementById("modaltitle").innerHTML="Assign Mentor";
			document.getElementById("popmentor").style.display = "block";
			document.getElementById("usertype").value = "mentor";
			document.getElementById("popschool").innerHTML=schoolname;
			document.getElementById("sid").value = schoolid;
			document.getElementById("atlbox2").classList.remove("hide");
			document.getElementById("mentorbox").classList.remove("hide");
			document.getElementById("mentorbox").style.display = "block";
			document.getElementById("selmentorschool").innerHTML="";
			showwatsonmentor_forschool(schoolid);			
		};
		for (var i = 0; i < classname1.length; i++) {
			classname1[i].addEventListener("click", mentortoschool, false);
		}
		
		var classname = document.getElementsByClassName("closebtn");
		var mycloseFunction = function() {
			document.getElementById("atlbox2").classList.add("hide");
			document.getElementById("mentorbox").classList.add("hide");
			document.getElementById("mentorbox").style.display = "none";
			document.getElementById("mentor").value = "";			
			document.getElementById("selmentorschool").innerHTML = "";	
			
			//clearSelected("mentor");
		};
		for (var i = 0; i < classname.length; i++) {
			classname[i].addEventListener("click", mycloseFunction, false);
		}
		*/
		document.getElementById("assignbtn").addEventListener("click",function(e) {	
			var xmlhttp = new XMLHttpRequest();
			var schoolid = document.getElementById("sid").value;
			var uid1 = document.getElementById("mentor").value;		
			if(document.getElementById("mentor").getAttribute("data-id")!="")
				var uid1 = document.getElementById("mentor").getAttribute("data-id");			
			var usertype = document.getElementById("usertype").value;
			schoolid = genraterandomnum1(schoolid);
			var divid = "";
			xmlhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					var myObj = JSON.parse(this.responseText);
					if(myObj.success==0){
							document.getElementsByClassName("closebtn")[0].click();
						//mycloseFunction();
					} else{
						//success..
						//mycloseFunction();				
						document.getElementsByClassName("closebtn")[0].click();						
						if(usertype=="mentor" && myObj.replyhtml!=""){
							divid = "ems"+document.getElementById("sid").value;	
							var node = document.createElement("DIV");
							node.className = "userprofile";
							node.innerHTML = myObj.replyhtml;
							document.getElementById(divid).appendChild(node);
						}						
					}
				}
			};
			xmlhttp.open("GET", "ajax.php?id="+schoolid+"&uid1="+uid1+"&mode=assign&type="+usertype, true);
			xmlhttp.send();
		});		
		
		function clearSelected(selectid){
			var elements = document.getElementById(selectid).options;
			for(var i = 0; i < elements.length; i++){
				elements[i].selected = false;
			}
		}

		</script>		
		';
		
		return $content;
	}
	
	public function project_collaboration()
	{
		$content = '<div class="forumpost clearfix lastpost firstpost starter" role="region" aria-label="forum-collaboration" id="p'.$this->values->id.'">
		<div class="row header clearfix">
		<div class="left picture">'.$this->userlink.'</div>

		<div class="topic firstpost starter">
		<div class="subject" role="heading" aria-level="2">'.$this->values->subject.'</div>
		<div class="atlclearfix">
		<div class="author" role="heading" aria-level="2">by
		<a href="'.$this->profileurl.'">'.$this->uname.'</a> - '.$this->values->createddate.'
		</div>
		<div class="atlright">
		<a href="javascript:void(0);" data-id="'.$this->values->id.'" onclick="postdelete(this);"></a>
		</div>
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
		</div>
		<div class="postreply">
		'.$this->replydata.'
		</div>

		<div id="myrep'.$this->values->discussion.'" data-text="show my Reply here"></div>
		'.$this->add_myreply().'
		
		<div id="myerrmsg'.$this->values->discussion.'" data-text="show error messages" class="atlerrormessage"></div>
		<div style="clear:both;"></div>
		</div>' ;

		return $content;
	}
	
	public function render_forumreply()
	{
		$content = '<div class="reply" id="p'.$this->replyvalues->id.'">
		  <div class="row header clearfix">
		  <div class="left picture">'.$this->replyuserlink.'</div>

		  <div class="topic firstpost starter">
		  <div class="subject" role="heading" aria-level="2">'.$this->replyvalues->subject.'</div>
		  <div class="atlclearfix">
		  <div class="author" role="heading" aria-level="2">by
		  <a href="'.$this->replyprofileurl.'">'.$this->replyuname.'</a> - '.$this->replyvalues->createddate.'
		  </div>
		  <div class="atlright">
		  <a href="javascript:void(0);" data-id="'.$this->replyvalues->id.'" onclick="postdelete(this);"></a>
		  </div>
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
	
	public function add_jsscript(){	
		
		$content ='
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
					<input type="hidden" name="liburlpath" id="liburlpath" value="">
				</div>			
				</div>
			</div>
		</div>';
		
		$content.='
		<script type= "text/javascript">		
		var classname = document.getElementsByClassName("closebtn");
		var mycloseFunction = function() {
			//var attribute = this.getAttribute("data-myattribute");
			document.getElementById("atlbox2").classList.add("hide");
			document.getElementById("assignbox").classList.add("hide");
			document.getElementById("assignbox").style.display = "none";
			document.getElementById("atlboxdel").classList.add("hide");
			document.getElementById("atlboxdel").style.display = "none";
		};
		for (var i = 0; i < classname.length; i++) {
			classname[i].addEventListener("click", mycloseFunction, false);
		}
		document.getElementById("addasset").addEventListener("click",function(e) {
			document.getElementById("atlbox2").classList.remove("hide");
			document.getElementById("assignbox").classList.remove("hide");
			document.getElementById("assignbox").style.display = "block";
		});
		
		document.getElementById("postdeletebtn").addEventListener("click",function(e) {
			deleteforumpost("");				
		});
		</script>
		';
		
		return $content;
	}
	
	public function assetpopupbox($projectid,$frmobject)
	{
		global $CFG;
		$content = '
		<div id="assignbox" class="modal moodle-has-zindex hide" data-region="modal-container" aria-hidden="false" role="dialog" style="z-index: 1052;">
			<div class="modal-dialog modal-lg" role="document" data-region="modal" aria-labelledby="0-modal-title" style="width:44%;">
			<div class="modal-content">
			<div class="modal-header " data-region="header">
			<button type="button" class="close closebtn" data-action="hide" aria-label="Close">
				<span aria-hidden="true">×</span>
			</button>
			<h4 id="modal-title" class="modal-title" data-region="title" tabindex="0"></h4>
			</div>
			<div class="modal-body" data-region="body" style="">				
			';
			$content.= $frmobject->render();
			$content.='</div>
			<div class="modal-footer" data-region="footer">		
			</div>
			</div>
			</div>
		</div>			
		';
		
		return $content;
	}
	public function filterform($name)
	{
		$states = get_atal_allstates();
		$content='<div class="filteroption" id="filter-'.$name.'" style="margin-top: 5%; margin-bottom: 10%;"><form autocomplete="off" method="post" accept-charset="utf-8" id="filter-form" class="mform"><div style="float:left;width:25%;margin-right:6px;"><input placeholder="Search by School Name/ATLid" class="form-control" type="textbox" name="name" id="id_name" ></div><div style="float:left;width:25%;margin-left:6px;"><select class="form-control" name="state" id="id_state"><option value="">Select State</option>';
		foreach($states as $state)
		{
			$content.='<option value="'.$state->id.'">'.$state->name.'</option>';
		}
		$content.='</select></div>
		<div style="float:left;"><select class="custom-select" name="cityid" id="id_cityid"><option value="0">Select District</option></select></div><input type="hidden" name="city" id="id_city"><div style="float:left;margin-left:6px;"><input style="margin-left:5px;" type="button" name="search" id="searchby-filters" value="Search" class="btn  btn-primary"><a style="margin-left:15px;" href="" class="btn  btn-primary">Reset</a></div></form></div>';
		return $content;
	}
	public function render_mentor_suggested_school()
	{
		global $OUTPUT;
		$suggestionlist = get_mentor_suggested_school();
		$newarray = array();
		foreach($suggestionlist as $key=>$value)
		{
			$newarray[$value->userid][]= $value;
		}
		$content.='<table class="table">';
		$content.='<th>Mentor Details</th>';
		$content.='<th>School of Mentor Choice</th>';
		if(count($suggestionlist)==0)
		{
			$content.='<tr><td style="width:50%"> No records Found </td></tr>';
		}
		else
		{
			foreach($newarray as $list)
			{
				$content.='<tr>';
				
				$detailmentorlink = $this->CFG->wwwroot.'/search/profile.php?key='.encryptdecrypt_userid($list[0]->id,"en");
				$content.='<td>';
				$usrimg = get_userprofilepic($list[0]);
				$userurl = getuser_profilelink($list[0]->id);
				$picture ='<div class="left picture"><a href="'.$userurl.'" >'.$usrimg.'</a></div>'; 
				$content.='<div><p><a href="'.$detailmentorlink.'">'.$picture.'</a></p></div>';
				$content.='<div class="username"> <p> Name :'.$list[0]->firstname.' '.$list[0]->lastname.'</p></div>';
				$content.='<div><p> Email : '.$list[0]->email.'</p></div>';
				if($list[0]->aim!='')
					$state = get_atal_statebystateid($list[0]->aim);
				$content.='<div><p>District : '.$list[0]->city.'</p></div></td>';
				$content.='<td>';
				foreach($list as $key=>$value)
				{
					$detailpagelink = $this->CFG->wwwroot.'../atalfeatures/schooldetail.php?id='.$value->schoolid;
					$content.='<div><p><input type="radio" name="schooldetail-'.$value->id.'" class="schooldetail-choice" data-choice-id="'.$value->choiceid.'" data-user-id="'.$value->id.'" data-school-id="'.$value->schoolid.'" data-info-id="'.$value->infoid.'"> Name :<a href="'.$detailpagelink.'">'.$value->name.'</a></p>';
					$content.='<p>ATL-id : '.$value->atl_id.'</a></p>';
					$city= get_atal_citybycityid($value->cityid); 
					if(count($city)>0){
						$state = get_atal_statebystateid($city[$value->cityid]->stateid);
						$content.='<p>District : '.$city[$value->cityid]->name.' , '.$state->name.'</p>';
					}
					if(count($list)>1)
						$content.='<hr>';
				}
				$content.='</td>';
				$content.='<td style="align:center;"><div><p><a class="assign-mentor-school alink">Assign<img src="'.$OUTPUT->image_url('editicon', 'theme').'" title=" Assign Mentor to School" alt=" Assign Mentor to School "></a></div>';
				//<a style="margin-left:10px;" class="remove-mentor-school alink">Reject<img src="'.$OUTPUT->image_url('deleteicon', 'theme').'" title=" Remove Mentor From School" alt=" Remove Mentor From School "></a>
				$content.='</td></tr>';
			}
		}
		$content.='</table>';
		return $content;
	}
	/*
					$detailpagelink = $this->CFG->wwwroot.'../atalfeatures/schooldetail.php?id='.$value->schoolid;
					$detailmentorlink = $this->CFG->wwwroot.'/search/profile.php?key='.encryptdecrypt_userid($value->id,"en");
					$userurl = getuser_profilelink($value->id);
					$content.='<tr><td style="width:40%">';
					$usrimg = get_userprofilepic($value);
					$picture ='<div class="left picture"><a href="'.$userurl.'" >'.$usrimg.'</a></div>'; 
					$content.='<div><p><a href="'.$detailmentorlink.'">'.$picture.'</a></p></div>';
					$content.='<div class="username"> <p> Name :'.$value->firstname.' '.$value->lastname.'</p></div>';
					$content.='<div><p> Email : '.$value->email.'</p></div>';
					if($value->aim!='')
						$state = get_atal_statebystateid($value->aim);
					$content.='<div><p>District : '.$value->city.' , '.$value->name.'</p></div></td>';
					$content.='<td style="width:30%">';
					$content.='<div><p>Name :<a href="'.$detailpagelink.'">'.$value->name.'</a></p></div>';
					$content.='<div><p>ATL-id : '.$value->atl_id.'</a></p></div>';
					$city= get_atal_citybycityid($value->cityid);
					if(count($city)>0){
						$state = get_atal_statebystateid($city[$value->cityid]->stateid);
						$content.='<div><p>District : '.$city[$value->cityid]->name.' , '.$state->name.'</p></div>';
					}
					$content.='</td>';
					$content.='<td style="width:30%;text-align:center;"><a data-id="'.$value->id.'" data-user-id="'.$value->id.'" data-school-id="'.$value->schoolid.'" class="assign-mentor-school alink">Assign<img src="'.$OUTPUT->image_url('editicon', 'theme').'" title=" Assign Mentor to School" alt=" Assign Mentor to School "></a><a style="margin-left:10px;" class="remove-mentor-school alink" data-user-id="'.$value->id.'" data-info-id="'.$value->infoid.'">Reject<img src="'.$OUTPUT->image_url('deleteicon', 'theme').'" title=" Remove Mentor From School" alt=" Remove Mentor From School "></a></td>';
					$content.='</tr>';
	*/
	//27-jan-2018...
	/*public function assetpopupbox($projectid)
	{
		global $CFG;
		$content = '
		<div id="assignbox" class="modal moodle-has-zindex hide" data-region="modal-container" aria-hidden="false" role="dialog" style="z-index: 1052;">
			<div class="modal-dialog modal-lg" role="document" data-region="modal" aria-labelledby="0-modal-title" style="width:44%;">
			<div class="modal-content">
			<div class="modal-header " data-region="header">
			<button type="button" class="close closebtn" data-action="hide" aria-label="Close">
				<span aria-hidden="true">×</span>
			</button>
			<h4 id="modal-title" class="modal-title" data-region="title" tabindex="0"></h4>
			</div>
			<div class="modal-body" data-region="body" style="">				
				<h5>Upload File to Project (pdf,video,images only)</h5>
				</br>
				<form name="asset" method="POST" enctype="multipart/form-data" action="'.$CFG->wwwroot.'/project/detail.php?id='.$projectid.'">
				<input type="file" name="projectfile" id="projectfile">
				</br>
				<div class="enrollbtn" align="center">				
				<input type="hidden" name="pid" id="pid" value="'.$projectid.'">
				<input type="hidden" name="assetflag" id="assetflag" value="y">
				<button class="btn btn-primary" id="enrolbtn" type="submit" value="Submit">Add</button>
				</div>
				</form>
			</div>
			<div class="modal-footer" data-region="footer">		
			</div>
			</div>
			</div>
		</div>			
		';
		
		return $content;
	}*/
	
	/*
	public function sidebar_project_assets($data){
		global $OUTPUT, $CFG;
		
		//$OUTPUT->image_url('projectimg/robotarm', 'theme')
		$content = '<div><h3 id="instance-0-header" class="blocktitle">Assets</h3>
		</div>';		
		$content.='<div style="margin-top:2%;clear:both;">';
		if(count($data)>0){
			foreach($data as $keys=>$value){
				$typ = strtolower($value->type);
				if($typ=="jpg" || $typ=="jpeg" || $typ=="png" || $typ=="gif"){
					$content.='<img src="'.$CFG->wwwroot.'/projectupload/'.$value->name.'" width="100"><br>';
				}
				if($typ=="pdf"){
					$content.='<embed src="'.$CFG->wwwroot.'/projectupload/'.$value->name.'" width="200px" /><br>';
				}
				if($typ=="mp4"){
					$content.=' <video width="200" height="200" controls>
					<source src="'.$CFG->wwwroot.'/projectupload/'.$value->name.'" type="video/mp4">
					<source src="movie.ogg" type="video/ogg">
					Your browser does not support the video tag.
					</video> ';
				}
			}
		}
		$content.='</div>';		
		$content.= '<div class="watsonlistsrch">
		<div>
		<input type="text" name="wastsonsearch" size="10">
		</div>
		<div><a href="javascript:void(0);"><img src="'.$OUTPUT->image_url('watson', 'theme').'"></a>
		</div>
		<div style="clear:both;"></div>';
		return $content;
	}
	*/
	
}

?>
