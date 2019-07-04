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
 * @Description: Create a New user (School Incharge)
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
		$userid = -1;
		
		$schoollist = $this->_customdata['schoollist'];
		if(count($schoollist)>0){
			$tmp = $schoollist;
			unset($schoollist);
			$schoollist[0] = "Select School";
			foreach($tmp as $key=>$values){
				$schoollist[$values->id] = $values->name;
			}
		} else{
			$schoollist[0] = "Select School";
		}
		
		$gender[''] = "Select Gender";
		$gender['m'] = "Male";
		$gender['f'] = "Female";
		
        $mform->addElement('hidden', 'userid');
        $mform->setType('userid', PARAM_INT);
        $mform->setDefault('userid', $USER->id);
		$mform->addElement('hidden', 'usertype', 'incharge');
		
        // Normal fields
        $mform->addElement('text', 'username', get_string('username'), 'size="50",maxlength="100",placeholder="Can be your Email or any alphanumeric character"');
		$mform->addRule('username', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('username', 'username', 'auth');
        $mform->setType('username', PARAM_RAW);
		
		$mform->addElement('text', 'newpassword', get_string('newpassword'), 'size="20",maxlength="20"');
        $mform->addHelpButton('newpassword', 'newpassword');       
        $mform->setDefault('newpassword', 'Test@123');
		
		$mform->addElement('text', 'firstname', get_string('firstname'), 'size="50",maxlength="100"');
		$mform->addRule('firstname', get_string('required'), 'required', null, 'client');
		
		$mform->addElement('text', 'lastname', get_string('lastname'), 'size="50",maxlength="100"');
		$mform->addRule('lastname', get_string('required'), 'required', null, 'client');
        
        $mform->addElement('text', 'email', get_string('email'), 'size="50",maxlength="100"');
		$mform->addRule('email', get_string('required'), 'required', null, 'client');	
        
		$mform->addElement('select', 'gender', 'Gender', $gender);
		$mform->addRule('gender', get_string('required'), 'required', null, 'client');
		
		$mform->addElement('select', 'school', 'School', $schoollist);
		$mform->addRule('school', get_string('required'), 'required', null, 'client');
		
		$mform->addElement('text', 'phone1', 'Contact Number', 'size="50",maxlength="20"');
		
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
			if ($course = $DB->get_record('user', array('username' => $data['username']), '*', IGNORE_MULTIPLE)) {
				if (empty($data['id']) || $course->id != $data['id']) {
					$errors['username'] = "User name already exists";
				}
			}
			if ($course = $DB->get_record('user', array('email' => $data['email']), '*', IGNORE_MULTIPLE)) {
				if (empty($data['id']) || $course->id != $data['id']) {
					$errors['email'] = "Email already exists";
				}
			}
		}
        return $errors;
    }
}
