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
        $mform = $this->_form; // Don't forget the underscore!
        // Normal fields
		
		$mform->addElement('text', 'email', 'Email To', 'size="50",maxlength="200"');	
		$mform->addRule('email', get_string('required'), 'required', null, 'client');
		//Subject
		$mform->addElement('text', 'subject', 'Subject', 'size="50",maxlength="200"');	
		$mform->setDefault('subject', 'Welcome To ATLInnonet');
		$mform->addRule('subject', get_string('required'), 'required', null, 'client');
		
		//Mail Body
		$mform->addElement('editor', 'mailbody', 'Mail Body', 'size="50"');	
		$mform->addRule('mailbody', get_string('required'), 'required', null, 'client');
		
        $this->add_action_buttons(false, 'Send Mail');       
    }

    /**
     * Validate the form data.
    *
	*/
    function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);
		if(!empty($data['email'])){
			if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
				$errors['email'] = "Invalid email format";
			}
		}
        return $errors;
    }
}
