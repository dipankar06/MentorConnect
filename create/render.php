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

/* @package: core_create
 * @CreatedBy: Jothi (IBM)
 * @CreatedOn: 9-03-2018
 * @Description:Class for rendering create tab functionality
*/

require_once('../external/commonrender.php');

class CreateStackholders extends CommonRender {
	public function __construct() {
	    global $DB, $PAGE, $CFG,$USER;
	    $this->userid = $USER->id;
	    $this->DB = $DB;
	    $this->usermsn = $USER->msn;
	    $this->CFG = $CFG;
		$this->recordsperpage=25;
		$this->total_mentors=getTotalMentorsCount();
		$this->total_schools=getTotalSchoolCount();
	}
//Fucntion to display a Tab 
	/*
	* function create a Mentor list
	* returns HTML Content
	*/
	public function rendercreatetab()
	{
		$mentor_link = $this->CFG->wwwroot.'/create';
		$school_link=$this->CFG->wwwroot.'/create/listschool.php';
		$event_link = $this->CFG->wwwroot.'/create/listevent.php';
		$content = '<div class="card-text content">
		<div id="block-createtab" class="block-createtab" data-region="createtab">
			<ul id="block-createtab-view-choices" class="nav nav-tabs" role="tablist">
				<li class="nav-item">
				<a id="v1" class="nav-link active" href="'.$mentor_link.'" data-tabname="mentors" aria-expanded="true">Mentors </a>
				</li>
				<li class="nav-item">
				<a id="v2" class="nav-link" href="'.$school_link.'" data-tabname="schools" aria-expanded="false">Schools</a>
				</li>
				<li class="nav-item">
				<a id="v2" class="nav-link" href="'.$event_link.'" data-tabname="events" aria-expanded="false">Events</a>
				</li>
			</ul>
		<div class="tab-content content-centred tabcontentatal">';
		$content.=$this->render_mentor_content();
		$content.='</div></div></div>'; //Close tabcotent,block-createab,card-text
	return $content;
	}
	/*
	* function create a school list
	* returns HTML Content
	*/
	public function rendercreatetab_school()
	{
		$mentor_link = $this->CFG->wwwroot.'/create';
		$school_link=$this->CFG->wwwroot.'/create/listschool.php';
		$event_link = $this->CFG->wwwroot.'/create/listevent.php';
		$content = '<div class="card-text content">
		<div id="block-createtab" class="block-createtab" data-region="createtab">
			<ul id="block-createtab-view-choices" class="nav nav-tabs" role="tablist">
				<li class="nav-item">
				<a id="v1" class="nav-link " href="'.$mentor_link.'" data-tabname="mentors" aria-expanded="false">Mentors </a>
				</li>
				<li class="nav-item">
				<a id="v2" class="nav-link active" href="'.$school_link.'" data-tabname="schools" aria-expanded="true">Schools</a>
				</li>
				<li class="nav-item">
				<a id="v2" class="nav-link " href="'.$event_link.'" data-tabname="events" aria-expanded="false">Events</a>
				</li>
			</ul>
		<div class="tab-content content-centred tabcontentatal">';
		$content.=$this->render_school_content();
		$content.='</div></div></div>'; //Close tabcotent,block-createab,card-text
	return $content;
	}
	/*
	* function create a Event list
	* returns HTML Content
	*/
	public function rendercreatetab_event()
	{
		$mentor_link = $this->CFG->wwwroot.'/create';
		$school_link=$this->CFG->wwwroot.'/create/listschool.php';
		$event_link = $this->CFG->wwwroot.'/create/listevent.php';
		$content = '<div class="card-text content">
		<div id="block-createtab" class="block-createtab" data-region="createtab">
			<ul id="block-createtab-view-choices" class="nav nav-tabs" role="tablist">
				<li class="nav-item">
				<a id="v1" class="nav-link " href="'.$mentor_link.'" data-tabname="mentors" aria-expanded="false">Mentors </a>
				</li>
				<li class="nav-item">
				<a id="v2" class="nav-link" href="'.$school_link.'" data-tabname="events" aria-expanded="false">Schools</a>
				</li>
				<li class="nav-item">
				<a id="v2" class="nav-link active" href="'.$event_link.'" data-tabname="events" aria-expanded="true">Events</a>
				</li>
			</ul>
		<div class="tab-content content-centred tabcontentatal">';
		$content.=$this->render_event_content();
		$content.='</div></div></div>'; //Close tabcotent,block-createab,card-text
	return $content;
	}
	
	/*
	* function create a student list
	* returns HTML Content
	*/
	public function render_student()
	{
		$schoolinfo = get_incharge_schoolid( $this->userid);
		$create_student_link=$this->CFG->wwwroot.'/project/createstudent.php';
		$content = '<div class="card card-block"><div role="main"><div class="assignmentor">
		<div class="heading card-block"><h3>'.$schoolinfo->name.' - Students List</h3><div style="float:right"><h5><a href="'.$create_student_link.'">Add New Student</a></h5></div></div>';
		$content.=$this->massuploadform();
		$content.=$this->getstudent_content($schoolinfo->schoolid);	
		$content.='</div></div></div>';
		return $content;
	}
	public function getstudent_content($school_id)
	{
		global $OUTPUT;
		$studentlist = student_list($school_id);
		$content.='<table class="table">';
		if(count($studentlist)==0)
		{
			$content.='<tr><td style="width:50%"> No records Found </td></tr>';
		}
		else
		{
			foreach($studentlist as $student)
			{
				$detailpagelink = $this->CFG->wwwroot.'/search/profile.php?key='.encryptdecrypt_userid($student->userid,"en");
				$content.='<tr><td style="width:50%">';
				if($student->picture!='0')
					$content.='<div><p><a href="'.$detailpagelink.'">'.$student->picture.'</a></p></div>';
				$content.='<div class="username"><p>'.$student->firstname.' '.$student->lastname.'</p></div>';
				$content.='<div><p>'.$student->email.'</p></div>';
				if(isset($student->studentclass))
					$content.='<div><p>Class : '.$student->studentclass.'th Std.</p></div>';
				$content.='</td>';
				$content.='<td style="width:50%;text-align:center;"><a href="'. $this->CFG->wwwroot.'/project/createstudent.php?key='.encryptdecrypt_userid($student->userid,"en").'">Edit<img src="'.$OUTPUT->image_url('editicon', 'theme').'" title=" Edit Student" alt=" Edit Student "></a><a style="margin-left:10px;" data-content="student" class="user-delete alink" data-id="'.$student->userid.'">Delete<img src="'.$OUTPUT->image_url('deleteicon', 'theme').'" title=" Delete Student" alt=" Delete Student "></a></td>';
				$content.='</tr>';
			}
		}
		$content.='</table>';
		return $content;
	}
//Fucntion to create a Mentor Lisiting 
public function render_mentor_content()
{
	global $OUTPUT;
	$content='';
	$page=1;
	$link = $this->CFG->wwwroot.'/atalfeatures/creatementor.php';
	//$content= $OUTPUT->heading('ATAL Mentors');
	$content.="<div class='overlay'>
		<div id='atlloaderimage' style='display:none;'></div>
		</div>";
	$content.= '<div id="myoverview_forum_view" class="tab-pane fade active in" role="tabpanel" aria-expanded="true"><div style="margin-top:2%;"><h5>ATAL Mentors('.$this->total_mentors.')</h5></div><div style="float:right"><h5> <a href="'.$link.'"> Add New Mentor</a></h5></div>';
	$content.=$this->filterform('mentor');
	$content.=$this->display_mentors(1);
	//echo "here";die;
	$content.='</div>';
	return $content;
}
// Fucntion to create Events Listing
public function render_event_content()
{
	global $OUTPUT;
	$content= $OUTPUT->heading('ATAL Events');
	$link = $this->CFG->wwwroot.'/create/createevent.php';
	$content='<div id="myoverview_event_view"class="tab-pane fade active in" role="tabpanel" aria-expanded="true"><div style="margin-top:2%;">
	</div><div style="float:right"><h5><a href="'.$link.'"> Add New Event</a></h5></div>';
	//$content.=$this->filterform('school');
	//$content.=$this->display_schools(1);
	$event_list=event_list();
	$content.=$this->generate_event_html($event_list);
	$content.='</div>';
	return $content;
}
//Fucntion to create School Listing
public function render_school_content()
{
	global $OUTPUT;
	//$content= $OUTPUT->heading('ATAL Mentors');
	$content='';
	$link = $this->CFG->wwwroot.'/create/createschool.php';
	$content.="<div class='overlay'>
		<div id='atlloaderimage' style='display:none;'></div>
		</div>";
	$content.='<div id="myoverview_timeline_view"class="tab-pane fade active in" role="tabpanel" aria-expanded="true"><div style="margin-top:2%;"><h5>ATAL Schools('.$this->total_schools.')</h5></div><div style="float:right"><h5> <a href="'.$link.'"> Add New School</a></h5></div>';
	$content.=$this->filterform('school');
	$content.=$this->display_schools(1);
	$content.='</div>';
	return $content;
}
public function display_mentors($page=1,$name='',$state=0,$city=0)
{
	global $OUTPUT;
	$condition='';
	$name = trim($name);
	if($name!='')
		$condition=" AND (firstname LIKE '%".$name."%' OR lastname LIKE '%".$name."%' OR email LIKE '%".$name."%')";
	if($state!='')
		$condition.=' AND aim='.$state;
	if($city!==0)
		$condition.=' AND city="'.$city.'"';
	$limit=$this->recordsperpage;
	$content='<div class="table-content-wrapper" id="table-content-wrapper">';
	$start_from = ($page-1) * $limit;  
	if($condition)
	{
		$mentor_list =mentor_list($this->recordsperpage,$start_from,$condition);
		$this->total_mentors =getTotalMentorsCount($condition);
	}
	else
		$mentor_list =mentor_list($this->recordsperpage,$start_from);
	$content.='<table class="table">';
	if(count($mentor_list)==0)
	{
		$content.='<tr><td style="width:50%"> No records Found </td></tr>';
	}
	else
	{
		foreach($mentor_list as $userdata)
		{
				$content.='<tr><td style="width:50%">';
				$detailpagelink = $this->CFG->wwwroot.'/search/profile.php?key='.encryptdecrypt_userid($userdata->id,"en");
				if($userdata->picture!='0')
					$content.='<div><p><a href="'.$detailpagelink.'">'.$userdata->picture.'</a></p></div>';
				$content.='<div class="username"><p>'.$userdata->firstname.' '.$userdata->lastname.' ,'.$userdata->city.'</p></div>';
				$content.='<div><p>'.$userdata->email.'</p></div>';
				$content.='<div><p>'.$userdata->department.'</p></div>';
				$content.='</td>';
				$content.='<td style="width:50%;text-align:center;"><a href="'. $this->CFG->wwwroot.'/atalfeatures/editmentor.php?key='.encryptdecrypt_userid($userdata->id,"en").'">Edit<img src="'.$OUTPUT->image_url('editicon', 'theme').'" title=" Edit Mentor" alt=" Edit Mentor "></a><a style="margin-left:10px;" data-content="mentor" class="user-delete alink" data-id="'.$userdata->id.'">Delete<img src="'.$OUTPUT->image_url('deleteicon', 'theme').'" title=" Delete Mentor" alt=" Delete Mentor "></a></td>';
				$content.='</tr>';
		}
	}
	$content.='</table>';
	$content.='<div class="pagination-wrapper" align="center">';
	($this->total_mentors>1)?$total_pages =ceil(($this->total_mentors)/$this->recordsperpage):$total_pages =1;	
	$content.= $this->paginate_function($this->recordsperpage,$page,$this->total_mentors,$total_pages,'mentor');
	$content.='</div>';	
	$content.='</div>';
	return $content;
}
public function display_schools($page=1,$name='',$state='',$city=0)
{
	global $OUTPUT;
	$name = trim($name);
	if($name!='')
		$condition=" AND (name LIKE '%".$name."%' OR atl_id LIKE '%".$name."%')";
	if($city==0 && $state!='')
		$condition.=' AND cityid IN (select id from {city} where stateid='.$state.')';
	if($city!=0)
		$condition.=' AND cityid='.$city;
	$limit=$this->recordsperpage;
	$content='<div class="table-content-wrapper" id="table-content-wrapper">';
	$start_from = ($page-1) * $limit;  
	if($condition)
	{
		$school_list =school_list($this->recordsperpage,$start_from,$condition);
		$this->total_schools =getTotalSchoolCount($condition);
	}
	else
		$school_list =school_list($this->recordsperpage,$start_from);
	$content.='<table class="table">';
	if(count($school_list)==0)
	{
		$content.='<tr><td style="width:50%"> No records Found </td></tr>';
	}
	else
	{
		foreach($school_list as $schooldata)
		{				
			$content.='<tr><td style="width:50%">';
			$city = get_atal_citybycityid($schooldata->cityid);
			$school_detail = $this->CFG->wwwroot.'/atalfeatures/schooldetail.php?id='.$schooldata->id;
			if(count($city)>0)
				$content.='<div style="font-size: 1.15rem;"><p><a class="badge-name" href="'.$school_detail.'">'.$schooldata->name.' , '.$city[$schooldata->cityid]->name.'</a></p></div>';
			else
				$content.='<div><p><a href="'.$school_detail.'">'.$schooldata->name.'</a></p></div>';
			if($schooldata->userid!='')
				$content.='<div><p>Incharge : <a class="blacktext" href="'.$schoolddata->userid.'">'.$schooldata->firstname.' '.$schooldata->lastname.'</a></p></div>';
			$content.='</td>';
			$school_edit = $this->CFG->wwwroot.'/create/createschool.php?id='.$schooldata->id;
			$content.='<td style="width:50%;text-align:center;"><a href="'. $school_edit.'">Edit<img src="'.$OUTPUT->image_url('editicon', 'theme').'" title=" Edit School" alt=" Edit School "></a><a style="margin-left:10px;" data-content="school" class="user-delete alink" data-id="'.$schooldata->id.'">Delete<img src="'.$OUTPUT->image_url('deleteicon', 'theme').'" title=" Delete School" alt=" Delete School "></a></td>';
			$content.='</tr>';
		}
	}
	$content.='</table>';
	$content.='<div class="pagination-wrapper" align="center">';
	($this->total_schools>1)?$total_pages =ceil(($this->total_schools)/$this->recordsperpage):$total_pages =1;	
	$content.= $this->paginate_function($this->recordsperpage,$page,$this->total_schools,$total_pages,'school');
	$content.='</div>';	
	$content.='</div>';
	return $content;
}
public function generate_event_html($eventlist)
{
	global $OUTPUT;
	$content.='<table class="table">';
	if(count($eventlist)==0)
	{
		$content.='<tr><td style="width:50%"> No records Found </td></tr>';
	}
	else
	{
		foreach($eventlist as $event)
		{
			$event_edit = $this->CFG->wwwroot.'/create/createevent.php?id='.$event->id;
			$content.='<tr><td style="width:50%"><div class="project"><p><h8>Event Name </h8>: ';
			$content.='<span style="color:#007473;">'.$event->name.'</span></p>';
			$content.='<p>Description : '.$event->description.'</p>';
			$content.='<p>Started On : '.date('m/d/Y', $event->timestart).'</p></div></td>';
			$content.='<td style="width:50%;text-align:center;">';
			$content.='<a href="'. $event_edit.'">Edit<img src="'.$OUTPUT->image_url('editicon', 'theme').'" title=" Edit School" alt=" Edit School "></a><a style="margin-left:10px;" data-content="event"class="user-delete alink" data-id="'.$event->id.'">Delete<img src="'.$OUTPUT->image_url('deleteicon', 'theme').'" title=" Delete Event" alt=" Delete Event "></a>';
			$content.='</td>
			</tr>';
		}
		
	}
	$content.='</table>';
	return $content;
}
public function filterform($name)
{
	$states = get_atal_allstates();
	if($name == 'mentor')
		$placeholder = 'Search by Mentor Name/Email';
	else
		$placeholder = 'Search by School Name/ATLid';
	$content='<div class="filteroption" id="filter-'.$name.'" style="margin-top: 5%; margin-bottom: 10%;"><form autocomplete="off" method="post" accept-charset="utf-8" id="filter-form" class="mform"><div style="float:left;width:25%;margin-right:6px;"><input placeholder="'.$placeholder.'" class="form-control" type="textbox" name="name" id="id_name" ></div><div style="float:left;width:25%;margin-left:6px;"><select class="form-control" name="state" id="id_state"><option value="">Select State</option>';
	foreach($states as $state)
	{
		$content.='<option value="'.$state->id.'">'.$state->name.'</option>';
	}
	$content.='</select></div>
	<div style="float:left;width:25%;margin-left:6px;"><select class="form-control" name="cityid" id="id_cityid"><option value="0">Select District</option></select></div><input type="hidden" name="city" id="id_city"><div style="float:left;width:20%;margin-left:6px;"><input type="button" name="search" id="searchby-filters" value="Search" class="btn  btn-primary"><a style="margin-left:6px;" href="" class="btn  btn-primary">Reset</a></div></form></div>';
	return $content;
}
	public function massuploadform()
	{
		global $OUTPUT;
		//$url =$OUTPUT->image_url('sample_excel', 'theme');
		$url =$this->CFG->wwwroot.'/create/STUDENT-LIST.xlsx';
		$src =$OUTPUT->image_url('excelicon', 'theme');
		//$url = $this->CFG->wwwroot."/theme/".$this->CFG->theme."/pix/sample_excel.png"; 
		$content='<div class="card card-block" >
	<div style="margin-bottom:3%;"><h5><span class="heading">Bulk upload students list to this portal </span></h5></div>
	<div class="col-xs-12">
	<form action="upload.php" method="post" enctype="multipart/form-data" name="massuploadstudent" id="massuploadstudent">
	<div class="form-group row  fitem   ">
			<div class="col-md-3">
				<span class="pull-xs-right text-nowrap">
					<abbr class="initialism text-danger" title="Required"><i class="icon fa icon-exclamation text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
				<i class="icon fa icon-question text-info fa-fw " aria-hidden="true" title="Choose xls" aria-label="Choose xls"></i>
				</span>
				<label class="col-form-label d-inline " for="fileToUpload">
					Choose File to be Uploaded
				</label>
			</div>
			<div class="col-md-9 form-inline felement" data-fieldtype="group">
			<div class="form-inline felement form-group fitem" data-fieldtype="text">
				<input class="form-control " id="file-to-upload" name="file" id="fileToUpload" value="" size="50" maxlength="100" placeholder="Choose xls" type="file">	
				 <small id="fileHelp" class="form-text text-muted text-danger">Upload Excel (xls,xlsx) Sheet Only! </small>
			</div>
			<div class="form-inline felement form-group fitem" style="margin-left: 1%;">
					<p><a href="'.$url.'">    Download <img width="29px;" src="'.$src.'"></a></p>				
					<small id="fileHelp" class="form-text text-muted text-danger">Please Use Only this Excel Format</small>					
			</div>
			</div>
	</div>
	</div>
    <div class="col-md-9 form-inline felement" data-fieldtype="group">
	</div>
	</div>
	<div class="form-group row  fitem femptylabel  " data-groupname="buttonar">
		<div class="col-md-3"></div>
		<div class="col-md-9 form-inline felement" data-fieldtype="group">
			<div class="form-group  fitem  ">
				<label class="col-form-label " for="id_submitbutton">
				</label>
				<span data-fieldtype="submit">
					<input class="btn btn-primary " name="submit" id="id_submit" value="Upload File" type="submit">
				</span>
			</div>   
			 <div class="form-group  fitem   btn-cancel">
				<label class="col-form-label " for="id_cancel">
				</label>
				<span data-fieldtype="submit">
					<input class="btn btn-primary" name="cancel" id="id_cancel" value="Cancel" onclick="skipClientValidation = true; return true;" type="button">
				</span>
			</div>
		</div>
	</div>
<input type="hidden" value="y" name="flag">
<input type="hidden" value="Upload File" name="submit"  type="submit">
    <!--<input type="file" name="file" id="fileToUpload">
    <input type="button" value="Upload" name="submit">-->
</form>
</div>
</div>';
	return $content;	
	}
}