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
 * @CreatedOn: 12-01-2018
 * @Description: Assign Badge to Students
*/

defined('MOODLE_INTERNAL') || die();


//moodleform is defined in formslib.php
require_once($CFG->dirroot.'/lib/formslib.php');

class assignbadge_form extends moodleform {

    /**
    * Define the form.
    */
    public function definition() {
        global $CFG, $USER, $OUTPUT;

        $mform = $this->_form; // Don't forget the underscore! 
		$btnstring = "Award";
		$options = $this->_customdata['studentlist'];
		$options1 = $this->_customdata['project'];        
		
		$badgeimgs = $this->_customdata['badgeimg'];
		// Normal fields
		$mform->addElement('html', $badgeimgs);
		$mform->addElement('html', '<div class="clearb"></div>');
		
		$mform->addElement('select', 'name', 'Student Name', $options);    
		$mform->addRule('name', get_string('required'), 'required', null, 'client');  
        
		$mform->addElement('select', 'project', 'Innovation', $options1);
		$mform->addRule('project', get_string('required'), 'required', null, 'client');
        
		//$this->add_action_buttons(false, $btnstring);
		
		$mform->addElement('button', 'awardbtn', $btnstring,array('class'=>'mgtop'));        
    }

    /**
     * Validate the form data.
     * @param array $usernew
     * @param array $files
     * @return array|bool
     */
    function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);
		if (empty($data['Project'])) {
			//$errors['Project'] = "Please Select a Project";
		}
        return $errors;
    }
}
