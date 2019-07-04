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

/* @package: core_ticket
 * @CreatedBy: Jothi (IBM)
 * @CreatedOn: 18-07-2018
 * @Description: ajax
*/
require_once(__DIR__ . '/../config.php');

require_login();
include_once(__DIR__ .'/lib.php');
include_once(__DIR__ .'/render.php');

define('AJAX_SCRIPT', true);
$postid = $_REQUEST['id']; // Pageid
$mode = $_REQUEST['mode'];
$statuslist = get_allstatus();
$renderObj = new techticket_render($statuslist);
$filtervalue='';

if($mode=='movetopage') {
	$filterby = trim($_REQUEST['filters']);
	$filterby = json_decode($filterby, true);
	echo get_contentwithfilter($renderObj,$postid,$mode,$filterby);	
} 
elseif($mode=='filterbystatus') {
	$filterbytwo = trim($_REQUEST['filterbytwo']);
	$filterbystatus = trim($_REQUEST['filterby']);
	$filterby = array('dropdown2'=>$filterbytwo,'dropdown1'=>$filterbystatus);
	echo get_contentwithfilter($renderObj,$postid,$mode,$filterby);
}
elseif($mode=='searchfilter') {
	$filterby = trim($_REQUEST['filters']);
	echo get_contentwithfilter($renderObj,$postid,$mode,$filterby);	
}
elseif($mode=='resetfilters') {
	$filterby = trim($_REQUEST['filters']);
	echo get_contentwithfilter($renderObj,$postid,$mode,$filterby);	
}
elseif($mode=='newreply') {
	$data = trim($_REQUEST['reply']);
	insert_newreply($postid,$data);
}
elseif($mode=='delete') {
	//ticketid: A33567A8786
	$temp = explode("A",$postid);
	$ticketid = $temp[1]-3567;
	$status = delete_ticket($ticketid);
	echo $status;
}
elseif($mode=='status') {	
	$temp = explode("A",$postid);
	$ticketid = $temp[1]-3567;
	if($_REQUEST['status']=="delete")
		$status = delete_ticket($ticketid,$_REQUEST['msg']);
	else
		$status = change_status($ticketid,$_REQUEST['status']);
	echo $status;
}
elseif($mode=='filterbycategory') {
	$filterby = trim($_REQUEST['filterby']);
	$filterbystatus = trim($_REQUEST['filterbytwo']);
	$filterby = array('dropdown2'=>$filterby,'dropdown1'=>$filterbystatus);
	echo get_contentwithfilter($renderObj,$postid,$mode,$filterby);	
}
else{
	$filterby = trim($_REQUEST['filters']);
	echo get_contentwithfilter($renderObj,$postid,$mode,$filterby);
}
die();
?>
