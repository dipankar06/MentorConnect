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
 * @Description:upload
*/
require_once('../config.php'); //Include Global Cofiguration file
require_once('render.php'); //include create folder render file
require_once('lib.php'); //include create folder library file
include_once(__DIR__ .'/lib.php');
require_once('../project/externallib.php'); //include project folder library file
require_once($CFG->libdir.'/filelib.php'); //Include global library
require_once("$CFG->libdir/phpexcel/PHPExcel.php");
//require_once('../massupload/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php');
$PAGE->set_url('/create/uploadschool.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Upload School');
echo $OUTPUT->header();
$start = $_REQUEST['start'];
$end = $_REQUEST['end'];
//CREATE TABLE foo LIKE bar;
// Query to remove test from email
//UPDATE some_table SET some_field = REPLACE(some_field, '&lt;', '<') ;
// echo substr($email, 0, -4); // Remove last 4 characters from email
// Code to  Move Data From Temp Table to Moodle Tables
$sql = "Select * from {temp_school_ph2_nov} limit $start,$end";
echo $sql."</br>";
die("Hrere");
if($_REQUEST['mod']=='execute'){
try
{
$result = $DB->get_records_sql($sql);
foreach($result as $schooldata)
{
	$school = new Stdclass();
	$transaction = $DB->start_delegated_transaction();
	$email=$schooldata->email;
	//$email=substr($schooldata->email, 0, -4);
	$emailarr = explode(",",$email);
	$schooldata->schoolname = ucwords(strtolower($schooldata->schoolname));
	$school->atl_id  = $schooldata->uid;
	$school->name  = $schooldata->schoolname;
	if( $schooldata->phone=='Not Available')
		$school->phone  = '';
	else
		$school->phone  = $schooldata->phone;
	$school->school_emailid  = $emailarr[0];
	//echo $schooldata->district;
	//Find city id and add it in cityid field
	$sql = "select c.id from {city} c join {state} s on s.id=c.stateid where c.name='".$schooldata->district."' and s.name='".$schooldata->state."'";
	//$city = $DB->get_record('city',array('name'=>$schooldata->district));
	$city = $DB->get_record_sql($sql);
	$cityid = $city->id;
	$school->cityid = $cityid;
	$schoolid = $DB->insert_record('school', $school); 
	$data = new StdClass();
	// Create School id as a new User
	$result = $DB->get_record('state',array('name'=>$schooldata->state),'id');
	if($result >0)
		$stateid =$result->id;
	$data->school = $schoolid;
	$data->flag='add';
	$data->usertype = "incharge";
	$data->username = $schooldata->uid;
	$data->email =$emailarr[0];
	$data->firstname = $schooldata->schoolname;
	$data->lastname = " ";
	$data->state = $stateid;
	if(isset($emailarr[1]))
		$data->alternate_school_email = trim($emailarr[1]);
	create_newuser($data);
	$transaction->allow_commit(); 
	$content.=$emailarr[0]." - Done"."</br>";
}
}
catch(Exception $e)
{
	$transaction->rollback($e);
	print $e->getMessage();die;
}
}

echo $content;
$result =$DB->get_records('school');
echo  "Total Record in DB : " . count($result)."</br>";
echo "<table class=table table-striped>";
foreach($result as $key => $value )
{
	echo '<tr><td>'.$value->id.'</td><td>'.$value->school_emailid.'</td></tr>';
}
echo "</table>";
echo $OUTPUT->footer();
?>    
