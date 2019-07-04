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
 * @Description: To Sent WelcomeMail for Launch, called in mail.php
*/

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

class CreateMailForm extends moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        global $CFG, $USER, $OUTPUT,$DB;
		$role=array('all'=>'All','mentor'=>'Mentors','incharge'=>'Schools','student'=>'Students');
        $mform = $this->_form; // Don't forget the underscore!
        // Normal fields
		//Role
		$mform->addElement('select', 'role', 'Role', $role);
		$mform->addRule('role', get_string('required'), 'required', null, 'client');
		
		//Subject
		$mform->addElement('text', 'subject', 'Subject', 'size="50",maxlength="200"');	
		$mform->setDefault('subject', 'Welcome To ATLInnonet');
		$mform->addRule('subject', get_string('required'), 'required', null, 'client');
		
		//Mail Body
		/* $mform->addElement('textarea', 'mailbody', 'Mail Body', 'wrap="virtual" rows="5" cols="50"');
		$mform->setDefault('mailbody', 'We have Upgrade our Portal . Please Login and Use it..');
		$mform->addRule('textarea', get_string('required'), 'required', null, 'client'); */
		
        $this->add_action_buttons(false, 'Send Mail');       
    }

    /**
     * Validate the form data.
    *
	*/
    function validation($data, $files) {
        /* global $DB;
        $errors = parent::validation($data, $files);
		if(!empty($data['school_emailid'])){
			if (!filter_var($data['school_emailid'], FILTER_VALIDATE_EMAIL)) {
				$errors['school_emailid'] = "Invalid email format";
			}
		}
        return $errors; */
    }
}
