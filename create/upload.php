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
//require_once('../massupload/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php');
require_once("$CFG->libdir/phpexcel/PHPExcel.php");
$PAGE->set_url('/create/upload.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Upload Page');
echo $OUTPUT->header();
class customException extends Exception {
  public function errorMessage() {
	global $OUTPUT,$CFG;
    
	$url =$CFG->wwwroot.'/create/STUDENT-LIST.xlsx';
	$src =$OUTPUT->image_url('excelicon', 'theme');
	$errorMsg = '<div class="alert alert-danger">
				  <strong>Invalid Excel Format ! <a href="'.$url.'"> Please Download from here !  <img width="29px;" src="'.$src.'"></a></strong>
				  <button class="close" type="button" data-dismiss="alert">×</button>
				  </div>'; 
	return $errorMsg;
  }
}
// Submit Function for mass upload Student Details
$content='<div class="card-block">
<h1>
  <a class="btn btn-primary pull-right" href="liststudent.php">Back</a>
</h1>
</div>';
if(isset($_REQUEST['flag'])){
	try
	{
		//$sheetno = isset($_POST['sheetno'])?$_POST['sheetno']:1;
		//$sheetno = $sheetno-1;
		$filename=$_FILES["file"]["tmp_name"];		
		$target_dir = "../massupload/uploads/";
		$target_file = $target_dir . basename($_FILES["file"]["name"]);
		$filename =$_FILES["file"]["name"];
		 if(file_exists($target_file)){
			 $ext = pathinfo($filename, PATHINFO_EXTENSION);
			$filename= basename($filename, ".".$ext);
			$filename=$filename.time().'.'.$ext;
			$target_file = $target_dir . $filename;
		 }	
			if($_FILES["file"]["size"] > 0){
					$filename = fopen($filename, "r");
					 if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
						//echo "The file ". basename( $_FILES["file"]["name"]). " has been uploaded.";
					} else {
						echo "Sorry, there was an error uploading your file.";
					} 
			} 
			//$target_file = "../massupload/uploads/STUDENT-LIST1525414045.xlsx";
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
			$rowData = $sheet->rangeToArray('A' . 1 . ':' . $highestColumn . 1, NULL, TRUE, FALSE);
			if(count($rowData)>0){
				if(trim($rowData[0][0])!='S.No' || trim($rowData[0][1])!='First Name' || trim($rowData[0][2])!='Second Name' || trim($rowData[0][3])!='Class' || trim($rowData[0][4])!='Email' || trim($rowData[0][5])!='Mobile'){
					unlink($target_file);
					throw new customException();
				}
			}
			for ($row = 2; $row <= $highestRow; $row++)
			{ 
				$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
					if(count($rowData)>0){
					for($i=0;$i<count($rowData[$i]);$i++)
					{
						if($rowData[$i][4]!='')
						{
							$checkemail = $DB->get_record('user', array('email'=>$rowData[$i][4]), 'id');
							$createuser=(isset($checkemail->id))?false:true;
							if($createuser)
							{
									$data = new Stdclass();
									$data->flag="add";
									$data->usertype="student";
									$data->username=$rowData[$i][4];
									$data->email=$rowData[$i][4];
									$data->icq="newuser";
									$data->newpassword = 'Test@123';
									$data->firstname = $rowData[$i][1];
									$data->lastname = $rowData[$i][2];
									$data->phone1 = $rowData[$i][5];
									$data->studentclass = $rowData[$i][3];				
									create_newuser($data);
									array_push($success,$rowData[$i][1]);
							}
							else
							{
									array_push($failure,$rowData[$i][4]);
							}
						}
					}	
				}
			}
			if($success){
			$content.='<div class="alert alert-success"> <strong>Following List of Students are Upload Successfully !</strong></div>
				<table class="table">
					<thead>
				  <tr>
					<th>Firstname</th>
				  </tr>
				</thead><tbody>';
				foreach($success as $student)
				{
					$content.='<tr><td>'.$student.'</td></tr>';
				}
			$content.='	</tbody></table>';
			}
			if($failure){
				$content.='<div class="alert alert-danger">
				  <strong>Email Id Already Exists! Failed to Upload the Following list of Students !</strong></div>
					 <table class="table">
						<thead>
					  <tr>
						<th>Email Id</th>
					  </tr>
					</thead><tbody>';
					foreach($failure as $student)
					{
						$content.= '<tr><td>'.$student.'</td></tr>';
					}
				$content.='</tbody></table>';
			}
			//$urltogo = new moodle_url('/create/liststudent.php', array('status' =>'success')); // Move To School Detail Page
			//redirect($urltogo); 
		}
		catch(customException $e) {
		  $content.=$e->errorMessage();
		}
		catch(Exception $e)
		{
			echo('Error loading file "'.pathinfo($inputfilename,PATHINFO_BASENAME).'": '.$e->getMessage());
			echo $OUTPUT->footer();
		}
}
echo $content;
echo $OUTPUT->footer();
?>    