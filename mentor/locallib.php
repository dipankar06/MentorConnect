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

/* @package: core_mentor
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 22-11-2018
 * @Description: Local Library functions for Mentor New Module
 Local Lib sepcifically use with render
*/

include_once(__DIR__ .'/render.php');

//Mentor: report your session Deatil page.
function get_mentorsession_details($recordset,$sessionid,$renderObj){
	global $DB;
	$school = $DB->get_record('school', array('id'=>$recordset->schoolid), 'name,address');
	return $renderObj->show_sessionDetails($recordset,$school);
}

//Get session details
function get_mentorsession_dbdata($sessionid){
	global $DB;
	$recordset = $DB->get_record('mentor_sessionrpt', array('id'=>$sessionid));
	return $recordset;
}

//delete a mentor: report your session DB data.
//Params: id int : session id.
function delete_mentorsession($id){
	global $DB, $CFG;
	$rptrecord = $DB->get_record('mentor_sessionrpt', array('id'=>$id),'id,mentorid');
	if(!isset($rptrecord->mentorid)){
		return false;
	}
	if(dodelete_session($id,$rptrecord)===true){
		$file_id=1;
		$context = context_user::instance($rptrecord->mentorid);
		$fs = get_file_storage();
		$files = $fs->get_area_files($context->id, 'mentorsession_file', 'files_1', $id, "filename", false);
		foreach ($files as $file) {
			$file1 = $fs->get_file($context->id, 'mentorsession_file', 'files_1', $id, $file->get_filepath(), $file->get_filename());			
			// Delete it if it exists
			if ($file1) {
				$file1->delete();
				$DB->delete_records('files', array('component'=>'mentorsession_file','itemid'=>$id));
			}
		}
		return true;
	} else{
		return false;
	}
}

function dodelete_session($id,$rptrecord){
	global $DB;
	try {
		// From this point we make database changes, so start transaction.
		$transaction = $DB->start_delegated_transaction();
		$DB->delete_records('mentor_sessionrpt', array('id'=>$id));
		$transaction->allow_commit(); //close transaction
		return true;
	} catch(Exception $e) {
		$transaction->rollback($e);
		return false;
	}
}

