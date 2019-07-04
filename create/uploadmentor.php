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
set_time_limit(0);
require_once('../config.php'); //Include Global Cofiguration file
require_once('render.php'); //include create folder render file
require_once('lib.php'); //include create folder library file
include_once(__DIR__ .'/lib.php');
require_once('../project/externallib.php'); //include project folder library file
require_once($CFG->libdir.'/filelib.php'); //Include global library
require_once("$CFG->libdir/phpexcel/PHPExcel.php");
//require_once('../massupload/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php');
$PAGE->set_url('/create/uploadmentor.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Upload Mentor');
echo $OUTPUT->header();
//echo strlen("Third_Gender/Transgender");die; 

//die();
//UPDATE mdl_temp_mentor SET email = REPLACE(email, 'test', '') WHERE ID <=4
$content = '';

//Code to Move Data From Temporary Table to moodle 
$start = $_REQUEST['start'];
$end = $_REQUEST['end'];
// Copy the table structure 
//CREATE TABLE foo LIKE bar;
$sql = "Select * from {temp_mentor_ph2_dec} limit $start,$end";
//echo $sql."</br>";
//$sql = 'Select * from {temp_mentor} where id = 35';
echo $sql."</br>";
//Finding Language Value
$language = get_languages();

$newidarray=''; 
//Finding Language Value
$state = $DB->get_records('state');
$newstate = array();
foreach($state as $stateid)
{
	$newstate[$stateid->id] =trim($stateid->name);
}

if($_REQUEST['mod']=='execute'){

try
{
$result = $DB->get_records_sql($sql );
$content.= "<div>Follwoing Mentors are added Successfully</div>";
foreach($result as $mentordata)
{
	$langidstr =array();
	$newidarray=array();  
	//$mentordata->email=substr($mentordata->email, 0, -4);
	$mentordata->email=trim($mentordata->email);
	//echo "here";die;
	//echo "<pre>";
	//print_r($mentordata);die;
	$emailrecord = $DB->get_record('user',array('email'=>$mentordata->email),'email');
	if(isset($emailrecord->email))
		continue; 
	$lan_array = explode(',',$mentordata->language);
	if(count($lan_array)>0)
	{
		foreach($lan_array as $lan)
		{
			if(array_search(strtolower($lan), array_map('strtolower', $language)))
				$newidarray[] =array_search(strtolower($lan), array_map('strtolower', $language));
		}
			$langidstr = implode(',',$newidarray);
	}
	else
		$langidstr = 5;
	$schoolid ='';
	$dob ='';
	$state = array_search(strtolower($mentordata->state), array_map('strtolower', $newstate));
	$school = $DB->get_record('school',array('atl_id'=>$mentordata->schoolassigneduid),'id');
	if(count($school)>0)
		$schoolid = $school->id;
	if($mentordata->dob){
		$dob = $mentordata->dob;
		$dob_array = explode('-',$mentordata->dob);
		$dob_array = array_reverse($dob_array);
		$dob = implode('/',$dob_array);
	}
	$data =new stdClass();
	$infodata = new stdClass();	
			$mentor_roleid = atal_get_roleidbyname("mentor");
			$data->auth = 'manual';
			$data->confirmed = '1';
			$data->mnethostid = '1';
			$data->username = trim($mentordata->email);
			$ran = generate_randomstring();
			$data->passraw = $ran;
			$data->password=hash_internal_user_password($ran);
			$data->idnumber = 'mentor';
			$data->firstname = trim(ucwords(strtolower($mentordata->firstname)));
			$data->lastname = trim(ucwords(strtolower($mentordata->lastname)));
			$data->email = trim($mentordata->email);
			$data->skype = '';
			$data->yahoo = $dob;
			$data->aim = $state ;
			$data->msn = $mentor_roleid;
			$data->phone1 = '';
			$data->phone2 = '';
			$data->institution  =  ucwords(trim($mentordata->institute,"'"));
			$data->department =  ucwords(trim($mentordata->areaspec,"'"));
			$data->address  = '';
			if(isset($mentordata->city))
			{
			if($mentordata->city == 'Hyderabad') 
				$mentordata->state = 'Telangana';
			$sql = "select c.name from {city} c join {state} s on s.id=c.stateid where c.name='".$mentordata->city."' and s.name='".$mentordata->state."'";
			$city = $DB->get_record_sql($sql);
			$cityname = $city->name;
			$data->city = $cityname;
			}
			$data->country = 'IN';
			$data->theme  = '';
			$data->timezone  = '99';
			$data->icq = "newuser";
			$data->lastip  = '';
			$data->secret  = '';
			if($mentordata->linkedin==0)
				$data->url  ='';
			else
				$data->url  =$mentordata->linkedin;
			$data->description  = ucwords(trim($mentordata->summary,"'")); 
			$data->timecreated = time();
			$data->timemodified = time();
			$data->lastnamephonetic  =$mentordata->degree;
			$data->firstnamephonetic  = strtolower($mentordata->registeras);
			$data->middlename  = $mentordata->yoc;
			$data->alternatename = $langidstr ;
		//	if($mentordata->gender=='Male')
			//	$data->gender = 'm';
			//elseif($mentordata->gender=='Trans')
			//	$data->gender = 't';
			//else
			//	$data->gender = 'f';
			//Details For Mentor India & Refreence check info
			$infodata = new stdClass();
			$infodata->schoolid  = $schoolid;
			$infodata->timecommitperday  = $mentordata->timecommit;
			$infodata->possibleareaofinterven  = trim($mentordata->areacont,"'");
			$mentordata->otherschool = 'null';
			if(strtolower($mentordata->otherschool)=='null'  || strtolower($mentordata->otherschool)=='no')
				$infodata->otherschooloption  = 'n';
			$infodata->whymentor  =  trim($mentordata->words,"'");
			$infodata->refree1_name  =isset($mentordata->ref1name)?$mentordata->ref1name:'';
			$infodata->refree1_contact  =isset($mentordata->ref1contact)?$mentordata->ref1contact:'';
			//$infodata->refree1_email  =isset($mentordata->ref1email)?substr($mentordata->ref1email, 0, -4):'';
			$infodata->refree1_email  =isset($mentordata->ref1email)?trim($mentordata->ref1email):'';
			$infodata->refree1_know   =isset($mentordata->ref1how)?$mentordata->ref1how:'';
			$infodata->refree2_name   =isset($mentordata->ref2name)?$mentordata->ref2name:'';
			$infodata->refree2_contact  =isset($mentordata->ref2contact)?$mentordata->ref2contact:'';
			//$infodata->refree2_email  =isset($mentordata->ref2email)?substr($mentordata->ref2email, 0, -4):'';
			$infodata->refree2_email  =isset($mentordata->ref2email)?trim($mentordata->ref2email):'';
			$infodata->refree2_know  =isset($mentordata->ref2how)?$mentordata->ref2how:'';
			$infodata->hearaboutmentor = isset($mentordata->hearabout)?$mentordata->hearabout:'';
			if($mentordata->company=='na')
				$infodata->company  ='';
			else
				$infodata->company  =$mentordata->company;
			//$infodata->company = isset($mentordata->company)?$mentordata->company:'';
			if($mentordata->othercompany!=0)
				$infodata->company = $mentordata->othercompany;
			$infodata->acceptterms  =1;
			if($mentordata->fburl==0)
				$infodata->fburl  ='';
			else
				$infodata->fburl  =$mentordata->fburl;
		//	echo "<pre>";print_r($data);
			//print_r($infodata);die;
			//continue;
			$uemail = trim($mentordata->email);
			if(!empty($uemail)){
				$transaction = $DB->start_delegated_transaction();
				//echo "<pre>";
				$id = $DB->insert_record('user', $data);
				//print_r($data);
				$infodata->userid = $id;
				$infodata->data  = 'mentor data';
				//print_r($infodata);								
				$userinfodataid = $DB->insert_record('user_info_data', $infodata);
				//echo $userinfodataid;				
				$usercontext = context_user::instance($id);
				if($schoolid!='' && $schoolid!=0)
				{
					$school= new stdClass();
					$school->userid = $id;
					$school->schoolid=$schoolid;
					$school->role='mentor';
					$text = $DB->insert_record('user_school', $school); 
					//print_r($school);
				}
				//die;
				$transaction->allow_commit(); 
				$content.= "<div>".$mentordata->email."</div>";
			}
}
$content.= "Mentor Migrated Successfully";
}
catch(Exception $e)
{
	print $e->getMessage();
} 
}

echo $content;


echo $OUTPUT->footer();
?>    
