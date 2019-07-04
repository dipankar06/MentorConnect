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
 * @CreatedBy: Jothi (IBM)
 * @CreatedOn: 09-10-2018
 * @Description: Mentor's School of Choice Form
*/

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
require_once($CFG->dirroot.'/lib/formslib.php');

class SelectSchoolForm extends moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        global $CFG, $USER, $OUTPUT,$DB;
        $mform = $this->_form; // Don't forget the underscore!
        // Normal fields
		$state = $city = array();
		$city['select_city'] = "Select City";
		$state = stateForMoodleForm();
		// Temp Hidden in Phase1
		$school = array(''=>'Select School');
		$cityid = getCityIdbyCityname($USER->city,$USER->aim);
		$schdata = getschool_bycity_mentorchoice($cityid);
		$i=0;
		if(count($schdata)>0){
			foreach($schdata as $key=>$values){
				$school[$values->id] = $values->schoolname.' - '.$values->cityname;
				$i++;
			}
		} 
		// Own state or different state
		$radioarray=array();
		$radioarray[] = $mform->createElement('radio', 'otherschool', '',' Yes ', 'yes');
		$radioarray[] = $mform->createElement('radio', 'otherschool', '', 'No', 'no');
		$mform->setDefault('otherschool', 'no');
		$mform->addGroup($radioarray, 'radioar', 'Choose School from other State', array(''), false);
		
		//School State
		$mform->addElement('select', 'state', 'State', $state);
		$mform->disabledIf('state','otherschool', 'noteq', 'yes');
		
		//School City
		$mform->addElement('select', 'cityid', 'City', $city);
		$mform->disabledIf('cityid','otherschool', 'noteq', 'yes');
		
		$mform->addElement('select', 'school', 'Select Atal School', $school,'max-width="50%"'); // Temp Hidden in Phase1
		$mform->addRule('school', get_string('required'), 'required', null, 'client');
		$mform->getElement('school')->setMultiple(true);
		
        $mform->addElement('hidden', 'city');
		$mform->addElement('hidden', 'schoolid');
		$this->add_action_buttons(false, 'Submit your Choice');       
    }

    /**
     * Validate the form data.
    *
	*/
    function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);
		//return $errors;
    }
}
