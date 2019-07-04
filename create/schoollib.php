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
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 12-03-2018
 * @Description:Library functions for Create School & Incharge Module
*/

require_once($CFG->libdir.'/filelib.php');
require_once($CFG->dirroot.'/project/externallib.php');

//New Student with School Incharge..
function create_newschool($data){
	global $DB;
	
	$school = new stdClass();
	$school->name=  ucfirst(trim($data->name));
	$school->cityid = $data->city;
	$school->address = trim($data->address);
	$school->atl_id = trim($data->atl_id);
	$school->school_emailid = $data->school_emailid;
	$school->phone = $data->phone;
	$school->principal_email = $data->principal_email;
	$school->principal_phone = trim($data->principal_phone);
	$school->principal_name = ucfirst(trim($data->principal_name));
	$school->udsid = trim($data->udsid);
	$school->pfms = trim($data->pfms);
	if ($data->flag=='add') {
		// Add School Details
		try
		{			
			$schoolid = $DB->insert_record('school', $school);
			if($schoolid > 0 ){
				if(trim($data->email)!=''){
					$data->school = $schoolid;
					$data->usertype = "incharge";
					$data->username = $data->atl_id;
					create_newuser($data); //Create School Incharge
				}
				else
				{
					$data->school = $schoolid;
					$data->usertype = "incharge";
					$data->username = $data->atl_id;
					$data->email =$data->school_emailid;
					$data->firstname = $data->name;
					$data->lastname = " ";
					create_newuser($data);
				}
			}
		}
		catch(Exception $e){
			echo $e->getMessage(); die;
		}
	}
	return true;
}

//update school data & Incharge Data
function update_school($data){
	//print_r($data);die;
	global $DB;
	$school = new stdClass();
	$school = $data;
	$school->id = $data->schoolid;
	$school->cityid = $data->city;
	$DB->update_record('school', $school);
	$user = new stdClass();
	if($data->inchargeid!=''&&$data->inchargeid!=0&&$data->inchargeid!=-1)
	{
		$result = get_atal_citybycityid($data->city);
		if(count($result)>0)
			$user->city = $result->name;
		$user->id = $data->inchargeid;
		$user->firstname = $data->firstname;
		$user->lastname = $data->lastname;
		//$user->username = $data->atl_id;
		$user->email = $data->email;
		$user->gender = $data->gender;
		$user->phone1 = $data->phone1;
		$DB->update_record('user', $user);
	}
	else
	{
		$newuser = new StdClass();
		$newuser->flag='add';
		$newuser->usertype = "incharge";
		$newuser->school = $data->schoolid;
		$newuser->firstname = $data->firstname;
		$newuser->lastname = $data->lastname;
		$newuser->gender = $data->gender;
		$newuser->phone1 = $data->phone1;
		$newuser->username = $data->atl_id;
		$newuser->email = $data->email;
		create_newuser($newuser); 
	}
	return true;
}