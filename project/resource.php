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

/* @package: core_project
 * @CreatedBy: Dipankar (IBM)
 * @CreatedOn: 27-01-2018
 * @Description: Project Resource add page
 Detail page for specific project i.e project collaboration
*/

require_once('../config.php');

require_login(null, false);
if (isguestuser()) {
    redirect($CFG->wwwroot);
}
$id = optional_param('id', 0, PARAM_INT); // Project id.

require_once($CFG->libdir.'/filelib.php');
include_once(__DIR__ .'/lib.php');

include_once(__DIR__ .'/addresource_form.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/mod/resource/locallib.php');

$frmobject = new mod_resource_mod_form($CFG->wwwroot.'/project/resource.php?id='.$id,array('course'=>$id,'module'=>0));
$data = $frmobject->get_data();
$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);
//$data->files = 605314889; //for Testing purpose only.....
$flag = 2;
if(isset($data->name) && !empty($data->name)){	
	if(!empty($data->files)){
		$fromform = frmadd_moduleinfo($data, $course, $frmobject);
		$flag = ($fromform==true)? 1 : 2;
	}
}

$url = $CFG->wwwroot.'/project/detail.php?flag='.$flag.'&id='.encryptdecrypt_projectid($id,"en");
redirect($url);

function frmadd_moduleinfo($moduleinfo, $course, $mform = null){
	global $DB, $USER,$CFG;
	$error = false;
	// First add course_module record because we need the context.
    $newcm = new stdClass();
    $newcm->course           = $course->id;
    $newcm->module           = $moduleinfo->module;
    $newcm->instance         = 0; // Not known yet, will be updated later (this is similar to restore code).
    $newcm->visible          = $moduleinfo->visible;
    $newcm->visibleold       = $moduleinfo->visible;
	if (isset($moduleinfo->visibleoncoursepage)) {
        $newcm->visibleoncoursepage = $moduleinfo->visibleoncoursepage;
    }
    if (isset($moduleinfo->cmidnumber)) {
        $newcm->idnumber  = $moduleinfo->cmidnumber;
    }
    $newcm->groupmode = 0;
    $newcm->groupingid = 0;
	if (isset($moduleinfo->showdescription)) {
        $newcm->showdescription = $moduleinfo->showdescription;
    } else {
        $newcm->showdescription = 0;
    }
	
	// From this point we make database changes, so start transaction.
    $transaction = $DB->start_delegated_transaction();
	if (!$moduleinfo->coursemodule = add_course_module($newcm)) {
        return false;
    }
	$addinstancefunction  = $moduleinfo->modulename."_addinstances";
	
	try {
        $returnfromfunc = $addinstancefunction($moduleinfo, $mform);
    } catch (moodle_exception $e) {
        return false;
    }
	try {
        $sectionid = course_add_cm_to_section($course, $moduleinfo->coursemodule, $moduleinfo->section);
    } catch (moodle_exception $e) {
        return false;
    }
	
	$transaction->allow_commit();
	
	return true;	
}

function resource_addinstances($data, $mform) {
    global $CFG, $DB;
    
    $cmid = $data->coursemodule;
    $data->timemodified = time();
	$data->displayoptions = serialize(Array("printintro" => 1 ));
	$data->introformat = 1;
	$data->revision = 1;
	unset($data->id);	
    $data->id = $DB->insert_record('resource', $data);
    // we need to use context now, so we need to make sure all needed info is already in db
    $DB->set_field('course_modules', 'instance', $data->id, array('id'=>$cmid));
    resource_set_mainfile($data);
    return $data->id;
}
?>