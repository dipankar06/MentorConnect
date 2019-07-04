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

/* @package: core_mentor
 * @CreatedBy: Jothi (IBM)
 * @CreatedOn: 21-11-2018
 * @Description: render
*/


require_once('../external/commonrender.php');

/*
 * Class to constrct HTML structure for NitiAdmin Administration Pages
*/

class MentorRender extends CommonRender {
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
	public function getMentorSessionsHtml($id='')
	{
		$html='';
		$condition='';
		if($id)
			$condition='and mu.id='.$id;
		$html.=$this->renderLoaderContent();
		$total_sessions = mentor_sessioncount($condition);
		$html.="<div class='card card-block'><div><div> <h2>My Sessions</h2> </div> <div class='pull-right'> <a class='btn btn-primary' href='mentorsession.php'> Report Session </a></div></div>";
		$total_session = mentor_sessioncount($condition);	
		$month = date("m");
		$mcondition = "and month(FROM_UNIXTIME(msr.dateofsession))=".$month;
		if($id)
			$mcondition = "and mu.id=$id and month(FROM_UNIXTIME(msr.dateofsession))=".$month;
		$total_session_month = mentor_sessioncount($mcondition);
		$html.="<div style='margin-top:3%;'><div style='float:left;'> <h5 style='color:#585555;'>$total_session Sessions Till Date  </h5></div><div style='float:left;margin-left:30%'> <h5 style='color:#585555;'>$total_session_month Sessions This Month </h5></div></div>";
		$html.=$this->getSessionListContent($id);
		$html.="</div>";
		return $html;
	}
	public function getSessionListContent($id)
	{
		$content='';
		$condition='';
		if($id)
			$condition='and mu.id='.$id;
		$condition.=' ORDER By id DESC';
		$sessionlist = get_allmysession(0,0,$condition);
		$newSessionlist = array ();
		foreach($sessionlist as $session)
		{
			$newObj = new StdClass();
			$month = explode("-",$session->dateofsession_date);
			$newSessionlist[$month[2]][$month[1]][] = $session;
		}
		$content.="<div style='margin-top:7%;' class='table-container'>";
		if(count($newSessionlist)>0){
			foreach($newSessionlist as $sessionlist)
			{
				foreach($sessionlist as $session){
					$content.="<p style='color:red'>".date('F Y ', strtotime($session[0]->dateofsession_date))."</p>";
					$content.="<div class='card'> <table class='table'>";
					foreach($session as $list){
						$date = date('d F ',$list->dateofsession);
						$content.="<tr>";
						$content.="<td style='width:15%;color:#878181;'><p>".$date."</p><p>".$list->starttime."</p><p>".$list->endtime."</p></td>";
						$content.="<td style='width:20%'>".$list->schoolname."</td>";
						$content.="<td style='width:40%'>".substr($list->details,0,100)." .</td>";
						//$content.="<td style='width:15%;text-align:center;'><a href='/myession.php'><i aria-hidden='true' title='next' aria-label='next' style='display: inline;padding: //5px;color:#1177d1;' class='icon fa fa-fw fa-arrow-right'></i></a></td>";
						$content.="<td style='width:10%;'>";
						$content.="<a href='".$this->CFG->wwwroot."/mentor/sessiondetail.php?key=".encryptdecrypt_userid($list->id,"en")."'>ViewDetails</a>";
						$content.="<a href='".$this->CFG->wwwroot."/mentor/mentorsession.php?key=".encryptdecrypt_userid($list->id,"en")."' style='margin-left:10%;'>Edit</a>";
						$content.="</td>";
						$content.="</tr>";
					}
					$content.="</table></div>";
				}
			}
		}
		else
			$content.="<p>No Sessions Found! </p>";
		$content.="</div>";
		return $content;
	}	
	
	public function show_sessionDetails($data,$school){
		$starttime = format_timeforReport($data->starttime);
		$endtime = format_timeforReport($data->endtime);
		$html='<div">
		<h1>
		  <a class="btn btn-primary pull-right goBack">Back</a>
		</h1>
		</div>';
		$html.="<table><tr><td width='60%' valign='top'><ul class='profilesearch' style='list-style-type:none;'>";
		$html .="<div class='details'></div>";
		$html.="<li class='details' style='padding-top:1%;'><p><h7>School:</h7>&nbsp;<font color='#daa520'>".$school->name."</font></p></li>";
		$html.="<li class='details' style='padding-top:1%;'><p><h7>Address:</h7>&nbsp;".$school->address."</p></li>";
		$html.="<li class='details' style='padding-top:1%;'><p><h7>Date Of Session:</h7>&nbsp;".date("d-m-Y",$data->dateofsession)."</p></li>";
		$html.="<li class='details' style='padding-top:1%;'><p><h7>Start Time:</h7>&nbsp;".$starttime."</p></li>";
		$html.="<li class='details' style='padding-top:1%;'><p><h7>End Time:</h7>&nbsp;".$endtime."</p></li>";
		$html.="<li class='details' style='padding-top:1%;'><p><h7>Session Type:</h7>&nbsp;".getSessionType($data->sessiontype)."</p></li>";
		$html.="<li class='details' style='padding-top:1%;'><p><h7>Total Hrs:</h7>&nbsp;".$data->totaltime."</p></li>";
		$html.="<li class='details' style='padding-top:1%;'><p><h7>Total Number Of Students:</h7>&nbsp;".$data->totalstudents."</p></li>";
		if(!empty($data->functiondetails))
			$html.="<li class='details' style='padding-top:1%;'><p><h7>Function Detail:</h7>&nbsp;".$data->functiondetails."</p></li>";
		$html.="<li class='details' style='padding-top:1%;'><p><h7>Session Detail:</h7>&nbsp;".$data->details."</p></li>";
		$html.="<li class='details' style='padding-top:1%;'><p><h7>Session Reported On:</h7>&nbsp;".date("d-m-Y",$data->timecreated)."</p></li>";
		$html.="</ul></td>";
		$html.="</tr></table>";
		return $html;
	}
}