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

/* @package: core_webservice
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 19-Dec-2018
 * @Description: Web services External File Upload for atlinnonet
 This file is a Copy of /webservice/upload.php
 /tmp/uploadedimages/ ..must create this folder in server.
*/

define('NO_MOODLE_COOKIES', true);
require_once(__DIR__ . '/../config.php');
// Allow CORS requests.
header('Access-Control-Allow-Origin: *');
require_once($CFG->dirroot . '/webservice/lib.php');

// Get image string posted from Android App
$data = $_REQUEST;
$base = $data['image'];

$imagebase = json_decode($base);
$image_path = array();
$responsearray = array();
$wsfunction = "";
$id = 0;
//Server temporary image location
$tmp_imgpath = '/tmp/uploadedimages/';

if(is_array($imagebase)){
	$responsemsg = "Error in Request";
	foreach($imagebase as $key=>$values){
		$imgarray = (array) $values;
		foreach($imgarray as $k1=>$v1){
			if($k1=='id'){
				$id = $v1;
			} elseif($k1=='wsfunction'){
				$wsfunction = $v1;
			} else{
				//Base64 encode content
				$imgdata = "";
				$imgdata = urldecode($v1);
				$binary=base64_decode($imgdata);
				$rand = rand(1,500);
				$filename = 'atlimg'.$rand.'.png';
				$fname = 'atlimg'.$rand;
				header('Content-Type: bitmap; charset=utf-8');
				$file = fopen($tmp_imgpath.$filename, 'wb');
				// Create File
				fwrite($file, $binary);
				fclose($file);
				$image_path[$fname] = $tmp_imgpath.$filename;
			}
		}
	}
	if(count($image_path)>0 && !empty($wsfunction)){
		//Link image to Moodle
		$obj = new wsimageupload();
		if($wsfunction=="mentorsession"){
			$result = $DB->get_record("mentor_sessionrpt",array('id'=>$id),'id,mentorid');
			$user = $DB->get_record("user",array('id'=>$result->mentorid),'id,firstname,lastname');
			$tmp = $user->firstname.' '.$user->lastname;
			$params = array('id'=>$id,'userid'=>$user->id,'username'=>$tmp);
			foreach($image_path as $k2=>$v2){
				$params['filepath'] = $v2;
				$params['filename'] = $k2;
				$obj->ws_mentorsession($params);
			}
			unset($result);
			unset($user);
			$responsemsg = "Image Uploaded Successfully";
		} else{
			$responsemsg = "wsfunction name not found!";
		}		
	}
} else{
	$responsemsg = "Error Occurs!";
}

//Class to Do Uploads..
class wsimageupload{
	//Upload Mentor Report your Session Files.
	public function ws_mentorsession($params){
		global $CFG;
		$context = context_user::instance($params['userid']);
		$fs = get_file_storage();
		$file_record = new stdClass;
		$file_record->component = 'mentorsession_file';
		$file_record->contextid = $context->id;
		$file_record->userid    = $params['userid'];
		$file_record->filearea  = 'files_1';
		$file_record->filename  = $params['filename'];
		$file_record->filepath  = '/';
		$file_record->itemid    = $params['id'];
		$file_record->license   = $CFG->sitedefaultlicense;
		$file_record->author    = $params['username'];
		$file_record->source    = serialize((object)array('source' => $params['filename']));
		//Create File
		$fs->create_file_from_pathname($file_record, $params['filepath']);
		unlink($params['filepath']); //Remove temp img file.
	}
}

echo $responsemsg;
exit;
?>