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

/* @package: core_massupload
 * @CreatedBy: Jothi (IBM)
 * @CreatedOn: 07-03-2018
 * @Description: Mass Upload Data From XLS sheet to Moodle DB
*/

require_once('../config.php');
require_once('PHPExcel-1.8/Classes/PHPExcel/IOFactory.php');
require_login(null, false);
if (isguestuser()) {
    redirect($CFG->wwwroot);
}
require_once($CFG->libdir.'/filelib.php');
$userrole = get_atalrolenamebyid($USER->msn);
if($userrole!="admin"){
	//Only School-incharge can create/edit a student Info.
	redirect($CFG->wwwroot.'/my');
}
$PAGE->set_url('/massupload/upload.php');
//Heading
$PAGE->set_pagelayout('standard');
$strmessages = "Upload Data";
$PAGE->set_title("{$SITE->shortname}: $strmessages");
$PAGE->set_heading("Upload Data");
echo $OUTPUT->header();
?>
<div class="card card-block" >
<div style="margin-bottom:5%;"><h2><span class="heading">Upload Xls Data to ATAL Portal </span></h2></div>
<div class="col-xs-12">
	<form action="upload.php" method="post" enctype="multipart/form-data">
	<div class="form-group row  fitem   ">
			<div class="col-md-3">
				<span class="pull-xs-right text-nowrap">
					<abbr class="initialism text-danger" title="Required"><i class="icon fa icon-exclamation text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
				<i class="icon fa icon-question text-info fa-fw " aria-hidden="true" title="Choose xls" aria-label="Choose xls"></i>
				</span>
				<label class="col-form-label d-inline " for="fileToUpload">
					Choose File to be Uploaded
				</label>
			</div>
			<div class="col-md-9 form-inline felement" data-fieldtype="text">
				<input class="form-control " name="file" id="fileToUpload" value="" size="50" maxlength="100" placeholder="Choose xls" type="file">	
				</div>
	</div>
	<div class="form-group row  fitem   " data-groupname="radioar">
    <div class="col-md-3">
		<span class="pull-xs-right text-nowrap">
					<abbr class="initialism text-danger" title="Required"><i class="icon fa icon-exclamation text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
				<i class="icon fa icon-question text-info fa-fw " aria-hidden="true" title="Choose xls" aria-label="Choose xls"></i>
		</span>
        <label class="col-form-label d-inline " for="fgroup_id_radioar">
            Select type
        </label>
    </div>
    <div class="col-md-9 form-inline felement" data-fieldtype="group">
		<label class="form-check-inline form-check-label  fitem  ">	
			<input name="categorytype" id="id_categorytype_school" value="school" type="radio">ATAL School
		</label>
		<label class="form-check-inline form-check-label  fitem  ">
			<input name="categorytype" id="id_categorytype_incharge" value="incharge" type="radio">School Incharge
		</label>
		<label class="form-check-inline form-check-label  fitem  ">
			<input name="categorytype" id="id_categorytype_students" value="students" type="radio">Students
		</label>
	</div>
	</div>
	<div class="form-group row  fitem femptylabel  " data-groupname="buttonar">
		<div class="col-md-3"></div>
		<div class="col-md-9 form-inline felement" data-fieldtype="group">
			<div class="form-group  fitem  ">
				<label class="col-form-label " for="id_submitbutton">
				</label>
				<span data-fieldtype="submit">
					<input class="btn btn-primary " name="submit" id="id_submit" value="Upload File" type="submit">
				</span>
			</div>   
			 <div class="form-group  fitem   btn-cancel">
				<label class="col-form-label " for="id_cancel">
				</label>
				<span data-fieldtype="submit">
					<input class="btn btn-secondary" name="cancel" id="id_cancel" value="Cancel" onclick="skipClientValidation = true; return true;" type="submit">
				</span>
			</div>
		</div>
	</div>
<input type="hidden" value="y" name="flag">
    <!--<input type="file" name="file" id="fileToUpload">
    <input type="submit" value="Upload" name="submit">-->
</form>
</div>
</div>
<?php
if(isset($_POST['flag'])){
	$categorytype = $_POST['categorytype'];
	$filename=$_FILES["file"]["tmp_name"];		
	$target_dir = "uploads/";
	$target_file = $target_dir . basename($_FILES["file"]["name"]);
	echo $target_file;
		if($_FILES["file"]["size"] > 0){
				$filename = fopen($filename, "r");
				//echo "Here";die;
				 if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
					echo "The file ". basename( $_FILES["file"]["name"]). " has been uploaded.";
				} else {
					echo "Sorry, there was an error uploading your file.";
				} 
							//die;
							/* $file = fopen($filename, "r");
							while (($getData = fgetcsv($file, 10000, ",")) !== FALSE)
							{
									print_r($getData);die;
							} */
		}
				try
				{
					$inputfilename = 'uploads/'.$_FILES["file"]["name"];
					//$inputfilename = fopen($filename, "r");
					$inputfiletype = PHPExcel_IOFactory::identify($inputfilename);
					
					$objReader = PHPExcel_IOFactory::createReader($inputfiletype);
					
					$objPHPExcel = $objReader->load($inputfilename);
					
					$sheet = $objPHPExcel->getSheet(1); 
					$highestRow = $sheet->getHighestRow(); 				
					$highestColumn = $sheet->getHighestColumn();
					//  Loop through each row of the worksheet in turn
					for ($row = 2; $row <= $highestRow; $row++)
					{ 
						//  Read a row of data into an array
						$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
						echo "<pre>";
						print_r($rowData);
						echo $rowData[0][0]."', '".$rowData[0][1]."', '".$rowData[0][2];
					}
				}
				catch(Exception $e)
				{
					die('Error loading file "'.pathinfo($inputfilename,PATHINFO_BASENAME).'": '.$e->getMessage());
				}
		 //}
	//print_r($_POST);die;

}
echo $OUTPUT->footer();
?>      
