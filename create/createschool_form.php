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
 * @Description: Create a New School.
*/

defined('MOODLE_INTERNAL') || die();


//moodleform is defined in formslib.php
require_once($CFG->dirroot.'/lib/formslib.php');

class create_school_form extends moodleform {

    /**
    * Define the form.
    */
    public function definition() {
        global $CFG, $USER, $OUTPUT,$DB;

        $schoolform = $this->_form; // Don't forget the underscore!
		$userid = 0;
		$state_records = $DB->get_records('state');
		$state = $city = array();
		$city['select_city'] = "Select District";
		$state['select_state']='Select State';
		foreach($state_records as $key=>$values){
			$state[$key] = $values->name;
		}
		$schoolid = $this->_customdata['id'];
		$gender = array(''=>'Select Gender','m'=>'Male','f'=>'Female','t'=>'Third_Gender/Transgender');
		$userrole = get_atalrolenamebyid($USER->msn);
        // Normal fields
		$schoolform->addElement('header', 'moodle', 'School');
		//School Name
        $schoolform->addElement('text', 'name', 'School Name', 'size="50",maxlength="200"');
		
		//School State
		$schoolform->addElement('select', 'state', 'State', $state);
		
		//School City
		$schoolform->addElement('select', 'cityid', 'District', $city);
		
		//School Adrdess
		$schoolform->addElement('textarea', 'address', 'Address', 'wrap="virtual" rows="5" cols="50" maxlength="225" resize="none"');
				
		//Atl-id
		if ($schoolid == 0) {
		$schoolform->addElement('text', 'atl_id', 'ATL ID', 'size="50",maxlength="100"');
		}
		else{
			$schoolform->addElement('text', 'atl_id', 'ATL ID', 'readonly size="50",maxlength="100"');
		}
		//School Email ID
		$schoolform->addElement('text', 'school_emailid', 'School Email Id', 'size="50",maxlength="200"');	
				
		//School Phone Number
		$schoolform->addElement('text', 'phone', 'School Phone Number', 'size="50",maxlength="10"');
		
		//School Principal Name
		$schoolform->addElement('text', 'principal_name', 'School Principal Name', 'size="50",maxlength="200"');
		
		//Principal Email id
		$schoolform->addElement('text', 'principal_email', 'Principal Email Id', 'size="50",maxlength="200"');
		
		//Principal Phone Number
		$schoolform->addElement('text', 'principal_phone', 'Principal Phone Number', 'size="50",maxlength="10"');	
		
		$schoolform->addElement('text', 'udsid', 'UDISE School Code', 'size="50",maxlength="11"');	
		
		$schoolform->addElement('text', 'pfms', 'PFMS', 'size="50",maxlength="50"');	
		
		//Atal Incharge
		$schoolform->addElement('header', 'moodle', 'ATL In-Charge');
		$schoolform->addElement('text', 'firstname', 'Incharge '.get_string('firstname'), 'size="50",maxlength="100"');
		
		
		$schoolform->addElement('text', 'lastname', 'Incharge '.get_string('lastname'), 'size="50",maxlength="100"');
	
        $schoolform->addElement('text', 'email', 'Incharge '.get_string('email'), 'size="50",maxlength="200"');
		
		$schoolform->addElement('select', 'gender', 'Incharge Gender', $gender);
		
		$schoolform->addElement('text', 'phone1', 'Incharge Mobile Number', 'size="23",maxlength="10"');
		if($userrole=='admin')
		{
			$schoolform->addRule('name', get_string('required'), 'required', null, 'client');
			$schoolform->addRule('state', get_string('required'), 'required', null, 'client');
			$schoolform->addRule('cityid', get_string('required'), 'required', null, 'client');
			$schoolform->addRule('atl_id', get_string('required'), 'required', null, 'client');
			$schoolform->addRule('address', get_string('required'), 'required', null, 'client');
			$schoolform->addRule('school_emailid', get_string('required'), 'required', null, 'client');
			$schoolform->addRule('udsid', get_string('required'), 'required', null, 'client');
		}
		else
		{
			$schoolform->addRule('name', get_string('required'), 'required', null, 'client');
			$schoolform->addRule('state', get_string('required'), 'required', null, 'client');
			$schoolform->addRule('cityid', get_string('required'), 'required', null, 'client');
			$schoolform->addRule('atl_id', get_string('required'), 'required', null, 'client');
			$schoolform->addRule('address', get_string('required'), 'required', null, 'client');
			$schoolform->addRule('school_emailid', get_string('required'), 'required', null, 'client');
			$schoolform->addRule('phone', get_string('required'), 'required', null, 'client');
			$schoolform->addRule('principal_name', get_string('required'), 'required', null, 'client');
			$schoolform->addRule('principal_email', get_string('required'), 'required', null, 'client');
			$schoolform->addRule('principal_phone', get_string('required'), 'required', null, 'client');
			$schoolform->addRule('firstname', get_string('required'), 'required', null, 'client');
			$schoolform->addRule('lastname', get_string('required'), 'required', null, 'client');
			$schoolform->addRule('email', get_string('required'), 'required', null, 'client');
			$schoolform->addRule('gender', get_string('required'), 'required', null, 'client');
			$schoolform->addRule('udsid', get_string('required'), 'required', null, 'client');
		}
		
		if ($schoolid == 0) {
			$schoolform->addElement('hidden', 'flag', 'add');
			$btnstring = 'Add';
        } else {
            $btnstring = 'Update';
			$schoolform->addElement('hidden', 'flag', 'edit');
        }
		$schoolform->addElement('hidden', 'schoolid');
		$schoolform->addElement('hidden', 'city');
		$schoolform->addElement('hidden', 'inchargeid');
		
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
		if(!empty($data['email'])){
			if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
				$errors['email'] = "Invalid email format";
			}
		}
		if(!check_only_characters($data['name']))
		{
			$errors['name'] = "Use Only Characters";
		}
		if(!empty($data['principal_name'])){
			if(!check_only_characters($data['principal_name']))
			{
				$errors['principal_name'] = "Use Only Characters";
			}
		}
		if(!empty($data['firstname'])){
			if(!check_only_characters($data['firstname']))
			{
				$errors['firstname'] = "Use Only Characters";
			}
		}
		if(!empty($data['lastname'])){
			if(!check_only_characters($data['lastname']))
			{
				$errors['lastname'] = "Use Only Characters";
			}
		}
		if(!empty($data['phone'])){
			if(!check_is_number($data['phone']))
			{
				$errors['phone'] = "Please Use only Numbers";
			}
			if(!mobile_number_length($data['phone']))
			{
				$errors['phone'] = "Please Give 10 Digit Mobile Number";
			}	
		}
		if(!empty($data['phone1'])){
			if(!check_is_number($data['phone1']))
			{
				$errors['phone1'] = "Please Use only Numbers";
			}
			if(!mobile_number_length($data['phone1']))
			{
				$errors['phone1'] = "Please Give 10 Digit Mobile Number";
			}	
		}
		if(!empty($data['principal_phone'])){
			if(!check_is_number($data['principal_phone']))
			{
				$errors['principal_phone'] = "Please Use only Numbers";
			}
			if(!mobile_number_length($data['principal_phone']))
			{
				$errors['principal_phone'] = "Please Give 10 Digit Mobile Number";
			}	
		}
		if(!empty($data['udsid'])){
			if(!check_is_number($data['udsid']))
			{
				$errors['udsid'] = "Please Use only Numbers";
			}
			if(strlen(trim($data['udsid']))!=10 && strlen(trim($data['udsid']))!=11)
				$errors['udsid'] = "Please Give 10 or 11 Digit Number";
		}
		if(empty($data['city'])){
			$errors['cityid'] = "Select District";
		}
		
        return $errors;
    }
}
