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
 * @CreatedOn: 14-12-2017
 * @Description: ajax calls
*/

require_once(__DIR__ . '/../config.php');
include_once(__DIR__ .'/lib.php');
include_once(__DIR__ .'/render.php');
$postid = $_REQUEST['id']; // Pageid
$mode = $_REQUEST['mode'];
$filterby = $_REQUEST['filterby']; 
$filterby=trim($filterby);
$admin_renderobj = new NitiAdministrationRender();
switch($mode)
{
	case 'movetopage-mentor':
		echo $admin_renderobj->generateContentwithfilter($postid,$filterby);
		break;
	case 'filter-mentor':
		echo $admin_renderobj->generateContentwithfilter($postid,$filterby);
		break;
	case 'filter-mentor-email':
		echo $admin_renderobj->generateContentwithfilter($postid,'email',$filterby);
		break;
	case 'filter-mentorlist-email':
		echo $admin_renderobj->generateContentwithfilter($postid,'listfilteremail',$filterby);
		break;
	case 'filter-school-atlid':
		echo $admin_renderobj->generateSchoolContentwithfilter($postid,'atlid',$filterby);
		break;
	case 'allschool-list':
		echo $admin_renderobj->generateSchoolContentwithfilter($postid,$filterby);
		break;
	case 'allstudent-list':
		echo $admin_renderobj->generateStudentContentwithfilter($postid,$filterby);
		break;
	case 'filter-student-email':
		echo $admin_renderobj->generateStudentContentwithfilter($postid,'email',$filterby);
		break;
	case 'filter-student-schooldetail':
		echo $admin_renderobj->generateStudentContentwithfilter($postid,'schoolfilter',$filterby);
		break;
	case 'all-meetinglist':
		echo $admin_renderobj->generateMeetingListContentwithfilter($postid,$filterby);
		break;
	case 'filter-meeting-school-atlid':
		echo $admin_renderobj->generateMeetingListContentwithfilter($postid,'schoolfilter',$filterby);
		break;
	case 'filter-ms-meeting-status':
		echo $admin_renderobj->generateMeetingListContentwithfilter($postid,'status',$filterby);
		break;	
	case 'filter-ms-meeting-month':
		echo $admin_renderobj->generateMeetingListContentwithfilter($postid,'month',$filterby);
		break;
	case 'movetopage-meeting':
		if($filterby=='all')
			echo $admin_renderobj->generateMeetingListContentwithfilter($postid,$filterby);
		else
			echo $admin_renderobj->generateMeetingListContentwithfilter($postid,'status',$filterby);
		break;
	case 'filter-schoolactivity':
		echo $admin_renderobj->generateSchoolActivityContentwithfilter($postid,$filterby);
		break;
	case 'filter-schoolactivity-atlid':
		echo $admin_renderobj->generateSchoolActivityContentwithfilter($postid,'atlid',$filterby);
		break;
	case 'all-schoolactivity':
		echo $admin_renderobj->generateSchoolActivityContentwithfilter($postid,$filterby);
		break;
	case 'filter-studentactivity':
		echo $admin_renderobj->generateStudentActivityContentwithfilter($postid,$filterby);
		break;
	case 'filter-studentactivity-schooldetail':
		echo $admin_renderobj->generateStudentActivityContentwithfilter($postid,'schoolfilter',$filterby);
		break;	
	case 'filter-studentactivity-email':
		echo $admin_renderobj->generateStudentActivityContentwithfilter($postid,'email',$filterby);
		break;	
	case 'allstudent-acitivylist':
		echo $admin_renderobj->generateStudentActivityContentwithfilter($postid,$filterby);
		break;	
	case 'filter-session-school-atlid':
		echo $admin_renderobj->generateMSessionContentwithfilter($postid,'atlid',$filterby);
		break;
	case 'all-sessionlist':
		echo $admin_renderobj->generateMSessionContentwithfilter($postid,$filterby);
		break;
		
}