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
 * @CreatedOn: 16-03-2018
 * @Description: Inovation Render Class of Project Module
*/

class innovation_render extends plugin_renderer_base {

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

	public function __construct($userid, $userrole) {
		global $DB, $PAGE, $CFG;
		$this->userid = $userid;
		$this->userrole = $userrole;
	}
	
	//Show Each Project with Mentors/Students in Assign Page, LoggedIn-Users: AtalChief & Student.
	public function showassignproject($values,$loggedinuser_role){
		$content = '<div id="innovid'.$values[0]['id'].'" class="project">
		<div><h5><a href="'.$values[0]['projectdetaillink'].'" class="plink">'.ucwords($values[0]['name']).'</a></h5></div>';		
		
		$showflag = $this->showadddelete_btn($loggedinuser_role,$values);
		
		if($showflag){
			$content.='
			<div class="add"><a href="javascript:void(0);" data-project="'.ucwords($values[0]['name']).'" data-action="mentor" data-pid="'.$values[0]['id'].'" class="openassign">Add Mentor</a></div>
			<div class="add"><a href="javascript:void(0);" data-project="'.ucwords($values[0]['name']).'" data-action="student" data-pid="'.$values[0]['id'].'" class="openassign">Add Student</a></div>
			';
		}
		$content.='<div class="clearb"></div>
		<table class="pcontent "><tr>
			<td class="detail">'.$values[0]['summary'].'</td>
			<td class="mentor">Assigned Mentors <div id="enm'.$values[0]['id'].'">';
				foreach($values as $k=>$v){
					$remove = "";
				    if($v['role']=='mentor'){
						if($showflag){
							//Remove this mentor user from this Project. If project is not Approved/InProgress.
							if($values[0]['completionnotify']==0){
								$remove='<p><a href="javascript:void(0);" data-user="'.encryptdecrypt_userid($v['userid']).'" data-project="'.encryptdecrypt_projectid($values[0]['id']).'" onclick="unenroluser(this)">Remove</a>';
								if($v['uenrolstat']==0){
									$remove.='</br><span>Approval Pending</span>';
								} else{ $remove.='</br><span>Approved</span>'; }
								$remove.='</p>';
							}
						}
						$content.='<div class="userprofile"><p>'.$v['userlink'].'</p>'.$v['firstname'].$remove.'</div>';					
					}
				}
		$content.='</div></td>
			<td class="student">Assigned Students <div id="ens'.$values[0]['id'].'">';
				foreach($values as $k=>$v){
					$remove="";
				    if($v['role']=='student'){
						$showflag = $this->showadddelete_btn($loggedinuser_role,$values,$v['userid']);
						if($showflag){
							//Remove this student user from this Project. If project is not Approved/InProgress.
							if($values[0]['completionnotify']==0){
								$remove='<p><a href="javascript:void(0);" data-user="'.encryptdecrypt_userid($v['userid']).'" data-project="'.encryptdecrypt_projectid($values[0]['id']).'" onclick="unenroluser(this)">Remove</a></p>';
							}
						}
						$content.='<div class="userprofile"><p>'.$v['userlink'].'</p>'.$v['firstname'].$remove.'</div>';
					}
				}
		$content.='</div></td>
		</table>';
		
		if($values[0]['loginuserrole']=='incharge')
		{
			$content.='<div class="projectbtnrow">';
			//Project Buttons
			if($values[0]['status']!="complete"){
				//Show Delete Project Btn
				$content.='<div class="add" >
				<input class="smallbtn atalbtn"  data-flag="d" data-project="'.ucwords($values[0]['name']).'" data-id="'.encryptdecrypt_projectid($values[0]['id']).'" name="deletebutton" value="Delete" type="button" onclick="setprojectstatus(this);"></div>';
			}
			if($values[0]['status']=="unapprove"){				
				//Show Reject Project Btn
				$content.='<div class="add" >
				<input class="smallbtn atalbtn"  data-flag="r" data-project="'.ucwords($values[0]['name']).'" data-id="'.encryptdecrypt_projectid($values[0]['id']).'" name="rejectbutton" value="Reject" type="button" onclick="setprojectstatus(this);"></div>';	
				//Show project Approve Btn
				$content.='<div class="add">
				<input class="smallbtn atalbtn"  data-flag="a" data-project="'.ucwords($values[0]['name']).'" data-id="'.encryptdecrypt_projectid($values[0]['id']).'" name="submitbutton" value="Approve" type="button" onclick="setprojectstatus(this);"></div>';
			}
			if($values[0]['status']=="active"){
				//Show Complete Project Btn
				$content.='<div class="add" >
				<input class="smallbtn atalbtn"  data-flag="c" data-project="'.ucwords($values[0]['name']).'" data-id="'.encryptdecrypt_projectid($values[0]['id']).'" name="completebutton" value="Complete" type="button" onclick="setprojectstatus(this);"></div>';	
			}			
			
			//Button row ends
			$content.='</div>';
		}
		
		if($values[0]['loginuserrole']=='student' && $values[0]['coursecommentcnt']!=0){
			$content.='<div class="projectbtnrow">';
			//Project Comments Messages
			$content.='<div class="projectmsg"><a refstat="'.$values[0]['status'].'" refid="'.$values[0]['id'].'" data-id="'.encryptdecrypt_projectid($values[0]['id']).'" href="javascript:void(0);" class="showprojectcomments">
			<i class="icon-bell" title="Notifications"></i>';
			$content.='<div class="msgcount" data-region="count-container" title="Notifications"></div></a>';
			$content.='</div>';
			$content.='</div>';
		} elseif($values[0]['loginuserrole']=='incharge' && $values[0]['coursecommentcnt']!=0){
			$content.='<div class="projectbtnrow">';
			//Project Comments Messages
			$content.='<div class="projectmsg"><a refstat="'.$values[0]['status'].'" refid="'.$values[0]['id'].'" data-id="'.encryptdecrypt_projectid($values[0]['id']).'" href="javascript:void(0);" class="showprojectmsgincharge">
			<i class="icon-bell" title="Notifications"></i>';
			$content.='<div class="msgcount" data-region="count-container" title="Notifications"></div></a>';
			$content.='</div>';
			$content.='</div>';
		} else{
			if($values[0]['status']=="reject"){
			$content.='<div class="projectbtnrow"><span class="projectmessage">Rejected</span>';
			$content.='</div>';
			}
		}
		
		$content.='</div>';
		return $content;
	}
	
	//Show List of Mentors,Students for project (assign-mentor-student-to-Project-page)
	public function popupbox($mentor,$student)
	{
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
				<div id="popproject" class="protitle"></div>
				<div id="popmentor" style="display:none;">
					<div class="sugmentor"><h6>Suggested Mentor</h6></div>
					<div class="watsonlist"></div>
					<div style="clear:both;margin-bottom:3%;"></div>
					<div class="mentorimg">
					<div id="watsonmentorsuggest">
						
					</div>
					</div>
					<div style="clear:both;margin-bottom:6%;"></div>
					<input data-id="" id="mentor" style="width:50%" type="text">
					<ul id="mentornamelist" class="custom-select" style="width: 50%; max-height: 170px; overflow: auto; padding: 2px; ">
					<li style="cursor:pointer;" value="0">Select Mentor</li>';
					if(count($mentor)>0){
						foreach($mentor as $key=>$value){
							$content.='<li class="customeditableselect" value="'.$value->userid.'">'.ucwords($value->firstname).' '.$value->lastname.'</li>';
						}
					}
				$content.='</ul>
				</div>
				<div id="popstudent" style="display:none;">
				<input data-id="" id="student" style="width:50%" type="text">
					<ul id="studentnamelist" class="custom-select" style="width: 50%; max-height: 170px; overflow: auto; padding: 2px; ">
					<li style="cursor:pointer;" value="0">Select Mentor</li>';
					if(count($student)>0){
						foreach($student as $key=>$value){
							$content.='<li class="customeditableselect" value="'.$value->userid.'">'.ucwords($value->firstname).' '.$value->lastname.'</li>';
						}
					}
				$content.='</ul>
				</div>
				<div id="selmentorschool" data-url="show selected mentor pic" style="float: right; margin-right: 20%;"></div>
				<div style="clear:both;margin-bottom:6%;"></div>							
			</div>
			<div class="modal-footer" data-region="footer">
			<button class="btn btn-primary" id="enrolbtn" type="button" data-action="save">Add</button>
			<button type="button" class="btn btn-secondary closebtn" data-action="cancel">Cancel</button>
			<input type="hidden" name="pid" id="pid">
			<input type="hidden" name="usertype" id="usertype">
			</div>
			</div>
			</div>
		</div>
		
		<script type= "text/javascript">
		//atlbox2 is the Main Modal window ...inc_end.mustache
		
		var classname = document.getElementsByClassName("closebtn");
		/*  var mycloseFunction = function() {
			//var attribute = this.getAttribute("data-myattribute");
			document.getElementById("atlbox2").classList.add("hide");
			document.getElementById("assignbox").classList.add("hide");
			document.getElementById("assignbox").style.display = "none";
			document.getElementById("popmentor").style.display = "none";
			document.getElementById("popstudent").style.display = "none";
			document.getElementById("mentor").value = "";			
			document.getElementById("student").value = "";			
			document.getElementById("selmentorschool").innerHTML = "";	
			//document.getElementsByClassName("customeditableselect").remove("active");
		
			//clearSelected("mentor");
			//clearSelected("student");
		}; 
		for (var i = 0; i < classname.length; i++) {
			classname[i].addEventListener("click", mycloseFunction, false);
		} */
		
		var classname1 = document.getElementsByClassName("openassign");
		var myassign = function() {
			var projectid = this.getAttribute("data-pid");
			var type = this.getAttribute("data-action");
			var projectname = this.getAttribute("data-project");
			if(type=="mentor"){
				document.getElementById("modal-title").innerHTML="Add Mentor";
				document.getElementById("popmentor").style.display = "block";
				document.getElementById("usertype").value = "mentor";
				document.getElementById("watsonmentorsuggest").innerHTML = "Searching ...";
				showwatsonmentor(projectid);
			} else{
				document.getElementById("modal-title").innerHTML="Add Student";
				document.getElementById("popstudent").style.display = "block";
				document.getElementById("usertype").value = "student";
			}
			document.getElementById("popproject").innerHTML=projectname;
			document.getElementById("atlbox2").classList.remove("hide");
			document.getElementById("assignbox").classList.remove("hide");
			document.getElementById("assignbox").style.display = "block";
			document.getElementById("pid").value = projectid;
			document.getElementById("selmentorschool").innerHTML="";
		};
		for (var i = 0; i < classname1.length; i++) {
			classname1[i].addEventListener("click", myassign, false);
		}
		
		document.getElementById("enrolbtn").addEventListener("click",function(e) {
			var xmlhttp = new XMLHttpRequest();
			var projectid = document.getElementById("pid").value;
			var uid1 = document.getElementById("mentor").value;
			if(document.getElementById("mentor").getAttribute("data-id")!="")
				var uid1 = document.getElementById("mentor").getAttribute("data-id");	
			var uid2 = document.getElementById("student").value;
			if(document.getElementById("student").getAttribute("data-id")!="")
				var uid2 = document.getElementById("student").getAttribute("data-id");	
			var usertype = document.getElementById("usertype").value;
			projectid = genraterandomnum1(projectid);
			var divid = "";
			xmlhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					var myObj = JSON.parse(this.responseText);
					if(myObj.success==0){
						document.getElementsByClassName("closebtn")[0].click();
						//mycloseFunction();
					} else{
						//success..
						document.getElementsByClassName("closebtn")[0].click();
						//mycloseFunction();			
						if(usertype=="mentor" && myObj.replyhtml!=""){
							divid = "enm"+document.getElementById("pid").value;	
							var node = document.createElement("DIV");
							node.className = "userprofile";
							node.innerHTML = myObj.replyhtml;
							document.getElementById(divid).appendChild(node);
						}
						if(usertype=="student" && myObj.replyhtml!=""){
							divid = "ens"+document.getElementById("pid").value;
							var node = document.createElement("DIV");
							node.className = "userprofile";
							node.innerHTML = myObj.replyhtml;
							document.getElementById(divid).appendChild(node);
						}
					}
				}
			};
			xmlhttp.open("GET", "ajax.php?id="+projectid+"&uid1="+uid1+"&uid2="+uid2+"&mode=enrol&type="+usertype, true);
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
	
	//Show Each Project with Mentors/Students in Assign Page, LoggedIn-Users: Mentor
	public function showassignproject_mentor($values){
		$content = '<div class="project">
		<div><h5><a href="'.$values[0]['projectdetaillink'].'" class="plink">'.ucwords($values[0]['name']).'</a></h5></div>';		
		
		$content.='<div class="clearb"></div>
		<div class="pcontent">
			<div class="detail">'.$values[0]['summary'].'</div>
			<div class="mentor">Assigned Mentors <div id="enm'.$values[0]['id'].'">';
				foreach($values as $k=>$v){
					$remove = "";
				    if($v['role']=='mentor'){						
						$content.='<div class="userprofile"><p>'.$v['userlink'].'</p>'.$v['firstname'].$remove.'</div>';					
					}
				}
		$content.='</div></div>
			<div class="student">Assigned Students <div id="ens'.$values[0]['id'].'">';
				foreach($values as $k=>$v){
					$remove="";
				    if($v['role']=='student'){						
						$content.='<div class="userprofile"><p>'.$v['userlink'].'</p>'.$v['firstname'].$remove.'</div>';
					}
				}
		$content.='</div></div>
		</div>';	
		
		$content.='</div>';
		return $content;
	}
	
	//Show Project Comments in Popup box/project Notifications
	public function popupbox_comments()	{
		$content = '
		<div id="newprojectpoup" class="modal moodle-has-zindex hide" data-region="modal-container" aria-hidden="false" role="dialog" style="z-index: 1052;">
			<div class="modal-dialog modal-lg" role="document" data-region="modal" aria-labelledby="0-modal-title" style="width:44%;">
			<div class="modal-content">
			<div class="modal-header " data-region="header">
			<button type="button" class="close newclosebtn" data-action="hide" aria-label="Close">
				<span aria-hidden="true">×</span>
			</button>
			<h4 id="modal-title" class="modal-title" data-region="title" tabindex="0">Notifications</h4>
			</div>
			<div class="modal-body" data-region="body" style="">
				<div id="projectmsgdiv"></div>
			</div>
			<div class="modal-footer" data-region="footer">
			<button type="button" class="btn btn-secondary newclosebtn" data-action="cancel">OK</button>
			</div>
			</div>
			</div>
		</div>
		';
		return $content;
	}
	
	//To Check whether to show ADD/Remove Student,Mentor Link to this user
	private function showadddelete_btn($loggedinuser_role,$values,$removeuserid=0){
		$flag = false;
		if ($loggedinuser_role=='incharge'){
			//Atal Incharge can do any thing Action in project.
			if($values[0]['status']=="complete"){
				return false;
			} else{
				return true;
			}
		} else if($loggedinuser_role=='student'){
			//Actions Taken By Users..
			if($removeuserid==0){
				if($values[0]['status']=="unapprove" && $this->userid==$values[0]['createdby']){
					$flag = true;
				}
				if($values[0]['status']=="reject"){
					$flag = false;
				}
			} else{
				//Student Remove Btn/Link
				if($values[0]['status']=="unapprove"){
					if($removeuserid==$this->userid){
						//Student cannot Remove himself from his own created project
						return false;
					} else if($this->userid==$values[0]['createdby']){
						//But Student Can Remove Other CoStudent from his Project.
						return true;
					}
				}
			}
		} else{ $flag = false; }
		return $flag;
	}
	
	//Show UnApprove Project with Mentors/Students in Assign Page, LoggedIn-Users: Mentor
	//Added: 27-May-2018
	public function showunapproveproject_mentor($values){
		$content = '<div class="project">
		<div><h5><a href="'.$values[0]['projectdetaillink'].'" class="plink">'.ucwords($values[0]['name']).'</a></h5></div>';	
		
		$content.='<div class="clearb"></div>
		<div class="pcontent">
			<div class="detail">'.$values[0]['summary'].'</div>
			<div class="mentor">Assigned Mentors <div id="enm'.$values[0]['id'].'">';
				foreach($values as $k=>$v){					
				    if($v['role']=='mentor'){						
						$content.='<div class="userprofile"><p>'.$v['userlink'].'</p>'.$v['firstname'].'</div>';					
					}
				}
		$content.='</div></div>
			<div class="student">Assigned Students <div id="ens'.$values[0]['id'].'">';
				foreach($values as $k=>$v){					
				    if($v['role']=='student'){						
						$content.='<div class="userprofile"><p>'.$v['userlink'].'</p>'.$v['firstname'].'</div>';
					}
				}
		$content.='</div></div>
		</div>';
		
		if($values[0]['loginuserrole']=='mentor')
		{
			//Project Buttons
			$content.='<div class="projectbtnrow">';				
			//Show Project Reject Btn
			$content.='<div class="add" >
			<input class="smallbtn atalbtn"  data-flag="r" data-project="'.ucwords($values[0]['name']).'" data-id="'.encryptdecrypt_projectid($values[0]['id']).'" name="rejectbutton" value="Reject" type="button" onclick="setmentorstatus(this);"></div>';
			//Show project Accept Btn
			$content.='<div class="add">
			<input class="smallbtn atalbtn"  data-flag="a" data-project="'.ucwords($values[0]['name']).'" data-id="'.encryptdecrypt_projectid($values[0]['id']).'" name="submitbutton" value="Accept" type="button" onclick="setmentorstatus(this);"></div>';
			//Button row ends
			$content.='</div>';
		}		
		$content.='</div>';
		return $content;
	}
}

?>
