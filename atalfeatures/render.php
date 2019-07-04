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
 * @CreatedBy: Jothi (IBM)
 * @CreatedOn: 16-2-2018
 * @Description: html render file.
*/

class AtalFeatures extends plugin_renderer_base {
	public $userid;
	public $DB;
	public $schoolid;
	public $usermsn;
	public function __construct($schoolid) {
	    global $DB, $PAGE, $CFG,$USER;
	    $this->userid = $USER->id;
	    $this->DB = $DB;
	    $this->schoolid = $schoolid;
	    $this->usermsn = $USER->msn;
	    $this->CFG = $CFG;
	}
	
	public function renderSchoolDetails($schooldata)
	{
		global $SESSION;
		$userrole = get_atalrolenamebyid($this->usermsn);
		$editbutton='';
		
		$html='<div">
		<h1>
		  <a class="btn btn-primary pull-right goBack">Back</a>
		</h1>
		</div>';
		if($userrole=="admin"){
			$editbutton = '<a style="padding-left:2%;" href="'. $this->CFG->wwwroot.'/create/createschool.php?id='.$this->schoolid.'"><img src="'.$this->CFG->wwwroot.'/theme/image.php/moove/theme/1519020214/addnew" height="20" width="20"></a> ';
		}
		$novalue = 'Not Specified';
		$html.="<table><tr><td width='60%' valign='top'><ul class='profilesearch' style='padding-top:0%'>";
		$html .="<div class='details' style='padding-top:1%;'><p><h5><font color='#daa520'>".ucwords($schooldata->name)."</font>".$editbutton."</h5></p></div>";
		$html .="<li class='details' style='padding-top:1%;'><p><h7>School Incharge: </h7>".$this->getSchoolIncharge()."</p></li>";
		$schooldata->address = $schooldata->address ?? $novalue;
		$html .="<li class='details' style='padding-top:1%;'><p><h7>School Address: </h7>".$schooldata->address."</p></li>";
		$schooldata->atl_id = $schooldata->atl_id ?? $novalue;
		$html .="<li class='details' style='padding-top:1%;'><p><h7>School ATL ID: </h7>".$schooldata->atl_id."</p></li>";
		$schooldata->school_emailid = $schooldata->school_emailid ?? $novalue;
		$html .="<li class='details' style='padding-top:1%;'><p><h7>School EmailId: </h7>".$schooldata->school_emailid."</p></li>";
		$schooldata->phone = $schooldata->phone ?? $novalue;
		$html .="<li class='details' style='padding-top:1%;'><p><h7>School Phone Number: </h7>".$schooldata->phone."</p></li>";
		$schooldata->principal_name = $schooldata->principal_name ?? $novalue;
		$html .="<li class='details' style='padding-top:1%;'><p><h7>Principal Name: </h7>".$schooldata->principal_name."</p></li>";
		$schooldata->principal_email = $schooldata->principal_email ?? $novalue;
		$html .="<li class='details' style='padding-top:1%;'><p><h7>Principal Email Address: </h7>".$schooldata->principal_email."</p></li>";
		$schooldata->principal_phone = $schooldata->principal_phone ?? $novalue;
		$html .="<li class='details' style='padding-top:1%;'><p><h7>Principal PhoneNumber: </h7>".$schooldata->principal_phone."</p></li>";
		$html .="</ul></td>"; //profilesearch class closure
		$html.=$this->render_student_list();
		$html .="</tr></table>";
		echo $html;
	}
	public function render_student_list()
	{
		$school = $this->schoolid;
		$sql="SELECT mus.userid,concat(mu.firstname,' ',mu.lastname) as studentname  FROM {user_school} mus join {user} mu on mu.id=mus.userid WHERE schoolid=$school and mus.role='student' and deleted=0 order by mu.firstname";
		$result = $this->DB->get_records_sql($sql);
		$content = '<td width="40%" valign="top">';
		if(count($result)>0)
		{
				$total = count((array)$result);
				$content.='<div  class="card-block"><h5><font color="#daa520">Students ( '.$total.' )</font></div>';
				$content.='<input type="text" id="searchstudent" style="width:60%">';
				$content.= '<ul id="studentname" style="width:60%;height:300px;margin-top:10px;overflow:auto;padding:2px;">';
				foreach($result as $student)
				{
					$student_detail = $this->CFG->wwwroot."/search/profile.php?key=".encryptdecrypt_userid($student->userid,"en");
					$content.='<li style="padding-top:3px;" value="'.ucfirst($student->studentname).'"><a class="blacktext" href="'.$student_detail.'">'.ucfirst($student->studentname).'</a></li>';
				}
				$content.= '</ul>';	
		}	
		$content.= '</td>';
		return $content;
	}
	public function getSchoolIncharge()
	{
		$record = $this->DB->get_record('user_school', array('schoolid'=>$this->schoolid,'role'=>'incharge'));
		if($record)
		{
			$userprofilelink = $this->CFG->wwwroot.'/search/profile.php?key='.encryptdecrypt_userid($record->userid,"en");
			$incharge_details = atal_getUserDetailsbyId($record->userid);
			$incharge_name = "<a href='$userprofilelink'>".$incharge_details->firstname.' '.$incharge_details->lastname."</a>";
			return $incharge_name;
		} else {
			return 'No Incharge Assigned';
		}
	}
	public function getGuideContents()
	{
		$content='';
		
		$threedprint = $this->CFG->wwwroot.'/mentorguides/Mentor - 3D Printing.pdf';
		$computaional = $this->CFG->wwwroot.'/mentorguides/Mentor - Computational Thinking.pdf';
		$design = $this->CFG->wwwroot.'/mentorguides/Mentor - Design Thinking.pdf';
		$digital = $this->CFG->wwwroot.'/mentorguides/Mentor - Digital Literacy.pdf';
		$effective = $this->CFG->wwwroot.'/mentorguides/Mentor - Effective Communication.pdf';
		$ideation = $this->CFG->wwwroot.'/mentorguides/Mentor - Ideation.pdf'; 
		$inspiring = $this->CFG->wwwroot.'/mentorguides/Mentor - Inspiring to Tinker.pdf'; 
		$physical = $this->CFG->wwwroot.'/mentorguides/Mentor - Physical Computing.pdf'; 
		$prototyping = $this->CFG->wwwroot.'/mentorguides/Mentor - Physical Prototyping.pdf';		
		$buildingrobots = $this->CFG->wwwroot.'/mentorguides/Mentor - Building Robots.pdf';
		$threeDmodeling = $this->CFG->wwwroot.'/mentorguides/Mentor - 3D Modeling.pdf';		
		$skills = $this->CFG->wwwroot.'/mentorguides/Mentor - Soft Skills.pdf'; 
		$howtouseportal = $this->CFG->wwwroot.'/atalfeatures/help.php'; 
		
		$content.= '<div class="card-block" style="width:50%;"><table class="table table-striped">';
		$content.='<tr><td>How To Use This Portal</td><td><a href="'.$howtouseportal.'">view<i class="icon fa fa-eye fa-fw " aria-hidden="true" title="View" aria-label="Hide"></i></a></td><td></td></tr>';
		$content.='<tr><td>3D Printing</td><td><a href="'.$threedprint.'">view<i class="icon fa fa-eye fa-fw " aria-hidden="true" title="View" aria-label="Hide"></i></a></td><td><a href="'.$threedprint.'" download>Download<i class="icon fa fa-arrow-down fa-fw " aria-hidden="true" title="Download" aria-label="Down"></i></a></td></tr>';
		$content.='<tr><td>Computational Thinking</td><td><a href="'.$computaional.'">view<i class="icon fa fa-eye fa-fw " aria-hidden="true" title="View" aria-label="Hide"></i></a></td><td><a href="'.$computaional.'" download>Download<i class="icon fa fa-arrow-down fa-fw " aria-hidden="true" title="Download" aria-label="Down"></a></td></tr>';
		$content.='<tr><td>Design Thinking</td><td><a href="'.$design.'">view<i class="icon fa fa-eye fa-fw " aria-hidden="true" title="View" aria-label="Hide"></i></a></td><td><a href="'.$design.'" download>Download<i class="icon fa fa-arrow-down fa-fw " aria-hidden="true" title="Download" aria-label="Download"></a></td></tr>';
		$content.='<tr><td>Digital Literacy</td><td><a href="'.$digital.'">view<i class="icon fa fa-eye fa-fw " aria-hidden="true" title="View" aria-label="Hide"></i></a></td><td><a href="'.$digital.'" download>Download<i class="icon fa fa-arrow-down fa-fw " aria-hidden="true" title="Download" aria-label="Download"></a></td></tr>';
		$content.='<tr><td>Effective Communication</td><td><a href="'.$effective.'">view<i class="icon fa fa-eye fa-fw " aria-hidden="true" title="View" aria-label="Hide"></i></a></td><td><a href="'.$effective.'" download>Download<i class="icon fa fa-arrow-down fa-fw " aria-hidden="true" title="Download" aria-label="Down"></a></td></tr>';
		$content.='<tr><td>Ideation</td><td><a href="'.$ideation.'">view<i class="icon fa fa-eye fa-fw " aria-hidden="true" title="View" aria-label="Hide"></i></a></td><td><a href="'.$ideation.'" download>Download<i class="icon fa fa-arrow-down fa-fw " aria-hidden="true" title="Download" aria-label="Download"></a></td></tr>';
		$content.='<tr><td>Inspiring to Tinker</td><td><a href="'.$inspiring.'">view<i class="icon fa fa-eye fa-fw " aria-hidden="true" title="View" aria-label="Hide"></i></a></td><td><a href="'.$inspiring.'" download>Download<i class="icon fa fa-arrow-down fa-fw " aria-hidden="true" title="Download" aria-label="Down"></a></td></tr>';
		$content.='<tr><td>Physical Computing</td><td><a href="'.$physical.'">view<i class="icon fa fa-eye fa-fw " aria-hidden="true" title="View" aria-label="Hide"></i></a></td><td><a href="'.$physical.'" download>Download<i class="icon fa fa-arrow-down fa-fw " aria-hidden="true" title="Download" aria-label="Down"></a></td></tr>';
		$content.='<tr><td>Physical Prototyping</td><td><a href="'.$prototyping.'">view<i class="icon fa fa-eye fa-fw " aria-hidden="true" title="View" aria-label="Hide"></i></a></td><td><a href="'.$prototyping.'" download>Download<i class="icon fa fa-arrow-down fa-fw " aria-hidden="true" title="Download" aria-label="Down"></a></td></tr>';
		$content.='<tr><td>Soft Skills</td><td><a href="'.$skills.'">view<i class="icon fa fa-eye fa-fw " aria-hidden="true" title="View" aria-label="Hide"></i></a></td><td><a href="'.$skills.'" download>Download<i class="icon fa fa-arrow-down fa-fw " aria-hidden="true" title="Download" aria-label="Down"></a></td></tr>';
		$content.='<tr><td>Building Robots</td><td><a href="'.$buildingrobots.'">view<i class="icon fa fa-eye fa-fw " aria-hidden="true" title="View" aria-label="Hide"></i></a></td><td><a href="'.$buildingrobots.'" download>Download<i class="icon fa fa-arrow-down fa-fw " aria-hidden="true" title="Download" aria-label="Down"></a></td></tr>';
		$content.='<tr><td>3D Modeling</td><td><a href="'.$threeDmodeling.'">view<i class="icon fa fa-eye fa-fw " aria-hidden="true" title="View" aria-label="Hide"></i></a></td><td><a href="'.$threeDmodeling.'" download>Download<i class="icon fa fa-arrow-down fa-fw " aria-hidden="true" title="Download" aria-label="Down"></a></td></tr>';
		$content.= '</table></div>';
		return $content;
	}
	public function getScormlist()
	{
		global $OUTPUT;
		$list = getScormResultset();
		$content='';
		$content.='<div class="card-block" style="width:55%;"><table class="table"><tr>';
		$status_array= array();
		if(count($list)>0)
		{
			$i=1;
			foreach($list as $key=>$values)
			{
				$src='';
				$href =  $this->CFG->wwwroot.'/mod/scorm/player.php?cm='.$values->modid.'&scoid='.$values->scormscosid;
				$status = checkScormStatusbyId($values->scormid,$values->courseid);
				//$src=(($i==1)?$OUTPUT->image_url('module1', 'theme'):(($i==2)?$OUTPUT->image_url('module2', 'theme'):$OUTPUT->image_url('module3', 'theme')));
				$icon_name = 'module_'.$i;
				$src=$OUTPUT->image_url($icon_name, 'theme');
				$status_array[$i]=($status)?1:0;
				$completionstatus = getScormStatus();
				if(!$completionstatus){
					$content.='<td><a href="'.$href.'"><img src="'.$src.'"><div>'.$values->scormname."</div></a><td>";
				}
				else{
					if($i==1)
					{
						if($status)
							$content.='<td class="grayout"><img src="'.$src.'"><div>'.$values->scormname."</div><td>";
						else
							$content.='<td><a href="'.$href.'"><img src="'.$src.'"><div>'.$values->scormname."</div></a><td>";
					}
					else
					{
						if(($status_array[$i]) || !$status_array[$i-1])
							$content.='<td class="grayout"><img src="'.$src.'"><div>'.$values->scormname."</div><td>";
						else
							$content.='<td><a href="'.$href.'"><img src="'.$src.'"><div>'.$values->scormname."</div></a><td>";
					}
					$i++;
				}
			}
		}
		$content.='</tr></table></div>';
		return $content;
	}
}

?>