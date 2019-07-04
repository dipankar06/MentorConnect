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

/* @package: core_nitiadmin
 * @CreatedBy: Jothi (IBM)
 * @CreatedOn: 20-06-2018
 * @Description: render
*/

require_once('../create/lib.php');
require_once('../mentor/lib.php');
require_once('../external/commonrender.php');

/*
 * Class to constrct HTML structure for NitiAdmin Administration Pages
*/

class NitiAdministrationRender extends CommonRender {
	public $userid;
	public $DB;
	public $usermsn;
	public $recordsperpage;
	public function __construct() {
	    global $DB, $PAGE, $CFG,$USER;
	    $this->userid = $USER->id;
	    $this->DB = $DB;
	    $this->usermsn = $USER->msn;
	    $this->CFG = $CFG;
		$this->recordsperpage=25;
	}
	/*
	* Function to construct a Wrapper for Administration Menus
	* Params : null
	* Returns : HTML
	*/
	public function getNitiAdminMenus()
	{
			$content = '<div class="card-text content card-block">
			<div id="block-createtab" class="block-createtab" data-region="createtab">
			<ul id="block-createtab-view-choices" class="nav nav-tabs" role="tablist">
				<li class="nav-item">
				<a id="v1" class="nav-link active" href="#mail" data-tabname="mail" role="tab" data-toggle="tab" aria-expanded="true">Mail configuration </a>
				</li>
				<li class="nav-item">
				<a id="v2" class="nav-link" href="#report"  data-tabname="mentor" role="tab" data-toggle="tab"  aria-expanded="false">Report</a>
				</li>
				<li class="nav-item">
				<a id="v3" class="nav-link" href="#admin"  data-tabname="all" role="tab" data-toggle="tab"  aria-expanded="false">Others</a>
				</li>	
			</ul>
		<div class="tab-content content-centred tabcontentatal">';
		$content.=$this->renderNitiadminMenus();
		$content.='</div></div></div>'; //Close tabcotent,block-createab,card-text
		return $content;
	}
	

	/*
	* Function to construct a Links for Administration Menus
	* Params : null
	* Returns : HTML string
	*/
	public function renderNitiadminMenus()
	{
		//$content= $OUTPUT->heading('ATAL Mentors');
		$maillink=$this->CFG->wwwroot.'/atalfeatures/mail.php';
		$maillink_in = $this->CFG->wwwroot.'/atalfeatures/mailtouser.php';
		$mailconfiglink=$this->CFG->wwwroot.'/atalfeatures/mailconfig.php';
		$bulkmail_link = $this->CFG->wwwroot.'/atalfeatures/bulkmail.php';
		$mentor_report= $this->CFG->wwwroot.'/nitiadmin/mentorreport.php';
		$school_report= $this->CFG->wwwroot.'/nitiadmin/schoolreport.php';
		$mentor_listreport= $this->CFG->wwwroot.'/nitiadmin/mentorlist.php';
		$school_listreport= $this->CFG->wwwroot.'/nitiadmin/schoollist.php';
		$student_listreport= $this->CFG->wwwroot.'/nitiadmin/studentlist.php';
		$ms_meeting_listreport= $this->CFG->wwwroot.'/nitiadmin/usermeeting.php';
		$student_report= $this->CFG->wwwroot.'/nitiadmin/studentreport.php';
		$user_tickets= $this->CFG->wwwroot.'/ticket';
		$ms_session_report=$this->CFG->wwwroot.'/nitiadmin/sessionreport.php';
		$content= '<div id="mail" class="tab-pane fade active in" role="tabpanel" aria-expanded="true">
			<div class="container">
                    <div class="row">
                        <div class="col-sm-9">
                            <ul>
								<p><li><a href="'.$maillink.'">Send Welcome Mail</a></li></p>
								<p><li><a href="'.$maillink_in.'">Send Mail to Individual</a></li></p>
								<p><li><a href="'.$bulkmail_link.'">Send Bulk Mail</a></li></p>
								<p><li><a href="'.$mailconfiglink.'">Configure Mailing Template (During Account Creation)</a></li></p>
                            </ul>
                        </div>
                    </div>
                </div>';
		$content.='</div>';
		$content.= '<div id="report" class="tab-pane fade" role="tabpanel" aria-expanded="true">';
		$content.='<div class="container">
                    <div class="row">
					      <div class="col-sm-3">
                                    <h4><a>User Report</a></h4><h4>
                                </h4>
							</div>
							<div class="col-sm-9">
								<ul class="list-unstyled">
									 <p><li><a href="'.$mentor_listreport.'">Mentors List</a></li></p>
									 <p><li><a href="'.$school_listreport.'">Schools List</a></li></p>
									 <p><li><a href="'.$student_listreport.'">Students List</a></li></p>
								</ul>
							</div>
                    </div>
					<hr>
					<div class="row">
					      <div class="col-sm-3">
                                    <h4><a>User Activity Report</a></h4><h4>
                                </h4>
							</div>
							<div class="col-sm-9">
								<ul class="list-unstyled">
									 <p><li><a href="'.$mentor_report.'">Mentor Activity Report</a></li></p>
									 <p><li><a href="'.$school_report.'">School Activity Report</a></li></p>
									 <p><li><a href="'.$student_report.'">Student Activity Report</a></li></p>
									 <p><li><a href="'.$ms_meeting_listreport.'">User Meeting Report</a></li></p>
									 <p><li><a href="'.$ms_session_report.'">Mentor&School Session Report</a></li></p>
								</ul>
							</div>
                    </div>
                </div>';
		$content.='</div>';
		$content.= '<div id="admin" class="tab-pane fade" role="tabpanel" aria-expanded="true">';
		$content.='<div class="container">
                    <div class="row">
					      <div class="col-sm-3">
                                    <h4><a>Ticket System</a></h4><h4>
                                </h4>
							</div>
							<div class="col-sm-9">
								<ul class="list-unstyled">
									 <p><li><a href="'.$user_tickets.'">User Tickets</a></li></p>							 
								</ul>
							</div>
                    </div>
					<hr>					
                </div>';
		$content.='</div>';
		
		return $content;
	}
	/*
	* Function to construct a HTML container for Mentor Activity Report Page
	* Params : null
	* Returns : HTML string
	*/
	public function getMentorReportHtml()
	{
		$html='';
		$html.=$this->renderLoaderContent();
		$total_mentors = getTotalMentorsCount();
		$html.="<div class='pull-right'>
		<a class='btn btn-primary' href='downloadexcel.php?tp=mentoribm' style='margin-right: 5px;'> Download IBM Mentors </a><a class='btn btn-primary' href='downloadexcel.php?tp=mentoractivity'> Download in Excel Fomat </a></div></div><br><br>";
		$html.='<div id="mentor-report-filter" style="margin-bottom: 1%;margin-top: 2%;">
		<div style="float:left;width: 35%;"><input placeholder="Search by Mentor Email" class="form-control" name="id_mentoremail" id="id_mentoremail" type="textbox"></div>
		<div style="float:left;margin-left: 1%;"><input id="mentorreport-filter-name" class="btn btn-primary" name="search" value="Search" type="button"></div><div style="float:left;margin-left: 1%;"><input id="mentorreport-reset" class="btn btn-primary" name="reset" value="Reset" type="button" data-mode="all"></div>';
		$html.= "<div class='pull-right' >
		<select name='filter-dropdown' id='filter-dropdown'>
		<option value='all'>Select All</option>
		<option value='completed'>Completed Tutorial</option>
		<option value='notstarted'>Not Started Tutorial</option>
		<option value='profile'>Updated Profile</option>
		<option value='noprofile'>Not Updated Profile</option>
		<option value='login'>Attempted Login</option>
		<option value='nologin'>Not Attempted Login</option>
		</select>";
		$html.="</div></div>";
		$conditionresult = getTutorialNotStartedMentor();
		if($conditionresult)
			$condition = ' and u.id in ('.$conditionresult.')';
		else
			$condition='';
		$meetingresult= getTotalMeetingCount('AND e.meetingstatus = 1');
		$latest_value = getTotalMentorsCount("and u.policyagreed=1");
		$html.="<div></br></br><table class='table bordered-table-report' style='margin-left:10%;width:75%'>
		<tr><td style='text-align:left !important'>Total Mentors in system : </td><td style='text-align:left !important'>". getTotalMentorsCount()."</td><td style='text-align:left !important'>Percentage</td></tr>
		<tr><td style='text-align:left !important'>No. of mentors who have attempted login : </td><td style='text-align:left !important'>".$latest_value."</td><td style='text-align:left !important'>".getPercentage($latest_value,$total_mentors)."</td></tr>";
		$latest_value = getTotalMentorsCount("and u.profilestatus=1");
		$html.="<tr><td style='text-align:left !important'>No. of mentors who have finished updating the profile : </td><td style='text-align:left !important'>".$latest_value."</td><td style='text-align:left !important'>".getPercentage($latest_value,$total_mentors)."</td></tr>";
		$latest_value = getTotalMentorsCount($condition);
		$html.="<tr><td style='text-align:left !important'>No. of mentors who have started the mentor training module : </td><td style='text-align:left !important'>".getTotalMentorsCount($condition)."</td><td style='text-align:left !important'>".getPercentage($latest_value,$total_mentors)."</td></tr>";
		$html.="<tr><td style='text-align:left !important'>No. of Mentors who have taken at least 1 session : </td><td style='text-align:left !important'>".$meetingresult."</td><td style='text-align:left !important'>".getPercentage($meetingresult,$total_mentors)."</td></tr>
		</table></div>";
		$html.= "<div id='table-content-wrapper' style='margin-top: 2%;'>";
		$html.= $this->generateContent();
		$html.="</div>";
		return $html;
	}
	/*
	* Function to construct a HTML container for Mentor List Report Page
	* Params : null
	* Returns : HTML
	*/
	public function getMentorListReportHtml()
	{
		$html='';
		$html.=$this->renderLoaderContent();
		$html.='<div id="mentor-report-filter" style="margin-bottom: 1%;margin-top: 2%;">
		<div style="float:left;width: 35%;"><input placeholder="Search by Mentor Email" class="form-control" name="id_mentoremail" id="id_mentoremail" type="textbox"></div>
		<div style="float:left;margin-left: 1%;"><input id="mentorreport-filter-name" class="btn btn-primary" name="search" value="Search" type="button"></div><div style="float:left;margin-left: 1%;"><input id="mentorreport-reset" class="btn btn-primary" name="reset" value="Reset" type="button" data-mode="mentorlist"></div>';
		$html.= "<div class='pull-right' >";
		$html.="</div></div>";
		$html.= "<div id='table-content-wrapper' style='margin-top: 6%;'>";
		$html.= $this->generateListContent_Mentor();
		$html.="</div>";
		return $html;
	}
	/*
	* Function to construct a HTML container for School List Report Page
	* Params : null
	* Returns : HTML string
	*/
	public function getSchoolListReportHtml()
	{
		$html='';
		$html.=$this->renderLoaderContent();		
		$filtermode  = 'filter-school-atlid';
		$resetmode = 'allschool-list';
		$html.='<div id="school-report-filter" style="margin-bottom: 1%;margin-top: 2%;">
		<div style="float:left;width: 35%;">
		<input placeholder="Search by School ATLid" class="form-control"	name="schoolatlid" id="id_schoolatlid" type="textbox"></div>
		<div style="float:left;margin-left: 1%;">
		<input id="schoolreport-filter-atlid" class="btn btn-primary" name="search" value="Search" type="button"></div>
		<input id="filter-mode" name="mode" value="'.$filtermode.'" type="hidden"></div>
		<div style="float:left;margin-left: 1%;">
		<input id="reset-mode" name="mode" value="'.$resetmode.'" type="hidden">
		<input id="schoolreport-reset" class="btn btn-primary" name="reset" value="Reset" type="button"></div>';
		$html.= "<div class='pull-right' >";
		$html.="</div></div>";
		$html.= "<div id='table-content-wrapper' style='margin-top: 6%;'>";
		$html.= $this->generateListContent_School();
		$html.="</div>";
		return $html;
	}
	/*
	* Function to construct a HTML container for Scjool Activity Report Page
	* Params : null
	* Returns : HTML string
	*/
	public function getSchoolActivityReportHtml()
	{
		$html='';
		$html.=$this->renderLoaderContent();
		//echo $start = microtime(true);
		//echo "<br>";
		$filtermode  = 'filter-schoolactivity-atlid';
		$resetmode = 'all-schoolactivity';
		$html.='<div id="school-report-filter" style="margin-bottom: 1%;margin-top: 2%;">
		<div style="float:left;width: 35%;">
		<input placeholder="Search by School ATLid" class="form-control"	name="schoolatlid" id="id_schoolatlid" type="textbox"></div>
		<div style="float:left;margin-left: 1%;">
		<input id="schoolreport-filter-atlid" class="btn btn-primary" name="search" value="Search" type="button"></div>
		<input id="filter-mode" name="mode" value="'.$filtermode.'" type="hidden"></div>
		<div style="float:left;margin-left: 1%;">
		<input id="reset-mode" name="mode" value="'.$resetmode.'" type="hidden">
		<input id="schoolreport-reset" class="btn btn-primary" name="reset" value="Reset" type="button"></div>';

		$html.= "<div class='pull-right' >
		<select name='filter-dropdown' id='filter-dropdown'>
		<option value='all'>Select All</option>
		<option value='profile'>Updated Profile</option>
		<option value='noprofile'>Not Updated Profile</option>
		<option value='login'>Attempted Login</option>
		<option value='nologin'>Not Attempted Login</option>
		</select>";
		$html.="</div>";

		$html.= "<div class='pull-right'>";
		$total_school = getTotalSchoolCount();
		$latest_value = getTotalSchoolCount('AND mu.policyagreed=1');
		$html.='<a class="btn btn-primary" href="downloadexcel.php?tp=schoolactivity"> Download in Excel Fomat </a>';
		$html.="</div></div>";
		$html.="<div></br></br><table class='table bordered-table-report' style='margin-left:10%;width:75%'>
		<tr><td style='text-align:left !important'>Total Schools in system </td><td style='text-align:left !important'>".$total_school."</td><td style='text-align:left !important'>Percentage</td></tr>
		<tr><td style='text-align:left !important'>No. of schools who have attempted login</td><td style='text-align:left !important'>".$latest_value."</td> <td style='text-align:left !important'>".getPercentage($latest_value,$total_school)."</td></tr>";
		$latest_value = getTotalSchoolCount('AND mu.profilestatus = 1');
		$html.="<tr><td style='text-align:left !important'>No. of schools who have finished updating the profile </td><td style='text-align:left !important'>".$latest_value."</td><td style='text-align:left !important'>".getPercentage($latest_value,$total_school)."</td></tr>
		</table></div>";
		$html.= "<div id='table-content-wrapper' style='margin-top: 6%;'>";
		$html.= $this->generateActivityReportContent_School();
		$html.="</div>";
		return $html;
	}
	/*
	* Function to construct a HTML container for Student Activity Report
	* Params : null
	* Returns : HTML string
	*/
	public function getStudentReportHtml()
	{
		$html='';
		$html.=$this->renderLoaderContent();
		$total_students = getTotalStudentCount();
		$html.='<div id="student-report-filter" style="margin-bottom: 1%;margin-top: 2%;">
		<a class="btn btn-primary pull-right" href="downloadexcel.php?tp=studentactivity" style="margin-right: 5px;">Download in Excel Fomat </a><br><br>
		<div style="float:left;width: 35%;">
		<input placeholder="Search by Student Email" class="form-control" name="studentemail" id="id_studentemail" type="textbox" data-info="filter-studentactivity-email"></div><div style="float:left;width: 35%;margin-left:5px;">
		<input placeholder="Search by School Name/ATLid" class="form-control" name="schoolfilter" id="id_schoolfilter" type="textbox" data-info="filter-studentactivity-schooldetail"></div>
		<div style="float:left;margin-left: 1%;">
		<input id="studentreport-filter-email" class="btn btn-primary" name="search" value="Search" type="button"></div><div style="float:left;margin-left: 1%;">
		<input id="studentreport-reset" class="btn btn-primary" name="reset" value="Reset" type="button" data-info="allstudent-acitivylist"></div>';
		$html.= "<div class='pull-right' >
		<select name='filter-dropdown' id='filter-dropdown' data-info='filter-studentactivity'>
		<option value='all'>Select All</option>
		<option value='profile'>Updated Profile</option>
		<option value='noprofile'>Not Updated Profile</option>
		<option value='login'>Attempted Login</option>
		<option value='nologin'>Not Attempted Login</option>
		</select>";
		$html.="</div></div>";

		$latest_value = getTotalStudentCount("and u.policyagreed=1");
		$html.="<div></br></br><table class='table bordered-table-report' style='margin-left:10%;width:75%'>
		<tr><td style='text-align:left !important'>Total Students in system : </td><td style='text-align:left !important'>".$total_students."</td><td style='text-align:left !important'>Percentage</td></tr>
		<tr><td style='text-align:left !important'>No. of Students who have attempted login : </td><td style='text-align:left !important'>".$latest_value."</td><td style='text-align:left !important'>".getPercentage($latest_value,$total_students)."</td></tr>";
		$latest_value = getTotalStudentCount("and u.profilestatus=1");
		$html.="<tr><td style='text-align:left !important'>No. of Students who have finished updating the profile : </td><td style='text-align:left !important'>".$latest_value."</td><td style='text-align:left !important'>".getPercentage($latest_value,$total_students)."</td></tr></table></div>";
		$latest_value = getTotalMentorsCount($condition);
		$html.= "<div id='table-content-wrapper' style='margin-top: 2%;'>";
		$html.= $this->generateActivityReportContent_Student();
		$html.="</div>";
		return $html;
	}
	/*
	* Function to construct a HTML container for User Meeting Report Page
	* Params : null
	* Returns : HTML string
	*/
	public function getMS_Meeting_ReportHtml()
	{
		$html='';
		$html.=$this->renderLoaderContent();
		$mode  = 'filter-meeting-school-atlid';
		$resetmode='all-meetinglist';
		$html.="<p></p>";
		$html.='<div id="school-report-filter" style="margin-bottom: 1%;margin-top: 2%;">';
		$html.="<a class='pull-right btn btn-primary' href='downloadexcel.php?tp=meetingreport'> Download in Excel Fomat </a>";
		$html.='<div style="float:left;width: 35%;"><input placeholder="Search by School ATLid" class="form-control" name="schoolatlid" id="id_schoolatlid" type="textbox"></div>
		<div style="float:left;margin-left: 1%;"><input id="schoolreport-filter-atlid" class="btn btn-primary meeting" name="search" value="Search" type="button"><input id="filter-mode" name="mode" value="'.$mode.'" type="hidden"></div><div style="float:left;margin-left: 1%;"><input id="reset-mode" name="mode" value="'.$resetmode.'" type="hidden"><input id="schoolreport-reset" class="btn btn-primary" name="reset" value="Reset" type="button"></div></div>';
		
		$html.= "<div class='pull-right' >";
		$html.="<select name='filter-dropdown-ms-meeting' id='filter-dropdown-ms-meeting'>
		<option value='all'>Select All</option>
		<option value='0'>Open</option>
		<option value='1'>Approved</option>
		<option value='2'>Rejected</option>
		<option value='3'>Meeting Completed</option>
		<option value='4'>Meeting Failed</option>
		</select>";
		/* $month_names = array('all'=>'Select All',1=>"January","February","March","April","May","June","July","August","September","October","November","December");
		$html.="<select name='filter-ms-meeting-month' id='filter-ms-meeting-month'>";
		foreach($month_names as $key=>$month)
		{
		$html.="<option value='".$key."'>".$month."</option>";
		}
		$html.="</select>"; */
		$html.="</div></div>";
		$html.="<div></br></br><table class='table bordered-table-report' style='margin-left:10%;width:75%'>
		<tr><td style='text-align:left !important'>Total no. of meetings open </td><td style='text-align:left !important'>".getTotalMeetingCount('AND e.meetingstatus = 0')."</td></tr>
		<tr><td style='text-align:left !important'>Total no. of meetings approved </td><td style='text-align:left !important'>".getTotalMeetingCount('AND e.meetingstatus = 1')."</td> </tr>
		<tr><td style='text-align:left !important'>Total no. of meetings rejected </td><td style='text-align:left !important'>".getTotalMeetingCount('AND e.meetingstatus = 2')."</td></tr>
		<tr><td style='text-align:left !important'>Total no. of meetings completed </td><td style='text-align:left !important'>".getTotalMeetingCount('AND e.meetingstatus = 3')."</td></tr>
		<tr><td style='text-align:left !important'>Total no. of meetings Failed </td><td style='text-align:left !important'>".getTotalMeetingCount('AND e.meetingstatus = 4')."</td></tr>
		</table></div>";
		$html.= "<div id='table-content-wrapper' style='margin-top: 6%;'>";
		$html.= $this->generateMeetingListContent();
		$html.="</div>";
		return $html;
	}
	/*
	* Function to construct a HTML container for Session Lists Page
	* Params : null
	* Returns : HTML string
	*/
	public function getSessionListReportHtml()
	{
		$html='';
		$html.=$this->renderLoaderContent();
		$mode  = 'filter-session-school-atlid';
		$resetmode='all-sessionlist';
		$html.="<p></p>";
		$html.='<div id="school-report-filter" style="margin-bottom: 1%;margin-top: 2%;">';
		$html.="<a class='pull-right btn btn-primary' href='downloadexcel.php?tp=sessionreport'> Download in Excel Fomat </a>";
		$html.='<div style="float:left;width: 35%;"><input placeholder="Search by School ATLid/School Name" class="form-control" name="schoolatlid" id="id_schoolatlid" type="textbox"></div>
		<div style="float:left;margin-left: 1%;"><input id="schoolreport-filter-atlid" class="btn btn-primary meeting" name="search" value="Search" type="button"><input id="filter-mode" name="mode" value="'.$mode.'" type="hidden"></div><div style="float:left;margin-left: 1%;"><input id="reset-mode" name="mode" value="'.$resetmode.'" type="hidden"><input id="schoolreport-reset" class="btn btn-primary" name="reset" value="Reset" type="button"></div></div>';
		
		$html.= "<div class='pull-right' >";
		/* $month_names = array('all'=>'Select All',1=>"January","February","March","April","May","June","July","August","September","October","November","December");
		$html.="<select name='filter-ms-session-month' id='filter-ms-session-month'>";
		foreach($month_names as $key=>$month)
		{
		$html.="<option value='".$key."'>".$month."</option>";
		}
		$html.="</select>";  */
		$html.="</div></div>";
		$html.=$this->getSessionPivotTable();
		$html.= "<div id='table-content-wrapper' style='margin-top: 2%;'>";
		$html.= $this->generateSessionListContent();
		$html.="</div>";
		return $html;
	}
	public function getSessionPivotTable()
	{
		$html.="<div></br></br><table class='table bordered-table-report' style='margin-left:10%;width:75%'>
		<tr><td style='text-align:left !important'>Total Hours of Mentoring  </td><td style='text-align:left !important'>".getSessionTiming()."</td></tr>
		<tr><td style='text-align:left !important'>Total Hours of In-Person visit - Meeting school officials</td><td style='text-align:left !important'>".getSessionTiming("and sessiontype='a'")."</td> </tr>
		<tr><td style='text-align:left !important'>Total Hours of In-Person visit - Own mentoring sessions with students</td><td style='text-align:left !important'>".getSessionTiming("and sessiontype='b'")."</td> </tr>
		<tr><td style='text-align:left !important'>Total Hours of In-Person visit - Attend School function</td><td style='text-align:left !important'>".getSessionTiming("and sessiontype='c'")."</td> </tr>
		<tr><td style='text-align:left !important'>Total Hours of Online - Video Conference session with students</td><td style='text-align:left !important'>".getSessionTiming("and sessiontype='d'")."</td></tr>
		</table></div>";
		return $html;
	}
	public function generateSessionListContent($page=1,$condition='')
	{
		$this->recordsperpage =20;
		$content='';
		$limit = $this->recordsperpage;
		$start_from = ($page-1) * $limit;  
		$sessionObj = getAllSessions($limit,$start_from,$condition);
		$total_sessions = getTotalSessionCount($condition);
		$content.="<div><div class='pull-left'><b>Total : ".$total_sessions."</b></div><br><br>";
		$content.="<table class='table-striped bordered-table-report' style='width: 100%;'>
		<th>S.No</th>
		<th>Mentor</th>
		<th>School</th>
		<th>Session Time </th>
		<th>Total Hours </th>
		<th>Session Type </th>
		<th>Total Students</th>
		<th>Session Date</th>
		<th>Session Description</th>";
		if($sessionObj)
		{
			$i=($page==1)?1:($page*$this->recordsperpage-$this->recordsperpage)+1;
			foreach($sessionObj as $session)
			{
				
				$detailpagelink_mentor= $this->CFG->wwwroot.'/search/profile.php?key='.encryptdecrypt_userid($session->mentorid,"en");
				$detailpagelink_mentor="<a href=".$detailpagelink_mentor.">".$session->mentorname."</a>";
				$detailpagelink_school = $this->CFG->wwwroot.'/atalfeatures/schooldetail.php?id='.$session->schoolid;
				$detailpagelink_school = "<a href=".$detailpagelink_school.">".$session->schoolname."</a>";
				$content.='<tr>';
				$content.="<td style='width: 2%'>".$i."</td>";
				$content.="<td  style='width: 10%'>".$detailpagelink_mentor."</td>";
				$content.="<td  style='width: 15%'>".$detailpagelink_school."</td>";
				$content.="<td  style='width: 13%'>".format_timeforReport($session->starttime).' - '.format_timeforReport($session->endtime)."</td>";
				$content.="<td  style='width: 10%'>".$session->totaltime."</td>";
				$content.="<td  style='width: 10%'>". getSessionType($session->sessiontype)."</td>";
				$content.="<td style='width: 5%'>".$session->totalstudents."</td>";
				$content.="<td  style='width: 15%'><a href='".$this->CFG->wwwroot."/mentor/sessiondetail.php?key=".encryptdecrypt_userid($session->id,"en")."'>
				".date('d-M-Y', $session->dateofsession)."</a></td>";
				$content.="<td style='width: 30%'>".substr($session->details,0,50)."</td>";
				$content.='</tr>';
				$i++;
			}
		}
		else
			$content.="<tr><td>No Records Found!</td></tr>";
		$content.='</table>';
		$content.='<div class="pagination-wrapper" align="center">';
		($total_sessions>1)?$total_pages =ceil(($total_sessions)/$this->recordsperpage):$total_pages =1;	
		$content.= $this->paginate_function($this->recordsperpage,$page,$total_sessions,$total_pages,'session-report');
		$content.='</div>';	
		return $content;
	}
	/*
	* Function to generate dynamic table content for User Meeting Report Page
	* Params : null
	* Returns : HTML string
	*/
	public function generateMeetingListContent($page=1,$condition='')
	{
		$content='';
		$limit = $this->recordsperpage;
		$start_from = ($page-1) * $limit;  
		$meetingObj = getAllMeetings($limit,$start_from,$condition);
		$total_meetings = getTotalMeetingCount($condition);
		$content.="<div><div class='pull-left'><b>Total : ".$total_meetings."</b></div><br><br>";
		$content.="<table class='table-striped bordered-table-report' style='width: 100%;'>
		<th>S.No</th>
		<th>Title</th>
		<th>Description</th>
		<th>Between </th>
		<th>Date </th>
		<th>Status</th>";
		if($meetingObj)
		{
			$i=($page==1)?1:($page*$this->recordsperpage-$this->recordsperpage)+1;
			foreach($meetingObj as $meeting)
			{
				
				$detailpagelink_intiated = $this->CFG->wwwroot.'/search/profile.php?key='.encryptdecrypt_userid($meeting->userid,"en");
				$detailpagelink_intiated="<a href=".$detailpagelink_intiated.">".$meeting->initiated."</a>";
				$detailpagelink_assigned = $this->CFG->wwwroot.'/search/profile.php?key='.encryptdecrypt_userid($meeting->parentid,"en");
				$detailpagelink_assigned = "<a href=".$detailpagelink_assigned.">".$meeting->assignee."</a>";
				$content.='<tr>';
				$content.="<td style='width: 2%'>".$i."</td>";
				$content.="<td>".$meeting->name."</td>";
				$content.="<td  style='width: 40%'>".$meeting->description."</td>";
				$content.="<td>".$detailpagelink_intiated.' AND '.$detailpagelink_assigned ."</td>";
				$content.="<td style='width: 8%'>".date('d-M-Y', $meeting->timestart)."</td>";
				$content.="<td >".getUserMeetingStatus($meeting->meetingstatus)."</td>";
				$content.='</tr>';
				$i++;
			}
		}
		else
			$content.="<tr><td>No Records Found!</td></tr>";
		$content.='</table>';
		$content.='<div class="pagination-wrapper" align="center">';
		($total_meetings>1)?$total_pages =ceil(($total_meetings)/$this->recordsperpage):$total_pages =1;	
		$content.= $this->paginate_function($this->recordsperpage,$page,$total_meetings,$total_pages,'meeting-report');
		$content.='</div>';	
		return $content;
	}
	/*
	* Function to construct a HTML container for Student List Report Page
	* Params : null
	* Returns : HTML string
	*/
	public function getStudentListReportHtml()
	{
		$html='';
		$html.=$this->renderLoaderContent();
		$html.='<div id="student-report-filter" style="margin-bottom: 1%;margin-top: 2%;">
		<div style="float:left;width: 35%;">
		<input placeholder="Search by Student Email" class="form-control" name="studentemail" id="id_studentemail" type="textbox" data-info="filter-student-email"></div><div style="float:left;width: 35%;margin-left:5px;">
		<input placeholder="Search by School Name/ATLid" class="form-control" name="schoolfilter" id="id_schoolfilter" type="textbox" data-info="filter-student-schooldetail"></div>
		<div style="float:left;margin-left: 1%;" >
		<input id="studentreport-filter-email" class="btn btn-primary" name="search" value="Search" type="button"></div><div style="float:left;margin-left: 1%;">
		<input id="studentreport-reset" class="btn btn-primary" name="reset" value="Reset" type="button" data-info="allstudent-list"></div>';
		$html.= "<div class='pull-right'>";
		$html.="</div></div>";
		$html.= "<div id='table-content-wrapper' style='margin-top: 6%;'>";
		$html.= $this->generateListContent_Student();
		$html.="</div>";
		return $html;
	}
	/*
	* Function to generate dynamic table content for Student List Report Page
	* Params : null
	* Returns : HTML string
	*/
	public function generateListContent_Student($page=1,$condition='')
	{
		$limit=$this->recordsperpage;
		if(isset($_REQUEST['atl']))
			$condition=("AND s.atl_id LIKE '%".trim($_REQUEST['atl'])."%'");
		$total_students = getTotalStudentCount($condition);
		$content.="<div><div class='pull-left'><b>Total : ".$total_students."</b></div><br><br>";
		$start_from = ($page-1) * $limit;  
		$studentlist = allstudent_list($this->recordsperpage,$start_from,$condition);
		$content.="<table class='table-striped bordered-table-report' style='width: 100%;'>
				<th>S.No</th>
				<th>Student Name</th>
				<th> Email id</th>
				<th> School Name</th>
				<th> Town/District</th>
				<th>Contact</th>";
		if($studentlist)
		{
			$i=($page==1)?1:($page*$this->recordsperpage-$this->recordsperpage)+1;
			foreach($studentlist as $student)
			{
				$content.='<tr>';
				$content.="<td>".$i."</td>";
				$content.="<td>".$student->firstname."</td>";
				$content.="<td>".$student->email."</td>";
				$content.="<td>".$student->name."</td>";
				$content.="<td>".$student->city."</td>";
				$content.="<td>".(isset($school->phone)?$school->phone:'NA')."</td>";
				$content.='</tr>';
				$i++;
			}
		}
		else
			$content.="<tr><td>No Records Found!</td></tr>";
		$content.='</table>';
		$content.='<div class="pagination-wrapper" align="center">';
		($total_students>1)?$total_pages =ceil(($total_students)/$this->recordsperpage):$total_pages =1;	
		$content.= $this->paginate_function($this->recordsperpage,$page,$total_students,$total_pages,'student-report');
		$content.='</div>';	
		return $content;
	}
	public function generateActivityReportContent_Student($page=1,$condition='')
	{
		$limit=$this->recordsperpage = 20;
		if(isset($_REQUEST['atl']))
			$condition=("AND s.atl_id LIKE '%".trim($_REQUEST['atl'])."%'");
		$total_students = getTotalStudentCount($condition);
		$content.="<div><div class='pull-left'><b>Total : ".$total_students."</b></div><br><br>";
		$start_from = ($page-1) * $limit;  
		$studentlist = allstudent_list($this->recordsperpage,$start_from,$condition);
		$content.="<table class='table-striped bordered-table-report' style='width: 100%;'>
		<th>S.No</th>
		<th>Student Name</th>
		<th>Student Email id</th>
		<th>Town/District</th>
		<th>Student Contact</th>
		<th>School Name</th>
		<th>School ATL id</th>
		<th>School EmailId</th>
		<th>School Phone</th>
		<th>Has the Student attempted a login</th>
		<th>Has the Student finished updating the profile?</th>";
		if($studentlist)
		{
			$i=($page==1)?1:($page*$this->recordsperpage-$this->recordsperpage)+1;
			foreach($studentlist as $student)
			{
				$content.='<tr>';
				$content.="<td>".$i."</td>";
				$content.="<td>".$student->firstname." ".$student->lastname."</td>";
				$content.="<td>".$student->email."</td>";
				$content.="<td>".$student->city."</td>";
				$content.="<td>".$student->phone1."</td>";
				$content.="<td>".$student->name."</td>";
				$content.="<td>".$student->atl_id."</td>";
				$content.="<td>".$student->school_emailid."</td>";
				$content.="<td>".(isset($school->phone)?$school->phone:'NA')."</td>";
				$content.="<td>".(($student->policyagreed)?'Y':'N')."</td>";
				$content.="<td>".(($student->profilestatus)?'Y':'N')."</td>";
				$content.='</tr>';
				$i++;
			}
		}
		else
			$content.="<tr><td>No Records Found!</td></tr>";
		$content.='</table>';
		$content.='<div class="pagination-wrapper" align="center">';
		($total_students>1)?$total_pages =ceil(($total_students)/$this->recordsperpage):$total_pages =1;	
		$content.= $this->paginate_function($this->recordsperpage,$page,$total_students,$total_pages,'student-acti-report');
		$content.='</div>';	
		return $content;
	}
	/*
	* Function to generate dynamic table content for School List Report Page
	* Params : null
	* Returns : HTML string
	*/
	public function generateListContent_School($page=1,$condition='')
	{
		$limit=$this->recordsperpage;
		$total_schools = getTotalSchoolCount($condition);
		$content.="<div><div class='pull-left'><b>Total : ".$total_schools."</b></div><br><br>";
		$start_from = ($page-1) * $limit;  
		$schoolist = school_list($this->recordsperpage,$start_from,$condition);
		$content.="<table class='table-striped bordered-table-report' style='width: 100%;'>
				<th>S.No</th>
				<th>School Name</th>
				<th> ATL id</th>
				<th> Address</th>
				<th> Town/District</th>
				<th>State</th>
				<th>School EmailId</th>
				<th>Contact</th>";
		if($schoolist)
		{
			$i=($page==1)?1:($page*$this->recordsperpage-$this->recordsperpage)+1;
			foreach($schoolist as $school)
			{
				$detailpagelink = $this->CFG->wwwroot.'/nitiadmin/studentlist.php?atl='.$school->atl_id;
				$school->name = "<a href=".$detailpagelink.">".$school->name."</a>";
				$cityname = isset($school->cityid)?get_atal_citybycityid($school->cityid):'NA';
				if(count($cityname)>0)
				{
					$statename = isset($school->cityid)?get_atal_statebystateid($cityname[$school->cityid]->stateid):'NA';
					$cityname=$cityname[$school->cityid]->name;
					if(count($statename))
						$statename=$statename->name;
				}
				$content.='<tr>';
				$content.="<td>".$i."</td>";
				$content.="<td>".$school->name."</td>";
				$content.="<td>".$school->atl_id."</td>";
				$content.="<td>".$school->address."</td>";
				$content.="<td>".$cityname."</td>";
				$content.="<td>".$statename."</td>";
				$content.="<td>".$school->school_emailid."</td>";
				$content.="<td>".$school->phone."</td>";
				$content.='</tr>';
				$i++;
			}
		}
		else
			$content.="<tr><td>No Records Found!</td></tr>";
		$content.='</table>';
		$content.='<div class="pagination-wrapper" align="center">';
		($total_schools>1)?$total_pages =ceil(($total_schools)/$this->recordsperpage):$total_pages =1;	
		$content.= $this->paginate_function($this->recordsperpage,$page,$total_schools,$total_pages,'school-report');
		$content.='</div>';	
		return $content;
	}
	/*
	* Function to generate dynamic table content for School Activity Report Page
	* Params : null
	* Returns : HTML string
	*/
	public function generateActivityReportContent_School($page=1,$condition='')
	{
		$limit=$this->recordsperpage;
		$total_schools = getTotalSchoolCount($condition);
		$content.="<div><div class='pull-left'><b>Total : ".$total_schools."</b></div><br><br>";
		$start_from = ($page-1) * $limit;  
		$schoolist = school_list_activityreport($this->recordsperpage,$start_from,$condition);		
		$content.="<table class='table-striped bordered-table-report' style='width: 100%;'>
				<th>S.No</th>
				<th>School Name</th>
				<th> ATL id</th>
				<th> Town/District</th>
				<th>State</th>
				<th>School EmailId</th>
				<th>Has the School attempted a login</th>
				<th>Has the School finished updating the profile?</th>
				<th>Total Meetings </th>
				<!--<th>Open Meeting</th>-->
				<th>Approved</th>
				<th>Rejected</th>";
		if($schoolist)
		{
			$i=($page==1)?1:($page*$this->recordsperpage-$this->recordsperpage)+1;
			foreach($schoolist as $school)
			{
				$detailpagelink = $this->CFG->wwwroot.'/nitiadmin/studentlist.php?atl='.$school->atl_id;
				$school->name = "<a href=".$detailpagelink.">".$school->name."</a>";
				$cityname = isset($school->cityid)?get_atal_citybycityid($school->cityid):'NA';
				if(count($cityname)>0)
				{
					$statename = isset($school->cityid)?get_atal_statebystateid($cityname[$school->cityid]->stateid):'NA';
					$cityname=$cityname[$school->cityid]->name;
					if(count($statename))
						$statename=$statename->name;
				}
				$content.='<tr>';
				$content.="<td>".$i."</td>";
				$content.="<td>".$school->name."</td>";
				$content.="<td>".$school->atl_id."</td>";
				$content.="<td>".$cityname."</td>";
				$content.="<td>".$statename."</td>";
				$content.="<td>".$school->school_emailid."</td>";
				$content.="<td>".(($school->policyagreed)?'Y':'N')."</td>";
				$content.="<td>".(($school->profilestatus)?'Y':'N')."</td>";
				$content.="<td>".($school->meetingcount)."</td>";
				//$content.="<td>".($school->open)."</td>";
				$content.="<td>".($school->approved)."</td>";
				$content.="<td>".($school->rejected)."</td>";
				$content.='</tr>';
				$i++;
			}
		}
		else
			$content.="<tr><td>No Records Found!</td></tr>";
		$content.='</table>';
		$content.='<div class="pagination-wrapper" align="center">';
		($total_schools>1)?$total_pages =ceil(($total_schools)/$this->recordsperpage):$total_pages =1;	
		$content.= $this->paginate_function($this->recordsperpage,$page,$total_schools,$total_pages,'school-report');
		$content.='</div>';	
		return $content;
	}
	/*
	* Function to generate dynamic table content for Mentor List Report Page
	* Params : null
	* Returns : HTML string
	*/
	public function generateListContent_Mentor($page=1,$condition='')
	{
		$limit=$this->recordsperpage;
		$total_mentors = getTotalMentorsCount($condition);
		$content.="<div><div class='pull-left'><b>Total : ".$total_mentors."</b></div><br><br>";
		$start_from = ($page-1) * $limit;  
		$mentorlist = getMentorReportList($this->recordsperpage,$start_from,$condition);
		$content.="<table class='table-striped bordered-table-report' style='width: 100%;'>
				<th>S.No</th>
				<th>FirstName</th>
				<th>LastName</th>
				<th>Email</th>
				<th>Gender</th>
				<th>DOB</th>";
		if($mentorlist)
		{
			$i=($page==1)?1:($page*$this->recordsperpage-$this->recordsperpage)+1;
			foreach($mentorlist as $mentor)
			{
				$detailpagelink = $this->CFG->wwwroot.'/search/profile.php?key='.encryptdecrypt_userid($mentor->id,"en");
				$mentor->firstname = "<a href=".$detailpagelink.">".$mentor->firstname."</a>";
				$content.='<tr>';
				$content.="<td>".$i."</td>";
				$content.="<td>".$mentor->firstname."</td>";
				$content.="<td>".$mentor->lastname."</td>";
				$content.="<td>".$mentor->email."</td>";
				$content.="<td>".(($mentor->gender=='m')?'Male':(($mentor->gender=='f')?'Female':(($mentor->gender=='t')?'TransGender':'')))."</td>";
				$content.="<td>".$mentor->yahoo."</td>";
				$content.='</tr>';
				$i++;
			}
		}
		else
			$content.="<tr><td>No Records Found!</td></tr>";
		$content.='</table>';
		$content.='<div class="pagination-wrapper" align="center">';
		($total_mentors>1)?$total_pages =ceil(($total_mentors)/$this->recordsperpage):$total_pages =1;	
		$content.= $this->paginate_function($this->recordsperpage,$page,$total_mentors,$total_pages,'mentor-report');
		$content.='</div>';	
		return $content;
	}
	/*
	* Function to generate dynamic table content for Mentor Activity Report Page
	* Params : null
	* Returns : HTML string
	*/
	public function generateContent($page=1,$condition='')
	{
		$limit=$this->recordsperpage;
		$total_mentors = getTotalMentorsCount($condition);
		$total_mentors_ibm = getTotalMentorsCount("and u.email like '%in.ibm.%'");
		$content.="<div><div class='pull-left'><b>Total : ".$total_mentors."</b>&nbsp;&nbsp;&nbsp;&nbsp;<b>Total IBM Mentors: ".$total_mentors_ibm."</b></div>";
		$start_from = ($page-1) * $limit;  
		$mentorlist = getMentorReportList($this->recordsperpage,$start_from,$condition);		
		
		$content.="<table class='table-striped bordered-table-report'>
			<th>S.No</th>
			<th>FirstName</th>
			<th>LastName</th>
			<th>Email</th>
			<th>Gender</th>
			<th>DOB</th>
			<th>Schools</th>
			<th>Has the mentor attempted a login</th>
			<th>Has the mentor finished updating the profile?</th>
			<th>Has the mentor started the mentor training module?</th>
			<th>Has the mentor finished the mentor training module?</th>
			<th>No. of sessions invitation sent</th>
			<th>No. of sessions accepted</th>
			<th>No. of sessions rejected</th>
			<th>No. of sessions taken</th>";						
		
		if($mentorlist)
		{
			$i=($page==1)?1:($page*$this->recordsperpage-$this->recordsperpage)+1;
			foreach($mentorlist as $mentor)
			{
				$schoolresult = getSchoolnamesofMentor($mentor->id);
				$schoolname = ($schoolresult)?$schoolresult->schoolname:'NA';
				$detailpagelink = $this->CFG->wwwroot.'/search/profile.php?key='.encryptdecrypt_userid($mentor->id,"en");
				$mentor->firstname = "<a href=".$detailpagelink.">".$mentor->firstname."</a>";
				$traningmodule_started = checkTrainingMentorExist($mentor->id);
				$traningmodule_started = ($traningmodule_started)?'Y':'N';
				$content.='<tr>';
				$content.="<td>".$i."</td>";
				$content.="<td>".$mentor->firstname."</td>";
				$content.="<td>".$mentor->lastname."</td>";
				$content.="<td>".$mentor->email."</td>";
				$content.="<td>".(($mentor->gender=='m')?'Male':(($mentor->gender=='f')?'Female':(($mentor->gender=='t')?'TransGender':'')))."</td>";
				$content.="<td>".$mentor->yahoo."</td>";
				$content.="<td>".$schoolname."</td>";
				$content.="<td>".(($mentor->policyagreed)?'Y':'N')."</td>";
				$content.="<td>".(($mentor->profilestatus)?'Y':'N')."</td>";
				$content.="<td>".$traningmodule_started."</td>";
				$content.="<td>".(($mentor->scormstatus)?'Y':'N')."</td>";
				$content.="<td>".($mentor->meetingcount)."</td>";
				$content.="<td>".($mentor->approved)."</td>";
				$content.="<td>".($mentor->rejected)."</td>";
				$content.="<td>".($mentor->completed)."</td>";
				$content.='</tr>';
				$i++;
			}
		}
		else
			$content.="<tr><td>No Records Found!</td></tr>";
		$content.='</table>';
		$content.='<div class="pagination-wrapper" align="center">';
		($total_mentors>1)?$total_pages =ceil(($total_mentors)/$this->recordsperpage):$total_pages =1;	
		$content.= $this->paginate_function($this->recordsperpage,$page,$total_mentors,$total_pages,'mentor-report');
		$content.='</div>';	
		return $content;
	}
	/*
	* Function to Construct object to download Mentor Activity data in Excel Sheet
	* Param : NULL
	* Returns : Object 
	*/
	public function getContentForExcel()
	{
		$mentorlist = getMentorReportListforExcel();
		$reportarr = array();
		$reportObj = new StdClass();
		if($mentorlist)
		{
			$i=0;
			foreach($mentorlist as $mentor)
			{
				$schoolresult = getSchoolnamesofMentor($mentor->id);
				$schoolname = ($schoolresult)?$schoolresult->schoolname:'NA';
				$traningmodule_started = checkTrainingMentorExist($mentor->id);
				$traningmodule_started = ($traningmodule_started)?'Y':'N';		
				$reportarr[$i]['email'] =  $mentor->email;
				$reportarr[$i]['sno'] =  $i+1;
				$reportarr[$i]['firstname'] =  $mentor->firstname;
				$reportarr[$i]['lastname'] =  $mentor->lastname;
				$reportarr[$i]['gender'] =(($mentor->gender=='m')?'Male':(($mentor->gender=='f')?'Female':'Trans Gender'));
				$reportarr[$i]['dob'] =  $mentor->yahoo;
				$reportarr[$i]['schoolname'] = $schoolname;
				$reportarr[$i]['attemptedlogin'] = (($mentor->policyagreed)?'Y':'N');
				$reportarr[$i]['updatedprofile'] =  (($mentor->profilestatus)?'Y':'N');
				$reportarr[$i]['traningmodule_started'] =$traningmodule_started;		
				$reportarr[$i]['completedtutorial'] =(($mentor->scormstatus)?'Y':'N');	
				$reportarr[$i]['meetingcount'] =$mentor->meetingcount;
				$reportarr[$i]['approved'] =$mentor->approved;
				$reportarr[$i]['rejected'] =$mentor->rejected;
				$reportarr[$i]['completed'] =$mentor->completed;
				$i++;
			}
		}
		$reportObj = (object)$reportarr;
		return $reportObj;
	}
	/*
	* Function to Construct object to download IBM Mentor Activity data in Excel Sheet
	* Param : NULL
	* Returns : Object 
	*/
	public function getContentForExcel_Ibmmentor()
	{
		$mentorlist = getIbmMentorReportListforExcel();
		$reportarr = array();
		$reportObj = new StdClass();
		if($mentorlist)
		{
			$i=0;
			foreach($mentorlist as $mentor)
			{
				//$schoolresult = getSchoolnamesofMentor($mentor->id);
				//$schoolname = ($schoolresult)?$schoolresult->schoolname:'NA';
				$traningmodule_started = checkTrainingMentorExist($mentor->id);
				$traningmodule_started = ($traningmodule_started)?'Y':'N';		
				$reportarr[$i]['email'] =  $mentor->email;
				$reportarr[$i]['sno'] =  $i+1;
				$reportarr[$i]['firstname'] =  $mentor->firstname;
				$reportarr[$i]['lastname'] =  $mentor->lastname;
				$reportarr[$i]['city'] = $mentor->city;
				$reportarr[$i]['phone1'] = $mentor->phone1;
				$reportarr[$i]['timecreated'] = date('m/d/Y', $mentor->timecreated);
				$reportarr[$i]['attemptedlogin'] = (($mentor->policyagreed)?'Y':'N');
				$reportarr[$i]['updatedprofile'] =  (($mentor->profilestatus)?'Y':'N');
				$reportarr[$i]['traningmodule_started'] =$traningmodule_started;		
				$reportarr[$i]['completedtutorial'] =(($mentor->scormstatus)?'Y':'N');	
				$reportarr[$i]['meetingcount'] =$mentor->meetingcount;
				$reportarr[$i]['approved'] =$mentor->approved;
				$reportarr[$i]['rejected'] =$mentor->rejected;
				$reportarr[$i]['completed'] =$mentor->completed;
				$i++;
			}
		}
		$reportObj = (object)$reportarr;
		return $reportObj;
	}
	/*
	* Function to Construct object to download School Activity data in Excel Sheet
	* Param : NULL
	* Returns : Object 
	*/
	public function getSchoolActivtyReportExcel()
	{
		$schoollist = school_activityreport_excel();
		$reportarr = array();
		$reportObj = new StdClass();
		if($schoollist)
		{
			$i=0;
			foreach($schoollist as $school)
			{
				$cityname = isset($school->cityid)?get_atal_citybycityid($school->cityid):'NA';
				if(count($cityname)>0)
				{
					$statename = isset($school->cityid)?get_atal_statebystateid($cityname[$school->cityid]->stateid):'NA';
					$cityname=$cityname[$school->cityid]->name;
					if(count($statename))
						$statename=$statename->name;
				}
				$reportarr[$i]['schoolname'] =  $school->name;
				$reportarr[$i]['sno'] =  $i+1;
				$reportarr[$i]['atlid'] =  $school->atl_id;
				$reportarr[$i]['district'] =  $cityname;
				$reportarr[$i]['state'] =  $statename;
				$reportarr[$i]['schoolemail'] = $school->school_emailid;
				$reportarr[$i]['attemptedlogin'] = (($school->policyagreed)?'Y':'N');
				$reportarr[$i]['updatedprofile'] =  (($school->profilestatus)?'Y':'N');
				$reportarr[$i]['meetingcount'] =$school->meetingcount;
				$reportarr[$i]['approved'] =$school->approved;
				$reportarr[$i]['rejected'] =$school->rejected;
				$i++;
			}
		}
		$reportObj = (object)$reportarr;
		return $reportObj;
	}
	/*
	* Function to Construct object to download Student Activity data in Excel Sheet
	* Param : NULL
	* Returns : Object 
	*/
	public function getStudentActivtyReportExcel()
	{
		$total_student = getTotalStudentCount();
		$studentlist = allstudent_list($total_student);
		$reportarr = array();
		$reportObj = new StdClass();
		if($studentlist)
		{
			$i=0;
			foreach($studentlist as $student)
			{

				$reportarr[$i]['studentemail'] =  $student->email;
				$reportarr[$i]['sno'] =  $i+1;
				$reportarr[$i]['student_name'] =  $student->firstname." ".$student->lastname;
				$reportarr[$i]['district'] =  $student->city;
				$reportarr[$i]['student_contact'] = $student->phone1;
				$reportarr[$i]['school_name'] = $student->name;
				$reportarr[$i]['school_atlid'] = $student->atl_id;
				$reportarr[$i]['school_emailid'] = $student->school_emailid;
				$reportarr[$i]['school_phone'] = (isset($student->phone)?$student->phone:'NA');
				$reportarr[$i]['attemptedlogin'] = (($student->policyagreed)?'Y':'N');
				$reportarr[$i]['updatedprofile'] =  (($student->profilestatus)?'Y':'N');
				$i++;
			}
		}
		$reportObj = (object)$reportarr;
		return $reportObj;
	}
	/*
	* Function to Construct object to download School Activity data in Excel Sheet
	* Param : NULL
	* Return : Object
	*/
	public function getMeetingReportExcel()
	{
		$meetinglist = msmeeting_report_excel();
		$reportarr = array();
		$reportObj = new StdClass();
		if($meetinglist)
		{
			$i=0;
			foreach($meetinglist as $meeting)
			{
				$reportarr[$i]['title'] =  $meeting->name;
				$reportarr[$i]['sno'] =  $i+1;
				$reportarr[$i]['intiated'] =  $meeting->initiated;
				$reportarr[$i]['assignee'] =  $meeting->assignee;
				$reportarr[$i]['description'] = strip_tags($meeting->description);
				$reportarr[$i]['createdon'] =  date("d-m-Y",$meeting->timestart);
				$reportarr[$i]['status'] =  getUserMeetingStatus($meeting->meetingstatus);
				$i++;
			}
		}
		$reportObj = (object)$reportarr;
		return $reportObj;
	}
	/*
	* Function to Construct object to download Mentor and School Session Activity data in Excel Sheet
	* Param : NULL
	* Return : Object
	*/
	public function getSessionReportExcel()
	{
		$sessionlist = getAllSessions();
		$reportarr = array();
		$reportObj = new StdClass();
		if($sessionlist)
		{
			$i=0;
			foreach($sessionlist as $session)
			{
				$reportarr[$i]['mentorname'] =  $session->mentorname;
				$reportarr[$i]['sno'] =  $i+1;
				$reportarr[$i]['mentoremail'] =  $session->mentoremail;
				$reportarr[$i]['schoolname'] =  $session->schoolname;
				$reportarr[$i]['schoolatlid'] =  $session->schoolatlid;
				$reportarr[$i]['dateofsession'] =  date("d-m-Y",$session->dateofsession);
				$reportarr[$i]['starttime'] =  $session->starttime;
				$reportarr[$i]['endtime'] =  $session->endtime;
				$reportarr[$i]['sessiontype'] =  getSessionType($session->sessiontype);
				$reportarr[$i]['totalstudents'] =  $session->totalstudents;
				$reportarr[$i]['details'] = strip_tags($session->details);
				$i++;
			}
		}
		$reportObj = (object)$reportarr;
		return $reportObj;
	}
	/*
	* Function to display Mentor Activity Report Page based on Filters from dropdown
	* This fucntion is also called by ajax
	* Params : null
	* Returns : HTML string
	*/
	public function generateContentwithfilter($page,$filterby,$emailval='')
	{
		$condition ='';
		$content='';
		switch($filterby)
		{
			case 'all':
				$content = $this->generateContent($page);
				break;
			case 'completed':
				$conditionresult = getTutorialCompletedMentor();
				if($conditionresult)
					$condition = ' and u.id in ('.$conditionresult.')';
				$content = $this->generateContent($page,$condition);
				break;
			case 'notstarted':
				$conditionresult = getTutorialNotStartedMentor();
				if($conditionresult)
					$condition = ' and u.id not in ('.$conditionresult.')';
				$content = $this->generateContent($page,$condition);
				break;
			case 'profile':
				$condition = ' and u.profilestatus=1';
				$content = $this->generateContent($page,$condition);
				break;
			case 'noprofile':
				$condition = ' and (u.profilestatus IS NULL) ';
				$content = $this->generateContent($page,$condition);
				break;
			case 'login':
				$condition = ' and u.policyagreed=1';
				$content = $this->generateContent($page,$condition);
				break;
			case 'nologin':
				$condition = ' and u.policyagreed=0';
				$content = $this->generateContent($page,$condition);
				break;	
			case 'email':
				$condition = ' and u.email="'.$emailval.'"';
				$content = $this->generateContent($page,$condition);
				break;	
			case 'mentorlist':
				$content = $this->generateListContent_Mentor($page);
				break;
			case 'listfilteremail':
				$condition = ' and u.email="'.$emailval.'"';
				$content = $this->generateListContent_Mentor($page,$condition);
				break;	
				
		}
		return $content;
	}
	/*
	* Function to display School List Report Page based on Filters
	* This fucntion is also called by ajax
	* Params : null
	* Returns : HTML string
	*/
	public function generateSchoolContentwithfilter($page,$filterby,$val='')
	{
		$condition ='';
		$content='';
		switch($filterby)
		{
			case 'atlid':
				$condition = ' and ms.atl_id="'.$val.'"';
				$content = $this->generateListContent_School($page,$condition);
				break;	
			case 'all':
				$content = $this->generateListContent_School($page,$condition);
				break;
		}
		return $content;
	}
	/*
	* Function to display Student List Report Page based on Filters from dropdown
	* This fucntion is also called by ajax
	* Params : null
	* Returns : HTML string
	*/
	public function generateStudentContentwithfilter($page,$filterby,$val='')
	{
		$condition ='';
		$content='';
		switch($filterby)
		{
			case 'all':
				$content = $this->generateListContent_Student($page,$condition);
				break;
			case 'schoolfilter':
				$condition=" AND (s.name LIKE '%".$val."%' OR s.atl_id LIKE '%".$val."%')";
				$content = $this->generateListContent_Student($page,$condition);
				break;
			case 'email':
				$condition = ' and u.email="'.$val.'"';
				$content = $this->generateListContent_Student($page,$condition);
				break;	
		}
		return $content;
	}
	/*
	* Function to display Student List Report Page based on Filters from dropdown
	* This fucntion is also called by ajax
	* Params : null
	* Returns : HTML string
	*/
	public function generateStudentActivityContentwithfilter($page,$filterby,$val='')
	{
		$condition ='';
		$content='';
		switch($filterby)
		{
			case 'all':
				$content = $this->generateActivityReportContent_Student($page,$condition);
				break;
			case 'schoolfilter':
				$condition=" AND (s.name LIKE '%".$val."%' OR s.atl_id LIKE '%".$val."%')";
				$content = $this->generateActivityReportContent_Student($page,$condition);
				echo $content;die;
				break;
			case 'email':
				$condition = ' and u.email="'.$val.'"';
				$content = $this->generateActivityReportContent_Student($page,$condition);
				break;	
			case 'profile':
				$condition = ' and u.profilestatus=1';
				$content = $this->generateActivityReportContent_Student($page,$condition);
				break;
			case 'noprofile':
				$condition = ' and (u.profilestatus IS NULL) ';
				$content = $this->generateActivityReportContent_Student($page,$condition);
				break;
			case 'login':
				$condition = ' and u.policyagreed=1';
				$content = $this->generateActivityReportContent_Student($page,$condition);
				break;
			case 'nologin':
				$condition = ' and u.policyagreed=0';
				$content = $this->generateActivityReportContent_Student($page,$condition);
				break;	
		}
		return $content;
	}
	/*
	* Function to display User Meeting Report Page based on Filters from dropdown
	* This fucntion is also called by ajax
	* Params : null
	* Returns : HTML string
	*/
	public function generateMeetingListContentwithfilter($page,$filterby,$val='')
	{
		$condition ='';
		$content='';
		switch($filterby)
		{
			case 'all':
				$content = $this->generateMeetingListContent($page,$condition);
				break;
			case 'schoolfilter':
				$useridObj = getSchoolIncharge($val);
				$userid=0;
				if($useridObj)
					$userid = implode(',',array_keys($useridObj));
				$condition=" AND e.userid in (".$userid.")";
				$content = $this->generateMeetingListContent($page,$condition);
				break;
			case 'status':
				$val = json_decode($val);
				$status = $val->val ; 
				$userid=0;
				$condition = ($status=='all')?$condition:$condition=" AND e.meetingstatus = $status";
				if(isset($val->name) || trim($val->name)!='')
				{
					$useridObj = getSchoolIncharge($val->name);
					if($useridObj)
						$userid = implode(',',array_keys($useridObj));
						$condition.=" AND e.userid in (".$userid.")";
				}
				$content = $this->generateMeetingListContent($page,$condition);
				break;
			case 'month':
				$val = json_decode($val);
				$month = $val->val ;
				$condition = ($month=='all')?$condition:$condition="and month(FROM_UNIXTIME(e.timestart))=".$month;
				//$condition = "and month(FROM_UNIXTIME(e.timestart))=".$month;
				$content = $this->generateMeetingListContent($page,$condition);
				break;
			case 'meetinglist':
				$content = $this->generateMeetingListContent($page);
				break;
		}
		return $content;
	}
	/*
	* Function to display School Activity Report Page based on Filters from dropdown
	* This fucntion is also called by ajax
	* Params : null
	* Returns : HTML string
	*/
	public function generateSchoolActivityContentwithfilter($page,$filterby,$val='')
	{
		$condition ='';
		$content='';
		switch($filterby)
		{
			case 'all':
				$content = $this->generateActivityReportContent_School($page,$condition);
				break;
			case 'atlid':
				$condition = ' and ms.atl_id="'.$val.'"';
				$content = $this->generateActivityReportContent_School($page,$condition);
				break;
			case 'profile':
				$condition = ' and mu.profilestatus=1';
				$content = $this->generateActivityReportContent_School($page,$condition);
				break;
			case 'noprofile':
				$condition = ' and (mu.profilestatus IS NULL) ';
				$content = $this->generateActivityReportContent_School($page,$condition);
				break;
			case 'login':
				$condition = ' and mu.policyagreed=1';
				$content = $this->generateActivityReportContent_School($page,$condition);
				break;
			case 'nologin':
				$condition = ' and mu.policyagreed=0';
				$content = $this->generateActivityReportContent_School($page,$condition);
				break;	
		}
		return $content;
	}
	/*
	* Function to display Mentor & Session Report Page based on Filters from dropdown
	* This fucntion is also called by ajax
	* Params : null
	* Returns : HTML string
	*/
	public function generateMSessionContentwithfilter($page,$filterby,$val='')
	{
		$condition ='';
		$content='';
		switch($filterby)
		{
			case 'all':
				$content = $this->generateSessionListContent($page,$condition);
				break;
			case 'atlid':
				$condition=" AND (ms.name LIKE '%".$val."%' OR ms.atl_id LIKE '%".$val."%')";
				//$condition = ' and ms.atl_id="'.$val.'"';
				$content = $this->generateSessionListContent($page,$condition);
				break;
		}
		return $content;
	}
}
?>