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
 * @CreatedOn: 15-03-2018
 * @Description:mass delete of mentors
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
$PAGE->set_url('/create/massdelete_mentor.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Mass Delete Mentor');
echo $OUTPUT->header();
//echo strlen("Third_Gender/Transgender");die; 

//UPDATE mdl_temp_mentor SET email = REPLACE(email, 'test', '') WHERE ID <=4
$content = '';

//Code to Move Data From Temporary Table to moodle 
$start = $_REQUEST['start'];
$end = $_REQUEST['end'];
// Copy the table structure 
//CREATE TABLE foo LIKE bar;
$sql = "Select * from {temp_mentor_del_dec} limit $start,$end";
echo $sql;
die;
$deletedata = $DB->get_records_sql($sql );
//echo "<pre>";
//print_r($deletedata);die;
if($_REQUEST['mod']=='execute'){
	$success = $failure = array();
	foreach($deletedata as $key=>$value)
	{
		$result = $DB->get_record('user',array('email'=>$value->email));
		if($result)
		{
			$transaction = $DB->start_delegated_transaction();
			// Moodle User Delete Function 
			$status = delete_user($result);
			if($status)
			{
				// Delete User From a Custom Table
				 $DB->delete_records('user_school', array('userid' =>$id,'role'=>'mentor'));
			}
			$transaction->allow_commit();
			array_push($success,$value->email);
		}
		else
		{
			array_push($failure,$value->email);
		}
	}
		if($success){
			$content.='<div class="alert alert-success"> <strong>Following List of Mentors Deleted Successfully !</strong></div>
				<div style="height:350px;overflow:auto;"><table class="table" height="350px;">
					<thead>
				  <tr>
					<th>Firstname</th>
				  </tr>
				</thead><tbody>';
				foreach($success as $user)
				{
					$content.='<tr><td>'.$user.'</td></tr>';
				}
			$content.='	</tbody></table></div>';
			}
			if($failure){
				$content.='<div class="alert alert-danger">
				  <strong>Email Doesnot Exist!</strong></div>
					 <div style="height:350px;overflow:auto;"><table class="table" height="350px;">
						<thead>
					  <tr>
						<th>Email Id</th>
					  </tr>
					</thead><tbody>';
					foreach($failure as $user)
					{
						$content.= '<tr><td>'.$user.'</td></tr>';
					}
				$content.='</tbody></table></div>';
			}
	}

/*
// Code to Move Data From Excel Sheet To Temp Table
$content='<div class="card-block">
<h1>
  <a class="btn btn-primary pull-right" href="/create">Back</a>
</h1>
</div>';
		try
		{
			$target_file = "../massupload/uploads/Innonet-MentorsToBeDELETED.xlsx";
			$inputfilename = $target_file;
			//$inputfilename = fopen($filename, "r");
			$inputfiletype = PHPExcel_IOFactory::identify($inputfilename);
			
			$objReader = PHPExcel_IOFactory::createReader($inputfiletype);
			
			$objPHPExcel = $objReader->load($inputfilename);
			
			$sheet = $objPHPExcel->getSheet(0); 
			$highestRow = $sheet->getHighestRow(); 				
			$highestColumn = $sheet->getHighestColumn();
			//  Loop through each row of the worksheet in turn
			$failure = $success = array();
			for ($row = 2; $row <= $highestRow; $row++)
			{ 
				$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

				if(count($rowData)>0){
					for($i=0;$i<count($rowData[$i]);$i++)
					{
						if($rowData[$i][0]!='')
						{
							$createuser=($rowData[$i][0]!='')?true:false;
							if($createuser)
							{
									$email =$rowData[$i][0];
									$userinfodata =  new Stdclass();
									$userinfodata->email = $email;
									$userinfodata->sno = isset($rowData[$i][1])?$rowData[$i][1] : '';
									$userinfodata->firstname = isset($rowData[$i][2])?$rowData[$i][2] : '';
									$userinfodata->lastname  = isset($rowData[$i][3])?$rowData[$i][3] : '';
									$userinfodata->gender= isset($rowData[$i][4])?$rowData[$i][4] : '';
									$userinfodata->dob = isset($rowData[$i][5])?$rowData[$i][5] : '';

									$text = $DB->insert_record('temp_mentor_del_dec', $userinfodata);
									array_push($success,$rowData[$i][0]);
							}
							else
							{
									array_push($failure,$rowData[$i][0]);
							}
						}
					}	
				}
			}
			if($success){
			$content.='<div class="alert alert-success"> <strong>Following List of Mentors are Upload Successfully !</strong></div>
				<div style="height:350px;overflow:auto;"><table class="table" height="350px;">
					<thead>
				  <tr>
					<th>Firstname</th>
				  </tr>
				</thead><tbody>';
				foreach($success as $student)
				{
					$content.='<tr><td>'.$student.'</td></tr>';
				}
			$content.='	</tbody></table></div>';
			}
			if($failure){
				$content.='<div class="alert alert-danger">
				  <strong>Email Id Already Exists! Failed to Upload the Following list of Mentors !</strong></div>
					 <div style="height:350px;overflow:auto;"><table class="table" height="350px;">
						<thead>
					  <tr>
						<th>Email Id</th>
					  </tr>
					</thead><tbody>';
					foreach($failure as $student)
					{
						$content.= '<tr><td>'.$student.'</td></tr>';
					}
				$content.='</tbody></table></div>';
			}
		}
		catch(Exception $e)
		{
			echo "<pre>";
			echo  $e->getTraceAsString();
			echo('Error loading file "'.pathinfo($inputfilename,PATHINFO_BASENAME).'": '.$e->getMessage());
		}

*/
/*
$result =$DB->get_records('user',array('msn'=>4,'deleted'=>0),'id desc');
echo  "Total Record in DB : " . count($result)."</br>";
echo "<table class=table table-striped>";
foreach($result as $key => $value )
{
	echo '<tr><td>'.$value->id.'</td><td>'.$value->email.'</td><td>'.$value->city.'</td><td>'.$value->aim.'</td></tr>';
}
echo "</table>"; 
*/
echo $content;
echo $OUTPUT->footer();
?>    
