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
 * @CreatedOn: 07-02-2018
 * @Description: Create a New School
*/

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

class CreateSchoolForm extends moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        global $CFG, $USER, $OUTPUT,$DB;

        $schoolform = $this->_form; // Don't forget the underscore!
		$this->_formname = "schoolform";
		$userid = -1;
		$state = $city = array();
		$city['select_city'] = "Select City";
		$state = stateForMoodleForm();
		
        // Normal fields
		//School Name
        $schoolform->addElement('text', 'name', 'School Name', 'size="50",maxlength="200"');
		$schoolform->addRule('name', get_string('required'), 'required', null, 'client');
		
		//School State
		$schoolform->addElement('select', 'state', 'State', $state);
		$schoolform->addRule('state', get_string('required'), 'required', null, 'client');
		
		//School City
		$schoolform->addElement('select', 'cityid', 'City', $city);
		
		//School Adrdess
		$schoolform->addElement('textarea', 'address', 'Address', 'wrap="virtual" rows="5" cols="50" maxlength="225" resize="none"');
		$schoolform->addRule('address', get_string('required'), 'required', null, 'client');
		
		//Atl-id
		$schoolform->addElement('text', 'atl_id', 'ATL ID', 'size="50",maxlength="100"');
		
		//School Email ID
		$schoolform->addElement('text', 'school_emailid', 'School Email Id', 'size="50",maxlength="200"');	
		
		//School Phone Number
		$schoolform->addElement('text', 'phone', 'School Phone Number', 'size="50",maxlength="50"'); 
		
		//School Principal Name
		$schoolform->addElement('text', 'principal_name', 'School Principal Name', 'size="50",maxlength="200"'); 
		
		//Principal Email id
		$schoolform->addElement('text', 'principal_email', 'Principal Email Id', 'size="50",maxlength="200"');	
		
		//Principal Phone Number
		$schoolform->addElement('text', 'principal_phone', 'Principal Phone Number', 'size="50",maxlength="50"');
		$schoolform->addElement('hidden', 'schoolid');
		$schoolid = $this->_customdata['id']; 
		if ($schoolid == -1) {
			$schoolform->addElement('hidden', 'flag', 'add');
			$btnstring = 'Add School';
        } else {
            $btnstring = 'Update School';
			$schoolform->addElement('hidden', 'flag', 'edit');
        }
		$schoolform->addElement('hidden', 'city');
        $this->add_action_buttons(false, $btnstring);       
    }

    /**
     * Validate the form data.
     * @param array $usernew
     * @param array $files
     * @return array|bool
     *
	*/
    function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);
		if(!empty($data['school_emailid'])){
			if (!filter_var($data['school_emailid'], FILTER_VALIDATE_EMAIL)) {
				$errors['school_emailid'] = "Invalid email format";
			}
		}
		if(!empty($data['principal_email'])){
			if (!filter_var($data['principal_email'], FILTER_VALIDATE_EMAIL)) {
				$errors['principal_email'] = "Invalid email format";
			}
		}
		
		if(empty($data['city'])){
			$errors['cityid'] = "Select City";
		}
		if($data['flag']=='add')
		{
			if ($id = $DB->get_record('school', array('name' => $data['name'],'cityid' => $data['city']))) 
			{
				if (count($id)==1) {
					$errors['name'] = "School Already Exists";
				}
			}
		}
        return $errors;
    }
}
