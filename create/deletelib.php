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
 * @CreatedOn: 20-03-2018
 * @Description: library file
*/

require_once(__DIR__ . '/../config.php');
require_once('lib.php');

/*
 * Function to Delete Mentor From ATAL Portal
 * Returns boolean
*/
function delete_mentor($id)
{
	global $DB;
	$result = $DB->get_record('user',array('id'=>$id));
	try{
	$transaction = $DB->start_delegated_transaction();
	// Moodle User Delete Fucnction 
	$status = delete_user($result);
		if($status)
		{
			// Delete User From a Custom Table
			 $DB->delete_records('user_school', array('userid' =>$id,'role'=>'mentor'));
		}
		$transaction->allow_commit();
		echo "success";die;
	}
	catch(Exception $e)
	{
		$transaction->rollback($e);
		echo "failed";die;
	} 
}

/*
 * Function to Delete School From ATAL Portal
 * Returns boolean
*/
function delete_school($id)
{
	global $DB;
	try{
	$transaction = $DB->start_delegated_transaction();	
	$result = $DB->get_records('user_school', array('schoolid'=>$id));
	if(count($result)>0)
	{
		foreach($result as $userdetail)
		{
			if($userdetail->role == 'student' || $userdetail->role == 'incharge')
			{
				$userobj = $DB->get_record('user',array('id'=>$userdetail->userid));
				$status = delete_user($userobj);
			}
		}
	}
	$DB->delete_records('user_school', array('schoolid'=>$id));
	$DB->delete_records('school', array('id'=>$id));
	$transaction->allow_commit();
	$course = $DB->get_records('course',array('idnumber'=>$id),null,'id');
	if(count($course)>0){
		foreach($course as $key=>$values){
			delete_course($values->id, false);
		}
	}
	echo "success";die;
	}
	catch(Exception $e)
	{
		$transaction->rollback($e);
		echo "failed";die;
	}
}

function delete_student($id)
{
	global $DB;
	try{
	$transaction = $DB->start_delegated_transaction();
	$userobj = $DB->get_record('user',array('id'=>$id));
	$status = delete_user($userobj);
	$DB->delete_records('user_school', array('userid'=>$id,'role'=>'student'));	
	$transaction->allow_commit();
	echo "success";die;
	}
	catch(Exception $e)
	{
		$transaction->rollback($e);
		echo "failed";die;
	}
	
}

function delete_event_admin($id)
{
	global $DB;
	try{
		$transaction = $DB->start_delegated_transaction();
		$data = $DB->get_record('event',array('id'=>$id),'parentid');
		if($data->parentid>0){
			//File attachment i.e event img is there to Delete.
			remove_eventimage($id,$data->parentid);
		}
		$DB->delete_records('event', array('id'=>$id));
		$transaction->allow_commit();
		echo "success"; die;
	}
	catch(Exception $e){
		$transaction->rollback($e);
		echo "failed"; die;
	}
}
?>