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
 * @CreatedOn: 08-06-2018
 * @Description: Ajax function 
*/

require_once(__DIR__ . '/../config.php');
include_once(__DIR__ .'/lib.php');
include_once(__DIR__ .'/render.php');
include_once(__DIR__ .'/deletelib.php');
include_once(__DIR__ .'/../atalfeatures/ajax.php'); // Ajax file is called for City value 
require_login();
define('AJAX_SCRIPT', true);
$postid = $_REQUEST['id']; // Pageid
$mode = $_REQUEST['mode'];
$name= isset($_REQUEST['name'])?$_REQUEST['name']:'';
$state= isset($_REQUEST['state'])?$_REQUEST['state']:0;
$city= isset($_REQUEST['city'])?$_REQUEST['city']:0;
if($city=='0')
	$city=0;
$createtab_renderobj = new CreateStackholders();
switch($mode)
{
	case 'movetopage':
		echo $createtab_renderobj->display_mentors($postid,$name,$state,$city);
		break;
	case 'deletementor':
		$mentor_id = $_REQUEST['delete_id'];
		delete_mentor($mentor_id);
		break;
	case 'movetopage_school':
		echo $createtab_renderobj->display_schools($postid,$name,$state,$city);
		break;
	case 'deleteschool':
		$schoolid = $_REQUEST['delete_id'];
		delete_school($schoolid); 
		break;
	case 'deletestudent':
		$studentid = $_REQUEST['delete_id'];
		delete_student($studentid); 
		break;
	case 'deleteevent':
		$eventid = $_REQUEST['delete_id'];
		delete_event_admin($eventid);
		break;
}
?>
