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
 * @CreatedOn: 26-02-2018
 * @Description: massupload
*/

require_once('../config.php');
?>
<html lang="en">
<head>
	<meta charset="UTF-8">
  <title>Migrate Data To DataBase </title>
</head>
<body>
<div>
	<form action="index.php" method="post" enctype="multipart/form-data">
	  <input type="hidden" value="y" name="flag">
    <input type="file" name="file" id="fileToUpload">
    <input type="submit" value="Upload" name="submit">
</form>
</div>
</body>
</html>          
<?php
// Upload Data From XLSX to DB
require 'PHPExcel-1.8/Classes/PHPExcel/IOFactory.php';

if(isset($_POST['flag'])){
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
		
}
?>      
