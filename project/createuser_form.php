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
 * @CreatedOn: 01-01-2018
 * @Description: Create a user
*/


defined('MOODLE_INTERNAL') || die();


//moodleform is defined in formslib.php
require_once($CFG->dirroot.'/lib/formslib.php');

class user_create_form extends moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        global $CFG, $USER, $OUTPUT;

        $mform = $this->_form; // Don't forget the underscore!
		$userid = $this->_customdata['id']; 
		$gender = array(''=>'Select Gender','m'=>'Male','f'=>'Female','t'=>'Third_Gender/Transgender');
		$studentclass =array();
		for($i=1;$i<=12;$i++)
		{
			$studentclass[$i]=$i;
		}
        $mform->addElement('hidden', 'userid');
		$mform->setDefault('userid', $userid);
        //$mform->setType('userid', $userid);
        //$mform->setDefault('userid', $USER->id);
		$mform->addElement('hidden', 'usertype', 'student');
		$mform->addElement('hidden', 'school', 0);
		$mform->addElement('hidden', 'user_school_id', 0);
        // Normal fields
		$mform->addElement('text', 'email', get_string('email'), 'size="50",maxlength="100"');
		$mform->addRule('email', get_string('required'), 'required', null, 'client');
		if ($userid == -1) 
		{
			/* 
			$mform->addElement('text', 'username', get_string('username'), 'size="50",maxlength="100",placeholder="Can be your Email or any alphanumeric character"'); 
			$mform->addRule('username', get_string('required'), 'required', null, 'client');
			$mform->addHelpButton('username', 'username', 'auth');
			$mform->setType('username', PARAM_RAW);
			//Password
			$mform->addElement('text', 'newpassword', get_string('newpassword'), 'size="20",maxlength="20"');
			$mform->addHelpButton('newpassword', 'newpassword');       
			$mform->setDefault('newpassword', 'Test@123'); */
		}
		/* else
			$mform->addElement('text', 'username', get_string('username'), 'readonly size="50",maxlength="100",placeholder="Can be your Email or any alphanumeric character"');  */
	
		$mform->addElement('text', 'firstname', get_string('firstname'), 'size="50",maxlength="100"');
		$mform->addRule('firstname', get_string('required'), 'required', null, 'client');
		
		$mform->addElement('text', 'lastname', get_string('lastname'), 'size="50",maxlength="100"');
		$mform->addRule('lastname', get_string('required'), 'required', null, 'client');
		
		$mform->addElement('select', 'studentclass', 'Class', $studentclass,'size="20",maxlength="2"');
		$mform->addRule('studentclass', get_string('required'), 'required', null, 'client');
        
		$mform->addElement('select', 'gender', 'Gender', $gender);
		$mform->addRule('gender', get_string('required'), 'required', null, 'client');
		
		$mform->addElement('text', 'phone1', 'Mobile', 'size="20",maxlength="10"');
		
        $mform->addElement('hidden', 'studentid', $userid);       
        if ($userid == -1) {
            $btnstring = get_string('createuser');
			$mform->addElement('hidden', 'flag', 'add');
        } else {
            $btnstring = get_string('updatemyprofile');
			$mform->addElement('hidden', 'flag', 'edit');
        }

        $this->add_action_buttons(false, $btnstring);

        $this->set_data($user);
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
			if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
				$errors['email'] = "Invalid email format";
			} else{
				// Add field validation check for duplicate email,username.
				if($data['studentid'] == -1){
				if ($result = $DB->get_record('user', array('email' => $data['email']), 'id', IGNORE_MULTIPLE)) {
					//if (empty($data['studentid']) || $result->id != $data['studentid']) {
						if ($result) {
							$errors['email'] = "Email already exists";
					}
				}
				}
			if($data['studentid'] > 0){
					if ($result = $DB->get_record('user', array('email' => $data['email']), 'id', IGNORE_MULTIPLE)) {
						if ($result && $result->id != $data['studentid']) {
						if ($result) {
							$errors['email'] = "Email already exists";
						}
					}
				}
			}
		}
		if(!check_only_characters($data['firstname']))
		{
			$errors['firstname'] = "Use Only Characters";
		}
		if(!check_only_characters($data['lastname']))
		{
			$errors['lastname'] = "Use Only Characters";
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
		return $errors;
	}
}
