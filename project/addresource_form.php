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
 * @Description: Project Module -Add resource to a Project
*/

defined('MOODLE_INTERNAL') || die();


//moodleform is defined in formslib.php
require_once($CFG->dirroot.'/lib/formslib.php');

class mod_resource_mod_form extends moodleform {

    /**
    * Define the form.
    */
    public function definition() {
        global $CFG, $USER, $OUTPUT;

        $mform = $this->_form; // Don't forget the underscore! 
		$btnstring = "Add";
		$courseid = $this->_customdata['course'];
		$module = $this->_customdata['module'];
        $mform->addElement('hidden', 'userid');
        $mform->setType('userid', PARAM_INT);
        $mform->setDefault('userid', $USER->id);

        // Normal fields
        $mform->addElement('text', 'name', 'FileName', 'size="50" maxlength="30"');
        $mform->addRule('name', get_string('required'), 'required');
        $mform->setType('name', PARAM_TEXT);
		
		$maxbytes = "8000000"; //in bytes - 8MB
        $mform->addElement('filepicker', 'files','File', null,
            array('maxbytes' => $maxbytes, 'accepted_types' => 'jpg,png,pdf,mp4'));

        $mform->addElement('hidden', 'modulename', 'resource');
        $mform->addElement('hidden', 'course', $courseid);
		$mform->addElement('hidden', 'module', $module);
		$mform->addElement('hidden', 'add', 'resource');
		$mform->addElement('hidden', 'update', 0);
		//other info
		$mform->addElement('hidden', 'display', 0);
		$mform->addElement('hidden', 'popupwidth', 620);
		$mform->addElement('hidden', 'popupheight', 450);
		$mform->addElement('hidden', 'printintro', 1);
		$mform->addElement('hidden', 'filterfiles', 0);
		$mform->addElement('hidden', 'visible', 1);
		$mform->addElement('hidden', 'visibleoncoursepage', 1);
		$mform->addElement('hidden', 'cmidnumber', '');
		$mform->addElement('hidden', 'completionunlocked', 1);
		$mform->addElement('hidden', 'completion', 1);
		$mform->addElement('hidden', 'completionexpected', 1);
		$mform->addElement('hidden', 'tags', '');
		$mform->addElement('hidden', 'coursemodule', 0);
		$mform->addElement('hidden', 'section', 0);
		$mform->addElement('hidden', 'instance', 0);
		
		 $this->add_action_buttons(false, get_string('save'));
		//$mform->addElement('button', 'addasset', $btnstring,array('class'=>'mgtop'));    
    }

    /**
     * Validate the form data.
     * @param array $usernew
     * @param array $files
     * @return array|bool
     */
    function validation($data, $files) {
        global $DB;
        /*$errors = parent::validation($data, $files);
		if (empty($data['name'])) {
			$errors['Project'] = "Please Enter File name";
		}
		$fs = get_file_storage();
        if (!$files = $fs->get_area_files($usercontext->id, 'user', 'draft', $data['files'], 'sortorder, id', false)) {
            $errors['files'] = get_string('required');            
        }
        return $errors;	
		*/
    }
}
